<?php
/**
 * CDNPAL NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2015 CDNPAL Ltd. All Rights Reserved
 * @license GPLv3
 */

namespace model\openrtb;

class RtbBidRequest {
	
	/*
	 * RTB V2 API Request Params
	*/
	
	// REQUIRED
	
	// bid
	public $id;
	
	public $RtbBidRequestImpList = array();

	public $RtbBidRequestSite;

	// bid // cur
	
	public $cur;
	
	// does not exist in openRTB. Here for compatability with proprietary RTB
	public $refurl = "";
	public $secure;

	public $RtbBidRequestDevice;
	
	public $RtbBidRequestRegs;
	
	// used only by NginAd internally
	public $is_local_request;
}
