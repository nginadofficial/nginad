<?php

namespace util;

class Password {
	
	public static function md5_split_salt($password, $salt = null) {

		$length = strlen($password);
		$firsthalf = floor($length/2);
		$secondhalf = $length - $firsthalf; /* YOU MUST subtract and not recalculate to avoid rounding errors! */
		
		$salt_concat = $salt == null ? '' : $salt;

		$result = md5(substr($password, 0, $firsthalf) . $salt_concat . substr($password, $secondhalf-1));

		return $result;
	}
	
}

?>