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

class AdCampaignVideoRestrictionsPreview extends AbstractTableGateway
{

	static protected $instance = null;

	public static function get_instance() {

		if (self::$instance == null):
			self::$instance = new \_factory\AdCampaignVideoRestrictionsPreview();
		endif;
		return self::$instance;
	}


    function __construct() {

            $this->table = 'AdCampaignVideoRestrictionsPreview';
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
        	$select->order('AdCampaignVideoRestrictionsPreviewID');

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
        		$select->order('AdCampaignVideoRestrictionsPreviewID');

        	}
    	);

    	    foreach ($resultSet as $obj):
    	        $obj_list[] = $obj;
    	    endforeach;

    		return $obj_list;
    }

    public function saveAdCampaignVideoRestrictionsPreview($VideoRestrictionsPreview) {
    	$data = array(
    	        'AdCampaignBannerPreviewID'        => $VideoRestrictionsPreview->AdCampaignBannerPreviewID,
    	        'GeoCountry'                       => $VideoRestrictionsPreview->GeoCountry === "" ? null : $VideoRestrictionsPreview->GeoCountry,
    	        'GeoState'                         => $VideoRestrictionsPreview->GeoState === "" ? null : $VideoRestrictionsPreview->GeoState,
    	        'GeoCity'                          => $VideoRestrictionsPreview->GeoCity === "" ? null : $VideoRestrictionsPreview->GeoCity,
    	        'MimesCommaSeparated'              => $VideoRestrictionsPreview->MimesCommaSeparated === "" ? null : $VideoRestrictionsPreview->MimesCommaSeparated,
    			'MinDuration'              		   => $VideoRestrictionsPreview->MinDuration === "" ? null : $VideoRestrictionsPreview->MinDuration,
    			'MaxDuration'              		   => $VideoRestrictionsPreview->MaxDuration === "" ? null : $VideoRestrictionsPreview->MaxDuration,
    			'ApisSupportedCommaSeparated'  	   => $VideoRestrictionsPreview->ApisSupportedCommaSeparated === "" ? null : $VideoRestrictionsPreview->ApisSupportedCommaSeparated,
    			'ProtocolsCommaSeparated'          => $VideoRestrictionsPreview->ProtocolsCommaSeparated === "" ? null : $VideoRestrictionsPreview->ProtocolsCommaSeparated,
    			'DeliveryCommaSeparated'           => $VideoRestrictionsPreview->DeliveryCommaSeparated === "" ? null : $VideoRestrictionsPreview->DeliveryCommaSeparated,
    			'PlaybackCommaSeparated'           => $VideoRestrictionsPreview->PlaybackCommaSeparated === "" ? null : $VideoRestrictionsPreview->PlaybackCommaSeparated,
    			'StartDelay'              		   => $VideoRestrictionsPreview->StartDelay === "" ? null : $VideoRestrictionsPreview->StartDelay,
    			'Linearity'              		   => $VideoRestrictionsPreview->Linearity === "" ? null : $VideoRestrictionsPreview->Linearity,
    			'FoldPos'              		   	   => $VideoRestrictionsPreview->FoldPos === "" ? null : $VideoRestrictionsPreview->FoldPos,
    			'MinHeight'              		   => $VideoRestrictionsPreview->MinHeight === "" ? null : $VideoRestrictionsPreview->MinHeight,
    			'MinWidth'              		   => $VideoRestrictionsPreview->MinWidth === "" ? null : $VideoRestrictionsPreview->MinWidth,
    			'PmpEnable'              		   => $VideoRestrictionsPreview->PmpEnable === "" ? null : $VideoRestrictionsPreview->PmpEnable,
    			'Secure'              		   	   => $VideoRestrictionsPreview->Secure === "" ? null : $VideoRestrictionsPreview->Secure,
    			'Optout'              		   	   => $VideoRestrictionsPreview->Optout === "" ? null : $VideoRestrictionsPreview->Optout,
    	        'Vertical'                         => $VideoRestrictionsPreview->Vertical === "" ? null : $VideoRestrictionsPreview->Vertical,
    			'DateCreated'                      => $VideoRestrictionsPreview->DateCreated
    	);

    	$video_preview_id = $VideoRestrictionsPreview->AdCampaignBannerPreviewID;
    	$params = array();
    	$params["AdCampaignBannerPreviewID"] = $video_preview_id;
    	$_video_restrictions_preview = $this->get_row($params);

    	$video_restrictions_preview_id = (int)$VideoRestrictionsPreview->AdCampaignVideoRestrictionsPreviewID;
    	 
    	if ($video_restrictions_preview_id === 0 && $_video_restrictions_preview === null): 
    		$this->insert($data);
    		return $this->getLastInsertValue();
    	else: 
    		if ($video_restrictions_preview_id === 0):
    			$video_restrictions_preview_id = $_video_restrictions_preview->AdCampaignVideoRestrictionsID;
    		endif;
    		$this->update($data, array('AdCampaignVideoRestrictionsPreviewID' => $video_restrictions_preview_id));
    		return null;
    	endif;
    }

    public function deleteAdCampaignVideoRestrictionsPreview($video_restrictions_preview_id) {
    	$this->delete(array('AdCampaignBannerPreviewID' => $video_restrictions_preview_id));
    }

};
