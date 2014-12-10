<?php
/**
 * CDNPAL NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2015 CDNPAL Ltd. All Rights Reserved
 * @license GPLv3
 */

namespace buyrtb\workflows\tasklets\display\adcampaignbannerrestrictions;

class CheckHttpLanguage {
	
	public static function execute(&$Logger, &$Workflow, \model\openrtb\RtbBidRequest &$RtbBidRequest, \model\openrtb\RtbBidRequestImp &$RtbBidRequestImp, &$AdCampaignBanner, &$AdCampaignBannerRestrictions) {
	
		/*
		 * Check browser language
		*/
		if ($AdCampaignBannerRestrictions->HttpLanguage !== null && $RtbBidRequest->RtbBidRequestDevice->language !== null):
		
			$has_http_language = false;
			
			$request_language_list = explode(";", strtolower($RtbBidRequest->RtbBidRequestDevice->language));
			$http_language_list = explode(";", strtolower($AdCampaignBannerRestrictions->HttpLanguage));
			
			foreach ($http_language_list as $http_language):
				
				if (in_array(trim($http_language), $request_language_list)):
				
					$has_http_language = true;
					break;
				
				endif;
				
			endforeach;
			
			if ($has_http_language === false):
				if ($Logger->setting_log === true):
					$Logger->log[] = "Failed: " . "Check browser language :: EXPECTED: " . $AdCampaignBannerRestrictions->HttpLanguage . " GOT: " . $RtbBidRequest->RtbBidRequestDevice->language;
				endif;
				return false;
			endif;
			
		endif;
		
		return true;
		
	}
}
