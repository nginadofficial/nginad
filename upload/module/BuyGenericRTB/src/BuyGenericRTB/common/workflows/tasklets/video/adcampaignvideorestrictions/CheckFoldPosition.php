<?php
/**
 * CDNPAL NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2015 CDNPAL Ltd. All Rights Reserved
 * @license GPLv3
 */

namespace buyrtb\workflows\tasklets\video\adcampaignvideorestrictions;

class CheckFoldPosition {
	
	public static function execute(&$Logger, &$Workflow, \model\openrtb\RtbBidRequest &$RtbBidRequest, \model\openrtb\RtbBidRequestImp &$RtbBidRequestImp, &$AdCampaignBanner, &$AdCampaignVideoRestrictions) {
		
		$RtbBidRequestVideo = $RtbBidRequestImp->RtbBidRequestVideo;
		
		if (!is_numeric($AdCampaignVideoRestrictions->FoldPos)):
			return true;
		endif;
		
		// Validate that the value is a number
		if (!is_numeric($RtbBidRequestVideo->pos)):
			if ($Logger->setting_log === true):
				$Logger->log[] = "Failed: " . "Check video fold position :: EXPECTED: "
						. 'Numeric Value,'
						. " GOT: " . $RtbBidRequestVideo->pos;
			endif;
			return false;
		endif;
		
		$result = $AdCampaignVideoRestrictions->FoldPos == $RtbBidRequestVideo->pos;
		
		if ($result === false && $Logger->setting_log === true):
			$Logger->log[] = "Failed: " . "Check video fold position :: EXPECTED: "
					. $AdCampaignVideoRestrictions->FoldPos
					. " GOT: " . $RtbBidRequestVideo->pos;
		endif;
		
		return $result;
	}
}
