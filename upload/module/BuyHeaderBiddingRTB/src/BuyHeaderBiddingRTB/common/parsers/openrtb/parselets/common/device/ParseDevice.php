<?php
/**
 * NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2016 NginAd Foundation. All Rights Reserved
 * @license GPLv3
 */

namespace buyrtbheaderbidding\parsers\openrtb\parselets\common\device;
use \Exception;

class ParseDevice {
	
	public static function execute(&$Logger, \buyrtbheaderbidding\parsers\openrtb\OpenRTBParser &$Parser, \model\openrtb\RtbBidRequest &$RtbBidRequest, \model\openrtb\RtbBidRequestDevice &$RtbBidRequestDevice) {
	
		$RtbBidRequestDevice->devicetype = DEVICE_DESKTOP;

		/*
		 * NginAd requires the User's User Agent
		* for partner bidding purposes.
		*
		* Since this is header bidding the user's
		* browser is calling the auction, so we can use the person's
		* User Agent which is passed through the load balancer
		*/
		
		$RtbBidRequestDevice->ua = isset($_SERVER['HTTP_X_REAL_UA']) && !empty($_SERVER['HTTP_X_REAL_UA']) ? $_SERVER['HTTP_X_REAL_UA'] : $_SERVER["HTTP_USER_AGENT"];
		
		if (strpos($RtbBidRequestDevice->ua, '%20') !== false):
			$RtbBidRequestDevice->ua = urldecode($RtbBidRequestDevice->ua);
		endif;
		
		/*
		 * NginAd requires the User's IP Address
		 * for black listing purposes and for fill ratios
		 * against the MD5 checksum.
		 * 
		 * Since this is header bidding the user's
		 * browser is calling the auction, so we can use the person's
		 * IP Address which is passed through the load balancer
		 */ 
		
		$RtbBidRequestDevice->ip = isset($_SERVER['HTTP_X_REAL_IP']) && !empty($_SERVER['HTTP_X_REAL_IP']) ? $_SERVER['HTTP_X_REAL_IP'] : $_SERVER["REMOTE_ADDR"];
		
		// debug
		if (!$RtbBidRequestDevice->ip):
			$RtbBidRequestDevice->ip = '127.0.0.1';
		endif;
		
	}
	
}
