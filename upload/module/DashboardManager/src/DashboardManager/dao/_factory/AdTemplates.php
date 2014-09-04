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
use _factory\CachedTableRead;

class AdTemplates
    extends CachedTableRead
{

	static protected $instance = null;

	public static function get_instance() {

		if (self::$instance == null):
			self::$instance = new \_factory\AdTemplates();
		endif;
		return self::$instance;
	}


    function __construct() {

            $this->table = 'AdTemplates';
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
        	$select->order('TemplateName');

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
        		$select->order('TemplateName');

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
     * @return DashboardManager\model\AdTemplates
     */
   public function get_row_object($params = null)
   {
       $rawData = $this->get_row($params);
       $DataObj = new \model\AdTemplates();
       if($rawData !== null):
       
           foreach(get_object_vars($DataObj) AS $key => $value):
           
               $DataObj->$key =$rawData[$key];
           endforeach;
       endif;

       return $DataObj;
   }
   
   /**
    * Query database and return results as an array of objects.
    * 
    * @param string $params
    * @return array:DashboardManager\model\AdTemplates
    */
   public function get_object($params = null)
   {
       $rawData = $this->get($params);
       $DataObj = array();
       
       if($rawData !== null):
       
           foreach($rawData AS $row_number => $row_data): // Get each row in the raw data.
           
               // New instance of model object in each row.
                $DataObj[$row_number] = new \model\AdTemplates();
       	        foreach(get_object_vars($DataObj[$row_number]) AS $key => $value): //Assign to object.
       	        
       		       $DataObj[$row_number]->$key = $row_data[$key];
       	        endforeach;
       	        
           endforeach;
       endif;
       return $DataObj;
   }
   
   /**
    * 
    * @param DashboardManager\model\AdTemplates $rawData
    * @return int Number of Rows affected by the save.
    */
   public function save_template(\model\AdTemplates $rawData)
   {
   	$data['TemplateName'] = substr($rawData->TemplateName,0,50);
   	if(intval($rawData->IsMobileFlag) == 0 || intval($rawData->IsMobileFlag) == 1):
   	
   		$data['IsMobileFlag'] = intval($rawData->IsMobileFlag);
   	
   	else:
   	
   		$data['IsMobileFlag'] = 0;
   	endif;
   	$data['Width']  = intval($rawData->Width);
   	$data['Height'] = intval($rawData->Height);
   	
   	$data['DateUpdated'] = date('Y-m-d H:i:s');
   	if(intval($rawData->AdTemplateID) > 0):
   	
   		return $this->update($data,array('AdTemplateID' => intval($rawData->AdTemplateID)));
   	
   	else:
   	
   	    $data['DateCreated'] = date('Y-m-d H:i:s');
   		return $this->insert($data);
   	endif;
   
   }

};

?>