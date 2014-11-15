<?php
/**
 * CDNPAL NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2015 CDNPAL Ltd. All Rights Reserved
 * @license GPLv3
 */

namespace buyrtb\workflows\tasklets\common\adcampaignbanner;

class CheckExclusiveInclusion {

	public static function execute(&$Logger, &$Workflow, &$RtbBid, &$AdCampaignBanner, &$AdCampaignBannerExclusiveInclusionFactory) {
		
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
				
					if ($Logger->setting_log === true):
						$Logger->log[] = "Failed: " . "Check banner page url, site exclusive inclusions do not match :: EXPECTED: " . $domain_to_match . " GOT: bid_request_site_page: " . $RtbBid->bid_request_site_page . ", bid_request_site_domain: " . $RtbBid->bid_request_site_domain;
					endif;
					// goto next banner
					return false;
					
				endif;
			
			elseif ($RtbBid->bid_request_refurl && $AdCampaignBannerExclusiveInclusion->InclusionType == "referrer"):
			
				if (strpos(strtolower($RtbBid->bid_request_refurl), $domain_to_match) === false):
				
					if ($Logger->setting_log === true):
						$Logger->log[] = "Failed: " . "Check banner page referrer url, site exclusive inclusions do not match :: EXPECTED: " . $domain_to_match . " GOT: " . $RtbBid->bid_request_refurl;
					endif;
					return false;
					
				endif;
			
			endif;
		
		endforeach;
		
		return true;
	}
	
}

