<?php
/**
 * CDNPAL NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2015 CDNPAL Ltd. All Rights Reserved
 * @license GPLv3
 */

namespace model\rtb;

class RtbBidRequest {
	
	/*
	 * RTB V2 API Request Params
	*/
	
	// REQUIRED
	
	// bid
	public $bid_request_id;
	
	public $RtbBidRequestImpList = array();

	public $RtbBidRequestSite;

	// bid // cur
	
	public $bid_request_cur;
	
	// does not exist in openRTB. Here for compatability with proprietary RTB
	public $bid_request_refurl = "";
	public $bid_request_secure;

	public $RtbBidRequestDevice;
	
	public $RtbBidRequestRegs;
	
}
