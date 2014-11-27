<?php
/**
 * CDNPAL NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2015 CDNPAL Ltd. All Rights Reserved
 * @license GPLv3
 */

namespace buyrtb\parsers\openrtb;
use \Exception;

class VideoParser {

	public function parse_request(&$Logger, \buyrtb\parsers\openrtb\OpenRTBParser &$Parser, \model\openrtb\RtbBidRequestVideo &$RtbBidRequestVideo, &$ad_impression_video) {

		// get list of supported mime types for video content
		
		$Parser->parse_list_with_exception(
				$RtbBidRequestVideo,
				$ad_impression_video,
				$Parser->expeption_missing_min_bid_request_params . ": video mimes",
				"mimes");

		$Parser->parse_with_exception(
				$RtbBidRequestVideo,
				$ad_impression_video,
				$Parser->expeption_missing_min_bid_request_params . ": video minduration",
				"minduration");
		
		$Parser->parse_with_exception(
				$RtbBidRequestVideo,
				$ad_impression_video,
				$Parser->expeption_missing_min_bid_request_params . ": video maxduration",
				"maxduration");
		
		// Protocol
		
		$Parser->parse_item(
				$RtbBidRequestVideo,
				$ad_impression_video,
				"protocol");
		
		// Protocol List
		
		$Parser->parse_item_list(
				$RtbBidRequestVideo,
				$ad_impression_video,
				"protocols");
		
		// Video Player Width
		
		$Parser->parse_item(
				$RtbBidRequestVideo,
				$ad_impression_video,
				"w");
		
		// Video Player Height
		
		$Parser->parse_item(
				$RtbBidRequestVideo,
				$ad_impression_video,
				"h");
		
		// Video Start Delay
		
		$Parser->parse_item(
				$RtbBidRequestVideo,
				$ad_impression_video,
				"startdelay");
		
		// Video Linearity
		
		$Parser->parse_item(
				$RtbBidRequestVideo,
				$ad_impression_video,
				"linearity");
		
		// Video Sequence Index
		
		$Parser->parse_item(
				$RtbBidRequestVideo,
				$ad_impression_video,
				"sequence");
		
		// Blocked Creative Attributes
		
		$Parser->parse_item_list(
				$RtbBidRequestVideo,
				$ad_impression_video,
				"battr");
		
		// Maximum Video extension beyond the max duration
		
		$Parser->parse_item(
				$RtbBidRequestVideo,
				$ad_impression_video,
				"maxextended");
		
		// Minimum Video Bitrate
		
		$Parser->parse_item(
				$RtbBidRequestVideo,
				$ad_impression_video,
				"minbitrate");
		
		// Maximum Video Bitrate
		
		$Parser->parse_item(
				$RtbBidRequestVideo,
				$ad_impression_video,
				"maxbitrate");
		
		// Flag which allows letterboxing
		
		$Parser->parse_item(
				$RtbBidRequestVideo,
				$ad_impression_video,
				"boxingallowed");
		
		// Allowed Playback Methods
		
		$Parser->parse_item_list(
				$RtbBidRequestVideo,
				$ad_impression_video,
				"playbackmethod");
		
		// Allowed Delivery Methods
		
		$Parser->parse_item_list(
				$RtbBidRequestVideo,
				$ad_impression_video,
				"delivery");
		
		// Video Position in Page
		
		$Parser->parse_item(
				$RtbBidRequestVideo,
				$ad_impression_video,
				"pos");
		
		// If present, an array of companion RtbBidRequestBanner objects
		
		if (isset($ad_impression_video["companionad"]) 
				&& is_array($ad_impression_video["companionad"]) 
				&& count($ad_impression_video["companionad"])):
				
			$DisplayParser = new \buyrtb\parsers\openrtb\DisplayParser();
				
			foreach ($ad_impression_video["companionad"] as $companionad):
				// this is a banner
				$RtbBidRequestBanner = new \model\openrtb\RtbBidRequestBanner();
				$DisplayParser->parse_request($Logger, $this, $RtbBidRequestBanner, $companionad);
				
				$RtbBidRequestVideo->RtbBidRequestBannerList[] = $RtbBidRequestBanner;
			endforeach;
		endif;
		
		// A List of Supported API Frameworks
		
		$Parser->parse_item_list(
				$RtbBidRequestVideo,
				$ad_impression_video,
				"api");
		
		// A List of Supported Companion Types
		
		$Parser->parse_item_list(
				$RtbBidRequestVideo,
				$ad_impression_video,
				"companiontype");
		
	}
}
