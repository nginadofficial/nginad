<?php
/**
 * CDNPAL NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2015 CDNPAL Ltd. All Rights Reserved
 * @license GPLv3
 */

namespace buyrtb\parsers\openrtb\parselets\common\imp;

class ParseFloor {
	
	public static function execute(&$Logger, \buyrtb\parsers\openrtb\OpenRTBParser &$Parser, \model\openrtb\RtbBidRequestImp &$RtbBidRequestImp, &$ad_impression) {
			
		/*
		 * Get floor price
		 */
	   	if (isset($ad_impression["bidfloor"])):
	    	$RtbBidRequestImp->bidfloor = $ad_impression["bidfloor"];
	  	endif;
	
	}
	
}
