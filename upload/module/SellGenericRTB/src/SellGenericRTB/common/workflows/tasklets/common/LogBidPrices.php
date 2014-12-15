<?php
/**
 * CDNPAL NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2015 CDNPAL Ltd. All Rights Reserved
 * @license GPLv3
 */

namespace sellrtb\workflows\tasklets\common;

class LogBidPrices {
	
	/*
	 * Log all ordered bid prices for second price auctions
	 */
	
	public static function execute(&$Logger, &$Workflow, \sellrtb\workflows\tasklets\popo\AuctionPopo &$AuctionPopo) {
	
		// init
		$AuctionPopo->bid_price_list 			= array();
		$AuctionPopo->adjusted_bid_price_list 	= array();
		
		$RTBPingerList = $AuctionPopo->SelectedPingerList;
		
		$result = false;
	
		foreach ($RTBPingerList as $method_outer_key => $RTBPinger):
	
			foreach ($RTBPingerList[$method_outer_key]->RtbBidResponse->RtbBidResponseSeatBidList as $outer_key => $RtbBidResponseSeatBid):
				
				foreach ($RTBPingerList[$method_outer_key]->RtbBidResponse->RtbBidResponseSeatBidList[$outer_key]->RtbBidResponseBidList as $key => $RtbBidResponseBid):
					
					self::logBidPrice($Logger, $AuctionPopo, $RTBPingerList[$method_outer_key]->RtbBidResponse->RtbBidResponseSeatBidList[$outer_key]->RtbBidResponseBidList[$key]);
					$result = true;
						
				endforeach;
			
			endforeach;
		
		endforeach;
	
		// grab hashed keys of bid prices
		$AuctionPopo->bid_price_list 			= array_keys($AuctionPopo->bid_price_list);
		$AuctionPopo->adjusted_bid_price_list 	= array_keys($AuctionPopo->adjusted_bid_price_list);
		
		// reverse sort so the highest bid is the first item in the array
		rsort($AuctionPopo->bid_price_list);
		rsort($AuctionPopo->adjusted_bid_price_list);
		
		return $result;
	
	}
	
	private static function logBidPrice(&$Logger, \sellrtb\workflows\tasklets\popo\AuctionPopo &$AuctionPopo, \model\openrtb\RtbBidResponseBid &$RtbBidResponseBid) {
	
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
	
		$bid_price 					= floatval($RtbBidResponseBid->price);
	
		$adjusted_bid_price 		= floatval($RtbBidResponseBid->adusted_bid_amount);
		
		$AuctionPopo->bid_price_list[(string)$bid_price] = true;
		$AuctionPopo->adjusted_bid_price_list[(string)$adjusted_bid_price] = true;

		
		
	}
	
}
