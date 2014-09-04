<?php
/**
 * CDNPAL NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2015 CDNPAL Ltd. All Rights Reserved
 * @license GPLv3
 */

namespace util;

class CachedStatsWrites {
	
	public static function incrementContractPublisherZoneHourlyImpressionsCached($config, \model\ContractPublisherZoneHourlyImpressions $ContractPublisherZoneHourlyImpressions) {

		$params = array();
		$params["AdCampaignBannerID"] 	= $ContractPublisherZoneHourlyImpressions->AdCampaignBannerID;
		$params["PublisherAdZoneID"] 	= $ContractPublisherZoneHourlyImpressions->PublisherAdZoneID;
		
		$class_dir_name = 'ContractPublisherZoneHourlyImpressions';
			
		$cached_key_exists = \util\CacheSql::does_cached_write_exist_apc($config, $params, $class_dir_name);
			
		if ($cached_key_exists):
			
			// increment bucket
			self::increment_cached_write_result_contract_publisher_zone_impressions_apc($config, $params, $class_dir_name, $ContractPublisherZoneHourlyImpressions);
			
		else:
			
			// get value sum from apc
			$current = \util\CacheSql::get_cached_read_result_apc($config, $params, $class_dir_name);
			
			if ($current != null):
				// write out values
				$ContractPublisherZoneHourlyImpressions->Impressions 		+= $current["Impressions"];
				$ContractPublisherZoneHourlyImpressions->SpendTotalGross 	+= floatval($current["SpendTotalGross"]);
				$ContractPublisherZoneHourlyImpressions->SpendTotalNet 		+= floatval($current["SpendTotalNet"]);
				
				self::incrementContractPublisherZoneHourlyImpressions($ContractPublisherZoneHourlyImpressions);
			endif;
			
			// delete existing key - reset bucket
			\util\CacheSql::delete_cached_write_apc($config, $params, $class_dir_name);
			
			// increment bucket
			self::increment_cached_write_result_contract_publisher_zone_impressions_apc($config, $params, $class_dir_name, $ContractPublisherZoneHourlyImpressions);
			
		endif;
		
	}
	
	public static function incrementContractPublisherZoneHourlyImpressions(\model\ContractPublisherZoneHourlyImpressions $ContractPublisherZoneHourlyImpressionsToAdd) {
	
		$ContractPublisherZoneHourlyImpressionsFactory = \_factory\ContractPublisherZoneHourlyImpressions::get_instance();
	
		$current_hour = date("m/d/Y h");
	
		$params = array();
		$params["AdCampaignBannerID"] 				= $ContractPublisherZoneHourlyImpressionsToAdd->AdCampaignBannerID;
		$params["PublisherAdZoneID"] 				= $ContractPublisherZoneHourlyImpressionsToAdd->PublisherAdZoneID;
		$params["MDYH"] 							= $current_hour;
		$ContractPublisherZoneHourlyImpressions 	= $ContractPublisherZoneHourlyImpressionsFactory->get_row($params);
	
		$contract_publisher_zone_hourly_counter 						= new \model\ContractPublisherZoneHourlyImpressions();
		$contract_publisher_zone_hourly_counter->AdCampaignBannerID 	= $ContractPublisherZoneHourlyImpressionsToAdd->AdCampaignBannerID;
		$contract_publisher_zone_hourly_counter->PublisherAdZoneID 		= $ContractPublisherZoneHourlyImpressionsToAdd->PublisherAdZoneID;
	

		if ($ContractPublisherZoneHourlyImpressions != null):
	
			$contract_publisher_zone_hourly_counter->Impressions 					= $ContractPublisherZoneHourlyImpressions->Impressions + $ContractPublisherZoneHourlyImpressionsToAdd->Impressions;
			$contract_publisher_zone_hourly_counter->SpendTotalGross 				= floatval($ContractPublisherZoneHourlyImpressions->SpendTotalGross) + $ContractPublisherZoneHourlyImpressionsToAdd->SpendTotalGross;
			$contract_publisher_zone_hourly_counter->SpendTotalNet 					= floatval($ContractPublisherZoneHourlyImpressions->SpendTotalNet) + $ContractPublisherZoneHourlyImpressionsToAdd->SpendTotalNet;
			$ContractPublisherZoneHourlyImpressionsFactory->updateContractPublisherZoneHourlyImpressions($contract_publisher_zone_hourly_counter);
				
		else:

			$contract_publisher_zone_hourly_counter->MDYH 				= $current_hour;
			$contract_publisher_zone_hourly_counter->Impressions 		= $ContractPublisherZoneHourlyImpressionsToAdd->Impressions;
			$contract_publisher_zone_hourly_counter->SpendTotalGross 	= $ContractPublisherZoneHourlyImpressionsToAdd->SpendTotalGross;
			$contract_publisher_zone_hourly_counter->SpendTotalNet 		= $ContractPublisherZoneHourlyImpressionsToAdd->SpendTotalNet;
			$contract_publisher_zone_hourly_counter->DateCreated 		= date("Y-m-d H:i:s");
			$ContractPublisherZoneHourlyImpressionsFactory->insertContractPublisherZoneHourlyImpressions($contract_publisher_zone_hourly_counter);
			
		endif;
	
	}	
	
