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


class LinkedBannerToAdZone extends \_factory\CachedTableRead
{

	static protected $instance = null;

	public static function get_instance() {

		if (self::$instance == null):
			self::$instance = new \_factory\LinkedBannerToAdZone();
		endif;
		return self::$instance;
	}


    function __construct() {

            $this->table = 'LinkedBannerToAdZone';
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
        	$select->order('LinkedBannerToAdZoneID');

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
        		$select->order('LinkedBannerToAdZoneID');

        	}
    	);

    	    foreach ($resultSet as $obj):
    	        $obj_list[] = $obj;
    	    endforeach;

    		return $obj_list;
    }
    
    public function saveLinkedBannerToAdZone(\model\LinkedBannerToAdZone $LinkedBannerToAdZone) {
    	$data = array(
    			'AdCampaignBannerID'	=> $LinkedBannerToAdZone->AdCampaignBannerID,
    			'PublisherAdZoneID'		=> $LinkedBannerToAdZone->PublisherAdZoneID,
    			'Weight'				=> $LinkedBannerToAdZone->Weight,
    			'DateUpdated'           => $LinkedBannerToAdZone->DateUpdated
    	);
    	
    	$linked_banner_to_ad_zone_id = (int)$LinkedBannerToAdZone->LinkedBannerToAdZoneID;
    	if ($linked_banner_to_ad_zone_id === 0):
	    	$data['DateCreated'] 				= $LinkedBannerToAdZone->DateCreated;
	    	$this->insert($data);
	    	return $this->getLastInsertValue();
    	else:
	    	$this->update($data, array('LinkedBannerToAdZoneID' => $linked_banner_to_ad_zone_id));
	    	return $linked_banner_to_ad_zone_id;
    	endif;
    }

    public function deleteLinkedBannerToAdZone($banner_id) {
    	
    	/*
    	 * First go through each publisherzone and make sure that
    	 * each one no longer is selected for contract
    	 */
    	$PublisherAdZoneFactory = \_factory\PublisherAdZone::get_instance();
    	$LinkedBannerToAdZoneFactory = \_factory\LinkedBannerToAdZone::get_instance();
    	$params = array();
    	$params["AdCampaignBannerID"] = $banner_id;
    	$LinkedBannerToAdZoneList = $LinkedBannerToAdZoneFactory->get($params);
    	
    	if ($LinkedBannerToAdZoneList != null):
	    	foreach ($LinkedBannerToAdZoneList as $LinkedBannerToAdZone):
		    	$params = array();
		    	$params["PublisherAdZoneID"] = $LinkedBannerToAdZone->PublisherAdZoneID;
		    	$LinkedBannerToAdZoneByPublisherAdZoneList = $LinkedBannerToAdZoneFactory->get($params);
		    	if (count($LinkedBannerToAdZoneByPublisherAdZoneList) <= 1):
		    		$PublisherAdZoneFactory->updatePublisherAdZonePublisherAdZoneType($LinkedBannerToAdZone->PublisherAdZoneID, AD_TYPE_ANY_REMNANT);
		    	endif;
	    	endforeach;
    	endif;
    	
    	$this->delete(array('AdCampaignBannerID' => $banner_id));
    }
    
    public function deleteLinkedBannerToAdZoneByPublisherAdZoneID($publisher_ad_zone_id) {
    	
    	/*
    	 * First go through each banner and make sure that 
    	 * each one no longer is selected for contract
    	 */
    	$AdCampaignBannerFactory = \_factory\AdCampaignBanner::get_instance();
    	$LinkedBannerToAdZoneFactory = \_factory\LinkedBannerToAdZone::get_instance();
    	$params = array();
    	$params["PublisherAdZoneID"] = $publisher_ad_zone_id;
    	$LinkedBannerToAdZoneList = $LinkedBannerToAdZoneFactory->get($params);
    	
    	if ($LinkedBannerToAdZoneList != null):
	    	foreach ($LinkedBannerToAdZoneList as $LinkedBannerToAdZone):
		    	$params = array();
		    	$params["AdCampaignBannerID"] = $LinkedBannerToAdZone->AdCampaignBannerID;
		    	$LinkedBannerToAdZoneByAdCampaignBannerList = $LinkedBannerToAdZoneFactory->get($params);
	    		if (count($LinkedBannerToAdZoneByAdCampaignBannerList) <= 1):
	    			$AdCampaignBannerFactory->updateAdCampaignBannerAdCampaignType($LinkedBannerToAdZone->AdCampaignBannerID, AD_TYPE_ANY_REMNANT);
	    		endif;
	    	endforeach;
    	endif;
    	
    	$this->delete(array('PublisherAdZoneID' => $publisher_ad_zone_id));
    }
};
?>