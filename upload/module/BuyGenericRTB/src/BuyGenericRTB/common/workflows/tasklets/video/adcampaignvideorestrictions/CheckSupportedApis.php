<?php
/**
 * CDNPAL NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2015 CDNPAL Ltd. All Rights Reserved
 * @license GPLv3
 */

namespace buyrtb\workflows\tasklets\video\adcampaignvideorestrictions;

class CheckSupportedApis {
	
	public static function execute(&$Logger, &$Workflow, \model\openrtb\RtbBidRequest &$RtbBidRequest, \model\openrtb\RtbBidRequestImp &$RtbBidRequestImp, &$AdCampaignBanner, &$AdCampaignVideoRestrictions) {
		
		$RtbBidRequestVideo = $RtbBidRequestImp->RtbBidRequestVideo;
		
		if (empty($AdCampaignVideoRestrictions->ApisSupportedCommaSeparated)):
			return true;
		endif;
		
		// Validate that the value is an array
		if (!is_array($RtbBidRequestVideo->api)):
			if ($Logger->setting_log === true):
			$Logger->log[] = "Failed: " . "Check video APIs code :: EXPECTED: "
					. 'Array(),'
					. " GOT: " . $RtbBidRequestVideo->api;
			endif;
			return false;
		endif;
		
		$api_code_list = explode(',', $AdCampaignVideoRestrictions->ApisSupportedCommaSeparated);
		
		foreach($api_code_list as $api_code):
		
			foreach($RtbBidRequestVideo->api as $api_code_to_match):
			
				if ($api_code_to_match == $api_code):
					
					return true;
					
				endif;
				
			endforeach;
		
		endforeach;
		
		if ($Logger->setting_log === true):
			$Logger->log[] = "Failed: " . "Check video APIs code :: EXPECTED: "
				. $AdCampaignVideoRestrictions->ApisSupportedCommaSeparated
				. " GOT: " . $RtbBidRequestVideo->api;
		endif;
		
		return false;
	}
}
