<?php
/**
 * NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2016 NginAd Foundation. All Rights Reserved
 * @license GPLv3
 */

namespace buyrtbheaderbidding\workflows\tasklets\video\insertionorderlineitemvideorestrictions;

class CheckMimeTypes {
	
	public static function execute(&$Logger, &$Workflow, \model\openrtb\RtbBidRequest &$RtbBidRequest, \model\openrtb\RtbBidRequestImp &$RtbBidRequestImp, &$InsertionOrderLineItem, &$InsertionOrderLineItemVideoRestrictions) {
		
		$RtbBidRequestVideo = $RtbBidRequestImp->RtbBidRequestVideo;
		
		$result = true;
		
		if (empty($InsertionOrderLineItemVideoRestrictions->MimesCommaSeparated)):
			return $result;
		endif;
		
		$mime_code_list = explode(',', strtolower($InsertionOrderLineItemVideoRestrictions->MimesCommaSeparated));
		
		if (!count($mime_code_list)):
			return $result;
		endif;
		

		// Validate that the value is an array
		if (!is_array($RtbBidRequestVideo->mimes)):
			if ($Logger->setting_log === true):
			$Logger->log[] = "Failed: Required Param: " . "Check video mime type code :: EXPECTED: "
					. 'Array(),'
					. " GOT: " . $RtbBidRequestVideo->mimes;
			endif;
			return false;
		endif;

		/*
		 * All codes in the publisher ad zone
		* for the publisher's video player settings
		* have to be included in the VAST video demand
		*/
		foreach($RtbBidRequestVideo->mimes as $mime_code_to_match):
		
			if (!in_array(strtolower($mime_code_to_match), $mime_code_list)):

				$result = false;
				break;
					
			endif;
			
		endforeach;
		
		if ($result === false && $Logger->setting_log === true):
			$Logger->log[] = "Failed: " . "Check video mime type code :: EXPECTED: "
				. $InsertionOrderLineItemVideoRestrictions->MimesCommaSeparated
				. " GOT: " . join(',', $RtbBidRequestVideo->mimes);
		endif;
		
		return $result;
	}
}
