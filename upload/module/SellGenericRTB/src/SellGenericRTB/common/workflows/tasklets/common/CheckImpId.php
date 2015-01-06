<?php
/**
 * CDNPAL NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2015 CDNPAL Ltd. All Rights Reserved
 * @license GPLv3
 */

namespace sellrtb\workflows\tasklets\common;

class CheckImpId {
	
	/*
	 * Make sure all responses match the impid that we sent in the request
	 */
	
	public static function execute(&$Logger, &$Workflow, \sellrtb\workflows\tasklets\popo\AuctionPopo &$AuctionPopo) {
	
		$result = false;
		
		$AuctionPopo->SelectedPingerList = array();
		
		foreach ($AuctionPopo->PingerList as $method_outer_key => $RTBPinger):
		
			$bids_remain = false;

			foreach ($AuctionPopo->PingerList[$method_outer_key]->RtbBidResponse->RtbBidResponseSeatBidList as $outer_key => $RtbBidResponseSeatBid):
			
				foreach ($AuctionPopo->PingerList[$method_outer_key]->RtbBidResponse->RtbBidResponseSeatBidList[$outer_key]->RtbBidResponseBidList as $key => $RtbBidResponseBid):
			
					$impid_match = self::isSameImpId($Logger, $AuctionPopo, $AuctionPopo->PingerList[$method_outer_key]->RtbBidResponse->RtbBidResponseSeatBidList[$outer_key]->RtbBidResponseBidList[$key]);
						
					if ($impid_match == false):
					
						unset($AuctionPopo->PingerList[$method_outer_key]->RtbBidResponse->RtbBidResponseSeatBidList[$outer_key]->RtbBidResponseBidList[$key]);
						
					endif;

				endforeach;
		
				if (count($AuctionPopo->PingerList[$method_outer_key]->RtbBidResponse->RtbBidResponseSeatBidList[$outer_key]->RtbBidResponseBidList)):
				
					$bids_remain = true;
				
				else: 
				
					unset($AuctionPopo->PingerList[$method_outer_key]->RtbBidResponse->RtbBidResponseSeatBidList[$outer_key]);
				
				endif;
				
			endforeach;

			if ($bids_remain === true):
				
				/*
				 * Those RTBPingers that still have at least 1 bid get added
				* to the selected pingers list in the POPO
				*/
				$AuctionPopo->SelectedPingerList[] = $AuctionPopo->PingerList[$method_outer_key];
				
				$result = true;
					
			endif;
			
		endforeach;
		
		return $result;
		
	}
	
	private static function isSameImpId(&$Logger, \sellrtb\workflows\tasklets\popo\AuctionPopo &$AuctionPopo, \model\openrtb\RtbBidResponseBid &$RtbBidResponseBid) {
		
		return $AuctionPopo->request_impid == $RtbBidResponseBid->impid;

	}
	
}
	