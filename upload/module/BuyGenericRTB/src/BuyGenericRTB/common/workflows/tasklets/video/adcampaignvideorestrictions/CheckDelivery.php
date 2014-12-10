<?php
/**
 * CDNPAL NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2015 CDNPAL Ltd. All Rights Reserved
 * @license GPLv3
 */

namespace buyrtb\workflows\tasklets\video\adcampaignvideorestrictions;

class CheckDelivery {
	
	public static function execute(&$Logger, &$Workflow, \model\openrtb\RtbBidRequest &$RtbBidRequest, \model\openrtb\RtbBidRequestImp &$RtbBidRequestImp, &$AdCampaignBanner, &$AdCampaignVideoRestrictions) {
		
		$RtbBidRequestVideo = $RtbBidRequestImp->RtbBidRequestVideo;
		
		if (empty($AdCampaignVideoRestrictions->DeliveryCommaSeparated)):
			return true;
		endif;
		
		// Validate that the value is an array
		if (!is_array($RtbBidRequestVideo->delivery)):
			if ($Logger->setting_log === true):
			$Logger->log[] = "Failed: " . "Check video delivery code :: EXPECTED: "
					. 'Array(),'
					. " GOT: " . $RtbBidRequestVideo->delivery;
			endif;
			return false;
		endif;
		
		$delivery_code_list = explode(',', $AdCampaignVideoRestrictions->DeliveryCommaSeparated);
		
		foreach($delivery_code_list as $delivery_code):
		
			foreach($RtbBidRequestVideo->delivery as $delivery_code_to_match):
			
				if ($delivery_code_to_match == $delivery_code):
					
					return true;
					
				endif;
				
			endforeach;
		
		endforeach;
		
		if ($Logger->setting_log === true):
			$Logger->log[] = "Failed: " . "Check video delivery code :: EXPECTED: "
				. $AdCampaignVideoRestrictions->DeliveryCommaSeparated
				. " GOT: " . $RtbBidRequestVideo->delivery;
		endif;
		
		return false;
	}
}
