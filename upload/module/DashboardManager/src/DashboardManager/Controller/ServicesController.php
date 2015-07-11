<?php

namespace DashboardManager\Controller;

use DashboardManager\ParentControllers\DemandAbstractActionController;

class ServicesController extends DemandAbstractActionController {
	
	public function sspAction() {
	
		$initialized = $this->initialize();
		if ($initialized !== true) return $initialized;
	
		
		$SspRtbChannelDailyStatsRollUpFactory = \_factory\SspRtbChannelDailyStatsRollUp::get_instance();
		$params = array();
		/*
		 * Grab only yesterday's stats
		 */
		$current_time = time();
		$yesterday_time = $current_time - 86400;
		$yesterday = date("m/d/Y", $yesterday_time);
		$params["MDY"] = $yesterday;
		$SspRtbChannelDailyStatsRollUpList = $SspRtbChannelDailyStatsRollUpFactory->get($params);

		$data = array();
		
		foreach ($SspRtbChannelDailyStatsRollUpList as $SspRtbChannelDailyStatsRollUp):

			$site_id =	$SspRtbChannelDailyStatsRollUp->SspRtbChannelSiteID;
			if (strlen($site_id) > 10):
				$site_id = '&hellip;' . substr($site_id, -10);
			endif;
			
			$site_name =	$SspRtbChannelDailyStatsRollUp->RtbChannelSiteName;
			if (strlen($site_name) > 20):
				$site_name = substr($site_name, 0, 20) . '&hellip;';
			endif;
			
			$publisher_name =	$SspRtbChannelDailyStatsRollUp->PublisherName;
			if (strlen($publisher_name) > 20):
				$publisher_name = substr($publisher_name, 0, 20) . '&hellip;';
			endif;
			
			$label_name = $SspRtbChannelDailyStatsRollUp->WebDomain . " - " . $site_name . " - " . $publisher_name;
			
			/*
			 * Also remember to replace colons : in SspRtbChannelSiteID
			 * in the Workflows that match the site ids with line items as well
			 */
			
			$row = array(
					" " => '<input type="checkbox" labelname="' . rawurlencode($label_name) . '" class="ckssp" name="ckssp[]" value="' . rawurlencode(str_replace(':', '-', $SspRtbChannelDailyStatsRollUp->SspRtbChannelSiteID) . ':' . str_replace(':', '-', $SspRtbChannelDailyStatsRollUp->BuySidePartnerName) . ':' . $label_name) . '" />',
					"Site ID" => $site_id,
					"Domain" => $SspRtbChannelDailyStatsRollUp->WebDomain,
					"Name" => $site_name,
					"IAB Cat" => $SspRtbChannelDailyStatsRollUp->IABCategory,
					"Daily Imps" => number_format($SspRtbChannelDailyStatsRollUp->ImpressionsOfferedCounter),
					"Average CPM" => $SspRtbChannelDailyStatsRollUp->BidTotalAverage,
					"Floor" => $SspRtbChannelDailyStatsRollUp->BidFloor,
					"Exchange" => $SspRtbChannelDailyStatsRollUp->BuySidePartnerName
			);
		
			$data[] = $row;
			
		endforeach;
		
		$this->setJsonHeader();
		return $this->getResponse()->setContent(json_encode(array("data"=>$data)));
	
	}
	
