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
		
		foreach ($RTBPingerList as $method_outer_key => $RTBPinger):
			
			$RTBPingerList[$method_outer_key]->uid = ++$uid;
		
			if ($RTBPingerList[$method_outer_key]->ping_success == true):
				
				$json_response_data = $RTBPingerList[$method_outer_key]->ping_response;
				
				try {
				
					$OpenRTBParser = new \sellrtb\parsers\openrtb\OpenRTBParser();
					$RTBPingerList[$method_outer_key]->RtbBidResponse = $OpenRTBParser->parse_request($json_response_data);

					self::addBidUids($RTBPingerList, $bid_uid);
										
					$AuctionPopo->PingerList[] = $RTBPingerList[$method_outer_key];
					
				} catch (Exception $e) {
				
					$RTBPingerList[$method_outer_key]->total_bids				= 0;
					$RTBPingerList[$method_outer_key]->won_bids					= 0;
					$RTBPingerList[$method_outer_key]->lost_bids				= 0;
					$RTBPingerList[$method_outer_key]->ping_success 			= false;
					$RTBPingerList[$method_outer_key]->ping_error_message 		= "OpenRTB Ping Response Base Validation Error: " . $e->getMessage() 
														. " Partner Name: " . $RTBPingerList[$method_outer_key]->partner_name . " Partner ID: " 
														. $RTBPingerList[$method_outer_key]->partner_id;
					
					continue;
						
				}

				$result = true;
			else:
			
				$Logger->log[] = $RTBPingerList[$method_outer_key]->ping_error_message;

			endif;
			
		endforeach;

		return $result;
	
	}
	
	private static function addBidUids(&$RTBPingerList, $uid) {
		
		foreach ($RTBPingerList as $method_outer_key => $RTBPinger):
			
			$RTBPingerList[$method_outer_key]->total_bids = 0;
		
			if (isset($RTBPingerList[$method_outer_key]->RtbBidResponse->RtbBidResponseSeatBidList)):
		
				foreach ($RTBPingerList[$method_outer_key]->RtbBidResponse->RtbBidResponseSeatBidList as $outer_key => $RtbBidResponseSeatBid):
				
					$RTBPingerList[$method_outer_key]->total_bids = count($RTBPingerList[$method_outer_key]->RtbBidResponse->RtbBidResponseSeatBidList[$outer_key]->RtbBidResponseBidList);
						
					if (isset($RTBPingerList[$method_outer_key]->RtbBidResponse->RtbBidResponseSeatBidList[$outer_key]->RtbBidResponseBidList)):
				
						foreach ($RTBPingerList[$method_outer_key]->RtbBidResponse->RtbBidResponseSeatBidList[$outer_key]->RtbBidResponseBidList as $key => $RtbBidResponseBid):
							
							$RTBPingerList[$method_outer_key]->RtbBidResponse->RtbBidResponseSeatBidList[$outer_key]->RtbBidResponseBidList[$key]->uid = ++$uid;
							
						endforeach;
					
					endif;
					
				endforeach;
				
			endif;
		
		endforeach;
	}
	
}
