<?php
/**
 * CDNPAL NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2015 CDNPAL Ltd. All Rights Reserved
 * @license GPLv3
 */

namespace sellrtb\workflows\tasklets\common;

class PickAWinner {
	
	/*
	 * By this time we have a selected bid list with 1 or more potential winners
	 * This tasklet will select one at random
	 */
	
	public static function execute(&$Logger, &$Workflow, &$OriginalRTBPingerList, \sellrtb\workflows\tasklets\popo\AuctionPopo &$AuctionPopo) {
	
		$result = false;
	
		$RTBPingerList = $AuctionPopo->SelectedPingerList;
	
		$AuctionPopo->SelectedPingerList = array();
	
		/*
		 * When we have 1 or more bids we need to pick a winner at random
		*/
	
		// get winning uid
		
		$winning_pinger_uid = null;
		$winning_bid_uid = null;
		$winning_bid_price = null;
		
		// get total number of bids
		
		$total_bids = 0;
		
		foreach ($RTBPingerList as $method_outer_key => $RTBPinger):
	
			foreach ($RTBPingerList[$method_outer_key]->RtbBidResponse->RtbBidResponseSeatBidList as $outer_key => $RtbBidResponseSeatBid):
		
				$total_bids += count($RTBPingerList[$method_outer_key]->RtbBidResponse->RtbBidResponseSeatBidList[$outer_key]->RtbBidResponseBidList);
			
			endforeach;
			
		endforeach;
	
		$random_winner_idx = rand(0, $total_bids - 1);
	
		$bid_count = 0;
		
		foreach ($RTBPingerList as $method_outer_key => $RTBPinger):
			
			$winner = false;
			
			foreach ($RTBPingerList[$method_outer_key]->RtbBidResponse->RtbBidResponseSeatBidList as $outer_key => $RtbBidResponseSeatBid):
					
				$RTBPingerList[$method_outer_key]->lost_bids = count($RTBPingerList[$method_outer_key]->RtbBidResponse->RtbBidResponseSeatBidList[$outer_key]->RtbBidResponseBidList);
			
				foreach ($RTBPingerList[$method_outer_key]->RtbBidResponse->RtbBidResponseSeatBidList[$outer_key]->RtbBidResponseBidList as $key => $RtbBidResponseBid):
			
					if ($bid_count++ == $random_winner_idx):
						
						$winner = true;
						$winning_bid_price 	= floatval($RTBPingerList[$method_outer_key]->RtbBidResponse->RtbBidResponseSeatBidList[$outer_key]->RtbBidResponseBidList[$key]->price);
						$RTBPingerList[$method_outer_key]->won_auction = true;
						$RTBPingerList[$method_outer_key]->RtbBidResponse->RtbBidResponseSeatBidList[$outer_key]->RtbBidResponseBidList[$key]->won_auction = true;
						$winning_pinger_uid = $RTBPingerList[$method_outer_key]->uid;
						$winning_bid_uid 	= $RTBPingerList[$method_outer_key]->RtbBidResponse->RtbBidResponseSeatBidList[$outer_key]->RtbBidResponseBidList[$key]->uid;
					
					else:
					
						unset($RTBPingerList[$method_outer_key]->RtbBidResponse->RtbBidResponseSeatBidList[$outer_key]->RtbBidResponseBidList[$key]);
					
						continue;
					
					endif;
					
				endforeach;
				
				// if the winning bid is not in this seatbid remove it
				
				if ($winner == false):
					
					unset($RTBPingerList[$method_outer_key]->RtbBidResponse->RtbBidResponseSeatBidList[$outer_key]);
					
				endif;
				
			endforeach;
			
			if ($winner == true):
					
				/*
				 * The winning RTBPinger has it's single winning bid
				* and all other bids in that RTB response are
				* now removed
				*/
				$AuctionPopo->SelectedPingerList[] = $RTBPingerList[$method_outer_key];
				
				$result = true;
				
				break;
					
			endif;
	
		endforeach;
		
		/*
		 * Now flag the reference to the winning pinger and update totals
		 */
		
		if ($result == true):
		
			foreach ($OriginalRTBPingerList as $method_outer_key => $RTBPinger):
			
				if ($OriginalRTBPingerList[$method_outer_key]->uid == $winning_pinger_uid):
					$OriginalRTBPingerList[$method_outer_key]->won_auction = true;
					$OriginalRTBPingerList[$method_outer_key]->lost_bids 	= $OriginalRTBPingerList[$method_outer_key]->total_bids - 1;
					$OriginalRTBPingerList[$method_outer_key]->won_bids 	= 1;
					$OriginalRTBPingerList[$method_outer_key]->winning_bid = floatval($winning_bid_price);
				else:
					$OriginalRTBPingerList[$method_outer_key]->lost_bids 	= $OriginalRTBPingerList[$method_outer_key]->total_bids;
					$OriginalRTBPingerList[$method_outer_key]->won_bids 	= 0;
				endif;
			
				if (isset($OriginalRTBPingerList[$method_outer_key]->RtbBidResponse) && $OriginalRTBPingerList[$method_outer_key]->RtbBidResponse !== null):
					
					/*
					 * If the RTB Bid Response fails parsing the RtbBidResponse member 
					 * of the pinger will be null and therefore should be skipped
					 */

					foreach ($OriginalRTBPingerList[$method_outer_key]->RtbBidResponse->RtbBidResponseSeatBidList as $outer_key => $RtbBidResponseSeatBid):
					
						foreach ($OriginalRTBPingerList[$method_outer_key]->RtbBidResponse->RtbBidResponseSeatBidList[$outer_key]->RtbBidResponseBidList as $key => $RtbBidResponseBid):
		
							if ($OriginalRTBPingerList[$method_outer_key]->RtbBidResponse->RtbBidResponseSeatBidList[$outer_key]->RtbBidResponseBidList[$key]->uid == $winning_bid_uid):
								$OriginalRTBPingerList[$method_outer_key]->RtbBidResponse->RtbBidResponseSeatBidList[$outer_key]->RtbBidResponseBidList[$key]->won_auction = true;
							endif;
		
						endforeach;
					
					endforeach;
				
				endif;
					
			endforeach;
			
		endif;
		
		return $result;
	
	}
}
