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

class AdCampaignPreview extends AbstractTableGateway
{

	static protected $instance = null;

	public static function get_instance() {

		if (self::$instance == null):
			self::$instance = new \_factory\AdCampaignPreview();
		endif;
		return self::$instance;
	}


    function __construct() {

            $this->table = 'AdCampaignPreview';
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
        	$select->order('AdCampaignPreviewID');

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
        		$select->order('AdCampaignPreviewID');

        	}
    	);

    	    foreach ($resultSet as $obj):
    	        $obj_list[] = $obj;
    	    endforeach;

    		return $obj_list;
    }

    public function saveAdCampaignPreview(\model\AdCampaignPreview $AdCampaignPreview) {
    	$data = array(
    	        'AdCampaignID'  	   => $AdCampaignPreview->AdCampaignID,
    			'Name'                 => $AdCampaignPreview->Name,
    	         // convert to MySQL DateTime
    			'StartDate'            => $AdCampaignPreview->StartDate,
    	        'EndDate'              => $AdCampaignPreview->EndDate,
    	        'Customer'             => $AdCampaignPreview->Customer,
    	        'CustomerID'           => $AdCampaignPreview->CustomerID,
    	        'MaxImpressions'       => $AdCampaignPreview->MaxImpressions,
    	        'MaxSpend'             => $AdCampaignPreview->MaxSpend,
    	        'Active'               => $AdCampaignPreview->Active,
    	        'DateUpdated'          => $AdCampaignPreview->DateUpdated,
    			'ChangeWentLive'       => $AdCampaignPreview->ChangeWentLive,
    			'WentLiveDate'         => $AdCampaignPreview->WentLiveDate
    	);

    	$ad_campaign_preview_id = (int)$AdCampaignPreview->AdCampaignPreviewID;
    	if ($ad_campaign_preview_id === 0):
    		$data['UserID'] 				= $AdCampaignPreview->UserID;
    		$data['DateCreated'] 			= $AdCampaignPreview->DateCreated;
    		$data['ImpressionsCounter']   	= $AdCampaignPreview->ImpressionsCounter;
    		$data['CurrentSpend']         	= $AdCampaignPreview->CurrentSpend;
    		$this->insert($data);
    		return $this->getLastInsertValue();
    	else: 
    		$this->update($data, array('AdCampaignPreviewID' => $ad_campaign_preview_id));
    		return null;
    	endif;
    }

    public function deleteAdCampaignPreview($ad_campaign_preview_id) {
    	$this->delete(array('AdCampaignPreviewID' => $ad_campaign_preview_id));
    }

    public function deActivateAdCampaignPreview($ad_campaign_preview_id) {

    	$params = array();
    	$params["AdCampaignPreviewID"] = $ad_campaign_preview_id;
    	$AdCampaignPreview = $this->get_row($params);

    	$AdCampaignPreview->Active = 0;
    	// get array of data
    	$data = $AdCampaignPreview->getArrayCopy();

    	$this->update($data, array('AdCampaignPreviewID' => $ad_campaign_preview_id));
    }

    public function doDeletedAdCampaignPreview($ad_campaign_preview_id) {

    	$params = array();
    	$params["AdCampaignPreviewID"] = $ad_campaign_preview_id;
    	$AdCampaignPreview = $this->get_row($params);

    	$AdCampaignPreview->Deleted = 1;
    	// get array of data
    	$data = $AdCampaignPreview->getArrayCopy();

    	$this->update($data, array('AdCampaignPreviewID' => $ad_campaign_preview_id));
    }

};
