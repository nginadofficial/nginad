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

class InsertionOrderLineItemRestrictionsPreview extends AbstractTableGateway
{

	static protected $instance = null;

	public static function get_instance() {

		if (self::$instance == null):
			self::$instance = new \_factory\InsertionOrderLineItemRestrictionsPreview();
		endif;
		return self::$instance;
	}


    function __construct() {

            $this->table = 'InsertionOrderLineItemRestrictionsPreview';
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
        	$select->order('InsertionOrderLineItemRestrictionsPreviewID');

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
        		$select->order('InsertionOrderLineItemRestrictionsPreviewID');

        	}
    	);

    	    foreach ($resultSet as $obj):
    	        $obj_list[] = $obj;
    	    endforeach;

    		return $obj_list;
    }

    public function saveInsertionOrderLineItemRestrictionsPreview(\model\InsertionOrderLineItemRestrictionsPreview $BannerRestrictionsPreview) {
    	$data = array(
    	        'InsertionOrderLineItemPreviewID'                 => $BannerRestrictionsPreview->InsertionOrderLineItemPreviewID,
    	        'GeoCountry'                       			=> $BannerRestrictionsPreview->GeoCountry === "" ? null : $BannerRestrictionsPreview->GeoCountry,
    	        'GeoState'                         			=> $BannerRestrictionsPreview->GeoState === "" ? null : $BannerRestrictionsPreview->GeoState,
    	        'GeoCity'                          			=> $BannerRestrictionsPreview->GeoCity === "" ? null : $BannerRestrictionsPreview->GeoCity,
    	        'AdTagType'                        			=> $BannerRestrictionsPreview->AdTagType === "" ? null : $BannerRestrictionsPreview->AdTagType,
    	        'AdPositionMinLeft'                			=> $BannerRestrictionsPreview->AdPositionMinLeft === "" ? null : $BannerRestrictionsPreview->AdPositionMinLeft,
    	        'AdPositionMaxLeft'                			=> $BannerRestrictionsPreview->AdPositionMaxLeft === "" ? null : $BannerRestrictionsPreview->AdPositionMaxLeft,
    	        'AdPositionMinTop'                 			=> $BannerRestrictionsPreview->AdPositionMinTop === "" ? null : $BannerRestrictionsPreview->AdPositionMinTop,
    	        'AdPositionMaxTop'                 			=> $BannerRestrictionsPreview->AdPositionMaxTop === "" ? null : $BannerRestrictionsPreview->AdPositionMaxTop,
    	        'FoldPos'                          			=> $BannerRestrictionsPreview->FoldPos === "" ? null : $BannerRestrictionsPreview->FoldPos,
    	        'Freq'                             			=> $BannerRestrictionsPreview->Freq === "" ? null : $BannerRestrictionsPreview->Freq,
    	        'Timezone'                         			=> $BannerRestrictionsPreview->Timezone === "" ? null : $BannerRestrictionsPreview->Timezone,
    	        'InIframe'                         			=> $BannerRestrictionsPreview->InIframe === "" ? null : $BannerRestrictionsPreview->InIframe,
    	        'MinScreenResolutionWidth'         			=> $BannerRestrictionsPreview->MinScreenResolutionWidth === "" ? null : $BannerRestrictionsPreview->MinScreenResolutionWidth,
    	        'MaxScreenResolutionWidth'         			=> $BannerRestrictionsPreview->MaxScreenResolutionWidth === "" ? null : $BannerRestrictionsPreview->MaxScreenResolutionWidth,
    	        'MinScreenResolutionHeight'        			=> $BannerRestrictionsPreview->MinScreenResolutionHeight === "" ? null : $BannerRestrictionsPreview->MinScreenResolutionHeight,
    	        'MaxScreenResolutionHeight'        			=> $BannerRestrictionsPreview->MaxScreenResolutionHeight === "" ? null : $BannerRestrictionsPreview->MaxScreenResolutionHeight,
    	        'HttpLanguage'                     			=> $BannerRestrictionsPreview->HttpLanguage === "" ? null : $BannerRestrictionsPreview->HttpLanguage,
    	        'BrowserUserAgentGrep'             			=> $BannerRestrictionsPreview->BrowserUserAgentGrep === "" ? null : $BannerRestrictionsPreview->BrowserUserAgentGrep,
    	        'Secure'                           			=> $BannerRestrictionsPreview->Secure === "" ? null : $BannerRestrictionsPreview->Secure,
    	        'Optout'                           			=> $BannerRestrictionsPreview->Optout === "" ? null : $BannerRestrictionsPreview->Optout,
    	        'Vertical'                         			=> $BannerRestrictionsPreview->Vertical === "" ? null : $BannerRestrictionsPreview->Vertical,
    			'DateCreated'                      			=> $BannerRestrictionsPreview->DateCreated
    	);

    	$banner_preview_id = $BannerRestrictionsPreview->InsertionOrderLineItemPreviewID;
    	$params = array();
    	$params["InsertionOrderLineItemPreviewID"] = $banner_preview_id;
    	$_banner_restrictions_preview = $this->get_row($params);

    	$banner_restrictions_preview_id = (int)$BannerRestrictionsPreview->InsertionOrderLineItemRestrictionsPreviewID;

    	if ($banner_restrictions_preview_id === 0 && $_banner_restrictions_preview === null): 
    		$this->insert($data);
    		return $this->getLastInsertValue();
    	else: 
    		if ($banner_restrictions_preview_id === 0):
    			$banner_restrictions_preview_id = $_banner_restrictions_preview->InsertionOrderLineItemRestrictionsID;
    		endif;
    		$this->update($data, array('InsertionOrderLineItemRestrictionsPreviewID' => $banner_restrictions_preview_id));
    		return $banner_restrictions_preview_id;
    	endif;
    }

    public function deleteInsertionOrderLineItemRestrictionsPreview($banner_restrictions_preview_id) {
    	$this->delete(array('InsertionOrderLineItemPreviewID' => $banner_restrictions_preview_id));
    }

};
