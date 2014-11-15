<?php
/**
 * CDNPAL NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2015 CDNPAL Ltd. All Rights Reserved
 * @license GPLv3
 */

namespace buyrtb\workflows\tasklets\common\adcampaign;

class GetGeoCodeCountry {
	
	public static function execute(&$Logger, &$Workflow, &$RtbBid) {
		
		/*
		 * use maxmind incrementally. The geo-Country pay DB we have is only 1 meg
		 * if we need city/state ok, but only load it if absolutely necessary
		 */
		
		if ($RtbBid->bid_request_device_ip !== null && $RtbBid->bid_request_geo === null):
			$Workflow->maxmind = new \geoip\maxmind();
			$RtbBid->bid_request_geo["country"] = $Workflow->maxmind->get_geo_code_country($RtbBid->bid_request_device_ip);
		endif;
	}
	
}
