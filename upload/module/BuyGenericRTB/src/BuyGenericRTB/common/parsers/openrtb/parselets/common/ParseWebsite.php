<?php
/**
 * CDNPAL NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2015 CDNPAL Ltd. All Rights Reserved
 * @license GPLv3
 */

namespace buyrtb\parsers\openrtb\parselets\common;
use \Exception;

class ParseWebsite {
	
	public static function execute(&$Logger, \buyrtb\parsers\openrtb\OpenRTBParser &$Parser, \model\openrtb\RtbBidRequest &$RtbBidRequest, \model\openrtb\RtbBidRequestSite &$RtbBidRequestSite, &$ad_campaign_site) {
	

			$RtbBidRequestPublisher = new \model\openrtb\RtbBidRequestPublisher();
			
			$Parser->parse_item(
					$RtbBidRequestSite,
					$ad_campaign_site,
					"id");

			$Parser->parse_item(
					$RtbBidRequestSite,
					$ad_campaign_site,
					"domain");
        
			$Parser->parse_item(
					$RtbBidRequestSite,
					$ad_campaign_site,
					"page");
			
	        if (isset($ad_campaign_site["cat"])):
	       		$RtbBidRequestSite->cat = self::get_category($ad_campaign_site["cat"]);
	        endif;
	        
	        if (strpos(strtolower($RtbBidRequestSite->domain), "https://") !== false
	        	|| strpos(strtolower($RtbBidRequestSite->page), "https://") !== false):
	        
	        	$RtbBidRequest->secure = 1;
	         
	        endif;

	        if (isset($ad_campaign_site["publisher"])):
	         
	        	$default_site_publisher = $ad_campaign_site["publisher"];
	        
	       		$Parser->parse_item(
		        		$RtbBidRequestPublisher,
		        		$default_site_publisher,
		        		"id");
	        
	       		$Parser->parse_item(
	       				$RtbBidRequestPublisher,
	       				$default_site_publisher,
	       				"name");
	        
		        if (isset($default_site_publisher["cat"])):
		        
		        	$RtbBidRequestPublisher->cat = self::get_category($default_site_publisher["cat"]);
		        		
		        endif;
		        
		        $Parser->parse_item(
		        		$RtbBidRequestPublisher,
		        		$default_site_publisher,
		        		"domain");
		        
	        endif;
	        
	        $RtbBidRequestSite->RtbBidRequestPublisher = $RtbBidRequestPublisher;
	
	}
	
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
	
	private static function get_category($main_category) {

		if ($main_category !== null):
			 
			/*
			 * Could be a subcategory like IAB10-2
			* In that case just get the main category and compare
			*/
			
			if (strpos($main_category, "-") !== false):
			 
				$main_category = substr($main_category, 0, strpos($main_category, "-"));
			
			endif;
			
			if (isset(self::$vertical_map[strtoupper($main_category)])):
			
				return self::$vertical_map[strtoupper($main_category)];
			
			endif;
			
		endif;
		
		return null;
	}
	
	public static $vertical_map = array(
	
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
}
