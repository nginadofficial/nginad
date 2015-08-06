<?php
/**
 * NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2016 NginAd Foundation. All Rights Reserved
 * @license GPLv3
 */

namespace model\openrtb;

class RtbBidRequestExtensionsUdi {
	
	/*
	 * FIDELITY MOD: ext.udi
	 */
	public $androidid;
	
	//md5 hashed android id
	public $androididmd5;
	
	// sha1 hashed android id
	public $androididsha1;
	
	// Hardware ID. As of today only commonly used on Android.
	public $imei;
	
	// md5 hashed imei
	public $imeimd5;
	
	// sha1 hashed imei
	public $imeisha1;
	
	// md5 hash of the iOS UDID
	public $udidmd5;
	
	// sha1 hash of the iOS UDID
	public $udidsha1;
	
	// md5 hash of the string representation (lowercase separated by colons) of the WiFi mac address
	public $macmd5;
	
	// sha1 hash of the string representation (lowercase separated by colons) of the WiFi mac address
	public $macsha1;
	
	// sha1 hash of the byte array of the WiFi mac address (iOS) or sha1 of the Android Id string
	public $odin;
	
	// Open-source identification scheme created by marketing company Appsfire
	public $openudid;
	
	// Privacy aware unique identifier on iOS6 and above. Replacement for UDID.
	public $idfa;
	
	// md5 hash of the unique identifier on iOS 6 or above
	public $idfamd5;
	
	// sha1 hash of the unique identifier on iOS 6 or above
	public $idfasha1;
	
	// Opt-in for IDFA (Apple Advertising Id)
	public $idfatracking;
	
	// Privacy aware unique identifier on Android. Replaces Android ID
	public $googleadid;
	
	// Opt-Out for googleadid
	public $googlednt;
	
	// a deviceid sent from BlackBerry 10 SDKs
	public $bbid;
	
	// a deviceid sent from WindowsPhone
	public $wpid;

}
