<?php
/**
 * NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2016 NginAd Foundation. All Rights Reserved
 * @license GPLv3
 */

namespace _factory;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\TableGateway\Feature;

class InsertionOrderPreview extends AbstractTableGateway
{

	static protected $instance = null;

	public static function get_instance() {

		if (self::$instance == null):
			self::$instance = new \_factory\InsertionOrderPreview();
		endif;
		return self::$instance;
	}


    function __construct() {

            $this->table = 'InsertionOrderPreview';
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
        	$select->order('InsertionOrderPreviewID');

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
        		$select->order('InsertionOrderPreviewID');

        	}
    	);

    	    foreach ($resultSet as $obj):
    	        $obj_list[] = $obj;
    	    endforeach;

    		return $obj_list;
    }

    public function saveInsertionOrderPreview(\model\InsertionOrderPreview $InsertionOrderPreview) {
    	$data = array(
    	        'InsertionOrderID'  	   => $InsertionOrderPreview->InsertionOrderID,
    			'Name'                 => $InsertionOrderPreview->Name,
    	         // convert to MySQL DateTime
    			'StartDate'            => $InsertionOrderPreview->StartDate,
    	        'EndDate'              => $InsertionOrderPreview->EndDate,
    	        'Customer'             => $InsertionOrderPreview->Customer,
    	        'CustomerID'           => $InsertionOrderPreview->CustomerID,
    	        'MaxImpressions'       => $InsertionOrderPreview->MaxImpressions,
    	        'MaxSpend'             => $InsertionOrderPreview->MaxSpend,
    	        'Active'               => $InsertionOrderPreview->Active,
    	        'DateUpdated'          => $InsertionOrderPreview->DateUpdated,
    			'ChangeWentLive'       => $InsertionOrderPreview->ChangeWentLive,
    			'WentLiveDate'         => $InsertionOrderPreview->WentLiveDate
    	);

    	$ad_campaign_preview_id = (int)$InsertionOrderPreview->InsertionOrderPreviewID;
    	if ($ad_campaign_preview_id === 0):
    		$data['UserID'] 				= $InsertionOrderPreview->UserID;
    		$data['DateCreated'] 			= $InsertionOrderPreview->DateCreated;
    		$data['ImpressionsCounter']   	= $InsertionOrderPreview->ImpressionsCounter;
    		$data['CurrentSpend']         	= $InsertionOrderPreview->CurrentSpend;
    		$this->insert($data);
    		return $this->getLastInsertValue();
    	else: 
    		$this->update($data, array('InsertionOrderPreviewID' => $ad_campaign_preview_id));
    		return $ad_campaign_preview_id;
    	endif;
    }

    public function deleteInsertionOrderPreview($ad_campaign_preview_id) {
    	$this->delete(array('InsertionOrderPreviewID' => $ad_campaign_preview_id));
    }

    public function deActivateInsertionOrderPreview($ad_campaign_preview_id) {

    	$params = array();
    	$params["InsertionOrderPreviewID"] = $ad_campaign_preview_id;
    	$InsertionOrderPreview = $this->get_row($params);

    	$InsertionOrderPreview->Active = 0;
    	// get array of data
    	$data = $InsertionOrderPreview->getArrayCopy();

    	$this->update($data, array('InsertionOrderPreviewID' => $ad_campaign_preview_id));
    }

    public function doDeletedInsertionOrderPreview($ad_campaign_preview_id) {

    	$params = array();
    	$params["InsertionOrderPreviewID"] = $ad_campaign_preview_id;
    	$InsertionOrderPreview = $this->get_row($params);

    	$InsertionOrderPreview->Deleted = 1;
    	// get array of data
    	$data = $InsertionOrderPreview->getArrayCopy();

    	$this->update($data, array('InsertionOrderPreviewID' => $ad_campaign_preview_id));
    }

};
