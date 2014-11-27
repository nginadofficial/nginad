<?php
/**
 * CDNPAL NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2015 CDNPAL Ltd. All Rights Reserved
 * @license GPLv3
 */

namespace buyrtb\parsers\openrtb\parselets\common;

class ParseSegment {
	
	public static function execute(&$Logger, \buyrtb\parsers\openrtb\OpenRTBParser &$Parser, \model\openrtb\RtbBidRequest &$RtbBidRequest, \model\openrtb\RtbBidRequestSegment &$RtbBidRequestSegment, &$segment) {
	
		// segment id
	
		\util\ParseHelper::parse_item(
				$RtbBidRequestSegment,
				$segment,
				"id");
	
		// segment name
	
		\util\ParseHelper::parse_item(
				$RtbBidRequestSegment,
				$segment,
				"name");
	
		// segment value
		
		\util\ParseHelper::parse_item(
				$RtbBidRequestSegment,
				$segment,
				"value");
	}
	
}
