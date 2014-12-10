<?php
/**
 * CDNPAL NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2015 CDNPAL Ltd. All Rights Reserved
 * @license GPLv3
 */

namespace buyrtb\workflows\tasklets\video\adcampaignbanner;

class CheckVideoDimensions {
	
	public static function execute(&$Logger, &$Workflow, \model\openrtb\RtbBidRequest &$RtbBidRequest, \model\openrtb\RtbBidRequestImp &$RtbBidRequestImp, &$AdCampaignBanner) {
	
		$RtbBidRequestVideo = $RtbBidRequestImp->RtbBidRequestVideo;
		
 		/*
  		* Check banner height and width match
      	*/
     	if ($AdCampaignBanner->Height != $RtbBidRequestVideo->h || $AdCampaignBanner->Width != $RtbBidRequestVideo->w):
        	if ($Logger->setting_log === true):
            	$Logger->log[] = "Failed: " . "Check banner height match :: EXPECTED: " . $AdCampaignBanner->Height . " GOT: " . $RtbBidRequestVideo->h;
            	$Logger->log[] = "Failed: " . "Check banner width match :: EXPECTED: " . $AdCampaignBanner->Width . " GOT: " . $RtbBidRequestVideo->w;
         	endif;
      		return false;
   		endif;
			
		return true;
	}
	
}

