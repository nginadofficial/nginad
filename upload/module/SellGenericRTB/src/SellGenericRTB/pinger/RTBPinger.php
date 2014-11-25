<?php
/**
 * CDNPAL NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2015 CDNPAL Ltd. All Rights Reserved
 * @license GPLv3
 */

namespace pinger;

class RTBPinger {
	
	protected $ping_data;
	
	protected $ping_url;
	
	public $partner_id;
	
	public $partner_name;
	
	protected $ping_connect_timeout_ms;
	
	protected $ping_timeout_ms;
	
	public $partner_quality_score;

	protected $verify_ssl 			= false;
	
	protected $timeout_enabled 		= false;
	
	public $uid;
	
	public $total_bids;
	
	public $lost_bids;
	
	public $won_bids;
	
	public $ping_success 			= false;
	
	public $ping_response;
	
	public $ping_error_message;
	
	public $user_agent 				= "NginAd RTB Ping Robot";
	
	public $winning_bid;  				// to be filled out by the ping manager
	
	public $won_auction				= false;  	// to be filled out by the ping manager
	
	public $is_loopback_pinger		= false;
	
	public $RtbBidResponse;
	
	public function __construct($partner_name, $partner_id, $ping_url, $ping_data, $ping_connect_timeout_ms, $ping_timeout_ms, $partner_quality_score, $verify_ssl, $timeout_enabled) {
		
		$this->partner_name				= $partner_name;
		$this->partner_id				= $partner_id;
		$this->ping_url 				= $ping_url;
		$this->ping_data 				= $ping_data;
		$this->ping_connect_timeout_ms 	= $ping_connect_timeout_ms;
		$this->ping_timeout_ms 			= $ping_timeout_ms;
		$this->partner_quality_score	= $partner_quality_score;
		$this->verify_ssl 				= $verify_ssl;
		$this->timeout_enabled 			= $timeout_enabled;
	}
	

	
	public function get_rtb_ping_curl_request() {
		
		$post_data = $this->ping_data;

		//open connection
		$ch = curl_init();
		
		//set the url, number of POST vars, POST data
		curl_setopt( $ch, CURLOPT_USERAGENT, $this->user_agent );
		curl_setopt( $ch, CURLOPT_URL, $this->ping_url);
		curl_setopt( $ch, CURLOPT_POST, true);
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $post_data);
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
		curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, $this->verify_ssl );
		curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, $this->verify_ssl );
		curl_setopt( $ch, CURLOPT_HEADER, false); 
		curl_setopt( $ch, CURLOPT_SSLVERSION, 3);
		curl_setopt( $ch, CURLOPT_MAXREDIRS, 10);
		
		if ($this->timeout_enabled == true):
			curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT_MS, $this->ping_connect_timeout_ms );
			curl_setopt( $ch, CURLOPT_TIMEOUT_MS, $this->ping_timeout_ms );
		endif;
		
		return $ch;
		
	}
	
	public function get_ping_data() {
		return $this->ping_data;
	}
	
	
}

