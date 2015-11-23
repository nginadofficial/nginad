<?php

namespace buyrtbfidelity\workflows\tasklets\common\insertionorderlineitem;

class CheckSspChannelSelections {

	public static function execute(&$Logger, &$Workflow, \model\openrtb\RtbBidRequest &$RtbBidRequest, \model\openrtb\RtbBidRequestImp &$RtbBidRequestImp, &$InsertionOrderLineItem) {
		
		/*
		 * Skip this decision for the test user installed
		 * user_login = suckmedia
		 * 
		 * Also enable the global exchange SSP selection
		 * bypass if set to true in config/autoload/global.php
		 */
		
		if ($InsertionOrderLineItem->UserID == TEST_USER_DEMAND
			|| $Workflow->config['settings']['ssp_channel_bypass'] === true):
			return true;
		endif;
		
		/*
		 * From NginAd 1.6 on, SSP inventory will only be available
		 * to private exchange Domain Admins on an explicit basis.
		 * That means that all demand customers must have chosen
		 * a SSP RTB channel via the insertion order and line item
		 * views for it to fill on inventory from that SSP 
		 * RTB site_id channel
		 */
		if ($RtbBidRequest->is_local_request === false):
			
			if (!empty($RtbBidRequest->RtbBidRequestSite->id)):
				$site_id = $RtbBidRequest->RtbBidRequestSite->id;
			elseif (!empty($RtbBidRequest->RtbBidRequestApp->id)):
				$site_id = $RtbBidRequest->RtbBidRequestApp->id;
			else:
				/*
				 * Return true here if you want to blindly bid
				 * against OpenRTB bid requests with no site object
				 * or app object with which to match ids from the 
				 * SSP chooser on the IO line item page from the
				 * demand dashboard.
				 */
				return false;
			endif;
			
			$site_id = strtolower($site_id);
			
			$exchange_name 			= strtolower($RtbBidRequest->ssp_exchange_name);
		
			$SspRtbChannelToInsertionOrderLineItemFactory =  \_factory\SspRtbChannelToInsertionOrderLineItem::get_instance();
			
			$params = array();
			$params["InsertionOrderLineItemID"] 		= $InsertionOrderLineItem->InsertionOrderLineItemID;
			$SspRtbChannelToInsertionOrderLineItemList 	= $SspRtbChannelToInsertionOrderLineItemFactory->get_cached($Workflow->config, $params);
			
			foreach ($SspRtbChannelToInsertionOrderLineItemList as $SspRtbChannelToInsertionOrderLineItem):
				
				$site_id_compare 		= strtolower($SspRtbChannelToInsertionOrderLineItem->SspPublisherChannelID);
				$exchange_name_compare 	= strtolower($SspRtbChannelToInsertionOrderLineItem->SspExchange);

				if ($site_id == $site_id_compare && $exchange_name == $exchange_name_compare):
					return true;
				endif;
	
			endforeach;

			return false;
			
		endif;
		
		return true;
	}
}
