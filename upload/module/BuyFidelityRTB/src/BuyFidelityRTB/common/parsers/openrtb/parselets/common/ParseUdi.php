<?php
/**
 * NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2016 NginAd Foundation. All Rights Reserved
 * @license GPLv3
 */

namespace buyrtbfidelity\parsers\openrtb\parselets\common;

class ParseUdi {
	
	public static function execute(&$Logger, \buyrtbfidelity\parsers\openrtb\OpenRTBParser &$Parser, \model\openrtb\RtbBidRequest &$RtbBidRequest, \model\openrtb\RtbBidRequestExtensionsUdi &$RtbBidRequestExtensionsUdi, &$udi) {

		// android id
		
		\util\ParseHelper::parse_item(
				$RtbBidRequestExtensionsUdi,
				$udi,
				"androidid");
		
		// md5 hashed android id
		
		\util\ParseHelper::parse_item(
				$RtbBidRequestExtensionsUdi,
				$udi,
				"androididmd5");	
		
		// sha1 hashed android id
		
		\util\ParseHelper::parse_item(
				$RtbBidRequestExtensionsUdi,
				$udi,
				"androididsha1");
		
		// Hardware ID. As of today only commonly used on Android.
		
		\util\ParseHelper::parse_item(
				$RtbBidRequestExtensionsUdi,
				$udi,
				"imei");
		
		// md5 hashed imei
		
		\util\ParseHelper::parse_item(
				$RtbBidRequestExtensionsUdi,
				$udi,
				"imeimd5");
		
		// sha1 hashed imei
		
		\util\ParseHelper::parse_item(
				$RtbBidRequestExtensionsUdi,
				$udi,
				"imeisha1");
		
		// md5 hash of the iOS UDID
		
		\util\ParseHelper::parse_item(
				$RtbBidRequestExtensionsUdi,
				$udi,
				"udidmd5");
		
		// sha1 hash of the iOS UDID
		
		\util\ParseHelper::parse_item(
				$RtbBidRequestExtensionsUdi,
				$udi,
				"udidsha1");
		
		// md5 hash of the string representation (lowercase separated by colons) of the WiFi mac address
		
		\util\ParseHelper::parse_item(
				$RtbBidRequestExtensionsUdi,
				$udi,
				"macmd5");
		
		// sha1 hash of the string representation (lowercase separated by colons) of the WiFi mac address
		
		\util\ParseHelper::parse_item(
				$RtbBidRequestExtensionsUdi,
				$udi,
				"macsha1");
		
		// sha1 hash of the byte array of the WiFi mac address (iOS) or sha1 of the Android Id string
		
		\util\ParseHelper::parse_item(
				$RtbBidRequestExtensionsUdi,
				$udi,
				"odin");
		
		// Open-source identification scheme created by marketing company Appsfire
		
		\util\ParseHelper::parse_item(
				$RtbBidRequestExtensionsUdi,
				$udi,
				"openudid");
		
		// Privacy aware unique identifier on iOS6 and above. Replacement for UDID.
		
		\util\ParseHelper::parse_item(
				$RtbBidRequestExtensionsUdi,
				$udi,
				"idfa");
		
		// md5 hash of the unique identifier on iOS 6 or above
		
		\util\ParseHelper::parse_item(
				$RtbBidRequestExtensionsUdi,
				$udi,
				"idfamd5");
		
		// sha1 hash of the unique identifier on iOS 6 or above
		
		\util\ParseHelper::parse_item(
				$RtbBidRequestExtensionsUdi,
				$udi,
				"idfasha1");
		
		// Opt-in for IDFA (Apple Advertising Id)
		
		\util\ParseHelper::parse_item(
				$RtbBidRequestExtensionsUdi,
				$udi,
				"idfatracking");
		
		// Privacy aware unique identifier on Android. Replaces Android ID
		
		\util\ParseHelper::parse_item(
				$RtbBidRequestExtensionsUdi,
				$udi,
				"googleadid");
		
		// Opt-Out for googleadid
		
		\util\ParseHelper::parse_item(
				$RtbBidRequestExtensionsUdi,
				$udi,
				"googlednt");
		
		// a deviceid sent from BlackBerry 10 SDKs
		
		\util\ParseHelper::parse_item(
				$RtbBidRequestExtensionsUdi,
				$udi,
				"bbid");
		
		// a deviceid sent from WindowsPhone
		
		\util\ParseHelper::parse_item(
				$RtbBidRequestExtensionsUdi,
				$udi,
				"wpid");
				
	}
	
}
