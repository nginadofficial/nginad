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

class AdCampaignBannerRestrictions extends \_factory\CachedTableRead
{

	static protected $instance = null;

	public static function get_instance() {

		if (self::$instance == null):
			self::$instance = new \_factory\AdCampaignBannerRestrictions();
		endif;
		return self::$instance;
	}


    function __construct() {

            $this->table = 'AdCampaignBannerRestrictions';
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
        	$select->order('AdCampaignBannerRestrictionsID');

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
        		$select->order('AdCampaignBannerRestrictionsID');

        	}
    	);

    	    foreach ($resultSet as $obj):
    	        $obj_list[] = $obj;
    	    endforeach;

    		return $obj_list;
    }

    public function saveAdCampaignBannerRestrictions(\model\AdCampaignBannerRestrictions $BannerRestrictions) {
    	$data = array(

    	        'AdCampaignBannerID'               => $BannerRestrictions->AdCampaignBannerID,
    	        'GeoCountry'                       => $BannerRestrictions->GeoCountry === "" ? null : $BannerRestrictions->GeoCountry,
    	        'GeoState'                         => $BannerRestrictions->GeoState === "" ? null : $BannerRestrictions->GeoState,
    	        'GeoCity'                          => $BannerRestrictions->GeoCity === "" ? null : $BannerRestrictions->GeoCity,
    	        'AdTagType'                        => $BannerRestrictions->AdTagType === "" ? null : $BannerRestrictions->AdTagType,
    	        'AdPositionMinLeft'                => $BannerRestrictions->AdPositionMinLeft === "" ? null : $BannerRestrictions->AdPositionMinLeft,
    	        'AdPositionMaxLeft'                => $BannerRestrictions->AdPositionMaxLeft === "" ? null : $BannerRestrictions->AdPositionMaxLeft,
    	        'AdPositionMinTop'                 => $BannerRestrictions->AdPositionMinTop === "" ? null : $BannerRestrictions->AdPositionMinTop,
    	        'AdPositionMaxTop'                 => $BannerRestrictions->AdPositionMaxTop === "" ? null : $BannerRestrictions->AdPositionMaxTop,
    	        'FoldPos'                          => $BannerRestrictions->FoldPos === "" ? null : $BannerRestrictions->FoldPos,
    	        'Freq'                             => $BannerRestrictions->Freq === "" ? null : $BannerRestrictions->Freq,
    	        'Timezone'                         => $BannerRestrictions->Timezone === "" ? null : $BannerRestrictions->Timezone,
    	        'InIframe'                         => $BannerRestrictions->InIframe === "" ? null : $BannerRestrictions->InIframe,
    	        'InMultipleNestedIframes'          => $BannerRestrictions->InMultipleNestedIframes === "" ? null : $BannerRestrictions->InMultipleNestedIframes,
    	        'MinScreenResolutionWidth'         => $BannerRestrictions->MinScreenResolutionWidth === "" ? null : $BannerRestrictions->MinScreenResolutionWidth,
    	        'MaxScreenResolutionWidth'         => $BannerRestrictions->MaxScreenResolutionWidth === "" ? null : $BannerRestrictions->MaxScreenResolutionWidth,
    	        'MinScreenResolutionHeight'        => $BannerRestrictions->MinScreenResolutionHeight === "" ? null : $BannerRestrictions->MinScreenResolutionHeight,
    	        'MaxScreenResolutionHeight'        => $BannerRestrictions->MaxScreenResolutionHeight === "" ? null : $BannerRestrictions->MaxScreenResolutionHeight,
    	        'HttpLanguage'                     => $BannerRestrictions->HttpLanguage === "" ? null : $BannerRestrictions->HttpLanguage,
    	        'BrowserUserAgentGrep'             => $BannerRestrictions->BrowserUserAgentGrep === "" ? null : $BannerRestrictions->BrowserUserAgentGrep,
    	        'CookieGrep'                       => $BannerRestrictions->CookieGrep === "" ? null : $BannerRestrictions->CookieGrep,
    	        'PmpEnable'                        => $BannerRestrictions->PmpEnable === "" ? null : $BannerRestrictions->PmpEnable,
    	        'Secure'                           => $BannerRestrictions->Secure === "" ? null : $BannerRestrictions->Secure,
    	        'Optout'                           => $BannerRestrictions->Optout === "" ? null : $BannerRestrictions->Optout,
    	        'Vertical'                         => $BannerRestrictions->Vertical === "" ? null : $BannerRestrictions->Vertical,
    			'DateCreated'                      => $BannerRestrictions->DateCreated
    	);

    	$banner_id = $BannerRestrictions->AdCampaignBannerID;
		$params = array();
		$params["AdCampaignBannerID"] = $banner_id;
    	$_banner_restrictions = $this->get_row($params);

    	$banner_restrictions_id = (int)$BannerRestrictions->AdCampaignBannerRestrictionsID;
    	if ($banner_restrictions_id === 0 && $_banner_restrictions === null): 
    		$this->insert($data);
    	else: 
    		if ($banner_restrictions_id === 0):
    			$banner_restrictions_id = $_banner_restrictions->AdCampaignBannerRestrictionsID;
    		endif;
    		$this->update($data, array('AdCampaignBannerRestrictionsID' => $banner_restrictions_id));
    	endif;
    }

    public function deleteAdCampaignBannerRestrictions($banner_id) {
    	$this->delete(array('AdCampaignBannerID' => $banner_id));
    }

};
