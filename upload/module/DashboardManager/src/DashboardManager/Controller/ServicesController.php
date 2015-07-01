<?php

namespace DashboardManager\Controller;

use DashboardManager\ParentControllers\DemandAbstractActionController;

class ServicesController extends DemandAbstractActionController {
	
	public function sspAction() {
	
		$initialized = $this->initialize();
		if ($initialized !== true) return $initialized;
	
		
		$SspRtbChannelDailyStatsRollUpFactory = \_factory\SspRtbChannelDailyStatsRollUp::get_instance();
		$params = array();
		$SspRtbChannelDailyStatsRollUpList = $SspRtbChannelDailyStatsRollUpFactory->get($params);

		$data = array();
		
		foreach ($SspRtbChannelDailyStatsRollUpList as $SspRtbChannelDailyStatsRollUp):
			
			$row = array(
					" " => '<input type="checkbox" name="ckssp" value="1" />',
					"Site ID" => $SspRtbChannelDailyStatsRollUp->SspRtbChannelSiteID,
					"Domain Name" => $SspRtbChannelDailyStatsRollUp->WebDomain,
					"Daily Impressions" => number_format($SspRtbChannelDailyStatsRollUp->ImpressionsOfferedCounter),
					"Average CPM" => $SspRtbChannelDailyStatsRollUp->BidTotalAverage,
					"Floor" => $SspRtbChannelDailyStatsRollUp->BidFloor,
					"Exchange" => "adx"
			);
		
			$data[] = $row;
			
		endforeach;
		
		$this->setJsonHeader();
		return $this->getResponse()->setContent(json_encode(array("data"=>$data)));
	
	}
	
}

?>