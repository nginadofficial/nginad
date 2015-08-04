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
use Zend\Db\Sql\Sql;

class BuySideHourlyImpressionsByTLD extends \_factory\CachedTableRead {

    static protected $instance = null;

    public static function get_instance() {

        if (self::$instance == null):
            self::$instance = new \_factory\BuySideHourlyImpressionsByTLD();
        endif;
        return self::$instance;
    }

    function __construct() {

        $this->table = 'BuySideHourlyImpressionsByTLD';
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
            $select->order('BuySideHourlyImpressionsByTLDID');
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
            $select->order('BuySideHourlyImpressionsByTLDID');
        }
        );

        foreach ($resultSet as $obj):
            $obj_list[] = $obj;
        endforeach;

        return $obj_list;
    }

    public function getPerTime($where_params = null, $is_super_admin = false, $is_domain_admin = false) {

        $results = $this->select(function (\Zend\Db\Sql\Select $select) use ($where_params) {
            $select->columns(array('InsertionOrderLineItemID', 'PublisherTLD', 'MDYH', 'Impressions', 'DateCreated', 'DateUpdated'));
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

            $select->order('InsertionOrderLineItemID');
        }
        );
        
        $headers = $this->getPerTimeHeader($is_super_admin);
        return $this->prepareList($results, $headers);

    }

    public function getPerTimeHeader($is_super_admin = false) {

        return ($is_super_admin) ? array(
            'InsertionOrderLineItemID' => '',
            'PublisherTLD' => '',
            'MDYH' => '',
            'Impressions' => '',
            'DateCreated' => '',
            'DateUpdated' => ''
                ) : array(
            'InsertionOrderLineItemID' => '',
            'PublisherTLD' => '',
            'MDYH' => '',
            'Impressions' => '',
            'DateCreated' => '',
            'DateUpdated' => ''
        );
    }

    public function getUserTLDStatistic($where_params){

        $obj_list = array();

        $resultSet = $this->select(function (\Zend\Db\Sql\Select $select) use ($where_params) {
                
                
                $select->columns(array('PublisherTLD', 'total_impressions' => new \Zend\Db\Sql\Expression('SUM(' . $this->table  . '.impressions)')));
                
                $select->join(
                     'InsertionOrderLineItem',
                     $this->table . '.InsertionOrderLineItemID = InsertionOrderLineItem.InsertionOrderLineItemID',
                     array()
                );

                $select->join(
                     'InsertionOrder',
                     'InsertionOrderLineItem.InsertionOrderID = InsertionOrder.InsertionOrderID',
                     array('Name')
                );

                $select->join(
                     'auth_Users',
                     'auth_Users.user_id = InsertionOrderLineItem.UserID',
                     array('user_login')
                );

                foreach ($where_params as $name => $value):
	                $select->where(
	                		$select->where->equalTo($name, $value)
	                );
                endforeach;
                
                $select->group('InsertionOrderLineItem.UserID');
                $select->group('PublisherTLD');
                $select->order('PublisherTLD');

            }
        );

        foreach ($resultSet as $obj):
            $obj_list[] = $obj;
        endforeach;

        return $obj_list;
        
    }
    
    public function getUserTLDStatisticHeader($is_super_admin = false) {

        return ($is_super_admin) ? array(
            'PublisherTLD' => 'Publisher TLD',
            'total_impressions' => 'Total impressions',
            'Name' => 'Ad Campaign',
            'user_login' => 'User',
                ) : array(
            'PublisherTLD' => 'Publisher TLD',
            'total_impressions' => 'Total impressions',
            'Name' => 'Ad Campaign',
            'user_login' => 'User',
        );
    }

    public function insertBuySideHourlyImpressionsByTLD(\model\BuySideHourlyImpressionsByTLD $BuySideHourlyImpressionsByTLD) {
        $data = array(
            'InsertionOrderLineItemID' => $BuySideHourlyImpressionsByTLD->InsertionOrderLineItemID,
            'MDYH' => $BuySideHourlyImpressionsByTLD->MDYH,
            'PublisherTLD' => $BuySideHourlyImpressionsByTLD->PublisherTLD,
            'Impressions' => $BuySideHourlyImpressionsByTLD->Impressions,
            'DateCreated' => $BuySideHourlyImpressionsByTLD->DateCreated
        );

        $this->insert($data);
    }

    public function updateBuySideHourlyImpressionsByTLD(\model\BuySideHourlyImpressionsByTLD $BuySideHourlyImpressionsByTLD) {
        $data = array(
            'Impressions' => $BuySideHourlyImpressionsByTLD->Impressions
        );

        $buyside_hourly_impressions_by_tld_id = (int) $BuySideHourlyImpressionsByTLD->BuySideHourlyImpressionsByTLDID;
        $this->update($data, array('BuySideHourlyImpressionsByTLDID' => $buyside_hourly_impressions_by_tld_id));
    }

}

;
