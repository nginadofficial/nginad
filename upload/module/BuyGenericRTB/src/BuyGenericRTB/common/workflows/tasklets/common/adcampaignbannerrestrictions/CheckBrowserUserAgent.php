<?php
/**
 * CDNPAL NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2015 CDNPAL Ltd. All Rights Reserved
 * @license GPLv3
 */

namespace buyrtb\workflows\tasklets\common\adcampaignbannerrestrictions;

class CheckBrowserUserAgent {
	
	public static function execute(&$Logger, &$Workflow, &$RtbBid, &$AdCampaignBanner, &$AdCampaignBannerRestrictions) {
	
		/*
		 * Check browser user-agent for string
		 */
		if ($AdCampaignBannerRestrictions->BrowserUserAgentGrep !== null && $RtbBid->bid_request_device_ua !== null):
		
			if (strpos(strtolower($RtbBid->bid_request_device_ua), strtolower($AdCampaignBannerRestrictions->BrowserUserAgentGrep)) === false):
				if (\rtbbuyv22\RtbBuyV22Logger::get_instance()->setting_log === true):
					\rtbbuyv22\RtbBuyV22Logger::get_instance()->log[] = "Failed: " . "Check browser user-agent for string :: EXPECTED: " . $AdCampaignBannerRestrictions->BrowserUserAgentGrep . " GOT: " . $RtbBid->bid_request_device_ua;
				endif;
				return false;
			endif;
			
		endif;
		
		return true;
		
	}
	
}
