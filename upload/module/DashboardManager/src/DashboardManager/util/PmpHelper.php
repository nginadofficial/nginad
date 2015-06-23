<?php

namespace util;

class PmpHelper {
	
	/*
	 * Pmp Tables Enabled State:
	 * 
	 * 0 - Default, neither enabled or disabled
	 * 1 - Enabled
	 * 2 - Disabled
	 */
	
	/* 
	 * I have an Website/RTB Channel and need all related Insertion Order
	 * line items elligible for the PMP deal
	 */
	
	public static function getPmpInsertionOrderLineItemsForWebsite($PublisherWebsiteID) {
		
		$PmpDealPublisherWebsiteToInsertionOrderFactory = \_factory\PmpDealPublisherWebsiteToInsertionOrder::get_instance();
		$params = array();
		$params["Enabled"] = 1;
		$params["PublisherWebsiteID"] = $PublisherWebsiteID;
		$PmpDealPublisherWebsiteToInsertionOrderList = $PmpDealPublisherWebsiteToInsertionOrderFactory->get($params);
	}
	
}
