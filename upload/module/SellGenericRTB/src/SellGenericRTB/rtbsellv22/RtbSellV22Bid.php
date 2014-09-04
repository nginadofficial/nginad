<?php
/**
 * CDNPAL NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2015 CDNPAL Ltd. All Rights Reserved
 * @license GPLv3
 */

namespace rtbsellv22;
use \Exception;

 class RtbSellV22Bid {

	public $rtb_base_url = "http://rtb.demandpartner.com";

	public $had_bid_response = false;

	protected $rtb_provider = "none";

	/*
	 * RTB V2 API Request Params
	 */

	// REQUIRED

	// bid
	public $bid_request_id;
	
	// bid // imp
	public $bid_request_imp_id;
	
	public $bid_request_imp_banner_h;
	public $bid_request_imp_banner_w;
	public $bid_request_imp_banner_pos;
	public $bid_request_imp_bidfloor;
	public $bid_request_imp_pmp;
	
	/*
	0 Unknown 
	1 Above the fold 
	2 DEPRECATED - May or may not be immediately visible depending on 
	screen size and resolution. 
	3 Below the fold 
	4 Header 
	5 Footer 
	6 Sidebar 
	7 Fullscreen 
	 */
	
	// bid // cur
	
	public $bid_request_cur;
	
	// bid // site
	public $bid_request_site_domain;
	public $bid_request_site_page = "";
	public $bid_request_site_id;
	public $bid_request_site_category;
	
	// bid // site // publisher
	public $bid_request_site_publisher_id;
	public $bid_request_site_publisher_name;
	public $bid_request_site_publisher_cat;
	public $bid_request_site_publisher_domain;
	
	// bid // user
	public $bid_request_user_id;
	
	// does not exist in openRTB. Here for compatability with proprietary RTB
	public $bid_request_refurl = "";
	public $bid_request_secure;
	
	// bid // device
	public $bid_request_device_ip;
	public $bid_request_device_ua;
	public $bid_request_device_language;
	public $bid_request_device_os;
	public $bid_request_device_make;
	public $bid_request_device_model;
	
	// regs // coppa
	public $bid_request_regs_coppa;
	
	public $bid_request_mobile;
	public $bid_request_geo;

	// object containing the JSON request
	public $bid_request;
	
	/*
	 * RTB V2 API Response Params
	 */

	// REQUIRED

	public $bid_response_id;
	public $bid_response_bid;
	public $bid_response_buyer;
	public $bid_response_creativeId;
	public $bid_response_landingPageURL;
	public $bid_response_landingPageTLD;
	public $bid_response_requestId;

	// NOT REQUIRED

	public $bid_response_ebid;
	public $bid_response_bidCurrency;
	public $bid_response_creativeJSURL;
	public $bid_response_creativeHTMLURL;
	public $bid_response_creativeTAG;
	public $bid_response_creativeAttribute;

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
	 * A List of AdCampaignBanner ORM objects that matched all the business rules from the incoming request
	*/
	private $AdCampaignBanner_Match_List = array();

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
	
	
	public function build_rtb_bid_request() {
		$bid_request = array();
		
		$bid_request["id"] 		= $this->bid_request_imp_id;
		
		
		
		$banner 						= (object) array(
													'h' 		=> $this->bid_request_imp_banner_h, 
													'w'			=> $this->bid_request_imp_banner_w, 
													'pos'		=> $this->bid_request_imp_banner_pos);
		
		
		
		$impression 					= (object) array(	
													'id'		=> $this->bid_request_imp_id, 
													'banner' 	=> $banner,
													'bidfloor'	=> $this->bid_request_imp_bidfloor);
		
		$bid_request["imp"][] 		= $impression;

		$publisher_array				= array();
		
		$publisher_array['id'] 			= $this->bid_request_site_publisher_id;
		$publisher_array['name'] 		= $this->bid_request_site_publisher_name;
		
		if ($this->bid_request_site_publisher_cat !== null):
			$publisher_array['cat'] 	= $this->bid_request_site_publisher_cat;
		endif;
		
		$publisher_array['domain'] 		= $this->bid_request_site_publisher_domain;

		$publisher						= (object) $publisher_array;
		
		$site_array						= array();
		
		$site_array['id']				= $this->bid_request_site_id;
		$site_array['domain']			= $this->bid_request_site_domain;
		
		if ($this->bid_request_site_category !== null):
			$site_array['cat'] 	= $this->bid_request_site_category;
		endif;
		
		$site_array['cat']				= $this->bid_request_site_category;
		$site_array['page']				= $this->bid_request_site_page;
		// $site_array['publisher']		= $publisher;
		
		$bid_request["site"]		= (object) $site_array;
											
		$device_array 					= array();
		
		$device_array['ua'] 			= $this->bid_request_device_ua;
		$device_array['ip'] 			= $this->bid_request_device_ip;
		$device_array['devicetype'] 	= $this->bid_request_mobile == true ? 1 : 2;
		
		if ($this->bid_request_device_os !== null):
			$device_array['os'] 		= $this->bid_request_device_os;
		endif;
		
		if ($this->bid_request_device_make !== null):
			$device_array['make'] 		= $this->bid_request_device_make;
		endif;
		
		if ($this->bid_request_device_model !== null):
			$device_array['model'] 		= $this->bid_request_device_model;
		endif;;

		$bid_request["device"] 	= (object) $device_array;
		
		$bid_request["user"]		= (object) array(	
													'id'		=> $this->bid_request_user_id);
		
		/*
		 * at - auction type
		 * 1 - first price auction
		 * 2 - second price auction
		 */ 
		$bid_request["at"]			= 1;
	
		// currently only USD is supported
		$bid_request["cur"]			= array("USD");
		
		$bid_request["regs"]		= (object) array(
													'coppa'		=> 1);
		
		$this->bid_request 			= $bid_request;

		return $this->bid_request;
		
		//var_dump(json_encode($this->bid_request));
		
	}
	
	public function create_rtb_request_from_publisher_impression($config, $banner_request) {
		
		$this->org_request							= $banner_request;
		
		$this->bid_request_id 						= $this->generate_transaction_id();
		
		// bid // imp
		$this->bid_request_imp_id					= 1;
		
		$this->bid_request_imp_banner_h				= $banner_request["height"];
		$this->bid_request_imp_banner_w				= $banner_request["width"];

		$this->bid_request_imp_banner_pos 			= $banner_request["atf"] == 1 ? 1 : 0;
		
		$this->bid_request_imp_bidfloor				= $banner_request["bidfloor"];
		
		$this->bid_request_site_publisher_id		= $banner_request["publisher_id"];
		$this->bid_request_site_publisher_name		= $banner_request["publisher_name"];
		
		if (isset($banner_request["publisher_iab_category"]) && !empty($banner_request["publisher_iab_category"])):
		
			$this->bid_request_site_publisher_cat	= $banner_request["publisher_iab_category"];
		endif;
		
		$this->bid_request_site_publisher_domain	= $banner_request["publisher_info_website"];
		
		/*
		 * Private auctions not yet supported
		 * $this->bid_request_imp_pmp
		 */

		// currently we only support USD
		$this->bid_request_cur						= array("USD");
		
		// bid // site
		$this->bid_request_site_id					= $banner_request["website_id"];
		$this->bid_request_site_domain				= $banner_request["org_tld"];
		$this->bid_request_site_category			= $banner_request["iab_category"];
		$this->bid_request_site_page 				= $banner_request["loc"];
		
		
		$this->bid_request_site_publisher_cat		= $banner_request["iab_category"];
		
		// bid // site // publisher
		
		// does not exist in openRTB. Here for compatability with proprietary RTB
		if (isset($banner_request["ref"]) && !empty($banner_request["ref"])):
			$this->bid_request_refurl 				= $banner_request["ref"];
		endif;
		
		$is_secure = false;
		
		if (isset($banner_request["loc"]) && !empty($banner_request["loc"])):
			$proto = parse_url($banner_request["loc"], PHP_URL_SCHEME);
			$is_secure = $proto != null && $proto == "https";
		elseif (isset($banner_request["tld"]) && !empty($banner_request["tld"])):
			$proto = parse_url($banner_request["tld"], PHP_URL_SCHEME);
			$is_secure = $proto != null && $proto == "https";
		endif;

		$this->bid_request_secure 					= $is_secure == true ? 1 : 0;

		// bid // device
		/*
		 * According to OpenRTB 2.2 we only want to provide 
		 * either the IP addres OR the geo object, but we do not
		 * need to provide both of them in a single request.
		 */
		$this->bid_request_device_ip				= $banner_request["ip_address"];
		$this->bid_request_device_ua				= $banner_request["user_agent"];
		
		$this->bid_request_user_id					= $banner_request["user_id"];
		
		// $this->bid_request_geo; // not needed according to OpenRTB 2.2 since the IP is provided
		
		$this->bid_request_mobile					= $banner_request["devicetype"] == 1;
		
		if ($this->bid_request_mobile == true):
		
			if (isset($banner_request["mobile_os"]) && !empty($banner_request["mobile_os"])):
				$this->bid_request_device_os 	= $banner_request["mobile_os"];
			endif;
			
			if (isset($banner_request["mobile_make"]) && !empty($banner_request["mobile_make"])):
				$this->bid_request_device_make 	= $banner_request["mobile_make"];
			endif;
			
			if (isset($banner_request["mobile_model"]) && !empty($banner_request["mobile_model"])):
				$this->bid_request_device_model = $banner_request["mobile_model"];
			endif;
			
		endif;

		if (isset($banner_request["language"]) && !empty($banner_request["language"])):
		
			$this->bid_request_device_language		= $banner_request["language"];
		endif;
		
		// regs // coppa
		$this->bid_request_regs_coppa				= 1;
	}
}

