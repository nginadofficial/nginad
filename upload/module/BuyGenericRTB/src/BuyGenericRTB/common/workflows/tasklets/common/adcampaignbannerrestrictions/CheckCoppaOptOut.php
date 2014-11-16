<?php
/**
 * CDNPAL NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2015 CDNPAL Ltd. All Rights Reserved
 * @license GPLv3
 */

namespace buyrtb\workflows\tasklets\common\adcampaignbannerrestrictions;

class CheckCoppaOptOut {
	
	public static function execute(&$Logger, &$Workflow, \model\openrtb\RtbBidRequest &$RtbBidRequest, \model\openrtb\RtbBidRequestImp &$RtbBidRequestImp, &$AdCampaignBanner, &$AdCampaignBannerRestrictions) {
	
		/*
		 * Check user for Coppa opt out status
		*/
		if ($AdCampaignBannerRestrictions->Optout !== null && $RtbBidRequest->RtbBidRequestRegs !== null && $RtbBidRequest->RtbBidRequestRegs->coppa != $AdCampaignBannerRestrictions->Optout):
			if ($Logger->setting_log === true):
				$Logger->log[] = "Failed: " . "Check user for Coppa opt out status :: EXPECTED: " 
					. $AdCampaignBannerRestrictions->Optout . " GOT: " 
					. $RtbBidRequest->RtbBidRequestRegs->coppa;
			endif;
			return false;
		endif;
		
		return true;
		
	}

}