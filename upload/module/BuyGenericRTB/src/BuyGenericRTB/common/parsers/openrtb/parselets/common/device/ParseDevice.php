<?php
/**
 * CDNPAL NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2015 CDNPAL Ltd. All Rights Reserved
 * @license GPLv3
 */

namespace buyrtb\parsers\openrtb\parselets\common\device;
use \Exception;

class ParseDevice {
	
	public static function execute(&$Logger, \buyrtb\parsers\openrtb\OpenRTBParser &$Parser, \model\openrtb\RtbBidRequest &$RtbBidRequest, \model\openrtb\RtbBidRequestDevice &$RtbBidRequestDevice, &$device) {
	
		$RtbBidRequestDevice->devicetype = DEVICE_DESKTOP;
		
		if (!isset($Parser->json_post["device"])):
			return;
		endif;
		
		$default_device = $Parser->json_post["device"];
		
		// do not track bit
		
		\util\ParseHelper::parse_item(
				$RtbBidRequestDevice,
				$default_device,
				"dnt");
		
		// User Agent. If URL encoded, decode
		
		if (isset($default_device["ua"])):
		
			\util\ParseHelper::parse_item(
					$RtbBidRequestDevice,
					$default_device,
					"ua");
			
			if (strpos($RtbBidRequestDevice->ua, '%20') !== false):
				$RtbBidRequestDevice->ua = urldecode($RtbBidRequestDevice->ua);
			endif;
		
		endif;
		
		/*
		 * NginAd requires the User's IP Address
		 * for black listing purposes and for fill ratios
		 * against the MD5 checksum.
		 * 
		 * The OpenRTB spec states it's optional
		 */ 
		\util\ParseHelper::parse_with_exception(
				$RtbBidRequestDevice,
				$default_device,
				$Parser->expeption_missing_min_bid_request_params . ": device_ip",
				"ip");
		
		// geo object
		
		if (isset($default_device["geo"])):
		
			$geo = $default_device["geo"];
			$RtbBidRequestGeo = new \model\openrtb\RtbBidRequestGeo();
			\buyrtb\parsers\openrtb\parselets\common\ParseGeo::execute($Logger, $Parser, $RtbBidRequest, $RtbBidRequestGeo, $geo);
			$RtbBidRequestDevice->RtbBidRequestGeo = $RtbBidRequestGeo;
			
		endif;
		
		// device id SHA1
		
		\util\ParseHelper::parse_item(
				$RtbBidRequestDevice,
				$default_device,
				"didsha1");
		
		// device id MD5
		
		\util\ParseHelper::parse_item(
				$RtbBidRequestDevice,
				$default_device,
				"didmd5");
		
		// platform device id SHA1
		
		\util\ParseHelper::parse_item(
				$RtbBidRequestDevice,
				$default_device,
				"dpidsha1");
		
		// platform device id MD5
		
		\util\ParseHelper::parse_item(
				$RtbBidRequestDevice,
				$default_device,
				"dpidmd5");
		
		// mac address SHA1
		
		\util\ParseHelper::parse_item(
				$RtbBidRequestDevice,
				$default_device,
				"macsha1");
		
		// mac address MD5
		
		\util\ParseHelper::parse_item(
				$RtbBidRequestDevice,
				$default_device,
				"macmd5");
		
		// IPv6 address
		
		\util\ParseHelper::parse_item(
				$RtbBidRequestDevice,
				$default_device,
				"ipv6");
		
		// mobile ISP carrier
		
		\util\ParseHelper::parse_item(
				$RtbBidRequestDevice,
				$default_device,
				"carrier");

		// language code ( alpha-2/ISO 639-1 )
		
		\util\ParseHelper::parse_item(
				$RtbBidRequestDevice,
				$default_device,
				"language");
		
		// Device OEM make
		
		\util\ParseHelper::parse_item(
				$RtbBidRequestDevice,
				$default_device,
				"make");
		
		// Device OEM model
		
		\util\ParseHelper::parse_item(
				$RtbBidRequestDevice,
				$default_device,
				"model");
		
		// OS name
		
		\util\ParseHelper::parse_item(
				$RtbBidRequestDevice,
				$default_device,
				"os");

		// OS version
		
		\util\ParseHelper::parse_item(
				$RtbBidRequestDevice,
				$default_device,
				"osv");
		
		// Bit Flag for Javascript Enabled
		
		\util\ParseHelper::parse_item(
				$RtbBidRequestDevice,
				$default_device,
				"js");
		
		// Connection type id
		
		\util\ParseHelper::parse_item(
				$RtbBidRequestDevice,
				$default_device,
				"connectiontype");
		
		// device type id
		
		if (isset($RtbBidRequestDevice->model)):
			 
			if (\mobileutil\MobileDeviceType::isPhone($RtbBidRequestDevice->model) === true):
			 
				$RtbBidRequestDevice->devicetype = DEVICE_MOBILE;
				 
			elseif(\mobileutil\MobileDeviceType::isTablet($RtbBidRequestDevice->model) === true):
			 
				$RtbBidRequestDevice->devicetype = DEVICE_TABLET;
				 
			endif;
		
		endif;
			
		if ($RtbBidRequestDevice->devicetype == DEVICE_DESKTOP
				&& isset($RtbBidRequestDevice->ua) 
				&& $RtbBidRequestDevice->ua != null):
		
			$detect = new \mobileutil\MobileDetect(null, $RtbBidRequestDevice->ua);
			 
			if ($detect->isTablet()):
			 
				$RtbBidRequestDevice->devicetype = DEVICE_TABLET;
			 
			elseif ($detect->isMobile()):
			 
				$RtbBidRequestDevice->devicetype = DEVICE_MOBILE;
			 
			endif;
			
		endif;
		
		// adobe flash version
		
		\util\ParseHelper::parse_item(
				$RtbBidRequestDevice,
				$default_device,
				"flashver");
		
		// native ads unique id
		
		\util\ParseHelper::parse_item(
				$RtbBidRequestDevice,
				$default_device,
				"ifa");
		
	}
	
}
