<?php
/**
 * NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2016 NginAd Foundation. All Rights Reserved
 * @license GPLv3
 */

namespace buyrtbheaderbidding\workflows;

class OpenRTBWorkflow
{
	public $current_time;
	
	// geocity light
	public $geo_info = null;
	public $maxmind = null;
	public $rtb_seat_id = null;
	public $config;
	
    public function process_business_rules_workflow($logger, $config, $rtb_seat_id, &$no_bid_reason, \model\openrtb\RtbBidRequest &$RtbBidRequest) {

    	$this->config = $config;
    	$this->rtb_seat_id = $rtb_seat_id;

    	/*
    	 * NGIAND HEADER BIDDING IMPLEMENTATION DETAILS
    	 * --------------------------------------------------------------------------
    	 * 
    	 * There are 2 ways header bidding could be implemented
    	 * on the bidder side.
    	 * 
    	 * 1. A mock bid where the header RTB request sent out to the Ad Exchange
    	 * 		responds with a mock bid, and in theory that bid should be similar
    	 * 		to the one they get when the actual ad tag is called to the same
    	 * 		ad exchange.
    	 * 
    	 * 2. A token/match system where the header RTB request sent out to the 
    	 * 		Ad Exchange registers a unique identifier for the bid request
    	 * 		and saves the winning ad tag from the exchange keyed to the header bid
    	 * 		unique identifier. When the win notice or ad tag is invoked, the actual
    	 * 		winner's tag from that ad exchange is sent back and displayed.
    	 * 
    	 * In NginAd we will implement #1 since a lot of NginAd users do not have access
    	 * to the big data systems can take to implement solution #2 where hashes for
    	 * every single header bid ad slot would need to be stored in the database
    	 * and propogated across the RTB backend cluster in real time.
    	 * 
    	 * Because we are using #1 and mock bidding we don't need to worry about 
    	 * other NginAd RTB servers behind the load balancer being synchronized 
    	 * against winners' unique ids.
    	 *  
    	 * Since we want to reduce the load put on NginAd by header bidding here we 
    	 * will cache mock bid responses to header bids keyed by the request ids.
    	 * 
    	 * The minutely cache interval will be settable via the application config.
    	 */
    	
    	$cache_params = \util\HeaderBiddingHelper::get_params_from_bid_request($this->config, $RtbBidRequest);
    	
    	if ($cache_params === false):
    		return array();
    	endif;

    	$cached_inventory_response = \util\HeaderBiddingHelper::get_stored_rtb_matching_line_items($this->config, $cache_params);
    	
    	if ($cached_inventory_response !== null):
    		return $cached_inventory_response;
    	endif;    	

    	/*
    	 * The bidding price in the test bid response has to be between 0.01 and 1 (USD),
    	* and as always, it must not be lower than the specified bid floor.
    	*/
    	
        $InsertionOrderLineItem_Match_List = array();
        
    	$InsertionOrderFactory = \_factory\InsertionOrder::get_instance();
    	$params = array();
    	$params["Active"] = 1;
    	
    	$InsertionOrderList = $InsertionOrderFactory->get_cached($this->config, $params);
    	
    	$this->current_time = time();

    	$InsertionOrderLineItemFactory = \_factory\InsertionOrderLineItem::get_instance();
    	$InsertionOrderLineItemDomainExclusionFactory = \_factory\InsertionOrderLineItemDomainExclusion::get_instance();
    	$InsertionOrderLineItemExclusiveInclusionFactory = \_factory\InsertionOrderLineItemDomainExclusiveInclusion::get_instance();
    	$InsertionOrderLineItemRestrictionsFactory = \_factory\InsertionOrderLineItemRestrictions::get_instance();
    	
    	// match ip against country code
    	\buyrtbheaderbidding\workflows\tasklets\common\insertionorder\GetGeoCodeCountry::execute($logger, $this, $RtbBidRequest);

    	foreach ($InsertionOrderList as $InsertionOrder):

	    	// Check campaign date
	    	if (\buyrtbheaderbidding\workflows\tasklets\common\insertionorder\CheckCampaignDate::execute($logger, $this, $RtbBidRequest, $InsertionOrder) === false):
	    		continue;
	    	endif;

        	// Check max spend
	    	if (\buyrtbheaderbidding\workflows\tasklets\common\insertionorder\CheckMaxSpend::execute($logger, $this, $RtbBidRequest, $InsertionOrder) === false):
	    		continue;
	    	endif;

	    	// Check max impressions
	    	if (\buyrtbheaderbidding\workflows\tasklets\common\insertionorder\CheckMaxImpressions::execute($logger, $this, $RtbBidRequest, $InsertionOrder) === false):
	    		continue;
	    	endif;

	    	// get markup rate for ad campaign
        	$markup_rate = \util\Markup::getMarkupRate($InsertionOrder, $this->config);

        	// iterate the active banners for this ad campaign
        	$params = array();
        	$params["InsertionOrderID"] = $InsertionOrder->InsertionOrderID;
        	$params["Active"] = 1;
        	$InsertionOrderLineItemList = $InsertionOrderLineItemFactory->get_cached($this->config, $params);

        	foreach ($RtbBidRequest->RtbBidRequestImpList as $RtbBidRequestImp):
        		
	        	foreach ($InsertionOrderLineItemList as $InsertionOrderLineItem):
			        	
	        		if ($InsertionOrderLineItem->ImpressionType == 'video'
	        				|| (empty($RtbBidRequestImp->RtbBidRequestBanner) && $InsertionOrderLineItem->ImpressionType == 'banner')):
	        			continue;
	        		endif;
	        		
			        /*
			         * check the business rules against the banner
			         */
		        	
	        		// Check PMP
	        		if (\buyrtbheaderbidding\workflows\tasklets\common\insertionorderlineitem\CheckPrivateMarketPlace::execute($logger, $this, $RtbBidRequest, $RtbBidRequestImp, $InsertionOrderLineItem) === false):
	        			continue;
	        		endif;
	        		
	        		// Check Domain Admin SSP Channel Selections
	        		if (\buyrtbheaderbidding\workflows\tasklets\common\insertionorderlineitem\CheckSspChannelSelections::execute($logger, $this, $RtbBidRequest, $RtbBidRequestImp, $InsertionOrderLineItem) === false):
	        			continue;
	        		endif;
	        		
		        	// Check banner date
		        	if (\buyrtbheaderbidding\workflows\tasklets\common\insertionorderlineitem\CheckBannerDate::execute($logger, $this, $RtbBidRequest, $RtbBidRequestImp, $InsertionOrderLineItem) === false):
		        		continue;
		        	endif;
	            	
		        	// Check impression price floor
		        	if (\buyrtbheaderbidding\workflows\tasklets\common\insertionorderlineitem\CheckPriceFloor::execute($logger, $this, $RtbBidRequest, $RtbBidRequestImp, $InsertionOrderLineItem, $markup_rate) === false):
		        		continue;
		        	endif;
		        	
	            	// Check banner domain exclusive inclusions
		        	if (\buyrtbheaderbidding\workflows\tasklets\common\insertionorderlineitem\CheckExclusiveInclusion::execute($logger, $this, $RtbBidRequest, $RtbBidRequestImp, $InsertionOrderLineItem, $InsertionOrderLineItemExclusiveInclusionFactory) === false):
		        		continue;
		        	endif;
	
	            	// Check banner domain exclusions match
		        	if (\buyrtbheaderbidding\workflows\tasklets\common\insertionorderlineitem\CheckDomainExclusion::execute($logger, $this, $RtbBidRequest, $RtbBidRequestImp, $InsertionOrderLineItem, $InsertionOrderLineItemDomainExclusionFactory) === false):
		        		continue;
		        	endif;
		        	
		        	// Display Banner Workflow
		        	$DisplayWorkflow = new \buyrtbheaderbidding\workflows\DisplayWorkflow();
		        	$passed_child_workflow = $DisplayWorkflow->process_business_rules_workflow($logger, $this, $RtbBidRequest, $RtbBidRequestImp, $InsertionOrderLineItem, $InsertionOrderLineItemRestrictionsFactory);
		        	 
					
					if ($passed_child_workflow === false):
						continue;
					endif;
		        	
	            	/*
	            	 * PASSED ALL THE BUSINESS RULES, ADD TO THE RESULTS
	            	 */
	                $InsertionOrderLineItemFactory->incrementInsertionOrderLineItemBidsCounterCached($this->config, $rtb_seat_id, $InsertionOrderLineItem->InsertionOrderLineItemID);
	
	                /*
	                 * Adjust the bid rate according to the markup
	                 */
	
	                $mark_down = floatval($InsertionOrderLineItem->BidAmount) * floatval($markup_rate);
	                $adusted_amount = floatval($InsertionOrderLineItem->BidAmount) - floatval($mark_down);
	
	                $InsertionOrderLineItem->BidAmount = sprintf("%1.4f", $adusted_amount);
	                
	                // default in config
					$currency = $this->config['settings']['rtb']['auction_currency'];
					
					if (isset($RtbBidRequest->cur[0])):
						$currency = $RtbBidRequest->cur[0];
					endif;
	                
	            	$InsertionOrderLineItem_Match_List[(string)$InsertionOrderLineItem->UserID][] = array(
	            											"currency" => $currency,
	            											"impid" => $RtbBidRequestImp->id,
	            											"InsertionOrderLineItem" => $InsertionOrderLineItem);
	
	        	endforeach;
        	
        	endforeach;

    	endforeach;

    	if (count($InsertionOrderLineItem_Match_List)):
    	
	    	// Check Ad Fraud
	    	if (\buyrtbheaderbidding\workflows\tasklets\common\thirdparty\CheckPublisherScore::execute($logger, $this, $RtbBidRequest) === false):
	    		$no_bid_reason = NOBID_BAD_PUBLISHER;
	    		return array();
	    	endif;
	    	 
	    	// Check Publisher Score
	    	if (\buyrtbheaderbidding\workflows\tasklets\common\thirdparty\CheckAdFraud::execute($logger, $this, $RtbBidRequest) === false):
	    		$no_bid_reason = NOBID_AD_FRAUD;
	    		return array();
	    	endif;
	    	
	    	// Check Cookie Match
	    	if (\buyrtbheaderbidding\workflows\tasklets\common\thirdparty\CheckCookieMatch::execute($logger, $this, $RtbBidRequest) === false):
	    		$no_bid_reason = NOBID_UNMATCHED_USER;
	    		return array();
	    	endif;
    	
    	endif;
    	
    	if ($cached_inventory_response === null):
    		\util\HeaderBiddingHelper::store_rtb_matching_line_items($this->config, $cache_params, $InsertionOrderLineItem_Match_List);
    	endif;
    	
    	return $InsertionOrderLineItem_Match_List;

    }


}
