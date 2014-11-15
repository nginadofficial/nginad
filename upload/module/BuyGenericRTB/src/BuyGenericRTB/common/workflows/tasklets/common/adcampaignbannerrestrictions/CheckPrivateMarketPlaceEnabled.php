<?php
/**
 * CDNPAL NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2015 CDNPAL Ltd. All Rights Reserved
 * @license GPLv3
 */

namespace buyrtb\workflows\tasklets\common\adcampaignbannerrestrictions;

class CheckPrivateMarketPlaceEnabled {
	
	public static function execute(&$Logger, &$Workflow, &$RtbBid, &$AdCampaignBanner, &$AdCampaignBannerRestrictions) {
	
		/*
		 * Check banner for PMP Enable
		 */
		if ($AdCampaignBannerRestrictions->PmpEnable !== null && $RtbBid->bid_request_imp_pmp !== null && $RtbBid->bid_request_imp_pmp != $AdCampaignBannerRestrictions->PmpEnable):
			if ($Logger->setting_log === true):
				$Logger->log[] = "Failed: " . "Check banner for PMP Enable :: EXPECTED: " . $AdCampaignBannerRestrictions->PmpEnable . " GOT: " . $RtbBid->bid_request_imp_pmp;
			endif;
			return false;
		endif;
		
		return true;
		
	}
	
}
