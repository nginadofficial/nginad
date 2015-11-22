<?php
/**
 * NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2016 NginAd Foundation. All Rights Reserved
 * @license GPLv3
 */

namespace buyrtb\workflows\tasklets\common\insertionorder;
use stdClass;

class GetGeoCodeCountry {
	
	public static function execute(&$Logger, &$Workflow, \model\openrtb\RtbBidRequest &$RtbBidRequest) {
		
		/*
		 * use maxmind incrementally. The geo-Country pay DB we have is only 1 meg
		 * if we need city/state ok, but only load it if absolutely necessary
		 */
		
		if (empty($RtbBidRequest->RtbBidRequestDevice)):
			$RtbBidRequest->RtbBidRequestDevice = new \stdClass();
		endif;
		
		$RtbBidRequestGeo = new \model\openrtb\RtbBidRequestGeo();
		$RtbBidRequest->RtbBidRequestDevice->RtbBidRequestGeo = $RtbBidRequestGeo;

		// Trust country from partner Geo object?
		//if (!empty($RtbBidRequest->RtbBidRequestDevice->ip) && filter_var($RtbBidRequest->RtbBidRequestDevice->ip, FILTER_VALIDATE_IP) && empty($RtbBidRequest->RtbBidRequestDevice->RtbBidRequestGeo->country)):
		if (!empty($RtbBidRequest->RtbBidRequestDevice->ip) && filter_var($RtbBidRequest->RtbBidRequestDevice->ip, FILTER_VALIDATE_IP)):
			$Workflow->maxmind = new \geoip\maxmind();
			$RtbBidRequest->RtbBidRequestDevice->RtbBidRequestGeo->country = $Workflow->maxmind->get_geo_code_country($RtbBidRequest->RtbBidRequestDevice->ip);
		endif;
	}
	
}
