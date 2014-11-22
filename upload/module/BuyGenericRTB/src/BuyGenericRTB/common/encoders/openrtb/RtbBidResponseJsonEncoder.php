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
		
		$bid_response["id"] 		= $RtbBidResponse->id;

		$bid_response["seatbid"] 	= self::getRtbBidResponseSeatBidList($RtbBidResponse);
		
		self::setArrayParam($RtbBidResponse, $bid_response, 'bidid');
		self::setArrayParam($RtbBidResponse, $bid_response, 'cur');
		self::setArrayParam($RtbBidResponse, $bid_response, 'customdata');
		self::setArrayParam($RtbBidResponse, $bid_response, 'nbr');

		$result = json_encode($bid_response);
	
		return $result;
		
	}	
	
	private static function getRtbBidResponseSeatBidList(\model\openrtb\RtbBidResponse &$RtbBidResponse) {
	
		$seatbid_list = array();
	
		foreach ($RtbBidResponse->RtbBidResponseSeatBidList as $RtbBidResponseSeatBid):

			$seatbid = array();
			$seatbid['bid'] =  self::getRtbBidResponseBidList($RtbBidResponseSeatBid);
			self::setArrayParam($RtbBidResponseSeatBid, $seatbid, 'seat');
			self::setArrayParam($RtbBidResponseSeatBid, $seatbid, 'group');

			$seatbid_list[] = (object)$seatbid;
				
		endforeach;
	
		return $seatbid_list;
	
	}
	
	private static function getRtbBidResponseBidList(\model\openrtb\RtbBidResponseSeatBid &$RtbBidResponseSeatBid) {
	
		$bid_list = array();

		foreach ($RtbBidResponseSeatBid->RtbBidResponseBidList as $RtbBidResponseBid):
	
			$bid = array();
			$bid['id'] 				= $RtbBidResponseBid->id;
			$bid['impid'] 			= $RtbBidResponseBid->impid;
			$bid['price'] 			= $RtbBidResponseBid->price;
			
			self::setArrayParam($RtbBidResponseBid, $bid, 'adid');
			self::setArrayParam($RtbBidResponseBid, $bid, 'nurl');
			self::setArrayParam($RtbBidResponseBid, $bid, 'adm');
			self::setArrayParam($RtbBidResponseBid, $bid, 'adomain');
			self::setArrayParam($RtbBidResponseBid, $bid, 'lurl');
			self::setArrayParam($RtbBidResponseBid, $bid, 'cid');
			self::setArrayParam($RtbBidResponseBid, $bid, 'crid');
			self::setArrayParam($RtbBidResponseBid, $bid, 'attr');
			self::setArrayParam($RtbBidResponseBid, $bid, 'dealid');
			self::setArrayParam($RtbBidResponseBid, $bid, 'h');
			self::setArrayParam($RtbBidResponseBid, $bid, 'w');
		
			$bid_list[] = (object)$bid;
		
		endforeach;
	
		return $bid_list;
	
	}
	
	private static function setArrayParam(&$obj, &$arr, $name) {
		if (!empty($obj->$name) ||
		(isset($obj->$name) && is_numeric($obj->$name))):
		$arr[$name] = $obj->$name;
		endif;
	}
	
}
