<?php
/**
 * CDNPAL NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2015 CDNPAL Ltd. All Rights Reserved
 * @license GPLv3
 */

namespace buyrtb\workflows\tasklets\common\thirdparty;

class CheckPublisherScore {
	
	private static $class_name = 'CheckPublisherScore';
	
	protected static $traq_url = "http://api.adsafeprotected.com/db/client/1/absit?adsafe_url=";
	
	public static function execute(&$Logger, &$Workflow, \model\openrtb\RtbBidRequest &$RtbBidRequest) {
		/*
		 * This is a placeholder for a partner Site Scoring Tasklet
		*
		* IE. Integral Ad Science, Comscore ect...
		*/
		$page_to_check 		= self::get_url_to_check($Logger, $Workflow, $RtbBidRequest);
		
		if ($page_to_check == null):
			/*
			 * No publisher url or ad source page to check.
			 * This will never happen for a dashboard publisher
			 * but could happen for a DSP impression
			 */ 
			return true;
		endif;
		
		// global.php settings config
		
		if ($Workflow->config['settings']['rtb']['third_party_traq'] == true):
			$passed_traq 		= self::check_integral_traq($Workflow, $page_to_check);
			if ($passed_traq === false):
				// optionally do some logging here with $Logger
				return false;
			endif;
		endif;
		
        return true;
	}
	
	private static function check_integral_traq(&$Workflow, $page_to_check) {
		
		$score_codes = self::get_traq_score_from_service($Workflow->config, $page_to_check);
		
		if ($score_codes == null):
			return true;
		endif;
		
		if (!empty($score_codes["action"]) && $score_codes["action"] != "passed"):
			// overall action failed
			return false;
		endif;
		
		if (!empty($score_codes["bsc"]["adt"]) && intval($score_codes["bsc"]["adt"]) < 600):
			// has too much adult content - b00bz
			return false;
		endif;
		
		/*
		 * Most exchanges insist on the sam score being set and above 500-700
		* This is the most permissive setting.
		*
		* In this setting if it's not set it's ignored allowing no-name
		* un-indexed websites to sell inventory.
		*/
		if (!empty($score_codes["bsc"]["sam"]) && intval($score_codes["bsc"]["sam"]) < 500):
			// not safe enough, ad safety score too low
			return false;
		endif;
		
		return true;
	}
	
	private static function get_url_to_check(&$Logger, &$Workflow, \model\openrtb\RtbBidRequest &$RtbBidRequest) {
		
		// try a few OpenRTB params to find one that's present
		
		$page_to_check = null;
		
		if (!empty($RtbBidRequest->RtbBidRequestSite->page)):
			$page_to_check = $RtbBidRequest->RtbBidRequestSite->page;
		elseif (!empty($RtbBidRequest->RtbBidRequestSite->domain)):
			$page_to_check = $RtbBidRequest->RtbBidRequestSite->domain;
		elseif (!empty($RtbBidRequest->RtbBidRequestSite->RtbBidRequestPublisher->domain)):
			$page_to_check = $RtbBidRequest->RtbBidRequestSite->RtbBidRequestPublisher->domain;
		endif;
		
		return $page_to_check;
	}
	
	private static function get_traq_score_from_service($config, $page_to_check) {
		
		$traq_json_response 		= self::checkScoreCached($config, $page_to_check);
		
		if ($traq_json_response == null):
			$traq_url 				= self::$traq_url . urlencode($page_to_check);
			$raw_response 			= \util\WorkflowHelper::get_ping_notice_url_curl_request($traq_url);
			$traq_json_response 	= json_decode($raw_response, true);
			
			if ($traq_json_response != null):
				/*
				 * If we get valid response from the service,
				 * store it for 1 day, so we are not constantly  
				 * calling the service with the same parameters
				 */
				$params = array();
				$params["PageURL"] 	= $page_to_check;
				$one_day_in_seconds = 86400;
				\util\CacheSql::put_cached_read_result_apc($config, $params, self::$class_name, $traq_json_response, $one_day_in_seconds);
			endif;
			
		endif;
		
		return $traq_json_response;
	}
	
	private static function checkScoreCached($config, $page_to_check) {
	
		$params = array();
		$params["PageURL"] 	= $page_to_check;

		$json_result = \util\CacheSql::get_cached_read_result_apc($config, $params, self::$class_name);
	
		return $json_result;
	}
	
}
