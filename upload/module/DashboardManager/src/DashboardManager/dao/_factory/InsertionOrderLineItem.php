<?php
/**
 * NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2016 NginAd Foundation. All Rights Reserved
 * @license GPLv3
 */

namespace _factory;

use Zend\Db\TableGateway\Feature;

class InsertionOrderLineItem extends \_factory\CachedTableRead
{

	static protected $instance = null;

	public static function get_instance() {

		if (self::$instance == null):
			self::$instance = new \_factory\InsertionOrderLineItem();
		endif;
		return self::$instance;
	}


    function __construct() {

            $this->table = 'InsertionOrderLineItem';
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
        	$select->order('InsertionOrderLineItemID');

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
        		$select->order('InsertionOrderLineItemID');

        	}
    	);

    	    foreach ($resultSet as $obj):
    	        $obj_list[] = $obj;
    	    endforeach;

    		return $obj_list;
    }

    public function saveInsertionOrderLineItem(\model\InsertionOrderLineItem $Banner) {
    	$data = array(
    			'InsertionOrderID'         => $Banner->InsertionOrderID,
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
    	$banner_id = (int)$Banner->InsertionOrderLineItemID;
    	if ($banner_id === 0): 
    		$data['UserID'] 			= $Banner->UserID;
    		$data['ImpressionsCounter'] = $Banner->ImpressionsCounter;
    		$data['BidsCounter']        = $Banner->BidsCounter;
    		$data['CurrentSpend']       = $Banner->CurrentSpend;
    		$data['DateCreated']        = $Banner->DateCreated;
    		$this->insert($data);
    		return $this->getLastInsertValue();
    	else: 
    		$this->update($data, array('InsertionOrderLineItemID' => $banner_id));
    		return $banner_id;
    	endif;
    }
    
    public function incrementInsertionOrderLineItemImpressionsCounterAndSpendCached($config, $buyer_id, $banner_id, $impression_cost_gross, $impression_cost_net) {
    	 
    	$params = array();
    	$params["BuySidePartnerID"] 	= $buyer_id;
    	$params["InsertionOrderLineItemID"] 	= $banner_id;
    	
    	$class_dir_name = 'BuySideHourlyImpressionsCounterCurrentSpend';
    	
    	$cached_key_exists = \util\CacheSql::does_cached_write_exist_apc($config, $params, $class_dir_name);

    	if ($cached_key_exists):
    	
	    	// increment bucket
	    	\util\CachedStatsWrites::increment_cached_write_result_impressions_spend_apc($config, $params, $class_dir_name, 1, $impression_cost_gross, $impression_cost_net);
    	
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
	    	\util\CachedStatsWrites::increment_cached_write_result_impressions_spend_apc($config, $params, $class_dir_name, 1, $impression_cost_gross, $impression_cost_net);
	    	
	    	// SYNCHRONIZED BLOCK END
	    	\util\CacheSql::reset_write_unlock($config, $params, $class_dir_name);
	    	
    		if ($current != null):
    		
	    		$bucket_impressions = $current["impressions"];
		    	$bucket_spend_gross = $current["spend_gross"];
		    	$bucket_spend_net = $current["spend_net"];

		    	// write out values
		    	$this->incrementInsertionOrderLineItemImpressionsCounterAndSpend($buyer_id, $banner_id, $bucket_impressions, $bucket_spend_gross, $bucket_spend_net);

		    endif;
	    	
    	endif;
    	
    }

    public function incrementInsertionOrderLineItemImpressionsCounterAndSpend($buyer_id, $banner_id, $impressions_value_to_increment, $spend_value_to_increment_gross, $spend_value_to_increment_net) {

    	$BuySideHourlyImpressionsCounterCurrentSpendFactory = \_factory\BuySideHourlyImpressionsCounterCurrentSpend::get_instance();

    	$current_hour = date("m/d/Y H");

    	$params = array();
    	$params["BuySidePartnerID"] 	= $buyer_id;
    	$params["InsertionOrderLineItemID"] 	= $banner_id;
    	$params["MDYH"] 				= $current_hour;
    	$BuySideHourlyImpressionsCounterCurrentSpend = $BuySideHourlyImpressionsCounterCurrentSpendFactory->get_row($params);
    
    	$buyside_hourly_impressions_counter_current_spend = new \model\BuySideHourlyImpressionsCounterCurrentSpend();
    	$buyside_hourly_impressions_counter_current_spend->InsertionOrderLineItemID 	= $banner_id;
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

    public function incrementInsertionOrderLineItemBidsCounterCached($config, $buyer_id, $banner_id) {
    
    	$params = array();
    	$params["BuySidePartnerID"] 	= $buyer_id;
    	$params["InsertionOrderLineItemID"] 	= $banner_id;
    	
  		$class_dir_name = 'BuySideHourlyBidsCounter';  	

  		$cached_key_exists = \util\CacheSql::does_cached_write_exist_apc($config, $params, $class_dir_name);
  		
  		if ($cached_key_exists):
  		 
	  		// increment bucket
	  		\util\CachedStatsWrites::increment_cached_write_result_int_apc($config, $params, $class_dir_name, 1);
	  		 
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
	  		\util\CachedStatsWrites::increment_cached_write_result_int_apc($config, $params, $class_dir_name, 1);
	  		
	    	// SYNCHRONIZED BLOCK END
	    	\util\CacheSql::reset_write_unlock($config, $params, $class_dir_name);
	  		
	  		if ($current != null):
		  		$bucket_value = $current["value"];
		  		
		  		// write out value
		  		$this->incrementInsertionOrderLineItemBidsCounter($buyer_id, $banner_id, $bucket_value);
	  		endif;
	  		 
  		endif;
  
    }
    
    public function incrementInsertionOrderLineItemBidsCounter($buyer_id, $banner_id, $bid_count) {

    	$BuySideHourlyBidsCounterFactory = \_factory\BuySideHourlyBidsCounter::get_instance();

    	$current_hour = date("m/d/Y H");
    	
    	$params = array();
    	$params["BuySidePartnerID"] 	= $buyer_id;
    	$params["InsertionOrderLineItemID"] 	= $banner_id;
    	$params["MDYH"] 				= $current_hour;
    	$BuySideHourlyBidsCounter = $BuySideHourlyBidsCounterFactory->get_row($params);

    	$buyside_hourly_bids_counter = new \model\BuySideHourlyBidsCounter();
    	$buyside_hourly_bids_counter->BuySidePartnerID 		= $buyer_id;
    	$buyside_hourly_bids_counter->InsertionOrderLineItemID 	= $banner_id;

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
    	$params["InsertionOrderLineItemID"] = $banner_id;
    	$params["PublisherTLD"] = $tld;
    
    	$class_dir_name = 'BuySideHourlyImpressionsByTLD';
    
    	$cached_key_exists = \util\CacheSql::does_cached_write_exist_apc($config, $params, $class_dir_name);
    
    	if ($cached_key_exists):
	    
	    	// increment bucket
	    	\util\CachedStatsWrites::increment_cached_write_result_int_apc($config, $params, $class_dir_name, 1);
	    	 
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
    		\util\CachedStatsWrites::increment_cached_write_result_int_apc($config, $params, $class_dir_name, 1);
    			
	    	// SYNCHRONIZED BLOCK END
	    	\util\CacheSql::reset_write_unlock($config, $params, $class_dir_name);
    		
    		if ($current != null):
	    		$bucket_value = $current["value"];
	    		
	    		// write out value
	    		$this->incrementBuySideHourlyImpressionsByTLD($banner_id, $tld, $bucket_value);
    		endif;

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
    	$params["InsertionOrderLineItemID"] = $banner_id;
    	$params["MDYH"] = $current_hour;
    	$params["PublisherTLD"] = $tld;
    	$BuySideHourlyImpressionsByTLD = $BuySideHourlyImpressionsByTLDFactory->get_row($params);
    
    	$buyside_hourly_impression_by_tld = new \model\BuySideHourlyImpressionsByTLD();
    	$buyside_hourly_impression_by_tld->InsertionOrderLineItemID = $banner_id;
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

    public function deleteInsertionOrderLineItem($banner_id) {
    	$this->delete(array('InsertionOrderLineItemID' => $banner_id));
    }


    public function saveInsertionOrderLineItemFromDataArray($data) {

    	$this->update($data, array('InsertionOrderLineItemID' => $data['InsertionOrderLineItemID']));
    }
    
    public function updateInsertionOrderLineItemBidAmount($banner_id, $bid_amount) {
    
    	$params = array();
    	$params["InsertionOrderLineItemID"] = $banner_id;
    	$InsertionOrderLineItem = $this->get_row($params);
    
    	if ($InsertionOrderLineItem != null):
    	
	    	$InsertionOrderLineItem->BidAmount = $bid_amount;
	    	// get array of data
	    	$data = $InsertionOrderLineItem->getArrayCopy();
	    
	    	$this->update($data, array('InsertionOrderLineItemID' => $banner_id));
    	endif;
    
    }
    
    public function deActivateInsertionOrderLineItem($banner_id) {

        $params = array();
        $params["InsertionOrderLineItemID"] = $banner_id;
        $InsertionOrderLineItem = $this->get_row($params);
        if ($InsertionOrderLineItem != null):
        
	        $InsertionOrderLineItem->Active = 0;
	        // get array of data
	        $data = $InsertionOrderLineItem->getArrayCopy();
	
	        $this->update($data, array('InsertionOrderLineItemID' => $banner_id));
	
        endif;
    }

};
