<?php
/**
 * CDNPAL NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2015 CDNPAL Ltd. All Rights Reserved
 * @license GPLv3
 */

namespace buyrtbfidelity\workflows;

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
    	
    	// $logger = \buyrtbfidelity\RtbBuyFidelityLogger::get_instance();
    	
    	/*
    	 * The bidding price in the test bid response has to be between 0.01 and 1 (USD),
    	* and as always, it must not be lower than the specified bid floor.
    	*/
    	
        $AdCampaignBanner_Match_List = array();
        
    	$AdCampaignFactory = \_factory\AdCampaign::get_instance();
    	$params = array();
    	$params["Active"] = 1;
    	
    	$AdCampaignList = $AdCampaignFactory->get_cached($this->config, $params);
    	
    	$this->current_time = time();

    	$AdCampaignBannerFactory = \_factory\AdCampaignBanner::get_instance();
    	$AdCampaignBannerDomainExclusionFactory = \_factory\AdCampaignBannerDomainExclusion::get_instance();
    	$AdCampaignBannerExclusiveInclusionFactory = \_factory\AdCampaignBannerDomainExclusiveInclusion::get_instance();
    	$AdCampaignBannerRestrictionsFactory = \_factory\AdCampaignBannerRestrictions::get_instance();
    	/*
    	 * FIDELITY MOD: 
    	 * Video ads will presently not be supported.
    	 * 
    	 * $AdCampaignVideoRestrictionsFactory = \_factory\AdCampaignVideoRestrictions::get_instance();
    	 */
    	
    	// match ip against country code
    	\buyrtbfidelity\workflows\tasklets\common\adcampaign\GetGeoCodeCountry::execute($logger, $this, $RtbBidRequest);

    	foreach ($AdCampaignList as $AdCampaign):

	    	// Check campaign date
	    	if (\buyrtbfidelity\workflows\tasklets\common\adcampaign\CheckCampaignDate::execute($logger, $this, $RtbBidRequest, $AdCampaign) === false):
	    		continue;
	    	endif;

        	// Check max spend
	    	if (\buyrtbfidelity\workflows\tasklets\common\adcampaign\CheckMaxSpend::execute($logger, $this, $RtbBidRequest, $AdCampaign) === false):
	    		continue;
	    	endif;

	    	// Check max impressions
	    	if (\buyrtbfidelity\workflows\tasklets\common\adcampaign\CheckMaxImpressions::execute($logger, $this, $RtbBidRequest, $AdCampaign) === false):
	    		continue;
	    	endif;

	    	// get markup rate for ad campaign
        	$markup_rate = \util\Markup::getMarkupRate($AdCampaign, $this->config);

        	// iterate the active banners for this ad campaign
        	$params = array();
        	$params["AdCampaignID"] = $AdCampaign->AdCampaignID;
        	$params["Active"] = 1;
        	$AdCampaignBannerList = $AdCampaignBannerFactory->get_cached($this->config, $params);

        	foreach ($RtbBidRequest->RtbBidRequestImpList as $RtbBidRequestImp):
        		
	        	foreach ($AdCampaignBannerList as $AdCampaignBanner):
			        	
	        		if (empty($RtbBidRequestImp->RtbBidRequestVideo) && $AdCampaignBanner->ImpressionType == 'video'
	        				|| !empty($RtbBidRequestImp->RtbBidRequestVideo) && $AdCampaignBanner->ImpressionType == 'banner'):
	        			continue;
	        		endif;
	        		
			        /*
			         * check the business rules against the banner
			         */
		        	
		        	// Check banner date
		        	if (\buyrtbfidelity\workflows\tasklets\common\adcampaignbanner\CheckBannerDate::execute($logger, $this, $RtbBidRequest, $RtbBidRequestImp, $AdCampaignBanner) === false):
		        		continue;
		        	endif;
	            
	            	// Check to see if this AdCampaginBanner is associated to a contract zone.
		        	if (\buyrtbfidelity\workflows\tasklets\common\adcampaignbanner\CheckIsContract::execute($logger, $this, $RtbBidRequest, $RtbBidRequestImp, $AdCampaignBanner) === false):
		        		continue;
		        	endif;
	            	
		        	// Check impression price floor
		        	if (\buyrtbfidelity\workflows\tasklets\common\adcampaignbanner\CheckPriceFloor::execute($logger, $this, $RtbBidRequest, $RtbBidRequestImp, $AdCampaignBanner, $markup_rate) === false):
		        		continue;
		        	endif;
		        	
	            	// Check banner domain exclusive inclusions
		        	if (\buyrtbfidelity\workflows\tasklets\common\adcampaignbanner\CheckExclusiveInclusion::execute($logger, $this, $RtbBidRequest, $RtbBidRequestImp, $AdCampaignBanner, $AdCampaignBannerExclusiveInclusionFactory) === false):
		        		continue;
		        	endif;
	
	            	// Check banner domain exclusions match
		        	if (\buyrtbfidelity\workflows\tasklets\common\adcampaignbanner\CheckDomainExclusion::execute($logger, $this, $RtbBidRequest, $RtbBidRequestImp, $AdCampaignBanner, $AdCampaignBannerDomainExclusionFactory) === false):
		        		continue;
		        	endif;
		        	
		        	/*
		        	 * FIDELITY MOD:
		        	 * Video ads will presently not be supported.
		        	 * 
		        	 * 
					if (!empty($RtbBidRequestImp->RtbBidRequestVideo)):
						
						// Video Workflow
						$VideoWorkflow = new \buyrtbfidelity\workflows\VideoWorkflow();
						$passed_child_workflow = $VideoWorkflow->process_business_rules_workflow($logger, $this, $RtbBidRequest, $RtbBidRequestImp, $AdCampaignBanner, $AdCampaignVideoRestrictionsFactory);
							
					else:
					
						// Display Banner Workflow - Default
						$DisplayWorkflow = new \buyrtbfidelity\workflows\DisplayWorkflow();
						$passed_child_workflow = $DisplayWorkflow->process_business_rules_workflow($logger, $this, $RtbBidRequest, $RtbBidRequestImp, $AdCampaignBanner, $AdCampaignBannerRestrictionsFactory);

					endif;
					*/
		        	
		        	// Display Banner Workflow
		        	$DisplayWorkflow = new \buyrtbfidelity\workflows\DisplayWorkflow();
		        	$passed_child_workflow = $DisplayWorkflow->process_business_rules_workflow($logger, $this, $RtbBidRequest, $RtbBidRequestImp, $AdCampaignBanner, $AdCampaignBannerRestrictionsFactory);
		        	 
					
					if ($passed_child_workflow === false):
						continue;
					endif;
		        	
	            	/*
	            	 * PASSED ALL THE BUSINESS RULES, ADD TO THE RESULTS
	            	 */
	                $AdCampaignBannerFactory->incrementAdCampaignBannerBidsCounterCached($this->config, $rtb_seat_id, $AdCampaignBanner->AdCampaignBannerID);
	
	                /*
	                 * Adjust the bid rate according to the markup
	                 */
	
	                $mark_down = floatval($AdCampaignBanner->BidAmount) * floatval($markup_rate);
	                $adusted_amount = floatval($AdCampaignBanner->BidAmount) - floatval($mark_down);
	
	                $AdCampaignBanner->BidAmount = sprintf("%1.4f", $adusted_amount);
	                
	                // default in config
					$currency = $this->config['settings']['rtb']['auction_currency'];
					
					if (isset($RtbBidRequest->cur[0])):
						$currency = $RtbBidRequest->cur[0];
					endif;
	                
	            	$AdCampaignBanner_Match_List[(string)$AdCampaignBanner->UserID][] = array(
	            											"currency" => $currency,
	            											"impid" => $RtbBidRequestImp->id,
	            											"AdCampaignBanner" => $AdCampaignBanner);
	
	        	endforeach;
        	
        	endforeach;

    	endforeach;

    	if (count($AdCampaignBanner_Match_List)):
    	
	    	// Check Ad Fraud
	    	if (\buyrtbfidelity\workflows\tasklets\common\thirdparty\CheckPublisherScore::execute($logger, $this, $RtbBidRequest) === false):
	    		$no_bid_reason = NOBID_BAD_PUBLISHER;
	    		return array();
	    	endif;
	    	 
	    	// Check Publisher Score
	    	if (\buyrtbfidelity\workflows\tasklets\common\thirdparty\CheckAdFraud::execute($logger, $this, $RtbBidRequest) === false):
	    		$no_bid_reason = NOBID_AD_FRAUD;
	    		return array();
	    	endif;
	    	
	    	// Check Cookie Match
	    	if (\buyrtbfidelity\workflows\tasklets\common\thirdparty\CheckCookieMatch::execute($logger, $this, $RtbBidRequest) === false):
	    		$no_bid_reason = NOBID_UNMATCHED_USER;
	    		return array();
	    	endif;
    	
    	endif;
    	
    	return $AdCampaignBanner_Match_List;

    }


}
