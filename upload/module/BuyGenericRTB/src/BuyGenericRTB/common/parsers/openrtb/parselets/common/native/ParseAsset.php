<?php
/**
 * NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2016 NginAd Foundation. All Rights Reserved
 * @license GPLv3
 */

namespace buyrtb\parsers\openrtb\parselets\common\native;

class ParseAsset {
	
	public static function execute(&$Logger, \buyrtb\parsers\openrtb\OpenRTBParser &$Parser, \model\openrtb\native\request\RtbBidRequestNativeAsset &$RtbBidRequestNativeAsset, &$rtb_native_asset) {
	
		
		// ID
			
		\util\ParseHelper::parse_item(
				$RtbBidRequestNativeAsset,
				$rtb_native_asset,
				"id");
		
		// Set to 1 if asset is required
			
		\util\ParseHelper::parse_item(
				$RtbBidRequestNativeAsset,
				$rtb_native_asset,
				"required");
		
		/*
		 * asset object may contain only one of title, img, data or video.
		 */
		
		$has_element = false;
		
		if (isset($rtb_native_asset["title"])):
		
			$title = $rtb_native_asset["title"];
		
			$RtbBidRequestNativeAsset->RtbBidRequestNativeTitle = new \model\openrtb\native\request\RtbBidRequestNativeTitle();
			
			\buyrtb\parsers\openrtb\parselets\common\native\ParseTitle::execute($Logger, $Parser, $RtbBidRequestNativeAsset->RtbBidRequestNativeTitle, $title);

			$has_element = true;
			
		endif;
		
		if (isset($rtb_native_asset["img"])):
			
			$image = $rtb_native_asset["img"];
			
			$RtbBidRequestNativeAsset->RtbBidRequestNativeImage = new \model\openrtb\native\request\RtbBidRequestNativeImage();
				
			\buyrtb\parsers\openrtb\parselets\common\native\ParseImage::execute($Logger, $Parser, $RtbBidRequestNativeAsset->RtbBidRequestNativeImage, $image);

			$has_element = true;
			
		endif;
		
		if (isset($rtb_native_asset["video"])):
				
			$video = $rtb_native_asset["video"];
				
			$RtbBidRequestNativeAsset->RtbBidRequestNativeVideo = new \model\openrtb\native\request\RtbBidRequestNativeVideo();
			
			\buyrtb\parsers\openrtb\parselets\common\native\ParseVideo::execute($Logger, $Parser, $RtbBidRequestNativeAsset->RtbBidRequestNativeVideo, $video);

			$has_element = true;
			
		endif;
		
		if (isset($rtb_native_asset["data"])):
			
			$data = $rtb_native_asset["data"];
			
			$RtbBidRequestNativeAsset->RtbBidRequestNativeData = new \model\openrtb\native\request\RtbBidRequestNativeData();
				
			\buyrtb\parsers\openrtb\parselets\common\native\ParseData::execute($Logger, $Parser, $RtbBidRequestNativeAsset->RtbBidRequestNativeData, $data);
			
			$has_element = true;
			
		endif;
		
		if ($has_element === false):
			throw new \Exception("Native Asset object must have at least one Title, Image, Video or Data object");
		endif;
	}
}
