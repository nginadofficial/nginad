<?php
/**
 * NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2016 NginAd Foundation. All Rights Reserved
 * @license GPLv3
 */

namespace buyheaderbiddingbuysidepartner;

class HeaderBiddingBuysidePartnerInit extends \rtbbuyheaderbidding\RtbBuyHeaderBiddingInit
{

	private static $logger_class_name = "\buyheaderbiddingbuysidepartner\HeaderBiddingBuysidePartnerLogger";
	private static $workflow_class_name = "\buyheaderbiddingbuysidepartner\HeaderBiddingBuysidePartnerWorkflow";
	
	public static function init() {
		
		\rtbbuyheaderbidding\RtbBuyHeaderBiddingLogger::$rtb_child_class_name = self::$logger_class_name;
		\rtbbuyheaderbidding\RtbBuyHeaderBiddingWorkflow::$rtb_child_class_name = self::$workflow_class_name;
		
	}
	
}

