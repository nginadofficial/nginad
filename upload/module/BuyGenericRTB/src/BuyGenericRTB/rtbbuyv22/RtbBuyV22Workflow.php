<?php
/**
 * CDNPAL NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2015 CDNPAL Ltd. All Rights Reserved
 * @license GPLv3
 */

namespace rtbbuyv22;

use rtbbuy\RtbBuyWorkflow;

abstract class RtbBuyV22Workflow extends RtbBuyWorkflow
{
	
	protected $rtb_provider = "none";
	public static $rtb_child_class_name = "none";

	// singleton
	private static $_instance;	

	public static function get_instance() {
		if (self::$_instance == null):
			self::$_instance = new self::$rtb_child_class_name();
		endif;
		return self::$_instance;	
	}
	
    public function process_business_rules_workflow($RtbBid) {

    	$logger = \rtbbuyv22\RtbBuyV22Logger::get_instance();
    	$DisplayWorkflow = new \buyrtb\workflows\DisplayWorkflow();
    	
    	return $DisplayWorkflow->process_business_rules_workflow($logger, $RtbBid);
    
    }

}
