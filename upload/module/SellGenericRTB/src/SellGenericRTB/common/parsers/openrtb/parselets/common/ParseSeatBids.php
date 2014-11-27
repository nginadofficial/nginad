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

class ParseSeatBids {
	
	public static function execute(&$Logger, \sellrtb\parsers\openrtb\OpenRTBParser &$Parser, \model\openrtb\RtbBidResponse &$RtbBidResponse) {
	
		
		if (!isset($Parser->json_response["seatbid"]) 
			|| !is_array($Parser->json_response["seatbid"])
			|| !count($Parser->json_response["seatbid"])):
			
			throw new Exception($Parser->expeption_missing_min_bid_request_params . ": seatbid is empty");
		endif;
		
		$RtbBidResponse->RtbBidResponseSeatBidList = array();
		
		foreach ($Parser->json_response["seatbid"] as $seatbid):

			$RtbBidResponseSeatBid = new \model\openrtb\RtbBidResponseSeatBid();
		
			\util\ParseHelper::parse_item(
					$RtbBidResponseSeatBid,
					$seatbid,
					"seat");
			
			\util\ParseHelper::parse_item(
					$RtbBidResponseSeatBid,
					$seatbid,
					"group");
			
			try {
				\sellrtb\parsers\openrtb\parselets\common\ParseBids::execute($Logger, $Parser, $RtbBidResponseSeatBid, $seatbid);
				$RtbBidResponse->RtbBidResponseSeatBidList[] = $RtbBidResponseSeatBid;
			} catch (Exception $e) {
				// throw new Exception($e->getMessage(), $e->getCode(), $e->getPrevious());
			}

		endforeach;



	}
}
