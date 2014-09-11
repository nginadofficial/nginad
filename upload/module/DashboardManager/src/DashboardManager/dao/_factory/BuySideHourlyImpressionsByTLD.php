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

    public function getPerTime($where_params = null, $is_admin = false) {

        $results = $this->select(function (\Zend\Db\Sql\Select $select) use ($where_params) {
            $select->columns(array('AdCampaignBannerID', 'PublisherTLD', 'MDYH', 'Impressions', 'DateCreated', 'DateUpdated'));
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

            $select->order('AdCampaignBannerID');
        }
        );
        
        $headers = $this->getPerTimeHeader($is_admin);
        return $this->prepareList($results, $headers);

    }

    public function getPerTimeHeader($is_admin = false) {

        return ($is_admin) ? array(
            'AdCampaignBannerID' => '',
            'PublisherTLD' => '',
            'MDYH' => '',
            'Impressions' => '',
            'DateCreated' => '',
            'DateUpdated' => ''
                ) : array(
            'AdCampaignBannerID' => '',
            'PublisherTLD' => '',
            'MDYH' => '',
            'Impressions' => '',
            'DateCreated' => '',
            'DateUpdated' => ''
        );
    }

    public function getUserTLDStatistic($is_admin = false) {

        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->from('userTLDStatistic');

        $statement = $sql->prepareStatementForSqlObject($select);
        $results = $statement->execute();
        $headers = $this->getUserTLDStatisticHeader($is_admin);
        return $this->prepareList($results, $headers);
    }
    
    
    public function getUserTLDStatisticHeader($is_admin = false) {

        return ($is_admin) ? array(
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
            'AdCampaignBannerID' => $BuySideHourlyImpressionsByTLD->AdCampaignBannerID,
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
