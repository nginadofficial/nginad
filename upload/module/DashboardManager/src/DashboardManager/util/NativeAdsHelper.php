<?php
/**
 * NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2016 NginAd Foundation. All Rights Reserved
 * @license GPLv3
 */

namespace util;

class NativeAdsHelper {
	
	public static function getNativeAdDataTypes() {
		
		return array(
			(string)DATA_ASSET_SPONSORED	=> "Sponsored",
			(string)DATA_ASSET_DESC			=> "Description",
			(string)DATA_ASSET_RATING		=> "Rating",
			(string)DATA_ASSET_LIKES		=> "Likes",
			(string)DATA_ASSET_DOWNLOADS	=> "Downloads",
			(string)DATA_ASSET_PRICE		=> "Price",
			(string)DATA_ASSET_SALEPRICE	=> "Sales Price",
			(string)DATA_ASSET_PHONE		=> "Phone",
			(string)DATA_ASSET_ADDRESS		=> "Address",
			(string)DATA_ASSET_DESC2		=> "Description 2",
			(string)DATA_ASSET_DISPLAY_URL	=> "Display URL",
			(string)DATA_ASSET_CTATEXT		=> "CTA Description"
		);
	}
	
}