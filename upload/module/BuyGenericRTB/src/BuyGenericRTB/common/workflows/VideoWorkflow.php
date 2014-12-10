<?php
/**
 * CDNPAL NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2015 CDNPAL Ltd. All Rights Reserved
 * @license GPLv3
 */

namespace buyrtb\workflows;

class VideoWorkflow
{
	
	public function process_business_rules_workflow(&$Logger, &$ParentWorkflow, \model\openrtb\RtbBidRequest &$RtbBidRequest, &$RtbBidRequestImp, &$AdCampaignBanner, &$AdCampaignVideoRestrictionsFactory) {
    	
    	// Check video restrictions
    	if (\buyrtb\workflows\tasklets\video\adcampaignbanner\VideoRestrictionsWorkflow::execute($Logger, $ParentWorkflow, $RtbBidRequest, $RtbBidRequestImp, $AdCampaignBanner, $AdCampaignVideoRestrictionsFactory) === false):
    		return false;
    	endif;
    	
    	return true;
    }


}
