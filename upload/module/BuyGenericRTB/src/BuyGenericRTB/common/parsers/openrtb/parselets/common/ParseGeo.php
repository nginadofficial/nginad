<?php
/**
 * CDNPAL NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2015 CDNPAL Ltd. All Rights Reserved
 * @license GPLv3
 */

namespace buyrtb\parsers\openrtb\parselets\common;

class ParseGeo {
	
	public static function execute(&$Logger, \buyrtb\parsers\openrtb\OpenRTBParser &$Parser, \model\openrtb\RtbBidRequest &$RtbBidRequest, \model\openrtb\RtbBidRequestGeo &$RtbBidRequestGeo, &$geo) {

		// location latitude
		
		$Parser->parse_item(
				$RtbBidRequestGeo,
				$geo,
				"lat");
		
		// location longitude
		
		$Parser->parse_item(
				$RtbBidRequestGeo,
				$geo,
				"lon");
		
		// location country
		
		$Parser->parse_item(
				$RtbBidRequestGeo,
				$geo,
				"country");
		
		// location state or region
		
		$Parser->parse_item(
				$RtbBidRequestGeo,
				$geo,
				"region");
		
		// location state or region fips
		
		$Parser->parse_item(
				$RtbBidRequestGeo,
				$geo,
				"regionfips104");
		
		// location metro area code
		// http://code.google.com/apis/adwords/docs/appendix/metrocodes.htm
		
		$Parser->parse_item(
				$RtbBidRequestGeo,
				$geo,
				"metro");
		
		// location city code
		// http://www.unece.org/cefact/locode/service/location.htm
		
		$Parser->parse_item(
				$RtbBidRequestGeo,
				$geo,
				"city");
		
		// location zip
		
		$Parser->parse_item(
				$RtbBidRequestGeo,
				$geo,
				"zip");
		
		// location geo data source
		
		$Parser->parse_item(
				$RtbBidRequestGeo,
				$geo,
				"type");

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
