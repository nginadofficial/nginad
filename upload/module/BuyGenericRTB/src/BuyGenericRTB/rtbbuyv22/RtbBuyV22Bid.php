<?php
/**
 * CDNPAL NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2015 CDNPAL Ltd. All Rights Reserved
 * @license GPLv3
 */

namespace rtbbuyv22;
use \Exception;
use rtbbuy\RtbBuyBid;

abstract class RtbBuyV22Bid extends RtbBuyBid {

	public $rtb_base_url = "http://rtb.demandpartner.com";

	public $had_bid_response = false;

	protected $rtb_provider = "none";

	// will be used for stats
	public $rtb_seat_id = null;
	
	public $response_seat_id = null;
	
	
	public $RtbBidRequest;
	
	/*
	 * an array of RtbBidResponse objects
	 */

	public $bid_responses = array();

	private $RtbNoBidResponse = array(
	        "requestId"=>"",
			"bid"=>""
	);

	/*
	 * A List of AdCampaignBanner ORM objects that matched all the business rules from the incoming request
	*/
	private $AdCampaignBanner_Match_List = array();

	
	// CONFIG

	public $config;

	public $is_local_request	= false;

	public function __construct($config = null, $rtb_seat_id = null, $response_seat_id = null) {
		
		$this->rtb_seat_id 		= $rtb_seat_id !== null ? $rtb_seat_id : $this->rtb_provider;
		$this->response_seat_id = $response_seat_id !== null ? $response_seat_id : 'na';
		$this->config 			= $config;
	}

	private function generate_transaction_id() {

		return uniqid("cdnp" . "." . $this->rtb_provider, true);
	}


	function send_bid_response() {

	    $log_header = "\n----------------------------------------------------------------\n";
	    $log_header.= date('m-d-Y H:i:s') . " ------- NEW BID RESPONSE " . $this->rtb_provider . " -------\n";
	    $log_header.= "----------------------------------------------------------------\n";

	    \rtbbuyv22\RtbBuyV22Logger::get_instance()->log[] = $log_header;

			header("Content-type: application/json");
		    $bid_response = json_encode($this->bid_responses);
			echo $bid_response;
			\rtbbuyv22\RtbBuyV22Logger::get_instance()->log[] = $bid_response;
			\rtbbuyv22\RtbBuyV22Logger::get_instance()->min_log[] = $bid_response;
	}
	
	public function build_outgoing_bid_response() {


		
		$this->bid_responses = $RtbBidResponse;
		
	}

	private function get_effective_ad_tag(&$AdCampaignBanner, $tld) {

			/*
			 * This is an ad tag somebody copy pasted into the RTB Manager
			 * which is not using our Revive ad server. Lets send back
			 * an on the fly iframe ad tag to our delivery mechanism
			 * which will send back the client's Javascript or an IFrame
			 * and count the ad impression.
			 */

			$delivery_adtag = $this->config['delivery']['adtag'];

			$classname = $this->random_classname();

			$winning_bid_auction_param = "";
			
			$cache_buster = time();

			if ($this->rtb_provider != "BuyLoopbackPartner"):
				$winning_bid_auction_param = "&winbid={NGINWBIDPRC}";
			endif;
			
			$effective_tag = "<script type='text/javascript' src='" . $delivery_adtag . "?zoneid=" . $AdCampaignBanner->AdCampaignBannerID . "&buyerid=" . $this->rtb_seat_id . "&height=" . $AdCampaignBanner->Height . "&width=" . $AdCampaignBanner->Width . "&tld=" . $tld . "&clktrc={NGINCLKTRK}" . $winning_bid_auction_param . "&ui=" . $this->user_ip_hash . "&cb=" . $cache_buster . "'></script>";
			
			return $effective_tag;
	}

	private function random_classname()
	{
			$random_classname = $this->rand_letter();
			$random_classname.= rand(100, 999);
			$random_classname.= $this->rand_letter();
			$random_classname.= rand(100, 999);

			return $random_classname;
	}

	private function rand_letter()
	{
		$int = rand(0,24);
		$a_z = "abcdefghijklmnopqrstuvwxyz";
		$rand_letter = $a_z[$int];
		return $rand_letter;
	}

	public function process_business_logic() {

	    $this->AdCampaignBanner_Match_List = \rtbbuyv22\RtbBuyV22Workflow::get_instance()->process_business_rules_workflow($this->config, $this->rtb_seat_id, $this->RtbBidRequest);
	}

	public function parse_incoming_request($raw_post = null) {

		$OpenRTBParser = new \buyrtb\parsers\openrtb\OpenRTBParser();
		$this->RtbBidRequest = $OpenRTBParser->parse_request($raw_post, $this->is_local_request);
		
		// transform to JSON
		//\buyrtb\encoders\openrtb\RtbBidRequestJsonEncoder::execute($this->RtbBidRequest);

	}

	public function convert_ads_to_bid_responses() {
		/*
		 * get TLD of the site url or page url for the
		* ad tag in case it's needed for the delivery module
		*/
		
		$tld = "not_available";
		// bid // site
		
		$parse = parse_url($this->RtbBidRequest->RtbBidRequestSite->domain);
		if (isset($parse['host'])):
			$tld = $parse['host'];
		else:
			$parse = parse_url($this->RtbBidRequest->RtbBidRequestSite->page);
			if (isset($parse['host'])):
				$tld = $parse['host'];
			endif;
		endif;

		$RtbBidResponse = array("id"=>$this->RtbBidRequest->id);
		$RtbBidResponse["seatbid"] = array();
		 
		foreach ($this->AdCampaignBanner_Match_List as $bid_imp_id => $AdCampaignBannerList):
			
			foreach ($AdCampaignBannerList as $AdCampaignBanner):
			
				$bidresponse = array();
					
				$bidresponse["id"] = $bid_imp_id;
				$bidresponse["adid"] = $this->generate_transaction_id();
				$bidresponse["impid"] = $bidresponse["adid"];
				$bidresponse["price"] = $AdCampaignBanner->BidAmount;
				$bidresponse["adm"] = $this->get_effective_ad_tag($AdCampaignBanner, $tld);
				$bidresponse["adomain"][] = $AdCampaignBanner->LandingPageTLD;
				$bidresponse["cid"] = "cdnpl_" . $AdCampaignBanner->AdCampaignBannerID;
				$bidresponse["crid"] = $bidresponse["impid"];
				$this->had_bid_response = true;
				
				$RtbBidResponse["seatbid"][] = array("bid"=>array($bidresponse), "seat"=>$this->response_seat_id);
				
			endforeach;
		 
		endforeach;
		
		$RtbBidResponse["cur"] = "USD";
	}
	
}

