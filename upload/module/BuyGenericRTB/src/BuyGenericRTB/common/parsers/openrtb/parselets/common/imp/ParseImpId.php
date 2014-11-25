<?php
/**
 * CDNPAL NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2015 CDNPAL Ltd. All Rights Reserved
 * @license GPLv3
 */

namespace buyrtb\parsers\openrtb\parselets\common\imp;
use \Exception;

class ParseImpId {
	
	public static function execute(&$Logger, \buyrtb\parsers\openrtb\OpenRTBParser &$Parser, \model\openrtb\RtbBidRequestImp &$RtbBidRequestImp, &$ad_impression) {
		
		/*
		 * Get impression id
		 */
		
		$Parser->parse_with_exception(
				$RtbBidRequestImp,
				$ad_impression,
				$Parser->expeption_missing_min_bid_request_params . ": imp_id",
				"id");
		
	}
}
