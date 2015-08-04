<?php
/**
 * NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2016 NginAd Foundation. All Rights Reserved
 * @license GPLv3
 */

/*
 * This partner is REQUIRED for sell side RTB,
 * DO NOT DELETE
 */

class LoopbackPartner {

	public $partner_name  				= 'LoopbackPartner';
	public $partner_id  				= 2110;
	// not used for this partner
	public $partner_rtb_url 			= "http://localhost"; 
	public $rtb_connection_timeout_ms 	= 50;
	public $rtb_timeout_ms 				= 130;
	public $partner_quality_score 		= 70;
	public $verify_ssl 					= false;
	public $enable_timeout 				= false;
	
	public function customize($json_ping_data) {
		
		/*
		 * This method is now responsible for decoding the
		 * data as json, and re-encoding it before it
		 * returns the modified OpenRTB request
		 */
		return $json_ping_data;
	}
}
