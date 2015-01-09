<?php
/**
 * CDNPAL NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2015 CDNPAL Ltd. All Rights Reserved
 * @license GPLv3
 */

namespace util;

class CacheSql {


	/*
	 * READ
	*
	* If the existing cached file does exist we
	*
	* 1. read from it
	*
	* If the existing cached file does not exist (minutely)
	*
	* 1. we get the data from the DB
	*
	* 2. we create the new file and write to it
	*/

	private static $null_value = "_NULL";
	private static $empty_array_value = "_EMPTY_ARRAY";
	
	public static function get_cached_read_result_apc($config, $params, $class_name) {
	
		$params["class_name"] = strtolower($class_name);
		$key = serialize($params);
		$key = md5($key);
		
		$success = false;
		$data = apc_fetch($key, $success);
		if ($success == true):
			$unserialized_data = unserialize($data);
			return $unserialized_data;
		endif;
		
		return null;
	}
	
	public static function get_cached_read_result_apc_type_convert($config, $params, $class_name) {
	
		$unserialized_data = self::get_cached_read_result_apc($config, $params, $class_name);
		
		if ($unserialized_data === self::$empty_array_value):
			return array();
		elseif ($unserialized_data === self::$null_value):
			return null;
		else:
			return $unserialized_data;
		endif;

	}
	
	// 60 second time to live by default
	public static function put_cached_read_result_apc($config, $params, $class_name, $data, $ttl = 60) {
	
		$params["class_name"] = strtolower($class_name);
		$key = serialize($params);
		$key = md5($key);

		if (is_array($data) && count($data) == 0):
			$data = self::$empty_array_value;
		elseif ($data === null):
			$data = self::$null_value;
		endif;

		apc_store($key, serialize($data), $ttl);
		return true;
	
	}	
	
	public static function create_reset_write_lock($config, $params, $class_name, $ttl = 60) {
		
		$lock_name = 'reset_lock';
		
		self::put_cached_read_result_apc($config, $params, $class_name . $lock_name, array($lock_name=>true), $ttl);
	}
	
	public static function reset_write_unlock($config, $params, $class_name) {
		
		$lock_name = 'reset_lock';
		
		self::delete_cached_write_apc($config, $params, $class_name . $lock_name);
	}
	
	public static function wait_for_reset_lock($config, $params, $class_name) {
		
		while (self::does_reset_lock_exist_apc($config, $params, $class_name)):
			// 50ms sleep to retry lock
			usleep(50000);
		endwhile;
		
		return true;
	}
	
	public static function does_reset_lock_exist_apc($config, $params, $class_name) {
	
		$lock_name = 'reset_lock';
	
		$sql_lock = self::get_cached_read_result_apc($config, $params, $class_name . $lock_name);
		return $sql_lock != null;
	}
	
	public static function does_cached_write_exist_apc($config, $params, $class_name) {
	
		$timer_name = 'write_timer';
		
		$write_timer = self::get_cached_read_result_apc($config, $params, $class_name . $timer_name);
		return $write_timer != null;
	}
	
	public static function delete_cached_write_apc($config, $params, $class_name) {
	
		$timer_name = 'write_timer';
		$params["class_name"] = strtolower($class_name . $timer_name);
		$key = serialize($params);
		$key = md5($key);
		
		// delete timer
		apc_delete($key);
		
		$params["class_name"] = strtolower($class_name);
		$key = serialize($params);
		$key = md5($key);
		
		// delete contents
		apc_delete($key);
	}
}