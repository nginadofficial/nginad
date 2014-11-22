<?php
/**
 * CDNPAL NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2015 CDNPAL Ltd. All Rights Reserved
 * @license GPLv3
 */

namespace buyrtb\workflows\tasklets\common\adcampaign;

class CheckMaxSpend {
	
	public static function execute(&$Logger, &$Workflow, \model\openrtb\RtbBidRequest &$RtbBidRequest, &$AdCampaign) {
	
        	if ($AdCampaign->CurrentSpend >= $AdCampaign->MaxSpend):
            	if ($Logger->setting_log === true):
            	   $Logger->log[] = "Failed: " . "Max Campaign Spend Exceeded";
            	endif;
        	   return false;
        	endif;
        	
        	return true;
	}
}

