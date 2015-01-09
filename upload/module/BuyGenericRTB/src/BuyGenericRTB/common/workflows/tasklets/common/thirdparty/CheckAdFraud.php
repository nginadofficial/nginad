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
			
			$project_honeypot_api_key = $Workflow->config['settings']['rtb']['project_honeypot_api_key'];
		
			$ProjectHoneyPot = new \util\ProjectHoneyPot($remote_ip, $project_honeypot_api_key);
			
			if ($ProjectHoneyPot->getError() !== null):
				/*
				 * something went wrong with the honeypot service
				 * better luck next time
				 */
				return true;
			endif;
			
			$passed_honeypot = !$ProjectHoneyPot->isListed();
			
			/*
			 * IPs are re-assigned. Only the ones with activity in the last 
			 * month should trigger ad fraud.
			 */
			if ($passed_honeypot === false && $ProjectHoneyPot->getRecency() <= 30):
				// optionally do some logging here with $Logger
				return false;
			endif;
			
		endif;

        return true;
	}
	
}
