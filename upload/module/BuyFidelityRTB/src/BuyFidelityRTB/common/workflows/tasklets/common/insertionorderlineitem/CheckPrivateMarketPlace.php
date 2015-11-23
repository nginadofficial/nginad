<?php

namespace buyrtbfidelity\workflows\tasklets\common\insertionorderlineitem;

class CheckPrivateMarketPlace {
	
	public static function execute(&$Logger, &$Workflow, \model\openrtb\RtbBidRequest &$RtbBidRequest, \model\openrtb\RtbBidRequestImp &$RtbBidRequestImp, &$InsertionOrderLineItem) {
	
		/*
		 * Skip this decision for the test user installed
		 * user_login = suckmedia
		 *
		 * Also enable the global exchange PMP selection
		 * bypass if set to true in config/autoload/global.php
		 */
		
		if ($InsertionOrderLineItem->UserID == TEST_USER_DEMAND
			|| $Workflow->config['settings']['pmp_channel_bypass'] === true):
			return true;
		endif;
		
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
