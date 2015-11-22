<?php
/**
 * NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2016 NginAd Foundation. All Rights Reserved
 * @license GPLv3
 */

namespace rtbbuyfidelity;
use \Exception;

abstract class RtbBuyFidelityBid extends \rtbbuy\RtbBuyBid {

	public $rtb_base_url = "http://rtb.demandpartner.com";

	public $had_bid_response = false;

	protected $rtb_provider = "none";
	
	protected $rtb_ssp_friendly_name = "none";

	// will be used for stats
	public $rtb_seat_id = null;
	
	public $response_seat_id = null;
	
	
	public $RtbBidRequest;
	public $RtbBidResponse;
	
	/*
	 * an array of RtbBidResponse objects
	 */

	public $bid_responses = array();

	private $RtbNoBidResponse = array(
	        "requestId"=>"",
			"bid"=>""
	);

	/*
	 * A List of InsertionOrderLineItem ORM objects that matched all the business rules from the incoming request
	*/
	private $InsertionOrderLineItem_Match_List = array();

	private $no_bid_reason;
	
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

	    \rtbbuyfidelity\RtbBuyFidelityLogger::get_instance()->log[] = $log_header;

			header("Content-type: application/json");
		    $bid_response = $this->bid_responses;
			echo $bid_response;
			\rtbbuyfidelity\RtbBuyFidelityLogger::get_instance()->log[] = $bid_response;
			\rtbbuyfidelity\RtbBuyFidelityLogger::get_instance()->min_log[] = $bid_response;
	}
	
	public function build_outgoing_bid_response() {

		// transform to JSON
		$this->bid_responses = \buyrtbfidelity\encoders\openrtb\RtbBidResponseJsonEncoder::execute($this->RtbBidResponse);
	}

	private function get_effective_ad_tag(&$InsertionOrderLineItem, $tld) {

			/*
			 * This is an ad tag somebody copy pasted into the RTB Manager
			 * which is not using our Revive ad server. Lets send back
			 * an on the fly iframe ad tag to our delivery mechanism
			 * which will send back the client's Javascript or an IFrame
			 * and count the ad impression.
			 */

			$delivery_adtag_js = $this->config['delivery']['adtag'];
			
			$classname = $this->random_classname();

			$winning_bid_auction_param = "";
			
			$cache_buster = time();

			if ($this->rtb_provider != "BuyLoopbackPartner"):
				$winning_bid_auction_param = "&winbid=\${AUCTION_PRICE}";
			endif;
			
			$effective_tag = "<script type='text/javascript' src='" . $delivery_adtag_js . "?zoneid=" . $InsertionOrderLineItem->InsertionOrderLineItemID . "&buyerid=" . $this->rtb_seat_id . "&height=" . $InsertionOrderLineItem->Height . "&width=" . $InsertionOrderLineItem->Width . "&tld=" . $tld . "&clktrc={NGINCLKTRK}" . $winning_bid_auction_param . "&ui=" . $this->user_ip_hash . "&cb=" . $cache_buster . "'></script>";
			
			// return rawurlencode($effective_tag);
			
			return $effective_tag;
	}
	
	private function get_banner_notice_url(&$InsertionOrderLineItem, $tld, $request_id, $price) {
	
		$delivery_adtag = $this->config['delivery']['url'];
			
		$classname = $this->random_classname();
	
		$vendor = "fidelity";
		
		$winning_bid_auction_param = "";
			
		$cache_buster = time();
	
		if ($this->rtb_provider != "BuyLoopbackPartner"):
			$winning_bid_auction_param = "&winbid=\${AUCTION_PRICE}";
		endif;
	
		$notice_tag = $delivery_adtag . "?nurl=true&zoneid=" . $InsertionOrderLineItem->InsertionOrderLineItemID . "&buyerid=" . $this->rtb_seat_id . "&orgprc=" . $price . "&request_id=" . $request_id . "&vendor=" . $vendor . "&tld=" . $tld . $winning_bid_auction_param . "&ui=" . $this->user_ip_hash . "&cb=" . $cache_buster;
	
		return $notice_tag;
	}
	
	private function get_video_notice_url(&$InsertionOrderLineItem, $tld) {
	
		$delivery_adtag = $this->config['delivery']['url'];
			
		$classname = $this->random_classname();
	
		$winning_bid_auction_param = "";
			
		$cache_buster = time();
	
		if ($this->rtb_provider != "BuyLoopbackPartner"):
			$winning_bid_auction_param = "&winbid=\${AUCTION_PRICE}";
		endif;

		$notice_tag = $delivery_adtag . "?video=vast&zoneid=" . $InsertionOrderLineItem->InsertionOrderLineItemID . "&buyerid=" . $this->rtb_seat_id . "&tld=" . $tld . "&clktrc={NGINCLKTRK}" . $winning_bid_auction_param . "&ui=" . $this->user_ip_hash . "&cb=" . $cache_buster;
	
		return $notice_tag;
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

	    $this->InsertionOrderLineItem_Match_List = \rtbbuyfidelity\RtbBuyFidelityWorkflow::get_instance()->process_business_rules_workflow($this->config, $this->rtb_seat_id, $this->no_bid_reason, $this->RtbBidRequest);
	}

	public function parse_incoming_request($raw_post = null) {

		$OpenRTBParser = new \buyrtbfidelity\parsers\openrtb\OpenRTBParser();
		try {
			$this->RtbBidRequest = $OpenRTBParser->parse_request($this->config, $this->is_local_request, $this->rtb_ssp_friendly_name, $raw_post);
		} catch (Exception $e) {
			$logger =\rtbbuyfidelity\RtbBuyFidelityLogger::get_instance();
			if ($logger->setting_log === true):
				$logger->log[] = "Failed: " . "RTB Request Parse ERROR :: " . $e->getMessage();
			endif;
			$this->no_bid_reason = NOBID_INVALID_REQUEST;
			return false;
		}
		
		
		return true;
	}
	
	/*
	 * FIDELITY MOD:
	* One bid per request only. Multiple bid/seat responses won’t be accepted.
	*/
	public function fidelity_dedupe_bid_response() {
	
		$high_bid = null;
	
		$InsertionOrderLineItem_Match_List = array();
	
		foreach ($this->InsertionOrderLineItem_Match_List as $user_id => $InsertionOrderLineItemObjList):
				
			$RtbBidResponseSeatBid	= new \model\openrtb\RtbBidResponseSeatBid();
		
			foreach ($InsertionOrderLineItemObjList as $InsertionOrderLineItemObj):
			
				$bid_imp_id 				= $InsertionOrderLineItemObj["impid"];
				$InsertionOrderLineItem 			= $InsertionOrderLineItemObj["InsertionOrderLineItem"];
					
				/*
				 * Poll the highest bid and set it as the only bid
				*/
				if ($high_bid === null || floatval($high_bid) < floatval($InsertionOrderLineItem->BidAmount)):
					
					$high_bid = $InsertionOrderLineItem->BidAmount;
					$_InsertionOrderLineItemObjList = array();
					$_InsertionOrderLineItemObjList[] = $InsertionOrderLineItemObj;
					$InsertionOrderLineItem_Match_List = array();
					$InsertionOrderLineItem_Match_List[$user_id] = $_InsertionOrderLineItemObjList;
						
				endif;
			
			endforeach;
		
		endforeach;
	
		$this->InsertionOrderLineItem_Match_List = $InsertionOrderLineItem_Match_List;
		
	}

	public function convert_ads_to_bid_response() {
		
		// init the bid response object
		$RtbBidResponse	= new \model\openrtb\RtbBidResponse();
		
		/*
		 * get TLD of the site url or page url for the
		* ad tag in case it's needed for the delivery module
		*/
		
		$tld = "not_available";

		$rtb_ids = null;
		
		if ($this->RtbBidRequest != null):
		
			$rtb_ids = \util\WorkflowHelper::getIdsFromRtbRequest($this->RtbBidRequest);
			
			$tld = $rtb_ids["tld"];
			
			$this->user_ip_hash = md5($this->RtbBidRequest->RtbBidRequestDevice->ip);
			
			$RtbBidResponse->id = $this->RtbBidRequest->id;
			
		endif;
		
		$RtbBidResponse->RtbBidResponseSeatBidList = array();
		
		$currency = null;
		
		$total_bids = 0;
		$spend_offered_in_bids = 0;
		
		foreach ($this->InsertionOrderLineItem_Match_List as $user_id => $InsertionOrderLineItemObjList):
		
			$RtbBidResponseSeatBid	= new \model\openrtb\RtbBidResponseSeatBid();
				
			foreach ($InsertionOrderLineItemObjList as $InsertionOrderLineItemObj):
				
				$bid_imp_id 				= $InsertionOrderLineItemObj["impid"];
				$InsertionOrderLineItem 			= $InsertionOrderLineItemObj["InsertionOrderLineItem"];
					
				if (isset($InsertionOrderLineItemObj["currency"]) && $currency == null):
					$currency = $InsertionOrderLineItemObj["currency"];
				endif;
					
				$RtbBidResponseBid	= new \model\openrtb\RtbBidResponseBid();
					
				$RtbBidResponseBid->id			= $this->generate_transaction_id();
				$RtbBidResponseBid->adid		= $RtbBidResponseBid->id;
				$RtbBidResponseBid->impid		= $bid_imp_id;
				$RtbBidResponseBid->price		= $InsertionOrderLineItem->BidAmount;
		
				if ($InsertionOrderLineItem->ImpressionType == 'video'):
					$RtbBidResponseBid->nurl 	= $this->get_video_notice_url($InsertionOrderLineItem, $tld);
				else:
					$RtbBidResponseBid->adm		= $this->get_effective_ad_tag($InsertionOrderLineItem, $tld);
					$RtbBidResponseBid->nurl 	= $this->get_banner_notice_url($InsertionOrderLineItem, $tld, $this->RtbBidRequest->id, $InsertionOrderLineItem->BidAmount);
				endif;
				
				$RtbBidResponseBid->adomain[] 	= $InsertionOrderLineItem->LandingPageTLD;
				$RtbBidResponseBid->cid	 		= "nginad_" . $InsertionOrderLineItem->InsertionOrderID;
				$RtbBidResponseBid->crid	 	= "nginad_" . $InsertionOrderLineItem->InsertionOrderLineItemID;
				$this->had_bid_response = true;
				
				$RtbBidResponseSeatBid->RtbBidResponseBidList[] = $RtbBidResponseBid;
				$spend_offered_in_bids += floatval($InsertionOrderLineItem->BidAmount / 1000);
				$total_bids++;
				
				\util\FrequencyHelper::incrementLineItemBidFrequencyCount($this->config, $InsertionOrderLineItem->InsertionOrderLineItemID);
				
			endforeach;
			
			$RtbBidResponseSeatBid->seat						= $user_id;
			$RtbBidResponse->RtbBidResponseSeatBidList[] 		= $RtbBidResponseSeatBid;
			
		endforeach;
			
		if (isset($InsertionOrderLineItemObj["currency"]) && $currency == null):
			$RtbBidResponse->cur				= $currency;
		else:
			$RtbBidResponse->cur				= $this->config['settings']['rtb']['auction_currency'];
		endif;
		
		if (!count($RtbBidResponse->RtbBidResponseSeatBidList)):
				
			// implement Rubicon Project's empty bid with $0.00 CPM here
			// also add the rejection code
			unset($RtbBidResponse->id);
			unset($RtbBidResponse->cur);
			if ($this->no_bid_reason != null):
				$RtbBidResponse->nbr 								= $this->no_bid_reason;
			else:
				$RtbBidResponseSeatBid								= new \model\openrtb\RtbBidResponseSeatBid();
				$RtbBidResponseBid									= new \model\openrtb\RtbBidResponseBid();
				$RtbBidResponseBid->price 							= 0;
				$RtbBidResponseSeatBid->RtbBidResponseBidList[] 	= $RtbBidResponseBid;
				$RtbBidResponse->RtbBidResponseSeatBidList[] 		= $RtbBidResponseSeatBid;
			endif;
			
		endif;
		
			$this->RtbBidResponse = $RtbBidResponse;
	
		$this->logImpressionsStatisticsData($tld, $total_bids, $spend_offered_in_bids);

	}
	
	protected function logImpressionsStatisticsData($tld, $total_bids, $spend_offered_in_bids) {
		
		if ($this->RtbBidRequest != null):
			/*
			 * CREATE AN HOURLY TALLY OF INCOMING RTB BIDS
			* FROM BOTH LOCAL PUBS AND REMOTE SSP RTB SITE ID
			* CHANNELS IN ORDER TO PROVIDE THE SITE SCOUT
			* RTB CHANNEL CHOOSER FUNCTIONALITY IN AN EXCEL LIKE
			* GRID LAYOUT WITH THE DAILY IMPS IN A SORTABLE COLUMN
			*/

			$buyside_partner_name 				= $this->rtb_ssp_friendly_name;
			$rtb_channel_site_domain			= $tld;
			$auction_bids_counter 				= $total_bids;
				
			$rtb_ids = \util\WorkflowHelper::getIdsFromRtbRequest($this->RtbBidRequest);
			
			$rtb_channel_site_id 			= $rtb_ids["rtb_channel_site_id"];
			$rtb_channel_site_name 			= $rtb_ids["rtb_channel_site_name"];
			$rtb_channel_publisher_name 	= $rtb_ids["rtb_channel_publisher_name"];
			$rtb_channel_site_iab_category 	= $rtb_ids["rtb_channel_site_iab_category"];
			$impressions_offered_counter 	= $rtb_ids["impressions_offered_counter"];
			
			$floor_price_if_any = 0;
			
			if (isset($this->RtbBidRequest->RtbBidRequestImpList) && is_array($this->RtbBidRequest->RtbBidRequestImpList)):
				foreach ($this->RtbBidRequest->RtbBidRequestImpList as $RtbBidRequestImp):
					if (isset($RtbBidRequestImp->bidfloor) && $RtbBidRequestImp->bidfloor > $floor_price_if_any):
						$floor_price_if_any = floatval($RtbBidRequestImp->bidfloor);
					endif;
				endforeach;
			endif;
			
			$method_params = array(
					"buyside_partner_name" 			=> $buyside_partner_name,
					"rtb_channel_site_id" 			=> $rtb_channel_site_id,
					"rtb_channel_site_name" 		=> $rtb_channel_site_name,
					"rtb_channel_site_domain" 		=> $rtb_channel_site_domain,
					"rtb_channel_site_iab_category" => $rtb_channel_site_iab_category,
					"rtb_channel_publisher_name" 	=> $rtb_channel_publisher_name,
					"impressions_offered_counter" 	=> $impressions_offered_counter,
					"auction_bids_counter" 			=> $auction_bids_counter,
					"spend_offered_in_bids" 		=> $spend_offered_in_bids,
					"floor_price_if_any" 			=> $floor_price_if_any,
			);
			
			if ($this->is_local_request === true):
				/*
				 * In the local context, $rtb_channel_site_id is the PublisherWebsiteID
				 */
				$PrivateExchangeRtbChannelDailyStatsFactory = \_factory\PrivateExchangeRtbChannelDailyStats::get_instance();
				$PrivateExchangeRtbChannelDailyStatsFactory->incrementPrivateExchangeRtbChannelDailyStatsCached($this->config, $method_params);
			else:
				$SspRtbChannelDailyStatsFactory = \_factory\SspRtbChannelDailyStats::get_instance();
				$SspRtbChannelDailyStatsFactory->incrementSspRtbChannelDailyStatsCached($this->config, $method_params);
			endif;
			
		endif;
	}
	
}

