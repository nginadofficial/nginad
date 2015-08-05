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
		$use_date = date("m/d/Y", $yesterday_time);
		
		$selected_date = $this->getRequest()->getQuery('selected-date');
		if ($selected_date != null):
			$selected_date = str_replace('-', '/', $selected_date);
			$selected_timestamp = strtotime($selected_date);
			if ($selected_timestamp !== false):
				$use_date = date("m/d/Y", $selected_timestamp);
			endif;
		endif;
		
		$insertion_order_id = $this->getRequest()->getQuery('insertion-order-id');
		if ($insertion_order_id != null):
			$insertion_order_id = intval($insertion_order_id);
			$markup_rate = \util\Markup::getMarkupRateByInsertionOrderIDAndUserID($insertion_order_id, $this->auth->getEffectiveIdentityID(), $this->config_handle);
		else:
		    $user_markup = \util\Markup::getMarkupForUser($this->auth->getEffectiveIdentityID(), $this->config_handle, false);
		    if ($user_markup != null):
		   		$markup_rate = $user_markup->MarkupRate;
		   	else:
		   		$markup_rate = $this->config_handle['system']['default_demand_markup_rate'];
			endif;
		endif;
		
		$params["MDY"] = $use_date;
		
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
			
			// floor should be marked up by the IO markup rate
			
			$adusted_floor_price = "";
			if ($SspRtbChannelDailyStatsRollUp->BidFloor):
				$floor_price = floatval($SspRtbChannelDailyStatsRollUp->BidFloor);
				// Approximation: actual implementation is mark-down in buyrtb\workflows\OpenRTBWorkflow
				$mark_up = $floor_price * floatval($markup_rate);
				$adusted_floor_price = floatval($floor_price) + floatval($mark_up);
				$adusted_floor_price = sprintf("%1.2f", $adusted_floor_price);
			endif;
				
			$row = array(
					" " => '<input type="checkbox" labelname="' . rawurlencode($label_name) . '" class="ckssp" name="ckssp[]" value="' . rawurlencode(str_replace(':', '__COLON__', $SspRtbChannelDailyStatsRollUp->SspRtbChannelSiteID) . ':' . str_replace(':', '__COLON__', $SspRtbChannelDailyStatsRollUp->BuySidePartnerName) . ':' . $label_name) . '" />',
					"Site ID" => $site_id,
					"Domain" => $SspRtbChannelDailyStatsRollUp->WebDomain,
					"Name" => $site_name,
					"IAB Cat" => $SspRtbChannelDailyStatsRollUp->IABCategory,
					"Daily Imps" => number_format($SspRtbChannelDailyStatsRollUp->ImpressionsOfferedCounter),
					"Average CPM" => $SspRtbChannelDailyStatsRollUp->BidTotalAverage,
					"Floor" => $adusted_floor_price,
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
		$use_date = date("m/d/Y", $yesterday_time);
		
		$selected_date = $this->getRequest()->getQuery('selected-date');
		if ($selected_date != null):
			$selected_date = str_replace('-', '/', $selected_date);
			$selected_timestamp = strtotime($selected_date);
			if ($selected_timestamp !== false):
				$use_date = date("m/d/Y", $selected_timestamp);
			endif;
		endif;

		$insertion_order_id = $this->getRequest()->getQuery('insertion-order-id');
		if ($insertion_order_id != null):
			$insertion_order_id = intval($insertion_order_id);
			$markup_rate = \util\Markup::getMarkupRateByInsertionOrderIDAndUserID($insertion_order_id, $this->auth->getEffectiveIdentityID(), $this->config_handle);
		else:
			$user_markup = \util\Markup::getMarkupForUser($this->auth->getEffectiveIdentityID(), $this->config_handle, false);
		    if ($user_markup != null):
		   		$markup_rate = $user_markup->MarkupRate;
		   	else:
		   		$markup_rate = $this->config_handle['system']['default_demand_markup_rate'];
			endif;
		endif;
		
		$params["MDY"] = $use_date;
		
		if ($this->auth->isSuperAdmin($this->config_handle)):
			$parent_id = $this->auth->getEffectiveIdentityID();
		else:
			$parent_id = $this->auth->getUserID();
		endif;
		
		if ($parent_id == 0) $parent_id = null;
		
		$PrivateExchangeRtbChannelDailyStatsRollUpList = $PrivateExchangeRtbChannelDailyStatsRollUpFactory->get($params, $parent_id);
	
		$data = array();
	
		$PublisherWebsiteFactory = \_factory\PublisherWebsite::get_instance();
		
		foreach ($PrivateExchangeRtbChannelDailyStatsRollUpList as $PrivateExchangeRtbChannelDailyStatsRollUp):
	
			$site_id =	$PrivateExchangeRtbChannelDailyStatsRollUp->PublisherWebsiteID;

			$site_name =	$PrivateExchangeRtbChannelDailyStatsRollUp->RtbChannelSiteName;
			if (strlen($site_name) > 20):
				$site_name = substr($site_name, 0, 20) . '&hellip;';
			endif;
				
			$publisher_name =	$PrivateExchangeRtbChannelDailyStatsRollUp->PublisherName;
			if (strlen($publisher_name) > 20):
				$publisher_name = substr($publisher_name, 0, 20) . '&hellip;';
			endif;
				
			$label_name = $PrivateExchangeRtbChannelDailyStatsRollUp->WebDomain . " - " . $site_name . " - " . $publisher_name;
				
			$adusted_floor_price = "";
			if ($PrivateExchangeRtbChannelDailyStatsRollUp->BidFloor):
				
				$floor_price = floatval($PrivateExchangeRtbChannelDailyStatsRollUp->BidFloor);
			
				$params = array();
				$params["PublisherWebsiteID"] = $site_id;
				$PublisherWebsite = $PublisherWebsiteFactory->get_row_cached($this->config_handle, $params);
				
				if ($PublisherWebsite == null) continue;
				
				// floor should be marked up by the private exchange domain admin rate for this publisher and website
				
				$px_markup_rate_for_publisher = \util\Markup::getPrivateExchangePublisherMarkupRate($site_id, $PublisherWebsite->DomainOwnerID, $this->config_handle);
				$mark_up = $floor_price * floatval($px_markup_rate_for_publisher);
				$adusted_floor_price = floatval($floor_price) + floatval($mark_up);
				
				// floor should be marked up by the global exchange rate for this publisher and website
	
				$exchange_markup_rate_for_publisher = \util\Markup::getPublisherMarkupRate($site_id, $PublisherWebsite->DomainOwnerID, $this->config_handle);
				$mark_up = $adusted_floor_price * floatval($exchange_markup_rate_for_publisher);
				$adusted_floor_price = floatval($adusted_floor_price) + floatval($mark_up);

				// floor should be marked up by the IO markup rate
				// Approximation: actual implementation is mark-down in buyrtb\workflows\OpenRTBWorkflow
				$mark_up = $adusted_floor_price * floatval($markup_rate);
				$adusted_floor_price = floatval($adusted_floor_price) + floatval($mark_up);
				$adusted_floor_price = sprintf("%1.2f", $adusted_floor_price);
				
			endif;
			
			$row = array(
					" " => '<input type="checkbox" labelname="' . rawurlencode($label_name) . '" class="ckssp" name="ckssp[]" value="' . rawurlencode($PrivateExchangeRtbChannelDailyStatsRollUp->PublisherWebsiteID . '::' . $label_name) . '" />',
					"Site ID" => $site_id,
					"Domain" => $PrivateExchangeRtbChannelDailyStatsRollUp->WebDomain,
					"Name" => $site_name,
					"IAB Cat" => $PrivateExchangeRtbChannelDailyStatsRollUp->IABCategory,
					"Daily Imps" => number_format($PrivateExchangeRtbChannelDailyStatsRollUp->ImpressionsOfferedCounter),
					"Average CPM" => $PrivateExchangeRtbChannelDailyStatsRollUp->BidTotalAverage,
					"Floor" => $adusted_floor_price,
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
		if ($this->auth->isSuperAdmin($this->config_handle)):
			$params["ParentID"] = $this->auth->getEffectiveIdentityID();
		else: 
			$params["ParentID"] = $this->auth->getUserID();
		endif;
		$PrivateExchangeRtbChannelDailyStatsRollUpPxFilterList = $PrivateExchangeRtbChannelDailyStatsRollUpPxFilterFactory->get($params);
	
		$insertion_order_id = $this->getRequest()->getQuery('insertion-order-id');
		if ($insertion_order_id != null):
			$insertion_order_id = intval($insertion_order_id);
			$markup_rate = \util\Markup::getMarkupRateByInsertionOrderIDAndUserID($insertion_order_id, $this->auth->getEffectiveIdentityID(), $this->config_handle);
		else:
			$user_markup = \util\Markup::getMarkupForUser($this->auth->getEffectiveIdentityID(), $this->config_handle, false);
			if ($user_markup != null):
				$markup_rate = $user_markup->MarkupRate;
			else:
				$markup_rate = $this->config_handle['system']['default_demand_markup_rate'];
			endif;
		endif;
		
		$data = array();

		$PublisherWebsiteFactory = \_factory\PublisherWebsite::get_instance();
		
		foreach ($PrivateExchangeRtbChannelDailyStatsRollUpPxFilterList as $PrivateExchangeRtbChannelDailyStatsRollUpPxFilter):
	
			$site_id =	$PrivateExchangeRtbChannelDailyStatsRollUpPxFilter->PublisherWebsiteID;
				
			$site_name =	$PrivateExchangeRtbChannelDailyStatsRollUpPxFilter->RtbChannelSiteName;
			if (strlen($site_name) > 20):
				$site_name = substr($site_name, 0, 20) . '&hellip;';
			endif;
				
			$publisher_name =	$PrivateExchangeRtbChannelDailyStatsRollUpPxFilter->PublisherName;
			if (strlen($publisher_name) > 20):
				$publisher_name = substr($publisher_name, 0, 20) . '&hellip;';
			endif;
				
			$label_name = $PrivateExchangeRtbChannelDailyStatsRollUpPxFilter->WebDomain . " - " . $site_name . " - " . $publisher_name;
				
			$adusted_floor_price = "";
			if ($PrivateExchangeRtbChannelDailyStatsRollUpPxFilter->BidFloor):
				
				$floor_price = floatval($PrivateExchangeRtbChannelDailyStatsRollUpPxFilter->BidFloor);

				$params = array();
				$params["PublisherWebsiteID"] = $site_id;
				$PublisherWebsite = $PublisherWebsiteFactory->get_row_cached($this->config_handle, $params);
				
				if ($PublisherWebsite == null) continue;
			
				// floor should be marked up by the private exchange domain admin rate for this publisher and website
				
				$px_markup_rate_for_publisher = \util\Markup::getPrivateExchangePublisherMarkupRate($site_id, $PublisherWebsite->DomainOwnerID, $this->config_handle);
				$mark_up = $floor_price * floatval($px_markup_rate_for_publisher);
				$adusted_floor_price = floatval($floor_price) + floatval($mark_up);
				
				// floor should be marked up by the global exchange rate for this publisher and website
	
				$exchange_markup_rate_for_publisher = \util\Markup::getPublisherMarkupRate($site_id, $PublisherWebsite->DomainOwnerID, $this->config_handle);
				$mark_up = $adusted_floor_price * floatval($exchange_markup_rate_for_publisher);
				$adusted_floor_price = floatval($adusted_floor_price) + floatval($mark_up);

				// floor should be marked up by the IO markup rate
				// Approximation: actual implementation is mark-down in buyrtb\workflows\OpenRTBWorkflow
				$mark_up = $adusted_floor_price * floatval($markup_rate);
				$adusted_floor_price = floatval($adusted_floor_price) + floatval($mark_up);
				$adusted_floor_price = sprintf("%1.2f", $adusted_floor_price);
				
			endif;
			
			$row = array(
					" " => '<input type="checkbox" labelname="' . rawurlencode($label_name) . '" class="ckssp" name="ckssp[]" value="' . rawurlencode($PrivateExchangeRtbChannelDailyStatsRollUpPxFilter->PublisherWebsiteID . '::' . $label_name) . '" />',
					"Site ID" => $site_id,
					"Domain" => $PrivateExchangeRtbChannelDailyStatsRollUpPxFilter->WebDomain,
					"Name" => $site_name,
					"IAB Cat" => $PrivateExchangeRtbChannelDailyStatsRollUpPxFilter->IABCategory,
					"Daily Imps" => number_format($PrivateExchangeRtbChannelDailyStatsRollUpPxFilter->ImpressionsOfferedCounter),
					"Average CPM" => $PrivateExchangeRtbChannelDailyStatsRollUpPxFilter->BidTotalAverage,
					"Floor" => $adusted_floor_price,
					"Exchange" => $PrivateExchangeRtbChannelDailyStatsRollUpPxFilter->BuySidePartnerName
			);
		
			$data[] = $row;
				
		endforeach;
	
		$this->setJsonHeader();
		return $this->getResponse()->setContent(json_encode(array("data"=>$data)));
	
	}
	
}

?>