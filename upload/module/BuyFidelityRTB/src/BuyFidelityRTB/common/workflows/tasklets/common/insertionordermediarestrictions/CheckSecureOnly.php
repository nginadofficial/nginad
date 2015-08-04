<?php
/**
 * NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2016 NginAd Foundation. All Rights Reserved
 * @license GPLv3
 */

namespace buyrtbfidelity\workflows\tasklets\common\insertionordermediarestrictions;

class CheckSecureOnly {

	public static function execute(&$Logger, &$Workflow, \model\openrtb\RtbBidRequest &$RtbBidRequest, \model\openrtb\RtbBidRequestImp &$RtbBidRequestImp, &$InsertionOrderLineItem, &$InsertionOrderMediaRestrictions) {
	
		/*
		 * Check banner for https:// secure
		 */
		if ($InsertionOrderMediaRestrictions->Secure !== null && $RtbBidRequestImp->secure !== null && $RtbBidRequestImp->secure != $InsertionOrderMediaRestrictions->Secure):
			if ($Logger->setting_log === true):
				$Logger->log[] = "Failed: " . "Check banner for https:// secure :: EXPECTED: " . $InsertionOrderMediaRestrictions->Secure . " GOT: " . $RtbBidRequestImp->secure;
			endif;
			return false;
		endif;		
		
		return true;
		
	}

}
