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

    public static function rebuild_header_bidder($config, $rebuild_header_id) {
    	
    	$HeaderBiddingPageFactory = \_factory\HeaderBiddingPage::get_instance();
    	$HeaderBiddingAdUnitFactory = \_factory\HeaderBiddingAdUnit::get_instance();
    	$PublisherAdZoneFactory = \_factory\PublisherAdZone::get_instance();
    	
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
	    		
	    			$ad_unit['sizes']			= '[[' . $HeaderBiddingAdUnit->Width . ', ' . $HeaderBiddingAdUnit->Height . ']]';
	    		
	    		endif;
	    		
	    		$bidder						= array();
	    		
	    		$bidder['bidder']			= $HeaderBiddingAdUnit->AdExchange;
	    		
	    		$custom_params =  unserialize($HeaderBiddingAdUnit->CustomParams);
	    		
	    		if ($custom_params != null && count($custom_params)):
	    		
		    		foreach ($header_bid_params as $header_bid_param):
						
		    			if (isset($custom_params[$header_bid_param])):
		    		
		    				$bidder['params'][$header_bid_param] = $custom_params[$header_bid_param];
		    			
		    			endif;
		    			
		    		endforeach;
		    		
	    		endif;
	    		
	    		$bidder['params']['hb_nginad_bidder_id'] = $HeaderBiddingAdUnit->HeaderBiddingAdUnitID;
	    		$ad_unit['bids'][]			= $bidder;

	    	endforeach;
	    	
	    	$ad_units[]						= $ad_unit;
	    	
    	endforeach;

    	$prebid_helper_template				= file_get_contents('public/js/prebid/nginad.prebid.template.js');	
    	
    	$encoded_data	 					= json_encode($ad_units, JSON_PRETTY_PRINT);
    	
    	$site_url							= $config['delivery']['site_url'];
    	
    	$site_url							= str_replace(array('http://', 'https://'), array('', ''), $site_url);
    	
    	$prebid_helper_template				= str_replace(array('__ADUNITS__', '__NGINAD_SERVER_DOMAIN__'), array($encoded_data, $site_url), $prebid_helper_template);
    	
    	fwrite($file_handle, $prebid_helper_template);
    					
    	fclose($file_handle);

    }
}


