<?php
/**
 * NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2016 NginAd Foundation. All Rights Reserved
 * @license GPLv3
 */

namespace buyrtbfidelity\workflows\tasklets\common\insertionorderlineitem;

class CheckDomainExclusion {
	
	public static function execute(&$Logger, &$Workflow, \model\openrtb\RtbBidRequest &$RtbBidRequest, \model\openrtb\RtbBidRequestImp &$RtbBidRequestImp, &$InsertionOrderLineItem, &$InsertionOrderLineItemDomainExclusionFactory) {
	
		/*
		 * Check banner domain exclusions match
		 */
		
		$params = array();
		$params["InsertionOrderLineItemID"] = $InsertionOrderLineItem->InsertionOrderLineItemID;
		$InsertionOrderLineItemDomainExclusionList = $InsertionOrderLineItemDomainExclusionFactory->get_cached($Workflow->config, $params);
		
		foreach ($InsertionOrderLineItemDomainExclusionList as $InsertionOrderLineItemDomainExclusion):
			
			$domain_to_match = strtolower($InsertionOrderLineItemDomainExclusion->DomainName);
			
			if ($InsertionOrderLineItemDomainExclusion->ExclusionType == "url"):
				
			 	$page_url = $RtbBidRequest->RtbBidRequestSite->page != null ? $RtbBidRequest->RtbBidRequestSite->page : "";
				$domain = $RtbBidRequest->RtbBidRequestSite->domain != null ? $RtbBidRequest->RtbBidRequestSite->domain : "";
				
				if (strpos(strtolower($page_url), $domain_to_match) !== false
					|| strpos(strtolower($domain), $domain_to_match) !== false):
					
					if ($Logger->setting_log === true):
						$Logger->log[] = "Failed: " . "Check banner page url, site exclusions match :: EXPECTED: "
								 . $domain_to_match . " GOT: bid_request_site_page: "
								 . $page_url . ", bid_request_site_domain: " 
								 . $domain;
					endif;
					// goto next banner
					return false;
					
				endif;
				
			elseif ($InsertionOrderLineItemDomainExclusion->ExclusionType == "referrer"):

				$referrer = $RtbBidRequest->RtbBidRequestSite->ref != null ? $RtbBidRequest->RtbBidRequestSite->ref : "";

				if (strpos(strtolower($referrer), $domain_to_match) !== false):
				
					if ($Logger->setting_log === true):
						$Logger->log[] = "Failed: " . "Check banner page referrer url, site exclusions match :: EXPECTED: " . $domain_to_match . " GOT: " . $RtbBidRequest->RtbBidRequestSite->ref;
					endif;
					return false;
					
				endif;
				
			endif;
		
		endforeach;
		
		return true;
		
	}
	
}

