<?php
/**
 * NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2016 NginAd Foundation. All Rights Reserved
 * @license GPLv3
 */

namespace buyrtb\workflows;

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
    	
    	// $logger = \rtbbuyv22\RtbBuyV22Logger::get_instance();
    	
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
    	$InsertionOrderLineItemVideoRestrictionsFactory = \_factory\InsertionOrderLineItemVideoRestrictions::get_instance();
    	
    	// match ip against country code
    	\buyrtb\workflows\tasklets\common\insertionorder\GetGeoCodeCountry::execute($logger, $this, $RtbBidRequest);

    	foreach ($InsertionOrderList as $InsertionOrder):

	    	// Check campaign date
	    	if (\buyrtb\workflows\tasklets\common\insertionorder\CheckCampaignDate::execute($logger, $this, $RtbBidRequest, $InsertionOrder) === false):
	    		continue;
	    	endif;

        	// Check max spend
	    	if (\buyrtb\workflows\tasklets\common\insertionorder\CheckMaxSpend::execute($logger, $this, $RtbBidRequest, $InsertionOrder) === false):
	    		continue;
	    	endif;

	    	// Check max impressions
	    	if (\buyrtb\workflows\tasklets\common\insertionorder\CheckMaxImpressions::execute($logger, $this, $RtbBidRequest, $InsertionOrder) === false):
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
			        	
	        		if ((empty($RtbBidRequestImp->RtbBidRequestVideo) && $InsertionOrderLineItem->ImpressionType == 'video')
	        				|| (empty($RtbBidRequestImp->RtbBidRequestBanner) && ($InsertionOrderLineItem->ImpressionType == 'banner' || $InsertionOrderLineItem->ImpressionType == 'image'))):
	        			continue;
	        		endif;
	        		
			        /*
			         * check the business rules against the banner
			         */
	        		
	        		// Check PMP
	        		if (\buyrtb\workflows\tasklets\common\insertionorderlineitem\CheckPrivateMarketPlace::execute($logger, $this, $RtbBidRequest, $RtbBidRequestImp, $InsertionOrderLineItem) === false):
	        			continue;
	        		endif;
	
	        		// Check Domain Admin SSP Channel Selections
	        		if (\buyrtb\workflows\tasklets\common\insertionorderlineitem\CheckSspChannelSelections::execute($logger, $this, $RtbBidRequest, $RtbBidRequestImp, $InsertionOrderLineItem) === false):
	        			continue;
	        		endif;
	        		
		        	// Check banner date
		        	if (\buyrtb\workflows\tasklets\common\insertionorderlineitem\CheckBannerDate::execute($logger, $this, $RtbBidRequest, $RtbBidRequestImp, $InsertionOrderLineItem) === false):
		        		continue;
		        	endif;
	            	
		        	// Check impression price floor
		        	if (\buyrtb\workflows\tasklets\common\insertionorderlineitem\CheckPriceFloor::execute($logger, $this, $RtbBidRequest, $RtbBidRequestImp, $InsertionOrderLineItem, $markup_rate) === false):
		        		continue;
		        	endif;
		        	
	            	// Check banner domain exclusive inclusions
		        	if (\buyrtb\workflows\tasklets\common\insertionorderlineitem\CheckExclusiveInclusion::execute($logger, $this, $RtbBidRequest, $RtbBidRequestImp, $InsertionOrderLineItem, $InsertionOrderLineItemExclusiveInclusionFactory) === false):
		        		continue;
		        	endif;
	
	            	// Check banner domain exclusions match
		        	if (\buyrtb\workflows\tasklets\common\insertionorderlineitem\CheckDomainExclusion::execute($logger, $this, $RtbBidRequest, $RtbBidRequestImp, $InsertionOrderLineItem, $InsertionOrderLineItemDomainExclusionFactory) === false):
		        		continue;
		        	endif;
		        	

					if (!empty($RtbBidRequestImp->RtbBidRequestVideo)):
						
						// Video Workflow
						$VideoWorkflow = new \buyrtb\workflows\VideoWorkflow();
						$passed_child_workflow = $VideoWorkflow->process_business_rules_workflow($logger, $this, $RtbBidRequest, $RtbBidRequestImp, $InsertionOrderLineItem, $InsertionOrderLineItemVideoRestrictionsFactory);
							
					else:
					
						// Display Banner Workflow - Default [ banner/ad tag/image creative types ]
						$DisplayWorkflow = new \buyrtb\workflows\DisplayWorkflow();
						$passed_child_workflow = $DisplayWorkflow->process_business_rules_workflow($logger, $this, $RtbBidRequest, $RtbBidRequestImp, $InsertionOrderLineItem, $InsertionOrderLineItemRestrictionsFactory);

					endif;
					
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
    	
    		// Check Publisher Score
	    	if (\buyrtb\workflows\tasklets\common\thirdparty\CheckPublisherScore::execute($logger, $this, $RtbBidRequest) === false):
		    	$no_bid_reason = NOBID_BAD_PUBLISHER;
		    	return array();
	    	endif;
	    	
	    	// Check Ad Fraud
	    	if (\buyrtb\workflows\tasklets\common\thirdparty\CheckAdFraud::execute($logger, $this, $RtbBidRequest) === false):
		    	$no_bid_reason = NOBID_AD_FRAUD;
		    	return array();
	    	endif;
	    	
	    	// Check Cookie Match
	    	if (\buyrtb\workflows\tasklets\common\thirdparty\CheckCookieMatch::execute($logger, $this, $RtbBidRequest) === false):
		    	$no_bid_reason = NOBID_UNMATCHED_USER;
		    	return array();
	    	endif;
	    	 
    	endif;

    	return $InsertionOrderLineItem_Match_List;

    }


}
