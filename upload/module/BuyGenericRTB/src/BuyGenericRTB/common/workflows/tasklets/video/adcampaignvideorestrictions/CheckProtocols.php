<?php
/**
 * CDNPAL NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2015 CDNPAL Ltd. All Rights Reserved
 * @license GPLv3
 */

namespace buyrtb\workflows\tasklets\video\adcampaignvideorestrictions;

class CheckProtocols {
	
	public static function execute(&$Logger, &$Workflow, \model\openrtb\RtbBidRequest &$RtbBidRequest, \model\openrtb\RtbBidRequestImp &$RtbBidRequestImp, &$AdCampaignBanner, &$AdCampaignVideoRestrictions) {
		
		$RtbBidRequestVideo = $RtbBidRequestImp->RtbBidRequestVideo;
		
		/*
		 * Check video fold position
		*/
		if ($AdCampaignVideoRestrictions->FoldPos !== null && $RtbBidRequestVideo->pos !== null 
			&& $AdCampaignVideoRestrictions->FoldPos != $RtbBidRequestVideo->pos):
			if ($Logger->setting_log === true):
				$Logger->log[] = "Failed: " . "Check video fold position :: EXPECTED: " 
					. $AdCampaignVideoRestrictions->FoldPos
					. " GOT: " . $RtbBidRequestVideo->pos;
			endif;
			return false;
		endif;
		
		return true;
	}
}
