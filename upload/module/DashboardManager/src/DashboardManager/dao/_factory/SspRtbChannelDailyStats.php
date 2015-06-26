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

class SspRtbChannelDailyStats extends \_factory\CachedTableRead
{

    static protected $instance = null;

    public static function get_instance() {

            if (self::$instance == null):
                    self::$instance = new \_factory\SspRtbChannelDailyStats();
            endif;
            return self::$instance;
    }
    
    function __construct() {

            $this->table = 'SspRtbChannelDailyStats';
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
        	$select->order('SspRtbChannelDailyStatsID');

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
        		$select->order('SspRtbChannelDailyStatsID');

        	}
    	);

    	    foreach ($resultSet as $obj):
    	        $obj_list[] = $obj;
    	    endforeach;

    		return $obj_list;
    }
    
    public function insertSspRtbChannelDailyStats(\model\SspRtbChannelDailyStats $SspRtbChannelDailyStats) {
    	$data = array(
    			'BuySidePartnerID'      			=> $SspRtbChannelDailyStats->BuySidePartnerID,
    			'SspRtbChannelSiteID'   			=> $SspRtbChannelDailyStats->SspRtbChannelSiteID,
    			'MDYH'   							=> $SspRtbChannelDailyStats->MDYH,
    			'ImpressionsOfferedCounter'   		=> $SspRtbChannelDailyStats->ImpressionsOfferedCounter,
    			'AuctionBidsCounter'   				=> $SspRtbChannelDailyStats->AuctionBidsCounter,
    			'DateCreated'   					=> $SspRtbChannelDailyStats->DateCreated
    	);

    	$this->insert($data);
    }

    public function updateSspRtbChannelDailyStats(\model\SspRtbChannelDailyStats $SspRtbChannelDailyStats) {
    	$data = array(
    			'ImpressionsOfferedCounter'   		=> $SspRtbChannelDailyStats->ImpressionsOfferedCounter,
    			'AuctionBidsCounter'   				=> $SspRtbChannelDailyStats->AuctionBidsCounter
    	);

    	$buyside_hourly_bids_counter_id = (int)$SspRtbChannelDailyStats->SspRtbChannelDailyStatsID;
    	$this->update($data, array('SspRtbChannelDailyStatsID' => $buyside_hourly_bids_counter_id));
    }

};
