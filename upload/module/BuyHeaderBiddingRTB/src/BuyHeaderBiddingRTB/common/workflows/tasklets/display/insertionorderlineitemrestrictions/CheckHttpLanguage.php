<?php
/**
 * NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2016 NginAd Foundation. All Rights Reserved
 * @license GPLv3
 */

namespace buyrtbheaderbidding\workflows\tasklets\display\insertionorderlineitemrestrictions;

class CheckHttpLanguage {
	
	public static function execute(&$Logger, &$Workflow, \model\openrtb\RtbBidRequest &$RtbBidRequest, \model\openrtb\RtbBidRequestImp &$RtbBidRequestImp, &$InsertionOrderLineItem, &$InsertionOrderLineItemRestrictions) {
	
		/*
		 * Check browser language
		*/
		if ($InsertionOrderLineItemRestrictions->HttpLanguage !== null && $RtbBidRequest->RtbBidRequestDevice->language !== null):
		
			$has_http_language = false;
			
			$request_language_list = explode(";", strtolower($RtbBidRequest->RtbBidRequestDevice->language));
			$http_language_list = explode(";", strtolower($InsertionOrderLineItemRestrictions->HttpLanguage));
			
			foreach ($http_language_list as $http_language):
				
				if (in_array(trim($http_language), $request_language_list)):
				
					$has_http_language = true;
					break;
				
				endif;
				
			endforeach;
			
			if ($has_http_language === false):
				if ($Logger->setting_log === true):
					$Logger->log[] = "Failed: " . "Check browser language :: EXPECTED: " . $InsertionOrderLineItemRestrictions->HttpLanguage . " GOT: " . $RtbBidRequest->RtbBidRequestDevice->language;
				endif;
				return false;
			endif;
			
		endif;
		
		return true;
		
	}
}
