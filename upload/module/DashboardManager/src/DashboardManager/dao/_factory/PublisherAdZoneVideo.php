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

class PublisherAdZoneVideo extends \_factory\CachedTableRead
{

	static protected $instance = null;

	public static function get_instance() {

		if (self::$instance == null):
			self::$instance = new \_factory\PublisherAdZoneVideo();
		endif;
		return self::$instance;
	}


    function __construct() {

            $this->table = 'PublisherAdZoneVideo';
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
        	$select->order(array('PublisherAdZoneID'));

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
    		$select->order(array('PublisherAdZoneID'));
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
     * @return \DashboardManager\dao\PublisherAdZone
     */
   public function get_row_object($params = null)
   {
       $rawData = $this->get_row($params);
       $DataObj = new \model\PublisherAdZone();
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
    * @return array:\DashboardManager\dao\PublisherAdZone
    */
   public function get_object($params = null)
   {
       $rawData = $this->get($params);
       $DataObj = array();
       if ($rawData !== null):
       
           foreach ($rawData AS $row_number => $row_data): // Get each row in the raw data.
           
               // New instance of model object in each row.
               $DataObj[$row_number] = new \model\PublisherAdZone();
       	        foreach (get_object_vars($DataObj[$row_number]) AS $key => $value): //Assign to object.
       	        
       		       $DataObj[$row_number]->$key = $row_data[$key];
       	        endforeach;
           endforeach;
       endif;
       
       return $DataObj;
   }
   
   
   public function savePublisherAdZoneVideo(\model\PublisherAdZoneVideo $PublisherAdZoneVideo) {
   	
   		$this->delete_zone($PublisherAdZoneVideo->PublisherAdZoneID);
   		
	   	$data = array(
	   			'PublisherAdZoneID'         	=> $PublisherAdZoneVideo->PublisherAdZoneID,
	   			'MimesCommaSeparated'       	=> $PublisherAdZoneVideo->MimesCommaSeparated === "" ? null : $PublisherAdZoneVideo->MimesCommaSeparated,
	   			'MinDuration'         			=> $PublisherAdZoneVideo->MinDuration === "" ? null : $PublisherAdZoneVideo->MinDuration,
	   			'MaxDuration'         			=> $PublisherAdZoneVideo->MaxDuration === "" ? null : $PublisherAdZoneVideo->MaxDuration,
	   			'ApisSupportedCommaSeparated'	=> $PublisherAdZoneVideo->ApisSupportedCommaSeparated === "" ? null : $PublisherAdZoneVideo->ApisSupportedCommaSeparated,
	   			'ProtocolsCommaSeparated'     	=> $PublisherAdZoneVideo->ProtocolsCommaSeparated === "" ? null : $PublisherAdZoneVideo->ProtocolsCommaSeparated,
	   			'DeliveryCommaSeparated'      	=> $PublisherAdZoneVideo->DeliveryCommaSeparated === "" ? null : $PublisherAdZoneVideo->DeliveryCommaSeparated,
	   			'PlaybackCommaSeparated'     	=> $PublisherAdZoneVideo->PlaybackCommaSeparated === "" ? null : $PublisherAdZoneVideo->PlaybackCommaSeparated,
	   			'StartDelay'         			=> $PublisherAdZoneVideo->StartDelay === "" ? null : $PublisherAdZoneVideo->StartDelay,
	   			'Linearity'         			=> $PublisherAdZoneVideo->Linearity === "" ? null : $PublisherAdZoneVideo->Linearity,
	   			'FoldPos'         				=> $PublisherAdZoneVideo->FoldPos === "" ? null : $PublisherAdZoneVideo->FoldPos,
	   			'DateCreated'         			=> $PublisherAdZoneVideo->DateCreated
	   	);

		$this->insert($data);
		return $this->getLastInsertValue();
   }
   
   /**
    * Delete the Ad specified.
    * 
    * @param int $PublisherAdZoneVideoID The integer ID of the Ad to delete.
    * @throws \InvalidArgumentException is thrown when an invalid integer is provided.
    * @return boolean|int Returns the rows affected, or FALSE if failure.
    */
   public function delete_zone($PublisherAdZoneID)
   {
       $result = $this->delete(array("PublisherAdZoneID" => intval($PublisherAdZoneID)));
   }
   
};
?>