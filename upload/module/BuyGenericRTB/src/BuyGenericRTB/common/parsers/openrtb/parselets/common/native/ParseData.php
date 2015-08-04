<?php
/**
 * NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2016 NginAd Foundation. All Rights Reserved
 * @license GPLv3
 */

namespace buyrtb\parsers\openrtb\parselets\common\native;

class ParseData {
	
	public static function execute(&$Logger, \buyrtb\parsers\openrtb\OpenRTBParser &$Parser, \model\openrtb\native\request\RtbBidRequestNativeData &$RtbBidRequestNativeData, &$rtb_native_data) {
	
		// Data Asset Type
		/*
		 * 1 Sponsored By message where response should contain the brand name of the sponsor.
		 * 2 Descriptive text associated with the product or service being advertised.
		 * 3 Rating of the product being offered to the user. For example an app's rating in an app store from 0-5.
		 * 4 Number of social ratings or "likes" of the product being offered to the user.
		 * 5 Number downloads/installs of this product
		 * 6 Price for product / app / in-app purchase. Value should include currency symbol in localised format.
		 * 7 Sale price that can be used together with price to indicate a discounted price compared to a regular price. Value should include currency symbol in localised format.
		 * 8 Phone number
		 * 9 Address
		 * 10 Additional descriptive text associated with the product or service being advertised
		 * 11 Display URL for the text ad
		 * 12 CTA description - descriptive text describing a 'call to action' button for the destination URL.
		 * 500+ Reserved for Exchange specific layouts.
		 */	
			
		\util\ParseHelper::parse_item(
				$RtbBidRequestNativeData,
				$rtb_native_data,
				"type");
		
		// Maximum length of the text in response
			
		\util\ParseHelper::parse_item(
				$RtbBidRequestNativeData,
				$rtb_native_data,
				"len");
		
	}
}
