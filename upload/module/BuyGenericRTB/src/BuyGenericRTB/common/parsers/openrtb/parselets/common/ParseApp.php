<?php
/**
 * CDNPAL NGINAD Customization
 */

namespace buyrtbsmaato\parsers\openrtb\parselets\common;

class ParseApp {
	
	public static function execute(&$Logger, \buyrtbsmaato\parsers\openrtb\OpenRTBParser &$Parser, \model\openrtb\RtbBidRequest &$RtbBidRequest, \model\openrtb\RtbBidRequestApp &$RtbBidRequestApp, &$rtb_app) {
	
		
		// ID
			
		\util\ParseHelper::parse_item(
				$RtbBidRequestApp,
				$rtb_app,
				"id");
		
		// App Name
			
		\util\ParseHelper::parse_item(
				$RtbBidRequestApp,
				$rtb_app,
				"name");
			
		// App Domain
			
		\util\ParseHelper::parse_item(
				$RtbBidRequestApp,
				$rtb_app,
				"domain");
			
		// App Categories
			
		\util\ParseHelper::parse_item_list(
				$RtbBidRequestApp,
				$rtb_app,
				"cat");
			
		self::fix_iab_categories($RtbBidRequestApp, "cat");
			
		// App Subsection Categories
		
		\util\ParseHelper::parse_item_list(
				$RtbBidRequestApp,
				$rtb_app,
				"sectioncat");
			
		self::fix_iab_categories($RtbBidRequestApp, "sectioncat");
			
		// App Categories for the page/view the ad zone for this impression is fired off from
			
		\util\ParseHelper::parse_item_list(
				$RtbBidRequestApp,
				$rtb_app,
				"pagecat");
			
		self::fix_iab_categories($RtbBidRequestApp, "pagecat");
			
		// Version of app
		
		\util\ParseHelper::parse_item(
				$RtbBidRequestApp,
				$rtb_app,
				"ver");
			
		// App Bundle
		
		\util\ParseHelper::parse_item(
				$RtbBidRequestApp,
				$rtb_app,
				"bundle");
		
		// Flag for Privacy Policy
			
		\util\ParseHelper::parse_item(
				$RtbBidRequestApp,
				$rtb_app,
				"privacypolicy");
			

			
		// Is App Paid?
			
		\util\ParseHelper::parse_item(
				$RtbBidRequestApp,
				$rtb_app,
				"paid");
		
		/*
		 * Publisher Object
		*/
		 
		if (isset($rtb_app["publisher"])):
		
			$RtbBidRequestPublisher = new \model\openrtb\RtbBidRequestPublisher();
			 
			$default_site_publisher = $rtb_app["publisher"];
			 
			// Publisher ID
			
			\util\ParseHelper::parse_item(
					$RtbBidRequestPublisher,
					$default_site_publisher,
					"id");
			 
			// Publisher Name
			
			\util\ParseHelper::parse_item(
					$RtbBidRequestPublisher,
					$default_site_publisher,
					"name");
			 
			// Publisher Categories
			 
			\util\ParseHelper::parse_item_list(
					$RtbBidRequestPublisher,
					$default_site_publisher,
					"cat");
			
			self::fix_iab_categories($RtbBidRequestPublisher, "cat");
			
			// Publisher Domain
			
			\util\ParseHelper::parse_item(
					$RtbBidRequestPublisher,
					$default_site_publisher,
					"domain");
			
			$RtbBidRequestApp->RtbBidRequestPublisher = $RtbBidRequestPublisher;
			
		endif;
		 
		/*
		 * Site Content Object
		*/
		 
		if (isset($rtb_app["content"])):
			
			$RtbBidRequestContent = new \model\openrtb\RtbBidRequestContent();
			 
			$default_site_content = $rtb_app["content"];
			 
			// Site Content ID
			
			\util\ParseHelper::parse_item(
					$RtbBidRequestContent,
					$default_site_content,
					"id");
			
			// Episode Number
			
			\util\ParseHelper::parse_item(
					$RtbBidRequestContent,
					$default_site_content,
					"episode");
			
			// Title
			
			\util\ParseHelper::parse_item(
					$RtbBidRequestContent,
					$default_site_content,
					"title");
			
			// Content Series
			
			\util\ParseHelper::parse_item(
					$RtbBidRequestContent,
					$default_site_content,
					"series");
			
			// Content Season
			
			\util\ParseHelper::parse_item(
					$RtbBidRequestContent,
					$default_site_content,
					"season");
			
			// Content Original URL
			
			\util\ParseHelper::parse_item(
					$RtbBidRequestContent,
					$default_site_content,
					"url");
			
			// Content Categories
			 
			\util\ParseHelper::parse_item_list(
					$RtbBidRequestContent,
					$default_site_content,
					"cat");
			
			self::fix_iab_categories($RtbBidRequestContent, "cat");
			
			// Content Video Quality
			
			\util\ParseHelper::parse_item(
					$RtbBidRequestContent,
					$default_site_content,
					"videoquality");
			
			// Content Video Quality
			
			\util\ParseHelper::parse_item(
					$RtbBidRequestContent,
					$default_site_content,
					"videoquality");
			
			// Content Keywords Meta
			/*
			 * Apparently Neal and Jim not sure on this one,
			* saying it could be a string or an array of strings
			*/
			
			if (isset($default_site_content["keywords"])):
				
				if (is_array($default_site_content["keywords"])):
					 
					\util\ParseHelper::parse_item_list(
							$RtbBidRequestContent,
							$default_site_content,
							"keywords");
				else:
				 
					\util\ParseHelper::parse_item(
							$RtbBidRequestContent,
							$default_site_content,
							"keywords");
				 
				endif;
				
			endif;
			
			// Content Rating
			
			\util\ParseHelper::parse_item(
					$RtbBidRequestContent,
					$default_site_content,
					"contentrating");
			
			// User Rating
			
			\util\ParseHelper::parse_item(
					$RtbBidRequestContent,
					$default_site_content,
					"userrating");
			
			// Context of Content
			
			\util\ParseHelper::parse_item(
					$RtbBidRequestContent,
					$default_site_content,
					"context");
			
			// Flag Indicating if Content is Live
			
			\util\ParseHelper::parse_item(
					$RtbBidRequestContent,
					$default_site_content,
					"livestream");
			
			// Flag Indicating if Content Source is Direct or Indirect
			
			\util\ParseHelper::parse_item(
					$RtbBidRequestContent,
					$default_site_content,
					"sourcerelationship");
			
			/*
			 * Site Content Video Producer Object
			*/
			
			if (isset($default_site_content["producer"])):
				 
				$RtbBidRequestProducer = new \model\openrtb\RtbBidRequestProducer();
				
				$default_site_content_producer = $default_site_content["producer"];
				
				// Video Producer ID
				 
				\util\ParseHelper::parse_item(
						$RtbBidRequestProducer,
						$default_site_content_producer,
						"id");
				 
				// Video Producer Name
				
				\util\ParseHelper::parse_item(
						$RtbBidRequestProducer,
						$default_site_content_producer,
						"name");
				 
				// Video Producer Categories
				
				\util\ParseHelper::parse_item_list(
						$RtbBidRequestProducer,
						$default_site_content_producer,
						"cat");
				 
				self::fix_iab_categories($RtbBidRequestProducer, "cat");
				 
				// Video Producer Domain
				 
				\util\ParseHelper::parse_item(
						$RtbBidRequestProducer,
						$default_site_content_producer,
						"domain");
				 
				$RtbBidRequestContent->RtbBidRequestProducer = $RtbBidRequestProducer;
				 
			endif;
			 
			// Length of Content
			
			\util\ParseHelper::parse_item(
					$RtbBidRequestContent,
					$default_site_content,
					"len");
			
			// QAG Media Rating of Content
			
			\util\ParseHelper::parse_item(
					$RtbBidRequestContent,
					$default_site_content,
					"qagmediarating");
			
			// QAG Video Addendum Embeddable Flag
			
			\util\ParseHelper::parse_item(
					$RtbBidRequestContent,
					$default_site_content,
					"embeddable");
			
			// Content Language
			
			\util\ParseHelper::parse_item(
					$RtbBidRequestContent,
					$default_site_content,
					"language");
			 
			$RtbBidRequestApp->RtbBidRequestContent = $RtbBidRequestContent;
			 
		endif;
		 
		// Website Keywords Meta
		/*
		 * Apparently Neal and Jim not sure on this one,
		* saying it could be a string or an array of strings
		*/
		
		if (isset($rtb_app["keywords"])):
		 
			if (is_array($rtb_app["keywords"])):
			 
				\util\ParseHelper::parse_item_list(
						$RtbBidRequestApp,
						$rtb_app,
						"keywords");
			else:
				
				\util\ParseHelper::parse_item(
						$RtbBidRequestApp,
						$rtb_app,
						"keywords");
				
			endif;
			 
		endif;
		 
		// The URL for the App Store for this App
			
		\util\ParseHelper::parse_item(
				$RtbBidRequestApp,
				$rtb_app,
				"storeurl");
		
	}
	
	private static function fix_iab_categories(&$obj, $name) {
		if (isset($obj->$name) && is_array($obj->$name)):
			self::fix_iab_category_list($obj->$name);
		else:
			unset($obj->$name);
		endif;
	}
	
	private static function fix_iab_category_list(&$iab_category_list) {
	
		for ($i = 0; $i < count($iab_category_list); $i++):
			$iab_category_list[$i] = self::get_category($iab_category_list[$i]);
		endfor;
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
