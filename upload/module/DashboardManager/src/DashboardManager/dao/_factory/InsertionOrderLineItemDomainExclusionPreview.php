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

class InsertionOrderLineItemDomainExclusionPreview extends AbstractTableGateway
{

	static protected $instance = null;

	public static function get_instance() {

		if (self::$instance == null):
			self::$instance = new \_factory\InsertionOrderLineItemDomainExclusionPreview();
		endif;
		return self::$instance;
	}


    function __construct() {

            $this->table = 'InsertionOrderLineItemDomainExclusionPreview';
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
        	$select->order('InsertionOrderLineItemDomainExclusionPreviewID');

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
        		$select->order('InsertionOrderLineItemDomainExclusionPreviewID');

        	}
    	);

    	    foreach ($resultSet as $obj):
    	        $obj_list[] = $obj;
    	    endforeach;

    		return $obj_list;
    }

    public function saveInsertionOrderLineItemDomainExclusionPreview(\model\InsertionOrderLineItemDomainExclusionPreview $BannerDomainExclusionPreview) {
    	$data = array(
    	        'InsertionOrderLineItemPreviewID'                          	=> $BannerDomainExclusionPreview->InsertionOrderLineItemPreviewID,
    			'ExclusionType'                        					=> $BannerDomainExclusionPreview->ExclusionType,
    	        'DomainName'                           					=> $BannerDomainExclusionPreview->DomainName,
    			'DateCreated'                          					=> $BannerDomainExclusionPreview->DateCreated
    	);
    	$banner_domain_exclusion_preview_id = (int)$BannerDomainExclusionPreview->InsertionOrderLineItemDomainExclusionPreviewID;
    	if ($banner_domain_exclusion_preview_id === 0): 
 
    		$this->insert($data);
    		return $this->getLastInsertValue();
    	else: 
    		$this->update($data, array('InsertionOrderLineItemDomainExclusionPreviewID' => $banner_domain_exclusion_preview_id));
    		return $banner_domain_exclusion_preview_id;
    	endif;
    }

    public function deleteInsertionOrderLineItemDomainExclusionPreview($banner_domain_exclusion_preview_id) {
        $this->delete(array('InsertionOrderLineItemDomainExclusionPreviewID' => $banner_domain_exclusion_preview_id));
    }

    public function deleteInsertionOrderLineItemDomainExclusionByBannerPreviewID($banner_preview_id) {
    	$this->delete(array('InsertionOrderLineItemPreviewID' => $banner_preview_id));
    }
};