<?php
/**
 * NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2016 NginAd Foundation. All Rights Reserved
 * @license GPLv3
 */

namespace buyrtb\workflows\tasklets\video\insertionorderlineitemvideorestrictions;

class CheckVideoDimensions {
	
	public static function execute(&$Logger, &$Workflow, \model\openrtb\RtbBidRequest &$RtbBidRequest, \model\openrtb\RtbBidRequestImp &$RtbBidRequestImp, &$InsertionOrderLineItem, &$InsertionOrderLineItemVideoRestrictions) {
		
		$RtbBidRequestVideo = $RtbBidRequestImp->RtbBidRequestVideo;
		
		$result1 = true;
		$result2 = true;
		
		if (is_numeric($InsertionOrderLineItemVideoRestrictions->MinHeight) && $InsertionOrderLineItemVideoRestrictions->MinHeight != 0):
			
			// Validate that the value is a number
			if (!is_numeric($RtbBidRequestVideo->h)):
				if ($Logger->setting_log === true):
					$Logger->log[] = "Failed: " . "Check video minimum height :: EXPECTED: "
							. 'Numeric Value,'
							. " GOT: " . $RtbBidRequestVideo->h;
				endif;
				$result1 = false;
			else:
			
				$result1 = $RtbBidRequestVideo->h >= $InsertionOrderLineItemVideoRestrictions->MinHeight;
				
				if ($result1 === false && $Logger->setting_log === true):
					$Logger->log[] = "Failed: " . "Check video minimum height :: EXPECTED: "
							. $InsertionOrderLineItemVideoRestrictions->MinHeight
							. " GOT: " . $RtbBidRequestVideo->h;
				endif;
			endif;
		endif;
		
		if (is_numeric($InsertionOrderLineItemVideoRestrictions->MinWidth) && $InsertionOrderLineItemVideoRestrictions->MinWidth != 0):
			
			// Validate that the value is a number
			if (!is_numeric($RtbBidRequestVideo->w)):
				if ($Logger->setting_log === true):
					$Logger->log[] = "Failed: " . "Check video minimum width :: EXPECTED: "
							. 'Numeric Value,'
							. " GOT: " . $RtbBidRequestVideo->w;
				endif;
				$result2 = false;
			else:
			
				$result2 = $RtbBidRequestVideo->w >= $InsertionOrderLineItemVideoRestrictions->MinWidth;
				
				if ($result2 === false && $Logger->setting_log === true):
					$Logger->log[] = "Failed: " . "Check video minimum width :: EXPECTED: "
							. $InsertionOrderLineItemVideoRestrictions->MinWidth
							. " GOT: " . $RtbBidRequestVideo->w;
				endif;
			endif;
			
		endif;

		return $result1 && $result2;
	}
	
}

