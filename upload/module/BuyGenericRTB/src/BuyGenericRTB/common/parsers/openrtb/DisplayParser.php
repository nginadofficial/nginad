<?php
/**
 * CDNPAL NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2015 CDNPAL Ltd. All Rights Reserved
 * @license GPLv3
 */

namespace buyrtb\parsers\openrtb;
use \Exception;

class DisplayParser {

	public function parse_request(&$Logger, \buyrtb\parsers\openrtb\OpenRTBParser &$Parser, \model\openrtb\RtbBidRequestBanner &$RtbBidRequestBanner, &$ad_impression_banner) {

	        // Parse Dimensions
	        try {
	        	\buyrtb\parsers\openrtb\parselets\common\banner\ParseDimensions::execute($Logger, $Parser, $RtbBidRequestBanner, $ad_impression_banner);
	        } catch (Exception $e) {
	        	throw new Exception($e->getMessage(), $e->getCode(), $e->getPrevious());
	        }
	        
	        // Banner ID
	         
	        $Parser->parse_item(
	        		$RtbBidRequestBanner,
	        		$ad_impression_banner,
	        		"id");
	        
	        // Parse Above the Fold
	        try {
	        	\buyrtb\parsers\openrtb\parselets\common\banner\ParseAboveTheFold::execute($Logger, $Parser, $RtbBidRequestBanner, $ad_impression_banner);
	        } catch (Exception $e) {
	        	throw new Exception($e->getMessage(), $e->getCode(), $e->getPrevious());
	        }
	        
	        // Blocked Creative Types
	        
	        $Parser->parse_item_list(
	        		$RtbBidRequestBanner,
	        		$ad_impression_banner,
	        		"btype");

	        // Blocked Creative Attributes
	         
	        $Parser->parse_item_list(
	        		$RtbBidRequestBanner,
	        		$ad_impression_banner,
	        		"battr");
	        
	        // Supported Mime Types
	        
	        $Parser->parse_item_list(
	        		$RtbBidRequestBanner,
	        		$ad_impression_banner,
	        		"mimes");
	        
	        // Is In Top Most DOM Document ( not in IFRAME )
	         
	        $Parser->parse_item(
	        		$RtbBidRequestBanner,
	        		$ad_impression_banner,
	        		"topframe");
	        
	        // Expandable Ad Properties ( IAB Rising Stars )
	         
	        $Parser->parse_item_list(
	        		$RtbBidRequestBanner,
	        		$ad_impression_banner,
	        		"expdir");
	        
	        // Supported API Frameworks
	        
	        $Parser->parse_item_list(
	        		$RtbBidRequestBanner,
	        		$ad_impression_banner,
	        		"api");
	}
}
