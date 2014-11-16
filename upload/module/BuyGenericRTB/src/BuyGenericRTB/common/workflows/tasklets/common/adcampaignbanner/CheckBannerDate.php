<?php
/**
 * CDNPAL NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2015 CDNPAL Ltd. All Rights Reserved
 * @license GPLv3
 */

namespace buyrtb\workflows\tasklets\common\adcampaignbanner;

class CheckBannerDate {
	
	public static function execute(&$Logger, &$Workflow, \model\openrtb\RtbBidRequest &$RtbBidRequest, \model\openrtb\RtbBidRequestImp &$RtbBidRequestImp, &$AdCampaignBanner) {
	
		/*
		 * Check banner date
		 */
        $banner_startdate                 = strtotime($AdCampaignBanner->StartDate);
  		$banner_enddate                   = strtotime($AdCampaignBanner->EndDate);

    	if ($Workflow->current_time < $banner_startdate || $Workflow->current_time > $banner_enddate):
        	if ($Logger->setting_log === true):
           		$Logger->log[] = "Failed: " . "Check banner date :: EXPECTED: " . $AdCampaignBanner->StartDate . "->" . $AdCampaignBanner->EndDate . " GOT: " . date('m/d/Y', $Workflow->current_time);
         	endif;
         	return false;
  		endif;
			
		return true;
	}
	
}
