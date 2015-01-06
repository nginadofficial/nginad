<?php
/**
 * CDNPAL NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2015 CDNPAL Ltd. All Rights Reserved
 * @license GPLv3
 */

namespace buyrtb\workflows\tasklets\display\adcampaignbannerrestrictions;

class CheckFoldPosition {
	
	public static function execute(&$Logger, &$Workflow, \model\openrtb\RtbBidRequest &$RtbBidRequest, \model\openrtb\RtbBidRequestImp &$RtbBidRequestImp, &$AdCampaignBanner, &$AdCampaignBannerRestrictions) {
		
		$RtbBidRequestBanner = $RtbBidRequestImp->RtbBidRequestBanner;
		
		$openrtb_fold_pos = 0;
		
		if($AdCampaignBannerRestrictions->FoldPos == 1):
			// above = 1 OpenRTB 2.2
			$openrtb_fold_pos = 1;
		elseif($AdCampaignBannerRestrictions->FoldPos == 2):
			// below = 3 OpenRTB 2.2
			$openrtb_fold_pos = 3;
		endif;
		
		/*
		 * Check banner system fold position (sFoldPos), I don't think we can trust the user fold position (uFoldPos)
		 * Partial above the fold not supported in OpenRTB
		*/
		if ($AdCampaignBannerRestrictions->FoldPos !== null && $RtbBidRequestBanner->pos !== null 
			&& $AdCampaignBannerRestrictions->FoldPos != 3
			&& $openrtb_fold_pos != $RtbBidRequestBanner->pos):
			if ($Logger->setting_log === true):
				$Logger->log[] = "Failed: " . "Check banner system fold position :: EXPECTED: " 
					. $openrtb_fold_pos
					. " GOT: " . $RtbBidRequestBanner->pos;
			endif;
			return false;
		endif;
		
		return true;
	}
}
