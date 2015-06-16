<?php
/**
 * CDNPAL NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2015 CDNPAL Ltd. All Rights Reserved
 * @license GPLv3
 */

namespace buyrtb\workflows\tasklets\display\adcampaignbannerrestrictions;

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
		
		/*
		 * Check banner multiple nested IFRAMEs disposition
		 * - we are checking for string 0 as well
		*/
		if ($InsertionOrderLineItemRestrictions->InMultipleNestedIframes != null && $InsertionOrderLineItemRestrictions->InMultipleNestedIframes == 0 && $in_iframe == true):
			if ($Logger->setting_log === true):
			$Logger->log[] = "Failed: " . "Check banner in multiple nested IFRAMEs :: EXPECTED: "
					. $InsertionOrderLineItemRestrictions->InMultipleNestedIframes
					. " GOT: " . $in_iframe;
			endif;
			return false;
		endif;
		
		return true;
	}
}
