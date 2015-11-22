<?php
/**
 * NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2016 NginAd Foundation. All Rights Reserved
 * @license GPLv3
 */

namespace util;

class FrequencyHelper {
	
	/*
	 * OK, here's the thing.
	 * Normally frequency capping is done on the 
	 * frequency of impressions on a certain publisher's 
	 * website, zone or inventory.
	 * 
	 * However, because in RTB we don't know if the bid
	 * will win the impression or not ahead of time,
	 * we can only calculate frequency against the bids.
	 */
	
	public static function incrementLineItemBidFrequencyCount($config, $InsertionOrderLineItemID, $reset = false) {
		
		$params = array();
		$params["InsertionOrderLineItemID"] = $InsertionOrderLineItemID;
		
		$class_dir_name = 'FrequencyHelper';
			
		// WAIT FOR SYNCHRONIZED LOCK TO BE FREED
		\util\CacheSql::wait_for_reset_lock($config, $params, $class_dir_name);
		
		$cached_key_exists = \util\CacheSql::does_cached_write_exist_apc($config, $params, $class_dir_name);
			
		if ($cached_key_exists && $reset === false):
		
			// increment bucket
			self::increment_frequency_impressions_counter($config, $params, $class_dir_name, 1);
			
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
			self::increment_frequency_impressions_counter($config, $params, $class_dir_name, 1);
				
			// SYNCHRONIZED BLOCK END
			\util\CacheSql::reset_write_unlock($config, $params, $class_dir_name);
			
		endif;
		
	}
	
	public static function checkLineItemImpressionFrequency($config, $frequency, $InsertionOrderLineItemID) {
		
		$current = \util\CacheSql::get_cached_read_result_apc_type_convert($config, $params, $class_name);
		
		if ($current !== null):
		
			$existing_impressions_counter = intval($current["ImpressionsCounter"]) - 1;
			
			$frequency = intval($frequency);
			
			/*
			 * Check for a remainder on the division.
			 * If no remainder, then return true to trigger another bid.
			 * 
			 * until a new impression is recorded by the nurl or the 
			 * click tracker. Only one should trigger a new impression 
			 * increment however.
			 */ 
			if ($existing_impressions_counter == 0):
				return true;
			elseif ($existing_impressions_counter % $frequency == 0):
				// reset the counter, with 1 offset
				self::incrementLineItemBidFrequencyCount($config, $InsertionOrderLineItemID, true);
				return true;
			endif;
			
			return false;
		else:
			/*
			 * No impressions counted yet,
			 * so go ahead and bid until we get some
			 */ 
			return true;
		endif;
		
	}
	
	protected static function increment_frequency_impressions_counter($config, $params, $class_name, $increment_amount = 1) {
	
		$current = \util\CacheSql::get_cached_read_result_apc_type_convert($config, $params, $class_name);
	
		if ($current !== null):
	
			$existing_impressions_counter = intval($current["ImpressionsCounter"]);
			$existing_impressions_counter += $increment_amount;

		else:
	
			$existing_impressions_counter = $increment_amount;

		endif;
	
		// cache up to 1 hour, the write the the db should occur before that.
		\util\CacheSql::put_cached_read_result_apc($config,
				$params,
				$class_name,
				array(
					"ImpressionsCounter" => $existing_impressions_counter
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
	
}