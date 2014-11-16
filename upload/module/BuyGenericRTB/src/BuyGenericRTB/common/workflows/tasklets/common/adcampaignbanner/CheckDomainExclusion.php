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
	
	public static function execute(&$Logger, &$Workflow, \model\openrtb\RtbBidRequest &$RtbBidRequest, \model\openrtb\RtbBidRequestImp &$RtbBidRequestImp, &$AdCampaignBanner, &$AdCampaignBannerDomainExclusionFactory) {
	
		/*
		 * Check banner domain exclusions match
		 */
		
		$params = array();
		$params["AdCampaignBannerID"] = $AdCampaignBanner->AdCampaignBannerID;
		$AdCampaignBannerDomainExclusionList = $AdCampaignBannerDomainExclusionFactory->get_cached($Workflow->config, $params);
		
		foreach ($AdCampaignBannerDomainExclusionList as $AdCampaignBannerDomainExclusion):
			
			$domain_to_match = strtolower($AdCampaignBannerDomainExclusion->DomainName);
			
			if ($AdCampaignBannerDomainExclusion->ExclusionType == "url"):
				
				if (strpos(strtolower($RtbBidRequest->RtbBidRequestSite->page), $domain_to_match) !== false
					|| strpos(strtolower($RtbBidRequest->RtbBidRequestSite->domain), $domain_to_match) !== false):
					
					if ($Logger->setting_log === true):
						$Logger->log[] = "Failed: " . "Check banner page url, site exclusions match :: EXPECTED: "
								 . $domain_to_match . " GOT: bid_request_site_page: "
								 . $RtbBidRequest->RtbBidRequestSite->page . ", bid_request_site_domain: " 
								 . $RtbBidRequest->RtbBidRequestSite->domain;
					endif;
					// goto next banner
					return false;
					
				endif;
				
			elseif ($RtbBidRequest->refurl && $AdCampaignBannerDomainExclusion->ExclusionType == "referrer"):
			
				if (strpos(strtolower($RtbBidRequest->refurl), $domain_to_match) !== false):
				
					if ($Logger->setting_log === true):
						$Logger->log[] = "Failed: " . "Check banner page referrer url, site exclusions match :: EXPECTED: " . $domain_to_match . " GOT: " . $RtbBidRequest->refurl;
					endif;
					return false;
					
				endif;
				
			endif;
		
		endforeach;
		
		return true;
		
	}
	
}

