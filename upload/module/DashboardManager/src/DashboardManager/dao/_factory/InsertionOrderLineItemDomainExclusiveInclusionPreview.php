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

class InsertionOrderLineItemDomainExclusiveInclusionPreview extends AbstractTableGateway
{

	static protected $instance = null;

	public static function get_instance() {

		if (self::$instance == null):
			self::$instance = new \_factory\InsertionOrderLineItemDomainExclusiveInclusionPreview();
		endif;
		return self::$instance;
	}


    function __construct() {

            $this->table = 'InsertionOrderLineItemDomainExclusiveInclusionPreview';
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
        	$select->order('InsertionOrderLineItemDomainExclusiveInclusionPreviewID');

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
        		$select->order('InsertionOrderLineItemDomainExclusiveInclusionPreviewID');

        	}
    	);

    	    foreach ($resultSet as $obj):
    	        $obj_list[] = $obj;
    	    endforeach;

    		return $obj_list;
    }

    public function saveInsertionOrderLineItemDomainExclusiveInclusionPreview(\model\InsertionOrderLineItemDomainExclusiveInclusionPreview $BannerDomainExclusiveInclusionPreview) {
    	$data = array(
    	        'InsertionOrderLineItemPreviewID'                     => $BannerDomainExclusiveInclusionPreview->InsertionOrderLineItemPreviewID,
    			'InclusionType'                        			=> $BannerDomainExclusiveInclusionPreview->InclusionType,
    	        'DomainName'                           			=> $BannerDomainExclusiveInclusionPreview->DomainName,
    			'DateCreated'                          			=> $BannerDomainExclusiveInclusionPreview->DateCreated
    	);
    	$banner_domain_exclusive_inclusion_preview_id = (int)$BannerDomainExclusiveInclusionPreview->InsertionOrderLineItemDomainExclusiveInclusionPreviewID;
    	if ($banner_domain_exclusive_inclusion_preview_id === 0): 
    		$this->insert($data);
    		return $this->getLastInsertValue();
    	else: 
    		$this->update($data, array('InsertionOrderLineItemDomainExclusiveInclusionPreviewID' => $banner_domain_exclusive_inclusion_preview_id));
    		return $banner_domain_exclusive_inclusion_preview_id;
    	endif;
    }

    public function deleteInsertionOrderLineItemDomainExclusiveInclusionPreview($banner_domain_exclusive_inclusion_preview_id) {
        $this->delete(array('InsertionOrderLineItemDomainExclusiveInclusionPreviewID' => $banner_domain_exclusive_inclusion_preview_id));
    }

    public function deleteInsertionOrderLineItemDomainExclusiveInclusionByBannerID($banner_id) {
    	$this->delete(array('InsertionOrderLineItemPreviewID' => $banner_id));
    }
};