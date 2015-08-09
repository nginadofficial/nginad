<?php
/**
 * NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2016 NginAd Foundation. All Rights Reserved
 * @license GPLv3
 */

namespace util;

class CachedStatsWrites {
	
	public static function incrementSellSideBidsCounterCached($config, \model\SellSidePartnerHourlyBids $SellSidePartnerHourlyBids) {
	
		$params = array();
		$params["SellSidePartnerID"] = $SellSidePartnerHourlyBids->SellSidePartnerID;
		$params["PublisherAdZoneID"] = $SellSidePartnerHourlyBids->PublisherAdZoneID;
		
		$class_dir_name = 'SellSidePartnerHourlyBids';
		 
		// WAIT FOR SYNCHRONIZED LOCK TO BE FREED
		\util\CacheSql::wait_for_reset_lock($config, $params, $class_dir_name);
		
		$cached_key_exists = \util\CacheSql::does_cached_write_exist_apc($config, $params, $class_dir_name);
		 
		if ($cached_key_exists):
		
			// increment bucket
			self::increment_cached_write_result_sellside_bids_apc($config, $params, $class_dir_name, $SellSidePartnerHourlyBids);
			 
		else:

			/*
			 * DO THIS BEFORE APC RESET OPERATIONS TO AVOID THREAD-LIKE DUPLICATION DUE TO THE LACK OF
			 * A SYNCHRONIZED KEYWORD IN PHP
			 */

			// SYNCHRONIZED BLOCK START
			\util\CacheSql::create_reset_write_lock($config, $params, $class_dir_name);

			// get value sum from apc
			$current = \util\CacheSql::get_cached_read_result_apc_type_convert($config, $params, $class_dir_name);
		
			// delete existing key - reset bucket
			\util\CacheSql::delete_cached_write_apc($config, $params, $class_dir_name);
			
			// increment bucket
			self::increment_cached_write_result_sellside_bids_apc($config, $params, $class_dir_name, $SellSidePartnerHourlyBids);
			
			// SYNCHRONIZED BLOCK END
			\util\CacheSql::reset_write_unlock($config, $params, $class_dir_name);

			if ($current != null):
				// write out values
				$SellSidePartnerHourlyBids->BidsWonCounter 					= $current["BidsWonCounter"];
				$SellSidePartnerHourlyBids->BidsLostCounter 				= $current["BidsLostCounter"];
				$SellSidePartnerHourlyBids->BidsErrorCounter 				= $current["BidsErrorCounter"];
				$SellSidePartnerHourlyBids->SpendTotalGross 				= floatval($current["SpendTotalGross"]);
				$SellSidePartnerHourlyBids->SpendTotalPrivateExchangeGross 	= floatval($current["SpendTotalPrivateExchangeGross"]);
				$SellSidePartnerHourlyBids->SpendTotalNet 					= floatval($current["SpendTotalNet"]);
				
				self::incrementSellSideBidsCounter($SellSidePartnerHourlyBids);
			endif;
		

		endif;
	
	}
	
