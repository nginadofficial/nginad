<?php
/**
 * CDNPAL NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2015 CDNPAL Ltd. All Rights Reserved
 * @license GPLv3
 */

namespace _factory;

use Zend\Db\TableGateway\Feature;
use Zend\Db\TableGateway\AbstractTableGateway;

class UsersThatDontFill extends AbstractTableGateway
{

	private $apc_key = 'UsersThatDontFill';
	
	static protected $instance = null;

	public static function get_instance() {

		if (self::$instance == null):
			self::$instance = new \_factory\UsersThatDontFill();
		endif;
		return self::$instance;
	}


    function __construct() {

            $this->table = 'UsersThatDontFill';
            $this->featureSet = new Feature\FeatureSet();
            $this->featureSet->addFeature(new Feature\GlobalAdapterFeature());
            $this->initialize();
    }
    
    /*
     * SPECIAL CACHING FUNCTIONS WITH MAINTENANCE
     * Needs to be inline and not inherited
     * 
     * Returns array of MD5 hashes concatenated with the Exchange, not objects like you may expect
     */
    
    public function get_cached($config, $params = array()) {
    	 	
    	$success = false;
    	$data = apc_fetch($this->apc_key, $success);
    	
    	if ($success == true):
    		
			return $data;

    	endif;
    	
    	$cached_data = array();
    	
    	return $cached_data;
    
    }
    
    public function update_data() {
    	
    	$params = array();
    	$cached_data = $this->get($params);
    	apc_store($this->apc_key, $cached_data);
    	
    	return $cached_data;
    	
    }
    
    public function clear_data() {
    	 
    	$this->clearOld();
    	 
    }

    public function get($params = null) {
        	// http://files.zend.com/help/Zend-Framework/zend.db.select.html

        $array_list = array();

    	$resultSet = $this->select(function (\Zend\Db\Sql\Select $select) use ($params) {
        		foreach ($params as $name => $value):
        		$select->where(
        				$select->where->equalTo($name, $value)
        		);
        		endforeach;
        		$select->columns(array(
        				'UserIPMD5', 'Exchange'
        		));
        		/* not sure how much is too much, get last 100k max for now */
        		$select->limit(20000, 0);
        		$select->order('DateUpdated DESC');
        	}
    	);

    	    foreach ($resultSet as $obj):
    	        $array_list[] = $obj->UserIPMD5 . '-' . $obj->Exchange;
    	    endforeach;

    		return $array_list;
    }

    public function saveUsersThatDontFill($UsersThatDontFillList) {
    
    	$values_to_replace_into = '';
    	$comma = '';
    	
    	foreach ($UsersThatDontFillList as $UsersThatDontFill):
    		
    		$values_to_replace_into.= $comma . '(\'' . $UsersThatDontFill->MDY . '\', \'' . mysql_escape_string($UsersThatDontFill->UserIPMD5) . '\', \'' . mysql_escape_string($UsersThatDontFill->Exchange) . '\', \'' . $UsersThatDontFill->DateCreated . '\', CURRENT_TIMESTAMP)';
    		$comma = ', ';
    		
    	endforeach;

    	$sql = "REPLACE INTO `UsersThatDontFill` (`MDY` ,`UserIPMD5` ,`Exchange` ,`DateCreated` ,`DateUpdated`) VALUES " . $values_to_replace_into;
    	$statement = $this->getAdapter()->query($sql);
    	$result = $statement->execute(array());

    }
    
    public function clearOld() {
    	$sql = "DELETE FROM UsersThatDontFill WHERE DateUpdated < (NOW() - INTERVAL 2 HOUR)";
    	$statement = $this->getAdapter()->query($sql);
    	$result = $statement->execute(array());
    }
};
