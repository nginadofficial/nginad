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
		
		if (empty($AdCampaignVideoRestrictions->ProtocolsCommaSeparated)):
			return true;
		endif;
		
		// Validate that the value is an array
		if (!is_array($RtbBidRequestVideo->protocols)):
			if ($Logger->setting_log === true):
			$Logger->log[] = "Failed: " . "Check video protocols code :: EXPECTED: "
					. 'Array(),'
					. " GOT: " . $RtbBidRequestVideo->protocols;
			endif;
			return false;
		endif;
		
		$protocols_code_list = explode(',', $AdCampaignVideoRestrictions->ProtocolsCommaSeparated);
		
		foreach($protocols_code_list as $protocols_code):
		
			foreach($RtbBidRequestVideo->protocols as $protocols_code_to_match):
			
				if ($protocols_code_to_match == $protocols_code):
					
					return true;
					
				endif;
				
			endforeach;
		
		endforeach;
		
		if ($Logger->setting_log === true):
			$Logger->log[] = "Failed: " . "Check video protocols code :: EXPECTED: "
				. $AdCampaignVideoRestrictions->ProtocolsCommaSeparated
				. " GOT: " . $RtbBidRequestVideo->protocols;
		endif;
		
		return false;
	}
}
