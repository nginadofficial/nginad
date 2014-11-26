<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Delivery\Controller;

use Zend\Mvc\Controller\AbstractActionController;

class IndexController extends AbstractActionController
{

    public function indexAction()
    {
        echo "NGINAD<br />\n";
        exit;
    }


    public function impressAction()
    {

    	$banner_request 						= array();
    	
    	$banner_request["demand_banner_id"] 	= $this->getRequest()->getQuery('zoneid');
    	$banner_request["publisher_banner_id"] 	= $this->getRequest()->getQuery('pzoneid');

    	$banner_request["adpos_x"] 				= $this->getRequest()->getQuery('adpos_x');
    	$banner_request["adpos_y"] 				= $this->getRequest()->getQuery('adpos_y');
    	$banner_request["atf"] 					= $this->getRequest()->getQuery('atf');
    	$banner_request["ct"] 					= $this->getRequest()->getQuery('ct');
    	$banner_request["dt"] 					= $this->getRequest()->getQuery('dt');
    	$banner_request["ifr"] 					= $this->getRequest()->getQuery('ifr');
    	$banner_request["loc"] 					= $this->getRequest()->getQuery('loc');
    	$banner_request["org_tld"] 				= $this->getRequest()->getQuery('org_tld');
    	$banner_request["ref"] 					= $this->getRequest()->getQuery('ref');
    	$banner_request["scres_height"] 		= $this->getRequest()->getQuery('scres_height');
    	$banner_request["scres_width"] 			= $this->getRequest()->getQuery('scres_width');
    	$banner_request["winbid"] 				= $this->getRequest()->getQuery('winbid');
    	$banner_request["tld"] 					= $this->getRequest()->getQuery('tld');
    	$banner_request["ui"] 					= $this->getRequest()->getQuery('ui');
    	
    	$config 								= $this->getServiceLocator()->get('Config');    	
    	
    	/*
    	 * Validate that the banner_id is an integer before continuing
    	 */
    	
    	if (intval($banner_request["demand_banner_id"])):
	
    		$this->process_demand_tag($config, $banner_request);

    	elseif (intval($banner_request["publisher_banner_id"])):
    	
    		$this->process_publisher_tag($config, $banner_request);
    	
    	endif;

    	// default case:
    	// NO AD, HTML COMMENT WITH AD SERVER TAG
    	echo "<!DOCTYPE html>\n<html><body><div style='margin: 0px; padding: 0px;'><!-- NGINAD AD SERVER - NO AD AVAILABLE --></div></body></html>\n";
        exit;
    }
    
    private function process_contract_zone_tag($config, $banner_request, $linked_banner_to_ad_zone_list) {
    	/*
    	 * Add publisher statistics logging
    	 */
    	
    	$banner_request["demand_banner_id"] = $this->get_banner_id_from_display_probability($linked_banner_to_ad_zone_list);
    
    	$AdCampaignBannerFactory = \_factory\AdCampaignBanner::get_instance();
    	
    	$params = array();
    	$params["AdCampaignBannerID"] 	= $banner_request["demand_banner_id"];
    	$AdCampaignBanner 				= $AdCampaignBannerFactory->get_row_cached($config, $params);
    	
    	if ($AdCampaignBanner != null):
    		$cpm_price = $AdCampaignBanner->BidAmount;
    	else:
    		$cpm_price = 0;
		endif;
		    	
    	$ContractPublisherZoneHourlyImpressions = new \model\ContractPublisherZoneHourlyImpressions();
    	
    	$ContractPublisherZoneHourlyImpressions->AdCampaignBannerID		= $banner_request["demand_banner_id"];
    	$ContractPublisherZoneHourlyImpressions->PublisherAdZoneID		= $banner_request["publisher_banner_id"];
    	$ContractPublisherZoneHourlyImpressions->Impressions			= 1;
    	$ContractPublisherZoneHourlyImpressions->SpendTotalGross		= floatval($cpm_price) / 1000;
    	$ContractPublisherZoneHourlyImpressions->SpendTotalNet			= $ContractPublisherZoneHourlyImpressions->SpendTotalGross;

    	\util\CachedStatsWrites::incrementContractPublisherZoneHourlyImpressionsCached($config, $ContractPublisherZoneHourlyImpressions);

    	$this->process_demand_tag($config, $banner_request);

    }
    
