<?php

namespace util;
	
class WorkflowHelper {
	
	protected static $user_agent 			= "NginAd RTB Ping Robot";
	
	protected static $verify_ssl 			= false;
	

	public static function get_ping_notice_url_curl_request($url) {
	
		//open connection
		$ch = curl_init();
	
		//set the url, number of POST vars, POST data
		curl_setopt( $ch, CURLOPT_USERAGENT, self::$user_agent );
		curl_setopt( $ch, CURLOPT_URL, $url);
		curl_setopt( $ch, CURLOPT_POST, false);
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
		curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, self::$verify_ssl );
		curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, self::$verify_ssl );
		curl_setopt( $ch, CURLOPT_HEADER, false);
		curl_setopt( $ch, CURLOPT_SSLVERSION, 3);
		curl_setopt( $ch, CURLOPT_MAXREDIRS, 10);
	
		// don't take more than 5 seconds connecting and 10 seconds for a response
		curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, 5 );
		curl_setopt( $ch, CURLOPT_TIMEOUT, 10 );
	
		$content = curl_exec($ch);
	
		return $content;
	}
	
	public static function get_first_key($input) {
		
		foreach ($input as $key => $value):
		
			return $key;
		
		endforeach;		
		
		return null;
	}
	
}
