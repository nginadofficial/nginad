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
		
		for ($y = 0; $y < count($RTBPingerList); $y++):
	
			for ($i = 0; $i < count($RTBPingerList[$y]->RtbBidResponse->RtbBidResponseSeatBidList); $i++):
		
				for ($j = 0; $j < count($RTBPingerList[$y]->RtbBidResponse->RtbBidResponseSeatBidList[$i]->RtbBidResponseBidList); $j++):
			
					$total_bids += count($RTBPingerList[$y]->RtbBidResponse->RtbBidResponseSeatBidList[$i]->RtbBidResponseBidList);
			
				endfor;
		
			endfor;
			
		endfor;
	
		$random_winner_idx = rand(0, $total_bids - 1);
	
		$bid_count = 0;
		
		for ($y = 0; $y < count($RTBPingerList); $y++):
			
			$winner = false;
			
			for ($i = 0; $i < count($RTBPingerList[$y]->RtbBidResponse->RtbBidResponseSeatBidList); $i++):
					
				$RTBPingerList[$y]->lost_bids = count($RTBPingerList[$y]->RtbBidResponse->RtbBidResponseSeatBidList[$i]->RtbBidResponseBidList);
			
				for ($j = 0; $j < count($RTBPingerList[$y]->RtbBidResponse->RtbBidResponseSeatBidList[$i]->RtbBidResponseBidList); $j++):
			
					if ($bid_count++ == $random_winner_idx):
						
						$winner = true;
						$winning_bid_price 	= floatval($RTBPingerList[$y]->RtbBidResponse->RtbBidResponseSeatBidList[$i]->RtbBidResponseBidList[$j]->price);
						$RTBPingerList[$y]->won_auction = true;
						$RTBPingerList[$y]->RtbBidResponse->RtbBidResponseSeatBidList[$i]->RtbBidResponseBidList[$j]->won_auction = true;
						$winning_pinger_uid = $RTBPingerList[$y]->uid;
						$winning_bid_uid 	= $RTBPingerList[$y]->RtbBidResponse->RtbBidResponseSeatBidList[$i]->RtbBidResponseBidList[$j]->uid;
					
					else:
					
						unset($RTBPingerList[$y]->RtbBidResponse->RtbBidResponseSeatBidList[$i]->RtbBidResponseBidList[$j]);
					
						continue;
					
					endif;
					
				endfor;
				
				// if the winning bid is not in this seatbid remove it
				
				if ($winner == false):
					
					unset($RTBPingerList[$y]->RtbBidResponse->RtbBidResponseSeatBidList[$i]);
					
				endif;
				
			endfor;
			
			if ($winner == true):
					
				/*
				 * The winning RTBPinger has it's single winning bid
				* and all other bids in that RTB response are
				* now removed
				*/
				$AuctionPopo->SelectedPingerList[] = $RTBPingerList[$y];
				
				$result = true;
				
				break;
					
			endif;
	
		endfor;
		
		/*
		 * Now flag the reference to the winning pinger and update totals
		 */
		
		if ($result == true):
		
			for ($y = 0; $y < count($OriginalRTBPingerList); $y++):
			
				if ($OriginalRTBPingerList[$y]->uid == $winning_pinger_uid):
					$OriginalRTBPingerList[$y]->won_auction = true;
					$OriginalRTBPingerList[$y]->lost_bids 	= $OriginalRTBPingerList[$y]->total_bids - 1;
					$OriginalRTBPingerList[$y]->won_bids 	= 1;
					$OriginalRTBPingerList[$y]->winning_bid = floatval($winning_bid_price);
				else:
					$OriginalRTBPingerList[$y]->lost_bids 	= $OriginalRTBPingerList[$y]->total_bids;
					$OriginalRTBPingerList[$y]->won_bids 	= 0;
				endif;
			
				if (isset($OriginalRTBPingerList[$y]->RtbBidResponse)):
					
					/*
					 * If the RTB Bid Response fails parsing the RtbBidResponse member 
					 * of the pinger will be null and therefore should be skipped
					 */

					for ($i = 0; $i < count($OriginalRTBPingerList[$y]->RtbBidResponse->RtbBidResponseSeatBidList); $i++):
					
						for ($j = 0; $j < count($OriginalRTBPingerList[$y]->RtbBidResponse->RtbBidResponseSeatBidList[$i]->RtbBidResponseBidList); $j++):
		
							if ($OriginalRTBPingerList[$y]->RtbBidResponse->RtbBidResponseSeatBidList[$i]->RtbBidResponseBidList[$j]->uid == $winning_bid_uid):
								$OriginalRTBPingerList[$y]->RtbBidResponse->RtbBidResponseSeatBidList[$i]->RtbBidResponseBidList[$j]->won_auction = true;
							endif;
		
						endfor;
					
					endfor;
				
				endif;
					
			endfor;
			
		endif;
		
		return $result;
	
	}
}
