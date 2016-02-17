<?php
/**
 * NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2016 NginAd Foundation. All Rights Reserved
 * @license GPLv3
 */

namespace buyrtbheaderbidding\workflows\tasklets\video\insertionorderlineitem;

class VideoRestrictionsWorkflow {
	
	public static function execute(&$Logger, &$ParentWorkflow, \model\openrtb\RtbBidRequest &$RtbBidRequest, \model\openrtb\RtbBidRequestImp &$RtbBidRequestImp, &$InsertionOrderLineItem, &$InsertionOrderLineItemVideoRestrictionsFactory) {
	
		/*
		 * Check banner restrictions (optional fields)
		*/
		
		$params = array();
		$params["InsertionOrderLineItemID"] = $InsertionOrderLineItem->InsertionOrderLineItemID;
		$InsertionOrderLineItemVideoRestrictions = $InsertionOrderLineItemVideoRestrictionsFactory->get_row_cached($ParentWorkflow->config, $params);
		
		// no banner restriction info to base rejection on
		if ($InsertionOrderLineItemVideoRestrictions === null):
			return true;
		endif;
		
		// Check video height and width match
		if (\buyrtbheaderbidding\workflows\tasklets\video\insertionorderlineitemvideorestrictions\CheckVideoDimensions::execute($Logger, $ParentWorkflow, $RtbBidRequest, $RtbBidRequestImp, $InsertionOrderLineItem, $InsertionOrderLineItemVideoRestrictions) === false):
			return false;
		endif;
		
		// Check video fold position
		if (\buyrtbheaderbidding\workflows\tasklets\video\insertionorderlineitemvideorestrictions\CheckFoldPosition::execute($Logger, $ParentWorkflow, $RtbBidRequest, $RtbBidRequestImp, $InsertionOrderLineItem, $InsertionOrderLineItemVideoRestrictions) === false):
			return false;
		endif;
		
		// Check Duration
		if (\buyrtbheaderbidding\workflows\tasklets\video\insertionorderlineitemvideorestrictions\CheckDuration::execute($Logger, $ParentWorkflow, $RtbBidRequest, $RtbBidRequestImp, $InsertionOrderLineItem, $InsertionOrderLineItemVideoRestrictions) === false):
			return false;
		endif;
		
		// Check Mime Types
		if (\buyrtbheaderbidding\workflows\tasklets\video\insertionorderlineitemvideorestrictions\CheckMimeTypes::execute($Logger, $ParentWorkflow, $RtbBidRequest, $RtbBidRequestImp, $InsertionOrderLineItem, $InsertionOrderLineItemVideoRestrictions) === false):
			return false;
		endif;
		
		// Check Delivery Type
		if (\buyrtbheaderbidding\workflows\tasklets\video\insertionorderlineitemvideorestrictions\CheckDelivery::execute($Logger, $ParentWorkflow, $RtbBidRequest, $RtbBidRequestImp, $InsertionOrderLineItem, $InsertionOrderLineItemVideoRestrictions) === false):
			return false;
		endif;
		
		// Check Playback Methods
		if (\buyrtbheaderbidding\workflows\tasklets\video\insertionorderlineitemvideorestrictions\CheckPlayback::execute($Logger, $ParentWorkflow, $RtbBidRequest, $RtbBidRequestImp, $InsertionOrderLineItem, $InsertionOrderLineItemVideoRestrictions) === false):
			return false;
		endif;
		
		// Check Protocols
		if (\buyrtbheaderbidding\workflows\tasklets\video\insertionorderlineitemvideorestrictions\CheckProtocols::execute($Logger, $ParentWorkflow, $RtbBidRequest, $RtbBidRequestImp, $InsertionOrderLineItem, $InsertionOrderLineItemVideoRestrictions) === false):
			return false;
		endif;
		
		// Check Start Delay
		if (\buyrtbheaderbidding\workflows\tasklets\video\insertionorderlineitemvideorestrictions\CheckStartDelay::execute($Logger, $ParentWorkflow, $RtbBidRequest, $RtbBidRequestImp, $InsertionOrderLineItem, $InsertionOrderLineItemVideoRestrictions) === false):
			return false;
		endif;
		
		// Check Supported APIs
		if (\buyrtbheaderbidding\workflows\tasklets\video\insertionorderlineitemvideorestrictions\CheckSupportedApis::execute($Logger, $ParentWorkflow, $RtbBidRequest, $RtbBidRequestImp, $InsertionOrderLineItem, $InsertionOrderLineItemVideoRestrictions) === false):
			return false;
		endif;
		
		// Check banner for https:// secure
		if (\buyrtbheaderbidding\workflows\tasklets\common\insertionordermediarestrictions\CheckSecureOnly::execute($Logger, $ParentWorkflow, $RtbBidRequest, $RtbBidRequestImp, $InsertionOrderLineItem, $InsertionOrderLineItemVideoRestrictions) === false):
			return false;
		endif;
		
		// Check user for Coppa opt out status
		if (\buyrtbheaderbidding\workflows\tasklets\common\insertionordermediarestrictions\CheckCoppaOptOut::execute($Logger, $ParentWorkflow, $RtbBidRequest, $RtbBidRequestImp, $InsertionOrderLineItem, $InsertionOrderLineItemVideoRestrictions) === false):
			return false;
		endif;
		
		// Check banner for it being in the right vertical
		if (\buyrtbheaderbidding\workflows\tasklets\common\insertionordermediarestrictions\CheckVertical::execute($Logger, $ParentWorkflow, $RtbBidRequest, $RtbBidRequestImp, $InsertionOrderLineItem, $InsertionOrderLineItemVideoRestrictions) === false):
			return false;
		endif;
		
		// Check banner geography
		if (\buyrtbheaderbidding\workflows\tasklets\common\insertionordermediarestrictions\CheckGeo::execute($Logger, $ParentWorkflow, $RtbBidRequest, $RtbBidRequestImp, $InsertionOrderLineItem, $InsertionOrderLineItemVideoRestrictions) === false):
			return false;
		endif;
		
		return true;
	}
	
}
