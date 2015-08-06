<?php
/**
 * NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2016 NginAd Foundation. All Rights Reserved
 * @license GPLv3
 */

namespace rtbbuyfidelity;

abstract class RtbBuyFidelityWorkflow extends \rtbbuy\RtbBuyWorkflow
{
	
    public function process_business_rules_workflow($config, $rtb_seat_id, &$no_bid_reason, \model\openrtb\RtbBidRequest &$RtbBidRequest) {

    	$logger = \rtbbuyfidelity\RtbBuyFidelityLogger::get_instance();
    	$OpenRTBWorkflow = new \buyrtbfidelity\workflows\OpenRTBWorkflow();
    	
    	return $OpenRTBWorkflow->process_business_rules_workflow($logger, $config, $rtb_seat_id, $no_bid_reason, $RtbBidRequest);
    
    }

}
