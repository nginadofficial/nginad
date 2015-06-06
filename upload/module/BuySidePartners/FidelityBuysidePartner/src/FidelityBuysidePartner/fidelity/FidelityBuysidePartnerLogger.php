<?php
/**
 * CDNPAL NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2015 CDNPAL Ltd. All Rights Reserved
 * @license GPLv3
 */

namespace buyfidelitybuysidepartner;

class FidelityBuysidePartnerLogger extends \rtbbuyfidelity\RtbBuyFidelityLogger
{
	protected $rtb_provider = "FidelityBuysidePartner";
	
	// logging settings
	public $setting_debug 					= true;
	public $setting_log 					= true;
	public $setting_min_log 				= false;
	public $setting_only_log_bids 			= false;
	public $setting_log_to_screen 			= false;
	public $setting_log_file_location 		= "logs/fidelitybuysidepartner/";

}
