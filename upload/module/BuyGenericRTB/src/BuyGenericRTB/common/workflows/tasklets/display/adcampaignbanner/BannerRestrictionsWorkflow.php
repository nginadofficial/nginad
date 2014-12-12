<?php
/**
 * CDNPAL NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2015 CDNPAL Ltd. All Rights Reserved
 * @license GPLv3
 */

namespace buyrtb\workflows\tasklets\display\adcampaignbanner;

class BannerRestrictionsWorkflow {
	
	public static function execute(&$Logger, &$ParentWorkflow, \model\openrtb\RtbBidRequest &$RtbBidRequest, \model\openrtb\RtbBidRequestImp &$RtbBidRequestImp, &$AdCampaignBanner, &$AdCampaignBannerRestrictionsFactory) {
	
		/*
		 * Check banner restrictions (optional fields)
		*/
		
		$params = array();
		$params["AdCampaignBannerID"] = $AdCampaignBanner->AdCampaignBannerID;
		$AdCampaignBannerRestrictions = $AdCampaignBannerRestrictionsFactory->get_row_cached($ParentWorkflow->config, $params);
		
		// no banner restriction info to base rejection on
		if ($AdCampaignBannerRestrictions === null):
			return true;
		endif;
		
		// Check if the banner is in an IFRAME
		if (\buyrtb\workflows\tasklets\display\adcampaignbannerrestrictions\CheckInIFrame::execute($Logger, $ParentWorkflow, $RtbBidRequest, $RtbBidRequestImp, $AdCampaignBanner, $AdCampaignBannerRestrictions) === false):
			return false;
		endif;
		
		// Check banner system fold position
		if (\buyrtb\workflows\tasklets\display\adcampaignbannerrestrictions\CheckFoldPosition::execute($Logger, $ParentWorkflow, $RtbBidRequest, $RtbBidRequestImp, $AdCampaignBanner, $AdCampaignBannerRestrictions) === false):
			return false;
		endif;
		
		// Check browser language
		if (\buyrtb\workflows\tasklets\display\adcampaignbannerrestrictions\CheckHttpLanguage::execute($Logger, $ParentWorkflow, $RtbBidRequest, $RtbBidRequestImp, $AdCampaignBanner, $AdCampaignBannerRestrictions) === false):
			return false;
		endif;
		
		// Check browser user-agent for string
		if (\buyrtb\workflows\tasklets\display\adcampaignbannerrestrictions\CheckBrowserUserAgent::execute($Logger, $ParentWorkflow, $RtbBidRequest, $RtbBidRequestImp, $AdCampaignBanner, $AdCampaignBannerRestrictions) === false):
			return false;
		endif;
		
		// Check banner for PMP Enable
		if (\buyrtb\workflows\tasklets\common\adcampaignmediarestrictions\CheckPrivateMarketPlaceEnabled::execute($Logger, $ParentWorkflow, $RtbBidRequest, $RtbBidRequestImp, $AdCampaignBanner, $AdCampaignBannerRestrictions) === false):
			return false;
		endif;
		
		// Check banner for https:// secure
		if (\buyrtb\workflows\tasklets\common\adcampaignmediarestrictions\CheckSecureOnly::execute($Logger, $ParentWorkflow, $RtbBidRequest, $RtbBidRequestImp, $AdCampaignBanner, $AdCampaignBannerRestrictions) === false):
			return false;
		endif;
		
		// Check user for Coppa opt out status
		if (\buyrtb\workflows\tasklets\common\adcampaignmediarestrictions\CheckCoppaOptOut::execute($Logger, $ParentWorkflow, $RtbBidRequest, $RtbBidRequestImp, $AdCampaignBanner, $AdCampaignBannerRestrictions) === false):
			return false;
		endif;
		
		// Check banner for it being in the right vertical
		if (\buyrtb\workflows\tasklets\common\adcampaignmediarestrictions\CheckVertical::execute($Logger, $ParentWorkflow, $RtbBidRequest, $RtbBidRequestImp, $AdCampaignBanner, $AdCampaignBannerRestrictions) === false):
			return false;
		endif;
		
		// Check banner geography
		if (\buyrtb\workflows\tasklets\common\adcampaignmediarestrictions\CheckGeo::execute($Logger, $ParentWorkflow, $RtbBidRequest, $RtbBidRequestImp, $AdCampaignBanner, $AdCampaignBannerRestrictions) === false):
			return false;
		endif;
		
		return true;
	}
	
}