	public static function incrementSellSideBidsCounter(\model\SellSidePartnerHourlyBids $SellSidePartnerHourlyBidsToAdd) {
	
		$SellSidePartnerHourlyBidsFactory = \_factory\SellSidePartnerHourlyBids::get_instance();
	
		$current_hour = date("m/d/Y H");
		
		$params = array();
		$params["SellSidePartnerID"] 	= $SellSidePartnerHourlyBidsToAdd->SellSidePartnerID;
		$params["PublisherAdZoneID"] 	= $SellSidePartnerHourlyBidsToAdd->PublisherAdZoneID;
		$params["MDYH"] 				= $current_hour;
		$SellSidePartnerHourlyBids 		= $SellSidePartnerHourlyBidsFactory->get_row($params);
	
		$sellside_partner_hourly_bids_counter 						= new \model\SellSidePartnerHourlyBids();
		$sellside_partner_hourly_bids_counter->SellSidePartnerID 	= $SellSidePartnerHourlyBidsToAdd->SellSidePartnerID;
		$sellside_partner_hourly_bids_counter->PublisherAdZoneID 	= $SellSidePartnerHourlyBidsToAdd->PublisherAdZoneID;
		

		if ($SellSidePartnerHourlyBids != null):
		
			$sellside_partner_hourly_bids_counter->SellSidePartnerHourlyBidsID 		= $SellSidePartnerHourlyBids->SellSidePartnerHourlyBidsID;
			$sellside_partner_hourly_bids_counter->BidsWonCounter 					= $SellSidePartnerHourlyBids->BidsWonCounter + $SellSidePartnerHourlyBidsToAdd->BidsWonCounter;
			$sellside_partner_hourly_bids_counter->BidsLostCounter 					= $SellSidePartnerHourlyBids->BidsLostCounter + $SellSidePartnerHourlyBidsToAdd->BidsLostCounter;
			$sellside_partner_hourly_bids_counter->BidsErrorCounter 				= $SellSidePartnerHourlyBids->BidsErrorCounter + $SellSidePartnerHourlyBidsToAdd->BidsErrorCounter;
			$sellside_partner_hourly_bids_counter->SpendTotalGross 					= floatval($SellSidePartnerHourlyBids->SpendTotalGross) + $SellSidePartnerHourlyBidsToAdd->SpendTotalGross;
			$sellside_partner_hourly_bids_counter->SpendTotalPrivateExchangeGross 	= floatval($SellSidePartnerHourlyBids->SpendTotalPrivateExchangeGross) + $SellSidePartnerHourlyBidsToAdd->SpendTotalPrivateExchangeGross;
			$sellside_partner_hourly_bids_counter->SpendTotalNet 					= floatval($SellSidePartnerHourlyBids->SpendTotalNet) + $SellSidePartnerHourlyBidsToAdd->SpendTotalNet;
			$SellSidePartnerHourlyBidsFactory->updateSellSidePartnerHourlyBids($sellside_partner_hourly_bids_counter);
			
		else:
		
			$sellside_partner_hourly_bids_counter->MDYH 							= $current_hour;
			$sellside_partner_hourly_bids_counter->BidsWonCounter 					= $SellSidePartnerHourlyBidsToAdd->BidsWonCounter;
			$sellside_partner_hourly_bids_counter->BidsLostCounter 					= $SellSidePartnerHourlyBidsToAdd->BidsLostCounter;
			$sellside_partner_hourly_bids_counter->BidsErrorCounter 				= $SellSidePartnerHourlyBidsToAdd->BidsErrorCounter;
			$sellside_partner_hourly_bids_counter->SpendTotalGross 					= $SellSidePartnerHourlyBidsToAdd->SpendTotalGross;
			$sellside_partner_hourly_bids_counter->SpendTotalPrivateExchangeGross 	= $SellSidePartnerHourlyBidsToAdd->SpendTotalPrivateExchangeGross;
			$sellside_partner_hourly_bids_counter->SpendTotalNet 					= $SellSidePartnerHourlyBidsToAdd->SpendTotalNet;
			$sellside_partner_hourly_bids_counter->DateCreated 						= date("Y-m-d H:i:s");
			$SellSidePartnerHourlyBidsFactory->insertSellSidePartnerHourlyBids($sellside_partner_hourly_bids_counter);
		endif;
	
	}
	
