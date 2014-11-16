<?php
/**
 * CDNPAL NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2015 CDNPAL Ltd. All Rights Reserved
 * @license GPLv3
 */

namespace buyrtb\parsers\openrtb\parselets\common;
use \Exception;

class ParseCurrency {
	
	public static function execute(&$Logger, \buyrtb\parsers\openrtb\OpenRTBParser &$Parser, \model\rtb\RtbBidRequest &$RtbBidRequest) {
	
		/*
		 * Only grab the main (first currency)
		 * Currently only USD is supported
		 */
		
		if (isset($Parser->json_post["cur"][0])):
		
			$currency = $Parser->json_post["cur"][0];
		
			$RtbBidRequest->bid_request_cur = strtoupper($currency);
			
			if ($RtbBidRequest->bid_request_cur != "USD"):
			 
				throw new Exception($Parser->expeption_missing_min_bid_request_params . ": cur: system only accepts USD currency at this time");
			 
			endif;
			
		else:
		
			throw new Exception($Parser->expeption_missing_min_bid_request_params . ": at least 1 cur object");
		
		endif;
		
	}
}
