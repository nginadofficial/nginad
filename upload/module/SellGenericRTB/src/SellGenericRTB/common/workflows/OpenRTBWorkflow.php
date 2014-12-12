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
    	$WinningRtbResponseBid 					= $WinningRTBPinger->RtbBidResponse->RtbBidResponseSeatBidList[0]->RtbBidResponseBidList[0];
    	 
    	$bid_price 								= floatval($WinningRtbResponseBid->price);
    	
    	$WinningRTBPinger->won_auction 			= true;
    	$WinningRTBPinger->winning_bid 			= $bid_price;
    	if ($AuctionPopo->ImpressionType == 'video' && empty($WinningRtbResponseBid->adm)
    			&& !empty($WinningRtbResponseBid->nurl)):
    			
    		if ($WinningRTBPinger->is_loopback_pinger):
    			
    			/*
	    		 * This is a VAST video ad zone auction and it was won
	    		 * by a local bidder and only has the nurl with no adm
	    		 * Grab the VAST XML from the database or the cache
    			 */
	    		if (preg_match("/zoneid=(\\d+)/", $WinningRtbResponseBid->nurl, $matches) && isset($matches[1])):
	    			
	    			$ad_campaign_banner_id 			= $matches[1];
	    		
	    			$AdCampaignBannerFactory = \_factory\AdCampaignBanner::get_instance();
	    			
		    		$params = array();
		    		$params["AdCampaignBannerID"] 	= $ad_campaign_banner_id;
	    			$AdCampaignBanner = $AdCampaignBannerFactory->get_row_cached($params);
	    			
	    			$AuctionPopo->winning_ad_tag	= $AdCampaignBanner->AdTag;
	    		endif;
	    		
    		else:
    		
	    		/*
	    		 * This is a VAST video ad zone auction and it was won
	    		 * by a bidder that put a URL to the VAST XML in their
	    		 * OpenRTB 2.2 nurl param so adm is empty.
	    		 * 
	    		 * We must now reverse proxy the VAST XML back to the 
	    		 * publisher's Flash Video Player in the HTTP
	    		 * Ad Tag response.
	    		 * 
	    		 * Could be a LiveRail Tag or something.
	    		 */
	    		
	    		$reverse_proxy_vast_xml 			= \util\WorkflowHelper::get_ping_notice_url_curl_request($WinningRtbResponseBid->nurl);

	    		$AuctionPopo->winning_ad_tag		= $reverse_proxy_vast_xml;
    		endif;
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
    	$AuctionPopo->winning_seat				= $WinningRTBPinger->RtbBidResponse->RtbBidResponseSeatBidList[0]->seat;
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
	    	
	    	if (preg_match("/zoneid=(\\d+)/", $AuctionPopo->winning_ad_tag, $matches) && isset($matches[1])):
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