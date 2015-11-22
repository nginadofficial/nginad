<?php
/**
 * NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2016 NginAd Foundation. All Rights Reserved
 * @license GPLv3
 */

namespace model\openrtb\native\request;

class RtbBidRequestNative {

	public $ver = 1;
	
	public $layout;
	
	public $adunit;
	
	public $plcmtcnt = 1;
	
	public $seq = 0;
	
	public $RtbBidRequestAssetList = array();
	
	public $ext;
	
}
