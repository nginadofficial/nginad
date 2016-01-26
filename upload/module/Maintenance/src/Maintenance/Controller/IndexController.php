<?php

/**
 * NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2016 NginAd Foundation. All Rights Reserved
 * @license GPLv3
 */

namespace Maintenance\Controller;

/*
 * Special case for Mike's environment where
 * Composer is not autoloading PHPOffice for 
 * whatever reason.
 */

//if (!class_exists('\\PHPExcel')):
//	require('vendor/PHPOffice/PHPExcel/Classes/PHPExcel.php');
//endif;

use Zend\Mvc\Controller\AbstractActionController;
use \Exception;

class IndexController extends AbstractActionController {

	protected $config;
	
	protected static $tor_file_source_location = 'https://www.dan.me.uk/torlist/';
	
    public function indexAction() {
        echo "NGINAD MAINTENANCE<br />\n";
        exit;
    }

    /*
     * This method should be hooked up to a minutely cron job.
     * It will only run the maintenance for each interval 
     * at it's scheduled time no matter how many times this 
     * Controller is called from a cron tab.
     * 
     * Ex: 
     */

    public function crontabAction() {

        $config = $this->getServiceLocator()->get('Config');

        $this->config = $config;

        $secret_key = $this->getRequest()->getQuery('secret_key');

        if ($secret_key != $config['maintenance']['secret_key_crontab']):
            die("Permission Denied");
        endif;

        foreach ($config['maintenance']['tasks'] as $tagname => $maintenance_element):
            $interval_in_minutes = $maintenance_element['interval_in_minutes'];
            $should_run_maintenance = \util\Maintenance::checkRunMaintenance($tagname, $interval_in_minutes);

            if ($should_run_maintenance === true):
                $maintenance_function = $maintenance_element['maintenance_function'];
                $this->$maintenance_function();
            endif;
        endforeach;

        echo "NGINAD MAINTENANCE\n";
        exit;
    }

    public function dailyMaintenanceAction() {
        /* nothing here yet */
    }
    
    /*
     * Set a new crontab for the torlist to:
    * 5 * * * * curl 'http://server.nginad.com/maintenance/torlist?secret_key=nginadxyz'; /usr/sbin/nginx -s reload
    */
    public function torlistAction() {
    	 
    	$config = $this->getServiceLocator()->get('Config');
    	 
    	$this->config = $config;
    	 
    	$secret_key = $this->getRequest()->getQuery('secret_key');
    	 
    	if ($secret_key != $config['maintenance']['secret_key_crontab']):
    		die("Permission Denied");
    	endif;
    	 
    	$this->updateTorIpBlockList();
    	
    	echo "NGINAD MAINTENANCE\n";
    	exit;
    }
    
    public function tenMinuteMaintenanceAction() {

		$this->updateInsertionOrderLineItemTotals();
    	$this->updatePublisherZoneTotals();
    }
    
    private function updateTorIpBlockList() {
    	
    	if ($this->config['settings']['rtb']['tor_protected'] !== true):
    		return false;
    	endif;
    	
    	$lines = file(self::$tor_file_source_location);
    	
    	if (count($lines) < 100) {
    		// bad request
    		return false;
    	}
    	
    	$apc_cached_tor_ip_list = array();
    	
    	if (is_writable($this->config['settings']['rtb']['tor_file_save_location'])):
	    	try {
		    	$fh = fopen($this->config['settings']['rtb']['tor_file_save_location'], "w");
		    	/*
		    	 * write it in nginx conf.d file include format
		    	 */
		    	foreach ($lines as $line):
		    		fwrite($fh, "deny " . trim($line) . ";\n");
		    	endforeach;
		    	
		    	fclose($fh);
	    	} catch (Exception $e) {
	    		echo "Tor Save File Location is not writable, Exception: " . $e->getMessage();
	    	}
    	endif;
    	
    	foreach ($lines as $line):
    		$apc_cached_tor_ip_list[trim($line)] = 1;
    	endforeach;

    	$params = array();
    	// 2 hour cache
    	\util\CacheSql::put_cached_read_result_apc($this->config, $params, "Maintenance", $apc_cached_tor_ip_list, 7200);
    	
    	return true;
    	
    }
    
