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

class PublisherWebsite extends \_factory\CachedTableRead
{

	static protected $instance = null;

	public static function get_instance() {

		if (self::$instance == null):
			self::$instance = new \_factory\PublisherWebsite();
		endif;
		return self::$instance;
	}


    function __construct() {

            $this->table = 'PublisherWebsite';
            $this->featureSet = new Feature\FeatureSet();
            $this->featureSet->addFeature(new Feature\GlobalAdapterFeature());
            $this->initialize();
    }

    /**
     * Query database and return a row of results.
     * 
     * @param string $params
     * @return multitype: ArrayObject|NULL|\ArrayObject
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
        		$select->order('WebDomain');

        	}
    	);

    	    if ($resultSet->count() > 0):
    	       return $resultSet->current();
            else:
    		  return null;
    		endif;  
    	    

    }

    /**
     * Query database and return results.
     * 
     * @param string $params
     * @return multitype:Ambigous <\Zend\Db\ResultSet\ResultSet, NULL, \Zend\Db\ResultSet\ResultSetInterface>
     */
    public function get($params = null, $orders = null) {
        	// http://files.zend.com/help/Zend-Framework/zend.db.select.html
    	
        $obj_list = array();

    	$resultSet = $this->select(function (\Zend\Db\Sql\Select $select) use ($params, $orders) {
        		foreach ($params as $name => $value):
        		$select->where(
        				$select->where->equalTo($name, $value)
        		);
        		endforeach;
        		//$select->limit(10, 0);

        		if($orders == null):
        			$select->order('WebDomain');
        		else:
        			$select->order($orders);
        		endif;
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
     * @return \DashboardManager\model\PublisherWebsite
     */
   public function get_row_object($params = null)
   {
       $rawData = $this->get_row($params);
       $DataObj = new \model\PublisherWebsite();
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
    * @return array:\DashboardManager\model\PublisherWebsite
    */
   public function get_object($params = null)
   {
       $rawData = $this->get($params);
       $DataObj = array();
       if ($rawData !== null):
       
           foreach ($rawData AS $row_number => $row_data): // Get each row in the raw data.
           
               // New instance of model object in each row.
               $DataObj[$row_number] = new \model\PublisherWebsite();
       	        foreach (get_object_vars($DataObj[$row_number]) AS $key => $value): //Assign to object.
       	        
       		       $DataObj[$row_number]->$key = $row_data[$key];
       	        endforeach;
           endforeach;
       endif;
       
       return $DataObj;
   }
   
   /**
    * Saves domain data, insert or update.
    * 
    * @param \DashboardManager\model\PublisherWebsite $rawData
    * @return int Number of Rows affected by the save.
    */
   public function save_domain(\model\PublisherWebsite $rawData)
   {
       
   	$data['WebDomain'] = substr($rawData->WebDomain,0,255);
   	$data['DomainOwnerID'] = intval($rawData->DomainOwnerID);
   	if (intval($rawData->ApprovalFlag) >= 0 && intval($rawData->ApprovalFlag) <= 2): 
   		$data['ApprovalFlag'] = intval($rawData->ApprovalFlag);
   	endif;
   	if (intval($rawData->AutoApprove) == 0 || intval($rawData->AutoApprove) == 1):
   		$data['AutoApprove'] = intval($rawData->AutoApprove);
   	endif;
   	
   	$data['IABCategory'] 	= $rawData->IABCategory;
   	$data['IABSubCategory'] = $rawData->IABSubCategory;
   	$data['Description'] 	= $rawData->Description;
   	
   	$data['DateUpdated'] = date('Y-m-d H:i:s');
   	if (intval($rawData->PublisherWebsiteID) > 0):
   		return $this->update($data, array('PublisherWebsiteID' => intval($rawData->PublisherWebsiteID)));
   	else:

   	    $data['DateCreated'] = date('Y-m-d H:i:s');
   		return $this->insert($data);
   	endif;
   
   }
      
   /**
    * Delete the specified web domain from the table.
    * 
    * @param int $PublisherWebsiteID The entry ID to delete.
    * @return boolean|int FALSE if an invalid entry ID/integer is provided, otherwise the number of rows affected.
    * @throws \InvalidArgumentException This exception is thrown if anything but an integer (or null) is provided.
    */
   public function delete_domain($PublisherWebsiteID)
   {
       $result = 0;
       
       if (is_int($PublisherWebsiteID) && intval($PublisherWebsiteID) > 0 && $PublisherWebsiteID !== null):
       
           try {
           $result = $this->delete(array("PublisherWebsiteID" => intval($PublisherWebsiteID)));
           }
           catch (\Exception $e) {
               
               return FALSE; // DB Error.
           }
           
           return $result;
       
       elseif (!is_int($PublisherWebsiteID) && $PublisherWebsiteID !== null ): // Not a number, but not null (EX: string). Throw exception.
       
           $message = "delete_domain() requires a positive integer as its first and only parameter. A value of type \"" . 
                gettype($PublisherWebsiteID) . "\" was provided instead.";
           throw new \InvalidArgumentException($message);
       endif;
       
       return FALSE; // Invalid ID.
   }

};
?>