	public static function increment_cached_write_result_contract_publisher_zone_impressions_apc($config, $params, $class_name, \model\ContractPublisherZoneHourlyImpressions $ContractPublisherZoneHourlyImpressionsToAdd) {
	
		$current = \util\CacheSql::get_cached_read_result_apc($config, $params, $class_name);
	
		if ($current !== null):
		
			$existing_bids_impressions_counter 	= intval($current["Impressions"]);
			$existing_bids_impressions_counter 	+= $ContractPublisherZoneHourlyImpressionsToAdd->Impressions;
				
			$existing_bids_spend_total_gross 	= floatval($current["SpendTotalGross"]);
			$existing_bids_spend_total_gross 	+= $ContractPublisherZoneHourlyImpressionsToAdd->SpendTotalGross;
		
			$existing_bids_spend_total_net 		= floatval($current["SpendTotalNet"]);
			$existing_bids_spend_total_net 		+= $ContractPublisherZoneHourlyImpressionsToAdd->SpendTotalNet;
				
		else:
		
			$existing_bids_impressions_counter	= $ContractPublisherZoneHourlyImpressionsToAdd->Impressions;
			$existing_bids_spend_total_gross	= $ContractPublisherZoneHourlyImpressionsToAdd->SpendTotalGross;
			$existing_bids_spend_total_net		= $ContractPublisherZoneHourlyImpressionsToAdd->SpendTotalNet;
				
		endif;
	
		// cache up to 1 hour, the write the the db should occur before that.
		\util\CacheSql::put_cached_read_result_apc($config,
				$params,
				$class_name,
				array(
						"Impressions"			=> $existing_bids_impressions_counter,
						"SpendTotalGross"		=> $existing_bids_spend_total_gross,
						"SpendTotalNet"			=> $existing_bids_spend_total_net
	
				));
	
		$timer_name = 'write_timer';
		$write_timer = \util\CacheSql::get_cached_read_result_apc($config, $params, $class_name . $timer_name);
		if ($write_timer == null) :
			/*
			 * 60 second write timer, when the apc cache value is gone the
			* contents are written the DB and the apc value is cleared
			*/
			\util\CacheSql::put_cached_read_result_apc($config, $params, $class_name . $timer_name, array("write_timer"=>true), 60);
		endif;
	
	}
	
