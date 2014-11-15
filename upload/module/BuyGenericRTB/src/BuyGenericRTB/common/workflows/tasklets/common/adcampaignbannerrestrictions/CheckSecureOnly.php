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

	public static function execute(&$Logger, &$Workflow, &$RtbBid, &$AdCampaignBanner, &$AdCampaignBannerRestrictions) {
	
		/*
		 * Check banner for https:// secure
		 */
		if ($AdCampaignBannerRestrictions->Secure !== null && $RtbBid->bid_request_secure !== null && $RtbBid->bid_request_secure != $AdCampaignBannerRestrictions->Secure):
			if ($Logger->setting_log === true):
				$Logger->log[] = "Failed: " . "Check banner for https:// secure :: EXPECTED: " . $AdCampaignBannerRestrictions->Secure . " GOT: " . $RtbBid->bid_request_secure;
			endif;
			return false;
		endif;		
		
		return true;
		
	}

}
