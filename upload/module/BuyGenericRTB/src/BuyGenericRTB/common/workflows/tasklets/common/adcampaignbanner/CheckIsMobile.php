<?php
/**
 * CDNPAL NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2015 CDNPAL Ltd. All Rights Reserved
 * @license GPLv3
 */

namespace buyrtb\workflows\tasklets\common\adcampaignbanner;

class CheckIsMobile {
	
	public static function execute(&$Logger, &$Workflow, &$RtbBid, &$AdCampaignBanner) {
	
		/*
		 * Check is mobile web, phone, tablet, native iOS or native Android
		 */
     	if ($RtbBid->bid_request_mobile != $AdCampaignBanner->IsMobile):
       		if ($Logger->setting_log === true):
        		$Logger->log[] = "Failed: " . "Check is mobile web :: EXPECTED: " . $AdCampaignBanner->IsMobile . " GOT: " . $RtbBid->bid_request_mobile;
      		endif;
     		continue;
		endif;
			
		return true;
	}
	

	
}

