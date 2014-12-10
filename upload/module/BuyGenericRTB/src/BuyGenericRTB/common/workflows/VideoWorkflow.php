<?php
/**
 * CDNPAL NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2015 CDNPAL Ltd. All Rights Reserved
 * @license GPLv3
 */

namespace buyrtb\workflows;

class VideoWorkflow
{
	public $current_time;
	
	// geocity light
	public $geo_info = null;
	public $maxmind = null;
	
	public $config;
	
    public function process_business_rules_workflow(&$Logger, &$ParentWorkflow, \model\openrtb\RtbBidRequest &$RtbBidRequest) {

    	return true;
    }


}
