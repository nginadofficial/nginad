<?php
/**
 * CDNPAL NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2015 CDNPAL Ltd. All Rights Reserved
 * @license GPLv3
 */

namespace _factory;

use Zend\Db\TableGateway\Feature;

class AdCampaignBanner extends \_factory\CachedTableRead
{

	static protected $instance = null;

	public static function get_instance() {

		if (self::$instance == null):
			self::$instance = new \_factory\AdCampaignBanner();
		endif;
		return self::$instance;
	}


    function __construct() {

            $this->table = 'AdCampaignBanner';
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

    public function saveAdCampaignBanner(\model\AdCampaignBanner $Banner) {
    	$data = array(
    			'AdCampaignID'         => $Banner->AdCampaignID,
    			'AdCampaignTypeID'	   => $Banner->AdCampaignTypeID,
    			'Name'                 => $Banner->Name,
    			'ImpressionType'       => $Banner->ImpressionType,
    			// convert to MySQL DateTime
    			'StartDate'            => $Banner->StartDate,
    			'EndDate'              => $Banner->EndDate,
    			'IsMobile'             => $Banner->IsMobile,
    			'IABSize'              => $Banner->IABSize,
    			'Height'               => $Banner->Height,
    			'Width'                => $Banner->Width,
    			'Weight'               => $Banner->Weight,
    			'BidAmount'            => $Banner->BidAmount,
    			'AdTag'                => $Banner->AdTag,
    			'DeliveryType'         => $Banner->DeliveryType,
    	        'LandingPageTLD'       => $Banner->LandingPageTLD,
    	        'Active'               => $Banner->Active
    	);
    	$banner_id = (int)$Banner->AdCampaignBannerID;
    	if ($banner_id === 0): 
    		$data['UserID'] 			= $Banner->UserID;
    		$data['ImpressionsCounter'] = $Banner->ImpressionsCounter;
    		$data['BidsCounter']        = $Banner->BidsCounter;
    		$data['CurrentSpend']       = $Banner->CurrentSpend;
    		$data['DateCreated']        = $Banner->DateCreated;
    		$this->insert($data);
    		return $this->getLastInsertValue();
    	else: 
    		$this->update($data, array('AdCampaignBannerID' => $banner_id));
    		return $banner_id;
    	endif;
    }
    
    public function incrementAdCampaignBannerImpressionsCounterAndSpendCached($config, $buyer_id, $banner_id, $impression_cost_gross, $impression_cost_net) {
    	 
    	$params = array();
    	$params["BuySidePartnerID"] 	= $buyer_id;
    	$params["AdCampaignBannerID"] 	= $banner_id;
    	
    	$class_dir_name = 'BuySideHourlyImpressionsCounterCurrentSpend';
    	
    	$cached_key_exists = \util\CacheSql::does_cached_write_exist_apc($config, $params, $class_dir_name);

    	if ($cached_key_exists):
    	
	    	// increment bucket
	    	\util\CachedStatsWrites::increment_cached_write_result_impressions_spend_apc($config, $params, $class_dir_name, 1, $impression_cost_gross, $impression_cost_net);
    	
    	else:
    	
	    	// get value sum from apc
	    	$current = \util\CacheSql::get_cached_read_result_apc($config, $params, $class_dir_name);

    		if ($current != null):
    		
	    		$bucket_impressions = $current["impressions"];
		    	$bucket_spend_gross = $current["spend_gross"];
		    	$bucket_spend_net = $current["spend_net"];

		    	// write out values
		    	$this->incrementAdCampaignBannerImpressionsCounterAndSpend($buyer_id, $banner_id, $bucket_impressions, $bucket_spend_gross, $bucket_spend_net);

		    endif;
		    
	    	// delete existing key - reset bucket
	    	\util\CacheSql::delete_cached_write_apc($config, $params, $class_dir_name);
	    	 
	    	// increment bucket
	    	\util\CachedStatsWrites::increment_cached_write_result_impressions_spend_apc($config, $params, $class_dir_name, 1, $impression_cost_gross, $impression_cost_net);
	    	
    	endif;
    	
    }

    public function incrementAdCampaignBannerImpressionsCounterAndSpend($buyer_id, $banner_id, $impressions_value_to_increment, $spend_value_to_increment_gross, $spend_value_to_increment_net) {

    	$BuySideHourlyImpressionsCounterCurrentSpendFactory = \_factory\BuySideHourlyImpressionsCounterCurrentSpend::get_instance();

    	$current_hour = date("m/d/Y H");

    	$params = array();
    	$params["BuySidePartnerID"] 	= $buyer_id;
    	$params["AdCampaignBannerID"] 	= $banner_id;
    	$params["MDYH"] 				= $current_hour;
    	$BuySideHourlyImpressionsCounterCurrentSpend = $BuySideHourlyImpressionsCounterCurrentSpendFactory->get_row($params);
    
    	$buyside_hourly_impressions_counter_current_spend = new \model\BuySideHourlyImpressionsCounterCurrentSpend();
    	$buyside_hourly_impressions_counter_current_spend->AdCampaignBannerID 	= $banner_id;
    	$buyside_hourly_impressions_counter_current_spend->BuySidePartnerID 	= $buyer_id;
    	if ($BuySideHourlyImpressionsCounterCurrentSpend != null):
	    	/*
	    	 * Increment Current Impressions Counter plus Current Spend
	    	 * Remember that the bids are counted by CPM,
	    	 * so it takes 1000 impressions to make a CPM bid amount
	    	 */
    		$buyside_hourly_impressions_counter_current_spend->BuySideHourlyImpressionsCounterCurrentSpendID = $BuySideHourlyImpressionsCounterCurrentSpend->BuySideHourlyImpressionsCounterCurrentSpendID;
	    	$buyside_hourly_impressions_counter_current_spend->CurrentSpendGross = floatval($BuySideHourlyImpressionsCounterCurrentSpend->CurrentSpendGross) + $spend_value_to_increment_gross;
    		$buyside_hourly_impressions_counter_current_spend->CurrentSpendNet = floatval($BuySideHourlyImpressionsCounterCurrentSpend->CurrentSpendNet) + $spend_value_to_increment_net;
    		$buyside_hourly_impressions_counter_current_spend->ImpressionsCounter = $BuySideHourlyImpressionsCounterCurrentSpend->ImpressionsCounter + $impressions_value_to_increment;
    		$BuySideHourlyImpressionsCounterCurrentSpendFactory->updateBuySideHourlyImpressionsCounterCurrentSpend($buyside_hourly_impressions_counter_current_spend);
    	else:

    		$buyside_hourly_impressions_counter_current_spend->MDYH = $current_hour;
	    	$buyside_hourly_impressions_counter_current_spend->CurrentSpendGross = $spend_value_to_increment_gross;
    		$buyside_hourly_impressions_counter_current_spend->CurrentSpendNet = $spend_value_to_increment_net;
    		$buyside_hourly_impressions_counter_current_spend->ImpressionsCounter = $impressions_value_to_increment;
    		$buyside_hourly_impressions_counter_current_spend->DateCreated = date("Y-m-d H:i:s");
    		$BuySideHourlyImpressionsCounterCurrentSpendFactory->insertBuySideHourlyImpressionsCounterCurrentSpend($buyside_hourly_impressions_counter_current_spend);	
    	endif;
    }

    public function incrementAdCampaignBannerBidsCounterCached($config, $buyer_id, $banner_id) {
    
    	$params = array();
    	$params["BuySidePartnerID"] 	= $buyer_id;
    	$params["AdCampaignBannerID"] 	= $banner_id;
    	
  		$class_dir_name = 'BuySideHourlyBidsCounter';  	

  		$cached_key_exists = \util\CacheSql::does_cached_write_exist_apc($config, $params, $class_dir_name);
  		
  		if ($cached_key_exists):
  		 
	  		// increment bucket
	  		\util\CachedStatsWrites::increment_cached_write_result_int_apc($config, $params, $class_dir_name, 1);
	  		 
  		else:
	  		 
	  		// get value sum from apc
	  		$current = \util\CacheSql::get_cached_read_result_apc($config, $params, $class_dir_name);
	  		
	  		if ($current != null):
		  		$bucket_value = $current["value"];
		  		
		  		// write out value
		  		$this->incrementAdCampaignBannerBidsCounter($buyer_id, $banner_id, $bucket_value);
	  		endif;
	  		
	  		// delete existing key - reset bucket
	  		\util\CacheSql::delete_cached_write_apc($config, $params, $class_dir_name);
	  		
	  		// increment bucket
	  		\util\CachedStatsWrites::increment_cached_write_result_int_apc($config, $params, $class_dir_name, 1);
	  		 
  		endif;
  
    }
    
    public function incrementAdCampaignBannerBidsCounter($buyer_id, $banner_id, $bid_count) {

    	$BuySideHourlyBidsCounterFactory = \_factory\BuySideHourlyBidsCounter::get_instance();

    	$current_hour = date("m/d/Y H");
    	
    	$params = array();
    	$params["BuySidePartnerID"] 	= $buyer_id;
    	$params["AdCampaignBannerID"] 	= $banner_id;
    	$params["MDYH"] 				= $current_hour;
    	$BuySideHourlyBidsCounter = $BuySideHourlyBidsCounterFactory->get_row($params);

    	$buyside_hourly_bids_counter = new \model\BuySideHourlyBidsCounter();
    	$buyside_hourly_bids_counter->BuySidePartnerID 		= $buyer_id;
    	$buyside_hourly_bids_counter->AdCampaignBannerID 	= $banner_id;

    	if ($BuySideHourlyBidsCounter != null):

    		$buyside_hourly_bids_counter->BuySideHourlyBidsCounterID = $BuySideHourlyBidsCounter->BuySideHourlyBidsCounterID;
    		$buyside_hourly_bids_counter->BidsCounter = $BuySideHourlyBidsCounter->BidsCounter + $bid_count;
    		$BuySideHourlyBidsCounterFactory->updateBuySideHourlyBidsCounter($buyside_hourly_bids_counter);
    	else:
    	
    		$buyside_hourly_bids_counter->MDYH = $current_hour;
    		$buyside_hourly_bids_counter->BidsCounter = $bid_count;
    		$buyside_hourly_bids_counter->DateCreated = date("Y-m-d H:i:s");
    		$BuySideHourlyBidsCounterFactory->insertBuySideHourlyBidsCounter($buyside_hourly_bids_counter);
    	endif;

    }

    public function incrementBuySideHourlyImpressionsByTLDCached($config, $banner_id, $tld) {
    
    	$params = array();
    	$params["AdCampaignBannerID"] = $banner_id;
    	$params["PublisherTLD"] = $tld;
    
    	$class_dir_name = 'BuySideHourlyImpressionsByTLD';
    
    	$cached_key_exists = \util\CacheSql::does_cached_write_exist_apc($config, $params, $class_dir_name);
    
    	if ($cached_key_exists):
	    
	    	// increment bucket
	    	\util\CachedStatsWrites::increment_cached_write_result_int_apc($config, $params, $class_dir_name, 1);
	    	 
    	else:
	    
    		// get value sum from apc
    		$current = \util\CacheSql::get_cached_read_result_apc($config, $params, $class_dir_name);
    		
    		if ($current != null):
	    		$bucket_value = $current["value"];
	    		
	    		// write out value
	    		$this->incrementBuySideHourlyImpressionsByTLD($banner_id, $tld, $bucket_value);
    		endif;
    		
    		// delete existing key - reset bucket
    		\util\CacheSql::delete_cached_write_apc($config, $params, $class_dir_name);
    		
    		// increment bucket
    		\util\CachedStatsWrites::increment_cached_write_result_int_apc($config, $params, $class_dir_name, 1);
	    
    	endif;
    	
    }
    
    public function incrementBuySideHourlyImpressionsByTLD($banner_id, $tld, $bid_count) {
    
    	/*
    	 * Validate Domain before counting an impression by TLD
    	*/
    	$is_valid_domain = preg_match("/^(?:[-A-Za-z0-9]+\.)+[A-Za-z]{2,6}$/i", $tld);
    
    	if (!$is_valid_domain):
    		return;
    	endif;
    
    	$BuySideHourlyImpressionsByTLDFactory = \_factory\BuySideHourlyImpressionsByTLD::get_instance();
    
        $current_hour = date("m/d/Y H");
    
    	$params = array();
    	$params["AdCampaignBannerID"] = $banner_id;
    	$params["MDYH"] = $current_hour;
    	$params["PublisherTLD"] = $tld;
    	$BuySideHourlyImpressionsByTLD = $BuySideHourlyImpressionsByTLDFactory->get_row($params);
    
    	$buyside_hourly_impression_by_tld = new \model\BuySideHourlyImpressionsByTLD();
    	$buyside_hourly_impression_by_tld->AdCampaignBannerID = $banner_id;
    	$buyside_hourly_impression_by_tld->MDYH = $current_hour;
    	$buyside_hourly_impression_by_tld->PublisherTLD = $tld;
    
    	if ($BuySideHourlyImpressionsByTLD != null):
    
	    	$buyside_hourly_impression_by_tld->BuySideHourlyImpressionsByTLDID = $BuySideHourlyImpressionsByTLD->BuySideHourlyImpressionsByTLDID;
	    	$buyside_hourly_impression_by_tld->Impressions = $BuySideHourlyImpressionsByTLD->Impressions + $bid_count;
	    	$BuySideHourlyImpressionsByTLDFactory->updateBuySideHourlyImpressionsByTLD($buyside_hourly_impression_by_tld);
    	else:

	    	$buyside_hourly_impression_by_tld->Impressions = $bid_count;
	    	$buyside_hourly_impression_by_tld->DateCreated = date("Y-m-d H:i:s");
	    	$BuySideHourlyImpressionsByTLDFactory->insertBuySideHourlyImpressionsByTLD($buyside_hourly_impression_by_tld);
    	endif;
    
    }

    public function deleteAdCampaignBanner($banner_id) {
    	$this->delete(array('AdCampaignBannerID' => $banner_id));
    }


    public function saveAdCampaignBannerFromDataArray($data) {

    	$this->update($data, array('AdCampaignBannerID' => $data['AdCampaignBannerID']));
    }

    public function updateAdCampaignBannerAdCampaignType($banner_id, $type_id) {
    
    	$params = array();
    	$params["AdCampaignBannerID"] = $banner_id;
    	$AdCampaignBanner = $this->get_row($params);
    
    	if ($AdCampaignBanner != null):
	    	 
    		$AdCampaignBanner->AdCampaignTypeID = $type_id;
	    	// get array of data
	    	$data = $AdCampaignBanner->getArrayCopy();
	    	 
	    	$this->update($data, array('AdCampaignBannerID' => $banner_id));
    	endif;
    
    }
    
    public function updateAdCampaignBannerBidAmount($banner_id, $bid_amount) {
    
    	$params = array();
    	$params["AdCampaignBannerID"] = $banner_id;
    	$AdCampaignBanner = $this->get_row($params);
    
    	if ($AdCampaignBanner != null):
    	
	    	$AdCampaignBanner->BidAmount = $bid_amount;
	    	// get array of data
	    	$data = $AdCampaignBanner->getArrayCopy();
	    
	    	$this->update($data, array('AdCampaignBannerID' => $banner_id));
    	endif;
    
    }
    
    public function deActivateAdCampaignBanner($banner_id) {

        $params = array();
        $params["AdCampaignBannerID"] = $banner_id;
        $AdCampaignBanner = $this->get_row($params);
        if ($AdCampaignBanner != null):
        
	        $AdCampaignBanner->Active = 0;
	        // get array of data
	        $data = $AdCampaignBanner->getArrayCopy();
	
	        $this->update($data, array('AdCampaignBannerID' => $banner_id));
	
        endif;
    }

};
