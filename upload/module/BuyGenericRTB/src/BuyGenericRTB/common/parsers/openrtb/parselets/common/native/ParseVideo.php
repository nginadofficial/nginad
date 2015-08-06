<?php
/**
 * NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2016 NginAd Foundation. All Rights Reserved
 * @license GPLv3
 */

namespace buyrtb\parsers\openrtb\parselets\common\native;

class ParseVideo {
	
	public static function execute(&$Logger, \buyrtb\parsers\openrtb\OpenRTBParser &$Parser, \model\openrtb\native\request\RtbBidRequestNativeVideo &$RtbBidRequestNativeVideo, &$rtb_native_video) {
	
		// get list of supported mime types for video content

		\util\ParseHelper::parse_list_with_exception(
				$RtbBidRequestNativeVideo,
				$rtb_native_video,
				$Parser->expeption_missing_min_bid_request_params . ": video mimes",
				"mimes");

		\util\ParseHelper::parse_with_exception(
				$RtbBidRequestNativeVideo,
				$rtb_native_video,
				$Parser->expeption_missing_min_bid_request_params . ": video minduration",
				"minduration");
		
		\util\ParseHelper::parse_with_exception(
				$RtbBidRequestNativeVideo,
				$rtb_native_video,
				$Parser->expeption_missing_min_bid_request_params . ": video maxduration",
				"maxduration");
		
		// Protocol List
		
		\util\ParseHelper::parse_list_with_exception(
				$RtbBidRequestNativeVideo,
				$rtb_native_video,
				$Parser->expeption_missing_min_bid_request_params . ": video protocols",
				"protocols");
		
	}
}
