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

abstract class RtbBuyV22Bid {

	public $rtb_base_url = "http://rtb.demandpartner.com";

	public $had_bid_response = false;

	protected $rtb_provider = "none";

	// will be used for stats
	public $rtb_seat_id = null;
	
	public $response_seat_id = null;
	
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

	// bid // site // publisher
	public $bid_request_site_publisher_cat;
	
	// does not exist in openRTB. Here for compatability with proprietary RTB
	public $bid_request_refurl = "";
	public $bid_request_secure;
	
	// bid // device
	public $bid_request_device_ip;
	public $bid_request_device_ua;
	public $bid_request_mobile;
	public $bid_request_geo;
	public $bid_request_device_language;
	
	// regs // coppa
	public $bid_request_regs_coppa;
	
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

	/*
	 * A List of AdCampaignBanner ORM objects that matched all the business rules from the incoming request
	*/
	private $AdCampaignBanner_Match_List = array();

	private $expeption_missing_min_bid_request_params = "Bid Request missing required parameter";
	private $missing_optional_bid_request_params = "Bid Request missing optional parameter";
	private $got_optional_bid_request_params = "Got Bid Request optional parameter";

	// transaction id is not specified, autogenerate

	/*
		1 Not Applicable 
		2 Automotive 
		3 Business and Finance 
		8 Education 
		9 Employment and Career 
		10 Entertainment and Leisure 
		12 Gaming 
		14 Health and Fitness 
		16 Home and Garden 
		18 Men's Interest 
		21 Music 
		23 News 
		24 Parenting and Family 
		27 Real Estate 
		28 Reference 
		29 Food and Dining 
		31 Shopping 
		32 Social Networking 
		33 Sports 
		34 Technology 
		36 Travel 
		38 Women's Interest 
	 */
	
	private $vertical_map = array(
			
			/*
			 * also covers 1-x for sub-categories
			 * get the first 4-5 chars substring and
			 * only compare the main categories
			 * 
			 * Ex: IAB10-2 becomes IAB10 and matches to 16
			 */ 
			// 
			
			"IAB1"=>"10",  			// Arts & Entertainment
			"IAB2"=>"2",			// Automotive
			"IAB3"=>"3",			// Business
			"IAB4"=>"9",			// Careers
			"IAB5"=>"8",			// Education
			"IAB6"=>"24",			// Family & Parenting
			"IAB7"=>"14",			// Health & Fitness
			"IAB8"=>"29",			// Food & Drink
			"IAB9"=>"18",			// Hobbies & Interests
			"IAB10"=>"16",			// Home & Garden
			"IAB11"=>"28",			// Law, Gov't & Politics
			"IAB12"=>"23",			// News		
			"IAB13"=>"3",			// Personal Finance
			"IAB14"=>"38",			// Society
			"IAB15"=>"34",			// Science
			"IAB16"=>"1",			// Pets
			"IAB16"=>"33",			// Sports
			"IAB18"=>"38",			// Style & Fashion
			"IAB19"=>"34",			// Technology & Computing
			"IAB20"=>"36",			// Travel
			"IAB21"=>"27",			// Real Estate
			"IAB22"=>"31",			// Shopping
			"IAB23"=>"1",			// Religion & Spirituality
			"IAB24"=>"1",			// Uncategorized
			"IAB25"=>"1",			// Non-Standard Content
			"IAB26"=>"1",			// Illegal Content
	);
	
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

		/*
		 * get TLD of the site url or page url for the
		 * ad tag in case it's needed for the delivery module
		 */

		$tld = "not_available";
		// bid // site

		$parse = parse_url($this->bid_request_site_domain);
		if (isset($parse['host'])):
			$tld = $parse['host'];
		else:
			$parse = parse_url($this->bid_request_site_page);
			if (isset($parse['host'])):
				$tld = $parse['host'];
			endif;
		endif;

		$this->user_ip_hash = md5($this->bid_request_device_ip);
		$http_regex = '/https?\:\/\/[^\" ]+/i';
		 
		$users_that_dont_fill_list = array();
		 
		if (count($this->AdCampaignBanner_Match_List)):
			$UsersThatDontFillFactory = \_factory\UsersThatDontFill::get_instance();
			$users_that_dont_fill_list = $UsersThatDontFillFactory->get_cached($this->config, array());
		endif;
		
	    $RtbBidResponse = array("id"=>$this->bid_request_id);
	    $RtbBidResponse["seatbid"] = array();
	    
		foreach ($this->AdCampaignBanner_Match_List as $AdCampaignBanner):
		
			$bidresponse = array();
		
			$bidresponse["id"] = $this->bid_request_id;
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
		
		$RtbBidResponse["cur"] = "USD";
		
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

			$cache_buster = time();
			
