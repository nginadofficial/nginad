<?php
/**
 * CDNPAL NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2015 CDNPAL Ltd. All Rights Reserved
 * @license GPLv3
 */

namespace buyrtb\parsers\openrtb\parselets\common\imp;
use \Exception;

class ParseImpId {
	
	public static function execute(&$Logger, \buyrtb\parsers\openrtb\OpenRTBParser &$Parser, \model\openrtb\RtbBidRequestImp &$RtbBidRequestImp, &$ad_impression) {
		
		/*
		 * Get impression id
		 */
	   	if (isset($ad_impression["id"])):
	    	$RtbBidRequestImp->bid_request_imp_id = $ad_impression["id"];
	 	else:
	     	throw new Exception($Parser->expeption_missing_min_bid_request_params . ": imp_id");
	  	endif;
		
	}
}
