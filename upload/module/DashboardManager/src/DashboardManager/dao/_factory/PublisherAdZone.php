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

class PublisherAdZone extends \_factory\CachedTableRead
{

	static protected $instance = null;

	public static function get_instance() {

		if (self::$instance == null):
			self::$instance = new \_factory\PublisherAdZone();
		endif;
		return self::$instance;
	}


    function __construct() {

            $this->table = 'PublisherAdZone';
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
        	$select->order(array('PublisherWebsiteID', 'AdName'));

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
    		$select->order(array('PublisherWebsiteID', 'AdName'));
    	}
    		);
    
    		foreach ($resultSet as $obj):
    			$obj_list[] = $obj;
    		endforeach;
    
    		return $obj_list;
    }
    
    /**
     * Query database and return joined table results. This query has a potential to result in high load.
     * 
     * @param string $params
     * @return multitype:Ambigous <\Zend\Db\ResultSet\ResultSet, NULL, \Zend\Db\ResultSet\ResultSetInterface>
     */
    public function get_joined($params = null) {
        	// http://files.zend.com/help/Zend-Framework/zend.db.select.html

        $obj_list = array();

    	$resultSet = $this->select(function (\Zend\Db\Sql\Select $select) use ($params) {
    	        $select->join("AdTemplates",
    	            "PublisherAdZone.AdTemplateID = AdTemplates.AdTemplateID",
    	            array(
    	        	    "TemplateName" => "TemplateName",
    	                "TemplateX" => "Width",
    	                "TemplateY" => "Height",
    	               ),
    	            $select::JOIN_LEFT);
    	        $select->join("PublisherWebsite",
    	            "PublisherWebsite.PublisherWebsiteID = PublisherAdZone.PublisherWebsiteID",
    	            array(
    	                "WebDomain" => "WebDomain",
    	                "DomainOwnerID" => "DomainOwnerID",
    	                "DomainDescription" => "Description",
    	                "DomainID" => "PublisherWebsiteID",
    	                ),
    	            $select::JOIN_INNER);
        		foreach ($params as $name => $value):
        		$select->where(
        				$select->where->equalTo($name, $value)
        		);
        		endforeach;
        		//$select->limit(10, 0);
        		$select->order(array('PublisherAdZone.PublisherWebsiteID', 'PublisherAdZone.AdName'));
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
   
   /**
    * Save Ads data, insert or update.
    * @param \DashboardManager\dao\PublisherAdZone $rawData
    * @return int Number of Rows affected by the save.
    */
   public function save_ads(\model\PublisherAdZone $rawData)
   {
       
    // We must enforce data integrity!
    $data['PublisherAdZoneID'] = intval(abs($rawData->PublisherAdZoneID));
    $data['PublisherWebsiteID'] = intval(abs($rawData->PublisherWebsiteID));
    $data['PublisherAdZoneTypeID'] = intval(abs($rawData->PublisherAdZoneTypeID));
   	$data['AdOwnerID'] = intval(abs($rawData->AdOwnerID));
   	$data['AdName'] = substr($rawData->AdName,0,100);
   	$data['Description'] = $rawData->Description;
   	
   	if ($rawData->ImpressionType == 'banner'):
   		$data['ImpressionType'] = 'banner';
   	else:
   		$data['ImpressionType'] = 'video';
   	endif;

   	$data['PassbackAdTag'] = $rawData->PassbackAdTag;
   	if (intval($rawData->AdStatus) >= 0 || intval($rawData->AdStatus) <= 2):
   	
   		$data['AdStatus'] = intval($rawData->AdStatus);
   		$data['AutoApprove'] = intval($rawData->AutoApprove);
   	else:
   	
   		$data['AdStatus'] = 0;
   		$data['AutoApprove'] = 0;
   	endif;
   	
   	if ($rawData->AdTemplateID === null):
   	
   	    $data['AdTemplateID'] = null;
   	    $data['Width'] = intval($rawData->Width);
   	    $data['Height'] = intval($rawData->Height);
   	
   	else: 
   	
   	    $data['AdTemplateID'] = intval(abs($rawData->AdTemplateID));
   	    $data['Width'] = intval($rawData->Width);
   	    $data['Height'] = intval($rawData->Height);

   	endif;
   	
   	if (intval($rawData->IsMobileFlag) == 0 || intval($rawData->IsMobileFlag) == 1):
   	
   		$data['IsMobileFlag'] = intval($rawData->IsMobileFlag);
   	
   	else:
   	
   		$data['IsMobileFlag'] = 0;
   	endif;
   	
   	
   	if (is_numeric($rawData->FloorPrice) && $rawData->FloorPrice !== null):
   	
   	    $data['FloorPrice'] = $rawData->FloorPrice;
   	endif;
   	if ($rawData->TotalRequests !== null):
   	
   	    $data['TotalRequests'] = intval(abs($rawData->TotalRequests));
   	endif;
   	if ($rawData->TotalImpressionsFilled !== null):
   	
   	    $data['TotalImpressionsFilled'] = intval(abs($rawData->TotalImpressionsFilled));
   	endif;
   	if (is_numeric($rawData->TotalAmount) && $rawData->TotalAmount !== null):
   	
   		$data['TotalAmount'] = $rawData->TotalAmount;
   	endif;
   	
   	$data['DateUpdated'] = date('Y-m-d H:i:s');
   	if (intval($rawData->PublisherAdZoneID) > 0):
   	
   		return $this->update($data,array('PublisherAdZoneID' => intval(abs($rawData->PublisherAdZoneID))));
   	
   	else:
   	
   	    $data['DateCreated'] = date('Y-m-d H:i:s');
   		$this->insert($data);
   		return $this->lastInsertValue;
   	endif;
   
   }
   
   /**
    * Delete the Ad specified.
    * 
    * @param int $PublisherAdZoneID The integer ID of the Ad to delete.
    * @throws \InvalidArgumentException is thrown when an invalid integer is provided.
    * @return boolean|int Returns the rows affected, or FALSE if failure.
    */
   public function delete_zone($PublisherAdZoneID)
   {
       $result = 0;
        
       if (is_int($PublisherAdZoneID) && intval($PublisherAdZoneID) > 0 && $PublisherAdZoneID !== null):
       
       	try {
       		$result = $this->delete(array("PublisherAdZoneID" => intval($PublisherAdZoneID)));
       	}
       	catch (\Exception $e) {
       		 
       		return FALSE; // DB Error.
       	}
       	 
       	return $result;
       
       elseif (!is_int($PublisherAdZoneID) && $PublisherAdZoneID !== null ): // Not a number, but not null (EX: string). Throw exception.
       
       	$message = "delete_zone() requires a positive integer as its first and only parameter. A value of type \"" .
       			gettype($PublisherAdZoneID) . "\" was provided instead.";
       	throw new \InvalidArgumentException($message);
       endif;
        
       return FALSE; // Invalid ID.
       
   }
   
   public function updatePublisherAdZonePublisherAdZoneType($publisher_ad_zone_id, $type_id) {
    
    	$params = array();
    	$params["PublisherAdZoneID"] = $publisher_ad_zone_id;
    	$PublisherAdZone = $this->get_row($params);
    
    	if ($PublisherAdZone != null):
	    	 
    		$PublisherAdZone->PublisherAdZoneTypeID = $type_id;
	    	// get array of data
	    	$data = $PublisherAdZone->getArrayCopy();
	    	 
	    	$this->update($data, array('PublisherAdZoneID' => $publisher_ad_zone_id));
    	endif;
    
    }
    
    public function updatePublisherAdZonePublisherAdZoneStatus($publisher_ad_zone_id, $approval_flag) {
    
    	$params = array();
    	$params["PublisherAdZoneID"] = $publisher_ad_zone_id;
    	$PublisherAdZone = $this->get_row($params);
    
    	if ($PublisherAdZone != null):
    	 
    	$PublisherAdZone->AutoApprove 	= 0;
    	$PublisherAdZone->AdStatus 		= $approval_flag;
    	// get array of data
    	$data = $PublisherAdZone->getArrayCopy();
    	 
    	$this->update($data, array('PublisherAdZoneID' => $publisher_ad_zone_id));
    	endif;
    
    }

};
?>