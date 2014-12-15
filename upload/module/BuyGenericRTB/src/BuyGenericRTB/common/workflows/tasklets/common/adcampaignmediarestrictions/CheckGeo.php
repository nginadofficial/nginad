<?php
/**
 * CDNPAL NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2015 CDNPAL Ltd. All Rights Reserved
 * @license GPLv3
 */

namespace buyrtb\workflows\tasklets\common\adcampaignmediarestrictions;

class CheckGeo {

	public static function execute(&$Logger, &$Workflow, \model\openrtb\RtbBidRequest &$RtbBidRequest, \model\openrtb\RtbBidRequestImp &$RtbBidRequestImp, &$AdCampaignBanner, &$AdCampaignMediaRestrictions) {
	
		/*
		 * Check banner geography
		 */
		
		// no geo info to base rejection on
		if ($RtbBidRequest->RtbBidRequestDevice == null || $RtbBidRequest->RtbBidRequestDevice->RtbBidRequestGeo === null):
			return true;
		endif;
			
		// no geo info to base rejection on	
		if ($AdCampaignMediaRestrictions->GeoCountry === null || empty($RtbBidRequest->RtbBidRequestDevice->RtbBidRequestGeo->country)):
			return true;
		endif;
		
		$has_country = false;
			
		$country = strtolower($RtbBidRequest->RtbBidRequestDevice->RtbBidRequestGeo->country);
		$geo_country_list = explode(",", $AdCampaignMediaRestrictions->GeoCountry);
			
		foreach ($geo_country_list as $geo_country):
			
			if (strtolower($geo_country) == $country):
				
				$has_country = true;
				break;
					
			endif;
				
		endforeach;
			
		if ($has_country === false):
			if ($Logger->setting_log === true):
				$Logger->log[] = "Failed: " . "Check banner geography : Country :: EXPECTED: " . strtolower($AdCampaignMediaRestrictions->GeoCountry) . " GOT: " . $country;
			endif;
			return false;
		else:
			
			/*
			 * STATE CHECK
			*/
		
			if ($Workflow->geo_info === null && $AdCampaignMediaRestrictions->GeoState !== null && !isset($RtbBidRequest->RtbBidRequestDevice->RtbBidRequestGeo->region)):
				
				if ($Workflow->maxmind === null):
					$Workflow->maxmind = new \geoip\maxmind();
				endif;
				
				$Workflow->geo_info = $Workflow->maxmind->get_geo_code($RtbBidRequest->RtbBidRequestDevice->ip);
				
				if ($Workflow->geo_info !== null):
					$RtbBidRequest->RtbBidRequestDevice->RtbBidRequestGeo->region = $Workflow->geo_info["state"];
					$RtbBidRequest->RtbBidRequestDevice->RtbBidRequestGeo->city = $Workflow->geo_info["city"];
				endif;
					
			endif;
				
			if ($AdCampaignMediaRestrictions->GeoState !== null && isset($RtbBidRequest->RtbBidRequestDevice->RtbBidRequestGeo->region)):
				
				if (!isset($RtbBidRequest->RtbBidRequestDevice->RtbBidRequestGeo->region) && $Workflow->geo_info === null):
					$Workflow->geo_info = $Workflow->maxmind->get_geo_code($RtbBidRequest->RtbBidRequestDevice->ip);
					$RtbBidRequest->RtbBidRequestDevice->RtbBidRequestGeo->region = $Workflow->geo_info["state"];
				endif;
					
				$has_state = false;
					
				$state = strtolower($RtbBidRequest->RtbBidRequestDevice->RtbBidRequestGeo->region);
				$geo_state_list = explode(",", $AdCampaignMediaRestrictions->GeoState);
				foreach ($geo_state_list as $geo_state):
						
					if (strtolower($geo_state) == $state):
						
						$has_state = true;
						break;
							
					endif;
						
				endforeach;
					
				if ($has_state === false):
					if ($Logger->setting_log === true):
						$Logger->log[] = "Failed: " . "Check banner geography : State :: EXPECTED: " . strtolower($AdCampaignMediaRestrictions->GeoState) . " GOT: " . $state;
					endif;
					return false;
				else:
					
						
					/*
					 * CITY CHECK
					*/
						
					if($Workflow->geo_info === null && $AdCampaignMediaRestrictions->GeoCity !== null && empty($RtbBidRequest->RtbBidRequestDevice->RtbBidRequestGeo->city)):
					
						if ($Workflow->maxmind === null):
							$Workflow->maxmind = new \geoip\maxmind();
						endif;
							
						$Workflow->geo_info = $Workflow->maxmind->get_geo_code($RtbBidRequest->RtbBidRequestDevice->ip);
						$RtbBidRequest->RtbBidRequestDevice->RtbBidRequestGeo->region = $Workflow->geo_info["state"];
						$RtbBidRequest->RtbBidRequestDevice->RtbBidRequestGeo->city = $Workflow->geo_info["city"];
							
					endif;
						
					if ($AdCampaignMediaRestrictions->GeoCity !== null && !empty($RtbBidRequest->RtbBidRequestDevice->RtbBidRequestGeo->city)):
						
						$has_city = false;
							
						$city = strtolower($RtbBidRequest->RtbBidRequestDevice->RtbBidRequestGeo->city);
						$geo_city_list = explode(",", $AdCampaignMediaRestrictions->GeoCity);
						foreach ($geo_city_list as $geo_city):
							
							if (strtolower($geo_city) == $city):
								
								$has_city = true;
								break;
								
							endif;
								
						endforeach;
							
						if ($has_city === false):
							if ($Logger->setting_log === true):
								$Logger->log[] = "Failed: " . "Check banner geography : City :: EXPECTED: " . strtolower($AdCampaignMediaRestrictions->GeoCity) . " GOT: " . $city;
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