    /*
     * @param array linked_banner_to_ad_zone_list
     * An array of associative objects with banners to 
     * pub ad zones and their display frequency
     * @return int banner, mobile or pre-roll id to display
     */
    private function get_banner_id_from_display_probability($linked_banner_to_ad_zone_list) {
    	
    	$banner_display_probability_pool = array();
    	
    	foreach ($linked_banner_to_ad_zone_list as $linked_banner_to_ad_zone):
    		
    		for ($i = 0; $i < $linked_banner_to_ad_zone->Weight; $i++):
    			
    			$banner_display_probability_pool[] = $linked_banner_to_ad_zone->AdCampaignBannerID;
    	
    		endfor;
    		
    	endforeach;
    	
    	$random_key = rand(0, count($banner_display_probability_pool) - 1);
    	
    	return $banner_display_probability_pool[$random_key];
    	
    }

    private function process_publisher_tag($config, $banner_request) {

    	$PublisherAdZoneFactory = \_factory\PublisherAdZone::get_instance();
    	
    	$params = array();
    	$params["AdStatus"] = 1;
    	$params["PublisherAdZoneID"] 	= $banner_request["publisher_banner_id"];
    	$PublisherAdZone 				= $PublisherAdZoneFactory->get_row_cached($config, $params);

	    if ($PublisherAdZone == null):
	    	return;
	    endif;
    	
    	/*
    	 * Is this ad zone linked to one or more contract banners?
    	 * If so forward the request to the contract banner
    	 * display probability logic.
    	 */
    	if ($PublisherAdZone->PublisherAdZoneTypeID == AD_TYPE_CONTRACT):
    		
	    	$LinkedBannerToAdZoneFactory = \_factory\LinkedBannerToAdZone::get_instance();
	    	
	    	$params = array();
	    	$params["PublisherAdZoneID"] 	= $banner_request["publisher_banner_id"];
	    	$LinkedBannerToAdZoneList 		= $LinkedBannerToAdZoneFactory->get_cached($config, $params);
	    	
	    	if ($LinkedBannerToAdZoneList != null && count($LinkedBannerToAdZoneList) > 0):
    			$this->process_contract_zone_tag($config, $banner_request, $LinkedBannerToAdZoneList);
	    	else:
	    		return;
    		endif;
    		
    	else:

	 		$banner_request = $this->build_banner_request($config, $banner_request);   	
	
	 		$RtbSellV22Bid = new \rtbsellv22\RtbSellV22Bid();
	 		
	 		$RtbSellV22Bid->create_rtb_request_from_publisher_display_impression($config, $banner_request);
	 		
	 		$bid_request = $RtbSellV22Bid->build_rtb_bid_request();
	 		
	 		$PingManager = new \pinger\PingManager($config, $bid_request, $PublisherAdZone->AdOwnerID, $PublisherAdZone->PublisherWebsiteID, $PublisherAdZone->FloorPrice, $banner_request["PublisherAdZoneID"], $banner_request["AdName"], $banner_request["WebDomain"]);
	 	
	 		if ($PublisherAdZone->PublisherAdZoneTypeID == AD_TYPE_IN_HOUSE_REMNANT 
	 				|| $PublisherAdZone->PublisherAdZoneTypeID == AD_TYPE_ANY_REMNANT):
	 			$PingManager->set_up_local_demand_ping_clients();	 		
	 		endif;
	 		
	 		if ($PublisherAdZone->PublisherAdZoneTypeID == AD_TYPE_RTB_REMNANT
	 				|| $PublisherAdZone->PublisherAdZoneTypeID == AD_TYPE_ANY_REMNANT):
	 			$PingManager->set_up_remote_rtb_ping_clients();
	 		endif;

	 		$PingManager->ping_rtb_ping_clients();
	 		
	 		$AuctionPopo   		= $PingManager->process_rtb_ping_responses();

	 		$auction_was_won 	= $AuctionPopo->auction_was_won;
	 		
	 		$winning_ad_tag 	= $AuctionPopo->winning_ad_tag;

	 		/*
	 		 * The RTB auction may not have been won because
	 		 * a floor price wasn't met or there simply may not 
	 		 * have been a valid bid on the auction.
	 		 * 
	 		 * Try to set the tag to the publisher's passback tag 
	 		 * if one exists and if not show the default ad
	 		 */
	 		if ($auction_was_won === false):
	 			if ($PublisherAdZone->PassbackAdTag != null 
	 			&& !empty($PublisherAdZone->PassbackAdTag)):
	 			
	 				$winning_ad_tag = $PublisherAdZone->PassbackAdTag;
	 			else:	
	 				return;
	 			endif;
	 		else: 
		 		/*
		 		 * Process the macro replacements in the winning Ad tag:
		 		 *
		 		 * NGINCLKTRK: The click tracking URL, TBD, generic click tracking not yet implemented.
		 		 * Try implementing your own custom CTR rate tracking
		 		 *
		 		 * NGINWBIDPRC: The winning bid price expressed as CPM.
		 		 * If this was a 2nd price auction, the value would be the second price expressed as CPM
		 		 */
		 		
		 		$winning_ad_tag = str_replace("{NGINCLKTRK}", "", $winning_ad_tag);
		 		$winning_ad_tag = str_replace("{NGINWBIDPRC}", $AuctionPopo->winning_bid_price, $winning_ad_tag);
		 		
	 		endif;
	 		
	 		$PingManager->process_rtb_ping_statistics($AuctionPopo);
	 		
	 		// now output the logs to the log file
	
	 		\rtbsellv22\RtbSellV22Logger::get_instance()->output_log();
	 		
	 		if ($AuctionPopo->loopback_demand_partner_won === true):
	 			
	 			$banner_request["demand_banner_id"] = $AuctionPopo->loopback_demand_partner_ad_campaign_banner_id;
	 			$this->process_demand_tag($config, $banner_request);
	 			
	 		else:
		 		
	 			header("Content-type: application/javascript");
		 		$output = "document.write(" . json_encode($winning_ad_tag) . ");";
		 		echo $output;
	 		
		 	endif;

	 		
 		endif;
 		
 		exit;
    }
    
