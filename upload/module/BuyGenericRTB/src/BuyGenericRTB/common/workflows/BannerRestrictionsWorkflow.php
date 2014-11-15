<?php
/**
 * CDNPAL NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2015 CDNPAL Ltd. All Rights Reserved
 * @license GPLv3
 */

namespace buyrtb\workflows;

class CheckBannerRestrictionsWorkflow {
	
	public static function execute(&$Logger, &$ParentWorkflow, &$RtbBid, &$AdCampaignBanner, &$AdCampaignBannerRestrictionsFactory) {
	
		/*
		 * Check banner restrictions (optional fields)
		*/
		
		$params = array();
		$params["AdCampaignBannerID"] = $AdCampaignBanner->AdCampaignBannerID;
		$AdCampaignBannerRestrictions = $AdCampaignBannerRestrictionsFactory->get_row_cached($RtbBid->config, $params);
		
		// no banner restriction info to base rejection on
		if ($AdCampaignBannerRestrictions === null):
			return true;
		endif;
		
		// Check banner system fold position
		if (\buyrtb\workflows\tasklets\common\adcampaignbannerrestrictions\CheckFoldPosition::execute($Logger, $ParentWorkflow, $RtbBid, $AdCampaignBanner, $AdCampaignBannerRestrictions) === false):
			return false;
		endif;
		
		// Check browser language
		if (\buyrtb\workflows\tasklets\common\adcampaignbannerrestrictions\CheckHttpLanguage::execute($Logger, $ParentWorkflow, $RtbBid, $AdCampaignBanner, $AdCampaignBannerRestrictions) === false):
			return false;
		endif;
		
		// Check browser user-agent for string
		if (\buyrtb\workflows\tasklets\common\adcampaignbannerrestrictions\CheckBrowserUserAgent::execute($Logger, $ParentWorkflow, $RtbBid, $AdCampaignBanner, $AdCampaignBannerRestrictions) === false):
			return false;
		endif;
		
		// Check banner for PMP Enable
		if (\buyrtb\workflows\tasklets\common\adcampaignbannerrestrictions\CheckPrivateMarketPlaceEnabled::execute($Logger, $ParentWorkflow, $RtbBid, $AdCampaignBanner, $AdCampaignBannerRestrictions) === false):
			return false;
		endif;
		
		// Check banner for https:// secure
		if (\buyrtb\workflows\tasklets\common\adcampaignbannerrestrictions\CheckSecureOnly::execute($Logger, $ParentWorkflow, $RtbBid, $AdCampaignBanner, $AdCampaignBannerRestrictions) === false):
			return false;
		endif;
		
		// Check user for Coppa opt out status
		if (\buyrtb\workflows\tasklets\common\adcampaignbannerrestrictions\CheckCoppaOptOut::execute($Logger, $ParentWorkflow, $RtbBid, $AdCampaignBanner, $AdCampaignBannerRestrictions) === false):
			return false;
		endif;
		
		// Check banner for it being in the right vertical
		if (\buyrtb\workflows\tasklets\common\adcampaignbannerrestrictions\CheckVertical::execute($Logger, $ParentWorkflow, $RtbBid, $AdCampaignBanner, $AdCampaignBannerRestrictions) === false):
			return false;
		endif;
		
		// Check banner geography
		if (\buyrtb\workflows\tasklets\common\adcampaignbannerrestrictions\CheckGeo::execute($Logger, $ParentWorkflow, $RtbBid, $AdCampaignBanner, $AdCampaignBannerRestrictions) === false):
			return false;
		endif;
		
		return true;
	}
	
}