			$effective_tag = "<script type='text/javascript' src='" . $delivery_adtag . "?zoneid=" . $AdCampaignBanner->AdCampaignBannerID . "&buyerid=" . $this->rtb_seat_id . "&height=" . $AdCampaignBanner->Height . "&width=" . $AdCampaignBanner->Width . "&tld=" . $tld . "&ct0={NGINTRK}&sndprc={NGIN2PRC}&ui=" . $this->user_ip_hash . "&cb=" . $cache_buster . "'></script>";
			
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

	    $this->AdCampaignBanner_Match_List = \rtbbuyv22\RtbBuyV22Workflow::get_instance()->process_business_rules_workflow($this);
	}

	public function parse_incoming_request($raw_post = null) {

		/*
		 * Get the incoming bid request data from the
		 * HTTP REQUEST
		 *
		 * If the required fields are not there throw an exception
		 * to the caller
		 */

		/*
		 * mobile, rich media, ect..
		 * mobile web, phone, tablet, native iOS or native Android
		 */
		
		if ($raw_post === null):
			$raw_post = file_get_contents('php://input');
		endif;
		
		\rtbbuyv22\RtbBuyV22Logger::get_instance()->min_log[] = "POST: " . $raw_post;

		if ($raw_post):
		    $json_post = json_decode($raw_post, true);
        else:
            $json_post = null;
        endif;

        $this->bid_request_mobile = 0;
        
		if ($json_post === null):
		
			throw new Exception($this->expeption_missing_min_bid_request_params . ": JSON POST DATA");
		
		endif;
		
        \rtbbuyv22\RtbBuyV22Logger::get_instance()->log[] = "POST: " . print_r($json_post, true);

        if (isset($json_post["id"])):
        	
        	$this->bid_request_id = $json_post["id"];
        	
        else:
        	
        	throw new Exception($this->expeption_missing_min_bid_request_params . ": id");

        endif;
        
        if (isset($json_post["imp"][0])):
        	
        	$default_impression = $json_post["imp"][0];
        	
        	if (isset($default_impression["id"])):
        		$this->bid_request_imp_id 			= $default_impression["id"];
        	else: 
        		throw new Exception($this->expeption_missing_min_bid_request_params . ": imp_id");
        	endif;
        	
        	if (isset($default_impression["banner"]["h"])):
        		$this->bid_request_imp_banner_h 	= $default_impression["banner"]["h"];
        	else:
        		throw new Exception($this->expeption_missing_min_bid_request_params . ": imp_banner_h");
        	endif;
        	
        	if (isset($default_impression["banner"]["w"])):
        		$this->bid_request_imp_banner_w 	= $default_impression["banner"]["w"];
        	else:
        		throw new Exception($this->expeption_missing_min_bid_request_params . ": imp_banner_w");
        	endif;

        	/*
        	 * OpenRTB 2.1
        	*
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
        	 
        	/*
        	 * Proprietary DB values
        	*
        	uFoldPos No No User defined Fold position of the ad slot. •
        	0 – Not available/applicable •
        	1 – Completely Above the Fold •
        	2 – Completely Below the Fold  •
        	3 – Partially Above the Fold
        	
        	sFoldPos No No System detected Fold position of the ad slot. •
        	0 – Not available/applicable •
        	1 – Completely Above the Fold •
        	2 – Completely Below the Fold  •
        	3 – Partially Above the Fold
        	*/
        	
        	/*
        	 * do conversion here
        	 */
        	
        	if (isset($default_impression["banner"]["pos"])):
        	
        		if ($default_impression["banner"]["pos"] == 1
        					|| $default_impression["banner"]["pos"] == 4):
        					
        			$this->bid_request_imp_banner_pos 	= 1;
        		
        		elseif ($default_impression["banner"]["pos"] == 6):
        		
        			$this->bid_request_imp_banner_pos 	= 3;
        		
        		elseif ($default_impression["banner"]["pos"] == 3
        					|| $default_impression["banner"]["pos"] == 5):
        					
        			$this->bid_request_imp_banner_pos 	= 2;
        		
        		endif;
        		
        	else:
        		throw new Exception($this->expeption_missing_min_bid_request_params . ": imp_banner_pos");
        	endif;

        	if (isset($default_impression["pmp"])):
        		$this->bid_request_imp_pmp = 1;
        	endif;
        	
        else:
        	
        	throw new Exception($this->expeption_missing_min_bid_request_params . ": at least 1 imp object");

        endif;

        if (isset($json_post["cur"][0])):
        
        	$this->bid_request_cur = strtoupper($json_post["cur"][0]);

        	if ($this->bid_request_cur != "USD"):
        	
        		throw new Exception($this->expeption_missing_min_bid_request_params . ": cur: system only accepts USD currency at this time");
        	
        	endif;
        
        else:
        
	        throw new Exception($this->expeption_missing_min_bid_request_params . ": at least 1 cur object");
        
        endif;
        	
        if (isset($json_post["site"])):
         
	        $default_site = $json_post["site"];
	         
	        if (isset($default_site["domain"])):
	        	$this->bid_request_site_domain 		= $default_site["domain"];
	        else:
	        	throw new Exception($this->expeption_missing_min_bid_request_params . ": site_domain");
	        endif;
        
	        if (isset($default_site["page"])):
	        	$this->bid_request_site_page 		= $default_site["page"];
	        endif;
	        
	        if (strpos(strtolower($this->bid_request_site_domain), "https://") !== false
	        	|| strpos(strtolower($this->bid_request_site_page), "https://") !== false):
	        
	        	$this->bid_request_secure = 1;
	         
	        endif;

	        if (isset($default_site["publisher"])):
	         
	        	$default_site_publisher = $default_site["publisher"];
		        if (isset($default_site_publisher["cat"])):
		       		$main_category = $default_site_publisher["cat"];
		        	
		        	if ($main_category !== null):
		        	
			        	/*
			        	 * Could be a subcategory like IAB10-2
			        	 * In that case just get the main category and compare
			        	 */
						
			        	if (strpos($main_category, "-") !== false):
			        		
			        		$main_category = substr($main_category, 0, strpos($main_category, "-"));
			        	
			        	endif;
			        	
			        	if (isset($this->vertical_map[strtoupper($main_category)])):
			        	
			        		$this->bid_request_site_publisher_cat = $this->vertical_map[strtoupper($main_category)];
			        	
			        	endif;
			        			
		        	endif;
		        		
		        endif;
	        
	        endif;
	        
        else:
         
        	throw new Exception($this->expeption_missing_min_bid_request_params . ": at least 1 site object");
        
        endif;
        
        $this->bid_request_mobile = 0;
        
        if (isset($json_post["device"])):
         
	        $default_device = $json_post["device"];
	         
	        if (isset($default_device["ip"])):
	        	$this->bid_request_device_ip 		= $default_device["ip"];
	        else:
	        	throw new Exception($this->expeption_missing_min_bid_request_params . ": device_ip");
	        endif;

	        if (isset($default_device["language"])):
	        	$this->bid_request_device_language 		= $default_device["language"];
	        endif;
	        
	        if (isset($default_device["model"])):
	        
				if (\mobileutil\MobileDeviceType::isPhone($default_device["model"]) === true):
			        
			   		$this->bid_request_mobile = 1;
			        
			  	elseif(\mobileutil\MobileDeviceType::isTablet($default_device["model"]) === true):
			        
			  		$this->bid_request_mobile = 2;
			        
			 	endif;
	        
	        elseif (isset($default_device["ua"])):
		        
		       	$this->bid_request_device_ua 		= $default_device["ua"];
		        
			    if (strpos($this->bid_request_device_ua, '%20') !== false):
			        $this->bid_request_device_ua = urldecode($this->bid_request_device_ua);
			    endif;
			        
			    $detect = new \mobileutil\MobileDetect(null, $this->bid_request_device_ua);
			        
			    if ($detect->isTablet() || ($detect->isMobile() && !$this->phone_size())):
			    
			       	$this->bid_request_mobile = 2;
			    
			    elseif ($detect->isMobile()):
			    
			       	$this->bid_request_mobile = 1;
			    
			    endif;

	        endif;

	        if (isset($default_device["geo"])):

	        	$geo = $default_device["geo"];
	        		
	        	$this->bid_request_geo = array();
	        
	        	if (isset($geo["country"])):
	        		$this->bid_request_geo["country"] = $geo["country"];
	        	endif;
	        
	        	if (isset($geo["region"])):
	        		$this->bid_request_geo["state"] = $geo["region"];
	        	endif;
	        	
	        	if (isset($geo["city"])):
	        		$this->bid_request_geo["city"] = $geo["city"];
	        	endif;
	        	
		        if (isset($this->bid_request_geo["country"])):
		        	\rtbbuyv22\RtbBuyV22Logger::get_instance()->log[] = "Geo Data Country: " . $this->bid_request_geo["country"];
		        endif;
		        if (isset($this->bid_request_geo["state"])):
		        	\rtbbuyv22\RtbBuyV22Logger::get_instance()->log[] = "Geo Data State: " . $this->bid_request_geo["state"];
		        endif;
		        if (isset($this->bid_request_geo["city"])):
		        	\rtbbuyv22\RtbBuyV22Logger::get_instance()->log[] = "Geo Data City: " . $this->bid_request_geo["city"];
		        endif;
	        
	        endif;
	        
	        
        else:
         
        	throw new Exception($this->expeption_missing_min_bid_request_params . ": at least 1 site object");
        
        endif;
        
        if (isset($json_post["regs"])):
         
        	$default_regs = $json_post["regs"];
         
        	if (isset($default_regs["regs"])):
        		$this->bid_request_regs_coppa 		= $default_regs["regs"];
        	endif;
        endif;

       \rtbbuyv22\RtbBuyV22Logger::get_instance()->log[] = "Is Mobile: " . $this->bid_request_mobile;


	}

	private function phone_size() {

	    $wxh = $this->bid_request_adWidth . "x" . $this->bid_request_adHeight;
	    return isset(\util\BannerOptions::$iab_mobile_phone_banner_options[$wxh]);
	}

}

