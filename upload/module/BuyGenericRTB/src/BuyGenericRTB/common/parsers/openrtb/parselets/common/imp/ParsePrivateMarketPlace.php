<?php
/**
 * CDNPAL NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2015 CDNPAL Ltd. All Rights Reserved
 * @license GPLv3
 */

namespace buyrtb\parsers\openrtb\parselets\common\imp;
use \Exception;

class ParsePrivateMarketPlace {
	
	public static function execute(&$Logger, \buyrtb\parsers\openrtb\OpenRTBParser &$Parser, \model\openrtb\RtbBidRequest &$RtbBidRequest, \model\openrtb\RtbBidRequestPmp &$RtbBidRequestPmp, &$pmp) {
		
		// Private Auction ?
		
		\util\ParseHelper::parse_item(
				$RtbBidRequestPmp,
				$pmp,
				"private_auction");
		
		// PMP deals
		
		if (isset($pmp["deals"]) && is_array($pmp["deals"])):
			
			$pmp_deals_list = $pmp["deals"];
				
			foreach ($pmp_deals_list as $pmp_deal):
				
				try {
					$RtbBidRequestDirectDeals = new \model\openrtb\RtbBidRequestDirectDeals();
					\buyrtb\parsers\openrtb\parselets\common\imp\ParseDirectDeals::execute($Logger, $Parser, $RtbBidRequest, $RtbBidRequestDirectDeals, $pmp_deal);
					$RtbBidRequestUser->RtbBidRequestDirectDealsList[] = $RtbBidRequestDirectDeals;
				} catch (Exception $e) {
					throw new Exception($e->getMessage(), $e->getCode(), $e->getPrevious());
				}
				
			endforeach;
			
		endif;
       
	}
}
