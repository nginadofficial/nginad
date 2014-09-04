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

class AdCampaignBannerDomainExclusionPreview extends AbstractTableGateway
{

	static protected $instance = null;

	public static function get_instance() {

		if (self::$instance == null):
			self::$instance = new \_factory\AdCampaignBannerDomainExclusionPreview();
		endif;
		return self::$instance;
	}


    function __construct() {

            $this->table = 'AdCampaignBannerDomainExclusionPreview';
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
        	$select->order('AdCampaignBannerDomainExclusionPreviewID');

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
        		$select->order('AdCampaignBannerDomainExclusionPreviewID');

        	}
    	);

    	    foreach ($resultSet as $obj):
    	        $obj_list[] = $obj;
    	    endforeach;

    		return $obj_list;
    }

    public function saveAdCampaignBannerDomainExclusionPreview(\model\AdCampaignBannerDomainExclusionPreview $BannerDomainExclusionPreview) {
    	$data = array(
    	        'AdCampaignBannerPreviewID'                          	=> $BannerDomainExclusionPreview->AdCampaignBannerPreviewID,
    			'ExclusionType'                        					=> $BannerDomainExclusionPreview->ExclusionType,
    	        'DomainName'                           					=> $BannerDomainExclusionPreview->DomainName,
    			'DateCreated'                          					=> $BannerDomainExclusionPreview->DateCreated
    	);
    	$banner_domain_exclusion_preview_id = (int)$BannerDomainExclusionPreview->AdCampaignBannerDomainExclusionPreviewID;
    	if ($banner_domain_exclusion_preview_id === 0): 
 
    		$this->insert($data);
    		return $this->getLastInsertValue();
    	else: 
    		$this->update($data, array('AdCampaignBannerDomainExclusionPreviewID' => $banner_domain_exclusion_preview_id));
    		return null;
    	endif;
    }

    public function deleteAdCampaignBannerDomainExclusionPreview($banner_domain_exclusion_preview_id) {
        $this->delete(array('AdCampaignBannerDomainExclusionPreviewID' => $banner_domain_exclusion_preview_id));
    }

    public function deleteAdCampaignBannerDomainExclusionByBannerPreviewID($banner_preview_id) {
    	$this->delete(array('AdCampaignBannerPreviewID' => $banner_preview_id));
    }
};