<?php
/**
 * CDNPAL NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2015 CDNPAL Ltd. All Rights Reserved
 * @license GPLv3
 */

namespace buyrtb\workflows\tasklets\common\adcampaign;

class CheckCampaignDate {
	
	public static function execute(&$Logger, &$Workflow, \model\openrtb\RtbBidRequest &$RtbBidRequest, &$AdCampaign) {
	
        	/*
        	 * Check campaign date
        	 */
        	$campaign_startdate                 = strtotime($AdCampaign->StartDate);
        	$campaign_enddate                   = strtotime($AdCampaign->EndDate);

        	if ($Workflow->current_time < $campaign_startdate || $Workflow->current_time > $campaign_enddate):
        	   if ($Logger->setting_log === true):
                    $Logger->log[] = "Failed: " . "Check campaign date :: EXPECTED: " . $AdCampaign->StartDate . "->" . $AdCampaign->EndDate . " GOT: " . date('m/d/Y', $Workflow->current_time);
        	   endif;
        	   return false;
        	endif;
        	
        	return true;
	}
	
}
