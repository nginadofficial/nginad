<?php
/**
 * NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2016 NginAd Foundation. All Rights Reserved
 * @license GPLv3
 */

namespace _factory;

use Zend\Db\TableGateway\Feature;

class InsertionOrderLineItemDomainExclusion extends \_factory\CachedTableRead
{

	static protected $instance = null;

	public static function get_instance() {

		if (self::$instance == null):
			self::$instance = new \_factory\InsertionOrderLineItemDomainExclusion();
		endif;
		return self::$instance;
	}


    function __construct() {

            $this->table = 'InsertionOrderLineItemDomainExclusion';
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
        	$select->order('InsertionOrderLineItemDomainExclusionID');

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
        		$select->order('InsertionOrderLineItemDomainExclusionID');

        	}
    	);

    	    foreach ($resultSet as $obj):
    	        $obj_list[] = $obj;
    	    endforeach;

    		return $obj_list;
    }

    public function saveInsertionOrderLineItemDomainExclusion(\model\InsertionOrderLineItemDomainExclusion $BannerDomainExclusion) {
    	$data = array(
    	        'InsertionOrderLineItemID'                        => $BannerDomainExclusion->InsertionOrderLineItemID,
    			'ExclusionType'                        		=> $BannerDomainExclusion->ExclusionType,
    	        'DomainName'                           		=> $BannerDomainExclusion->DomainName,
    			'DateCreated'                          		=> $BannerDomainExclusion->DateCreated
    	);
    	$banner_domain_exclusion_id = (int)$BannerDomainExclusion->InsertionOrderLineItemDomainExclusionID;
    	if ($banner_domain_exclusion_id === 0): 
    		$this->insert($data);
    		return $this->getLastInsertValue();
    	else: 
    		$this->update($data, array('InsertionOrderLineItemDomainExclusionID' => $banner_domain_exclusion_id));
    		return $banner_domain_exclusion_id;
    	endif;
    }

    public function deleteInsertionOrderLineItemDomainExclusion($banner_domain_exclusion_id) {
        $this->delete(array('InsertionOrderLineItemDomainExclusionID' => $banner_domain_exclusion_id));
    }

    public function deleteInsertionOrderLineItemDomainExclusionByBannerID($banner_id) {
    	$this->delete(array('InsertionOrderLineItemID' => $banner_id));
    }
};