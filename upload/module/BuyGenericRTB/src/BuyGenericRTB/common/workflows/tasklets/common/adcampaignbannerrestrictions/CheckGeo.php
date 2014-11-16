<?php
/**
 * CDNPAL NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2015 CDNPAL Ltd. All Rights Reserved
 * @license GPLv3
 */

namespace buyrtb\workflows\tasklets\common\adcampaignbannerrestrictions;

class CheckGeo {

	public static function execute(&$Logger, &$Workflow, \model\openrtb\RtbBidRequest &$RtbBidRequest, \model\openrtb\RtbBidRequestImp &$RtbBidRequestImp, &$AdCampaignBanner, &$AdCampaignBannerRestrictions) {
	
		/*
		 * Check banner geography
		 */
		
		// no geo info to base rejection on
		if ($RtbBidRequest->RtbBidRequestDevice == null || $RtbBidRequest->RtbBidRequestDevice->RtbBidRequestGeo === null):
			return true;
		endif;
			
		// no geo info to base rejection on	
		if ($AdCampaignBannerRestrictions->GeoCountry === null || !isset($RtbBidRequest->RtbBidRequestDevice->RtbBidRequestGeo->bid_request_geo_country)):
			return true;
		endif;
			
		$has_country = false;
			
		$country = strtolower($RtbBid->bid_request_geo["country"]);
		$geo_country_list = explode(",", $AdCampaignBannerRestrictions->GeoCountry);
			
		foreach ($geo_country_list as $geo_country):
			
			if (strtolower($geo_country) == $country):
				
				$has_country = true;
				break;
					
			endif;
				
		endforeach;
			
		if ($has_country === false):
			if ($Logger->setting_log === true):
				$Logger->log[] = "Failed: " . "Check banner geography : Country :: EXPECTED: " . strtolower($AdCampaignBannerRestrictions->GeoCountry) . " GOT: " . $country;
			endif;
			return false;
		else:
			
			/*
			 * STATE CHECK
			*/
				
			if ($Workflow->geo_info === null && $AdCampaignBannerRestrictions->GeoState !== null && !isset($RtbBid->bid_request_geo["state"])):
				
				if ($Workflow->maxmind === null):
					$Workflow->maxmind = new \geoip\maxmind();
				endif;
				
				$Workflow->geo_info = $Workflow->maxmind->get_geo_code($RtbBid->bid_request_device_ip);
				
				if ($Workflow->geo_info !== null):
					$RtbBid->bid_request_geo["state"] = $Workflow->geo_info["state"];
					$RtbBid->bid_request_geo["city"] = $Workflow->geo_info["city"];
				endif;
					
			endif;
				
			if ($AdCampaignBannerRestrictions->GeoState !== null && isset($RtbBid->bid_request_geo["state"])):
				
				if (!isset($RtbBid->bid_request_geo["state"]) && $Workflow->geo_info === null):
					$Workflow->geo_info = $Workflow->maxmind->get_geo_code($this->bid_request_device_ip);
					$this->bid_request_geo["state"] = $Workflow->geo_info["state"];
				endif;
					
				$has_state = false;
					
				$state = strtolower($RtbBid->bid_request_geo["state"]);
				$geo_state_list = explode(",", $AdCampaignBannerRestrictions->GeoState);
				foreach ($geo_state_list as $geo_state):
						
					if (strtolower($geo_state) == $state):
						
						$has_state = true;
						break;
							
					endif;
						
				endforeach;
					
				if ($has_state === false):
					if ($Logger->setting_log === true):
						$Logger->log[] = "Failed: " . "Check banner geography : State :: EXPECTED: " . strtolower($AdCampaignBannerRestrictions->GeoState) . " GOT: " . $state;
					endif;
					return false;
				else:
					
						
					/*
					 * CITY CHECK
					*/
						
					if($Workflow->geo_info === null && $AdCampaignBannerRestrictions->GeoCity !== null && !isset($RtbBid->bid_request_geo["city"])):
						
						if ($Workflow->maxmind === null):
							$Workflow->maxmind = new \geoip\maxmind();
						endif;
							
						$Workflow->geo_info = $Workflow->maxmind->get_geo_code($RtbBid->bid_request_device_ip);
						$RtbBid->bid_request_geo["state"] = $Workflow->geo_info["state"];
						$RtbBid->bid_request_geo["city"] = $Workflow->geo_info["city"];
							
					endif;
						
					if ($AdCampaignBannerRestrictions->GeoCity !== null && isset($RtbBid->bid_request_geo["city"])):
						
						$has_city = false;
							
						$city = strtolower($RtbBid->bid_request_geo["city"]);
						$geo_city_list = explode(",", $AdCampaignBannerRestrictions->GeoCity);
						foreach ($geo_city_list as $geo_city):
							
							if (strtolower($geo_city) == $city):
								
								$has_city = true;
								break;
								
							endif;
								
						endforeach;
							
						if ($has_city === false):
							if ($Logger->setting_log === true):
								$Logger->log[] = "Failed: " . "Check banner geography : City :: EXPECTED: " . strtolower($AdCampaignBannerRestrictions->GeoCity) . " GOT: " . $city;
							endif;
							return false;
						endif;
							
					endif;
						
				endif;
					
			endif;

		endif;
		
		return true;
		
	}
	
}
