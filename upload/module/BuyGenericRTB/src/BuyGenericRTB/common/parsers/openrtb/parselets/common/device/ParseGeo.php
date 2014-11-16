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

		if (isset($geo["country"])):
			$RtbBidRequestGeo->country = $geo["country"];
		endif;
			
		if (isset($geo["region"])):
			$RtbBidRequestGeo->state = $geo["region"];
		endif;
				
		if (isset($geo["city"])):
			$RtbBidRequestGeo->city = $geo["city"];
		endif;
				
		if (isset($RtbBidRequestGeo->country)):
			$Logger->log[] = "Geo Data Country: " . $this->bid_request_geo["country"];
		endif;
		if (isset($RtbBidRequestGeo->state)):
			$Logger->log[] = "Geo Data State: " . $this->bid_request_geo["state"];
		endif;
		if (isset($RtbBidRequestGeo->city)):
			$Logger->log[] = "Geo Data City: " . $this->bid_request_geo["city"];
		endif;
	}
}
