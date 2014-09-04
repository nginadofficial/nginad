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

class AdCampaign extends \_factory\CachedTableRead
{

	static protected $instance = null;

	public static function get_instance() {

		if (self::$instance == null):
			self::$instance = new \_factory\AdCampaign();
		endif;
		return self::$instance;
	}


    function __construct() {

            $this->table = 'AdCampaign';
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
        	$select->order('AdCampaignID');

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
        		$select->order('AdCampaignID');

        	}
    	);

    	    foreach ($resultSet as $obj):
    	        $obj_list[] = $obj;
    	    endforeach;

    		return $obj_list;
    }

    public function saveAdCampaign(\model\AdCampaign $AdCampaign) {
    	$data = array(
    			'Name'                 => $AdCampaign->Name,
    	         // convert to MySQL DateTime
    			'StartDate'            => $AdCampaign->StartDate,
    	        'EndDate'              => $AdCampaign->EndDate,
    	        'Customer'             => $AdCampaign->Customer,
    	        'CustomerID'           => $AdCampaign->CustomerID,
    	        'MaxImpressions'       => $AdCampaign->MaxImpressions,
    	        'MaxSpend'             => $AdCampaign->MaxSpend,
    	        'Active'               => $AdCampaign->Active,
    	        'DateUpdated'          => $AdCampaign->DateUpdated
    	);

    	$ad_campaign_id = (int)$AdCampaign->AdCampaignID;
    	if ($ad_campaign_id === 0): 
    		$data['UserID'] 					= $AdCampaign->UserID;
    		$data['DateCreated'] 				= $AdCampaign->DateCreated;
    		$data['ImpressionsCounter']   		= $AdCampaign->ImpressionsCounter;
    		$data['CurrentSpend']         		= $AdCampaign->CurrentSpend;
    		$this->insert($data);
    		return $this->getLastInsertValue();
    	else: 
    		$this->update($data, array('AdCampaignID' => $ad_campaign_id));
    		return $ad_campaign_id;
        endif;
    }

    public function saveAdCampaignFromDataArray($data) {

    	$this->update($data, array('AdCampaignID' => $data['AdCampaignID']));
    }

    public function deleteAdCampaign($ad_campaign_id) {
    	$this->delete(array('AdCampaignID' => $ad_campaign_id));
    }

    public function deActivateAdCampaign($ad_campaign_id) {

    	$params = array();
    	$params["AdCampaignID"] = $ad_campaign_id;
    	$AdCampaign = $this->get_row($params);

    	$AdCampaign->Active = 0;
    	// get array of data
    	$data = $AdCampaign->getArrayCopy();

    	$this->update($data, array('AdCampaignID' => $ad_campaign_id));
    }

};
