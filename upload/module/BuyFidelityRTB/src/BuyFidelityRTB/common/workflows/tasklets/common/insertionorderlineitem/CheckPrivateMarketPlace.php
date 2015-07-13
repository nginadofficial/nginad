<?php

namespace buyrtbfidelity\workflows\tasklets\common\insertionorderlineitem;

class CheckPrivateMarketPlace {
	
	public static function execute(&$Logger, &$Workflow, \model\openrtb\RtbBidRequest &$RtbBidRequest, \model\openrtb\RtbBidRequestImp &$RtbBidRequestImp, &$InsertionOrderLineItem) {
	
		/*
		 * A. Check to see if this is a private auction 
		 * 		from another exchange. If so lets ignore it since
		 * 		we don't process private auctions from remote SSPs.
		 */
		
		if (
			!empty($RtbBidRequestImp->RtbBidRequestPmp->private_auction) && $RtbBidRequestImp->RtbBidRequestPmp->private_auction == 1
					):
			return false;
		endif;

		return true;
	}
	
}
