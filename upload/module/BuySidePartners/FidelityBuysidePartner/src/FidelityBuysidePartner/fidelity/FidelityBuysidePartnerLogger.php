<?php
/**
 * NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2016 NginAd Foundation. All Rights Reserved
 * @license GPLv3
 */

namespace buyfidelitybuysidepartner;

class FidelityBuysidePartnerLogger extends \rtbbuyfidelity\RtbBuyFidelityLogger
{
	protected $rtb_provider = "FidelityBuysidePartner";
	
	// logging settings
	public $setting_debug 					= false;
	public $setting_log 					= false;
	public $setting_min_log 				= true;
	public $setting_only_log_bids 			= true;
	public $setting_log_to_screen 			= false;
	public $setting_log_file_location 		= "logs/fidelitybuysidepartner/";

}
