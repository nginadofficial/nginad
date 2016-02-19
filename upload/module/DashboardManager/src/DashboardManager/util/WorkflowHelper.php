<?php

namespace util;
	
class WorkflowHelper {
	
	protected static $user_agent 			= "NginAd RTB Ping Robot";
	
	protected static $verify_ssl 			= false;
	

	public static function get_ping_notice_url_curl_request($url) {
	
		//open connection
		$ch = curl_init();
	
		//set the url, number of POST vars, POST data
		curl_setopt( $ch, CURLOPT_USERAGENT, self::$user_agent );
		curl_setopt( $ch, CURLOPT_URL, $url);
		curl_setopt( $ch, CURLOPT_POST, false);
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
		curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, self::$verify_ssl );
		curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, self::$verify_ssl );
		curl_setopt( $ch, CURLOPT_HEADER, false);
		curl_setopt( $ch, CURLOPT_SSLVERSION, 3);
		curl_setopt( $ch, CURLOPT_MAXREDIRS, 10);
	
		// don't take more than 5 seconds connecting and 10 seconds for a response
		curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, 5 );
		curl_setopt( $ch, CURLOPT_TIMEOUT, 10 );
	
		$content = curl_exec($ch);
	
		return $content;
	}
	
	public static function get_first_key($input) {
		
		foreach ($input as $key => $value):
		
			return $key;
		
		endforeach;		
		
		return null;
	}
	
	public static function getIdsFromRtbRequest(&$RtbBidRequest) {

		$rtb_channel_site_id				= "N/A";
		$rtb_channel_site_name				= "N/A";
		$rtb_channel_publisher_name			= "N/A";
		$rtb_channel_site_iab_category		= null;
		$impressions_offered_counter 		= 0;
		$tld								= "not_available";
		
		if (isset($RtbBidRequest->RtbBidRequestSite->id)):
			$rtb_channel_site_id = $RtbBidRequest->RtbBidRequestSite->id;
		elseif (isset($RtbBidRequest->RtbBidRequestApp->id)):
			$rtb_channel_site_id = $RtbBidRequest->RtbBidRequestApp->id;
		endif;
		if (isset($RtbBidRequest->RtbBidRequestSite->name)):
			$rtb_channel_site_name = $RtbBidRequest->RtbBidRequestSite->name;
		elseif (isset($RtbBidRequest->RtbBidRequestApp->name)):
			$rtb_channel_site_name = $RtbBidRequest->RtbBidRequestApp->name;
		endif;
		if (isset($RtbBidRequest->RtbBidRequestSite->RtbBidRequestPublisher->name)):
			$rtb_channel_publisher_name = $RtbBidRequest->RtbBidRequestSite->RtbBidRequestPublisher->name;
		elseif (isset($RtbBidRequest->RtbBidRequestApp->RtbBidRequestPublisher->name)):
			$rtb_channel_publisher_name = $RtbBidRequest->RtbBidRequestApp->RtbBidRequestPublisher->name;
		endif;
			
		if (isset($RtbBidRequest->RtbBidRequestSite->cat[0])):
			$rtb_channel_site_iab_category = $RtbBidRequest->RtbBidRequestSite->cat[0];
		elseif (isset($RtbBidRequest->RtbBidRequestApp->cat[0])):
			$rtb_channel_site_iab_category = $RtbBidRequest->RtbBidRequestApp->cat[0];
		endif;
			
		if ($rtb_channel_site_iab_category === null && isset($RtbBidRequest->RtbBidRequestSite->sectioncat[0])):
			$rtb_channel_site_iab_category = $RtbBidRequest->RtbBidRequestSite->sectioncat[0];
		elseif ($rtb_channel_site_iab_category === null && isset($RtbBidRequest->RtbBidRequestApp->sectioncat[0])):
			$rtb_channel_site_iab_category = $RtbBidRequest->RtbBidRequestApp->sectioncat[0];
		endif;
			
		if ($rtb_channel_site_iab_category === null && isset($RtbBidRequest->RtbBidRequestSite->pagecat[0])):
			$rtb_channel_site_iab_category = $RtbBidRequest->RtbBidRequestSite->pagecat[0];
		elseif ($rtb_channel_site_iab_category === null && isset($RtbBidRequest->RtbBidRequestApp->pagecat[0])):
			$rtb_channel_site_iab_category = $RtbBidRequest->RtbBidRequestApp->pagecat[0];
		endif;
			
		if ($rtb_channel_site_iab_category !== null):
			$rtb_channel_site_iab_category = array_search($rtb_channel_site_iab_category,
				self::$vertical_map);
		endif;
		if ($rtb_channel_site_iab_category === null || $rtb_channel_site_iab_category === false):
			$rtb_channel_site_iab_category = "N/A";
		endif;
		
		if (isset($RtbBidRequest->RtbBidRequestImpList) && is_array($RtbBidRequest->RtbBidRequestImpList)):
			$impressions_offered_counter = count($RtbBidRequest->RtbBidRequestImpList);
		endif;
		
		$parse = null;
		
		if (isset($RtbBidRequest->RtbBidRequestSite->domain)):
			if (strpos($RtbBidRequest->RtbBidRequestSite->domain, 'http') !== 0):
				$RtbBidRequest->RtbBidRequestSite->domain = 'http://' . $RtbBidRequest->RtbBidRequestSite->domain;
			endif;
			$parse = parse_url($RtbBidRequest->RtbBidRequestSite->domain);
		elseif (isset($RtbBidRequest->RtbBidRequestApp->domain)):
			if (strpos($RtbBidRequest->RtbBidRequestApp->domain, 'http') !== 0):
				$RtbBidRequest->RtbBidRequestApp->domain = 'http://' . $RtbBidRequest->RtbBidRequestApp->domain;
			endif;
			$parse = parse_url($RtbBidRequest->RtbBidRequestApp->domain);
		endif;
		
		if (!empty($parse) && isset($parse['host'])):
			$tld = $parse['host'];
		else:
			if (isset($RtbBidRequest->RtbBidRequestSite->page)):
				if (strpos($RtbBidRequest->RtbBidRequestSite->page, 'http') !== 0):
					$RtbBidRequest->RtbBidRequestSite->page = 'http://' . $RtbBidRequest->RtbBidRequestSite->page;
				endif;
				$parse = parse_url($RtbBidRequest->RtbBidRequestSite->page);
			elseif (isset($RtbBidRequest->RtbBidRequestApp->page)):
				if (strpos($RtbBidRequest->RtbBidRequestApp->page, 'http') !== 0):
					$RtbBidRequest->RtbBidRequestApp->page = 'http://' . $RtbBidRequest->RtbBidRequestApp->page;
				endif;
				$parse = parse_url($RtbBidRequest->RtbBidRequestApp->page);
			endif;
			if (!empty($parse) && isset($parse['host'])):
				$tld = $parse['host'];
			endif;
		endif;
		
		return array(
				"rtb_channel_site_id" 				=> $rtb_channel_site_id,
				"rtb_channel_site_name" 			=> $rtb_channel_site_name,
				"rtb_channel_publisher_name"		=> $rtb_channel_publisher_name,
				"rtb_channel_site_iab_category"		=> $rtb_channel_site_iab_category,
				"impressions_offered_counter"		=> $impressions_offered_counter,
				"tld"								=> $tld
				);
		
	}
	
	public static $vertical_map = array(
	
			/*
			 * also covers 1-x for sub-categories
			 * get the first 4-5 chars substring and
			 * only compare the main categories
			 *
			 * Ex: IAB10-2 becomes IAB10 and matches to 16
			 */

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
			"IAB17"=>"33",			// Sports
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
