<?php

namespace buyrtb\workflows\tasklets\common\insertionorderlineitem;

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
		 * A. First check to see if this is a private auction 
		 * 		from another exchange. If so lets ignore it since
		 * 		we don't process private auctions from remote SSPs.
		 * 
		 * B. Next check to see if this is a local request from a 
		 * 		publisher on this NginAd install since we are only taking 
		 * 		PMP requests from:
		 * 
		 * 1. Domain Admins who are running a private exchange, in which
		 * 		case the private exchange ad tag loads generate OpenRTB requests
		 * 		internally which are transmitted through PMP
		 * 
		 * 2. Domain Admins who are running a private exchange but who
		 * 		are bidding on other Domain Admins' publisher inventory
		 * 		through the NginAd install's Platform Connection.
		 * 
		 * NOTE: As of NginAd 1.6 InsertionOrderLineItems which are filled
		 * 			by local inventory from publishers on private exchanges
		 * 			or signed up via the home page MUST be filled via PMP
		 */
		
		if (
			($RtbBidRequest->is_local_request === true && (empty($RtbBidRequestImp->RtbBidRequestPmp->private_auction) || $RtbBidRequestImp->RtbBidRequestPmp->private_auction == 0))
			||
			($RtbBidRequest->is_local_request === false && !empty($RtbBidRequestImp->RtbBidRequestPmp->private_auction) && $RtbBidRequestImp->RtbBidRequestPmp->private_auction == 1)
					):
			return false;
		elseif ($RtbBidRequest->is_local_request === true && !empty($RtbBidRequestImp->RtbBidRequestPmp->private_auction) && $RtbBidRequestImp->RtbBidRequestPmp->private_auction == 1):
			
			if (!count($RtbBidRequestImp->RtbBidRequestPmp->RtbBidRequestDirectDealsList)):
				return false;
			endif;
			
			foreach($RtbBidRequestImp->RtbBidRequestPmp->RtbBidRequestDirectDealsList as $RtbBidRequestDirectDeals):
				/*
				 * Here "seats" are mapped to InsertionOrderLineItemIDs
				 */
				foreach ($RtbBidRequestDirectDeals->wseat as $seat_id):
				
					if ($InsertionOrderLineItem->InsertionOrderLineItemID == $seat_id):
						/*
						 * If bidfloor is set, set it in the imp object
						 * and it will be checked later on in the workflow
						 * in the CheckPriceFloor tasklet as if it was part 
						 * of the imp object
						 */
						if (isset($RtbBidRequestDirectDeals->bidfloor)):
							$RtbBidRequestImp->bidfloor = $RtbBidRequestDirectDeals->bidfloor;
						endif;
						return true;
					endif;
				endforeach;
			endforeach;
			
			return false;
		endif;

		return true;
	}
	
}
