<?php
/**
 * NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2016 NginAd Foundation. All Rights Reserved
 * @license GPLv3
 */

namespace buyfidelitybuysidepartner;

class FidelityBuysidePartnerInit extends \rtbbuyfidelity\RtbBuyFidelityInit
{

	private static $logger_class_name = "\buyfidelitybuysidepartner\FidelityBuysidePartnerLogger";
	private static $workflow_class_name = "\buyfidelitybuysidepartner\FidelityBuysidePartnerWorkflow";
	
	public static function init() {
		
		\rtbbuyfidelity\RtbBuyFidelityLogger::$rtb_child_class_name = self::$logger_class_name;
		\rtbbuyfidelity\RtbBuyFidelityWorkflow::$rtb_child_class_name = self::$workflow_class_name;
		
	}
	
}

