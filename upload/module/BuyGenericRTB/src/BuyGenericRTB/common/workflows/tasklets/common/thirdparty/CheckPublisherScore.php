<?php
/**
 * CDNPAL NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2015 CDNPAL Ltd. All Rights Reserved
 * @license GPLv3
 */

namespace buyrtb\workflows\tasklets\common\thirdparty;

class CheckPublisherScore {
	
	public static function execute(&$Logger, &$Workflow, \model\openrtb\RtbBidRequest &$RtbBidRequest) {
		/*
		 * This is a placeholder for a partner Site Scoring Tasklet
		*
		* IE. Integral Ad Science, Comscore ect...
		*/

		$page_to_check = "example.com";
		$traq_url = "http://api.adsafeprotected.com/db/client/1/absit?adsafe_url=" . urlencode($page_to_check);
		
		
		// TODO: implement APC caching
		
		//$response = file_get_contents($traq_url);
        //$integral_traq_obj = json_decode($response);
        	
        return true;
	}
	
}
