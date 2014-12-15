<?php
/**
 * CDNPAL NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2015 CDNPAL Ltd. All Rights Reserved
 * @license GPLv3
 */

namespace sellrtb\workflows\tasklets\common;

class AdjustBidAmountWithMarkup {
	
	public static function execute(&$Logger, &$Workflow, \sellrtb\workflows\tasklets\popo\AuctionPopo &$AuctionPopo) {
	
		$result = false;
		
		foreach ($AuctionPopo->SelectedPingerList as $method_outer_key => $RTBPinger):
		
			foreach ($AuctionPopo->SelectedPingerList[$method_outer_key]->RtbBidResponse->RtbBidResponseSeatBidList as $outer_key => $RtbBidResponseSeatBid):
			
				foreach ($AuctionPopo->SelectedPingerList[$method_outer_key]->RtbBidResponse->RtbBidResponseSeatBidList[$outer_key]->RtbBidResponseBidList as $key => $RtbBidResponseBid):
			
					self::setAdjustedMarkup($Logger, $AuctionPopo, $AuctionPopo->SelectedPingerList[$method_outer_key]->RtbBidResponse->RtbBidResponseSeatBidList[$outer_key]->RtbBidResponseBidList[$key]);
					$result = true;
					
				endforeach;
		
			endforeach;
		
		endforeach;
		
		return $result;
		
	}
	
	private static function setAdjustedMarkup(&$Logger, \sellrtb\workflows\tasklets\popo\AuctionPopo &$AuctionPopo, \model\openrtb\RtbBidResponseBid &$RtbBidResponseBid) {
		
		/*
		 * Set the publisher's markup rate.
		*
		* So if the Publisher's floor price is $0.09
		* and the markup rate is 40%
		*
		* Then the bid must be at least $0.15
		* $0.15 * 40% = 0.09 CPM
		*
		* Also make sure it's greater than zero
		*/
		
		$bid_price 		= floatval($RtbBidResponseBid->price);
		
		$mark_down = floatval($bid_price) * floatval($AuctionPopo->publisher_markup_rate);
		$RtbBidResponseBid->adusted_bid_amount = $bid_price - floatval($mark_down);

	}
}
