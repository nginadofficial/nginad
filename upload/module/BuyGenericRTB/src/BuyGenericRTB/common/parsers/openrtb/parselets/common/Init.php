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

class Init {
	
	public static function execute(&$Logger, \buyrtb\parsers\openrtb\OpenRTBParser &$Parser, \model\openrtb\RtbBidRequest &$RtbBidRequest) {
	
		/*
		 * Initialize Data
		 */
		
		/*
		 * Get the incoming bid request data from the
		* HTTP REQUEST
		*
		* If the required fields are not there throw an exception
		* to the caller
		*/
		
		/*
		 * mobile, rich media, ect..
		* mobile web, phone, tablet, native iOS or native Android
		*/
		
		if ($Parser->raw_post === null):
			$Parser->raw_post = file_get_contents('php://input');
		endif;
		
		$Logger->min_log[] = "POST: " . $Parser->raw_post;
		
		if ($Parser->raw_post):
			$Parser->json_post = json_decode($Parser->raw_post, true);
		else:
			$Parser->json_post = null;
		endif;
		
		$Parser->bid_request_mobile = 0;
		
		if ($Parser->json_post === null):
		
			throw new Exception($Parser->expeption_missing_min_bid_request_params . ": JSON POST DATA");
		
		endif;
		
		$Logger->log[] = "POST: " . print_r($Parser->json_post, true);
		
		\util\ParseHelper::parse_with_exception(
				$RtbBidRequest, 
				$Parser->json_post, 
				$Parser->expeption_missing_min_bid_request_params . ": id", 
				"id");

	}
	
}
