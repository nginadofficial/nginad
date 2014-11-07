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

class PublisherHourlyBids extends \_factory\CachedTableRead {

    static protected $instance = null;
    static $visibleAdminFiealds = array();
    static $visibleUserFiealds = array();

    public static function get_instance() {

        if (self::$instance == null):
            self::$instance = new \_factory\PublisherHourlyBids();
        endif;
        return self::$instance;
    }

    function __construct() {

        $this->table = 'PublisherHourlyBids';
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
            $select->order('PublisherHourlyBidsID');
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
            $select->order('PublisherHourlyBidsID');
        }
        );

        foreach ($resultSet as $obj):
            $obj_list[] = $obj;
        endforeach;

        return $obj_list;
    }

    public function getPerTime($where_params = null, $is_admin = 0) {

        $obj_list = array();

        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->from('publisherHourlyBidsBidsPerTime');
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
            $obj['MDYH'] = $this->re_normalize_time($obj['MDYH']);
            $obj_list[] = $obj;
        endforeach;

        return $obj_list;
    }

    public function getPerTimeHeader($is_admin = false) {

        $metadata = new Metadata($this->adapter);
        $header = $metadata->getColumnNames('publisherHourlyBidsPerTime');
        return ($is_admin) ? $header : array_values(array_diff($header, $this->adminFields));
    }

    public function insertPublisherHourlyBids(\model\PublisherHourlyBids $PublisherHourlyBids) {
        $data = array(
            'PublisherAdZoneID' => $PublisherHourlyBids->PublisherAdZoneID,
            'MDYH' => $PublisherHourlyBids->MDYH,
        	'AuctionCounter' => $PublisherHourlyBids->AuctionCounter,
            'BidsWonCounter' => $PublisherHourlyBids->BidsWonCounter,
            'BidsLostCounter' => $PublisherHourlyBids->BidsLostCounter,
            'BidsErrorCounter' => $PublisherHourlyBids->BidsErrorCounter,
            'SpendTotalGross' => $PublisherHourlyBids->SpendTotalGross,
            'SpendTotalNet' => $PublisherHourlyBids->SpendTotalNet,
            'DateCreated' => $PublisherHourlyBids->DateCreated
        );
        $this->insert($data);
    }

    public function updatePublisherHourlyBids(\model\PublisherHourlyBids $PublisherHourlyBids) {
        $data = array(
        	'AuctionCounter' => $PublisherHourlyBids->AuctionCounter,
            'BidsWonCounter' => $PublisherHourlyBids->BidsWonCounter,
            'BidsLostCounter' => $PublisherHourlyBids->BidsLostCounter,
            'BidsErrorCounter' => $PublisherHourlyBids->BidsErrorCounter,
            'SpendTotalGross' => $PublisherHourlyBids->SpendTotalGross,
            'SpendTotalNet' => $PublisherHourlyBids->SpendTotalNet,
        );

        $publisher_hourly_bids_id = (int) $PublisherHourlyBids->PublisherHourlyBidsID;
        $this->update($data, array('PublisherHourlyBidsID' => $publisher_hourly_bids_id));
    }

}

;
