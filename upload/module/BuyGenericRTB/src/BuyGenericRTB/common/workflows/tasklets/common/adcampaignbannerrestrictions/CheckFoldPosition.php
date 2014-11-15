<?php
/**
 * CDNPAL NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2015 CDNPAL Ltd. All Rights Reserved
 * @license GPLv3
 */

namespace buyrtb\workflows\tasklets\common\adcampaignbannerrestrictions;

class CheckFoldPosition {
	
	public static function execute(&$Logger, &$Workflow, &$RtbBid, &$AdCampaignBanner, &$AdCampaignBannerRestrictions) {
		
		/*
		 * Check banner system fold position (sFoldPos), I don't think we can trust the user fold position (uFoldPos)
		*/
		if ($AdCampaignBannerRestrictions->FoldPos !== null && $RtbBid->bid_request_sFoldPos !== null && $AdCampaignBannerRestrictions->FoldPos != $RtbBid->bid_request_sFoldPos):
			if ($Logger->setting_log === true):
				$Logger->log[] = "Failed: " . "Check banner system fold position :: EXPECTED: " . $AdCampaignBannerRestrictions->FoldPos . " GOT: " . $RtbBid->bid_request_sFoldPos;
			endif;
			return false;
		endif;
		
		return true;
	}
}
