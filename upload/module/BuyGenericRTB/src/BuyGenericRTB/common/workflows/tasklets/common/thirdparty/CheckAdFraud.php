<?php
/**
 * NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2016 NginAd Foundation. All Rights Reserved
 * @license GPLv3
 */

namespace buyrtb\workflows\tasklets\common\thirdparty;

class CheckAdFraud {
	
	private static $class_name = 'CheckAdFraud';
	
	protected static $forensiq_url = "http://api.forensiq.com/check";
	
	public static function execute(&$Logger, &$Workflow, \model\openrtb\RtbBidRequest &$RtbBidRequest) {
    	/*
    	 * This is a placeholder for a User Scoring Tasklet
    	 * 
    	 * IE. DoubleVerify, Forensiq, Moat, ect...
    	*/
		
		$page_to_check = self::get_url_to_check($Logger, $Workflow, $RtbBidRequest);

		/*
		 * Check valid IP Address
		 */
		
		$remote_ip = $RtbBidRequest->RtbBidRequestDevice->ip;
		
		if (empty($remote_ip) || !filter_var($remote_ip, FILTER_VALIDATE_IP)):
			// optionally do some logging here with $Logger
			return false;
		endif;
		
		/*
		 * Google Project Honeypot Check
		 * The only free bot detection online AFAIK
		 */
		
		// global.php settings config
		
		if ($Workflow->config['settings']['rtb']['tor_protected'] == true):
			
			$is_tor_request = self::get_request_from_tor_browser($Workflow, $remote_ip);
			if ($is_tor_request === true):
				// optionally do some logging here with $Logger
				return false;
			endif;
		endif;
		
		
		if ($Workflow->config['settings']['rtb']['project_honeypot_protected'] == true):
			
			$is_honeypot_safe = self::get_honeypot_score_from_service($Workflow->config, $remote_ip);
			if ($is_honeypot_safe === false):
				// optionally do some logging here with $Logger
				return false;
			endif;
		endif;
		
		if ($Workflow->config['settings']['rtb']['forensiq_protected'] == true):
			$passed_forensiq 		= self::check_forensiq($Workflow, $page_to_check, $RtbBidRequest);
			if ($passed_forensiq === false):
				// optionally do some logging here with $Logger
				return false;
			endif;
		endif;

        return true;
	}
	
	protected static function get_request_from_tor_browser(&$Workflow, $remote_ip) {

		$params = array();
		$apc_cached_tor_ip_list = \util\CacheSql::get_cached_read_result_apc_type_convert($Workflow->config, $params, "Maintenance");
		
		/*
		 * IP is a tor IP address
		 */

		if (!empty($apc_cached_tor_ip_list) && isset($apc_cached_tor_ip_list[$remote_ip])):
			return false;
		endif;
	}
	
	protected static function check_forensiq(&$Workflow, $page_to_check, \model\openrtb\RtbBidRequest &$RtbBidRequest) {
	
		$score_codes = self::get_forensiq_score_from_service($Workflow->config, $page_to_check, $RtbBidRequest);
	
		if ($score_codes == null):
			return true;
		endif;
	
		if (!empty($score_codes["suspect"]) && $score_codes["suspect"] == "true"):
			// overall action failed
			return false;
		endif;
	
		if (!empty($score_codes["riskScore"]) && intval($score_codes["riskScore"]) >= 65):
			// risk score action failed
			return false;
		endif;
	
		return true;
	}
	
	protected static function get_honeypot_score_from_service($config, $remote_ip) {
	
		$honeypot_safe = self::checkHoneyPotCached($config, $remote_ip);
	
		if ($honeypot_safe === null):
		
			$project_honeypot_api_key = $config['settings']['rtb']['project_honeypot_api_key'];
				
			$ProjectHoneyPot = new \util\ProjectHoneyPot($remote_ip, $project_honeypot_api_key);
			
			if ($ProjectHoneyPot->getError() !== null):
				/*
				 * something went wrong with the honeypot service
				* better luck next time
				*/
				return true;
			endif;
			
			$honeypot_listed = !$ProjectHoneyPot->isListed();
			
			/*
			 * IPs are re-assigned. Only the ones with activity in the last
			* month should trigger ad fraud.
			*/
			if ($honeypot_listed === false && $ProjectHoneyPot->getRecency() <= 30):
				// optionally do some logging here with $Logger
				$honeypot_safe = false;
			else:
				$honeypot_safe = true;
			endif;
					
			/*
			 * If we get valid response from the service,
			 * store it for 1 hour, so we are not constantly
			 * calling the service with the same parameters
			 */
			
			$params = array();
			$params["remote_ip"] = $remote_ip;
			$one_hour_in_seconds = 3600;
			\util\CacheSql::put_cached_read_result_apc($config, $params, self::$class_name, $honeypot_safe, $one_hour_in_seconds);
	
		endif;
	
		return $honeypot_safe;
	}
	
	private static function get_forensiq_score_from_service($config, $page_to_check, \model\openrtb\RtbBidRequest &$RtbBidRequest) {
	
		$forensiq_api_key 			= $config['settings']['rtb']['forensiq_api_key'];
		$remote_ip 					= isset($RtbBidRequest->RtbBidRequestDevice->ip) ? $RtbBidRequest->RtbBidRequestDevice->ip : "unknown";
		$domain 					= isset($RtbBidRequest->RtbBidRequestSite->domain) ? $RtbBidRequest->RtbBidRequestSite->domain : "unknown";
		$ua		 					= isset($RtbBidRequest->RtbBidRequestDevice->ua) ? $RtbBidRequest->RtbBidRequestDevice->ua : "unknown";
		$pid						= isset($RtbBidRequest->RtbBidRequestSite->RtbBidRequestPublisher->name) ? $RtbBidRequest->RtbBidRequestSite->RtbBidRequestPublisher->name : "unknown";
		$sid						= isset($RtbBidRequest->RtbBidRequestSite->RtbBidRequestPublisher->id) ? $RtbBidRequest->RtbBidRequestSite->RtbBidRequestPublisher->id : "unknown";
		$imptype					= empty($RtbBidRequestImp->RtbBidRequestVideo) == 'video' ? 'video' : 'display';
	
		$forensiq_url 				= self::$forensiq_url;
		$forensiq_url.= 			'?ck=' . $forensiq_api_key;
		$forensiq_url.= 			'&rt=' . $imptype;
		$forensiq_url.= 			'&output=JSON';
		$forensiq_url.= 			'&ip=' . $remote_ip;
		$forensiq_url.= 			'&url=' . rawurlencode($page_to_check);
		$forensiq_url.= 			'&ua=' . rawurlencode($ua);
		$forensiq_url.= 			'&seller=' . rawurlencode($domain);
		$forensiq_url.= 			'&cmp=' . rawurlencode($pid);
		$forensiq_url.= 			'&s=' . rawurlencode($sid);
	
		$raw_response 				= \util\WorkflowHelper::get_ping_notice_url_curl_request($forensiq_url);
		$forensiq_json_response 	= json_decode($raw_response, true);
	
		return $forensiq_json_response;
	}
	
	private static function checkHoneyPotCached($config, $remote_ip) {
	
		$params = array();
		$params["remote_ip"] = $remote_ip;
	
		$boolean_result = \util\CacheSql::get_cached_read_result_apc_type_convert($config, $params, self::$class_name);
	
		return $boolean_result;
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
	
}
