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

class ContractPublisherZoneHourlyImpressions extends \_factory\CachedTableRead
{

	static protected $instance = null;

	public static function get_instance() {

		if (self::$instance == null):
			self::$instance = new \_factory\ContractPublisherZoneHourlyImpressions();
		endif;
		return self::$instance;
	}


    function __construct() {

            $this->table = 'ContractPublisherZoneHourlyImpressions';
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
        	$select->order('ContractPublisherZoneHourlyImpressionsID');

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
        		$select->order('ContractPublisherZoneHourlyImpressionsID');

        	}
    	);

    	    foreach ($resultSet as $obj):
    	        $obj_list[] = $obj;
    	    endforeach;

    		return $obj_list;
    }

    public function getPerTime($where_params = null) {
            
        $obj_list = array();

        $low_range = $high_range = time();
        
        if (!empty($where_params['DateCreatedGreater'])):
        	$low_range = strtotime($where_params['DateCreatedGreater']);
        endif;
        
        if (!empty($where_params['DateCreatedLower'])):
        	$high_range = strtotime($where_params['DateCreatedLower']);
        endif;
        
        $date_span = $high_range - $low_range;
        
        // if span is greater than 2 days switch to custom reporting format
        $switch_to_custom_threshold = 2 * 86400;
        
        $list_date_span = false;
        
        if ($date_span > $switch_to_custom_threshold):
        	$list_date_span = true;
        endif;
        
        $resultSet = $this->select(function (\Zend\Db\Sql\Select $select) use ($where_params) {
                $select->columns(array('MDYH', 'Impressions', 'SpendTotalNet', 'DateCreated', 'DateUpdated'));
                if(!empty($where_params['DateCreatedGreater'])):
                    $select->where(
                            $select->where->greaterThanOrEqualTo($this->table . '.DateCreated', $where_params['DateCreatedGreater'])
                    );
                endif;

                if(!empty($where_params['DateCreatedLower'])):
                    $select->where(
                            $select->where->lessThanOrEqualTo($this->table . '.DateCreated', $where_params['DateCreatedLower'])
                    );
                endif;

                $select->join(
                     'PublisherAdZone',
                     $this->table . '.PublisherAdZoneID = PublisherAdZone.PublisherAdZoneID',
                     array('AdName')
                );
                
                $select->order($this->table . '.AdCampaignBannerID');

            }
        );

            foreach ($resultSet as $obj):
                $obj['MDYH'] = $this->re_normalize_time($obj['MDYH']);
	            if($list_date_span === true):
	           		$obj['MDYH'] = 'DATE SPAN';
	            else:
	           		$obj['MDYH'] = $this->re_normalize_time($obj['MDYH']);
	            endif;
            endforeach;

            return $obj_list;
    }
    
    public function getPerTimeHeader($is_admin = false) {

        return array('MDYH', 'Impressions', 'SpendTotalNet', 'DateCreated', 'DateUpdated', 'AdName');
    }

    public function getPerZone($where_params = null) {
            
        $obj_list = array();

        $resultSet = $this->select(function (\Zend\Db\Sql\Select $select) use ($where_params) {
                
                $select->columns(array(
                    'ImpressionsTotal' => new \Zend\Db\Sql\Expression('SUM(' . $this->table  . '.Impressions)'),
                    'SpendTotalNet' => new \Zend\Db\Sql\Expression('ROUND(SUM(' . $this->table  . '.SpendTotalNet), 7)'),
                ));

                $select->join(
                     'PublisherAdZone',
                     $this->table . '.PublisherAdZoneID = PublisherAdZone.PublisherAdZoneID',
                     array('AdName')
                );

                $select->join(
                     'PublisherWebsite',
                     'PublisherAdZone.PublisherWebsiteID = PublisherWebsite.PublisherWebsiteID',
                     array('WebDomain')
                );
                
                $select->join(
                     'AdCampaignBanner',
                     $this->table . '.AdCampaignBannerID = AdCampaignBanner.AdCampaignBannerID',
                     array('banner_name' => 'Name')
                );

                $select->join(
                     'auth_Users',
                     'auth_Users.user_id = AdCampaignBanner.UserID',
                     array('user_login')
                );

                $select->group($this->table . '.AdCampaignBannerID');
                $select->group($this->table . '.PublisherAdZoneID');

                $select->order('PublisherWebsite.WebDomain');

            }
        );

            foreach ($resultSet as $obj):
                $obj_list[] = $obj;
            endforeach;

            return $obj_list;
    }

    public function insertContractPublisherZoneHourlyImpressions(\model\ContractPublisherZoneHourlyImpressions $ContractPublisherZoneHourlyImpressions) {
    	$data = array(
    			'AdCampaignBannerID'   		=> $ContractPublisherZoneHourlyImpressions->AdCampaignBannerID,
    			'PublisherAdZoneID'    		=> $ContractPublisherZoneHourlyImpressions->PublisherAdZoneID,
    			'MDYH'						=> $ContractPublisherZoneHourlyImpressions->MDYH,
    			'Impressions'  				=> $ContractPublisherZoneHourlyImpressions->Impressions,
    			'SpendTotalGross'  			=> $ContractPublisherZoneHourlyImpressions->SpendTotalGross,
    			'SpendTotalNet'  			=> $ContractPublisherZoneHourlyImpressions->SpendTotalNet,
    			'DateCreated'   			=> $ContractPublisherZoneHourlyImpressions->DateCreated
    	);
    	$this->insert($data);
    }

    public function updateContractPublisherZoneHourlyImpressions(\model\ContractPublisherZoneHourlyImpressions $ContractPublisherZoneHourlyImpressions) {
    	$data = array(
    			'Impressions'   			=> $ContractPublisherZoneHourlyImpressions->Impressions,
    			'SpendTotalGross'  			=> $ContractPublisherZoneHourlyImpressions->SpendTotalGross,
    			'SpendTotalNet'  			=> $ContractPublisherZoneHourlyImpressions->SpendTotalNet,
    	);

    	$contract_publisher_zone_hourly_impressions_id = (int)$ContractPublisherZoneHourlyImpressions->ContractPublisherZoneHourlyImpressionsID;
    	$this->update($data, array('ContractPublisherZoneHourlyImpressionsID' => $contract_publisher_zone_hourly_impressions_id));
    }

};
