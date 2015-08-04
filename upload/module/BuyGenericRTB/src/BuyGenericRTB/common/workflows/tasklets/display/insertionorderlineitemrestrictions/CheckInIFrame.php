<?php
/**
 * NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2016 NginAd Foundation. All Rights Reserved
 * @license GPLv3
 */

namespace buyrtb\workflows\tasklets\display\insertionorderlineitemrestrictions;

class CheckInIFrame {
	
	public static function execute(&$Logger, &$Workflow, \model\openrtb\RtbBidRequest &$RtbBidRequest, \model\openrtb\RtbBidRequestImp &$RtbBidRequestImp, &$InsertionOrderLineItem, &$InsertionOrderLineItemRestrictions) {
		
		$RtbBidRequestBanner = $RtbBidRequestImp->RtbBidRequestBanner;
		
		if (isset($RtbBidRequestBanner->topframe)):
			$in_iframe = $RtbBidRequestBanner->topframe == 1 ? false : true; 
		else:
			$in_iframe = false;
		endif;
		/*
		 * Check banner IFRAME disposition
		 * - we are checking for string 0 as well
		*/
		if ($InsertionOrderLineItemRestrictions->InIframe != null && $InsertionOrderLineItemRestrictions->InIframe == 0 && $in_iframe == true):
			if ($Logger->setting_log === true):
				$Logger->log[] = "Failed: " . "Check banner in IFRAME :: EXPECTED: " 
					. $InsertionOrderLineItemRestrictions->InIframe
					. " GOT: " . $in_iframe;
			endif;
			return false;
		endif;
		
		return true;
	}
}
