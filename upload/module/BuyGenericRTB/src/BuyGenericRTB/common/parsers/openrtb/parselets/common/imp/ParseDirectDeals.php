<?php
/**
 * CDNPAL NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2015 CDNPAL Ltd. All Rights Reserved
 * @license GPLv3
 */

namespace buyrtb\parsers\openrtb\parselets\common\imp;

class ParseDirectDeals {
	
	public static function execute(&$Logger, \buyrtb\parsers\openrtb\OpenRTBParser &$Parser, \model\openrtb\RtbBidRequest &$RtbBidRequest, \model\openrtb\RtbBidRequestDirectDeals &$RtbBidRequestDirectDeals, &$direct_deal) {
	
		// Deal ID
	
		$Parser->parse_with_exception(
				$RtbBidRequestDirectDeals,
				$direct_deal,
				$Parser->expeption_missing_min_bid_request_params . ": direct deal id",
				"id");
	
		// Deal Bid Floor
		
		$Parser->parse_item(
				$RtbBidRequestDirectDeals,
				$direct_deal,
				"bidfloor");
		
		// Deal Bid Floor Currency
		
		$Parser->parse_item(
				$RtbBidRequestDirectDeals,
				$direct_deal,
				"bidfloorcur");
		 
		// Deal Buyer Seats allowed to bid
		
		$Parser->parse_item_list(
				$RtbBidRequestDirectDeals,
				$direct_deal,
				"wseat");
		
		// Deal Advertiser Domains allowed to bid
		
		$Parser->parse_item_list(
				$RtbBidRequestDirectDeals,
				$direct_deal,
				"wadomain");
		
		// Deal Auction type ( second price or first price )
		
		$Parser->parse_item(
				$RtbBidRequestDirectDeals,
				$direct_deal,
				"at");
		
	}
	
}
