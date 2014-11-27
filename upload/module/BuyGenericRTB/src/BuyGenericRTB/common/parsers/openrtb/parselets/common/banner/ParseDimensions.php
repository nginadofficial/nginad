<?php
/**
 * CDNPAL NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2015 CDNPAL Ltd. All Rights Reserved
 * @license GPLv3
 */

namespace buyrtb\parsers\openrtb\parselets\common\banner;
use \Exception;

class ParseDimensions {
	
	public static function execute(&$Logger, \buyrtb\parsers\openrtb\OpenRTBParser &$Parser, \model\openrtb\RtbBidRequestBanner &$RtbBidRequestBanner, &$ad_impression_banner) {
			
		/*
		 * Get Width and Height
		 */
		
		$Parser->parse_with_exception(
				$RtbBidRequestBanner,
				$ad_impression_banner,
				$Parser->expeption_missing_min_bid_request_params . ": imp_banner_w",
				"w");
		
		$Parser->parse_with_exception(
				$RtbBidRequestBanner,
				$ad_impression_banner,
				$Parser->expeption_missing_min_bid_request_params . ": imp_banner_h",
				"h");

		// Width Max
		
		$Parser->parse_item(
				$RtbBidRequestBanner,
				$ad_impression_banner,
				"wmax");
		
		// Height Max
		
		$Parser->parse_item(
				$RtbBidRequestBanner,
				$ad_impression_banner,
				"hmax");
		
		// Width Min
		
		$Parser->parse_item(
				$RtbBidRequestBanner,
				$ad_impression_banner,
				"wmin");
		
		// Height Min
		
		$Parser->parse_item(
				$RtbBidRequestBanner,
				$ad_impression_banner,
				"hmin");
		
	}
}
