<?php
/**
 * NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2016 NginAd Foundation. All Rights Reserved
 * @license GPLv3
 */

namespace rtbbuyheaderbidding;

abstract class RtbBuyHeaderBiddingWorkflow extends \rtbbuy\RtbBuyWorkflow
{
	
    public function process_business_rules_workflow($config, $rtb_seat_id, &$no_bid_reason, \model\openrtb\RtbBidRequest &$RtbBidRequest) {

    	$logger = \rtbbuyheaderbidding\RtbBuyHeaderBiddingLogger::get_instance();
    	$OpenRTBWorkflow = new \buyrtbheaderbidding\workflows\OpenRTBWorkflow();
    	
    	return $OpenRTBWorkflow->process_business_rules_workflow($logger, $config, $rtb_seat_id, $no_bid_reason, $RtbBidRequest);
    
    }

}
