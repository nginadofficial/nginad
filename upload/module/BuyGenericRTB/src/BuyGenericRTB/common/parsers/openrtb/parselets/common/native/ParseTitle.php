<?php
/**
 * NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2016 NginAd Foundation. All Rights Reserved
 * @license GPLv3
 */

namespace buyrtb\parsers\openrtb\parselets\common\native;

class ParseTitle {
	
	public static function execute(&$Logger, \buyrtb\parsers\openrtb\OpenRTBParser &$Parser, \model\openrtb\native\request\RtbBidRequestNativeTitle &$RtbBidRequestNativeTitle, &$rtb_native_title) {
	
		
		// Title

		try {
			\util\ParseHelper::parse_with_exception(
				$RtbBidRequestNativeTitle,
		     	$rtb_native_title,
		    	$Parser->expeption_missing_min_bid_request_params . ": len",
		   		"len");
			
		} catch (Exception $e) {
	  		throw new Exception($e->getMessage(), $e->getCode(), $e->getPrevious());
	 	}
		
	}
}
