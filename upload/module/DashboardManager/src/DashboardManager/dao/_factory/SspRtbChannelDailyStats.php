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
    			'BuySidePartnerName'      			=> $SspRtbChannelDailyStats->BuySidePartnerName,
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

    	$private_exchange_rtb_channel_daily_stats_id = (int)$SspRtbChannelDailyStats->SspRtbChannelDailyStatsID;
    	$this->update($data, array('SspRtbChannelDailyStatsID' => $private_exchange_rtb_channel_daily_stats_id));
    }
    
    public function incrementSspRtbChannelDailyStatsCached($config, $buyside_partner_name, $rtb_channel_site_id, $impressions_offered_counter, $auction_bids_counter) {
    	
    	$params = array();
    	$params["BuySidePartnerName"] 			= $buyside_partner_name;
    	$params["SspRtbChannelSiteID"] 			= $rtb_channel_site_id;
    	
    	$class_dir_name = 'SspRtbChannelDailyStats';
    	
    	$cached_key_exists = \util\CacheSql::does_cached_write_exist_apc($config, $params, $class_dir_name);

    	if ($cached_key_exists):
    	
	    	// increment bucket
	    	\util\CachedStatsWrites::increment_cached_write_result_ssp_rtb_channel_stats($config, $params, $class_dir_name, $impressions_offered_counter, $auction_bids_counter);
    	
    	else:
    	
	    	// get value sum from apc
	    	$current = \util\CacheSql::get_cached_read_result_apc($config, $params, $class_dir_name);

    		if ($current != null):
    		
	    		$impressions_offered_counter 	= $current["impressions_offered_counter"];
		    	$auction_bids_counter 			= $current["auction_bids_counter"];

		    	// write out values
		    	$this->incrementSspRtbChannelDailyStats($config, $buyside_partner_name, $rtb_channel_site_id, $impressions_offered_counter, $auction_bids_counter);

		    endif;
		    
	    	// delete existing key - reset bucket
	    	\util\CacheSql::delete_cached_write_apc($config, $params, $class_dir_name);
	    	 
	    	// increment bucket
	    	\util\CachedStatsWrites::increment_cached_write_result_ssp_rtb_channel_stats($config, $params, $class_dir_name, $impressions_offered_counter, $auction_bids_counter);
	    	
    	endif;
    	
    }

    public function incrementSspRtbChannelDailyStats($config, $buyside_partner_name, $rtb_channel_site_id, $impressions_offered_counter, $auction_bids_counter) {
    	
    	$SspRtbChannelDailyStatsFactory = \_factory\SspRtbChannelDailyStats::get_instance();
    	
    	$current_hour = date("m/d/Y H");
    	
    	$params = array();
    	$params["BuySidePartnerName"] 	= $buyside_partner_name;
    	$params["SspRtbChannelSiteID"] 	= $rtb_channel_site_id;
    	$params["MDYH"] 				= $current_hour;
    	$SspRtbChannelDailyStats 		= $SspRtbChannelDailyStatsFactory->get_row($params);
    	
    	$ssp_rtb_channel_daily_stats = new \model\SspRtbChannelDailyStats();
    	$ssp_rtb_channel_daily_stats->BuySidePartnerName 		= $buyside_partner_name;
    	$ssp_rtb_channel_daily_stats->SspRtbChannelSiteID 		= $rtb_channel_site_id;
    	
    	if ($SspRtbChannelDailyStats != null):
    	
	    	$ssp_rtb_channel_daily_stats->SspRtbChannelDailyStatsID = $SspRtbChannelDailyStats->SspRtbChannelDailyStatsID;
	    	$ssp_rtb_channel_daily_stats->ImpressionsOfferedCounter = $SspRtbChannelDailyStats->ImpressionsOfferedCounter + $impressions_offered_counter;
	    	$ssp_rtb_channel_daily_stats->AuctionBidsCounter = $SspRtbChannelDailyStats->AuctionBidsCounter + $auction_bids_counter;
	    	$SspRtbChannelDailyStatsFactory->updateSspRtbChannelDailyStats($ssp_rtb_channel_daily_stats);
    	else:
    	
	    	$ssp_rtb_channel_daily_stats->MDYH = $current_hour;
	    	$ssp_rtb_channel_daily_stats->ImpressionsOfferedCounter = $impressions_offered_counter;
	    	$ssp_rtb_channel_daily_stats->AuctionBidsCounter = $auction_bids_counter;
	    	$ssp_rtb_channel_daily_stats->DateCreated = date("Y-m-d H:i:s");
	    	$SspRtbChannelDailyStatsFactory->insertSspRtbChannelDailyStats($ssp_rtb_channel_daily_stats);
    	endif;
    	
    }

};
