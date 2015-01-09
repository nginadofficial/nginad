<?php

namespace util;
use \Exception;

class ProjectHoneyPot {
	
	protected $honeypot_key;
	
	protected $remote_ip = null;
	
	protected $response = null;
	
	protected $error = null;

	private static $visitor_type = array(
			0 => 'Search Engine Bot',
			1 => 'Suspicious',
			2 => 'Harvester',
			3 => 'Suspicious, Harvester',
			4 => 'Comment Spammer',
			5 => 'Suspicious, Comment Spammer',
			6 => 'Harvester, Comment Spammer',
			7 => 'Suspicious, Harvester, Comment Spammer'
	);
	
	private static $search_engine = array(
			0 => 'Undocumented',
			1 => 'AltaVista',
			2 => 'Ask',
			3 => 'Baidu',
			4 => 'Excite',
			5 => 'Google',
			6 => 'Looksmart',
			7 => 'Lycos',
			8 => 'MSN',
			9 => 'Yahoo',
			10 => 'Cuil',
			11 => 'InfoSeek',
			12 => 'Miscellaneous'
	);
	

	public function __construct($remote_ip, $honeypot_key) {
		$this->remote_ip = $remote_ip;
		$this->honeypot_key = $honeypot_key;
		try {
			$this->query();
		} catch (Exception $e) {
			$this->error = $e->getMessage();
		}
	}

	private function query() {
		$this->response = explode( ".", gethostbyname( $this->honeypot_key . "." .
				implode ( ".", array_reverse( explode( ".",
						$this->remote_ip ) ) ) .
				".dnsbl.httpbl.org" ) );
	}

	public function isListed() {
		if ($this->response[0] == 127) {
			return true;
		}
		return false;
	}

	public function getVisitorType() {
		if ($this->isListed()):
			return $this->response[3];
		endif;
		return false;
	}

	public function getFormattedVisitorType() {
		if ($this->isListed()):
			if ($this->response[3] == 0):
				return self::$visitor_type[$this->response[3]] . ' (' . self::$search_engine[$this->response[2]] . ')';
			else:
				return self::$visitor_type[$this->response[3]];
			endif;
		else:
			return false;
		endif;
	}
		
	/**
	 * @return int Threat score (out of a possible 255)
	 */
	public function getThreatRating() {
		if ($this->isListed()):
			return $this->response[2];
		endif;
		return 0;
	}
	
	/**
	 * Gets the number of days since an event was tracked for an ip address
	 * if it is listed in the httpBL.
	 *
	 * @return int Number of days since most recent event (up to max of 255)
	 */
	public function getRecency() {
		if ($this->isListed()):
			return $this->response[1];
		endif;
		return 0;
	}
	
	public function getError() {
		return $this->error;
	}

	protected function getRawResponse() {
		return $this->response;
	}
	
	protected function getRemoteIp() {
		return $this->remote_ip;
	}
	
	public function isSearchEngine() {
		if ($this->isListed() && $this->response[3] == 0):
			return true;
		endif;
		return false;
	}
}
