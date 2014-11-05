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
use Zend\Db\Sql\Sql;
use Zend\Db\Metadata\Metadata;

class BuySideHourlyBidsCounter extends \_factory\CachedTableRead
{

    static protected $instance = null;

    public static function get_instance() {

            if (self::$instance == null):
                    self::$instance = new \_factory\BuySideHourlyBidsCounter();
            endif;
            return self::$instance;
    }
    
    function __construct() {

            $this->table = 'BuySideHourlyBidsCounter';
            $this->featureSet = new Feature\FeatureSet();
            $this->featureSet->addFeature(new Feature\GlobalAdapterFeature());
            $this->adminFields = array_merge($this->adminFields, array(
                'BuySidePartnerID',
            ));
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
        	$select->order('AdCampaignBannerID');

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
        		$select->order('AdCampaignBannerID');

        	}
    	);

    	    foreach ($resultSet as $obj):
    	        $obj_list[] = $obj;
    	    endforeach;

    		return $obj_list;
    }

    public function getPerTime($where_params = null, $is_admin = false) {
            
        $obj_list = array();

//        $resultSet = $this->select(function (\Zend\Db\Sql\Select $select) use ($where_params) {
//                
//                // $select->columns(array('BidsCounter'));
//                $select->columns(array('BuySidePartnerID', 'MDYH', 'BidsCounter', 'DateCreated', 'DateUpdated'));
//                if(!empty($where_params['DateCreatedGreater'])):
//                    $select->where(
//                            $select->where->greaterThanOrEqualTo($this->table . '.DateCreated', $where_params['DateCreatedGreater'])
//                    );
//                endif;
//
//                if(!empty($where_params['DateCreatedLower'])):
//                    $select->where(
//                            $select->where->lessThanOrEqualTo($this->table . '.DateCreated', $where_params['DateCreatedLower'])
//                    );
//                endif;
//
//                $select->join(
//                     'AdCampaignBanner',
//                     $this->table . '.AdCampaignBannerID = AdCampaignBanner.AdCampaignBannerID',
//                     array()
//                );
//                
//                $select->join(
//                     'AdCampaign',
//                     'AdCampaignBanner.AdCampaignID = AdCampaign.AdCampaignID',
//                     array('Name')
//                );
//
//                $select->order($this->table . '.AdCampaignBannerID');
//
//            }
//        );
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->from('buySideHourlyBidsPerTime');
        if(!empty($where_params['DateCreatedGreater'])):
            $select->where(
                    $select->where->greaterThanOrEqualTo('buySideHourlyBidsPerTime.DateCreated', $where_params['DateCreatedGreater'])
            );
        endif;

        if(!empty($where_params['DateCreatedLower'])):
            $select->where(
                    $select->where->lessThanOrEqualTo('buySideHourlyBidsPerTime.DateCreated', $where_params['DateCreatedLower'])
            );
        endif;

        $statement = $sql->prepareStatementForSqlObject($select);
        $results = $statement->execute();

        foreach ($results as $obj):
            if(!$is_admin){
                array_walk($obj, function($item, $key) use (&$obj){
                    if(array_search($key, $this->adminFields) !== FALSE){
                        $obj[$key] = FALSE;
                    }
                });
                $obj = array_filter($obj, function($value){
                    return $value !== FALSE;
                });
            }
            $obj['MDYH'] = $this->re_normalize_time($obj['MDYH']);
            $obj_list[] = $obj;
        endforeach;

        return $obj_list;
    }
    
    public function getPerTimeHeader($is_admin = false){
     
        $metadata = new Metadata($this->adapter);
        $header = $metadata->getColumnNames('buySideHourlyBidsPerTime');
        return ($is_admin) ? $header : array_values(array_diff($header, $this->adminFields));
        
    }
    
    public function insertBuySideHourlyBidsCounter(\model\BuySideHourlyBidsCounter $BuySideHourlyBidsCounter) {
    	$data = array(
    			'BuySidePartnerID'      => $BuySideHourlyBidsCounter->BuySidePartnerID,
    			'AdCampaignBannerID'   	=> $BuySideHourlyBidsCounter->AdCampaignBannerID,
    			'MDYH'   				=> $BuySideHourlyBidsCounter->MDYH,
    			'BidsCounter'   		=> $BuySideHourlyBidsCounter->BidsCounter,
    			'DateCreated'   		=> $BuySideHourlyBidsCounter->DateCreated
    	);

    	$this->insert($data);
    }

    public function updateBuySideHourlyBidsCounter(\model\BuySideHourlyBidsCounter $BuySideHourlyBidsCounter) {
    	$data = array(
    			'BidsCounter'   			=> $BuySideHourlyBidsCounter->BidsCounter
    	);

    	$buyside_hourly_bids_counter_id = (int)$BuySideHourlyBidsCounter->BuySideHourlyBidsCounterID;
    	$this->update($data, array('BuySideHourlyBidsCounterID' => $buyside_hourly_bids_counter_id));
    }

};
