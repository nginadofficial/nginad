<?php
/**
 * CDNPAL NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2015 CDNPAL Ltd. All Rights Reserved
 * @license GPLv3
 */

namespace buyrtb\parsers\openrtb\parselets\common\banner;
use \Exception;

class ParseAboveTheFold {
	
	public static function execute(&$Logger, \buyrtb\parsers\openrtb\OpenRTBParser &$Parser, \model\openrtb\RtbBidRequestBanner &$RtbBidRequestBanner, &$ad_impression_banner) {
		
		/*
		 * OpenRTB 2.1
		 *
		0 Unknown
		1 Above the fold
		2 DEPRECATED - May or may not be immediately visible depending on
		screen size and resolution.
		3 Below the fold
		4 Header
		5 Footer
		6 Sidebar
		7 Fullscreen
		*/
		
		/*
		 * Proprietary DB values
		*
		uFoldPos No No User defined Fold position of the ad slot. •
		0 – Not available/applicable •
		1 – Completely Above the Fold •
		2 – Completely Below the Fold  •
		3 – Partially Above the Fold
		 
		sFoldPos No No System detected Fold position of the ad slot. •
		0 – Not available/applicable •
		1 – Completely Above the Fold •
		2 – Completely Below the Fold  •
		3 – Partially Above the Fold
		*/
		 
		/*
		 * do conversion here
		 */
		 
		if (isset($ad_impression_banner["pos"])):
			 
			if ($ad_impression_banner["pos"] == 1
				|| $ad_impression_banner["pos"] == 4):
			 
				$RtbBidRequestBanner->pos 	= 1;
			
			elseif ($ad_impression_banner["pos"] == 6):
			
				$RtbBidRequestBanner->pos 	= 3;
			
			elseif ($ad_impression_banner["pos"] == 3
				|| $ad_impression_banner["pos"] == 5):
					 
				$RtbBidRequestBanner->pos 	= 2;
			else:
				$RtbBidRequestBanner->pos 	= 0;
			endif;
			
		else:
			throw new Exception($Parser->expeption_missing_min_bid_request_params . ": imp_banner_pos");
		endif;
	}
}