	public function platformconnectionAction() {
	
		$initialized = $this->initialize();
		if ($initialized !== true) return $initialized;
	
	
		$PrivateExchangeRtbChannelDailyStatsRollUpFactory = \_factory\PrivateExchangeRtbChannelDailyStatsRollUp::get_instance();
		$params = array();
		/*
		 * Grab only yesterday's stats
		*/
		$current_time = time();
		$yesterday_time = $current_time - 86400;
		$yesterday = date("m/d/Y", $yesterday_time);
		$params["MDY"] = $yesterday;
		$PrivateExchangeRtbChannelDailyStatsRollUpList = $PrivateExchangeRtbChannelDailyStatsRollUpFactory->get($params, $this->auth->getUserID());
	
		$data = array();
	
		foreach ($PrivateExchangeRtbChannelDailyStatsRollUpList as $PrivateExchangeRtbChannelDailyStatsRollUp):
	
			$site_id =	$PrivateExchangeRtbChannelDailyStatsRollUp->PublisherWebsiteID;
			if (strlen($site_id) > 10):
				$site_id = '&hellip;' . substr($site_id, -10);
			endif;
				
			$site_name =	$PrivateExchangeRtbChannelDailyStatsRollUp->RtbChannelSiteName;
			if (strlen($site_name) > 20):
				$site_name = substr($site_name, 0, 20) . '&hellip;';
			endif;
				
			$publisher_name =	$PrivateExchangeRtbChannelDailyStatsRollUp->PublisherName;
			if (strlen($publisher_name) > 20):
				$publisher_name = substr($publisher_name, 0, 20) . '&hellip;';
			endif;
				
			$label_name = $PrivateExchangeRtbChannelDailyStatsRollUp->WebDomain . " - " . $site_name . " - " . $publisher_name;
				
			$row = array(
					" " => '<input type="checkbox" labelname="' . rawurlencode($label_name) . '" class="ckssp" name="ckssp[]" value="' . rawurlencode($PrivateExchangeRtbChannelDailyStatsRollUp->PublisherWebsiteID . '::' . $label_name) . '" />',
					"Site ID" => $site_id,
					"Domain" => $PrivateExchangeRtbChannelDailyStatsRollUp->WebDomain,
					"Name" => $site_name,
					"IAB Cat" => $PrivateExchangeRtbChannelDailyStatsRollUp->IABCategory,
					"Daily Imps" => number_format($PrivateExchangeRtbChannelDailyStatsRollUp->ImpressionsOfferedCounter),
					"Average CPM" => $PrivateExchangeRtbChannelDailyStatsRollUp->BidTotalAverage,
					"Floor" => $PrivateExchangeRtbChannelDailyStatsRollUp->BidFloor,
					"Exchange" => $PrivateExchangeRtbChannelDailyStatsRollUp->BuySidePartnerName
			);
		
			$data[] = $row;
				
		endforeach;
	
		$this->setJsonHeader();
		return $this->getResponse()->setContent(json_encode(array("data"=>$data)));
	
	}
	
	public function privateexchangeAction() {
	
		$initialized = $this->initialize();
		if ($initialized !== true) return $initialized;
	
	
		$PrivateExchangeRtbChannelDailyStatsRollUpPxFilterFactory = \_factory\PrivateExchangeRtbChannelDailyStatsRollUpPxFilter::get_instance();
		$params = array();
		$params["ParentID"] = $this->auth->getUserID();
		$PrivateExchangeRtbChannelDailyStatsRollUpPxFilterList = $PrivateExchangeRtbChannelDailyStatsRollUpPxFilterFactory->get($params);
	
		$data = array();
	
		foreach ($PrivateExchangeRtbChannelDailyStatsRollUpPxFilterList as $PrivateExchangeRtbChannelDailyStatsRollUpPxFilter):
	
			$site_id =	$PrivateExchangeRtbChannelDailyStatsRollUpPxFilter->PublisherWebsiteID;
			if (strlen($site_id) > 10):
				$site_id = '&hellip;' . substr($site_id, -10);
			endif;
				
			$site_name =	$PrivateExchangeRtbChannelDailyStatsRollUpPxFilter->RtbChannelSiteName;
			if (strlen($site_name) > 20):
				$site_name = substr($site_name, 0, 20) . '&hellip;';
			endif;
				
			$publisher_name =	$PrivateExchangeRtbChannelDailyStatsRollUpPxFilter->PublisherName;
			if (strlen($publisher_name) > 20):
				$publisher_name = substr($publisher_name, 0, 20) . '&hellip;';
			endif;
				
			$label_name = $PrivateExchangeRtbChannelDailyStatsRollUpPxFilter->WebDomain . " - " . $site_name . " - " . $publisher_name;
				
			$row = array(
					" " => '<input type="checkbox" labelname="' . rawurlencode($label_name) . '" class="ckssp" name="ckssp[]" value="' . rawurlencode($PrivateExchangeRtbChannelDailyStatsRollUpPxFilter->PublisherWebsiteID . '::' . $label_name) . '" />',
					"Site ID" => $site_id,
					"Domain" => $PrivateExchangeRtbChannelDailyStatsRollUpPxFilter->WebDomain,
					"Name" => $site_name,
					"IAB Cat" => $PrivateExchangeRtbChannelDailyStatsRollUpPxFilter->IABCategory,
					"Daily Imps" => number_format($PrivateExchangeRtbChannelDailyStatsRollUpPxFilter->ImpressionsOfferedCounter),
					"Average CPM" => $PrivateExchangeRtbChannelDailyStatsRollUpPxFilter->BidTotalAverage,
					"Floor" => $PrivateExchangeRtbChannelDailyStatsRollUpPxFilter->BidFloor,
					"Exchange" => $PrivateExchangeRtbChannelDailyStatsRollUpPxFilter->BuySidePartnerName
			);
		
			$data[] = $row;
				
		endforeach;
	
		$this->setJsonHeader();
		return $this->getResponse()->setContent(json_encode(array("data"=>$data)));
	
	}
	
}

?>