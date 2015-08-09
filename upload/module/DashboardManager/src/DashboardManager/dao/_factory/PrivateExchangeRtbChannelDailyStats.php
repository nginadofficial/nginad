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
use Zend\Db\Metadata\Metadata;

class PrivateExchangeRtbChannelDailyStats extends \_factory\CachedTableRead
{

    static protected $instance = null;

    public static function get_instance() {

            if (self::$instance == null):
                    self::$instance = new \_factory\PrivateExchangeRtbChannelDailyStats();
            endif;
            return self::$instance;
    }
    
    function __construct() {

            $this->table = 'PrivateExchangeRtbChannelDailyStats';
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
        	$select->order('PrivateExchangeRtbChannelDailyStatsID');

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
        		$select->order('PrivateExchangeRtbChannelDailyStatsID');

        	}
    	);

    	    foreach ($resultSet as $obj):
    	        $obj_list[] = $obj;
    	    endforeach;

    		return $obj_list;
    }
    
    public function insertPrivateExchangeRtbChannelDailyStats(\model\PrivateExchangeRtbChannelDailyStats $PrivateExchangeRtbChannelDailyStats) {
    	$data = array(
    			'PublisherWebsiteID'   				=> $PrivateExchangeRtbChannelDailyStats->PublisherWebsiteID,
    			'RtbChannelSiteName'   				=> $PrivateExchangeRtbChannelDailyStats->RtbChannelSiteName,
    			'MDY'   							=> $PrivateExchangeRtbChannelDailyStats->MDY,
    			'MDYH'   							=> $PrivateExchangeRtbChannelDailyStats->MDYH,
    			'ImpressionsOfferedCounter'   		=> $PrivateExchangeRtbChannelDailyStats->ImpressionsOfferedCounter,
    			'AuctionBidsCounter'   				=> $PrivateExchangeRtbChannelDailyStats->AuctionBidsCounter,
    			'BidTotalAmount'   					=> $PrivateExchangeRtbChannelDailyStats->BidTotalAmount,
    			'BidFloor'   						=> $PrivateExchangeRtbChannelDailyStats->BidFloor
    	);

    	$this->insert($data);
    }

    public function updatePrivateExchangeRtbChannelDailyStats(\model\PrivateExchangeRtbChannelDailyStats $PrivateExchangeRtbChannelDailyStats) {
    	$data = array(
    			'ImpressionsOfferedCounter'   		=> $PrivateExchangeRtbChannelDailyStats->ImpressionsOfferedCounter,
    			'AuctionBidsCounter'   				=> $PrivateExchangeRtbChannelDailyStats->AuctionBidsCounter,
    			'BidTotalAmount'   					=> $PrivateExchangeRtbChannelDailyStats->BidTotalAmount,
    			'BidFloor'   						=> $PrivateExchangeRtbChannelDailyStats->BidFloor
    	);

    	$private_exchange_rtb_channel_daily_stats_id = (int)$PrivateExchangeRtbChannelDailyStats->PrivateExchangeRtbChannelDailyStatsID;
    	$this->update($data, array('PrivateExchangeRtbChannelDailyStatsID' => $private_exchange_rtb_channel_daily_stats_id));
    }
    
    public function incrementPrivateExchangeRtbChannelDailyStatsCached($config, $method_params) {
    	
    	$publisher_website_id = $method_params["publisher_website_id"] = $method_params["rtb_channel_site_id"];
    	$rtb_channel_site_name = $method_params["rtb_channel_site_name"];
    	$impressions_offered_counter = $method_params["impressions_offered_counter"];
    	$auction_bids_counter = $method_params["auction_bids_counter"];
    	$spend_offered_in_bids = $method_params["spend_offered_in_bids"];
    	$floor_price_if_any = $method_params["floor_price_if_any"];
    	
    	$params = array();
    	
    	$class_dir_name = 'PrivateExchangeRtbChannelDailyStats';
    	
    	$cached_key_exists = \util\CacheSql::does_cached_write_exist_apc($config, $params, $class_dir_name);

    	if ($cached_key_exists):
    	
	    	// increment bucket
	    	\util\CachedStatsWrites::increment_cached_write_result_private_exchange_channel_stats($config, $params, $class_dir_name, $method_params);
    	
    	else:
    	
	    	/*
	    	 * DO THIS BEFORE APC RESET OPERATIONS TO AVOID THREAD-LIKE DUPLICATION DUE TO THE LACK OF
	    	* A SYNCHRONIZED KEYWORD IN PHP
	    	*/
	    	
	    	// SYNCHRONIZED BLOCK START
	    	\util\CacheSql::create_reset_write_lock($config, $params, $class_dir_name);
	    	
	    	// get value sum from apc
	    	$current = \util\CacheSql::get_cached_read_result_apc_type_convert($config, $params, $class_dir_name);
	    	
	    	// delete existing key - reset bucket
	    	\util\CacheSql::delete_cached_write_apc($config, $params, $class_dir_name);
	    		
	    	// increment bucket
	    	\util\CachedStatsWrites::increment_cached_write_result_private_exchange_channel_stats($config, $params, $class_dir_name, $method_params);
	    	
	    	// SYNCHRONIZED BLOCK END
	    	\util\CacheSql::reset_write_unlock($config, $params, $class_dir_name);
    	
    		if ($current != null):

		    	// write out values
		    	$this->incrementPrivateExchangeRtbChannelDailyStats($config, $current);

		    endif;

    	endif;
    	
    }

    public function incrementPrivateExchangeRtbChannelDailyStats($config, $current) {
    	
    	if (!is_array($current)):
    		return;
    	endif;
    	
    	$PrivateExchangeRtbChannelDailyStatsFactory = \_factory\PrivateExchangeRtbChannelDailyStats::get_instance();
    	
    	foreach ($current as $publisher_website_id => $method_params):

	    	$publisher_website_id = $method_params["publisher_website_id"];
	    	$rtb_channel_site_name = $method_params["rtb_channel_site_name"];
	    	$impressions_offered_counter = $method_params["impressions_offered_counter"];
	    	$auction_bids_counter = $method_params["auction_bids_counter"];
	    	$spend_offered_in_bids = $method_params["spend_offered_in_bids"];
	    	$floor_price_if_any = $method_params["floor_price_if_any"];

	    	$current_hour 	= date("m/d/Y H");
	    	$current_day 	= date("m/d/Y");
	    	
	    	$params = array();
	    	$params["PublisherWebsiteID"] 	= $publisher_website_id;
	    	$params["MDYH"] 				= $current_hour;
	    	$PrivateExchangeRtbChannelDailyStats 		= $PrivateExchangeRtbChannelDailyStatsFactory->get_row($params);
	    	
	    	$private_exchange_rtb_channel_daily_stats = new \model\PrivateExchangeRtbChannelDailyStats();
	    	$private_exchange_rtb_channel_daily_stats->PublisherWebsiteID 		= $publisher_website_id;
	    	$private_exchange_rtb_channel_daily_stats->BidFloor 				= $floor_price_if_any;
	    	$private_exchange_rtb_channel_daily_stats->RtbChannelSiteName 		= $rtb_channel_site_name;
	    	
	    	if ($PrivateExchangeRtbChannelDailyStats != null):
	    	
		    	$private_exchange_rtb_channel_daily_stats->PrivateExchangeRtbChannelDailyStatsID = $PrivateExchangeRtbChannelDailyStats->PrivateExchangeRtbChannelDailyStatsID;
		    	$private_exchange_rtb_channel_daily_stats->ImpressionsOfferedCounter = $PrivateExchangeRtbChannelDailyStats->ImpressionsOfferedCounter + $impressions_offered_counter;
		    	$private_exchange_rtb_channel_daily_stats->AuctionBidsCounter = $PrivateExchangeRtbChannelDailyStats->AuctionBidsCounter + $auction_bids_counter;
		    	$private_exchange_rtb_channel_daily_stats->BidTotalAmount = $PrivateExchangeRtbChannelDailyStats->BidTotalAmount + $spend_offered_in_bids;
		    	$PrivateExchangeRtbChannelDailyStatsFactory->updatePrivateExchangeRtbChannelDailyStats($private_exchange_rtb_channel_daily_stats);
	    	else:
	    	
		    	$private_exchange_rtb_channel_daily_stats->MDYH = $current_hour;
	    		$private_exchange_rtb_channel_daily_stats->MDY 	= $current_day;
		    	$private_exchange_rtb_channel_daily_stats->ImpressionsOfferedCounter = $impressions_offered_counter;
		    	$private_exchange_rtb_channel_daily_stats->AuctionBidsCounter = $auction_bids_counter;
		    	$private_exchange_rtb_channel_daily_stats->BidTotalAmount = $spend_offered_in_bids;
		    	$private_exchange_rtb_channel_daily_stats->DateCreated = date("Y-m-d H:i:s");
		    	$PrivateExchangeRtbChannelDailyStatsFactory->insertPrivateExchangeRtbChannelDailyStats($private_exchange_rtb_channel_daily_stats);
	    	endif;
    	
    	endforeach;
    	
    }
};
