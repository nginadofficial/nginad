<?php

namespace util;
use \Exception;

class ParseHelper {
	
	/*
	 * Make sure it's a single item
	*/
	public static function parse_item(&$obj, &$arr, $name, $obj_name = null) {
	
		if ($obj_name == null):
			$obj_name = $name;
		endif;
	
		if (isset($arr[$name]) && !is_array($arr[$name]) && !is_object($arr[$name])):
			
			$obj->$obj_name = $arr[$name];
	
		endif;
	}
	
	/*
	 * Make sure it's a list
	*/
	public static function parse_item_list(&$obj, &$arr, $name, $obj_name = null) {
	
		if ($obj_name == null):
			$obj_name = $name;
		endif;
	
		if (isset($arr[$name]) && is_array($arr[$name]) && count($arr[$name])):
			
			$obj->$obj_name = $arr[$name];
	
		endif;
	}
	
	/*
	 * Make sure it's a single item
	*/
	public static function parse_with_exception(&$obj, &$arr, $exception, $name, $obj_name = null) {
	
		if ($obj_name == null):
			$obj_name = $name;
		endif;
	
		if (isset($arr[$name]) && !is_array($arr[$name]) && !is_object($arr[$name])):
				
			$obj->$obj_name = $arr[$name];
				
		else:
			
			throw new Exception($exception);
	
		endif;
	}
	
	/*
	 * Make sure it's a list
	*/
	public static function parse_list_with_exception(&$obj, &$arr, $exception, $name, $obj_name = null) {
	
		if ($obj_name == null):
			$obj_name = $name;
		endif;
	
		if (isset($arr[$name]) && is_array($arr[$name]) && count($arr[$name])):
			
			$obj->$obj_name = $arr[$name];
			
		else:
			
			throw new Exception($exception);
	
		endif;
	}
}
