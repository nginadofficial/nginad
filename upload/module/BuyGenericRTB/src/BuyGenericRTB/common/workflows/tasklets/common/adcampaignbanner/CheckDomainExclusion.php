<?php
/**
 * CDNPAL NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2015 CDNPAL Ltd. All Rights Reserved
 * @license GPLv3
 */

namespace buyrtb\workflows\tasklets\common\adcampaignbanner;

class CheckDomainExclusion {
	
	public static function execute(&$Logger, &$Workflow, &$RtbBid, &$AdCampaignBanner, &$AdCampaignBannerDomainExclusionFactory) {
	
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
					// goto next banner
					return false;
					
				endif;
				
			elseif ($RtbBid->bid_request_refurl && $AdCampaignBannerDomainExclusion->ExclusionType == "referrer"):
			
				if (strpos(strtolower($RtbBid->bid_request_refurl), $domain_to_match) !== false):
				
					if (\rtbbuyv22\RtbBuyV22Logger::get_instance()->setting_log === true):
						\rtbbuyv22\RtbBuyV22Logger::get_instance()->log[] = "Failed: " . "Check banner page referrer url, site exclusions match :: EXPECTED: " . $domain_to_match . " GOT: " . $RtbBid->bid_request_refurl;
					endif;
					return false;
					
				endif;
				
			endif;
		
		endforeach;
		
		return true;
		
	}
	
}

