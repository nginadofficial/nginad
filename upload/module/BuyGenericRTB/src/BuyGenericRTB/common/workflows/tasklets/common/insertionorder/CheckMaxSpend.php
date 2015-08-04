<?php
/**
 * NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2016 NginAd Foundation. All Rights Reserved
 * @license GPLv3
 */

namespace buyrtb\workflows\tasklets\common\insertionorder;

class CheckMaxSpend {
	
	public static function execute(&$Logger, &$Workflow, \model\openrtb\RtbBidRequest &$RtbBidRequest, &$InsertionOrder) {
	
        	if ($InsertionOrder->CurrentSpend >= $InsertionOrder->MaxSpend):
            	if ($Logger->setting_log === true):
            	   $Logger->log[] = "Failed: " . "Max Campaign Spend Exceeded";
            	endif;
        	   return false;
        	endif;
        	
        	return true;
	}
}

