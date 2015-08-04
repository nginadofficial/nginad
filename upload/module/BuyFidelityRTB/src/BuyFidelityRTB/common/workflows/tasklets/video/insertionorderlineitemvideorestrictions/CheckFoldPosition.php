<?php
/**
 * NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2016 NginAd Foundation. All Rights Reserved
 * @license GPLv3
 */

namespace buyrtbfidelity\workflows\tasklets\video\insertionorderlineitemvideorestrictions;

class CheckFoldPosition {
	
	public static function execute(&$Logger, &$Workflow, \model\openrtb\RtbBidRequest &$RtbBidRequest, \model\openrtb\RtbBidRequestImp &$RtbBidRequestImp, &$InsertionOrderLineItem, &$InsertionOrderLineItemVideoRestrictions) {
		
		$RtbBidRequestVideo = $RtbBidRequestImp->RtbBidRequestVideo;
		
		if (!is_numeric($InsertionOrderLineItemVideoRestrictions->FoldPos)):
			return true;
		endif;
		
		// Validate that the value is a number
		if (!is_numeric($RtbBidRequestVideo->pos)):
			if ($Logger->setting_log === true):
				$Logger->log[] = "Failed: " . "Check video fold position :: EXPECTED: "
						. 'Numeric Value,'
						. " GOT: " . $RtbBidRequestVideo->pos;
			endif;
			return false;
		endif;
		
		$result = $InsertionOrderLineItemVideoRestrictions->FoldPos == $RtbBidRequestVideo->pos;
		
		if ($result === false && $Logger->setting_log === true):
			$Logger->log[] = "Failed: " . "Check video fold position :: EXPECTED: "
					. $InsertionOrderLineItemVideoRestrictions->FoldPos
					. " GOT: " . $RtbBidRequestVideo->pos;
		endif;
		
		return $result;
	}
}
