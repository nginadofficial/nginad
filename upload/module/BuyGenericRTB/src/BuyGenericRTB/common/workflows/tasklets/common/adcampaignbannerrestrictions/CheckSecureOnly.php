<?php
/**
 * CDNPAL NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2015 CDNPAL Ltd. All Rights Reserved
 * @license GPLv3
 */

namespace buyrtb\workflows\tasklets\common\adcampaignbannerrestrictions;

class CheckSecureOnly {

	public static function execute(&$Logger, &$Workflow, \model\openrtb\RtbBidRequest &$RtbBidRequest, \model\openrtb\RtbBidRequestImp &$RtbBidRequestImp, &$AdCampaignBanner, &$AdCampaignBannerRestrictions) {
	
		/*
		 * Check banner for https:// secure
		 */
		if ($AdCampaignBannerRestrictions->Secure !== null && $RtbBidRequest->secure !== null && $RtbBidRequest->secure != $AdCampaignBannerRestrictions->Secure):
			if ($Logger->setting_log === true):
				$Logger->log[] = "Failed: " . "Check banner for https:// secure :: EXPECTED: " . $AdCampaignBannerRestrictions->Secure . " GOT: " . $RtbBidRequest->secure;
			endif;
			return false;
		endif;		
		
		return true;
		
	}

}
