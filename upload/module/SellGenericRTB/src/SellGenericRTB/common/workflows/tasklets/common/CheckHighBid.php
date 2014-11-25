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
		
		for ($y = 0; $y < count($RTBPingerList); $y++):
	
			for ($i = 0; $i < count($RTBPingerList[$y]->RtbBidResponse->RtbBidResponseSeatBidList); $i++):
				
				for ($j = 0; $j < count($RTBPingerList[$y]->RtbBidResponse->RtbBidResponseSeatBidList[$i]->RtbBidResponseBidList); $j++):
						
					self::getHighBidPrice($Logger, $AuctionPopo, $RTBPingerList[$y]->RtbBidResponse->RtbBidResponseSeatBidList[$i]->RtbBidResponseBidList[$j]);
				
				endfor;
		
			endfor;
			
		endfor;
	
		if ($AuctionPopo->highest_bid_price === null):
		
			return result;
		
		endif;
		
		for ($y = 0; $y < count($RTBPingerList); $y++):
			
			$bids_remain = false;
		
			for ($i = 0; $i < count($RTBPingerList[$y]->RtbBidResponse->RtbBidResponseSeatBidList); $i++):
			
				for ($j = 0; $j < count($RTBPingerList[$y]->RtbBidResponse->RtbBidResponseSeatBidList[$i]->RtbBidResponseBidList); $j++):
				
					$is_auction_winner_candidate = self::isAuctionWinnerCandidate($Logger, $AuctionPopo, $RTBPingerList[$y]->RtbBidResponse->RtbBidResponseSeatBidList[$i]->RtbBidResponseBidList[$j]);
					
					if ($is_auction_winner_candidate == false):
					
						unset($RTBPingerList[$y]->RtbBidResponse->RtbBidResponseSeatBidList[$i]->RtbBidResponseBidList[$j]);
						
					endif;
				
				endfor;
				
				if (count($RTBPingerList[$y]->RtbBidResponse->RtbBidResponseSeatBidList[$i]->RtbBidResponseBidList)):
				
					$bids_remain = true;
				
				else: 
				
					unset($RTBPingerList[$y]->RtbBidResponse->RtbBidResponseSeatBidList[$i]);
				
				endif;
			
			endfor;
			
			if ($bids_remain == true):
			
				/*
				 * Those RTBPingers that still have at least 1 bid get added
				* to the selected pingers list in the POPO
				*/
				$AuctionPopo->SelectedPingerList[] = $RTBPingerList[$y];
					
				$result = true;
					
			endif;
				
		endfor;
		
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
