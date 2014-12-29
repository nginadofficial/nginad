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

class SellSidePartnerHourlyBids extends \_factory\CachedTableRead {

    static protected $instance = null;
    static $visibleAdminFiealds = array();
    static $visibleUserFiealds = array();

    public static function get_instance() {

        if (self::$instance == null):
            self::$instance = new \_factory\SellSidePartnerHourlyBids();
        endif;
        return self::$instance;
    }

    function __construct() {

        $this->table = 'SellSidePartnerHourlyBids';
        $this->featureSet = new Feature\FeatureSet();
        $this->featureSet->addFeature(new Feature\GlobalAdapterFeature());

        $this->adminFields = array_merge($this->adminFields, array(
            'SellSidePartnerID',
            'AverageBidNet'
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
            $select->order('SellSidePartnerHourlyBidsID');
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
            $select->order('SellSidePartnerHourlyBidsID');
        }
        );

        foreach ($resultSet as $obj):
            $obj_list[] = $obj;
        endforeach;

        return $obj_list;
    }

    public function getPerTime($where_params = null, $is_admin = 0) {

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
        
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->from('sellSidePartnerHourlyBidsPerTime');
        if (!empty($where_params['DateCreatedGreater'])):
            $select->where(
                    $select->where->greaterThanOrEqualTo('DateCreated', $where_params['DateCreatedGreater'])
            );
        endif;

        if (!empty($where_params['DateCreatedLower'])):
            $select->where(
                    $select->where->lessThanOrEqualTo('DateCreated', $where_params['DateCreatedLower'])
            );
        endif;

        $statement = $sql->prepareStatementForSqlObject($select);
        $results = $statement->execute();

        foreach ($results as $obj):
            if (!$is_admin) {
                array_walk($obj, function($item, $key) use (&$obj) {
                    if (array_search($key, $this->adminFields) !== FALSE) {
                        $obj[$key] = FALSE;
                    }
                });
                $obj = array_filter($obj, function($value) {
                    return $value !== FALSE;
                });
            }
            
            if (empty($obj['AverageBidNet'])):
            	$obj['AverageBidNet'] = 0;
            endif;
            
            if (empty($obj['AverageBidGross'])):
            	$obj['AverageBidGross'] = 0;
            endif;
            
            if($list_date_span === true):
           		$obj['MDYH'] = 'DATE SPAN';
            else:
           		$obj['MDYH'] = $this->re_normalize_time($obj['MDYH']);
            endif;
            $obj_list[] = $obj;
        endforeach;

        return $obj_list;
    }

    public function getPerTimeHeader($is_admin = false) {

        $metadata = new Metadata($this->adapter);
        $header = $metadata->getColumnNames('sellSidePartnerHourlyBidsPerTime');
        return ($is_admin) ? $header : array_values(array_diff($header, $this->adminFields));
    }

    public function insertSellSidePartnerHourlyBids(\model\SellSidePartnerHourlyBids $SellSidePartnerHourlyBids) {
        $data = array(
            'SellSidePartnerID' => $SellSidePartnerHourlyBids->SellSidePartnerID,
            'PublisherAdZoneID' => $SellSidePartnerHourlyBids->PublisherAdZoneID,
            'MDYH' => $SellSidePartnerHourlyBids->MDYH,
            'BidsWonCounter' => $SellSidePartnerHourlyBids->BidsWonCounter,
            'BidsLostCounter' => $SellSidePartnerHourlyBids->BidsLostCounter,
            'BidsErrorCounter' => $SellSidePartnerHourlyBids->BidsErrorCounter,
            'SpendTotalGross' => $SellSidePartnerHourlyBids->SpendTotalGross,
            'SpendTotalNet' => $SellSidePartnerHourlyBids->SpendTotalNet,
            'DateCreated' => $SellSidePartnerHourlyBids->DateCreated
        );
        $this->insert($data);
    }

    public function updateSellSidePartnerHourlyBids(\model\SellSidePartnerHourlyBids $SellSidePartnerHourlyBids) {
        $data = array(
            'BidsWonCounter' => $SellSidePartnerHourlyBids->BidsWonCounter,
            'BidsLostCounter' => $SellSidePartnerHourlyBids->BidsLostCounter,
            'BidsErrorCounter' => $SellSidePartnerHourlyBids->BidsErrorCounter,
            'SpendTotalGross' => $SellSidePartnerHourlyBids->SpendTotalGross,
            'SpendTotalNet' => $SellSidePartnerHourlyBids->SpendTotalNet,
        );

        $sellside_partner_hourly_bids_id = (int) $SellSidePartnerHourlyBids->SellSidePartnerHourlyBidsID;
        $this->update($data, array('SellSidePartnerHourlyBidsID' => $sellside_partner_hourly_bids_id));
    }

}

;
