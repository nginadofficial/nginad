<?php
/**
 * NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2016 NginAd Foundation. All Rights Reserved
 * @license GPLv3
 */

namespace buyrtbfidelity\workflows\tasklets\common\insertionordermediarestrictions;

class CheckCoppaOptOut {
	
	public static function execute(&$Logger, &$Workflow, \model\openrtb\RtbBidRequest &$RtbBidRequest, \model\openrtb\RtbBidRequestImp &$RtbBidRequestImp, &$InsertionOrderLineItem, &$InsertionOrderMediaRestrictions) {
	
		/*
		 * Check user for Coppa opt out status
		*/
		if ($InsertionOrderMediaRestrictions->Optout !== null && $RtbBidRequest->RtbBidRequestRegulations !== null && $RtbBidRequest->RtbBidRequestRegulations->coppa != $InsertionOrderMediaRestrictions->Optout):
			if ($Logger->setting_log === true):
				$Logger->log[] = "Failed: " . "Check user for Coppa opt out status :: EXPECTED: " 
					. $InsertionOrderMediaRestrictions->Optout . " GOT: " 
					. $RtbBidRequest->RtbBidRequestRegulations->coppa;
			endif;
			return false;
		endif;
		
		return true;
		
	}

}