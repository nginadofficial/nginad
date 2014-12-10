<?php
/**
 * CDNPAL NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2015 CDNPAL Ltd. All Rights Reserved
 * @license GPLv3
 */

namespace buyrtb\workflows\tasklets\video\adcampaignvideorestrictions;

class CheckDuration {
	
	public static function execute(&$Logger, &$Workflow, \model\openrtb\RtbBidRequest &$RtbBidRequest, \model\openrtb\RtbBidRequestImp &$RtbBidRequestImp, &$AdCampaignBanner, &$AdCampaignVideoRestrictions) {
		
		$RtbBidRequestVideo = $RtbBidRequestImp->RtbBidRequestVideo;
		
		$result1 = false;
		$result2 = false;
		
		if (is_numeric($AdCampaignVideoRestrictions->MinDuration) && $AdCampaignVideoRestrictions->MinDuration != 0):
			
			// Validate that the value is a number
			if (!is_numeric($RtbBidRequestVideo->minduration)):
				if ($Logger->setting_log === true):
					$Logger->log[] = "Failed: " . "Check video minimum duration :: EXPECTED: "
							. 'Numeric Value,'
							. " GOT: " . $RtbBidRequestVideo->minduration;
				endif;
				return false;
			endif;
			
			$result1 = $RtbBidRequestVideo->minduration >= $AdCampaignVideoRestrictions->MinDuration;
			
			if ($result1 === false && $Logger->setting_log === true):
				$Logger->log[] = "Failed: " . "Check video minimum duration :: EXPECTED: "
						. $AdCampaignVideoRestrictions->MinDuration
						. " GOT: " . $RtbBidRequestVideo->minduration;
			endif;
			
		endif;
		
		if (is_numeric($AdCampaignVideoRestrictions->MaxDuration) && $AdCampaignVideoRestrictions->MaxDuration != 0):
			
			// Validate that the value is a number
			if (!is_numeric($RtbBidRequestVideo->maxduration)):
				if ($Logger->setting_log === true):
					$Logger->log[] = "Failed: " . "Check video maximum duration :: EXPECTED: "
							. 'Numeric Value,'
							. " GOT: " . $RtbBidRequestVideo->maxduration;
				endif;
				return false;
			endif;
			
			$result2 = $RtbBidRequestVideo->maxduration >= $AdCampaignVideoRestrictions->MaxDuration;
			
			if ($result2 === false && $Logger->setting_log === true):
				$Logger->log[] = "Failed: " . "Check video maximum duration :: EXPECTED: "
						. $AdCampaignVideoRestrictions->MaxDuration
						. " GOT: " . $RtbBidRequestVideo->maxduration;
			endif;
			
		endif;
		
		return $result1 && $result2;
	}
}