	public static function incrementPublisherBidsCounterCached($config, \model\PublisherHourlyBids $PublisherHourlyBids) {
	
		$params = array();
		$params["PublisherAdZoneID"] = $PublisherHourlyBids->PublisherAdZoneID;
	
		$class_dir_name = 'PublisherHourlyBids';
			
		// WAIT FOR SYNCHRONIZED LOCK TO BE FREED
		\util\CacheSql::wait_for_reset_lock($config, $params, $class_dir_name);

		$cached_key_exists = \util\CacheSql::does_cached_write_exist_apc($config, $params, $class_dir_name);
			
		if ($cached_key_exists):
	
			// increment bucket
			self::increment_cached_write_result_publisher_bids_apc($config, $params, $class_dir_name, $PublisherHourlyBids);
		
		else:
		
			/*
			 * DO THIS BEFORE APC RESET OPERATIONS TO AVOID THREAD-LIKE DUPLICATION DUE TO THE LACK OF
			 * A SYNCHRONIZED KEYWORD IN PHP
			 */
		
			// SYNCHRONIZED BLOCK START
			\util\CacheSql::create_reset_write_lock($config, $params, $class_dir_name);

			// get value sum from apc
			$current = \util\CacheSql::get_cached_read_result_apc_type_convert($config, $params, $class_dir_name);
		
			// delete existing key - reset bucket
			\util\CacheSql::delete_cached_write_apc($config, $params, $class_dir_name);
			
			// increment bucket
			self::increment_cached_write_result_publisher_bids_apc($config, $params, $class_dir_name, $PublisherHourlyBids);
			
			// SYNCHRONIZED BLOCK END
			\util\CacheSql::reset_write_unlock($config, $params, $class_dir_name);

			if ($current != null):
				// write out values
				$PublisherHourlyBids->AuctionCounter 					= $current["AuctionCounter"];
				$PublisherHourlyBids->BidsWonCounter 					= $current["BidsWonCounter"];
				$PublisherHourlyBids->BidsLostCounter 					= $current["BidsLostCounter"];
				$PublisherHourlyBids->BidsErrorCounter 					= $current["BidsErrorCounter"];
				$PublisherHourlyBids->SpendTotalGross 					= floatval($current["SpendTotalGross"]);
				$PublisherHourlyBids->SpendTotalPrivateExchangeGross 	= floatval($current["SpendTotalPrivateExchangeGross"]);
				$PublisherHourlyBids->SpendTotalNet 					= floatval($current["SpendTotalNet"]);
			
				self::incrementPublisherBidsCounter($PublisherHourlyBids);
			endif;

		endif;
	
	}
	
	public static function incrementPublisherBidsCounter(\model\PublisherHourlyBids $PublisherHourlyBidsToAdd) {
	
		$PublisherHourlyBidsFactory = \_factory\PublisherHourlyBids::get_instance();
	
		$current_hour = date("m/d/Y H");
	
		$params = array();
		$params["PublisherAdZoneID"] 		= $PublisherHourlyBidsToAdd->PublisherAdZoneID;
		$params["MDYH"] 					= $current_hour;
		$PublisherHourlyBids 				= $PublisherHourlyBidsFactory->get_row($params);
	
		$publisher_hourly_bids_counter 							= new \model\PublisherHourlyBids();
		$publisher_hourly_bids_counter->PublisherAdZoneID 		= $PublisherHourlyBidsToAdd->PublisherAdZoneID;
	
	
		if ($PublisherHourlyBids != null):
	
			$publisher_hourly_bids_counter->PublisherHourlyBidsID 					= $PublisherHourlyBids->PublisherHourlyBidsID;
			$publisher_hourly_bids_counter->AuctionCounter 							= $PublisherHourlyBids->AuctionCounter + $PublisherHourlyBidsToAdd->AuctionCounter;
			$publisher_hourly_bids_counter->BidsWonCounter 							= $PublisherHourlyBids->BidsWonCounter + $PublisherHourlyBidsToAdd->BidsWonCounter;
			$publisher_hourly_bids_counter->BidsLostCounter 						= $PublisherHourlyBids->BidsLostCounter + $PublisherHourlyBidsToAdd->BidsLostCounter;
			$publisher_hourly_bids_counter->BidsErrorCounter 						= $PublisherHourlyBids->BidsErrorCounter + $PublisherHourlyBidsToAdd->BidsErrorCounter;
			$publisher_hourly_bids_counter->SpendTotalGross 						= floatval($PublisherHourlyBids->SpendTotalGross) + $PublisherHourlyBidsToAdd->SpendTotalGross;
			$publisher_hourly_bids_counter->SpendTotalPrivateExchangeGross 			= floatval($PublisherHourlyBids->SpendTotalPrivateExchangeGross) + $PublisherHourlyBidsToAdd->SpendTotalPrivateExchangeGross;
			$publisher_hourly_bids_counter->SpendTotalNet 							= floatval($PublisherHourlyBids->SpendTotalNet) + $PublisherHourlyBidsToAdd->SpendTotalNet;
			$PublisherHourlyBidsFactory->updatePublisherHourlyBids($publisher_hourly_bids_counter);
				
		else:
	
			$publisher_hourly_bids_counter->MDYH 							= $current_hour;
			$publisher_hourly_bids_counter->AuctionCounter 					= $PublisherHourlyBidsToAdd->AuctionCounter;
			$publisher_hourly_bids_counter->BidsWonCounter 					= $PublisherHourlyBidsToAdd->BidsWonCounter;
			$publisher_hourly_bids_counter->BidsLostCounter 				= $PublisherHourlyBidsToAdd->BidsLostCounter;
			$publisher_hourly_bids_counter->BidsErrorCounter				= $PublisherHourlyBidsToAdd->BidsErrorCounter;
			$publisher_hourly_bids_counter->SpendTotalGross 				= $PublisherHourlyBidsToAdd->SpendTotalGross;
			$publisher_hourly_bids_counter->SpendTotalPrivateExchangeGross 	= $PublisherHourlyBidsToAdd->SpendTotalPrivateExchangeGross;
			$publisher_hourly_bids_counter->SpendTotalNet 					= $PublisherHourlyBidsToAdd->SpendTotalNet;
			$publisher_hourly_bids_counter->DateCreated 					= date("Y-m-d H:i:s");
			$PublisherHourlyBidsFactory->insertPublisherHourlyBids($publisher_hourly_bids_counter);
		endif;
	
	}
	
