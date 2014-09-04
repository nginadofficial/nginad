<?php
/**
 * CDNPAL NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2015 CDNPAL Ltd. All Rights Reserved
 * @license GPLv3
 */

namespace rtbbuyv22;

abstract class RtbBuyV22Workflow
{
	
	protected $rtb_provider = "none";
	public static $rtb_child_class_name = "none";

	// singleton
	private static $_instance;	

	public static function get_instance() {
		if (self::$_instance == null):
			self::$_instance = new self::$rtb_child_class_name();
		endif;
		return self::$_instance;	
	}
	
    public function process_business_rules_workflow($RtbBid) {

        $AdCampaignBanner_Match_List = array();

    	$AdCampaignFactory = \_factory\AdCampaign::get_instance();
    	$params = array();
    	$params["Active"] = 1;
    	$AdCampaignList = $AdCampaignFactory->get_cached($RtbBid->config, $params);

    	$current_time = time();

    	$AdCampaignBannerFactory = \_factory\AdCampaignBanner::get_instance();
    	$AdCampaignBannerDomainExclusionFactory = \_factory\AdCampaignBannerDomainExclusion::get_instance();
    	$AdCampaignBannerExclusiveInclusionFactory = \_factory\AdCampaignBannerDomainExclusiveInclusion::get_instance();
    	$AdCampaignBannerRestrictionsFactory = \_factory\AdCampaignBannerRestrictions::get_instance();

    	// geocity light
    	$geo_info = null;
    	$maxmind = null;
    	/*
    	 * use maxmind incrementally. The geo-Country pay DB we have is only 1 meg
    	 * if we need city/state ok, but only load it if absolutely necessary
    	 */

    	if ($RtbBid->bid_request_device_ip !== null && $RtbBid->bid_request_geo === null):
    		$maxmind = new \geoip\maxmind();
    		$RtbBid->bid_request_geo["country"] = $maxmind->get_geo_code_country($RtbBid->bid_request_device_ip);
    	endif;

    	foreach ($AdCampaignList as $AdCampaign):

        	/*
        	 * Check campaign date
        	 */
        	$campaign_startdate                 = strtotime($AdCampaign->StartDate);
        	$campaign_enddate                   = strtotime($AdCampaign->EndDate);

        	if ($current_time < $campaign_startdate || $current_time > $campaign_enddate):
        	   if (\rtbbuyv22\RtbBuyV22Logger::get_instance()->setting_log === true):
                    \rtbbuyv22\RtbBuyV22Logger::get_instance()->log[] = "Failed: " . "Check campaign date :: EXPECTED: " . $AdCampaign->StartDate . "->" . $AdCampaign->EndDate . " GOT: " . date('m/d/Y', $current_time);
        	   endif;
        	   continue;
        	endif;


        	/*
        	 * Check max spend
        	 */

        	if ($AdCampaign->CurrentSpend >= $AdCampaign->MaxSpend):
            	if (\rtbbuyv22\RtbBuyV22Logger::get_instance()->setting_log === true):
            	   \rtbbuyv22\RtbBuyV22Logger::get_instance()->log[] = "Failed: " . "Max Campaign Spend Exceeded";
            	endif;
        	   continue;
        	endif;

        	/*
        	 * Check max impressions
        	 */
        	if ($AdCampaign->ImpressionsCounter >= $AdCampaign->MaxImpressions):
            	if (\rtbbuyv22\RtbBuyV22Logger::get_instance()->setting_log === true):
            	   \rtbbuyv22\RtbBuyV22Logger::get_instance()->log[] = "Failed: " . "Max Campaign Impressions Exceeded";
            	endif;
        	   continue;
        	endif;


        	$markup_rate = \util\Markup::getMarkupRate($AdCampaign, $RtbBid->config);

        	$params = array();
        	$params["AdCampaignID"] = $AdCampaign->AdCampaignID;
        	$params["Active"] = 1;
        	$AdCampaignBannerList = $AdCampaignBannerFactory->get_cached($RtbBid->config, $params);

        	foreach ($AdCampaignBannerList as $AdCampaignBanner):
	        	
	        /*
	         * check the rules against the banner
	         */
	        	
            	/*
            	 * Check banner date
            	 */
            	$banner_startdate                 = strtotime($AdCampaignBanner->StartDate);
            	$banner_enddate                   = strtotime($AdCampaignBanner->EndDate);

            	if ($current_time < $banner_startdate || $current_time > $banner_enddate):
                	if (\rtbbuyv22\RtbBuyV22Logger::get_instance()->setting_log === true):
                	   \rtbbuyv22\RtbBuyV22Logger::get_instance()->log[] = "Failed: " . "Check banner date :: EXPECTED: " . $AdCampaignBanner->StartDate . "->" . $AdCampaignBanner->EndDate . " GOT: " . date('m/d/Y', $current_time);
                	endif;
            	   continue;
            	endif;

            	/*
            	 * Check is mobile web, phone, tablet, native iOS or native Android
            	 */
            	if ($RtbBid->bid_request_mobile != $AdCampaignBanner->IsMobile):
                	if (\rtbbuyv22\RtbBuyV22Logger::get_instance()->setting_log === true):
                	   \rtbbuyv22\RtbBuyV22Logger::get_instance()->log[] = "Failed: " . "Check is mobile web :: EXPECTED: " . $AdCampaignBanner->IsMobile . " GOT: " . $RtbBid->bid_request_mobile;
                	endif;
            	   continue;
            	endif;

            	/*
            	 * Check banner height and width match
            	 */
            	if ($AdCampaignBanner->Height != $RtbBid->bid_request_imp_banner_h || $AdCampaignBanner->Width != $RtbBid->bid_request_imp_banner_w):
                	if (\rtbbuyv22\RtbBuyV22Logger::get_instance()->setting_log === true):
                	   \rtbbuyv22\RtbBuyV22Logger::get_instance()->log[] = "Failed: " . "Check banner height match :: EXPECTED: " . $AdCampaignBanner->Height . " GOT: " . $RtbBid->bid_request_imp_banner_h;
                	   \rtbbuyv22\RtbBuyV22Logger::get_instance()->log[] = "Failed: " . "Check banner width match :: EXPECTED: " . $AdCampaignBanner->Width . " GOT: " . $RtbBid->bid_request_imp_banner_w;
                	endif;
            	   continue;
            	endif;
            
            	/*
            	 * Check to see if this AdCampaginBanner is associated to a
            	 * contract zone. Contract bound banners are not eligible for
            	 * RTB bidding.
            	 */
            	if ($AdCampaignBanner->AdCampaignTypeID == AD_TYPE_CONTRACT):
            		continue;
            	elseif ($AdCampaignBanner->AdCampaignTypeID == AD_TYPE_IN_HOUSE_REMNANT && $RtbBid->is_local_request == false):
            		continue;
            	elseif ($AdCampaignBanner->AdCampaignTypeID == AD_TYPE_RTB_REMNANT && $RtbBid->is_local_request == true):
	           		continue;
            	endif;
            	
            	/*
            	 * Check banner domain exclusive inclusions
            	 * This will narrow the publisher pool down so we
            	 * only working with the publishers that the client wants
            	 * to advertise on.
            	*/

            	$params = array();
            	$params["AdCampaignBannerID"] = $AdCampaignBanner->AdCampaignBannerID;
            	$AdCampaignBannerExclusiveInclusionList = $AdCampaignBannerExclusiveInclusionFactory->get_cached($RtbBid->config, $params);

            	foreach ($AdCampaignBannerExclusiveInclusionList as $AdCampaignBannerExclusiveInclusion):

	            	$domain_to_match = strtolower($AdCampaignBannerExclusiveInclusion->DomainName);

	            	if ($AdCampaignBannerExclusiveInclusion->InclusionType == "url"):

			            if (strpos(strtolower($RtbBid->bid_request_site_page), $domain_to_match) === false
				            	&& strpos(strtolower($RtbBid->bid_request_site_domain), $domain_to_match) === false):

				            if (\rtbbuyv22\RtbBuyV22Logger::get_instance()->setting_log === true):
				            	\rtbbuyv22\RtbBuyV22Logger::get_instance()->log[] = "Failed: " . "Check banner page url, site exclusive inclusions do not match :: EXPECTED: " . $domain_to_match . " GOT: bid_request_site_page: " . $RtbBid->bid_request_site_page . ", bid_request_site_domain: " . $RtbBid->bid_request_site_domain;
			            	endif;
			            	// goto next in the larger enclosing foreach loop
			            	continue 2;

		            	endif;

	            	elseif ($RtbBid->bid_request_refurl && $AdCampaignBannerExclusiveInclusion->InclusionType == "referrer"):

		            	if (strpos(strtolower($RtbBid->bid_request_refurl), $domain_to_match) === false):

			            	if (\rtbbuyv22\RtbBuyV22Logger::get_instance()->setting_log === true):
			            		\rtbbuyv22\RtbBuyV22Logger::get_instance()->log[] = "Failed: " . "Check banner page referrer url, site exclusive inclusions do not match :: EXPECTED: " . $domain_to_match . " GOT: " . $RtbBid->bid_request_refurl;
			            	endif;
			            	continue 2;

		            	endif;

	            	endif;

            	endforeach;


            	/*
            	 * Check banner domain exclusions match
            	 */


            	$params = array();
            	$params["AdCampaignBannerID"] = $AdCampaignBanner->AdCampaignBannerID;
            	$AdCampaignBannerDomainExclusionList = $AdCampaignBannerDomainExclusionFactory->get_cached($RtbBid->config, $params);

            	foreach ($AdCampaignBannerDomainExclusionList as $AdCampaignBannerDomainExclusion):

                	$domain_to_match = strtolower($AdCampaignBannerDomainExclusion->DomainName);

                	if ($AdCampaignBannerDomainExclusion->ExclusionType == "url"):

                    	if (strpos(strtolower($RtbBid->bid_request_site_page), $domain_to_match) !== false
                    	|| strpos(strtolower($RtbBid->bid_request_site_domain), $domain_to_match) !== false):

                        	if (\rtbbuyv22\RtbBuyV22Logger::get_instance()->setting_log === true):
                        	   \rtbbuyv22\RtbBuyV22Logger::get_instance()->log[] = "Failed: " . "Check banner page url, site exclusions match :: EXPECTED: " . $domain_to_match . " GOT: bid_request_site_page: " . $RtbBid->bid_request_site_page . ", bid_request_site_domain: " . $RtbBid->bid_request_site_domain;
                        	endif;
                    	   // goto next in the larger enclosing foreach loop
                    	   continue 2;

                    	endif;

                	elseif ($RtbBid->bid_request_refurl && $AdCampaignBannerDomainExclusion->ExclusionType == "referrer"):

                    	if (strpos(strtolower($RtbBid->bid_request_refurl), $domain_to_match) !== false):

                        	if (\rtbbuyv22\RtbBuyV22Logger::get_instance()->setting_log === true):
                        	   \rtbbuyv22\RtbBuyV22Logger::get_instance()->log[] = "Failed: " . "Check banner page referrer url, site exclusions match :: EXPECTED: " . $domain_to_match . " GOT: " . $RtbBid->bid_request_refurl;
                        	endif;
                    	   continue 2;

                    	endif;

                	endif;

            	endforeach;

            	/*
            	 * Check banner restrictions (optional fields)
            	 */


            	$params = array();
            	$params["AdCampaignBannerID"] = $AdCampaignBanner->AdCampaignBannerID;
            	$AdCampaignBannerRestrictions = $AdCampaignBannerRestrictionsFactory->get_row_cached($RtbBid->config, $params);

            	/*
            	 * Check banner restrictions
            	 */

            	if ($AdCampaignBannerRestrictions !== null):

                	/*
                	 * Check banner position on page
                	 */
            	
	            	/*
	            	 * Banner position check not supported by OpenRTB 2.1
	            	*/
            	
                	/*
                	 * Check banner system fold position (sFoldPos), I don't think we can trust the user fold position (uFoldPos)
                	 */
                	if ($AdCampaignBannerRestrictions->FoldPos !== null && $RtbBid->bid_request_sFoldPos !== null && $AdCampaignBannerRestrictions->FoldPos != $RtbBid->bid_request_sFoldPos):
                    	if (\rtbbuyv22\RtbBuyV22Logger::get_instance()->setting_log === true):
                    	   \rtbbuyv22\RtbBuyV22Logger::get_instance()->log[] = "Failed: " . "Check banner system fold position :: EXPECTED: " . $AdCampaignBannerRestrictions->FoldPos . " GOT: " . $RtbBid->bid_request_sFoldPos;
                    	endif;
                	   continue;
                	endif;

	            	/*
	            	 * Frequency capping not supported by OpenRTB 2.1
	            	*/

	            	/*
	            	 * Time Zone not supported by OpenRTB 2.1
	            	*/

                	/*
                	 * IFrame detection not supported by OpenRTB 2.1
                	 */

                	/*
                	 * Multiple nested IFrames detection not supported by OpenRTB 2.1
                	*/

                	/*
                	 * Client's screen resolution detection not supported by OpenRTB 2.1
                	 */

                	/*
                	 * Check browser language
                	 */
                	if ($AdCampaignBannerRestrictions->HttpLanguage !== null && $RtbBid->bid_request_device_language !== null):

                    	$has_http_language = false;

                    	$request_language_list = explode(";", strtolower($RtbBid->bid_request_device_language));
                    	$http_language_list = explode(";", strtolower($AdCampaignBannerRestrictions->HttpLanguage));

                    	foreach ($http_language_list as $http_language):

                        	if (in_array(trim($http_language), $request_language_list)):

                            	$has_http_language = true;
                            	break;

                        	endif;

                    	endforeach;

                    	if ($has_http_language === false):
                        	if (\rtbbuyv22\RtbBuyV22Logger::get_instance()->setting_log === true):
                        	   \rtbbuyv22\RtbBuyV22Logger::get_instance()->log[] = "Failed: " . "Check browser language :: EXPECTED: " . $AdCampaignBannerRestrictions->HttpLanguage . " GOT: " . $RtbBid->bid_request_device_language;
                        	endif;
                    	   continue;
                    	endif;

                	endif;

                	/*
                	 * Check browser user-agent for string
                	 */
                	if ($AdCampaignBannerRestrictions->BrowserUserAgentGrep !== null && $RtbBid->bid_request_device_ua !== null):

                    	if (strpos(strtolower($RtbBid->bid_request_device_ua), strtolower($AdCampaignBannerRestrictions->BrowserUserAgentGrep)) === false):
                        	if (\rtbbuyv22\RtbBuyV22Logger::get_instance()->setting_log === true):
                        	   \rtbbuyv22\RtbBuyV22Logger::get_instance()->log[] = "Failed: " . "Check browser user-agent for string :: EXPECTED: " . $AdCampaignBannerRestrictions->BrowserUserAgentGrep . " GOT: " . $RtbBid->bid_request_device_ua;
                        	endif;
                    	   continue;
                    	endif;

                	endif;

                	
                	/*
                	 * Check browser cookie for string detection not supported by OpenRTB 2.1
                	*/



                	/*
                	 * Check banner for PMP Enable
                	 */
                	if ($AdCampaignBannerRestrictions->PmpEnable !== null && $RtbBid->bid_request_imp_pmp !== null && $RtbBid->bid_request_imp_pmp != $AdCampaignBannerRestrictions->PmpEnable):
                    	if (\rtbbuyv22\RtbBuyV22Logger::get_instance()->setting_log === true):
                    	   \rtbbuyv22\RtbBuyV22Logger::get_instance()->log[] = "Failed: " . "Check banner for PMP Enable :: EXPECTED: " . $AdCampaignBannerRestrictions->PmpEnable . " GOT: " . $RtbBid->bid_request_imp_pmp;
                    	endif;
                	   continue;
                	endif;

                	/*
                	 * Check banner for https:// secure
                	 */
                	if ($AdCampaignBannerRestrictions->Secure !== null && $RtbBid->bid_request_secure !== null && $RtbBid->bid_request_secure != $AdCampaignBannerRestrictions->Secure):
                    	if (\rtbbuyv22\RtbBuyV22Logger::get_instance()->setting_log === true):
                    	   \rtbbuyv22\RtbBuyV22Logger::get_instance()->log[] = "Failed: " . "Check banner for https:// secure :: EXPECTED: " . $AdCampaignBannerRestrictions->Secure . " GOT: " . $RtbBid->bid_request_secure;
                    	endif;
                	   continue;
                	endif;

                	/*
                	 * Check user for Coppa opt out status
                	 */
                	if ($AdCampaignBannerRestrictions->Optout !== null && $RtbBid->bid_request_regs_coppa !== null && $RtbBid->bid_request_regs_coppa != $AdCampaignBannerRestrictions->Optout):
                    	if (\rtbbuyv22\RtbBuyV22Logger::get_instance()->setting_log === true):
                    	   \rtbbuyv22\RtbBuyV22Logger::get_instance()->log[] = "Failed: " . "Check user for Coppa opt out status :: EXPECTED: " . $AdCampaignBannerRestrictions->Optout . " GOT: " . $RtbBid->bid_request_regs_coppa;
                    	endif;
                	   continue;
                	endif;

                	/*
                	 * Check banner for it being in the right vertical
                	 */
                	if ($AdCampaignBannerRestrictions->Vertical !== null && $RtbBid->bid_request_site_publisher_cat !== null):

                    	$has_vertical = false;

                    	$vertical_list = explode(",", $AdCampaignBannerRestrictions->Vertical);
                    	foreach ($vertical_list as $vertical_id):

                        	if ($RtbBid->bid_request_site_publisher_cat == $vertical_id):

                            	$has_vertical = true;
                            	break;

                        	endif;

                    	endforeach;

                    	if ($has_vertical === false):
                        	if (\rtbbuyv22\RtbBuyV22Logger::get_instance()->setting_log === true):
                        	   \rtbbuyv22\RtbBuyV22Logger::get_instance()->log[] = "Failed: " . "Check banner for it being in the right vertical :: EXPECTED: " . $AdCampaignBannerRestrictions->Vertical . " GOT: " . $RtbBid->bid_request_site_publisher_cat;
                        	endif;
                    	   continue;
                    	endif;

                	endif;

                	/*
                	 * Check banner geography
                	*/

                	if ($RtbBid->bid_request_geo !== null):

	                	if ($AdCampaignBannerRestrictions->GeoCountry !== null && isset($RtbBid->bid_request_geo["country"])):

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
			                	if (\rtbbuyv22\RtbBuyV22Logger::get_instance()->setting_log === true):
			                		\rtbbuyv22\RtbBuyV22Logger::get_instance()->log[] = "Failed: " . "Check banner geography : Country :: EXPECTED: " . strtolower($AdCampaignBannerRestrictions->GeoCountry) . " GOT: " . $country;
			                	endif;
			                	continue;
		                	else:

			                	/*
			                	 * STATE CHECK
			                	*/

			                	if ($geo_info === null && $AdCampaignBannerRestrictions->GeoState !== null && !isset($RtbBid->bid_request_geo["state"])):

			                		if ($maxmind === null):
			                			$maxmind = new \geoip\maxmind();
									endif;

						            $geo_info = $maxmind->get_geo_code($RtbBid->bid_request_ip);
						            $RtbBid->bid_request_geo["state"] = $geo_info["state"];
						           	$RtbBid->bid_request_geo["city"] = $geo_info["city"];

				                endif;

			                	if ($AdCampaignBannerRestrictions->GeoState !== null && isset($RtbBid->bid_request_geo["state"])):

			                		if (!isset($RtbBid->bid_request_geo["state"]) && $geo_info === null):
			                			$geo_info = $maxmind->get_geo_code($this->bid_request_ip);
			                		$this->bid_request_geo["state"] = $geo_info["state"];
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
					                	if (\rtbbuyv22\RtbBuyV22Logger::get_instance()->setting_log === true):
						                	\rtbbuyv22\RtbBuyV22Logger::get_instance()->log[] = "Failed: " . "Check banner geography : State :: EXPECTED: " . strtolower($AdCampaignBannerRestrictions->GeoState) . " GOT: " . $state;
						                	endif;
					                	continue;
				                	else:


				                		/*
				                		 * CITY CHECK
				                		 */

					                	if($geo_info === null && $AdCampaignBannerRestrictions->GeoCity !== null && !isset($RtbBid->bid_request_geo["city"])):

						                	if ($maxmind === null):
						                		$maxmind = new \geoip\maxmind();
						                	endif;

						                	$geo_info = $maxmind->get_geo_code($RtbBid->bid_request_ip);
						                	$RtbBid->bid_request_geo["state"] = $geo_info["state"];
						                	$RtbBid->bid_request_geo["city"] = $geo_info["city"];

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
							                	if (\rtbbuyv22\RtbBuyV22Logger::get_instance()->setting_log === true):
								                	\rtbbuyv22\RtbBuyV22Logger::get_instance()->log[] = "Failed: " . "Check banner geography : City :: EXPECTED: " . strtolower($AdCampaignBannerRestrictions->GeoCity) . " GOT: " . $city;
								                	endif;
							                	continue;
						                	endif;

					                	endif;

				                	endif;

				               	endif;

		                	endif;

	                	endif;

                	endif;

                endif;
            	/*
            	 * PASSED ALL THE BUSINESS RULES, ADD TO THE RESULTS
            	 */
                $AdCampaignBannerFactory->incrementAdCampaignBannerBidsCounterCached($RtbBid->config, $RtbBid->rtb_seat_id, $AdCampaignBanner->AdCampaignBannerID);

                /*
                 * Adjust the bid rate according to the markup
                 */

                $mark_down = floatval($AdCampaignBanner->BidAmount) * floatval($markup_rate);
                $adusted_amount = floatval($AdCampaignBanner->BidAmount) - floatval($mark_down);
                $AdCampaignBanner->BidAmount = sprintf("%1.4f", $adusted_amount);

            	$AdCampaignBanner_Match_List[] = $AdCampaignBanner;

        	endforeach;

    	endforeach;

    	return $AdCampaignBanner_Match_List;

    }


}
