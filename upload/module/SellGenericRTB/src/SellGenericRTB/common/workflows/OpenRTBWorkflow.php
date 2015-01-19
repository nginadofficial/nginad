<?php
/**
 * CDNPAL NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2015 CDNPAL Ltd. All Rights Reserved
 * @license GPLv3
 */

namespace sellrtb\workflows;

class OpenRTBWorkflow {

	public $config;
	
    public function process_business_rules_workflow($logger, $config, &$RTBPingerList, \sellrtb\workflows\tasklets\popo\AuctionPopo &$AuctionPopo) {
	
    	$this->config = $config;
    	
    	$AuctionPopo->auction_was_won = false;
    	
    	// Add Bids to POPO
    	
    	if (\sellrtb\workflows\tasklets\common\AddBids::execute($logger, $this, $RTBPingerList, $AuctionPopo) === false):
    		return false;
    	endif;
    	
    	// Make sure that each bid response matches the impression id of the RTB request
    	
    	if (\sellrtb\workflows\tasklets\common\CheckImpId::execute($logger, $this, $AuctionPopo) === false):
    		return false;
    	endif;
    	
    	// Adjust the bid amounts with the correct exchange markup
    	 
    	if (\sellrtb\workflows\tasklets\common\AdjustBidAmountWithMarkup::execute($logger, $this, $AuctionPopo) === false):
    		return false;
    	endif;
    	
    	// Exclude bids that don't meet the floor
    	
    	if (\sellrtb\workflows\tasklets\common\CheckBidFloor::execute($logger, $this, $AuctionPopo) === false):
    		return false;
    	endif;
    	
    	// Log and sort bid prices and adjusted bid prices in the case of a second price auction type
    	 
    	if (\sellrtb\workflows\tasklets\common\LogBidPrices::execute($logger, $this, $AuctionPopo) === false):
    		return false;
    	endif;    	
    	
    	// Exclude bids that aren't the highest or tied for first place
    	 
    	if (\sellrtb\workflows\tasklets\common\CheckHighBid::execute($logger, $this, $AuctionPopo) === false):
    		return false;
    	endif;
    	
    	// Exclude partners who's scores aren't the highest or tied for first place
    	
    	if (\sellrtb\workflows\tasklets\common\CheckPartnerScore::execute($logger, $this, $AuctionPopo) === false):
    		return false;
    	endif;
    	
    	// Pick a winner from the remaining list of bids
    	
    	if (\sellrtb\workflows\tasklets\common\PickAWinner::execute($logger, $this, $RTBPingerList, $AuctionPopo) === false):
    		return false;
    	endif;
    	
    	// if we got here the auction was won
    	
    	$AuctionPopo->auction_was_won = true;
    	
    	$WinningRTBPinger 						= $AuctionPopo->SelectedPingerList[0];
    	$seat_bid_list_key 						= \util\WorkflowHelper::get_first_key($WinningRTBPinger->RtbBidResponse->RtbBidResponseSeatBidList);
    	$bid_list_key							= \util\WorkflowHelper::get_first_key($WinningRTBPinger->RtbBidResponse->RtbBidResponseSeatBidList[$seat_bid_list_key]->RtbBidResponseBidList);
    	$WinningRtbResponseBid 					= $WinningRTBPinger->RtbBidResponse->RtbBidResponseSeatBidList[$seat_bid_list_key]->RtbBidResponseBidList[$bid_list_key];
    	 
    	$bid_price 								= floatval($WinningRtbResponseBid->price);
    	
    	$WinningRTBPinger->won_auction 			= true;
    	$WinningRTBPinger->winning_bid 			= $bid_price;
    	if ($AuctionPopo->ImpressionType == 'video' && empty($WinningRtbResponseBid->adm)
    			&& !empty($WinningRtbResponseBid->nurl)):
    			
	    		/*
	    		 * This is a VAST video ad zone auction and it was won
	    		 * by a bidder that put a URL to the VAST XML in their
	    		 * OpenRTB 2.2 nurl param so adm is empty.
	    		 * 
	    		 * Could be a LiveRail Tag or something.
	    		 */
	    		
	    		/*
	    		 * We don't need to rawurlencode() the response here before
	    		 * putting it in the adm field because it never gets used
	    		 * again. We are putting it in the field for conceptual
	    		 * parity reasons only.
	    		 */
    		
	    		$WinningRtbResponseBid->adm 		= $WinningRtbResponseBid->nurl;

	    		$AuctionPopo->winning_ad_tag		= $WinningRtbResponseBid->adm;
	    		
	    		/*
	    		 * now unset the nurl since we already fired off the impression
	    		 * and we don't want to fire it off again later.
	    		 */ 
	    		unset($WinningRtbResponseBid->nurl);
	    		
    	elseif (!empty($WinningRtbResponseBid->adm)):
    	
    		$AuctionPopo->winning_ad_tag		= rawurldecode($WinningRtbResponseBid->adm);
    	
    		if (!empty($WinningRtbResponseBid->nurl)):
					
    			/*
    			 * Was there a notice url here?
    			 * 
    			 * Lets fire it off after we have served the output back to the user.
    			 * 
    			 * Store it in the POPO for now.
    			 */	
    			$AuctionPopo->nurl 				= $WinningRtbResponseBid->nurl;
    			
    		endif;
	    	
    	endif;
    	$AuctionPopo->winning_bid_price			= sprintf("%1.4f", $this->encrypt_bid($bid_price));
    	$AuctionPopo->winning_partner_id		= $WinningRTBPinger->partner_id;
    	$popo_seat_bid_key						= \util\WorkflowHelper::get_first_key($WinningRTBPinger->RtbBidResponse->RtbBidResponseSeatBidList);
    	$AuctionPopo->winning_seat				= $WinningRTBPinger->RtbBidResponse->RtbBidResponseSeatBidList[$popo_seat_bid_key]->seat;
    	/*
    	 * Was this a second price auction?
    	 * If so record the winning bid
    	 */
		if ($AuctionPopo->is_second_price_auction === true):
		
			// second price is only tabulated if there is more than 1 valid bid
			$index = 0;

			if (($AuctionPopo->bid_price_list) > 1):
				$index = 1;
			endif;
			
			$AuctionPopo->second_price_winning_bid_price = $AuctionPopo->bid_price_list[$index];
			
			// second price is only tabulated if there is more than 1 valid bid
			$index = 0;
			
			if (($AuctionPopo->adjusted_bid_price_list) > 1):
				$index = 1;
			endif;
			
			$AuctionPopo->second_price_winning_adjusted_bid_price = $AuctionPopo->adjusted_bid_price_list[$index];
			
		endif;

    	if ($WinningRTBPinger->is_loopback_pinger):
    	
    		if ($AuctionPopo->ImpressionType == 'video' && empty($WinningRtbResponseBid->adm)
    			&& !empty($WinningRtbResponseBid->nurl)):
    			$ad_tag_to_compare = $WinningRtbResponseBid->nurl;
    		else:
    			$ad_tag_to_compare = $AuctionPopo->winning_ad_tag;
    		endif;
    		
		    if (preg_match("/zoneid=(\\d+)/", $ad_tag_to_compare, $matches) && isset($matches[1])):
		    	$AuctionPopo->loopback_demand_partner_ad_campaign_banner_id 	= $matches[1];
		    	$AuctionPopo->loopback_demand_partner_won 						= true;
		    endif;
    	endif;
    	
    	return $WinningRTBPinger;
    	
    }

    private function encrypt_bid($unencrypted_bid_price) {
    
    	return $unencrypted_bid_price;
    }
}