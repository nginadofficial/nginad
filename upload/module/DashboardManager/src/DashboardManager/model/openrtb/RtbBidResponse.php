<?php
/**
 * CDNPAL NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2015 CDNPAL Ltd. All Rights Reserved
 * @license GPLv3
 */

namespace model\openrtb;

class RtbBidResponse {
	

	/*
	 * RTB V2 API Response Params
	*/
	
	// REQUIRED
	
	public $bid_response_id;
	public $bid_response_bid;
	public $bid_response_buyer;
	public $bid_response_creativeId;
	public $bid_response_landingPageURL;
	public $bid_response_landingPageTLD;
	public $bid_response_requestId;
	
	// NOT REQUIRED
	
	public $bid_response_ebid;
	public $bid_response_bidCurrency;
	public $bid_response_creativeJSURL;
	public $bid_response_creativeHTMLURL;
	public $bid_response_creativeTAG;
	public $bid_response_creativeAttribute;
	
	public $user_ip_hash;
	
}