	public static function increment_cached_write_result_publisher_bids_apc($config, $params, $class_name, \model\PublisherHourlyBids $PublisherHourlyBidsToAdd) {
	
		$current = \util\CacheSql::get_cached_read_result_apc_type_convert($config, $params, $class_name);
	
		if ($current !== null):
		
			$existing_auction_counter = intval($current["AuctionCounter"]);
			$existing_auction_counter += $PublisherHourlyBidsToAdd->AuctionCounter;
		
			$existing_bids_won_counter = intval($current["BidsWonCounter"]);
			$existing_bids_won_counter += $PublisherHourlyBidsToAdd->BidsWonCounter;
		
			$existing_bids_lost_counter = intval($current["BidsLostCounter"]);
			$existing_bids_lost_counter += $PublisherHourlyBidsToAdd->BidsLostCounter;
				
			$existing_bids_error_counter = intval($current["BidsErrorCounter"]);
			$existing_bids_error_counter += $PublisherHourlyBidsToAdd->BidsErrorCounter;
				
			$existing_bids_spend_total_gross = floatval($current["SpendTotalGross"]);
			$existing_bids_spend_total_gross += $PublisherHourlyBidsToAdd->SpendTotalGross;
		
			$existing_bids_spend_total_private_exchange_gross = floatval($current["SpendTotalPrivateExchangeGross"]);
			$existing_bids_spend_total_private_exchange_gross += $PublisherHourlyBidsToAdd->SpendTotalPrivateExchangeGross;
			
			$existing_bids_spend_total_net = floatval($current["SpendTotalNet"]);
			$existing_bids_spend_total_net += $PublisherHourlyBidsToAdd->SpendTotalNet;
				
		else:
	
			$existing_auction_counter							= $PublisherHourlyBidsToAdd->AuctionCounter;
			$existing_bids_won_counter							= $PublisherHourlyBidsToAdd->BidsWonCounter;
			$existing_bids_lost_counter							= $PublisherHourlyBidsToAdd->BidsLostCounter;
			$existing_bids_error_counter						= $PublisherHourlyBidsToAdd->BidsErrorCounter;
			$existing_bids_spend_total_gross					= $PublisherHourlyBidsToAdd->SpendTotalGross;
			$existing_bids_spend_total_private_exchange_gross	= $PublisherHourlyBidsToAdd->SpendTotalPrivateExchangeGross;
			$existing_bids_spend_total_net						= $PublisherHourlyBidsToAdd->SpendTotalNet;
				
		endif;
	
		// cache up to 1 hour, the write the the db should occur before that.
		\util\CacheSql::put_cached_read_result_apc($config,
				$params,
				$class_name,
				array(
						"AuctionCounter"						=> $existing_auction_counter,
						"BidsWonCounter"						=> $existing_bids_won_counter,
						"BidsLostCounter"						=> $existing_bids_lost_counter,
						"BidsErrorCounter"						=> $existing_bids_error_counter,
						"SpendTotalGross"						=> $existing_bids_spend_total_gross,
						"SpendTotalPrivateExchangeGross"		=> $existing_bids_spend_total_private_exchange_gross,
						"SpendTotalNet"							=> $existing_bids_spend_total_net
	
				),
				3600
			);
	
		$timer_name = 'write_timer';
		$write_timer = \util\CacheSql::get_cached_read_result_apc($config, $params, $class_name . $timer_name);
		if ($write_timer == null) :
			/*
			 * 60 second write timer, when the apc cache value is gone the
			* contents are written the DB and the apc value is cleared
			*/
			\util\CacheSql::put_cached_read_result_apc($config, $params, $class_name . $timer_name, array($timer_name=>true), 60);
		endif;
	
	}
	
	
	public static function increment_cached_write_result_sellside_bids_apc($config, $params, $class_name, \model\SellSidePartnerHourlyBids $SellSidePartnerHourlyBidsToAdd) {
	
		$current = \util\CacheSql::get_cached_read_result_apc_type_convert($config, $params, $class_name);
	
		if ($current !== null):
		
			$existing_bids_won_counter = intval($current["BidsWonCounter"]);
			$existing_bids_won_counter += $SellSidePartnerHourlyBidsToAdd->BidsWonCounter;
		
			$existing_bids_lost_counter = intval($current["BidsLostCounter"]);
			$existing_bids_lost_counter += $SellSidePartnerHourlyBidsToAdd->BidsLostCounter;
			
			$existing_bids_error_counter = intval($current["BidsErrorCounter"]);
			$existing_bids_error_counter += $SellSidePartnerHourlyBidsToAdd->BidsErrorCounter;
			
			$existing_bids_spend_total_gross = floatval($current["SpendTotalGross"]);
			$existing_bids_spend_total_gross += $SellSidePartnerHourlyBidsToAdd->SpendTotalGross;

			$existing_bids_spend_total_private_exchange_gross = floatval($current["SpendTotalPrivateExchangeGross"]);
			$existing_bids_spend_total_private_exchange_gross += $SellSidePartnerHourlyBidsToAdd->SpendTotalPrivateExchangeGross;
			
			$existing_bids_spend_total_net = floatval($current["SpendTotalNet"]);
			$existing_bids_spend_total_net += $SellSidePartnerHourlyBidsToAdd->SpendTotalNet;
			
		else: 
		
			$existing_bids_won_counter							= $SellSidePartnerHourlyBidsToAdd->BidsWonCounter;
			$existing_bids_lost_counter							= $SellSidePartnerHourlyBidsToAdd->BidsLostCounter;
			$existing_bids_error_counter						= $SellSidePartnerHourlyBidsToAdd->BidsErrorCounter;
			$existing_bids_spend_total_gross					= $SellSidePartnerHourlyBidsToAdd->SpendTotalGross;
			$existing_bids_spend_total_private_exchange_gross	= $SellSidePartnerHourlyBidsToAdd->SpendTotalPrivateExchangeGross;
			$existing_bids_spend_total_net						= $SellSidePartnerHourlyBidsToAdd->SpendTotalNet;
			
		endif;
	
		// cache up to 1 hour, the write the the db should occur before that.
		\util\CacheSql::put_cached_read_result_apc($config, 
				$params, 
				$class_name, 
				array(
						"BidsWonCounter"						=> $existing_bids_won_counter, 
						"BidsLostCounter"						=> $existing_bids_lost_counter,
						"BidsErrorCounter"						=> $existing_bids_error_counter,
						"SpendTotalGross"						=> $existing_bids_spend_total_gross,
						"SpendTotalPrivateExchangeGross"		=> $existing_bids_spend_total_private_exchange_gross,
						"SpendTotalNet"							=> $existing_bids_spend_total_net
						
					),
				3600
			);
	
		$timer_name = 'write_timer';
		$write_timer = \util\CacheSql::get_cached_read_result_apc($config, $params, $class_name . $timer_name);
		if ($write_timer == null) :
			/*
			 * 60 second write timer, when the apc cache value is gone the
			* contents are written the DB and the apc value is cleared
			*/
			\util\CacheSql::put_cached_read_result_apc($config, $params, $class_name . $timer_name, array($timer_name=>true), 60);
		endif;
	
	}
	
