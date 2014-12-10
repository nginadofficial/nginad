<?php
/**
 * CDNPAL NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2015 CDNPAL Ltd. All Rights Reserved
 * @license GPLv3
 */

namespace buyrtb\workflows\tasklets\video\adcampaignbanner;

class VideoRestrictionsWorkflow {
	
	public static function execute(&$Logger, &$ParentWorkflow, \model\openrtb\RtbBidRequest &$RtbBidRequest, \model\openrtb\RtbBidRequestImp &$RtbBidRequestImp, &$AdCampaignBanner, &$AdCampaignVideoRestrictionsFactory) {
	
		/*
		 * Check banner restrictions (optional fields)
		*/
		
		$params = array();
		$params["AdCampaignBannerID"] = $AdCampaignBanner->AdCampaignBannerID;
		$AdCampaignVideoRestrictions = $AdCampaignVideoRestrictionsFactory->get_row_cached($ParentWorkflow->config, $params);
		
		// no banner restriction info to base rejection on
		if ($AdCampaignVideoRestrictions === null):
			return true;
		endif;
		
		// Check video height and width match
		if (\buyrtb\workflows\tasklets\video\adcampaignvideorestrictions\CheckVideoDimensions::execute($Logger, $ParentWorkflow, $RtbBidRequest, $RtbBidRequestImp, $AdCampaignBanner, $AdCampaignVideoRestrictions) === false):
			return false;
		endif;
		
		// Check video fold position
		if (\buyrtb\workflows\tasklets\video\adcampaignvideorestrictions\CheckFoldPosition::execute($Logger, $ParentWorkflow, $RtbBidRequest, $RtbBidRequestImp, $AdCampaignBanner, $AdCampaignVideoRestrictions) === false):
			return false;
		endif;
		
		// Check Duration
		if (\buyrtb\workflows\tasklets\video\adcampaignvideorestrictions\CheckDuration::execute($Logger, $ParentWorkflow, $RtbBidRequest, $RtbBidRequestImp, $AdCampaignBanner, $AdCampaignVideoRestrictions) === false):
			return false;
		endif;
		
		// Check Mime Types
		if (\buyrtb\workflows\tasklets\video\adcampaignvideorestrictions\CheckMimeTypes::execute($Logger, $ParentWorkflow, $RtbBidRequest, $RtbBidRequestImp, $AdCampaignBanner, $AdCampaignVideoRestrictions) === false):
			return false;
		endif;
		
		// Check Delivery Type
		if (\buyrtb\workflows\tasklets\video\adcampaignvideorestrictions\CheckDelivery::execute($Logger, $ParentWorkflow, $RtbBidRequest, $RtbBidRequestImp, $AdCampaignBanner, $AdCampaignVideoRestrictions) === false):
			return false;
		endif;
		
		// Check Playback Methods
		if (\buyrtb\workflows\tasklets\video\adcampaignvideorestrictions\CheckPlayback::execute($Logger, $ParentWorkflow, $RtbBidRequest, $RtbBidRequestImp, $AdCampaignBanner, $AdCampaignVideoRestrictions) === false):
			return false;
		endif;
		
		// Check Protocols
		if (\buyrtb\workflows\tasklets\video\adcampaignvideorestrictions\CheckProtocols::execute($Logger, $ParentWorkflow, $RtbBidRequest, $RtbBidRequestImp, $AdCampaignBanner, $AdCampaignVideoRestrictions) === false):
			return false;
		endif;
		
		// Check Start Delay
		if (\buyrtb\workflows\tasklets\video\adcampaignvideorestrictions\CheckStartDelay::execute($Logger, $ParentWorkflow, $RtbBidRequest, $RtbBidRequestImp, $AdCampaignBanner, $AdCampaignVideoRestrictions) === false):
			return false;
		endif;
		
		// Check Supported APIs
		if (\buyrtb\workflows\tasklets\video\adcampaignvideorestrictions\CheckSupportedApis::execute($Logger, $ParentWorkflow, $RtbBidRequest, $RtbBidRequestImp, $AdCampaignBanner, $AdCampaignVideoRestrictions) === false):
			return false;
		endif;
		
		// Check banner for PMP Enable
		if (\buyrtb\workflows\tasklets\common\adcampaignmediarestrictions\CheckPrivateMarketPlaceEnabled::execute($Logger, $ParentWorkflow, $RtbBidRequest, $RtbBidRequestImp, $AdCampaignBanner, $AdCampaignVideoRestrictions) === false):
			return false;
		endif;
		
		// Check banner for https:// secure
		if (\buyrtb\workflows\tasklets\common\adcampaignmediarestrictions\CheckSecureOnly::execute($Logger, $ParentWorkflow, $RtbBidRequest, $RtbBidRequestImp, $AdCampaignBanner, $AdCampaignVideoRestrictions) === false):
			return false;
		endif;
		
		// Check user for Coppa opt out status
		if (\buyrtb\workflows\tasklets\common\adcampaignmediarestrictions\CheckCoppaOptOut::execute($Logger, $ParentWorkflow, $RtbBidRequest, $RtbBidRequestImp, $AdCampaignBanner, $AdCampaignVideoRestrictions) === false):
			return false;
		endif;
		
		// Check banner for it being in the right vertical
		if (\buyrtb\workflows\tasklets\common\adcampaignmediarestrictions\CheckVertical::execute($Logger, $ParentWorkflow, $RtbBidRequest, $RtbBidRequestImp, $AdCampaignBanner, $AdCampaignVideoRestrictions) === false):
			return false;
		endif;
		
		// Check banner geography
		if (\buyrtb\workflows\tasklets\common\adcampaignmediarestrictions\CheckGeo::execute($Logger, $ParentWorkflow, $RtbBidRequest, $RtbBidRequestImp, $AdCampaignBanner, $AdCampaignVideoRestrictions) === false):
			return false;
		endif;
		
		return true;
	}
	
}
