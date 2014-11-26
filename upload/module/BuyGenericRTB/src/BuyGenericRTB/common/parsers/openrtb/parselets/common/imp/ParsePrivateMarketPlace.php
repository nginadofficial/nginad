<?php
/**
 * CDNPAL NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2015 CDNPAL Ltd. All Rights Reserved
 * @license GPLv3
 */

namespace buyrtb\parsers\openrtb\parselets\common\imp;

class ParsePrivateMarketPlace {
	
	public static function execute(&$Logger, \buyrtb\parsers\openrtb\OpenRTBParser &$Parser, \model\openrtb\RtbBidRequestImp &$RtbBidRequestImp, &$ad_impression) {
		
		$RtbBidRequestPmp = new \model\openrtb\RtbBidRequestPmp();
		
		// default
		$RtbBidRequestPmp->private_auction = 0;
		
		/*
		 * Get impression id
		 */
        if (isset($ad_impression["pmp"]) && isset($ad_impression["pmp"]["deals"])):
        
        	$RtbBidRequestPmp->private_auction = 1;
        
        	foreach ($ad_impression["pmp"]["deals"] as $deal):
        	
        		if (!isset($deal["id"])):
        			continue;
        		endif;
        		
        		$RtbBidRequestDirectDeals = new \model\openrtb\RtbBidRequestDirectDeals();
        	
        		$RtbBidRequestDirectDeals->id = $deal["id"];
        	
        		$Parser->parse_item(
        				$RtbBidRequestDirectDeals,
        				$deal,
        				"bidfloor");
        		
        		// second price ?
        		
        		$Parser->parse_item(
        				$RtbBidRequestDirectDeals,
        				$deal,
        				"at");
        		
        		$RtbBidRequestPmp->RtbBidRequestDirectDealsList[] = $RtbBidRequestDirectDeals;
        		
        	endforeach;
        	
        endif;
        
        $RtbBidRequestImp->RtbBidRequestPmp = $RtbBidRequestPmp;
	}
}
