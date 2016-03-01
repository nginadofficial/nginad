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
	
	public static function setArrayParam(&$obj, &$arr, $name, $type) {
		if (!empty($obj->$name) ||
			(isset($obj->$name) && is_numeric($obj->$name))):
			
			if ($type == 'string'):
				$arr[$name] = (string)$obj->$name;
			elseif ($type == 'integer'):
				$arr[$name] = intval($obj->$name);
			elseif ($type == 'float'):
				$arr[$name] = floatval($obj->$name);
			elseif ($type == 'object'):
				$arr[$name] = (object)($obj->$name);
			elseif ($type == 'array'):
				$arr[$name] = (array)($obj->$name);
			else:
				// default same type
				$arr[$name] = $obj->$name;
			endif;
		endif;
	}
	
	// VAST tag methods
	
	public static function isVastURL($adtag) {
		
		if (strpos($adtag, "<VAST") !== false):
			return false;
		elseif (filter_var($adtag, FILTER_VALIDATE_URL)):
			return true;
		endif;
		
		return false;
	}
}
