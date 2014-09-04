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

class AdCampainMarkup extends \_factory\CachedTableRead
{

	static protected $instance = null;

	public static function get_instance() {

		if (self::$instance == null):
			self::$instance = new \_factory\AdCampainMarkup();
		endif;
		return self::$instance;
	}


    function __construct() {

            $this->table = 'AdCampainMarkup';
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

    public function insertAdCampainMarkup(\model\AdCampainMarkup $AdCampainMarkup) {
    	$data = array(
    	        'AdCampaignID'         	=> $AdCampainMarkup->AdCampaignID,
    			'MarkupRate'        	=> $AdCampainMarkup->MarkupRate
    	);

    	$this->insert($data);
    }

    public function updateAdCampainMarkup(\model\AdCampainMarkup $AdCampainMarkup) {
    	$data = array(
    	        'AdCampaignID'         	=> $AdCampainMarkup->AdCampaignID,
    			'MarkupRate'        	=> $AdCampainMarkup->MarkupRate
    	);

    	$ad_campaign_id = $AdCampainMarkup->AdCampaignID;
    	$this->update($data, array('AdCampaignID' => $ad_campaign_id));
    }

    public function deleteMaintenanceRecord($ad_campaign_id) {
    	$this->delete(array('AdCampaignID' => $ad_campaign_id));
    }

};
