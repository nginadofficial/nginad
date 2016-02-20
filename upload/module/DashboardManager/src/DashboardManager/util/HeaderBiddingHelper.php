<?php
/**
 * NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2016 NginAd Foundation. All Rights Reserved
 * @license GPLv3
 */

namespace util;

class HeaderBiddingHelper {

	protected static $class_name = 'HeaderBiddingHelper';
	
    public static function rebuild_header_bidder($config, $rebuild_header_id) {
    	
    	$site_url							= $config['delivery']['site_url'];
    	$site_url							= str_replace(array('http://', 'https://'), array('', ''), $site_url);
    	
    	$HeaderBiddingPageFactory 			= \_factory\HeaderBiddingPage::get_instance();
    	$HeaderBiddingAdUnitFactory 		= \_factory\HeaderBiddingAdUnit::get_instance();
    	$PublisherAdZoneFactory 			= \_factory\PublisherAdZone::get_instance();
    	$PublisherWebsiteFactory 			= \_factory\PublisherWebsite::get_instance();
    	
    	$params 							= array();
    	$params["HeaderBiddingPageID"]		= $rebuild_header_id;
    	$HeaderBiddingPage 					= $HeaderBiddingPageFactory->get_row($params);
    
    	$params 							= array();
    	$params["HeaderBiddingPageID"]		= $rebuild_header_id;
    	$PublisherAdZoneList 				= $PublisherAdZoneFactory->get($params);

    	$header_bidding_file_dir			= "public/headerbid/" . $PublisherAdZoneList[0]->AdOwnerID;
    	
    	$header_bidding_file_path			= $header_bidding_file_dir . "/" . $HeaderBiddingPage->JSHeaderFileUnqName;
    	
    	if (!count($PublisherAdZoneList) && $HeaderBiddingPage != null):
    		unlink($header_bidding_file_path);
    		return;
    	elseif ($HeaderBiddingPage == null):
    		return;
    	endif;

    	if (!file_exists($header_bidding_file_dir)):
    		mkdir($header_bidding_file_dir, 0777, true);
    	endif;

    	$file_handle = fopen($header_bidding_file_path, "w");

    	$ad_units = array();
    	
    	foreach ($PublisherAdZoneList as $PublisherAdZone):
    	
	    	$params 							= array();
	    	$params["PublisherWebsiteID"]		= $PublisherAdZone->PublisherWebsiteID;
	    	$PublisherWebsite 					= $PublisherWebsiteFactory->get_row($params);
	    	 
	    	if ($PublisherWebsite == null):
	    		continue;
	    	endif;

	    	$params 							= array();
	    	$params["PublisherAdZoneID"]		= $PublisherAdZone->PublisherAdZoneID;
	    	$HeaderBiddingAdUnitList 			= $HeaderBiddingAdUnitFactory->get($params);
    	
	    	if (!count($HeaderBiddingAdUnitList)):
	    		continue;
	    	endif;
	    	
	    	$ad_unit 					= array();
	    	
	    	foreach ($HeaderBiddingAdUnitList as $HeaderBiddingAdUnit):
	    		
	    		if (!isset(\util\ZoneHelper::$header_bidding_adxs[$HeaderBiddingAdUnit->AdExchange])):
	    			continue;
	    		endif;
	    		
	    		$header_bid_params = \util\ZoneHelper::$header_bidding_adxs[$HeaderBiddingAdUnit->AdExchange];

	    		if (!isset($ad_unit['code'])):
	    		
		    		$ad_unit['code']			= $HeaderBiddingAdUnit->DivID;

	    		endif;
	    		
	    		if (!isset($ad_unit['sizes'])):
	    		
	    			$hw					= array(intval($HeaderBiddingAdUnit->Width), intval($HeaderBiddingAdUnit->Height));
	    			$ad_unit['sizes'][] = $hw;

	    		endif;
	    		
	    		$bidder						= array();
	    		
	    		$bidder['bidder']			= $HeaderBiddingAdUnit->AdExchange;
	    		
	    		$bidder['params']			= array();
	    		
	    		$custom_params =  unserialize($HeaderBiddingAdUnit->CustomParams);
	    		
	    		if ($custom_params != null && count($custom_params)):
	    		
		    		foreach ($header_bid_params as $header_bid_param):
						
		    			if (isset($custom_params[$header_bid_param])):
		    		
		    				$bidder['params'][$header_bid_param] = $custom_params[$header_bid_param];
		    			
		    			endif;
		    			
		    		endforeach;
		    		
	    		endif;
	    		
	    		$bidder['params']['hb_nginad_bidder_id'] 	= $HeaderBiddingAdUnit->HeaderBiddingAdUnitID;
	    		$bidder['params']['hb_nginad_pub_id'] 		= $PublisherAdZone->PublisherAdZoneID;
	    		$bidder['params']['hb_nginad_zone_width'] 	= $PublisherAdZone->Width;
	    		$bidder['params']['hb_nginad_zone_height'] 	= $PublisherAdZone->Height;
	    		$bidder['params']['hb_nginad_zone_tld'] 	= $PublisherWebsite->WebDomain;
	    		$ad_unit['bids'][]							= $bidder;

	    	endforeach;
	    	
	    	/*
	    	 * Add the house NginAd Bidder as the last header bidder on the list
	    	 * for each publisher zone
	    	 */
	    	$bidder										= array();
	    	$bidder['bidder']							= 'nginad';
	    	$bidder['params']							= array();
	    	$bidder['params']['pzoneid'] 				= $PublisherAdZone->PublisherAdZoneID;
	    	$bidder['params']['nginad_domain'] 			= $site_url;
	    	$bidder['params']['hb_nginad_bidder_id'] 	= '7878787';
	    	$bidder['params']['hb_nginad_pub_id'] 		= $PublisherAdZone->PublisherAdZoneID;
	    	$bidder['params']['hb_nginad_zone_width'] 	= $PublisherAdZone->Width;
	    	$bidder['params']['hb_nginad_zone_height'] 	= $PublisherAdZone->Height;
	    	$bidder['params']['hb_nginad_zone_tld'] 	= $PublisherWebsite->WebDomain;
	    	$ad_unit['bids'][]							= $bidder;
	    	 
	    	$ad_units[]									= $ad_unit;
	    	
    	endforeach;
    	
    	$prebid_helper_template				= file_get_contents('public/js/prebid/nginad.prebid.template.js');	
    	
    	$encoded_data	 					= json_encode($ad_units, JSON_PRETTY_PRINT);
    	
    	$site_url							= $config['delivery']['site_url'];
    	
    	$site_url							= str_replace(array('http://', 'https://'), array('', ''), $site_url);
    	
    	$prebid_helper_template				= str_replace(array('__ADUNITS__', '__NGINAD_SERVER_DOMAIN__'), array($encoded_data, $site_url), $prebid_helper_template);
    	
    	fwrite($file_handle, $prebid_helper_template);
    					
    	fclose($file_handle);

    }
    
