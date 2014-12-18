<?php

/**
 * CDNPAL NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2015 CDNPAL Ltd. All Rights Reserved
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
use Zend\Mail\Message;
use Zend\Mime;
use PHPExcel_IOFactory;
use util\Maintenance;

class IndexController extends AbstractActionController {

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

        echo "NGINAD MAINTENANCE<br />\n";
        exit;
    }

    public function excelAction() {
        define('EOL', (PHP_SAPI == 'cli') ? PHP_EOL : '<br />');
        // Create new PHPExcel object
        echo date('H:i:s'), " Create new PHPExcel object", EOL;
        $objPHPExcel = new \PHPExcel();

        var_dump($objPHPExcel);
        echo "EXCEL MAINTENANCE ACTION<br />\n";
        exit;
    }

    public function dailyMaintenanceAction() {
        /* nothing here yet */
    }

    public function tenMinuteMaintenanceAction() {

		$this->updateAdCampaignBannerTotals();
    	$this->updatePublisherZoneTotals();
    }
    
    private function updatePublisherZoneTotals() {
    	 
    	/*
    	 * update all compiled stats into the PublisherAdZone table
    	*/
    	
    	$PublisherImpressionsAndSpendHourlyTotals = \_factory\PublisherImpressionsAndSpendHourlyTotals::get_instance();
    	
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
	    	$PublisherAdZoneNew->PublisherAdZoneTypeID = $PublisherAdZone->PublisherAdZoneTypeID;
	    	$PublisherAdZoneNew->ImpressionType = $PublisherAdZone->ImpressionType;
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
    
    private function updateAdCampaignBannerTotals() {
    	
    	/*
    	 * update all compiled stats into the AdCampaignBanner table
    	*/
    	
    	$BidTotalsRollupFactory = \_factory\BidTotalsRollup::get_instance();
    	$ImpressionAndSpendTotalsRollupFactory = \_factory\ImpressionAndSpendTotalsRollup::get_instance();
    	
    	$AdCampaignBannerFactory = \_factory\AdCampaignBanner::get_instance();
    	$params = array();
    	$params["Active"] = 1;
    	$AdCampaignBannerList = $AdCampaignBannerFactory->get($params);
    	
    	foreach ($AdCampaignBannerList as $AdCampaignBanner):
	    	
	    	$banner_id = $AdCampaignBanner->AdCampaignBannerID;
	    	
	    	$params = array();
	    	$params["AdCampaignBannerID"] = $banner_id;
	    	$BidTotalsRollup = $BidTotalsRollupFactory->get_row($params);
	    	if ($BidTotalsRollup == null):
	    		continue;
	    	endif;
	    	$ImpressionAndSpendTotalsRollup = $ImpressionAndSpendTotalsRollupFactory->get_row($params);
	    	if ($ImpressionAndSpendTotalsRollup == null):
	    		continue;
	    	endif;
	    	
	    	$AdCampaignBanner->BidsCounter = $BidTotalsRollup->TotalBids;
	    	$AdCampaignBanner->ImpressionsCounter = $ImpressionAndSpendTotalsRollup->TotalImpressions;
	    	$AdCampaignBanner->CurrentSpend = $ImpressionAndSpendTotalsRollup->TotalSpendGross;
	    	
	    	$data = $AdCampaignBanner->getArrayCopy();
	    	
	    	$AdCampaignBannerFactory->saveAdCampaignBannerFromDataArray($data);
	    	
    	endforeach;
    	
    	/*
    	 * Update all AdCampaign tables with the new info from the AdCampaignBanner tables
    	*/
    	
    	$AdCampaignFactory = \_factory\AdCampaign::get_instance();
    	$params = array();
    	$params["Active"] = 1;
    	$AdCampaignList = $AdCampaignFactory->get($params);
    	
    	foreach ($AdCampaignList as $AdCampaign):
    	
	    	$ad_campaign_id = $AdCampaign->AdCampaignID;
	    	
	    	$AdCampaignBannerFactory = \_factory\AdCampaignBanner::get_instance();
	    	$params = array();
	    	$params["AdCampaignID"] = $ad_campaign_id;
	    	$AdCampaignBannerList = $AdCampaignBannerFactory->get($params);
	    	
	    	$impressions_counter 	= 0;
	    	$current_spend			= 0;
	    	 
	    	foreach ($AdCampaignBannerList as $AdCampaignBanner):
	    	 
		    	$impressions_counter 	+= $AdCampaignBanner->ImpressionsCounter;
		    	$current_spend 			+= floatval($AdCampaignBanner->CurrentSpend);
		    	 
	    	endforeach;
	    	
	    	$AdCampaign->ImpressionsCounter 	= $impressions_counter;
	    	$AdCampaign->CurrentSpend 			= $current_spend;
	    	 
	    	$data = $AdCampaign->getArrayCopy();
	    	 
	    	$AdCampaignFactory->saveAdCampaignFromDataArray($data);
	    	 
    	endforeach;
    }
    
    private function getPerTime($obj, $config, $is_admin, $extra_params = null) {
    
    
    	$params = $this->params()->fromQuery();
    	if (!empty($params['step'])) {
    		$step = $params['step'];
    	} else {
    		$step = 1;
    	}
    
    	$DateCreatedGreater = date('Y-m-d H:i:s', time() - 15 * $step * 60);
    	$DateCreatedLower = date('Y-m-d H:i:s', time() - 15 * ($step - 1) * 60);
    
    	if (!empty($params['step'])) {
    
    		switch ($params['interval']) {
    
    			case '0':
    				$where_params = array(
    				'DateCreatedGreater' => $DateCreatedGreater,
    				'DateCreatedLower' => $DateCreatedLower,
    				);
    				break;
    
    			case '1':
    				$where_params = array(
    				'DateCreatedGreater' => ($params['time_from'] < $params['time_to']) ? $params['time_from'] : $params['time_to'],
    				'DateCreatedLower' => ($params['time_from'] > $params['time_to']) ? $params['time_from'] : $params['time_to'],
    				);
    				break;
    
    			default:
    				return false;
    				break;
    		}
    	} else {
    		$where_params = array(
    				'DateCreatedGreater' => $DateCreatedGreater,
    				'DateCreatedLower' => $DateCreatedLower,
    		);
    	}
    
    	if ($extra_params !== null && count($extra_params) > 0):
	    	foreach ($extra_params as $key => $value):
	    		$where_params[$key] = $value;
	    	endforeach;
    	endif;
    
    	
    	if (!empty($params['refresh'])) {
    		$refresh = true;
    	} else {
    		$refresh = false;
    	}
    
    	$data = array(
    			// 'data' => $obj->getPerTimeCached($config, $where_params, 900, $refresh, $is_admin),
    			'data' => $obj->getPerTime($where_params),
    			'step' => $step
    	);
    
    	return json_encode($data);
    
    }

}
