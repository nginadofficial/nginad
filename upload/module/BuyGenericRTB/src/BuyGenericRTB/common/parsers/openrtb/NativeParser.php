<?php
/**
 * NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2016 NginAd Foundation. All Rights Reserved
 * @license GPLv3
 */

namespace buyrtb\parsers\openrtb;
use \Exception;

class NativeParser {

	public function parse_request(&$Logger, \buyrtb\parsers\openrtb\OpenRTBParser &$Parser, \model\openrtb\native\request\RtbBidRequestNative &$RtbBidRequestNative, &$ad_impression_native) {

		// get list of supported mime types for video content
		
		// Version
		
		\util\ParseHelper::parse_item(
				$RtbBidRequestNative,
				$ad_impression_native,
				"ver");

		// Layout
		/*
		 * 1 Content Wall
		 * 2 App Wall
		 * 3 News Feed
		 * 4 Chat List
		 * 5 Carousel
		 * 6 Content Stream
		 * 7 Grid adjoining the content
		 * 500+ Reserved for Exchange specific layouts.
		 */
		
		\util\ParseHelper::parse_item(
				$RtbBidRequestNative,
				$ad_impression_native,
				"layout");
		
		// Ad unit
		/*
		 * 1 Paid Search Units
		 * 2 Recommendation Widgets
		 * 3 Promoted Listings
		 * 4 In-Ad (IAB Standard) with Native Element Units
		 * 5 Custom / "Can't Be Contained"
		 * 500+ Reserved for Exchange specific layouts.
		 */		
		
		\util\ParseHelper::parse_item(
				$RtbBidRequestNative,
				$ad_impression_native,
				"adunit");
		
		// The number of identical placements in this Layout
		
		\util\ParseHelper::parse_item(
				$RtbBidRequestNative,
				$ad_impression_native,
				"plcmtcnt");
		
		/*
		 * see the IAB Core Six layout types
		 * http://www.iab.net/media/file/IAB-Native-Advertising-Playbook2.pdf
		 */ 
		
		\util\ParseHelper::parse_item(
				$RtbBidRequestNative,
				$ad_impression_native,
				"seq");
		

		// A required array of companion RtbBidRequestNativeAsset objects
		
		if (isset($ad_impression_native["assets"])
			&& is_array($ad_impression_native["assets"])
			&& count($ad_impression_native["assets"])):
			
			$last_exception = null;
			
			foreach ($ad_impression_native["assets"] as $asset):
				// this is a native asset
				$RtbBidRequestNativeAsset = new \model\openrtb\native\request\RtbBidRequestNativeAsset();
		
				try {
					\buyrtb\parsers\openrtb\parselets\common\native\ParseAsset::execute($Logger, $Parser, $RtbBidRequestNativeAsset, $asset);	
				} catch (Exception $e) {
					$last_exception = $e;
					continue;
				}
				
				$RtbBidRequestNative->RtbBidRequestAssetList[] = $RtbBidRequestNativeAsset;

			endforeach;
			
			if (!count($RtbBidRequestNative->RtbBidRequestAssetList)):
				throw new Exception("At least one assets object in the Native object is required: LAST MESSAGE: " . $last_exception->getMessage(), $last_exception->getCode(), $last_exception->getPrevious());
			endif;
			
		else: 
			throw new Exception("At least one assets object in the Native object is required");
		endif;
		
	}
}
