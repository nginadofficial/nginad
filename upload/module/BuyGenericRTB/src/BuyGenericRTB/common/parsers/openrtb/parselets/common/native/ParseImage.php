<?php
/**
 * NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2016 NginAd Foundation. All Rights Reserved
 * @license GPLv3
 */

namespace buyrtb\parsers\openrtb\parselets\common\native;

class ParseImage {
	
	public static function execute(&$Logger, \buyrtb\parsers\openrtb\OpenRTBParser &$Parser, \model\openrtb\native\request\RtbBidRequestNativeImage &$RtbBidRequestNativeImage, &$rtb_native_image) {
	
		
		// Image Type
		/*
		 * 1 Icon image
		 * 2 Logo image for the brand/app.
		 * 3 Large image preview for the ad
		 * 500+ Reserved for Exchange specific layouts.
		 */	
			
		\util\ParseHelper::parse_item(
				$RtbBidRequestNativeImage,
				$rtb_native_image,
				"type");
		
		// Width of the image in pixels.
			
		\util\ParseHelper::parse_item(
				$RtbBidRequestNativeImage,
				$rtb_native_image,
				"w");
	
		// The minimum requested width of the image in pixels.
			
		\util\ParseHelper::parse_item(
				$RtbBidRequestNativeImage,
				$rtb_native_image,
				"wmin");
		
		// Height of the image in pixels.
			
		\util\ParseHelper::parse_item(
				$RtbBidRequestNativeImage,
				$rtb_native_image,
				"h");
		
		// The minimum requested height of the image in pixels.
			
		\util\ParseHelper::parse_item(
				$RtbBidRequestNativeImage,
				$rtb_native_image,
				"hmin");

		// get list of supported mime types for images
		
		\util\ParseHelper::parse_item_list(
				$RtbBidRequestNativeImage,
				$rtb_native_image,
				"mimes");
		
	}
}