	public static function increment_cached_write_result_private_exchange_channel_stats($config, $params, $class_name, $method_params) {
	
    	$publisher_website_id = $method_params["publisher_website_id"] = $method_params["rtb_channel_site_id"];
    	$rtb_channel_site_name = $method_params["rtb_channel_site_name"];
    	$impressions_offered_counter = $method_params["impressions_offered_counter"];
    	$auction_bids_counter = $method_params["auction_bids_counter"];
    	$spend_offered_in_bids = $method_params["spend_offered_in_bids"];
    	$floor_price_if_any = $method_params["floor_price_if_any"];
	
		$current = \util\CacheSql::get_cached_read_result_apc_type_convert($config, $params, $class_name);
	
		if ($current !== null):
		
			if (isset($current[$publisher_website_id])):
			
				$existing_impressions_offered_counter = intval($current[$publisher_website_id]["impressions_offered_counter"]);
				$impressions_offered_counter += $existing_impressions_offered_counter;
				$current[$publisher_website_id]["impressions_offered_counter"] = intval($impressions_offered_counter);
			
				$existing_auction_bids_counter = floatval($current[$publisher_website_id]["auction_bids_counter"]);
				$auction_bids_counter += $existing_auction_bids_counter;
				$current[$publisher_website_id]["auction_bids_counter"] = intval($auction_bids_counter);
			
				$existing_spend_offered_in_bids = floatval($current[$publisher_website_id]["spend_offered_in_bids"]);
				$spend_offered_in_bids += $existing_spend_offered_in_bids;
				$current[$publisher_website_id]["spend_offered_in_bids"] = floatval($spend_offered_in_bids);
			
			else:
				
				$current[$publisher_website_id] = $method_params;
		
			endif;
	
		else:
				
			$current = array();
			$current[$publisher_website_id] = $method_params;
				
		endif;
	
		// cache up to 1 hour, the write the the db should occur before that.
		\util\CacheSql::put_cached_read_result_apc(
				$config,
				$params,
				$class_name,
				$current,
				3600
		);
	
		$timer_name = 'write_timer';
		$write_timer = \util\CacheSql::get_cached_read_result_apc($config, $params, $class_name . $timer_name);
	
		if ($write_timer == null):
	
		/*
		 * 60 second write timer, when the apc cache value is gone the
		* contents are written the DB and the apc value is cleared
		*/
		\util\CacheSql::put_cached_read_result_apc($config, $params, $class_name . $timer_name, array($timer_name=>true), 60);
		endif;
	
	}
	