    private function build_banner_request($config, $banner_request) {
    	
    	$banner_request_id = intval($banner_request["publisher_banner_id"]);
    	
    	$PublisherAdZoneFactory = \_factory\PublisherAdZone::get_instance();
    	
    	$params = array();
    	$params["PublisherAdZoneID"] = $banner_request_id;
    	$params["AdStatus"] = 1;
    	$PublisherAdZone = $PublisherAdZoneFactory->get_row_cached($config, $params);

    	$PublisherWebsite = null;
    	
    	if ($PublisherAdZone == null):
    		return null;
    	endif;
    	
    	$PublisherWebsiteFactory = \_factory\PublisherWebsite::get_instance();
    	$params = array();
    	$params["PublisherWebsiteID"] = $PublisherAdZone->PublisherWebsiteID;
    	$PublisherWebsite = $PublisherWebsiteFactory->get_row_cached($config, $params);
    	
    	if ($PublisherWebsite == null):
    		return null;
    	endif;
    	
    	/*
    	 * Produce the RTB request to our demand partners
    	 */
    	
    	$banner_request["PublisherAdZoneID"] 		= $PublisherAdZone->PublisherAdZoneID;
    	$banner_request["AdName"] 					= $PublisherAdZone->AdName;
    	$banner_request["WebDomain"] 				= $PublisherWebsite->WebDomain;
    	
    	if (!isset($banner_request["org_tld"]) || $banner_request["org_tld"] == null):
	    	
	    	$banner_request["org_tld"] = $PublisherWebsite->WebDomain;
	    	
    	endif;
    	
    	$banner_request["height"] 					= $PublisherAdZone->Height;
    	$banner_request["width"] 					= $PublisherAdZone->Width;
    	$banner_request["bidfloor"] 				= sprintf("%1.4f", $PublisherAdZone->FloorPrice);
    	$banner_request["iab_category"] 			= $PublisherWebsite->IABCategory;
    	$banner_request["iab_sub_category"] 		= $PublisherWebsite->IABSubCategory;
    	$banner_request["website_id"] 				= $PublisherAdZone->PublisherWebsiteID;
    	
    	$banner_request["publisher_info_website"] 	= "example.com"; 		// will be populated from the initial publisher signup.
    	$banner_request["publisher_id"] 			= 1; 					// will be populated from the initial publisher signup.
    	$banner_request["publisher_name"] 			= "Demo Publisher"; 	// will be populated from the initial publisher signup.
    	$banner_request["publisher_iab_category"] 	= null;
    	
    	$ip_address = isset($_SERVER['HTTP_X_REAL_IP']) && !empty($_SERVER['HTTP_X_REAL_IP']) ? $_SERVER['HTTP_X_REAL_IP'] : $_SERVER["REMOTE_ADDR"];
    	
    	// to debug on dev
    	if (empty($ip_address)):
    		$ip_address = "127.0.0.1";
    	endif;
    	
    	$banner_request["ip_address"] 				= $ip_address;
    	$banner_request["user_id"] 					= md5($banner_request["ip_address"]);
    	
    	$user_agent = (isset($_SERVER["HTTP_USER_AGENT"])) ? $_SERVER["HTTP_USER_AGENT"] : "";
    	
    	$banner_request["user_agent"] 				= $user_agent;
    	
    	$banner_request["language"] 				= isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2) : null;
    	
