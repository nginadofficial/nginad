<?php
/**
 * NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2016 NginAd Foundation. All Rights Reserved
 * @license GPLv3
 */

namespace buyrtbfidelity\workflows\tasklets\display\insertionorderlineitemrestrictions;

class CheckBrowserUserAgent {
	
	public static function execute(&$Logger, &$Workflow, \model\openrtb\RtbBidRequest &$RtbBidRequest, \model\openrtb\RtbBidRequestImp &$RtbBidRequestImp, &$InsertionOrderLineItem, &$InsertionOrderLineItemRestrictions) {
	
		/*
		 * Check browser user-agent for string
		 */
		if ($InsertionOrderLineItemRestrictions->BrowserUserAgentGrep !== null && $RtbBidRequest->RtbBidRequestDevice->ua !== null):
		
			if (strpos(strtolower($RtbBidRequest->RtbBidRequestDevice->ua), strtolower($InsertionOrderLineItemRestrictions->BrowserUserAgentGrep)) === false):
				if ($Logger->setting_log === true):
					$Logger->log[] = "Failed: " . "Check browser user-agent for string :: EXPECTED: " 
							. $InsertionOrderLineItemRestrictions->BrowserUserAgentGrep . " GOT: " 
							. $RtbBidRequest->RtbBidRequestDevice->ua;
				endif;
				return false;
			endif;
			
		endif;
		
		return true;
		
	}
	
}
