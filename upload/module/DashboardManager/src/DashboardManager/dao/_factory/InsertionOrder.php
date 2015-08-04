<?php
/**
 * NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2016 NginAd Foundation. All Rights Reserved
 * @license GPLv3
 */

namespace _factory;

use Zend\Db\TableGateway\Feature;

class InsertionOrder extends \_factory\CachedTableRead
{

	static protected $instance = null;

	public static function get_instance() {

		if (self::$instance == null):
			self::$instance = new \_factory\InsertionOrder();
		endif;
		return self::$instance;
	}


    function __construct() {

            $this->table = 'InsertionOrder';
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
        	$select->order('InsertionOrderID');

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
        		$select->order('InsertionOrderID');

        	}
    	);

    	    foreach ($resultSet as $obj):
    	        $obj_list[] = $obj;
    	    endforeach;

    		return $obj_list;
    }

    public function saveInsertionOrder(\model\InsertionOrder $InsertionOrder) {
    	$data = array(
    			'Name'                 => $InsertionOrder->Name,
    	         // convert to MySQL DateTime
    			'StartDate'            => $InsertionOrder->StartDate,
    	        'EndDate'              => $InsertionOrder->EndDate,
    	        'Customer'             => $InsertionOrder->Customer,
    	        'CustomerID'           => $InsertionOrder->CustomerID,
    	        'MaxImpressions'       => $InsertionOrder->MaxImpressions,
    	        'MaxSpend'             => $InsertionOrder->MaxSpend,
    	        'Active'               => $InsertionOrder->Active,
    	        'DateUpdated'          => $InsertionOrder->DateUpdated
    	);

    	$ad_campaign_id = (int)$InsertionOrder->InsertionOrderID;
    	if ($ad_campaign_id === 0): 
    		$data['UserID'] 					= $InsertionOrder->UserID;
    		$data['DateCreated'] 				= $InsertionOrder->DateCreated;
    		$data['ImpressionsCounter']   		= $InsertionOrder->ImpressionsCounter;
    		$data['CurrentSpend']         		= $InsertionOrder->CurrentSpend;
    		$this->insert($data);
    		return $this->getLastInsertValue();
    	else: 
    		$this->update($data, array('InsertionOrderID' => $ad_campaign_id));
    		return $ad_campaign_id;
        endif;
    }

    public function saveInsertionOrderFromDataArray($data) {

    	$this->update($data, array('InsertionOrderID' => $data['InsertionOrderID']));
    }

    public function deleteInsertionOrder($ad_campaign_id) {
    	$this->delete(array('InsertionOrderID' => $ad_campaign_id));
    }

    public function deActivateInsertionOrder($ad_campaign_id) {

    	$params = array();
    	$params["InsertionOrderID"] = $ad_campaign_id;
    	$InsertionOrder = $this->get_row($params);

    	$InsertionOrder->Active = 0;
    	// get array of data
    	$data = $InsertionOrder->getArrayCopy();

    	$this->update($data, array('InsertionOrderID' => $ad_campaign_id));
    }

};
