<?php

namespace buyrtb\workflows\tasklets\common\insertionorderlineitem;

class CheckSspChannelSelections {

	public static function execute(&$Logger, &$Workflow, \model\openrtb\RtbBidRequest &$RtbBidRequest, \model\openrtb\RtbBidRequestImp &$RtbBidRequestImp, &$InsertionOrderLineItem) {
		
		/*
		 * From NginAd 1.6 on, SSP inventory will only be available
		 * to private exchange Domain Admins on an explicit basis.
		 * That means that all demand customers must have chosen
		 * a SSP RTB channel via the insertion order and line item
		 * views for it to fill on inventory from that SSP 
		 * RTB site_id channel
		 */
		if ($RtbBidRequest->is_local_request === false):
			
			$site_id 				= strtolower($RtbBidRequest->RtbBidRequestSite->id);
			$exchange_name 			= strtolower($RtbBidRequest->ssp_exchange_name);
		
			$SspRtbChannelToInsertionOrderLineItemFactory =  \_factory\SspRtbChannelToInsertionOrderLineItem::get_instance();
			
			$params = array();
			$params["InsertionOrderLineItemID"] 		= $InsertionOrderLineItem->InsertionOrderLineItemID;
			$SspRtbChannelToInsertionOrderLineItemList 	= $SspRtbChannelToInsertionOrderLineItemFactory->get_cached($params);
			
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
