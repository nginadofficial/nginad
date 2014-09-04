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

class ReportSubscription extends \_factory\CachedTableRead {

    static protected $instance = null;

    public static function get_instance() {

        if (self::$instance == null):
            self::$instance = new \_factory\ReportSubscription();
        endif;
        return self::$instance;
    }

    function __construct() {

        $this->table = 'ReportSubscription';
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
            
            $select->join(
                 'auth_Users',
                 'auth_Users.user_id = ' . $this->table .  '.UserID',
                 array('*')
            );
        }
        );

        foreach ($resultSet as $obj):
            $obj_list[] = $obj;
        endforeach;

        return $obj_list;
    }

    public function get_row_object($params = null) {
        $rawData = $this->get_row($params);
        $DataObj = new \model\ReportSubscription();
        if ($rawData !== null):

            foreach (get_object_vars($DataObj) AS $key => $value):

                $DataObj->$key = $rawData[$key];
            endforeach;
        endif;

        return $DataObj;
    }

    public function updateReportSubscription(\model\ReportSubscription $ReportSubscription) {

        $data['ReportSubscriptionID'] = $ReportSubscription->ReportSubscriptionID;
        $data['UserID'] = $ReportSubscription->UserID;
        $data['Status'] = $ReportSubscription->Status;

        $ReportSubscriptionID = (int) $ReportSubscription->ReportSubscriptionID;
        if ($ReportSubscriptionID === 0):
            $data['DateCreated'] = $ReportSubscription->DateCreated;
            $this->insert($data);
            return $this->getLastInsertValue();

        else:
            $data['DateUpdated'] = $ReportSubscription->DateUpdated;
            return $this->update($data, array('ReportSubscriptionID' => $ReportSubscriptionID));
        endif;
    }

    public function deleteReportSubscription($ReportSubscriptionID) {
        return $this->delete(array('ReportSubscriptionID' => $ReportSubscriptionID));
    }

}