<?php
/**
 * CDNPAL NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2015 CDNPAL Ltd. All Rights Reserved
 * @license GPLv3
 */

namespace _factory;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Sql\Sql;
use Zend\Db\Metadata\Metadata;

abstract class CachedTableRead extends AbstractTableGateway
{
	
    protected $adminFields = array();
    
    private static $null_value = "_NULL";
    private static $empty_array_value = "_EMPTY_ARRAY";
    
    public function get_row_cached($config, $params = array()) {
    	
    	$cached_data = \util\CacheSql::get_cached_read_result_apc($config, $params, $this->table . '_S');
    	 
    	if ($cached_data !== null):
    	
    		if ($cached_data == self::$null_value):
    			return null;
    		else:
    			return $cached_data;
    		endif;
    	
    	else:

    		$data = $this->get_row($params);
    	
    		\util\CacheSql::put_cached_read_result_apc($config, $params, $this->table . '_S', $data);
    	
    		return $data;
    		
    	endif;
    }
    
    public function get_cached($config, $params = array()) {
    	
    	$cached_data = \util\CacheSql::get_cached_read_result_apc($config, $params, $this->table . '_M');
    	 
    	if ($cached_data !== null):
    	
    		if ($cached_data == self::$empty_array_value):
    			return array();
    		else:
    			return $cached_data;
    		endif;
    	
    	else:

    		$data = $this->get($params);
    	
    		\util\CacheSql::put_cached_read_result_apc($config, $params, $this->table . '_M', $data);
    	
    		return $data;
    		
    	endif;
    	 
    }
    
    // default APC ttl is 15 minutes, 900 seconds
    public function getPerTimeCached($config, $where_params = null, $time_to_live = 900, $refresh = false, $is_admin = FALSE) {
    
    	$cached_data = \util\CacheSql::get_cached_read_result_apc($config, $where_params, $this->table . '_M');
    	 
	   	if ($cached_data !== null && !$refresh):
    	
    		if ($cached_data == self::$empty_array_value):
    			return array();
    		else:
    			return $cached_data;
    		endif;
    	
    	else:

    		$data = $this->getPerTime($where_params, $is_admin);

                \util\CacheSql::put_cached_read_result_apc($config, $where_params, $this->table . '_M', $data, $time_to_live);
    	
    		return $data;
    		
    	endif;
    }
    
    public function getPerTimeHeader($is_admin = false){
     
        $metadata = new Metadata($this->adapter);
        $header = $metadata->getColumnNames($this->table);
//        return ($is_admin) ? $header : array_diff($header, $this->adminFields);
        return ($is_admin) ? $header : $header;
        
    }
    
    public function re_normalize_time($str_time) {
	
		$denormalized_time = "";
		
		$timestamp_parts = explode(" ", $str_time);
		if (count($timestamp_parts) == 2):
			
			$date_timestamp = $timestamp_parts[0];
			$hour_timestamp = $timestamp_parts[1];
			
			$denormalized_time = strtotime($date_timestamp);
			
			$timestamp_hours_as_seconds 		= intval($hour_timestamp) * 3600;
			$denormalized_time					+= $timestamp_hours_as_seconds;
		
			$denormalized_time 					= date("m/d/Y h:i A", $denormalized_time);
		
		endif;
		
		return $denormalized_time;

    }
    
    protected function prepareList($records, $headers){
        
        $obj_list = array();
        foreach ($records as $obj):
            $row = array();
            foreach ($headers as $key => $val) {
                if (isset($obj[$key])):
            		$row[$key] = $obj[$key];
                endif;
            }
//            $obj = array_intersect_ukey($obj, $this->getPerTimeHeader($is_admin), function ($key1, $key2) { return (int)!($key1 == $key2);});
            if (!empty($row['MDYH']))
                $row['MDYH'] = $this->re_normalize_time($row['MDYH']);
            $obj_list[] = $row;
        endforeach;
        return $obj_list;
        
    }
    
}