    public static function record_header_auction_publisher_nginad_bid_loss($config, $WebDomain, $PublisherAdZoneID, $AdName) {
    	
    	$PublisherHourlyBids = new \model\PublisherHourlyBids();
    		
    	$PublisherHourlyBids->PublisherAdZoneID						= $PublisherAdZoneID;
    	$PublisherHourlyBids->AuctionCounter						= 1;
    	$PublisherHourlyBids->BidsWonCounter						= 0;
    	$PublisherHourlyBids->BidsLostCounter						= 1;
    	$PublisherHourlyBids->BidsErrorCounter						= 0;
    	$PublisherHourlyBids->SpendTotalGross						= 0;
    	$PublisherHourlyBids->SpendTotalPrivateExchangeGross		= 0;
    	$PublisherHourlyBids->SpendTotalNet							= 0;
    	
    	\util\CachedStatsWrites::incrementPublisherBidsCounterCached($config, $PublisherHourlyBids);
    	
    	$log_header = "----------------------------------------------------------------\n";
    	$log_header.= "NEW BID RESPONSE, WEBSITE: " . $WebDomain . ", PubZoneID: " . $PublisherAdZoneID . ", AD: " . $AdName;
    	
    	\rtbsellv22\RtbSellV22Logger::get_instance()->log[] = $log_header;
    	
    	$log_header = "NEW BID RESPONSE, WEBSITE: " . $WebDomain . ", PubZoneID: " . $PublisherAdZoneID . ", AD: " . $AdName;
    	
    	\rtbsellv22\RtbSellV22Logger::get_instance()->min_log[] = $log_header;
    	
    	$log = "----------------------------------------------------------------";
    	$log.= "\nDate: " 		. date('m-d-Y H:i:s');
    	$log.= "\nHeader Bid Other Exchange Direct Tag Win";
    	$log.= "\nTotal Bids: 1";
    	$log.= "\nBids Won: 0" ;
    	$log.= "\nBids Lost: 1" ;
    	$log.= "\nBid Errors: 0";
    	$log.= "\nError List: ";
    	
    	$log.= "\n----------------------------------------------------------------\n";
    	
    	\rtbsellv22\RtbSellV22Logger::get_instance()->log[] = $log;
    	\rtbsellv22\RtbSellV22Logger::get_instance()->min_log[] = $log;
    	
    	
    }
    
    
    public static function get_params_from_bid_request($config, &$RtbBidRequest) {

    	if (!isset($RtbBidRequest->RtbBidRequestImpList[0]->tagid)
    		|| !isset($RtbBidRequest->RtbBidRequestImpList[0]->RtbBidRequestBanner)):
    	
    		return false;

    	endif;
    	
    	$PublisherAdZoneID 				= $RtbBidRequest->RtbBidRequestImpList[0]->tagid;
    	
    	$domain							= $RtbBidRequest->RtbBidRequestSite->domain;
    	$page							= $RtbBidRequest->RtbBidRequestSite->page;
    	$banner_width					= $RtbBidRequest->RtbBidRequestImpList[0]->RtbBidRequestBanner->w;
    	$banner_height					= $RtbBidRequest->RtbBidRequestImpList[0]->RtbBidRequestBanner->h;

    	$params 								= array();
    	$params["PublisherAdZoneID"] 			= $PublisherAdZoneID;
    	$params["domain"] 						= $domain;
    	$params["page"] 						= $page;
    	$params["banner_width"] 				= $banner_width;
    	$params["banner_height"] 				= $banner_height;
    	 
    	return $params;
    
    }
    
    public static function store_rtb_matching_line_items($config, $params, $line_items) {

    	$cache_time_minutes 		= intval($config['settings']['header_bidding']['cache_time_minutes']);
    	
    	$one_minute_in_seconds 		= 60;
    	
    	$cache_time_minutes 		= $cache_time_minutes * $one_minute_in_seconds;
    	
    	\util\CacheSql::put_cached_read_result_apc($config, $params, self::$class_name, $line_items, $cache_time_minutes);

    }
    
    public static function get_stored_rtb_matching_line_items($config, $params) {
    	
    	$stored_response = \util\CacheSql::get_cached_read_result_apc($config, $params, self::$class_name);
    	
    	if ($stored_response == null):
    		return $stored_response;
    	endif;
    	
    	return $stored_response;
    	
    }
    
}

