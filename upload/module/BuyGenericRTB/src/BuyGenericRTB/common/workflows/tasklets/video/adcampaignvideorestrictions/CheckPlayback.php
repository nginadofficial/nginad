<?php
/**
 * CDNPAL NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2015 CDNPAL Ltd. All Rights Reserved
 * @license GPLv3
 */

namespace buyrtb\workflows\tasklets\video\adcampaignvideorestrictions;

class CheckPlayback {
	
	public static function execute(&$Logger, &$Workflow, \model\openrtb\RtbBidRequest &$RtbBidRequest, \model\openrtb\RtbBidRequestImp &$RtbBidRequestImp, &$AdCampaignBanner, &$AdCampaignVideoRestrictions) {
		
		$RtbBidRequestVideo = $RtbBidRequestImp->RtbBidRequestVideo;
		
		if (empty($AdCampaignVideoRestrictions->PlaybackCommaSeparated)):
			return true;
		endif;
		
		// Validate that the value is an array
		if (!is_array($RtbBidRequestVideo->playbackmethod)):
			if ($Logger->setting_log === true):
			$Logger->log[] = "Failed: " . "Check video playback code :: EXPECTED: "
					. 'Array(),'
					. " GOT: " . $RtbBidRequestVideo->playbackmethod;
			endif;
			return false;
		endif;
		
		$playbackmethod_code_list = explode(',', $AdCampaignVideoRestrictions->PlaybackCommaSeparated);
		
		foreach($playbackmethod_code_list as $playbackmethod_code):
		
			foreach($RtbBidRequestVideo->playbackmethod as $playbackmethod_code_to_match):
			
				if ($playbackmethod_code_to_match == $playbackmethod_code):
					
					return true;
					
				endif;
				
			endforeach;
		
		endforeach;
		
		if ($Logger->setting_log === true):
			$Logger->log[] = "Failed: " . "Check video playback code :: EXPECTED: "
				. $AdCampaignVideoRestrictions->PlaybackCommaSeparated
				. " GOT: " . $RtbBidRequestVideo->playbackmethod;
		endif;
		
		return false;
	}
}
