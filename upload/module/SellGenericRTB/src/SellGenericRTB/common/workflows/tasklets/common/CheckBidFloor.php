<?php
/**
 * CDNPAL NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2015 CDNPAL Ltd. All Rights Reserved
 * @license GPLv3
 */

namespace sellrtb\workflows\tasklets\common;

class CheckBidFloor {
	
	public static function execute(&$Logger, &$Workflow, \sellrtb\workflows\tasklets\popo\AuctionPopo &$AuctionPopo) {
	
		$result = false;
		
		$RTBPingerList = $AuctionPopo->SelectedPingerList;
		
		$AuctionPopo->SelectedPingerList = array();
		
		foreach ($RTBPingerList as $method_outer_key => $RTBPinger):
		
			$bids_remain = false;
		
			foreach ($RTBPingerList[$method_outer_key]->RtbBidResponse->RtbBidResponseSeatBidList as $outer_key => $RtbBidResponseSeatBid):
			
				foreach ($RTBPingerList[$method_outer_key]->RtbBidResponse->RtbBidResponseSeatBidList[$outer_key]->RtbBidResponseBidList as $key => $RtbBidResponseBid):
			
					$is_over_floor = self::isOverBidFloor($Logger, $AuctionPopo, $RTBPingerList[$method_outer_key]->RtbBidResponse->RtbBidResponseSeatBidList[$outer_key]->RtbBidResponseBidList[$key]);
						
					if ($is_over_floor == false):

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
	
	private static function isOverBidFloor(&$Logger, \sellrtb\workflows\tasklets\popo\AuctionPopo &$AuctionPopo, \model\openrtb\RtbBidResponseBid &$RtbBidResponseBid) {
		
		$bid_price 					= floatval($RtbBidResponseBid->price);
		$adusted_bid_amount 		= floatval($RtbBidResponseBid->adusted_bid_amount);
		
		if ($AuctionPopo->FloorPrice > $adusted_bid_amount || $bid_price <= 0):
			return false;
		else:
			return true;
		endif;

	}
	
}

