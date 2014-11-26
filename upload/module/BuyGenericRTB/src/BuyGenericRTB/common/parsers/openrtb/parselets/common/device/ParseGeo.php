<?php
/**
 * CDNPAL NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2015 CDNPAL Ltd. All Rights Reserved
 * @license GPLv3
 */

namespace buyrtb\parsers\openrtb\parselets\common\device;

class ParseGeo {
	
	public static function execute(&$Logger, \buyrtb\parsers\openrtb\OpenRTBParser &$Parser, \model\openrtb\RtbBidRequest &$RtbBidRequest, \model\openrtb\RtbBidRequestGeo &$RtbBidRequestGeo, &$geo) {

		$Parser->parse_item(
				$RtbBidRequestGeo,
				$geo,
				"country");
		
		$Parser->parse_item(
				$RtbBidRequestGeo,
				$geo,
				"region");
		
		$Parser->parse_item(
				$RtbBidRequestGeo,
				$geo,
				"city");

		if (isset($RtbBidRequestGeo->country)):
			$Logger->log[] = "Geo Data Country: " . $this->bid_request_geo["country"];
		endif;
		if (isset($RtbBidRequestGeo->region)):
			$Logger->log[] = "Geo Data Region: " . $this->bid_request_geo["region"];
		endif;
		if (isset($RtbBidRequestGeo->city)):
			$Logger->log[] = "Geo Data City: " . $this->bid_request_geo["city"];
		endif;
	}
}
