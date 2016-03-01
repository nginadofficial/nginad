<?php
/**
 * NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2016 NginAd Foundation. All Rights Reserved
 * @license GPLv3
 */

namespace buyrtbfidelity\encoders\openrtb;


class RtbBidResponseJsonEncoder {
	
	public static function execute(\model\openrtb\RtbBidResponse &$RtbBidResponse) {
		
		$bid_response = array();
		
		\util\ParseHelper::setArrayParam($RtbBidResponse, $bid_response, 'id', 'string');
		
		$bid_response["seatbid"] 	= self::getRtbBidResponseSeatBidList($RtbBidResponse);
		
		\util\ParseHelper::setArrayParam($RtbBidResponse, $bid_response, 'bidid', 'string');
		\util\ParseHelper::setArrayParam($RtbBidResponse, $bid_response, 'cur', 'string');
		\util\ParseHelper::setArrayParam($RtbBidResponse, $bid_response, 'customdata', 'string');
		\util\ParseHelper::setArrayParam($RtbBidResponse, $bid_response, 'nbr', 'integer');

		if (isset($bid_response['nbr'])):
			unset($bid_response["seatbid"]);
		endif;
		
		$result = json_encode($bid_response);
	
		return $result;
		
	}	
	
	private static function getRtbBidResponseSeatBidList(\model\openrtb\RtbBidResponse &$RtbBidResponse) {
	
		$seatbid_list = array();
		
		foreach ($RtbBidResponse->RtbBidResponseSeatBidList as $RtbBidResponseSeatBid):

			$seatbid = array();
			$seatbid['bid'] =  self::getRtbBidResponseBidList($RtbBidResponseSeatBid);
			\util\ParseHelper::setArrayParam($RtbBidResponseSeatBid, $seatbid, 'seat', 'string');
			\util\ParseHelper::setArrayParam($RtbBidResponseSeatBid, $seatbid, 'group', 'integer');

			$seatbid_list[] = (object)$seatbid;
				
		endforeach;
	
		return $seatbid_list;
	
	}
	
	private static function getRtbBidResponseBidList(\model\openrtb\RtbBidResponseSeatBid &$RtbBidResponseSeatBid) {
	
		$bid_list = array();

		foreach ($RtbBidResponseSeatBid->RtbBidResponseBidList as $RtbBidResponseBid):
	
			$bid = array();

			\util\ParseHelper::setArrayParam($RtbBidResponseBid, $bid, 'id', 'string');
			\util\ParseHelper::setArrayParam($RtbBidResponseBid, $bid, 'impid', 'string');
			\util\ParseHelper::setArrayParam($RtbBidResponseBid, $bid, 'price', 'float');
			\util\ParseHelper::setArrayParam($RtbBidResponseBid, $bid, 'adid', 'string');
			\util\ParseHelper::setArrayParam($RtbBidResponseBid, $bid, 'nurl', 'string');
			\util\ParseHelper::setArrayParam($RtbBidResponseBid, $bid, 'adm', 'string');
			\util\ParseHelper::setArrayParam($RtbBidResponseBid, $bid, 'adomain', 'array');
			\util\ParseHelper::setArrayParam($RtbBidResponseBid, $bid, 'iurl', 'string');
			\util\ParseHelper::setArrayParam($RtbBidResponseBid, $bid, 'cid', 'string');
			\util\ParseHelper::setArrayParam($RtbBidResponseBid, $bid, 'crid', 'string');
			\util\ParseHelper::setArrayParam($RtbBidResponseBid, $bid, 'attr', 'array');
			\util\ParseHelper::setArrayParam($RtbBidResponseBid, $bid, 'dealid', 'string');
			\util\ParseHelper::setArrayParam($RtbBidResponseBid, $bid, 'h', 'integer');
			\util\ParseHelper::setArrayParam($RtbBidResponseBid, $bid, 'w', 'integer');
		
			$bid_list[] = (object)$bid;
		
		endforeach;
	
		return $bid_list;
	
	}
	

	
}
