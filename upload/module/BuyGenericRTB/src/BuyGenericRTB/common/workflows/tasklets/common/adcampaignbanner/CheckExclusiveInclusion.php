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

	public static function execute(&$Logger, &$Workflow, \model\openrtb\RtbBidRequest &$RtbBidRequest, \model\openrtb\RtbBidRequestImp &$RtbBidRequestImp, &$AdCampaignBanner, &$AdCampaignBannerExclusiveInclusionFactory) {
		
		/*
		 * Check banner domain exclusive inclusions
		 * This will narrow the publisher pool down so we
		 * only working with the publishers that the client wants
		 * to advertise on.
		 */
		
		$params = array();
		$params["AdCampaignBannerID"] = $AdCampaignBanner->AdCampaignBannerID;
		$AdCampaignBannerExclusiveInclusionList = $AdCampaignBannerExclusiveInclusionFactory->get_cached($Workflow->config, $params);
		
		$result = true;
		
		foreach ($AdCampaignBannerExclusiveInclusionList as $AdCampaignBannerExclusiveInclusion):
			
			$domain_to_match = strtolower($AdCampaignBannerExclusiveInclusion->DomainName);
			
			if ($AdCampaignBannerExclusiveInclusion->InclusionType == "url"):
					
				$page_url = $RtbBidRequest->RtbBidRequestSite->page != null ? $RtbBidRequest->RtbBidRequestSite->page : "";
				$domain = $RtbBidRequest->RtbBidRequestSite->domain != null ? $RtbBidRequest->RtbBidRequestSite->domain : "";

				if (strpos(strtolower($page_url), $domain_to_match) === false
					&& strpos(strtolower($domain), $domain_to_match) === false):
				
					if ($Logger->setting_log === true):
						$Logger->log[] = "Failed: " . "Check banner page url, site exclusive inclusions do not match :: EXPECTED: " 
								. $domain_to_match . " GOT: bid_request_site_page: " 
								. $page_url . ", bid_request_site_domain: " 
								. $domain;
					endif;
					$result = false;
					continue;
					
				else:
				
					return true;
				
				endif;
			
			elseif ($AdCampaignBannerExclusiveInclusion->InclusionType == "referrer"):
			
				$referrer = $RtbBidRequest->RtbBidRequestSite->ref != null ? $RtbBidRequest->RtbBidRequestSite->ref : "";

				if (strpos(strtolower($referrer), $domain_to_match) === false):
				
					if ($Logger->setting_log === true):
						$Logger->log[] = "Failed: " . "Check banner page referrer url, site exclusive inclusions do not match :: EXPECTED: " . $domain_to_match . " GOT: " . $RtbBidRequest->refurl;
					endif;
					$result = false;
					continue;
					
				else:
					
					return true;
					
				endif;
			
			endif;
		
		endforeach;
		
		return $result;
	}
	
}

