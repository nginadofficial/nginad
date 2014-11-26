<?php
/**
 * CDNPAL NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2015 CDNPAL Ltd. All Rights Reserved
 * @license GPLv3
 */

namespace sellrtb\workflows\tasklets\common;
use \Exception;

class AddBids {
	
	public static function execute(&$Logger, &$Workflow, &$RTBPingerList, \sellrtb\workflows\tasklets\popo\AuctionPopo &$AuctionPopo) {
	
		$result = false;
		
		$uid = 0;
		
		for ($y = 0; $y < count($RTBPingerList); $y++):
			
			$RTBPingerList[$y]->uid = ++$uid;
		
			if ($RTBPingerList[$y]->ping_success == true):
				
				$json_response_data = $RTBPingerList[$y]->ping_response;
				
				try {
				
					$OpenRTBParser = new \sellrtb\parsers\openrtb\OpenRTBParser();
					$RTBPingerList[$y]->RtbBidResponse = $OpenRTBParser->parse_request($json_response_data);
						
					$AuctionPopo->PingerList[] = $RTBPingerList[$y];
					
				} catch (Exception $e) {
				
					$RTBPingerList[$y]->ping_success 			= false;
					$RTBPingerList[$y]->ping_error_message 		= "OpenRTB Ping Response Base Validation Error: " . $e->getMessage() 
														. " Partner Name: " . $RTBPingerList[$y]->partner_name . " Partner ID: " 
														. $RTBPingerList[$y]->partner_id;
					
					continue;
						
				}

				$result = true;
			else:
			
				$Logger->log[] = $RTBPingerList[$y]->ping_error_message;

			endif;
			
		endfor;
		
		// add uids for bids
		
		$uid = 0;
		
		for ($y = 0; $y < count($AuctionPopo->PingerList); $y++):
		
			for ($i = 0; $i < count($AuctionPopo->PingerList[$y]->RtbBidResponse->RtbBidResponseSeatBidList); $i++):
				
				$AuctionPopo->PingerList[$y]->total_bids = count($AuctionPopo->PingerList[$y]->RtbBidResponse->RtbBidResponseSeatBidList[$i]->RtbBidResponseBidList);
			
				for ($j = 0; $j < count($AuctionPopo->PingerList[$y]->RtbBidResponse->RtbBidResponseSeatBidList[$i]->RtbBidResponseBidList); $j++):
					
					$AuctionPopo->PingerList[$y]->RtbBidResponse->RtbBidResponseSeatBidList[$i]->RtbBidResponseBidList[$j]->uid = ++$uid;
					
				endfor;
			
			endfor;
		
		endfor;

		return $result;
	
	}
	
}
