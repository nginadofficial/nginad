<?php
/**
 * CDNPAL NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2015 CDNPAL Ltd. All Rights Reserved
 * @license GPLv3
 */

namespace buyrtb\encoders\openrtb;


class RtbBidResponseJsonEncoder {
	
	public static function execute(\model\openrtb\RtbBidResponse &$RtbBidResponse) {
		
		$bid_response = array();
		
		\util\ParseHelper::setArrayParam($RtbBidResponse, $bid_response, 'id');
		
		$bid_response["seatbid"] 	= self::getRtbBidResponseSeatBidList($RtbBidResponse);
		
		\util\ParseHelper::setArrayParam($RtbBidResponse, $bid_response, 'bidid');
		\util\ParseHelper::setArrayParam($RtbBidResponse, $bid_response, 'cur');
		\util\ParseHelper::setArrayParam($RtbBidResponse, $bid_response, 'customdata');
		\util\ParseHelper::setArrayParam($RtbBidResponse, $bid_response, 'nbr');

		$result = json_encode($bid_response);
	
		return $result;
		
	}	
	
	private static function getRtbBidResponseSeatBidList(\model\openrtb\RtbBidResponse &$RtbBidResponse) {
	
		$seatbid_list = array();
		
		foreach ($RtbBidResponse->RtbBidResponseSeatBidList as $RtbBidResponseSeatBid):

			$seatbid = array();
			$seatbid['bid'] =  self::getRtbBidResponseBidList($RtbBidResponseSeatBid);
			\util\ParseHelper::setArrayParam($RtbBidResponseSeatBid, $seatbid, 'seat');
			\util\ParseHelper::setArrayParam($RtbBidResponseSeatBid, $seatbid, 'group');

			$seatbid_list[] = (object)$seatbid;
				
		endforeach;
	
		return $seatbid_list;
	
	}
	
	private static function getRtbBidResponseBidList(\model\openrtb\RtbBidResponseSeatBid &$RtbBidResponseSeatBid) {
	
		$bid_list = array();

		foreach ($RtbBidResponseSeatBid->RtbBidResponseBidList as $RtbBidResponseBid):
	
			$bid = array();

			\util\ParseHelper::setArrayParam($RtbBidResponseBid, $bid, 'id');
			\util\ParseHelper::setArrayParam($RtbBidResponseBid, $bid, 'impid');
			\util\ParseHelper::setArrayParam($RtbBidResponseBid, $bid, 'price');
			\util\ParseHelper::setArrayParam($RtbBidResponseBid, $bid, 'adid');
			\util\ParseHelper::setArrayParam($RtbBidResponseBid, $bid, 'nurl');
			\util\ParseHelper::setArrayParam($RtbBidResponseBid, $bid, 'adm');
			\util\ParseHelper::setArrayParam($RtbBidResponseBid, $bid, 'adomain');
			\util\ParseHelper::setArrayParam($RtbBidResponseBid, $bid, 'lurl');
			\util\ParseHelper::setArrayParam($RtbBidResponseBid, $bid, 'cid');
			\util\ParseHelper::setArrayParam($RtbBidResponseBid, $bid, 'crid');
			\util\ParseHelper::setArrayParam($RtbBidResponseBid, $bid, 'attr');
			\util\ParseHelper::setArrayParam($RtbBidResponseBid, $bid, 'dealid');
			\util\ParseHelper::setArrayParam($RtbBidResponseBid, $bid, 'h');
			\util\ParseHelper::setArrayParam($RtbBidResponseBid, $bid, 'w');
		
			$bid_list[] = (object)$bid;
		
		endforeach;
	
		return $bid_list;
	
	}
	

	
}
