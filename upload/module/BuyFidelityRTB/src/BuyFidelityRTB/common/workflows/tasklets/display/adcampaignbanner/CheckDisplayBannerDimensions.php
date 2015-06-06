<?php
/**
 * CDNPAL NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2015 CDNPAL Ltd. All Rights Reserved
 * @license GPLv3
 */

namespace buyrtbfidelity\workflows\tasklets\display\adcampaignbanner;

class CheckDisplayBannerDimensions {
	
	public static function execute(&$Logger, &$Workflow, \model\openrtb\RtbBidRequest &$RtbBidRequest, \model\openrtb\RtbBidRequestImp &$RtbBidRequestImp, &$AdCampaignBanner) {
	
		$RtbBidRequestBanner = $RtbBidRequestImp->RtbBidRequestBanner;
		
		if (!empty($RtbBidRequestImp->RtbBidRequestImpExtensions->strictbannersize)):
			$strictbannersize = intval($RtbBidRequestImp->RtbBidRequestImpExtensions->strictbannersize);
		else:
			$strictbannersize = 1;
		endif;
		
		if (!$strictbannersize):
			/*
			 * Check banner height and width are smaller than the request dimensions
			 * Make sure it's at least 75% of the original ad zone size
			 * 
			*/
			if ($RtbBidRequestBanner->h < $AdCampaignBanner->Height  
				|| $RtbBidRequestBanner->w < $AdCampaignBanner->Width
				|| ($RtbBidRequestBanner->h * .75) > $AdCampaignBanner->Height
				|| ($RtbBidRequestBanner->w * .75) > $AdCampaignBanner->Width
			):
			
				if ($Logger->setting_log === true):
					$Logger->log[] = "Failed: " . "Check banner height match NOT STRICT :: EXPECTED: " . $AdCampaignBanner->Height . " GOT: " . $RtbBidRequestBanner->h;
					$Logger->log[] = "Failed: " . "Check banner width match NOT STRICT :: EXPECTED: " . $AdCampaignBanner->Width . " GOT: " . $RtbBidRequestBanner->w;
				endif;
				return false;
			endif;
		
		elseif ($AdCampaignBanner->Height != $RtbBidRequestBanner->h || $AdCampaignBanner->Width != $RtbBidRequestBanner->w):
			/*
			 * Check banner height and width match
			*/
        	if ($Logger->setting_log === true):
            	$Logger->log[] = "Failed: " . "Check banner height match STRICT :: EXPECTED: " . $AdCampaignBanner->Height . " GOT: " . $RtbBidRequestBanner->h;
            	$Logger->log[] = "Failed: " . "Check banner width match STRICT :: EXPECTED: " . $AdCampaignBanner->Width . " GOT: " . $RtbBidRequestBanner->w;
         	endif;
      		return false;
   		endif;
			
		return true;
	}
	
}

