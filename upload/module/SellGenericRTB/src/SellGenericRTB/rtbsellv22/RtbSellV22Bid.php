<?php
/**
 * NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2016 NginAd Foundation. All Rights Reserved
 * @license GPLv3
 */

namespace rtbsellv22;
use rtbsell\RtbSellBid;

 class RtbSellV22Bid extends RtbSellBid {

	public $rtb_base_url = "http://rtb.demandpartner.com";

	public $had_bid_response = false;

	protected $rtb_provider = "none";

	// object containing the JSON request
	public $RtbBidRequest;
	// inject PMP to local requests
	public $RtbBidRequestLocalPmp;
	
	public $RtbBidResponse;
	
	public $user_ip_hash;
	
	/*
	 * an array of RtbBidResponse objects
	 */

	public $bid_responses = array();

	private $RtbNoBidResponse = array(
	        "requestId"=>"",
			"bid"=>""
	);

	// original bid params from the Delivery controller
	public $org_request;
	
	/*
	 * A List of InsertionOrderLineItem ORM objects that matched all the business rules from the incoming request
	*/
	private $InsertionOrderLineItem_Match_List = array();

	private $expeption_missing_min_bid_request_params = "Bid Request missing required parameter";
	private $missing_optional_bid_request_params = "Bid Request missing optional parameter";
	private $got_optional_bid_request_params = "Got Bid Request optional parameter";

	// transaction id is not specified, autogenerate
	
	// CONFIG

	public $config;


	public function __construct($config = null) {
		$this->config = $config;
	}

	private function generate_transaction_id() {

		return uniqid("cdnp" . "." . $this->rtb_provider, true);
	}
	
	
	public function build_rtb_bid_request_generic() {

		return \buyrtb\encoders\openrtb\RtbBidRequestJsonEncoder::execute($this->RtbBidRequest);
		
	}
	
	public function build_rtb_bid_request_loopback() {

		return \buyrtb\encoders\openrtb\RtbBidRequestJsonEncoder::execute($this->RtbBidRequestLocalPmp);
	
	}
	
	private function setObjParam(&$obj, &$arr, $name, $obj_name = null) {
		
		if ($obj_name == null):
			$obj_name = $name;
		endif;
		
		if (!empty($arr[$name]) ||
		(isset($arr[$name]) && is_numeric($arr[$name]))):
			$obj->$obj_name = $arr[$name];
		endif;
	}	
	
	private function create_rtb_request_video($config, $banner_request) {
	
		$RtbBidRequestVideo						= new \model\openrtb\RtbBidRequestVideo();
		$RtbBidRequestVideo->id					= $this->generate_transaction_id();
	
		$this->setObjParam($RtbBidRequestVideo, $banner_request, "video_mimes", "mimes");
		
		$this->setObjParam($RtbBidRequestVideo, $banner_request, "video_min_duration", "minduration");
		
		$this->setObjParam($RtbBidRequestVideo, $banner_request, "video_max_duration", "maxduration");
		
		$this->setObjParam($RtbBidRequestVideo, $banner_request, "video_protocols", "protocols");

		$this->setObjParam($RtbBidRequestVideo, $banner_request, "video_width", "w");
		
		$this->setObjParam($RtbBidRequestVideo, $banner_request, "video_height", "h");
		
		$this->setObjParam($RtbBidRequestVideo, $banner_request, "video_start_delay", "startdelay");
		
		$this->setObjParam($RtbBidRequestVideo, $banner_request, "video_linearity", "linearity");
		
		$this->setObjParam($RtbBidRequestVideo, $banner_request, "video_delivery", "delivery");
		
		$this->setObjParam($RtbBidRequestVideo, $banner_request, "video_foldpos", "pos");
	
		$this->setObjParam($RtbBidRequestVideo, $banner_request, "video_apis_supported", "api");
		
		return $RtbBidRequestVideo;
	}
	
	private function create_rtb_request_banner($config, $banner_request) {
		
		$RtbBidRequestBanner						= new \model\openrtb\RtbBidRequestBanner();
		$RtbBidRequestBanner->id					= $this->generate_transaction_id();
		
		$this->setObjParam($RtbBidRequestBanner, $banner_request, "banner_height", "h");
		$this->setObjParam($RtbBidRequestBanner, $banner_request, "banner_width", "w");
		
		// if banner is not above the fold, do not announce it
		if ($banner_request["atf"] == 1):
			$RtbBidRequestBanner->pos 				= $banner_request["atf"] == 1 ? 1 : 0;
		endif;
		
		// if banner is not in the top level frame, do not announce it
		if ($banner_request["ifr"] == 0):
			$RtbBidRequestBanner->topframe 			= $banner_request["ifr"] == 0 ? 1 : 0;
		endif;

		return $RtbBidRequestBanner;
	}
	
	public function clone_local_rtb_request_with_pmp($config, $banner_request, $PmpDealPublisherWebsiteToInsertionOrderLineItemList) {
		/*
		 * Take the existing RTB requests that we send to DSPs
		 * and inject the PMP object for local domain admins
		 * which are using the platform for private exchanges
		 * or which are bidding on platform connection inventory
		 * from other domain admins on the platform who's 
		 * publishers have platform connection enabled on 
		 * their inventory (website domains).
		 */
		// shallow copy - children are not cloned
		$this->RtbBidRequestLocalPmp 		= clone $this->RtbBidRequest;

		if (!count($PmpDealPublisherWebsiteToInsertionOrderLineItemList)):
			return;
		endif;
		
		$RtbBidRequestPmp							= new \model\openrtb\RtbBidRequestPmp();
		
		$RtbBidRequestPmp->private_auction			= 1;
		
		/*
		 * In this implementation wseats 
		 * is an array of InsertionOrderLineItemIDs
		 * from the domain admins who have added the 
		 * inventory to their InsertionOrders
		 */
		$wseats = array();
		
		foreach ($PmpDealPublisherWebsiteToInsertionOrderLineItemList as $PmpDealPublisherWebsiteToInsertionOrderLineItem):

			$wseats[] = $PmpDealPublisherWebsiteToInsertionOrderLineItem->InsertionOrderLineItemID;
		
		endforeach;
		
		$RtbBidRequestDirectDeals				= new \model\openrtb\RtbBidRequestDirectDeals();
		$RtbBidRequestDirectDeals->id			= $this->generate_transaction_id();
		$this->setObjParam($RtbBidRequestDirectDeals, $banner_request, "bidfloor");
		$RtbBidRequestDirectDeals->at 			= $config['settings']['rtb']['second_price_auction'] === true ? 2 : 1;
		$RtbBidRequestDirectDeals->bidfloorcur  = $config['settings']['rtb']['auction_currency'];
		$RtbBidRequestDirectDeals->wseat  		= $wseats;
		
		$RtbBidRequestPmp->RtbBidRequestDirectDealsList[] = $RtbBidRequestDirectDeals;
		
		/*
		 * Add the PMP to each OpenRTB impression object
		 * 
		 * Because the original clone at the beginning of the 
		 * method was a shallow copy, we need to deep copy the
		 * Impression object children in order to change their
		 * values.
		 */
		$this->RtbBidRequestLocalPmp->RtbBidRequestImpList = array();
		
		foreach ($this->RtbBidRequest->RtbBidRequestImpList as $RtbBidRequestImpKey => $RtbBidRequestImpValue):
			
			$this->RtbBidRequestLocalPmp->RtbBidRequestImpList[$RtbBidRequestImpKey] = clone $this->RtbBidRequest->RtbBidRequestImpList[$RtbBidRequestImpKey];
			$this->RtbBidRequestLocalPmp->RtbBidRequestImpList[$RtbBidRequestImpKey]->RtbBidRequestPmp = $RtbBidRequestPmp;
			
		endforeach;
			
	}
	
	public function create_rtb_request_from_publisher_display_impression($config, $banner_request) {
		
		$this->org_request							= $banner_request;
		
		$RtbBidRequest 								= new \model\openrtb\RtbBidRequest();
		$RtbBidRequest->id 							= $this->generate_transaction_id();
		
		$RtbBidRequestImp							= new \model\openrtb\RtbBidRequestImp();	
		$RtbBidRequestImp->media_type 				= $banner_request["ImpressionType"];
		$RtbBidRequestImp->id						= $this->generate_transaction_id();
		
		$is_secure = false;
		
		if (isset($banner_request["loc"]) && !empty($banner_request["loc"])):
			$proto = parse_url($banner_request["loc"], PHP_URL_SCHEME);
			$is_secure = $proto != null && $proto == "https";
		elseif (isset($banner_request["tld"]) && !empty($banner_request["tld"])):
			$proto = parse_url($banner_request["tld"], PHP_URL_SCHEME);
			$is_secure = $proto != null && $proto == "https";
		endif;
		
		/*
		 * Only set the secure flag if the page is https:// 
		 */
		if ($is_secure == true):
			$RtbBidRequestImp->secure				= 1;
		endif;
		
		$RtbBidRequestImp->media_type 				= $banner_request["ImpressionType"];
		
		if ($banner_request["ImpressionType"] == 'video'):
		
			$RtbBidRequestVideo = $this->create_rtb_request_video($config, $banner_request);
			$RtbBidRequestImp->RtbBidRequestVideo	= $RtbBidRequestVideo;
		else:
		
			$RtbBidRequestBanner = $this->create_rtb_request_banner($config, $banner_request);
			$RtbBidRequestImp->RtbBidRequestBanner	= $RtbBidRequestBanner;
		endif;
		
		$this->setObjParam($RtbBidRequestImp, $banner_request, "bidfloor");
		
		/*
		 * Private auctions not yet supported
		*  $RtbBidRequestImp->RtbBidRequestPmp
		*/
		
		$RtbBidRequest->RtbBidRequestImpList[] 		= $RtbBidRequestImp;
	
		$RtbBidRequestSite 							= new \model\openrtb\RtbBidRequestSite();
		$RtbBidRequestPublisher 					= new \model\openrtb\RtbBidRequestPublisher();
		
		$this->setObjParam($RtbBidRequestPublisher, $banner_request, "publisher_id", "id");
		$this->setObjParam($RtbBidRequestPublisher, $banner_request, "publisher_name", "name");
		$this->setObjParam($RtbBidRequestPublisher, $banner_request, "publisher_iab_category", "cat");
		$this->setObjParam($RtbBidRequestPublisher, $banner_request, "publisher_info_website", "domain");

		$this->setObjParam($RtbBidRequestSite, $banner_request, "website_id", "id");
		$this->setObjParam($RtbBidRequestSite, $banner_request, "website_name", "name");
		
		$this->setObjParam($RtbBidRequestSite, $banner_request, "org_tld", "domain");
		$this->setObjParam($RtbBidRequestSite, $banner_request, "iab_category", "cat");
		$this->setObjParam($RtbBidRequestSite, $banner_request, "loc", "page");
		$this->setObjParam($RtbBidRequestSite, $banner_request, "ref");

		$RtbBidRequestSite->RtbBidRequestPublisher 	= $RtbBidRequestPublisher;
		
		$RtbBidRequest->RtbBidRequestSite 			= $RtbBidRequestSite;
		
		$RtbBidRequestDevice 						= new \model\openrtb\RtbBidRequestDevice();
		
		$this->setObjParam($RtbBidRequestDevice, $banner_request, "user_agent", "ua");
		$this->setObjParam($RtbBidRequestDevice, $banner_request, "ip_address", "ip");
		$this->setObjParam($RtbBidRequestDevice, $banner_request, "language");
		$this->setObjParam($RtbBidRequestDevice, $banner_request, "devicetype", "type");
		
		if (isset($RtbBidRequestDevice->devicetype) && $RtbBidRequestDevice->type != DEVICE_DESKTOP):
			
			$this->setObjParam($RtbBidRequestDevice, $banner_request, "mobile_os", "os");
			$this->setObjParam($RtbBidRequestDevice, $banner_request, "mobile_make", "make");
			$this->setObjParam($RtbBidRequestDevice, $banner_request, "mobile_model", "model");
			
		endif;

		$RtbBidRequest->RtbBidRequestDevice 		= $RtbBidRequestDevice;
		
		if (!empty($banner_request["ip_address"])):
			$RtbBidRequestUser 						= new \model\openrtb\RtbBidRequestUser();
			$this->user_ip_hash						= md5($banner_request["ip_address"]);			
			$RtbBidRequestUser->id					= $this->user_ip_hash;
			$RtbBidRequest->RtbBidRequestUser 		= $RtbBidRequestUser;
		endif;

		// first price auction
		$RtbBidRequest->at 							= $config['settings']['rtb']['second_price_auction'] === true ? 2 : 1;

		// currency from the config file
		$RtbBidRequest->cur							= array($config['settings']['rtb']['auction_currency']);


		$RtbBidRequestRegulations					= new \model\openrtb\RtbBidRequestRegulations();
		$RtbBidRequestRegulations->coppa			= 1;

		$RtbBidRequest->RtbBidRequestRegulations 	= $RtbBidRequestRegulations;

		// assign response to instance
		$this->RtbBidRequest						= $RtbBidRequest;
		
	}
}

