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
	
	public static function execute(&$Logger, &$Workflow, \model\openrtb\RtbBidRequest &$RtbBidRequest, \model\openrtb\RtbBidRequestImp &$RtbBidRequestImp, &$AdCampaignBanner, &$AdCampaignBannerRestrictions) {
	
		/*
		 * Check banner for PMP Enable
		 */
		if ($AdCampaignBannerRestrictions->PmpEnable !== null && $RtbBidRequestImp->pmp !== null && $RtbBidRequestImp->pmp != $AdCampaignBannerRestrictions->PmpEnable):
			if ($Logger->setting_log === true):
				$Logger->log[] = "Failed: " . "Check banner for PMP Enable :: EXPECTED: " . $AdCampaignBannerRestrictions->PmpEnable . " GOT: " . $RtbBidRequestImp->pmp;
			endif;
			return false;
		endif;
		
		return true;
		
	}
	
}
