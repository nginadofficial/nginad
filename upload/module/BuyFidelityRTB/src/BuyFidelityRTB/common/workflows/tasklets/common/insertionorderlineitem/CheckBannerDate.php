<?php
/**
 * NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2016 NginAd Foundation. All Rights Reserved
 * @license GPLv3
 */

namespace buyrtbfidelity\workflows\tasklets\common\insertionorderlineitem;

class CheckBannerDate {
	
	public static function execute(&$Logger, &$Workflow, \model\openrtb\RtbBidRequest &$RtbBidRequest, \model\openrtb\RtbBidRequestImp &$RtbBidRequestImp, &$InsertionOrderLineItem) {
	
		/*
		 * Check banner date
		 */
        $banner_startdate                 = strtotime($InsertionOrderLineItem->StartDate);
  		$banner_enddate                   = strtotime($InsertionOrderLineItem->EndDate);

    	if ($Workflow->current_time < $banner_startdate || $Workflow->current_time > $banner_enddate):
        	if ($Logger->setting_log === true):
           		$Logger->log[] = "Failed: " . "Check banner date :: EXPECTED: " . $InsertionOrderLineItem->StartDate . "->" . $InsertionOrderLineItem->EndDate . " GOT: " . date('m/d/Y', $Workflow->current_time);
         	endif;
         	return false;
  		endif;
			
		return true;
	}
	
}
