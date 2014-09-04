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


class LinkedBannerToAdZonePreview extends AbstractTableGateway
{

	static protected $instance = null;

	public static function get_instance() {

		if (self::$instance == null):
			self::$instance = new \_factory\LinkedBannerToAdZonePreview();
		endif;
		return self::$instance;
	}


    function __construct() {

            $this->table = 'LinkedBannerToAdZonePreview';
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
        	$select->order('LinkedBannerToAdZonePreviewID');

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
        		$select->order('LinkedBannerToAdZonePreviewID');

        	}
    	);

    	    foreach ($resultSet as $obj):
    	        $obj_list[] = $obj;
    	    endforeach;

    		return $obj_list;
    }
   
    public function saveLinkedBannerToAdZonePreview(\model\LinkedBannerToAdZonePreview $LinkedBannerToAdZonePreview) {
    	$data = array(
    			'AdCampaignBannerPreviewID'		=> $LinkedBannerToAdZonePreview->AdCampaignBannerPreviewID,
    			'PublisherAdZoneID'				=> $LinkedBannerToAdZonePreview->PublisherAdZoneID,
    			'Weight'						=> $LinkedBannerToAdZonePreview->Weight,
    			'DateUpdated'           		=> $LinkedBannerToAdZonePreview->DateUpdated
    	);
    	 
    	$linked_banner_to_ad_zone_preview_id 	= (int)$LinkedBannerToAdZonePreview->LinkedBannerToAdZonePreviewID;
    	if ($linked_banner_to_ad_zone_preview_id === 0):
	    	$data['DateCreated'] 				= $LinkedBannerToAdZonePreview->DateCreated;
	    	$this->insert($data);
	    	return $this->getLastInsertValue();
    	else:
	    	$this->update($data, array('LinkedBannerToAdZonePreviewID' => $linked_banner_to_ad_zone_preview_id));
	    	return $linked_banner_to_ad_zone_preview_id;
    	endif;
    }
    
    public function deleteLinkedBannerToAdZonePreview($preview_banner_id) {
    	$this->delete(array('AdCampaignBannerPreviewID' => $preview_banner_id));
    }
    
};
?>