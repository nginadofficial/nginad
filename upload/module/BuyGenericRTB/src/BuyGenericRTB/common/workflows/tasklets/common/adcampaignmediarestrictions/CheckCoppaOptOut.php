<?php
/**
 * CDNPAL NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2015 CDNPAL Ltd. All Rights Reserved
 * @license GPLv3
 */

namespace buyrtb\workflows\tasklets\common\adcampaignmediarestrictions;

class CheckCoppaOptOut {
	
	public static function execute(&$Logger, &$Workflow, \model\openrtb\RtbBidRequest &$RtbBidRequest, \model\openrtb\RtbBidRequestImp &$RtbBidRequestImp, &$AdCampaignBanner, &$AdCampaignMediaRestrictions) {
	
		/*
		 * Check user for Coppa opt out status
		*/
		if ($AdCampaignMediaRestrictions->Optout !== null && $RtbBidRequest->RtbBidRequestRegulations !== null && $RtbBidRequest->RtbBidRequestRegulations->coppa != $AdCampaignMediaRestrictions->Optout):
			if ($Logger->setting_log === true):
				$Logger->log[] = "Failed: " . "Check user for Coppa opt out status :: EXPECTED: " 
					. $AdCampaignMediaRestrictions->Optout . " GOT: " 
					. $RtbBidRequest->RtbBidRequestRegulations->coppa;
			endif;
			return false;
		endif;
		
		return true;
		
	}

}