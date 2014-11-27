<?php
/**
 * CDNPAL NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2015 CDNPAL Ltd. All Rights Reserved
 * @license GPLv3
 */

namespace sellrtb\parsers\openrtb\parselets\common;
use \Exception;

class Init {
	
	public static function execute(&$Logger, \sellrtb\parsers\openrtb\OpenRTBParser &$Parser, \model\openrtb\RtbBidResponse &$RtbBidResponse) {
	
		/*
		 * Initialize Data
		 */
		
		/*
		 * Get the incoming bid request data from the
		* HTTP RESPONSE
		*
		* If the required fields are not there throw an exception
		* to the caller
		*/
		
		$Logger->min_log[] = "POST: " . $Parser->raw_post;
		
		if ($Parser->raw_post):
			$Parser->json_response = json_decode($Parser->raw_post, true);
		else:
			$Parser->json_response = null;
		endif;
		
		if ($Parser->json_response === null):
		
			throw new Exception($Parser->expeption_missing_min_bid_request_params . ": JSON RESPONSE DATA");
		
		endif;
		
		$Logger->log[] = "RESPONSE: " . print_r($Parser->json_response, true);

		\util\ParseHelper::parse_with_exception(
				$RtbBidResponse,
				$Parser->json_response,
				$Parser->expeption_missing_min_bid_request_params . ": id",
				"id");
		
		\util\ParseHelper::parse_item(
				$RtbBidResponse,
				$Parser->json_response,
				"bidid");		

		\util\ParseHelper::parse_item(
				$RtbBidResponse,
				$Parser->json_response,
				"cur");

		\util\ParseHelper::parse_item(
				$RtbBidResponse,
				$Parser->json_response,
				"customdata");

		\util\ParseHelper::parse_item(
				$RtbBidResponse,
				$Parser->json_response,
				"nbr");
		
	}
	
}
