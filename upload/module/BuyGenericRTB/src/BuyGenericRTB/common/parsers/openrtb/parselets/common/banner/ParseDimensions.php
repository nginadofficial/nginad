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
		 * Get Height and Width
		 */
		
		$Parser->parse_with_exception(
				$RtbBidRequestBanner,
				$ad_impression_banner,
				$Parser->expeption_missing_min_bid_request_params . ": imp_banner_h",
				"h");
		
		$Parser->parse_with_exception(
				$RtbBidRequestBanner,
				$ad_impression_banner,
				$Parser->expeption_missing_min_bid_request_params . ": imp_banner_w",
				"w");
		
	}
}
