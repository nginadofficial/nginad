<?php
/**
 * CDNPAL NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2015 CDNPAL Ltd. All Rights Reserved
 * @license GPLv3
 */

namespace buyrtb\workflows\tasklets\video\adcampaignvideorestrictions;

class CheckVideoDimensions {
	
	public static function execute(&$Logger, &$Workflow, \model\openrtb\RtbBidRequest &$RtbBidRequest, \model\openrtb\RtbBidRequestImp &$RtbBidRequestImp, &$AdCampaignBanner, &$AdCampaignVideoRestrictions) {
		
		$RtbBidRequestVideo = $RtbBidRequestImp->RtbBidRequestVideo;
		
		$result1 = true;
		$result2 = true;
		
		if (is_numeric($AdCampaignVideoRestrictions->MinHeight) && $AdCampaignVideoRestrictions->MinHeight != 0):
			
			// Validate that the value is a number
			if (!is_numeric($RtbBidRequestVideo->h)):
				if ($Logger->setting_log === true):
					$Logger->log[] = "Failed: " . "Check video minimum height :: EXPECTED: "
							. 'Numeric Value,'
							. " GOT: " . $RtbBidRequestVideo->h;
				endif;
				$result1 = false;
			else:
			
				$result1 = $RtbBidRequestVideo->h >= $AdCampaignVideoRestrictions->MinHeight;
				
				if ($result1 === false && $Logger->setting_log === true):
					$Logger->log[] = "Failed: " . "Check video minimum height :: EXPECTED: "
							. $AdCampaignVideoRestrictions->MinHeight
							. " GOT: " . $RtbBidRequestVideo->h;
				endif;
			endif;
		endif;
		
		if (is_numeric($AdCampaignVideoRestrictions->MinWidth) && $AdCampaignVideoRestrictions->MinWidth != 0):
			
			// Validate that the value is a number
			if (!is_numeric($RtbBidRequestVideo->w)):
				if ($Logger->setting_log === true):
					$Logger->log[] = "Failed: " . "Check video minimum width :: EXPECTED: "
							. 'Numeric Value,'
							. " GOT: " . $RtbBidRequestVideo->w;
				endif;
				$result2 = false;
			else:
			
				$result2 = $RtbBidRequestVideo->w >= $AdCampaignVideoRestrictions->MinWidth;
				
				if ($result2 === false && $Logger->setting_log === true):
					$Logger->log[] = "Failed: " . "Check video minimum width :: EXPECTED: "
							. $AdCampaignVideoRestrictions->MinWidth
							. " GOT: " . $RtbBidRequestVideo->w;
				endif;
			endif;
			
		endif;

		return $result1 && $result2;
	}
	
}

