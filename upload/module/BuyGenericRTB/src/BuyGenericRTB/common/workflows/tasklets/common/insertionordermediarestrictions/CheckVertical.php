<?php
/**
 * NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2016 NginAd Foundation. All Rights Reserved
 * @license GPLv3
 */

namespace buyrtb\workflows\tasklets\common\insertionordermediarestrictions;

class CheckVertical {

	public static function execute(&$Logger, &$Workflow, \model\openrtb\RtbBidRequest &$RtbBidRequest, \model\openrtb\RtbBidRequestImp &$RtbBidRequestImp, &$InsertionOrderLineItem, &$InsertionOrderMediaRestrictions) {

		
		$vertical_to_check = null;
		
		if (!empty($RtbBidRequest->RtbBidRequestSite->cat)):
			$verticals_to_check = $RtbBidRequest->RtbBidRequestSite->cat;
		elseif (!empty($RtbBidRequest->RtbBidRequestSite->RtbBidRequestPublisher->cat)):
			$verticals_to_check = $RtbBidRequest->RtbBidRequestSite->RtbBidRequestPublisher->cat;
		elseif (!empty($RtbBidRequest->RtbBidRequestApp->cat)):
			$verticals_to_check = $RtbBidRequest->RtbBidRequestApp->cat;
		elseif (!empty($RtbBidRequest->RtbBidRequestApp->RtbBidRequestPublisher->cat)):
			$verticals_to_check = $RtbBidRequest->RtbBidRequestApp->RtbBidRequestPublisher->cat;
		endif;
		
		/*
		 * Check banner for it being in the right vertical
		 */
		if ($InsertionOrderMediaRestrictions->Vertical !== null && !empty($verticals_to_check) && is_array($verticals_to_check)):
		
			$has_vertical = false;
			
			$vertical_list = explode(",", $InsertionOrderMediaRestrictions->Vertical);
			foreach ($vertical_list as $vertical_id):
				
				foreach ($verticals_to_check as $vertical_to_check):
						
					if ($vertical_to_check == $vertical_id):
					
						$has_vertical = true;
						break 2;
						
					endif;

				endforeach;
				
			endforeach;
			
			if ($has_vertical === false):
				if ($Logger->setting_log === true):
					$Logger->log[] = "Failed: " . "Check publisher zone for it being in the right vertical :: EXPECTED: " . $InsertionOrderMediaRestrictions->Vertical . " GOT: " . $RtbBidRequest->RtbBidRequestSite->RtbBidRequestPublisher->cat;
				endif;
				return false;
			endif;
			
		endif;
		
		return true;
		
	}

}
