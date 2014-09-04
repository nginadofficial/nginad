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
use Zend\Db\TableGateway\Feature;


class AdType extends AbstractTableGateway
{

	static protected $instance = null;

	public static function get_instance() {

		if (self::$instance == null):
			self::$instance = new \_factory\AdType();
		endif;
		return self::$instance;
	}


    function __construct() {

            $this->table = 'AdType';
            $this->featureSet = new Feature\FeatureSet();
            $this->featureSet->addFeature(new Feature\GlobalAdapterFeature());
            $this->initialize();
    }

    /**
     * Query database and return a row of results.
     * 
     * @param string $params
     * @return Ambigous <\Zend\Db\ResultSet\ResultSet, NULL, \Zend\Db\ResultSet\ResultSetInterface>|NULL
     */
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
        	$select->order('AdTypeName');

        }
        	);

    	    foreach ($resultSet as $obj):
    	         return $obj;
    	    endforeach;

        	return null;
    }

    /**
     * Query database and return results.
     * 
     * @param string $params
     * @return multitype:Ambigous <\Zend\Db\ResultSet\ResultSet, NULL, \Zend\Db\ResultSet\ResultSetInterface>
     */
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
        		$select->order('AdTypeName');

        	}
    	);

    	    foreach ($resultSet as $obj):
    	        $obj_list[] = $obj;
    	    endforeach;

    		return $obj_list;
    }
   
    /**
     * Query database for a row and return results as an object.
     * 
     * @param string $params
     * @return \model\AdType
     */
   public function get_row_object($params = null)
   {
       $rawData = $this->get_row($params);
       $DataObj = new \model\AdType();
       if ($rawData !== null):
       
           foreach (get_object_vars($DataObj) AS $key => $value):
           
               $DataObj->$key =$rawData[$key];
           endforeach;
       endif;

       return $DataObj;
   }
   
   /**
    * Query database and return results as an array of objects.
    * 
    * @param string $params
    * @return array:\model\AdType
    */
   public function get_object($params = null)
   {
       $rawData = $this->get_row($params);
       $DataObj = array();
       if ($rawData !== null):
       
           foreach ($rawData AS $row_number => $row_data): // Get each row in the raw data.
           
               // New instance of model object in each row.
               $DataObj[$row_number] = new \model\AdType();
       	        foreach (get_object_vars($DataObj[$row_number]) AS $key => $value): //Assign to object.
       	        
       		       $DataObj[$row_number]->$key = $row_data[$key];
       	        endforeach;
           endforeach;
       endif;
       
       return $DataObj;
   }
   
   /**
    * 
    * @param \model\AdType $rawData
    * @return int Number of Rows affected by the save.
    */
   public function save_type(\model\AdType $rawData)
   {
   	$data['AdTypeName'] = substr($rawData->AdTypeName,0,50);
   	$data['AdTypeDescription'] = $rawData->AdTypeDescription;
   	$data['DateUpdated'] = date('Y-m-d H:i:s');
   	if (intval($rawData->AdTypeID) > 0):
   	
   		return $this->update($data,array('AdTypeID' => intval($rawData->AdTypeID)));
   	
   	else:
   	
   	    $data['DateCreated'] = date('Y-m-d H:i:s');
   		return $this->insert($data);
   	endif;
   
   }

};
?>