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

class Maintenance extends \_factory\CachedTableRead
{

	static protected $instance = null;

	public static function get_instance() {

		if (self::$instance == null):
			self::$instance = new \_factory\Maintenance();
		endif;
		return self::$instance;
	}


    function __construct() {

            $this->table = 'Maintenance';
            $this->featureSet = new Feature\FeatureSet();
            $this->featureSet->addFeature(new Feature\GlobalAdapterFeature());
            $this->initialize();
    }

    public function get_row($params = null) {
        // http://files.zend.com/help/Zend-Framework/zend.db.select.html

        $obj_list = array();

        $resultSet = $this->select(function (\Zend\Db\Sql\Select $select) use ($params) {
        	foreach ($params as $name => $value):
        	$select->where(
        			$select->where->equalTo($name, $value)
        	);
        	endforeach;
        	$select->limit(1, 0);

        }
        	);

    	    foreach ($resultSet as $obj):
    	         return $obj;
    	    endforeach;

        	return null;
    }

    public function get($params = null) {
        	// http://files.zend.com/help/Zend-Framework/zend.db.select.html

        $obj_list = array();

    	$resultSet = $this->select(function (\Zend\Db\Sql\Select $select) use ($params) {
        		foreach ($params as $name => $value):
        		$select->where(
        				$select->where->equalTo($name, $value)
        		);
        		endforeach;
        		//$select->limit(10, 0);

        	}
    	);

    	    foreach ($resultSet as $obj):
    	        $obj_list[] = $obj;
    	    endforeach;

    		return $obj_list;
    }

    public function insertMaintenanceRecord(\model\Maintenance $Maintenance) {
    	$data = array(
    	        'TagName'         	=> $Maintenance->TagName,
    			'LastUpdated'       => $Maintenance->LastUpdated
    	);

    	$this->insert($data);
    }

    public function updateMaintenanceRecord(\model\Maintenance $Maintenance) {
    	$data = array(
    			'TagName'         	=> $Maintenance->TagName,
    			'LastUpdated'       => $Maintenance->LastUpdated
    	);

    	$tag_name = $Maintenance->TagName;
    	$this->update($data, array('TagName' => $tag_name));
    }

    public function deleteMaintenanceRecord($tag_name) {
    	$this->delete(array('TagName' => $tag_name));
    }

};
