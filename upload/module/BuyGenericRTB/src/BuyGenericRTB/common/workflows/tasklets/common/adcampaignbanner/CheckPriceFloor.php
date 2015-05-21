<?php
/**
 * CDNPAL NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2015 CDNPAL Ltd. All Rights Reserved
 * @license GPLv3
 */

namespace buyrtb\workflows\tasklets\common\adcampaignbanner;

class CheckPriceFloor {
	
	public static function execute(&$Logger, &$Workflow, \model\openrtb\RtbBidRequest &$RtbBidRequest, \model\openrtb\RtbBidRequestImp &$RtbBidRequestImp, &$AdCampaignBanner, $markup_rate) {
	
		/*
		 * Check banner floor
		 */
        $imp_floor                 		= $RtbBidRequestImp->bidfloor;
        $mark_down 						= floatval($AdCampaignBanner->BidAmount) * floatval($markup_rate);
  		$banner_bid_amount              = floatval($AdCampaignBanner->BidAmount) - floatval($mark_down);

    	if (floatval($imp_floor) > floatval($banner_bid_amount)):
        	if ($Logger->setting_log === true):
           		$Logger->log[] = "Failed: " . "Check bid floor :: EXPECTED LESS THAN: " . $imp_floor . " -> GOT: " . $banner_bid_amount;
         	endif;
         	
         	return false;
  		endif;
			
		return true;
	}
	
}
