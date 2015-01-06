<?php
/**
 * CDNPAL NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2015 CDNPAL Ltd. All Rights Reserved
 * @license GPLv3
 */

namespace sellrtb\workflows\tasklets\common;

/*
 * After this tasklet all bids should be tied for first place
 */

class CheckHighBid {
	
	
	public static function execute(&$Logger, &$Workflow, \sellrtb\workflows\tasklets\popo\AuctionPopo &$AuctionPopo) {
	
		$result = false;
	
		$RTBPingerList = $AuctionPopo->SelectedPingerList;
		
		$AuctionPopo->SelectedPingerList = array();
	
		/*
		 * When multiple bid impression pings are enabled in a next version
		* We will also need to check the response impid against the impression ids
		* that were sent in the outgoing RTB requests
		* 
		* Determine the high bid price
		*/
		
		foreach ($RTBPingerList as $method_outer_key => $RTBPinger):
	
			foreach ($RTBPingerList[$method_outer_key]->RtbBidResponse->RtbBidResponseSeatBidList as $outer_key => $RtbBidResponseSeatBid):
				
				foreach ($RTBPingerList[$method_outer_key]->RtbBidResponse->RtbBidResponseSeatBidList[$outer_key]->RtbBidResponseBidList as $key => $RtbBidResponseBid):
						
					self::getHighBidPrice($Logger, $AuctionPopo, $RTBPingerList[$method_outer_key]->RtbBidResponse->RtbBidResponseSeatBidList[$outer_key]->RtbBidResponseBidList[$key]);
				
				endforeach;
		
			endforeach;
			
		endforeach;
	
		if ($AuctionPopo->highest_bid_price === null):
		
			return result;
		
		endif;
		
		foreach ($RTBPingerList as $method_outer_key => $RTBPinger):
			
			$bids_remain = false;
		
			foreach ($RTBPingerList[$method_outer_key]->RtbBidResponse->RtbBidResponseSeatBidList as $outer_key => $RtbBidResponseSeatBid):
			
				foreach ($RTBPingerList[$method_outer_key]->RtbBidResponse->RtbBidResponseSeatBidList[$outer_key]->RtbBidResponseBidList as $key => $RtbBidResponseBid):
					
					$is_auction_winner_candidate = self::isAuctionWinnerCandidate($Logger, $AuctionPopo, $RTBPingerList[$method_outer_key]->RtbBidResponse->RtbBidResponseSeatBidList[$outer_key]->RtbBidResponseBidList[$key]);
					
					if ($is_auction_winner_candidate == false):
					
						unset($RTBPingerList[$method_outer_key]->RtbBidResponse->RtbBidResponseSeatBidList[$outer_key]->RtbBidResponseBidList[$key]);
						
					endif;
				
				endforeach;
				
				if (count($RTBPingerList[$method_outer_key]->RtbBidResponse->RtbBidResponseSeatBidList[$outer_key]->RtbBidResponseBidList)):
				
					$bids_remain = true;
				
				else: 
				
					unset($RTBPingerList[$method_outer_key]->RtbBidResponse->RtbBidResponseSeatBidList[$outer_key]);
				
				endif;
			
			endforeach;
			
			if ($bids_remain === true):
			
				/*
				 * Those RTBPingers that still have at least 1 bid get added
				* to the selected pingers list in the POPO
				*/
				$AuctionPopo->SelectedPingerList[] = $RTBPingerList[$method_outer_key];
					
				$result = true;
					
			endif;
				
		endforeach;
		
		return $result;
	
	}
	
	private static function getHighBidPrice(&$Logger, \sellrtb\workflows\tasklets\popo\AuctionPopo &$AuctionPopo, \model\openrtb\RtbBidResponseBid &$RtbBidResponseBid) {
	
		$bid_price 		= floatval($RtbBidResponseBid->adusted_bid_amount);
	
		if ($AuctionPopo->highest_bid_price === null
			||
			$bid_price >= $AuctionPopo->highest_bid_price):
			
			$AuctionPopo->highest_bid_price = $bid_price;
		
			return true;
			
		endif;
		
		return false;
	
	}
	
	private static function isAuctionWinnerCandidate(&$Logger, \sellrtb\workflows\tasklets\popo\AuctionPopo &$AuctionPopo, \model\openrtb\RtbBidResponseBid &$RtbBidResponseBid) {
	
		$bid_price 		= floatval($RtbBidResponseBid->adusted_bid_amount);
	
		if ($bid_price >= $AuctionPopo->highest_bid_price):
			
			return true;
			
		endif;
	
	}
	
}
