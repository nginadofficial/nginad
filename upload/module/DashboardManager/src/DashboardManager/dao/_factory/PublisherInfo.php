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

class PublisherInfo extends \_factory\CachedTableRead
{

	static protected $instance = null;

	public static function get_instance() {

		if (self::$instance == null):
			self::$instance = new \_factory\PublisherInfo();
		endif;
		return self::$instance;
	}


    function __construct() {

            $this->table = 'PublisherInfo';
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
        	$select->order('PublisherInfoID');

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
    public function get($params = null, $orders = null) {
        	// http://files.zend.com/help/Zend-Framework/zend.db.select.html

        $obj_list = array();

    	$resultSet = $this->select(function (\Zend\Db\Sql\Select $select) use ($params, $orders) {
        		if($params != null):
	        		foreach ($params as $name => $value):
	        		$select->where(
	        				$select->where->equalTo($name, $value)
	        		);
	        		endforeach;
        		endif;
        		//$select->limit(10, 0);
        		if($orders == null):
        		  $select->order('PublisherInfoID');
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
     * @return \model\Wesbsites
     */
    public function get_row_object($params = null)
   {
       $rawData = $this->get_row($params);
       $DataObj = new \model\PublisherInfo();
       if ($rawData !== null):
       
           foreach (get_object_vars($DataObj) AS $key => $value):
           
               $DataObj->$key =$rawData[$key];
           endforeach;
       endif;

       return $DataObj;
   }

   /**
    * 
    * @param \model\PublisherWebsite $rawData
    * @return int Number of Rows affected by the save.
    */
    public function savePublisherInfo(\model\PublisherInfo $PublisherInfo) {
    	
    	$data['PublisherInfoID'] = $PublisherInfo->PublisherInfoID;
    	$data['Name']          	 = $PublisherInfo->Name;
        $data['Domain']   	 = $PublisherInfo->Domain;
    	$data['Email']       = $PublisherInfo->Email;
    	$data['IABCategory'] = $PublisherInfo->IABCategory; 
    	
    	$PublisherInfoID = (int)$PublisherInfo->PublisherInfoID;
    	if ($PublisherInfoID === 0): 
    		$data['DateCreated']  = $PublisherInfo->DateCreated;

    		$this->insert($data);
    		return $this->getLastInsertValue();

    	else: 
    	    $data['DateUpdated']  = $PublisherInfo->DateUpdated;
    		return $this->update($data, array('PublisherInfoID' => $PublisherInfoID));
    	endif;
 
    }

    public function deletePublisherInfo($PublisherInfoID) {
    	return $this->delete(array('PublisherInfoID' => $PublisherInfoID));
    }
};
