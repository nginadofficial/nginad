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
	
	public static function execute(&$Logger, \buyrtb\parsers\openrtb\OpenRTBParser &$Parser, \model\rtb\RtbBidRequestBanner &$RtbBidRequestBanner, &$ad_impression_banner) {
			
		/*
		 * Get Height and Width
		 */
		if (isset($ad_impression_banner["h"])):
			$RtbBidRequestBanner->bid_request_imp_banner_h 	= $ad_impression_banner["h"];
		else:
			throw new Exception($Parser->expeption_missing_min_bid_request_params . ": imp_banner_h");
		endif;
		 
		if (isset($ad_impression_banner["w"])):
			$RtbBidRequestBanner->bid_request_imp_banner_w 	= $ad_impression_banner["w"];
		else:
			throw new Exception($Parser->expeption_missing_min_bid_request_params . ": imp_banner_w");
		endif;
		
	}
}
