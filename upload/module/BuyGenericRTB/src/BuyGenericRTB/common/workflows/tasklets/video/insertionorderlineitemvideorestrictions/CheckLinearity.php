<?php
/**
 * NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2016 NginAd Foundation. All Rights Reserved
 * @license GPLv3
 */

namespace buyrtb\workflows\tasklets\video\insertionorderlineitemvideorestrictions;

class CheckLinearity {
	
	public static function execute(&$Logger, &$Workflow, \model\openrtb\RtbBidRequest &$RtbBidRequest, \model\openrtb\RtbBidRequestImp &$RtbBidRequestImp, &$InsertionOrderLineItem, &$InsertionOrderLineItemVideoRestrictions) {
		
		$RtbBidRequestVideo = $RtbBidRequestImp->RtbBidRequestVideo;
		
		if (!is_numeric($InsertionOrderLineItemVideoRestrictions->Linearity)):
			return true;
		endif;
		
		// Validate that the value is a number
		if (!is_numeric($RtbBidRequestVideo->linearity)):
			if ($Logger->setting_log === true):
				$Logger->log[] = "Failed: " . "Check video linearity code :: EXPECTED: "
						. 'Numeric Value,'
						. " GOT: " . $RtbBidRequestVideo->linearity;
			endif;
			return false;
		endif;
		
		$result = $InsertionOrderLineItemVideoRestrictions->Linearity == $RtbBidRequestVideo->linearity;
		
		if ($result === false && $Logger->setting_log === true):
			$Logger->log[] = "Failed: " . "Check video linearity code :: EXPECTED: "
				. $InsertionOrderLineItemVideoRestrictions->Linearity
				. " GOT: " . $RtbBidRequestVideo->linearity;
		endif;
		
		return $result;
	}
}
