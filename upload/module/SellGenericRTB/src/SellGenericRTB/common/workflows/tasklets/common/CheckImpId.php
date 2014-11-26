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
	
	public static function execute(&$Logger, &$Workflow, &$RTBPingerList, \sellrtb\workflows\tasklets\popo\AuctionPopo &$AuctionPopo) {
	
		$result = false;
		
		$AuctionPopo->SelectedPingerList = array();
		
		for ($y = 0; $y < count($RTBPingerList); $y++):
		
			for ($i = 0; $i < count($RTBPingerList[$y]->RtbBidResponse->RtbBidResponseSeatBidList); $i++):
			
				for ($j = 0; $j < count($RTBPingerList[$y]->RtbBidResponse->RtbBidResponseSeatBidList[$i]->RtbBidResponseBidList); $j++):
			
					$impid_match = self::isSameImpId($Logger, $AuctionPopo, $RTBPingerList[$y]->RtbBidResponse->RtbBidResponseSeatBidList[$i]->RtbBidResponseBidList[$j]);
						
					if ($impid_match == false):
					
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
	
	private static function isSameImpId(&$Logger, \sellrtb\workflows\tasklets\popo\AuctionPopo &$AuctionPopo, \model\openrtb\RtbBidResponseBid &$RtbBidResponseBid) {
		
		return $AuctionPopo->request_impid == $RtbBidResponseBid->impid;

	}
	
}
	