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
	
	public static function execute(&$Logger, &$Workflow, &$RTBPingerList, \sellrtb\workflows\tasklets\popo\AuctionPopo &$AuctionPopo) {
	
		$result = false;
		
		for ($y = 0; $y < count($RTBPingerList); $y++):
		
			for ($i = 0; $i < count($RTBPingerList[$y]->RtbBidResponse->RtbBidResponseSeatBidList); $i++):
			
				for ($j = 0; $j < count($RTBPingerList[$y]->RtbBidResponse->RtbBidResponseSeatBidList[$i]->RtbBidResponseBidList); $j++):
			
					self::setAdjustedMarkup($Logger, $AuctionPopo, $RTBPingerList[$y]->RtbBidResponse->RtbBidResponseSeatBidList[$i]->RtbBidResponseBidList[$j]);
					$result = true;
					
				endfor;
		
			endfor;
		
		endfor;
		
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
