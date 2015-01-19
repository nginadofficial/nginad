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
	
    	$banner_request["dtrack"] 				= $this->getRequest()->getQuery('dtrack');
    	$banner_request["vast"] 				= $this->getRequest()->getQuery('vast');
    	$banner_request["video"] 				= $this->getRequest()->getQuery('video');
    	$banner_request["adpos_x"] 				= $this->getRequest()->getQuery('adpos_x');
    	$banner_request["adpos_y"] 				= $this->getRequest()->getQuery('adpos_y');
    	$banner_request["atf"] 					= $this->getRequest()->getQuery('atf');
    	$banner_request["ifr"] 					= $this->getRequest()->getQuery('ifr');
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
    	
    	if (isset($banner_request["dtrack"]) && $banner_request["dtrack"] == "true"):
    	
    		echo "dtrack";
    		exit;
    	
    	elseif (isset($banner_request["vast"]) && $banner_request["vast"] == "tracker"):
    	
    		$this->track_video_impression($config, $banner_request);
    	
    	elseif (intval($banner_request["demand_banner_id"])):
	
    		$this->process_demand_tag($config, $banner_request);

    	elseif (intval($banner_request["publisher_banner_id"])):
    	
    		$this->process_publisher_tag($config, $banner_request);
    	
    	endif; 

    	// default case:
    	// NO AD, HTML COMMENT WITH AD SERVER TAG
    	
    	if (isset($banner_request["video"]) && $banner_request["video"] == 'vast'):
    		header("Content-type: text/xml");
    		echo '<?xml version="1.0" encoding="utf-8"?><VAST version="2.0" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="vast.xsd"><Ad id="NONDELIVERY"><InLine><AdSystem>NGINAD AD SERVER</AdSystem><AdTitle /><Impression></Impression><Creatives><Creative /></Creatives></InLine></Ad></VAST>';
    	else:
    		echo "<!DOCTYPE html>\n<html><body><div style='margin: 0px; padding: 0px;'><!-- NGINAD AD SERVER - NO AD AVAILABLE --></div></body></html>\n";
        endif;
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
    	
	    $banner_request["ImpressionType"] = $PublisherAdZone->ImpressionType;
	    
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

	 		$banner_request = $this->build_request_array($config, $banner_request);   	
	
	 		$RtbSellV22Bid = new \rtbsellv22\RtbSellV22Bid();
	 		
	 		$RtbSellV22Bid->create_rtb_request_from_publisher_display_impression($config, $banner_request);
	 		
	 		$bid_request = $RtbSellV22Bid->build_rtb_bid_request();
	 		
	 		$PingManager = new \pinger\PingManager($config, $bid_request, $PublisherAdZone->AdOwnerID, $PublisherAdZone->PublisherWebsiteID, $PublisherAdZone->FloorPrice, $banner_request["PublisherAdZoneID"], $banner_request["AdName"], $banner_request["WebDomain"], $banner_request["ImpressionType"]);
	 	
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
	 		 * Auction stats should be published to the database
	 		 * regardless of whether there was a winning bid or not.
	 		 */
	 		$PingManager->process_rtb_ping_statistics($AuctionPopo);
	 		
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
	 		
	 		// now output the logs to the log file
	
	 		\rtbsellv22\RtbSellV22Logger::get_instance()->output_log();
	 		
	 		
	 		$tracker_url = "";
	 		
	 		if ($banner_request["ImpressionType"] == 'video' && \util\ParseHelper::isVastURL($winning_ad_tag) === true && $AuctionPopo->auction_was_won):
	 		
		 		$encryption_key 				= $config['settings']['rtb']['encryption_key'];
		 		$params = array();
		 		$params["winning_price"]		= $AuctionPopo->winning_bid_price;
		 		$params["auction_timestamp"] 	= time();
		 		
		 		$vast_auction_param 	= $this->encrypt_vast_auction_params($encryption_key, $params);
		 		$vast_publisher_param 	= $this->encrypt_vast_auction_params($encryption_key, $AuctionPopo->vast_publisher_imp_obj);
		 			
		 		$tracker_url = $this->get_vast_tracker_url($config, $vast_auction_param, $vast_publisher_param);
		 		$banner_request["tracker_url"] = $tracker_url;
		 		
	 		endif;
	 		
	 		if ($AuctionPopo->loopback_demand_partner_won === true):
	 			
	 			$banner_request["demand_banner_id"] = $AuctionPopo->loopback_demand_partner_ad_campaign_banner_id;
	 			$banner_request["winning_partner_id"] = $AuctionPopo->winning_partner_id;
	 			$banner_request["winning_seat"] = $AuctionPopo->winning_seat;
	 			$this->process_demand_tag($config, $banner_request);
	 			
	 			/* 
	 			 * If this is a local auction we don't need to worry about
	 			 * firing off notice urls
	 			 */
	 			
	 		else:
		 		

	 			if ($banner_request["ImpressionType"] == 'video'):
		 			header("Content-type: text/xml");
	 				if(\util\ParseHelper::isVastURL($winning_ad_tag) === true):
	 					echo $this->get_vast_wrapper_xml($config, $winning_ad_tag, $tracker_url);
	 				else:
	 					echo $winning_ad_tag;
	 				endif;

	 			else:
	 			
	 				// credit publisher account here
		 		
		 			header("Content-type: application/javascript");
			 		$output = "document.write(" . json_encode($winning_ad_tag) . ");";
			 		echo $output;
	 			endif;
			 		
	 			
	 			if (!empty($AuctionPopo->nurl)):
		 			/*
		 			 * If this is a remote RTB auction we do need to worry about
		 			 * firing off notice urls
		 			 *
	    			 * If safe_mode is off we can fire off an asynchronous CURL
	    			 * call which will not block. Otherwise we are stuck
	    			 * with curl call with a timeout.
	    			 * 
	    			 * curl must also be on the path
		 			 */
		 			
		 			// clear output buffer
		 			ob_end_flush();
		 			
		 			// check if curl is installed
		 			$has_curl_on_path = $config['settings']['shell']['has_curl_on_path'];
		 			
		 			if(!ini_get('safe_mode') && $has_curl_on_path):
		 				
		 				exec('bash -c "exec nohup setsid curl \'' . $AuctionPopo->nurl . '\' > /dev/null 2>&1 &"');
		 				
		 			else: 
		 				
		 				\util\WorkflowHelper::get_ping_notice_url_curl_request($AuctionPopo->nurl);
		 				
		 			endif;
	 			endif;
	 			
		 	endif;

	 		
 		endif;
 		
 		exit;
    }
    
    private function build_request_array($config, $banner_request) {
    	
    	/*
    	 * Produce the banner request params for our demand partners
    	*/
    	 
    	$banner_request_id = intval($banner_request["publisher_banner_id"]);
    	 
    	$PublisherAdZone = $this->add_ad_zone_request_params($config, $banner_request, $banner_request_id);
    	
    	if ($PublisherAdZone == null):
    		return null;
    	endif;

    	if ($PublisherAdZone->ImpressionType == 'video'):
    	
    		$banner_request["ImpressionType"] = 'video';
    		$this->build_video_request($config, $banner_request, $PublisherAdZone);
    	
    	else:
    	
    		$banner_request["ImpressionType"] = 'banner';
    		$this->build_banner_request($config, $banner_request, $PublisherAdZone);
    	
    	endif;
    	
    	return $banner_request;
    	
    }
    
    private function build_video_request($config, &$banner_request, &$PublisherAdZone) {
    
    	$this->add_ad_zone_video_request_params($config, $banner_request, $PublisherAdZone);
    	
    	$this->add_publisher_request_params($config, $banner_request, $PublisherAdZone);
    	 
    	$this->add_publisher_website_request_params($config, $banner_request, $PublisherAdZone);
    	
    	$this->add_user_request_params($config, $banner_request);
    
    	$this->add_mobile_request_params($config, $banner_request);
    
    	return $banner_request;
    
    }
    
    private function build_banner_request($config, &$banner_request, &$PublisherAdZone) {

    	$this->add_ad_zone_banner_request_params($config, $banner_request, $PublisherAdZone);
    	
    	$this->add_publisher_request_params($config, $banner_request, $PublisherAdZone);
    	
    	$this->add_publisher_website_request_params($config, $banner_request, $PublisherAdZone);
    	
    	$this->add_user_request_params($config, $banner_request);

		$this->add_mobile_request_params($config, $banner_request);
    	 
    	return $banner_request;
	   
    }
    
    private function add_ad_zone_video_request_params($config, &$banner_request, &$PublisherAdZone) {
    
    	$PublisherAdZoneVideoFactory = \_factory\PublisherAdZoneVideo::get_instance();
    	
    	$params = array();
    	$params["PublisherAdZoneID"] = $PublisherAdZone->PublisherAdZoneID;
    	$PublisherAdZoneVideo = $PublisherAdZoneVideoFactory->get_row_cached($config, $params);
		
    	if ($PublisherAdZoneVideo == null):
    		return;
    	endif;
    	
    	if (!empty($PublisherAdZoneVideo->MimesCommaSeparated)):
    		$banner_request["video_mimes"] 				= explode(',', $PublisherAdZoneVideo->MimesCommaSeparated);
    	endif;
    	if (!empty($PublisherAdZoneVideo->ApisSupportedCommaSeparated)):
    		$banner_request["video_apis_supported"] 	= explode(',', $PublisherAdZoneVideo->ApisSupportedCommaSeparated);
    	endif;
    	if (!empty($PublisherAdZoneVideo->ProtocolsCommaSeparated)):
    		$banner_request["video_protocols"] 			= explode(',', $PublisherAdZoneVideo->ProtocolsCommaSeparated);
    	endif;
    	if (!empty($PublisherAdZoneVideo->DeliveryCommaSeparated)):
    		$banner_request["video_delivery"] 			= explode(',', $PublisherAdZoneVideo->DeliveryCommaSeparated);
    	endif;
    	if (!empty($PublisherAdZoneVideo->PlaybackCommaSeparated)):
    		$banner_request["video_playback"]			= explode(',', $PublisherAdZoneVideo->PlaybackCommaSeparated);
    	endif;
    	
    	$banner_request["video_min_duration"] 		= $PublisherAdZoneVideo->MinDuration;
    	$banner_request["video_max_duration"] 		= $PublisherAdZoneVideo->MaxDuration;
    	$banner_request["video_start_delay"] 		= $PublisherAdZoneVideo->StartDelay;
    	$banner_request["video_linearity"] 			= $PublisherAdZoneVideo->Linearity;
    	$banner_request["video_foldpos"] 			= $PublisherAdZoneVideo->FoldPos;
    	
    	if ($PublisherAdZone->Height != 0):
    		$banner_request["video_height"] 			= $PublisherAdZone->Height;
    	endif;
    	
    	if ($PublisherAdZone->Width != 0):
    		$banner_request["video_width"] 				= $PublisherAdZone->Width;
    	endif;
    }
    
    private function add_ad_zone_banner_request_params($config, &$banner_request, &$PublisherAdZone) {
    
    	$banner_request["banner_height"] 			= $PublisherAdZone->Height;
    	$banner_request["banner_width"] 			= $PublisherAdZone->Width;
    	 
    }
    
    private function add_ad_zone_request_params($config, &$banner_request, $banner_request_id) {
    	 
    	$PublisherAdZoneFactory = \_factory\PublisherAdZone::get_instance();
    	
    	$params = array();
    	$params["PublisherAdZoneID"] = $banner_request_id;
    	$params["AdStatus"] = 1;
    	$PublisherAdZone = $PublisherAdZoneFactory->get_row_cached($config, $params);

    	$banner_request["PublisherAdZoneID"] 		= $PublisherAdZone->PublisherAdZoneID;
    	$banner_request["AdName"] 					= $PublisherAdZone->AdName;
    	
    	$banner_request["bidfloor"] 				= sprintf("%1.4f", $PublisherAdZone->FloorPrice);
    	$banner_request["website_id"] 				= $PublisherAdZone->PublisherWebsiteID;
    	
    	return $PublisherAdZone;
    	
    }
    
    private function add_publisher_website_request_params($config, &$banner_request, &$PublisherAdZone) {
    	
    	$PublisherWebsiteFactory = \_factory\PublisherWebsite::get_instance();
    	$params = array();
    	$params["PublisherWebsiteID"] = $PublisherAdZone->PublisherWebsiteID;
    	$PublisherWebsite = $PublisherWebsiteFactory->get_row_cached($config, $params);
    	 
    	if ($PublisherWebsite == null):
    		return;
    	endif;
    	
    	$banner_request["WebDomain"] 				= $PublisherWebsite->WebDomain;
    	$banner_request["iab_category"] 			= array($PublisherWebsite->IABCategory);
    	$banner_request["iab_sub_category"] 		= array($PublisherWebsite->IABSubCategory);
    	
    	if (!isset($banner_request["org_tld"]) || $banner_request["org_tld"] == null):
    	
    		$banner_request["org_tld"] = $PublisherWebsite->WebDomain;
    	
    	endif;
    }
    
    private function add_publisher_request_params($config, &$banner_request, &$PublisherAdZone) {
    	
    	$PublisherInfoFactory = \_factory\PublisherInfo::get_instance();
    	$params = array();
    	$params["PublisherInfoID"] = $PublisherAdZone->AdOwnerID;
    	$PublisherInfo = $PublisherInfoFactory->get_row_cached($config, $params);
    	 
    	if ($PublisherInfo == null):
    		return;
    	endif;
    	
    	$banner_request["publisher_info_website"] 	= $PublisherInfo->Domain;
    	$banner_request["publisher_id"] 			= $PublisherInfo->PublisherInfoID;
    	$banner_request["publisher_name"] 			= $PublisherInfo->Name;
    	$banner_request["publisher_iab_category"] 	= array($PublisherInfo->IABCategory);
    	
    }
    
    private function add_user_request_params($config, &$banner_request) {
    	
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
    	 
    }
    
    private function add_mobile_request_params($config, &$banner_request) {
    	
    	/*
    	 * Device Type
    	*
    	* 1	 Mobile/Tablet
    	* 2	 Personal	Computer
    	*/
    	 
    	$detect = new \mobileutil\MobileDetect(null, $banner_request["user_agent"]);
    	if ($detect->isMobile()):
	    	$banner_request["devicetype"] = DEVICE_MOBILE;
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
    		$banner_request["devicetype"] = DEVICE_DESKTOP;
    	endif;
    	
    }
    
    
    private function process_demand_tag($config, $banner_request) {
    	
    	$banner_request_id = intval($banner_request["demand_banner_id"]);
    	 
    	if (isset($banner_request["winning_seat"]) && $banner_request["winning_partner_id"]):
    		$buyer_id = 'local:' . $banner_request["winning_partner_id"] . ':' . $banner_request["winning_seat"];
    	else:
    		$buyer_id = $this->getRequest()->getQuery('buyerid');
    	endif;
    	
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

			$is_video_impression 		= false;
			
			if ((isset($banner_request["ImpressionType"]) && $banner_request["ImpressionType"] == 'video')
				||
				(isset($banner_request["video"]) && $banner_request["video"] == 'vast')
				):
			
				$is_video_impression 	= true;
			
			endif;
			
			/*
			 * Video can not be cached
			 */
	    	if (file_exists($cache_file) && $is_video_impression === false):
	    	
	    		$cached_tag = file_get_contents($cache_file);
	    		
	    		if ($banner_request["dt"] == "in"):
	    			$this->ad_macros_to_adtag($cached_tag, $banner_request);
	    			header("Content-type: application/javascript");
	    		else:
	    			$this->ad_macros_to_adtag($cached_tag, $banner_request);
	    		endif;
	    	
	    		echo $cached_tag;
	    		exit;
	    		
	    	endif;
    	
	    	$tag_cachable 				= true;
	    	
	    	if ($is_video_impression === true):
    		
	    		$adtag = $AdCampaignBanner->AdTag;
	    	
	    		if(\util\ParseHelper::isVastURL($adtag) === true):
	    			
	    			$tracker_url = "";
	    			if (isset($banner_request["tracker_url"])):
	    				$tracker_url = $banner_request["tracker_url"];
	    			endif;
	    			$output = $this->get_vast_wrapper_xml($config, $adtag, $tracker_url);
	    			$tag_cachable = false;
	    			
	    		else:
	    			$output = $adtag;
	    		endif;

	    		header("Content-type: text/xml");
	    	
	    	elseif ($banner_request["dt"] == "in"):
	    	
		    	header("Content-type: application/javascript");
		    	$output = "document.write(" . json_encode($AdCampaignBanner->AdTag) . ");";
		    	 
    		else:
    		
		    	$output = "<!DOCTYPE html>\n<html><head></head><body style=\"margin: 0px; padding: 0px;\">" . $AdCampaignBanner->AdTag
		    	. "\r\n\r\n</body></html>";
    		
	    	endif;

	    	
	    	if ($tag_cachable === true):
	    		
		    	$fh = fopen($cache_file, "w");
		    	fwrite($fh, $output);
		    	fclose($fh);
		    	
	    	endif;
    	 
	    	if ($is_video_impression === false):
	    		$this->ad_macros_to_adtag($output, $banner_request);
	    	endif;
	    	
	    	echo $output;
	    	exit;
	    	
    	endif;
    	
    	echo "NGINAD";
    	exit;
    }
    
    private function track_video_impression($config, $banner_request) {
    	
    	$error_message 			= "Error";
    	
    	$ap_param 				= $this->getRequest()->getQuery('ap');
    	$pp_param 				= $this->getRequest()->getQuery('pp');
    	
    	if (empty($ap_param) || empty($pp_param)):
    		die($error_message);
    	endif;
    	
    	$encryption_key 	= $config['settings']['rtb']['encryption_key'];
    	
    	$ap = $this->decrypt_vast_auction_params($encryption_key, $ap_param);
    	
    	if (empty($ap)):
    		die($error_message);
    	endif;
    	
    	$pp = $this->decrypt_vast_auction_params($encryption_key, $pp_param);
    	
    	if (empty($ap) || (!($pp instanceof \model\PublisherHourlyBids))):
    		die($error_message);
    	endif;
    	
    	$minutes_to_expire = 5;
    	
    	if (intval($ap["auction_timestamp"]) < (time() - (60 * $minutes_to_expire))):
    		// timestamp token expired
    		die($error_message);
    	endif;
    	
    	\util\CachedStatsWrites::incrementPublisherBidsCounterCached($config, $pp);
    	
    	echo 'tracking_id: ' . $pp->PublisherAdZoneID . '_' . md5($pp_param . $ap_param);
    	exit;
    }
    
    private function encrypt_vast_auction_params($encryption_key, $params) {
    		
    	/*
    	 * serialize params for URL using ZF2 encryption
    	 */
    	
    	$filter = new \Zend\Filter\Encrypt();
    	$filter->setKey($encryption_key);
    	$filter->setVector('12345678901234567890');
    	return urlencode($filter->filter(serialize($params)));
    	
    }
    
    private function get_vast_tracker_url($config, $vast_auction_param, $vast_publisher_param) {
    
    	$delivery_adtag = $config['delivery']['url'];
    	
    	$cache_buster = time();
    
    	$notice_tag = $delivery_adtag . "?vast=tracker&ap=" . $vast_auction_param . "&pp=" . $vast_publisher_param . "&cb=" . $cache_buster;
    
    	return $notice_tag;
    }
    
    private function decrypt_vast_auction_params($encryption_key, $param) {
    
    	/*
    	 * unserialize params for URL using ZF2 encryption
    	 */
    	 
    	$decrypted_params = null;
    	
    	try {
	    	$filter = new \Zend\Filter\Decrypt();
			$filter->setKey($encryption_key);
			$decrypted_string = $filter->filter($param);
			$decrypted_params = unserialize($decrypted_string);
    	} catch (Exception $e) {
    		// logging here
    	}
    	
    	return $decrypted_params;
    	 
    }
    
    private function get_vast_wrapper_xml($config, $vast_url, $tracker_url) {
    	
    	$delivery_adtag = $config['delivery']['url'];
    	
    	$nl = "\n";
    	
    	$vast_wrapper_xml = '<?xml version="1.0" encoding="utf-8"?> ' . $nl
    						. '<VAST version="2.0" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="vast.xsd"> ' . $nl
    						. '	<Ad id="NginAdVideoAd">' . $nl
    						. '		<Wrapper>' . $nl
    						. '			<AdSystem>NGINAD AD SERVER</AdSystem>' . $nl
    						. '			<VASTAdTagURI><![CDATA[' . $vast_url . ']]></VASTAdTagURI>' . $nl;
    	
    	if (!empty($tracker_url)):
    	
    		$vast_wrapper_xml.= '			<Impression><![CDATA[' . $tracker_url . ']]></Impression>' . $nl;
    	
    	else:
    	
    		// dummy code for required field	
    		$vast_wrapper_xml.= '			<Impression><![CDATA[' . $delivery_adtag . "?dtrack=true" . ']]></Impression>' . $nl;
    	
    	endif;
    	
    	$vast_wrapper_xml.= '			<Creatives>' . $nl
    						. '				<Creative>' . $nl
    						. '					<CompanionAds/>' . $nl
    						. '				</Creative>' . $nl
    						. '			</Creatives>' . $nl
    						. '		</Wrapper>' . $nl
    						. '	</Ad>' . $nl
    						. '</VAST>';
    	
    	return $vast_wrapper_xml;
    }
    
    private function ad_macros_to_adtag(&$adtag, &$banner_request) {
    	
    	// AppNexus nefarious REFERER_URL macro
    	if (!empty($banner_request["ref"])):
    		$adtag = str_replace('${REFERER_URL}', rawurlencode($banner_request["ref"]), $adtag);
    	endif;
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
