<?php
/**
 * CDNPAL NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2015 CDNPAL Ltd. All Rights Reserved
 * @license GPLv3
 */

namespace buyrtb\workflows\tasklets\display\adcampaignbanner;

class CheckDisplayBannerDimensions {
	
	public static function execute(&$Logger, &$Workflow, \model\openrtb\RtbBidRequest &$RtbBidRequest, \model\openrtb\RtbBidRequestImp &$RtbBidRequestImp, &$AdCampaignBanner) {
	
 		/*
  		* Check banner height and width match
      	*/
     	if ($AdCampaignBanner->Height != $RtbBid->bid_request_imp_banner_h || $AdCampaignBanner->Width != $RtbBid->bid_request_imp_banner_w):
        	if ($Logger->setting_log === true):
            	$Logger->log[] = "Failed: " . "Check banner height match :: EXPECTED: " . $AdCampaignBanner->Height . " GOT: " . $RtbBid->bid_request_imp_banner_h;
            	$Logger->log[] = "Failed: " . "Check banner width match :: EXPECTED: " . $AdCampaignBanner->Width . " GOT: " . $RtbBid->bid_request_imp_banner_w;
         	endif;
      		return false;
   		endif;
			
		return true;
	}
	
}

