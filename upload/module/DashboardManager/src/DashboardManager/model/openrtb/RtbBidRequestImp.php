<?php
/**
 * CDNPAL NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2015 CDNPAL Ltd. All Rights Reserved
 * @license GPLv3
 */

namespace model\openrtb;

class RtbBidRequestImp {
	

	// bid // imp
	public $id;
	
	public $RtbBidRequestBanner;
	
	public $RtbBidRequestVideo;
	
	public $displaymanager;
	
	public $displaymanagerver;
	
	public $instl = 0;
	
	public $tagid;
	
	// floor price
	public $bidfloor = 0;
	
	// floor price currency
	public $bidfloorcur = "USD";
	
	public $secure;
	
	public $iframebuster;
	
	public $RtbBidRequestPmp;
	
	public $ext;
	
	/*
	 * Not used by OpenRTB
	 */

	public $media_type;
	
}
