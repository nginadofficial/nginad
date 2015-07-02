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
			
			$row = array(
					" " => '<input type="checkbox" labelname="' . rawurlencode($label_name) . '" class="ckssp" name="ckssp[]" value="' . rawurlencode($SspRtbChannelDailyStatsRollUp->SspRtbChannelSiteID) . '" />',
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
	
}

?>