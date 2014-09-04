<?php
/**
 * CDNPAL NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2015 CDNPAL Ltd. All Rights Reserved
 * @license GPLv3
 */

namespace buygenericbuysidepartner;

class GenericBuysidePartnerInit extends \rtbbuyv22\RtbBuyV22Init
{

	private static $logger_class_name = "\buygenericbuysidepartner\GenericBuysidePartnerLogger";
	private static $workflow_class_name = "\buygenericbuysidepartner\GenericBuysidePartnerWorkflow";
	
	public static function init() {
		
		\rtbbuyv22\RtbBuyV22Logger::$rtb_child_class_name = self::$logger_class_name;
		\rtbbuyv22\RtbBuyV22Workflow::$rtb_child_class_name = self::$workflow_class_name;
		
	}
	
}

