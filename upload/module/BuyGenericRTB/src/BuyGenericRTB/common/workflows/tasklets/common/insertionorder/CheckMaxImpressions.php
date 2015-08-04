<?php
/**
 * NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2016 NginAd Foundation. All Rights Reserved
 * @license GPLv3
 */

namespace buyrtb\workflows\tasklets\common\insertionorder;

class CheckMaxImpressions {
	
	public static function execute(&$Logger, &$Workflow, \model\openrtb\RtbBidRequest &$RtbBidRequest, &$InsertionOrder) {
	
        /*
       	 * Check max impressions
         */
    	if ($InsertionOrder->ImpressionsCounter >= $InsertionOrder->MaxImpressions):
      		if ($Logger->setting_log === true):
           		$Logger->log[] = "Failed: " . "Max Campaign Impressions Exceeded";
       		endif;
      		return false;
    	endif;
		 
		return true;
	}
	
}

