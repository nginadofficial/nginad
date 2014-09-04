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

class AdCampaignBannerDomainExclusion extends \_factory\CachedTableRead
{

	static protected $instance = null;

	public static function get_instance() {

		if (self::$instance == null):
			self::$instance = new \_factory\AdCampaignBannerDomainExclusion();
		endif;
		return self::$instance;
	}


    function __construct() {

            $this->table = 'AdCampaignBannerDomainExclusion';
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
        	$select->order('AdCampaignBannerDomainExclusionID');

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
        		$select->order('AdCampaignBannerDomainExclusionID');

        	}
    	);

    	    foreach ($resultSet as $obj):
    	        $obj_list[] = $obj;
    	    endforeach;

    		return $obj_list;
    }

    public function saveAdCampaignBannerDomainExclusion(\model\AdCampaignBannerDomainExclusion $BannerDomainExclusion) {
    	$data = array(
    	        'AdCampaignBannerID'                        => $BannerDomainExclusion->AdCampaignBannerID,
    			'ExclusionType'                        		=> $BannerDomainExclusion->ExclusionType,
    	        'DomainName'                           		=> $BannerDomainExclusion->DomainName,
    			'DateCreated'                          		=> $BannerDomainExclusion->DateCreated
    	);
    	$banner_domain_exclusion_id = (int)$BannerDomainExclusion->AdCampaignBannerDomainExclusionID;
    	if ($banner_domain_exclusion_id === 0): 
    		$this->insert($data);
    	else: 
    		$this->update($data, array('AdCampaignBannerDomainExclusionID' => $banner_domain_exclusion_id));
    	endif;
    }

    public function deleteAdCampaignBannerDomainExclusion($banner_domain_exclusion_id) {
        $this->delete(array('AdCampaignBannerDomainExclusionID' => $banner_domain_exclusion_id));
    }

    public function deleteAdCampaignBannerDomainExclusionByBannerID($banner_id) {
    	$this->delete(array('AdCampaignBannerID' => $banner_id));
    }
};