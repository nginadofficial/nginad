<?php
/**
 * NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2016 NginAd Foundation. All Rights Reserved
 * @license GPLv3
 */

namespace buyrtbfidelity\workflows;

class VideoWorkflow
{
	
	public function process_business_rules_workflow(&$Logger, &$ParentWorkflow, \model\openrtb\RtbBidRequest &$RtbBidRequest, &$RtbBidRequestImp, &$InsertionOrderLineItem, &$InsertionOrderLineItemVideoRestrictionsFactory) {
    	
    	// Check video restrictions
    	if (\buyrtbfidelity\workflows\tasklets\video\insertionorderlineitem\VideoRestrictionsWorkflow::execute($Logger, $ParentWorkflow, $RtbBidRequest, $RtbBidRequestImp, $InsertionOrderLineItem, $InsertionOrderLineItemVideoRestrictionsFactory) === false):
    		return false;
    	endif;
    	
    	return true;
    }


}
