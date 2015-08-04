<?php
/**
 * NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2016 NginAd Foundation. All Rights Reserved
 * @license GPLv3
 */

namespace buyrtb\workflows\tasklets\display\insertionorderlineitem;

class CheckDisplayBannerDimensions {
	
	public static function execute(&$Logger, &$Workflow, \model\openrtb\RtbBidRequest &$RtbBidRequest, \model\openrtb\RtbBidRequestImp &$RtbBidRequestImp, &$InsertionOrderLineItem) {
	
		$RtbBidRequestBanner = $RtbBidRequestImp->RtbBidRequestBanner;
		
 		/*
  		* Check banner height and width match
      	*/
     	if ($InsertionOrderLineItem->Height != $RtbBidRequestBanner->h || $InsertionOrderLineItem->Width != $RtbBidRequestBanner->w):
        	if ($Logger->setting_log === true):
            	$Logger->log[] = "Failed: " . "Check banner height match :: EXPECTED: " . $InsertionOrderLineItem->Height . " GOT: " . $RtbBidRequestBanner->h;
            	$Logger->log[] = "Failed: " . "Check banner width match :: EXPECTED: " . $InsertionOrderLineItem->Width . " GOT: " . $RtbBidRequestBanner->w;
         	endif;
      		return false;
   		endif;
			
		return true;
	}
	
}

