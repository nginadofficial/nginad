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
		
		$bid_uid = 0;
		
		for ($y = 0; $y < count($RTBPingerList); $y++):
			
			$RTBPingerList[$y]->uid = ++$uid;
		
			if ($RTBPingerList[$y]->ping_success == true):
				
				$json_response_data = $RTBPingerList[$y]->ping_response;
				
				try {
				
					$OpenRTBParser = new \sellrtb\parsers\openrtb\OpenRTBParser();
					$RTBPingerList[$y]->RtbBidResponse = $OpenRTBParser->parse_request($json_response_data);

					self::addBidUids($RTBPingerList, $bid_uid);
										
					$AuctionPopo->PingerList[] = $RTBPingerList[$y];
					
				} catch (Exception $e) {
				
					$RTBPingerList[$y]->total_bids				= 0;
					$RTBPingerList[$y]->won_bids				= 0;
					$RTBPingerList[$y]->lost_bids				= 0;
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

		return $result;
	
	}
	
	private static function addBidUids(&$RTBPingerList, $uid) {
		
		for ($y = 0; $y < count($RTBPingerList); $y++):
			
			$RTBPingerList[$y]->total_bids = 0;
		
			if (isset($RTBPingerList[$y]->RtbBidResponse->RtbBidResponseSeatBidList)):
		
				for ($i = 0; $i < count($RTBPingerList[$y]->RtbBidResponse->RtbBidResponseSeatBidList); $i++):
				
					$RTBPingerList[$y]->total_bids = count($RTBPingerList[$y]->RtbBidResponse->RtbBidResponseSeatBidList[$i]->RtbBidResponseBidList);
						
					if (isset($RTBPingerList[$y]->RtbBidResponse->RtbBidResponseSeatBidList[$i]->RtbBidResponseBidList)):
				
						for ($j = 0; $j < count($RTBPingerList[$y]->RtbBidResponse->RtbBidResponseSeatBidList[$i]->RtbBidResponseBidList); $j++):
							
							$RTBPingerList[$y]->RtbBidResponse->RtbBidResponseSeatBidList[$i]->RtbBidResponseBidList[$j]->uid = ++$uid;
							
						endfor;
					
					endif;
					
				endfor;
				
			endif;
		
		endfor;
	}
	
}