	public static function increment_cached_write_result_ssp_rtb_channel_stats($config, $params, $class_name, $method_params) {
		
		$buyside_partner_name = $method_params["buyside_partner_name"];
		$rtb_channel_site_id = $method_params["rtb_channel_site_id"];
		$rtb_channel_site_name = $method_params["rtb_channel_site_name"];
		$rtb_channel_site_domain = $method_params["rtb_channel_site_domain"];
		$rtb_channel_site_iab_category = $method_params["rtb_channel_site_iab_category"];
		$rtb_channel_publisher_name = $method_params["rtb_channel_publisher_name"];
		$impressions_offered_counter = $method_params["impressions_offered_counter"];
		$auction_bids_counter = $method_params["auction_bids_counter"];
		$spend_offered_in_bids = $method_params["spend_offered_in_bids"];
		$floor_price_if_any = $method_params["floor_price_if_any"];
		
		$current = \util\CacheSql::get_cached_read_result_apc_type_convert($config, $params, $class_name);
	
		if ($current !== null):
		
			if (isset($current[$rtb_channel_site_id])):
			
				$existing_impressions_offered_counter = intval($current[$rtb_channel_site_id]["impressions_offered_counter"]);
				$impressions_offered_counter += $existing_impressions_offered_counter;
				$current[$rtb_channel_site_id]["impressions_offered_counter"] = intval($impressions_offered_counter);
				
				$existing_auction_bids_counter = floatval($current[$rtb_channel_site_id]["auction_bids_counter"]);
				$auction_bids_counter += $existing_auction_bids_counter;
				$current[$rtb_channel_site_id]["auction_bids_counter"] = intval($auction_bids_counter);
				
				$existing_spend_offered_in_bids = floatval($current[$rtb_channel_site_id]["spend_offered_in_bids"]);
				$spend_offered_in_bids += $existing_spend_offered_in_bids;
				$current[$rtb_channel_site_id]["spend_offered_in_bids"] = floatval($spend_offered_in_bids);
				
			else:
				
				$current[$rtb_channel_site_id] = $method_params;
				
			endif;
			
		else: 
			
			$current = array();
			$current[$rtb_channel_site_id] = $method_params;

		endif;

		// cache up to 1 hour, the write the the db should occur before that.
		\util\CacheSql::put_cached_read_result_apc(
				$config, 
				$params, 
				$class_name, 
				$current,
				3600
		);

		$timer_name = 'write_timer';
		$write_timer = \util\CacheSql::get_cached_read_result_apc($config, $params, $class_name . $timer_name);
	
		if ($write_timer == null):
	
			/*
			 * 60 second write timer, when the apc cache value is gone the
			* contents are written the DB and the apc value is cleared
			*/
			\util\CacheSql::put_cached_read_result_apc($config, $params, $class_name . $timer_name, array($timer_name=>true), 60);
		endif;
	
	}
	
	
	public static function increment_cached_write_result_impressions_spend_apc($config, $params, $class_name, $impressions_value, $spend_value_gross, $spend_value_net) {
	
		$current = \util\CacheSql::get_cached_read_result_apc($config, $params, $class_name);
	
		if ($current !== null):
	
			$existing_impressions = intval($current["impressions"]);
			$impressions_value += $existing_impressions;
				
			$existing_spend_gross = floatval($current["spend_gross"]);
			$spend_value_gross += $existing_spend_gross;
			
			$existing_spend_net = floatval($current["spend_net"]);
			$spend_value_net += $existing_spend_net;
			
		endif;
		
		// cache up to 1 hour, the write the the db should occur before that.
		\util\CacheSql::put_cached_read_result_apc($config, $params, $class_name, array("impressions"=>intval($impressions_value), "spend_gross"=>floatval($spend_value_gross), "spend_net"=>floatval($spend_value_net)), 3600);
		
		$timer_name = 'write_timer';
		$write_timer = \util\CacheSql::get_cached_read_result_apc($config, $params, $class_name . $timer_name);
		
		if ($write_timer == null):
		
			/*
			 * 60 second write timer, when the apc cache value is gone the
			* contents are written the DB and the apc value is cleared
			*/
			\util\CacheSql::put_cached_read_result_apc($config, $params, $class_name . $timer_name, array($timer_name=>true), 60);
		endif;
	
	}
	
	public static function increment_cached_write_result_int_apc($config, $params, $class_name, $value, $ttl = 60) {
	
		$current = \util\CacheSql::get_cached_read_result_apc($config, $params, $class_name);
	
		if ($current !== null):
			$existing_value = intval($current["value"]);
			$value += $existing_value;
		endif;
		
		// cache up to 1 hour, the write the the db should occur before that.
		\util\CacheSql::put_cached_read_result_apc($config, $params, $class_name, array("value"=>intval($value)), 3600);
		
		$timer_name = 'write_timer';
		$write_timer = \util\CacheSql::get_cached_read_result_apc($config, $params, $class_name . $timer_name);
		if ($write_timer == null) :
			/*
			 * 60 second write timer, when the apc cache value is gone the
			* contents are written the DB and the apc value is cleared
			*/
			\util\CacheSql::put_cached_read_result_apc($config, $params, $class_name . $timer_name, array($timer_name=>true), 60);
		endif;
	
	}
	
}
