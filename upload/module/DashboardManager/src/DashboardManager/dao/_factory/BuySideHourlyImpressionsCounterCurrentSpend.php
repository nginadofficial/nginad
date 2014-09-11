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

class BuySideHourlyImpressionsCounterCurrentSpend extends \_factory\CachedTableRead {

    static protected $instance = null;

    public static function get_instance() {

        if (self::$instance == null):
            self::$instance = new \_factory\BuySideHourlyImpressionsCounterCurrentSpend();
        endif;
        return self::$instance;
    }

    function __construct() {

        $this->table = 'BuySideHourlyImpressionsCounterCurrentSpend';
        $this->featureSet = new Feature\FeatureSet();
        $this->featureSet->addFeature(new Feature\GlobalAdapterFeature());

        $this->adminFields = array_merge($this->adminFields, array(
            'BuySidePartnerID',
            'AverageBidNet',
            'AverageBidCurrentSpendNet',
            'CurrentSpendNet'
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
            $select->order('BuySideHourlyImpressionsCounterCurrentSpendID');
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
            $select->order('BuySideHourlyImpressionsCounterCurrentSpendID');
        }
        );

        foreach ($resultSet as $obj):
            $obj_list[] = $obj;
        endforeach;

        return $obj_list;
    }

    public function getPerTime($where_params = null, $is_admin = false) {
        $obj_list = array();

        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->from('impressionsCurrentSpendPerTime');
        if (!empty($where_params['DateCreatedGreater'])):
            $select->where(
                    $select->where->greaterThanOrEqualTo('impressionsCurrentSpendPerTime.DateCreated', $where_params['DateCreatedGreater'])
            );
        endif;

        if (!empty($where_params['DateCreatedLower'])):
            $select->where(
                    $select->where->lessThanOrEqualTo('impressionsCurrentSpendPerTime.DateCreated', $where_params['DateCreatedLower'])
            );
        endif;

        $statement = $sql->prepareStatementForSqlObject($select);
        
        $results = $statement->execute();
        $headers = $this->getPerTimeHeader($is_admin);

        return $this->prepareList($results, $headers);

    }

    public function getPerTimeHeader($is_admin = false) {

        return ($is_admin) ? array(
            'BuySidePartnerID' => '',
            'MDYH' => '',
            'ImpressionsCounter' => '',
            'CurrentSpendGross' => '',
            'CurrentSpendNet' => '',
            'AverageBidCurrentSpendNet' => '',
            'AverageBidCurrentSpendGross' => '',
            'DateCreated' => '',
            'DateUpdated' => '',
            'Name' => ''
                ) : array(
            'MDYH' => '',
            'ImpressionsCounter' => '',
            'CurrentSpendGross' => '',
            'AverageBidCurrentSpendGross' => '',
            'DateCreated' => '',
            'DateUpdated' => '',
            'Name' => ''
        );
    }

//    public function getUserImpressionsSpendAdmin() {
//
//        $sql = new Sql($this->adapter);
//        $select = $sql->select();
//        $select->from('userImpressionsSpendAdmin');
//
//        $statement = $sql->prepareStatementForSqlObject($select);
//        $results = $statement->execute();
//
//        $obj_list = array();
//        foreach ($results as $obj):
//            $obj_list[] = (object) $obj;
//        endforeach;
//
//        return $obj_list;
//    }

    public function getUserImpressionsSpend($is_admin = false) {

        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->from('userImpressionsSpend');

        $statement = $sql->prepareStatementForSqlObject($select);
        $results = $statement->execute();

        $obj_list = array();
        $headers = $this->getUserImpressionsSpendHeaders($is_admin);
        
        return $this->prepareList($results, $headers);
        
    }

    public function getUserImpressionsSpendHeaders($is_admin = false) {

        return ($is_admin) ? array(
            'BuySidePartnerID' => '',
            'TotalSpendGross' => '',
            'TotalSpendNet' => '',
            'Name' => '',
            'user_login' => '',
                ) : array(
            'BuySidePartnerID' => '',
            'TotalSpendGross' => '',
            'Name' => '',
            'user_login' => '',
        );
    }

    public function getUserImpressionsSpendHeadersAdmin() {

        $metadata = new Metadata($this->adapter);
        return $metadata->getColumnNames('userImpressionsSpendAdmin');
    }

    public function insertBuySideHourlyImpressionsCounterCurrentSpend(\model\BuySideHourlyImpressionsCounterCurrentSpend $BuySideHourlyImpressionsCounterCurrentSpend) {

        $data = array(
            'BuySidePartnerID' => $BuySideHourlyImpressionsCounterCurrentSpend->BuySidePartnerID,
            'AdCampaignBannerID' => $BuySideHourlyImpressionsCounterCurrentSpend->AdCampaignBannerID,
            'MDYH' => $BuySideHourlyImpressionsCounterCurrentSpend->MDYH,
            'ImpressionsCounter' => $BuySideHourlyImpressionsCounterCurrentSpend->ImpressionsCounter,
            'CurrentSpendGross' => $BuySideHourlyImpressionsCounterCurrentSpend->CurrentSpendGross,
            'CurrentSpendNet' => $BuySideHourlyImpressionsCounterCurrentSpend->CurrentSpendNet,
            'DateCreated' => $BuySideHourlyImpressionsCounterCurrentSpend->DateCreated
        );

        $this->insert($data);
    }

    public function updateBuySideHourlyImpressionsCounterCurrentSpend(\model\BuySideHourlyImpressionsCounterCurrentSpend $BuySideHourlyImpressionsCounterCurrentSpend) {
        $data = array(
            'ImpressionsCounter' => $BuySideHourlyImpressionsCounterCurrentSpend->ImpressionsCounter,
            'CurrentSpendGross' => $BuySideHourlyImpressionsCounterCurrentSpend->CurrentSpendGross,
            'CurrentSpendNet' => $BuySideHourlyImpressionsCounterCurrentSpend->CurrentSpendNet
        );
        $buyside_hourly_impressions_counter_current_spend_id = (int) $BuySideHourlyImpressionsCounterCurrentSpend->BuySideHourlyImpressionsCounterCurrentSpendID;
        $this->update($data, array('BuySideHourlyImpressionsCounterCurrentSpendID' => $buyside_hourly_impressions_counter_current_spend_id));
    }

}

;
