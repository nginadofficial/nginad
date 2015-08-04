<?php
/**
 * NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2016 NginAd Foundation. All Rights Reserved
 * @license GPLv3
 */

namespace buyrtbfidelity\workflows\tasklets\common\insertionorder;

class CheckCampaignDate {
	
	public static function execute(&$Logger, &$Workflow, \model\openrtb\RtbBidRequest &$RtbBidRequest, &$InsertionOrder) {
	
        	/*
        	 * Check campaign date
        	 */
        	$campaign_startdate                 = strtotime($InsertionOrder->StartDate);
        	$campaign_enddate                   = strtotime($InsertionOrder->EndDate);

        	if ($Workflow->current_time < $campaign_startdate || $Workflow->current_time > $campaign_enddate):
        	   if ($Logger->setting_log === true):
                    $Logger->log[] = "Failed: " . "Check campaign date :: EXPECTED: " . $InsertionOrder->StartDate . "->" . $InsertionOrder->EndDate . " GOT: " . date('m/d/Y', $Workflow->current_time);
        	   endif;
        	   return false;
        	endif;
        	
        	return true;
	}
	
}
