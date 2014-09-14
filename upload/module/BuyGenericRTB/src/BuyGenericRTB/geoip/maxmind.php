<?php
/**
 * CDNPAL NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2015 CDNPAL Ltd. All Rights Reserved
 * @license GPLv3
 */

namespace geoip;

define('ROOT', 'module/BuyGenericRTB/src/BuyGenericRTB/_geoip/');



class maxmind {

	public function get_geo_code_country($ip_address) {

		require_once(ROOT . "geoip.inc");

		$gi = geoip_open(ROOT . "GeoLiteIPCountry.dat", GEOIP_STANDARD);

		$record = geoip_country_code_by_addr($gi, $ip_address);

		geoip_close($gi);

		if (!empty($record)):
			return $record;
		else:
			return null;
		endif;
		
	}

    public function get_geo_code($ip_address) {

    	require_once(ROOT . "geoipcity.inc");

        $gi = geoip_open(ROOT . "GeoLiteCity.dat", GEOIP_STANDARD);

        $record = geoip_record_by_addr($gi, $ip_address);

        geoip_close($gi);

        return array(
            "country"=>$record->country_code,
            "state"=>$record->region,
            "city"=>$record->city
        );
    }

}


