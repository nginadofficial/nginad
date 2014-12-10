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
		
		/*
		 * Check banner system fold position (sFoldPos), I don't think we can trust the user fold position (uFoldPos)
		*/
		if ($AdCampaignBannerRestrictions->FoldPos !== null && $RtbBidRequestBanner->pos !== null 
			&& $AdCampaignBannerRestrictions->FoldPos != $RtbBidRequestBanner->pos):
			if ($Logger->setting_log === true):
				$Logger->log[] = "Failed: " . "Check banner system fold position :: EXPECTED: " 
					. $AdCampaignBannerRestrictions->FoldPos
					. " GOT: " . $RtbBidRequestBanner->pos;
			endif;
			return false;
		endif;
		
		return true;
	}
}