	public static function incrementSellSideBidsCounterCached($config, \model\SellSidePartnerHourlyBids $SellSidePartnerHourlyBids) {
	
		$params = array();
		$params["SellSidePartnerID"] = $SellSidePartnerHourlyBids->SellSidePartnerID;
		$params["PublisherAdZoneID"] = $SellSidePartnerHourlyBids->PublisherAdZoneID;
		
		$class_dir_name = 'SellSidePartnerHourlyBids';
		 
		$cached_key_exists = \util\CacheSql::does_cached_write_exist_apc($config, $params, $class_dir_name);
		 
		if ($cached_key_exists):

			// increment bucket
			self::increment_cached_write_result_sellside_bids_apc($config, $params, $class_dir_name, $SellSidePartnerHourlyBids);
			 
		else:

			// get value sum from apc
			$current = \util\CacheSql::get_cached_read_result_apc($config, $params, $class_dir_name);

			if ($current != null):
				// write out values
				$SellSidePartnerHourlyBids->BidsWonCounter 		+= $current["BidsWonCounter"];
				$SellSidePartnerHourlyBids->BidsLostCounter 	+= $current["BidsLostCounter"];
				$SellSidePartnerHourlyBids->BidsErrorCounter 	+= $current["BidsErrorCounter"];
				$SellSidePartnerHourlyBids->SpendTotalGross 	+= floatval($current["SpendTotalGross"]);
				$SellSidePartnerHourlyBids->SpendTotalNet 		+= floatval($current["SpendTotalNet"]);
				
				self::incrementSellSideBidsCounter($SellSidePartnerHourlyBids);
			endif;
		
			// delete existing key - reset bucket
			\util\CacheSql::delete_cached_write_apc($config, $params, $class_dir_name);
			 
			// increment bucket
			self::increment_cached_write_result_sellside_bids_apc($config, $params, $class_dir_name, $SellSidePartnerHourlyBids);
		
		endif;
	
	}
	
	public static function incrementSellSideBidsCounter(\model\SellSidePartnerHourlyBids $SellSidePartnerHourlyBidsToAdd) {
	
		$SellSidePartnerHourlyBidsFactory = \_factory\SellSidePartnerHourlyBids::get_instance();
	
		$current_hour = date("m/d/Y h");
		
		$params = array();
		$params["SellSidePartnerID"] 	= $SellSidePartnerHourlyBidsToAdd->SellSidePartnerID;
		$params["PublisherAdZoneID"] 	= $SellSidePartnerHourlyBidsToAdd->PublisherAdZoneID;
		$params["MDYH"] 				= $current_hour;
		$SellSidePartnerHourlyBids 		= $SellSidePartnerHourlyBidsFactory->get_row($params);
	
		$sellside_partner_hourly_bids_counter 						= new \model\SellSidePartnerHourlyBids();
		$sellside_partner_hourly_bids_counter->SellSidePartnerID 	= $SellSidePartnerHourlyBidsToAdd->SellSidePartnerID;
		$sellside_partner_hourly_bids_counter->PublisherAdZoneID 	= $SellSidePartnerHourlyBidsToAdd->PublisherAdZoneID;
		

		if ($SellSidePartnerHourlyBids != null):
		
			$sellside_partner_hourly_bids_counter->SellSidePartnerHourlyBidsID 	= $SellSidePartnerHourlyBids->SellSidePartnerHourlyBidsID;
			$sellside_partner_hourly_bids_counter->BidsWonCounter 				= $SellSidePartnerHourlyBids->BidsWonCounter + $SellSidePartnerHourlyBidsToAdd->BidsWonCounter;
			$sellside_partner_hourly_bids_counter->BidsLostCounter 				= $SellSidePartnerHourlyBids->BidsLostCounter + $SellSidePartnerHourlyBidsToAdd->BidsLostCounter;
			$sellside_partner_hourly_bids_counter->BidsErrorCounter 			= $SellSidePartnerHourlyBids->BidsErrorCounter + $SellSidePartnerHourlyBidsToAdd->BidsErrorCounter;
			$sellside_partner_hourly_bids_counter->SpendTotalGross 				= floatval($SellSidePartnerHourlyBids->SpendTotalGross) + $SellSidePartnerHourlyBidsToAdd->SpendTotalGross;
			$sellside_partner_hourly_bids_counter->SpendTotalNet 				= floatval($SellSidePartnerHourlyBids->SpendTotalNet) + $SellSidePartnerHourlyBidsToAdd->SpendTotalNet;
			$SellSidePartnerHourlyBidsFactory->updateSellSidePartnerHourlyBids($sellside_partner_hourly_bids_counter);
			
		else:
		
			$sellside_partner_hourly_bids_counter->MDYH 			= $current_hour;
			$sellside_partner_hourly_bids_counter->BidsWonCounter 	= $SellSidePartnerHourlyBidsToAdd->BidsWonCounter;
			$sellside_partner_hourly_bids_counter->BidsLostCounter 	= $SellSidePartnerHourlyBidsToAdd->BidsLostCounter;
			$sellside_partner_hourly_bids_counter->BidsErrorCounter = $SellSidePartnerHourlyBidsToAdd->BidsErrorCounter;
			$sellside_partner_hourly_bids_counter->SpendTotalGross 	= $SellSidePartnerHourlyBidsToAdd->SpendTotalGross;
			$sellside_partner_hourly_bids_counter->SpendTotalNet 	= $SellSidePartnerHourlyBidsToAdd->SpendTotalNet;
			$sellside_partner_hourly_bids_counter->DateCreated 		= date("Y-m-d H:i:s");
			$SellSidePartnerHourlyBidsFactory->insertSellSidePartnerHourlyBids($sellside_partner_hourly_bids_counter);
		endif;
	
	}
	