    private function updatePublisherZoneTotals() {
    	 
    	/*
    	 * update all compiled stats into the PublisherAdZone table
    	*/
    	
    	$PublisherImpressionsAndSpendHourlyTotals = \_factory\PublisherImpressionsAndSpendHourlyTotals::get_instance($this->config);
    	
    	$PublisherAdZoneFactory = \_factory\PublisherAdZone::get_instance();
    	$params = array();
    	$PublisherAdZoneList = $PublisherAdZoneFactory->get($params);
    	 
    	foreach ($PublisherAdZoneList as $PublisherAdZone):
    	
	    	$ad_zone_id = $PublisherAdZone->PublisherAdZoneID;
	    	
	    	$params = array();
	    	$params["PublisherAdZoneID"] = $ad_zone_id;
	    	$PublisherTotalsRollup = $PublisherImpressionsAndSpendHourlyTotals->get_row($params);
	    	if ($PublisherTotalsRollup == null):
	    		continue;
	    	endif;
	    	
	    	$PublisherAdZoneNew = new \model\PublisherAdZone();
	    	
	    	$PublisherAdZoneNew->PublisherAdZoneID = $PublisherAdZone->PublisherAdZoneID;
	    	$PublisherAdZoneNew->PublisherWebsiteID = $PublisherAdZone->PublisherWebsiteID;
	    	$PublisherAdZoneNew->ImpressionType = $PublisherAdZone->ImpressionType;
	    	$PublisherAdZoneNew->AuctionType = $PublisherAdZone->AuctionType;
	    	$PublisherAdZoneNew->HeaderBiddingAdUnitID = $PublisherAdZone->HeaderBiddingAdUnitID;
	    	$PublisherAdZoneNew->AdOwnerID = $PublisherAdZone->AdOwnerID;
	    	$PublisherAdZoneNew->AdName = $PublisherAdZone->AdName;
	    	$PublisherAdZoneNew->Description = $PublisherAdZone->Description;
	    	$PublisherAdZoneNew->PassbackAdTag = $PublisherAdZone->PassbackAdTag;
	    	$PublisherAdZoneNew->AdStatus = $PublisherAdZone->AdStatus;
	    	$PublisherAdZoneNew->AutoApprove = $PublisherAdZone->AutoApprove;
	    	$PublisherAdZoneNew->AdTemplateID = $PublisherAdZone->AdTemplateID;
	    	$PublisherAdZoneNew->IsMobileFlag = $PublisherAdZone->IsMobileFlag;
	    	$PublisherAdZoneNew->Width = $PublisherAdZone->Width;
	    	$PublisherAdZoneNew->Height = $PublisherAdZone->Height;
	    	$PublisherAdZoneNew->FloorPrice = $PublisherAdZone->FloorPrice;
	    	$PublisherAdZoneNew->TotalRequests = $PublisherTotalsRollup->TotalRequests;
	    	$PublisherAdZoneNew->TotalImpressionsFilled = $PublisherTotalsRollup->TotalImpressions;
	    	$PublisherAdZoneNew->TotalAmount = $PublisherTotalsRollup->TotalRevenue;
	    	$PublisherAdZoneNew->DateCreated = $PublisherAdZone->DateCreated;
	    	$PublisherAdZoneNew->DateUpdated = $PublisherAdZone->DateUpdated;

	    	$PublisherAdZoneFactory->save_ads($PublisherAdZoneNew);
	    	
    	endforeach;
    	
    }
    
    private function updateInsertionOrderLineItemTotals() {
    	
    	/*
    	 * update all compiled stats into the InsertionOrderLineItem table
    	*/
    	
    	$BidTotalsRollupFactory = \_factory\BidTotalsRollup::get_instance();
    	$ImpressionAndSpendTotalsRollupFactory = \_factory\ImpressionAndSpendTotalsRollup::get_instance();
    	
    	$InsertionOrderLineItemFactory = \_factory\InsertionOrderLineItem::get_instance();
    	$params = array();
    	$params["Active"] = 1;
    	$InsertionOrderLineItemList = $InsertionOrderLineItemFactory->get($params);
    	
    	foreach ($InsertionOrderLineItemList as $InsertionOrderLineItem):
	    	
	    	$banner_id = $InsertionOrderLineItem->InsertionOrderLineItemID;
	    	
	    	$params = array();
	    	$params["InsertionOrderLineItemID"] = $banner_id;
	    	$BidTotalsRollup = $BidTotalsRollupFactory->get_row($params);
	    	if ($BidTotalsRollup == null):
	    		continue;
	    	endif;
	    	$ImpressionAndSpendTotalsRollup = $ImpressionAndSpendTotalsRollupFactory->get_row($params);
	    	if ($ImpressionAndSpendTotalsRollup == null):
	    		continue;
	    	endif;
	    	
	    	$InsertionOrderLineItem->BidsCounter = $BidTotalsRollup->TotalBids;
	    	$InsertionOrderLineItem->ImpressionsCounter = $ImpressionAndSpendTotalsRollup->TotalImpressions;
	    	$InsertionOrderLineItem->CurrentSpend = $ImpressionAndSpendTotalsRollup->TotalSpendGross;
	    	
	    	$data = $InsertionOrderLineItem->getArrayCopy();
	    	
	    	$InsertionOrderLineItemFactory->saveInsertionOrderLineItemFromDataArray($data);
	    	
    	endforeach;
    	
    	/*
    	 * Update all InsertionOrder tables with the new info from the InsertionOrderLineItem tables
    	*/
    	
    	$InsertionOrderFactory = \_factory\InsertionOrder::get_instance();
    	$params = array();
    	$params["Active"] = 1;
    	$InsertionOrderList = $InsertionOrderFactory->get($params);
    	
    	foreach ($InsertionOrderList as $InsertionOrder):
    	
	    	$ad_campaign_id = $InsertionOrder->InsertionOrderID;
	    	
	    	$InsertionOrderLineItemFactory = \_factory\InsertionOrderLineItem::get_instance();
	    	$params = array();
	    	$params["InsertionOrderID"] = $ad_campaign_id;
	    	$InsertionOrderLineItemList = $InsertionOrderLineItemFactory->get($params);
	    	
	    	$impressions_counter 	= 0;
	    	$current_spend			= 0;
	    	 
	    	foreach ($InsertionOrderLineItemList as $InsertionOrderLineItem):
	    	 
		    	$impressions_counter 	+= $InsertionOrderLineItem->ImpressionsCounter;
		    	$current_spend 			+= floatval($InsertionOrderLineItem->CurrentSpend);
		    	 
	    	endforeach;
	    	
	    	$InsertionOrder->ImpressionsCounter 	= $impressions_counter;
	    	$InsertionOrder->CurrentSpend 			= $current_spend;
	    	 
	    	$data = $InsertionOrder->getArrayCopy();
	    	 
	    	$InsertionOrderFactory->saveInsertionOrderFromDataArray($data);
	    	 
    	endforeach;
    }
    
}
