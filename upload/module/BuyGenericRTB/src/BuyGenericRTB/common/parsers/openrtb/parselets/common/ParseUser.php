<?php
/**
 * CDNPAL NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2015 CDNPAL Ltd. All Rights Reserved
 * @license GPLv3
 */

namespace buyrtb\parsers\openrtb\parselets\common;

class ParseUser {
	
	public static function execute(&$Logger, \buyrtb\parsers\openrtb\OpenRTBParser &$Parser, \model\openrtb\RtbBidRequest &$RtbBidRequest, \model\openrtb\RtbBidRequestUser &$RtbBidRequestUser, &$rtb_user) {
	
	        if (isset($rtb_user["id"])):
	        	$RtbBidRequestUser->id 		= $rtb_user["id"];
	        endif;

	}
	
}
