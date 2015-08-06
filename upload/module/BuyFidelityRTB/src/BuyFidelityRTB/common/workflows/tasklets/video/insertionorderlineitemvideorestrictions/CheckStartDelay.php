<?php
/**
 * NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2016 NginAd Foundation. All Rights Reserved
 * @license GPLv3
 */

namespace buyrtbfidelity\workflows\tasklets\video\insertionorderlineitemvideorestrictions;

class CheckStartDelay {
	
	public static function execute(&$Logger, &$Workflow, \model\openrtb\RtbBidRequest &$RtbBidRequest, \model\openrtb\RtbBidRequestImp &$RtbBidRequestImp, &$InsertionOrderLineItem, &$InsertionOrderLineItemVideoRestrictions) {
		
		$RtbBidRequestVideo = $RtbBidRequestImp->RtbBidRequestVideo;
		
		if (!is_numeric($InsertionOrderLineItemVideoRestrictions->StartDelay)):
			return true;
		endif;
		
		// Validate that the value is a number
		if (!is_numeric($RtbBidRequestVideo->startdelay)):
			if ($Logger->setting_log === true):
				$Logger->log[] = "Failed: " . "Check video start delay code :: EXPECTED: "
						. 'Numeric Value,'
						. " GOT: " . $RtbBidRequestVideo->startdelay;
			endif;
			return false;
		endif;
		
		$result = $InsertionOrderLineItemVideoRestrictions->StartDelay == $RtbBidRequestVideo->startdelay;
		
		if ($result === false && $Logger->setting_log === true):
			$Logger->log[] = "Failed: " . "Check video start delay code :: EXPECTED: "
				. $InsertionOrderLineItemVideoRestrictions->StartDelay
				. " GOT: " . $RtbBidRequestVideo->startdelay;
		endif;
		
		return $result;
		
	}
}
