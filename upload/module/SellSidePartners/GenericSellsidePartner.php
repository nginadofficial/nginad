<?php
/**
 * CDNPAL NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2015 CDNPAL Ltd. All Rights Reserved
 * @license GPLv3
 */

/*
 * This partner is REQUIRED for sell side RTB,
 * DO NOT DELETE
 */

class GenericSellsidePartner {
	
	public $partner_name  				= 'GenericSellsidePartner';
	// overridden by rtb.config.php config for this partner
	public $partner_id  				= 0;
	// overridden by rtb.config.php config for this partner
	public $partner_rtb_url 			= "http://server.nginad.com/bid"; 
	public $rtb_connection_timeout_ms 	= 50;
	public $rtb_timeout_ms 				= 130;
	public $partner_quality_score 		= 65;
	public $verify_ssl 					= false;
	
	public function customize($json_ping_data) {
	
		return $json_ping_data;
	}
	
}
