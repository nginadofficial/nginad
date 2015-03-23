<?php
/**
 * CDNPAL NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2015 CDNPAL Ltd. All Rights Reserved
 * @license GPLv3
 */

namespace buyrtb\workflows\tasklets\common\thirdparty;

class CheckAdFraud {
	
	private static $class_name = 'CheckAdFraud';
	
	public static function execute(&$Logger, &$Workflow, \model\openrtb\RtbBidRequest &$RtbBidRequest) {
    	/*
    	 * This is a placeholder for a User Scoring Tasklet
    	 * 
    	 * IE. DoubleVerify, Moat, ect...
    	*/

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
		
		if ($Workflow->config['settings']['rtb']['project_honeypot_protected'] == true):
			
			$is_honeypot_safe = self::get_honeypot_score_from_service($Workflow->config, $remote_ip);
		
			return $is_honeypot_safe;
			
		endif;

        return true;
	}
	
	private static function get_honeypot_score_from_service($config, $remote_ip) {
	
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
	
	private static function checkHoneyPotCached($config, $remote_ip) {
	
		$params = array();
		$params["remote_ip"] = $remote_ip;
	
		$boolean_result = \util\CacheSql::get_cached_read_result_apc_type_convert($config, $params, self::$class_name);
	
		return $boolean_result;
	}
	
}
