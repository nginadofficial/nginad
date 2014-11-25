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

class ParseBids {
	
	public static function execute(&$Logger, \sellrtb\parsers\openrtb\OpenRTBParser &$Parser, \model\openrtb\RtbBidResponseSeatBid &$RtbBidResponseSeatBid, $seatbid) {
	
		if (!isset($seatbid["bid"])
		|| !is_array($seatbid["bid"])
		|| !count($seatbid["bid"])):
			
		throw new Exception($Parser->expeption_missing_min_bid_request_params . ": bid is empty");
		endif;
		
		foreach ($seatbid["bid"] as $bid):
		
			$RtbBidResponseBid = new \model\openrtb\RtbBidResponseBid();

			$Parser->parse_with_exception(
					$RtbBidResponseBid,
					$bid,
					$Parser->expeption_missing_min_bid_request_params . ": bid id",
					"id");
		
			$Parser->parse_with_exception(
					$RtbBidResponseBid,
					$bid,
					$Parser->expeption_missing_min_bid_request_params . ": bid impid",
					"impid");
			
			$Parser->parse_with_exception(
					$RtbBidResponseBid,
					$bid,
					$Parser->expeption_missing_min_bid_request_params . ": bid price",
					"price");
			
			$Parser->parse_item(
					$RtbBidResponseBid,
					$bid,
					"adid");
				
			$Parser->parse_item(
					$RtbBidResponseBid,
					$bid,
					"nurl");
				
			$Parser->parse_item(
					$RtbBidResponseBid,
					$bid,
					"adm");
			
			$Parser->parse_item(
					$RtbBidResponseBid,
					$bid,
					"adomain");
			
			$Parser->parse_item(
					$RtbBidResponseBid,
					$bid,
					"lurl");
			
			$Parser->parse_item(
					$RtbBidResponseBid,
					$bid,
					"cid");
			
			$Parser->parse_item(
					$RtbBidResponseBid,
					$bid,
					"crid");
			
			$Parser->parse_item(
					$RtbBidResponseBid,
					$bid,
					"attr");
			
			$Parser->parse_item(
					$RtbBidResponseBid,
					$bid,
					"dealid");
			
			$Parser->parse_item(
					$RtbBidResponseBid,
					$bid,
					"h");
			
			$Parser->parse_item(
					$RtbBidResponseBid,
					$bid,
					"w");
			
			$RtbBidResponseSeatBid->RtbBidResponseBidList[] = $RtbBidResponseBid;
			
		endforeach;

	}
	
}
