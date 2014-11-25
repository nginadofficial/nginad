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
	
	public static function execute(&$Logger, &$Workflow, &$RTBPingerList, \sellrtb\workflows\tasklets\popo\AuctionPopo &$AuctionPopo) {
	
		$result = false;
		
		$AuctionPopo->SelectedPingerList = array();
		
		for ($y = 0; $y < count($RTBPingerList); $y++):
		
			for ($i = 0; $i < count($RTBPingerList[$y]->RtbBidResponse->RtbBidResponseSeatBidList); $i++):
			
				for ($j = 0; $j < count($RTBPingerList[$y]->RtbBidResponse->RtbBidResponseSeatBidList[$i]->RtbBidResponseBidList); $j++):
			
					$is_over_floor = self::isOverBidFloor($Logger, $AuctionPopo, $RTBPingerList[$y]->RtbBidResponse->RtbBidResponseSeatBidList[$i]->RtbBidResponseBidList[$j]);
						
					if ($is_over_floor == false):
					
						unset($RTBPingerList[$y]->RtbBidResponse->RtbBidResponseSeatBidList[$i]->RtbBidResponseBidList[$j]);
						
					endif;

				endfor;
		
				if (count($RTBPingerList[$y]->RtbBidResponse->RtbBidResponseSeatBidList) 
					&& count($RTBPingerList[$y]->RtbBidResponse->RtbBidResponseSeatBidList[0]->RtbBidResponseBidList)):
				
					/*
					 * Those RTBPingers that still have at least 1 bid get added
					* to the selected pingers list in the POPO
					*/
					$AuctionPopo->SelectedPingerList[] = $RTBPingerList[$y];
						
					$result = true;
					
				endif;
				
			endfor;

		endfor;
		
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

