<?php
/**
 * CDNPAL NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2015 CDNPAL Ltd. All Rights Reserved
 * @license GPLv3
 */

namespace buyrtb\parsers\openrtb\parselets\common;

class ParseSecondPrice {
	// second price?
	public static function execute(&$Logger, \buyrtb\parsers\openrtb\OpenRTBParser &$Parser, \model\openrtb\RtbBidRequest &$RtbBidRequest) {
	
		if (isset($Parser->json_post["at"])):
			$RtbBidRequest->at 		= $Parser->json_post["at"];
		endif;

	}
}
