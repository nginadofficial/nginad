<?php
/**
 * CDNPAL NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2015 CDNPAL Ltd. All Rights Reserved
 * @license GPLv3
 */

namespace buyrtb\workflows;

class DisplayWorkflow
{
	public $current_time;
	
	// geocity light
	public $geo_info = null;
	public $maxmind = null;
	
    public function process_business_rules_workflow($logger, $RtbBid) {

    	// $logger = \rtbbuyv22\RtbBuyV22Logger::get_instance();
    	
        $AdCampaignBanner_Match_List = array();

    	$AdCampaignFactory = \_factory\AdCampaign::get_instance();
    	$params = array();
    	$params["Active"] = 1;
    	$AdCampaignList = $AdCampaignFactory->get_cached($RtbBid->config, $params);

    	$this->current_time = time();

    	$AdCampaignBannerFactory = \_factory\AdCampaignBanner::get_instance();
    	$AdCampaignBannerDomainExclusionFactory = \_factory\AdCampaignBannerDomainExclusion::get_instance();
    	$AdCampaignBannerExclusiveInclusionFactory = \_factory\AdCampaignBannerDomainExclusiveInclusion::get_instance();
    	$AdCampaignBannerRestrictionsFactory = \_factory\AdCampaignBannerRestrictions::get_instance();

    	// match ip against country code
    	\buyrtb\workflows\tasklets\common\adcampaign\GetGeoCodeCountry::execute($logger, $this, $RtbBid);
    	
    	foreach ($AdCampaignList as $AdCampaign):

	    	// Check campaign date
	    	if (\buyrtb\workflows\tasklets\common\adcampaign\CheckCampaignDate::execute($logger, $this, $RtbBid, $AdCampaign) === false):
	    		continue;
	    	endif;

        	// Check max spend
	    	if (\buyrtb\workflows\tasklets\common\adcampaign\CheckMaxSpend::execute($logger, $this, $RtbBid, $AdCampaign) === false):
	    		continue;
	    	endif;

	    	// Check max impressions
	    	if (\buyrtb\workflows\tasklets\common\adcampaign\CheckMaxImpressions::execute($logger, $this, $RtbBid, $AdCampaign) === false):
	    		continue;
	    	endif;

	    	// get markup rate for ad campaign
        	$markup_rate = \util\Markup::getMarkupRate($AdCampaign, $RtbBid->config);

        	// iterate the active banners for this ad campaign
        	$params = array();
        	$params["AdCampaignID"] = $AdCampaign->AdCampaignID;
        	$params["Active"] = 1;
        	$AdCampaignBannerList = $AdCampaignBannerFactory->get_cached($RtbBid->config, $params);

        	foreach ($AdCampaignBannerList as $AdCampaignBanner):
		        	
		        /*
		         * check the business rules against the banner
		         */
	        	
	        	// Check banner date
	        	if (\buyrtb\workflows\tasklets\common\adcampaignbanner\CheckBannerDate::execute($logger, $this, $RtbBid, $AdCampaignBanner) === false):
	        		continue;
	        	endif;
	        	
            	// Check is mobile web, phone, tablet, native iOS or native Android
	        	if (\buyrtb\workflows\tasklets\common\adcampaignbanner\CheckIsMobile::execute($logger, $this, $RtbBid, $AdCampaignBanner) === false):
	        		continue;
	        	endif;

            	// Check banner height and width match
	        	if (\buyrtb\workflows\tasklets\display\adcampaignbanner\CheckDisplayBannerDimensions::execute($logger, $this, $RtbBid, $AdCampaignBanner) === false):
	        		continue;
	        	endif;
            
            	// Check to see if this AdCampaginBanner is associated to a contract zone.
	        	if (\buyrtb\workflows\tasklets\common\adcampaignbanner\CheckIsContract::execute($logger, $this, $RtbBid, $AdCampaignBanner) === false):
	        		continue;
	        	endif;
            	
            	// Check banner domain exclusive inclusions
	        	if (\buyrtb\workflows\tasklets\common\adcampaignbanner\CheckExclusiveInclusion::execute($logger, $this, $RtbBid, $AdCampaignBanner, $AdCampaignBannerExclusiveInclusionFactory) === false):
	        		continue;
	        	endif;

            	// Check banner domain exclusions match
	        	if (\buyrtb\workflows\tasklets\common\adcampaignbanner\CheckDomainExclusion::execute($logger, $this, $RtbBid, $AdCampaignBanner, $AdCampaignBannerExclusiveInclusionFactory) === false):
	        		continue;
	        	endif;

            	// Check banner restrictions
	        	if (\buyrtb\workflows\CheckBannerRestrictionsWorkflow::execute($logger, $this, $RtbBid, $AdCampaignBanner, $AdCampaignBannerRestrictionsFactory) === false):
	        		continue;
	        	endif;
	        	
            	/*
            	 * PASSED ALL THE BUSINESS RULES, ADD TO THE RESULTS
            	 */
                $AdCampaignBannerFactory->incrementAdCampaignBannerBidsCounterCached($RtbBid->config, $RtbBid->rtb_seat_id, $AdCampaignBanner->AdCampaignBannerID);

                /*
                 * Adjust the bid rate according to the markup
                 */

                $mark_down = floatval($AdCampaignBanner->BidAmount) * floatval($markup_rate);
                $adusted_amount = floatval($AdCampaignBanner->BidAmount) - floatval($mark_down);

                $AdCampaignBanner->BidAmount = sprintf("%1.4f", $adusted_amount);
                
            	$AdCampaignBanner_Match_List[] = $AdCampaignBanner;

        	endforeach;

    	endforeach;

    	return $AdCampaignBanner_Match_List;

    }


}