	public static function increment_cached_write_result_sellside_bids_apc($config, $params, $class_name, \model\SellSidePartnerHourlyBids $SellSidePartnerHourlyBidsToAdd) {
	
		$current = \util\CacheSql::get_cached_read_result_apc($config, $params, $class_name);
	
		if ($current !== null):
		
			$existing_bids_won_counter = intval($current["BidsWonCounter"]);
			$existing_bids_won_counter += $SellSidePartnerHourlyBidsToAdd->BidsWonCounter;
		
			$existing_bids_lost_counter = intval($current["BidsLostCounter"]);
			$existing_bids_lost_counter += $SellSidePartnerHourlyBidsToAdd->BidsLostCounter;
			
			$existing_bids_error_counter = intval($current["BidsErrorCounter"]);
			$existing_bids_error_counter += $SellSidePartnerHourlyBidsToAdd->BidsErrorCounter;
			
			$existing_bids_spend_total_gross = floatval($current["SpendTotalGross"]);
			$existing_bids_spend_total_gross += $SellSidePartnerHourlyBidsToAdd->SpendTotalGross;

			$existing_bids_spend_total_net = floatval($current["SpendTotalNet"]);
			$existing_bids_spend_total_net += $SellSidePartnerHourlyBidsToAdd->SpendTotalNet;
			
		else: 
		
			$existing_bids_won_counter			= $SellSidePartnerHourlyBidsToAdd->BidsWonCounter;
			$existing_bids_lost_counter			= $SellSidePartnerHourlyBidsToAdd->BidsLostCounter;
			$existing_bids_error_counter		= $SellSidePartnerHourlyBidsToAdd->BidsErrorCounter;
			$existing_bids_spend_total_gross	= $SellSidePartnerHourlyBidsToAdd->SpendTotalGross;
			$existing_bids_spend_total_net		= $SellSidePartnerHourlyBidsToAdd->SpendTotalNet;
			
		endif;
	
		// cache up to 1 hour, the write the the db should occur before that.
		\util\CacheSql::put_cached_read_result_apc($config, 
				$params, 
				$class_name, 
				array(
						"BidsWonCounter"		=> $existing_bids_won_counter, 
						"BidsLostCounter"		=> $existing_bids_lost_counter,
						"BidsErrorCounter"		=> $existing_bids_error_counter,
						"SpendTotalGross"		=> $existing_bids_spend_total_gross,
						"SpendTotalNet"			=> $existing_bids_spend_total_net
						
				));
	
		$timer_name = 'write_timer';
		$write_timer = \util\CacheSql::get_cached_read_result_apc($config, $params, $class_name . $timer_name);
		if ($write_timer == null) :
			/*
			 * 60 second write timer, when the apc cache value is gone the
			* contents are written the DB and the apc value is cleared
			*/
			\util\CacheSql::put_cached_read_result_apc($config, $params, $class_name . $timer_name, array("write_timer"=>true), 60);
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
			\util\CacheSql::put_cached_read_result_apc($config, $params, $class_name . $timer_name, array("write_timer"=>true), 60);
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
			\util\CacheSql::put_cached_read_result_apc($config, $params, $class_name . $timer_name, array("write_timer"=>true), 60);
		endif;
	
	}
	
}