    	/*
    	 * Device Type
    	 *
    	 * 1	 Mobile/Tablet
    	 * 2	 Personal	Computer
    	 */
    	
    	$detect = new \mobileutil\MobileDetect(null, $banner_request["user_agent"]);
    	if ($detect->isMobile()):
	    	$banner_request["devicetype"] = 1;
	    	
	    	if ($detect->is('iOS')):
	    		$banner_request["mobile_os"] = "iOS";
	    		$banner_request["mobile_make"] = "Apple";
		    	if ($detect->isTablet()):
		    		$banner_request["mobile_model"] = "iPad";
		    	else:
		    		$banner_request["mobile_model"] = "iPhone";
		    	endif;
	    	elseif ($detect->is('AndroidOS') || $detect->is('Chrome')):
	    		$banner_request["mobile_os"] = "Android";
	    	endif;
    	else:
    		$banner_request["devicetype"] = 2;
    	endif;
    	 
    	return $banner_request;
	   
    }
    
    
    private function process_demand_tag($config, $banner_request) {
    	
    	$banner_request_id = intval($banner_request["demand_banner_id"]);
    	 
    	$buyer_id = $this->getRequest()->getQuery('buyerid');
    	
    	$cache_file_dir =  $config['delivery']['cache_file_location'] . 'demand/' . date('m.d.Y') . '/' . date('H') . '/' . date('i') . '/';
    	
    	if (!file_exists($cache_file_dir)):
    		mkdir($cache_file_dir, 0777, true);
    	endif;
    	
    	$tag_type = "default";
    	
    	if (isset($banner_request["dt"]) && $banner_request["dt"]):
    		$tag_type = trim($banner_request["dt"]);
    	endif;
    	
    	$cache_file = $cache_file_dir . $banner_request_id . "." . $tag_type . ".zone.txt";
    	
    	$AdCampaignBannerFactory = \_factory\AdCampaignBanner::get_instance();
    	
    	$params = array();
    	$params["AdCampaignBannerID"] 	= $banner_request_id;
    	$AdCampaignBanner 				= $AdCampaignBannerFactory->get_row_cached($config, $params);    	

	    if ($AdCampaignBanner != null):
	    
		    $banner_impression_cost 		= $AdCampaignBanner->BidAmount;
		    $spend_increase_gross 			= floatval($banner_impression_cost) / 1000;
		    $spend_increase_net				= $spend_increase_gross;
		    
		    $found_second_price 			= false;
		    
		    if ($banner_request["winbid"] !== null && !empty($banner_request["winbid"])):
		    	$decrypted_second_price = $this->decrypt_second_price($banner_request["winbid"]);
			    if (floatval($decrypted_second_price)):
			    	$spend_increase_net				= floatval($decrypted_second_price) / 1000;
			    	$found_second_price 			= true;
			    endif;
		    endif;
		    
		    if ($found_second_price === false):
			    /*
			     * We already marked down this demand customer's bid when we sent it to the DSP
			     * So at this point we have to get the original bid price set by the demand user
			     * in the demand dashboard and set it to the gross cost for the demand customer.
			     * If we got a second price URL Macro parameter from the DSP, then we are finished.
			     * We set the second price to the net price for the impression.
			     * Otherwise we have to calculate the markup on the demand dashboard bid again
			     * and get the net impression price that way.
			     * 
			     * Remember that the RTB bid price sent to the DSP was already marked down by the markup
			     * price in class RtbBuyV22Workflow:
			     * Line 544: $adusted_amount = floatval($AdCampaignBanner->BidAmount) - floatval($mark_down);
			     */

			    $AdCampaignFactory		= \_factory\AdCampaign::get_instance();
		    	$params					= array();
		    	$params["AdCampaignID"] = $AdCampaignBanner->AdCampaignID;
			    $AdCampaign				= $AdCampaignFactory->get_row_cached($config, $params);
			    
			    $markup_rate 			= \util\Markup::getMarkupRate($AdCampaign, $config);
			    
			    $mark_down 				= floatval($spend_increase_gross) * floatval($markup_rate);
			    $spend_increase_net 	= floatval($spend_increase_gross) - floatval($mark_down);
		    
			endif;
		    
		    $AdCampaignBannerFactory->incrementAdCampaignBannerImpressionsCounterAndSpendCached($config, $buyer_id, $banner_request_id, $spend_increase_gross, $spend_increase_net);
		    $AdCampaignBannerFactory->incrementBuySideHourlyImpressionsByTLDCached($config, $banner_request_id, $banner_request["tld"]);

	    	if (file_exists($cache_file)):
	    	
	    		$cached_tag = file_get_contents($cache_file);
	    		if ($banner_request["dt"] == "in"):
	    			header("Content-type: application/javascript");
	    		endif;
	    	
	    		echo $cached_tag;
	    		exit;
	    		
	    	endif;
    	
	    	if ($banner_request["dt"] == "in"):
	    	
		    	header("Content-type: application/javascript");
		    	$output = "document.write(" . json_encode($AdCampaignBanner->AdTag) . ");";
		    	 
    		else:
		    	$output = "<!DOCTYPE html>\n<html><head></head><body style=\"margin: 0px; padding: 0px;\">" . $AdCampaignBanner->AdTag
		    	. "\r\n\r\n</body></html>";
	    	endif;

	    	
	    	$fh = fopen($cache_file, "w");
	    	fwrite($fh, $output);
	    	fclose($fh);
    	 
	    	echo $output;
	    	exit;
	    	
    	endif;
    	
    	echo "NGINAD";
    	exit;
    }
    
    private function decrypt_second_price($encrypted_price) {
    	
    	/*
    	 * Add your favorite 2nd price encryption 
    	 * algorithm here!
    	 */
    	
    	$decrytped_price = $encrypted_price;
    	
    	return $decrytped_price;
    	
    }
}
