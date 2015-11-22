<?php
/**
 * Advertised Media Group - AdBlade SSP Integration
 */

namespace buyrtbfidelity\workflows\tasklets\common\insertionordermediarestrictions;

class CheckFrequency {
	
	public static function execute(&$Logger, &$Workflow, \model\openrtb\RtbBidRequest &$RtbBidRequest, \model\openrtb\RtbBidRequestImp &$RtbBidRequestImp, &$InsertionOrderLineItem, &$InsertionOrderLineItemRestrictions) {
		
		/*
		 * OK, here's the thing.
		 * Normally frequency capping is done on the
		 * frequency of impressions on a certain publisher's
		 * website, zone or inventory.
		 *
		 * However, because in RTB we don't know if the bid
		 * will win the impression or not ahead of time,
		 * we can only calculate frequency against the bids.
		 */
		
		if($InsertionOrderLineItemRestrictions->Freq === null):
			return true;
		endif;
		
		$frequency = intval($InsertionOrderLineItemRestrictions->Freq);
	
		return \util\FrequencyHelper::checkLineItemImpressionFrequency($Workflow->config, $frequency, $InsertionOrderLineItem->InsertionOrderLineItemID);
		
	}
}
