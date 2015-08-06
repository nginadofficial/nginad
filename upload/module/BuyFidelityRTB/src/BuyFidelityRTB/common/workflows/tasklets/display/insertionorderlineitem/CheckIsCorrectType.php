<?php
/**
 * NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2016 NginAd Foundation. All Rights Reserved
 * @license GPLv3
 */

namespace buyrtbfidelity\workflows\tasklets\display\insertionorderlineitem;

class CheckIsCorrectType {
	
	public static function execute(&$Logger, &$Workflow, \model\openrtb\RtbBidRequest &$RtbBidRequest, \model\openrtb\RtbBidRequestImp &$RtbBidRequestImp, &$InsertionOrderLineItem) {
		
		/*
		 * Check is mobile web, phone, tablet, native iOS or native Android
		 * 
		 *  define('BANNER_TYPE_XHTML_TEXT_AD', 		1);
			define('BANNER_TYPE_XHTML_BANNER_AD', 		2);
			define('BANNER_TYPE_JAVASCRIPT_AD', 		3);
			define('BANNER_TYPE_IFRAME_AD', 			4);
		 * 
		 */
		
		/*
		 * The ad returned must correspond with the requested ad type. 
		 * E.g. if the request only allows an image banner, you may not return a richmedia ad, etc., 
		 * see the OpenRTB specs regarding the attributes Banner.btype and Banner.mimes.
		 * 
		 * Currently NginAd Only supports Javascript tags
		 */
		
		if (empty($RtbBidRequestImp->RtbBidRequestBanner->btype)
			|| !is_array($RtbBidRequestImp->RtbBidRequestBanner->btype)):
			return true;
		elseif (in_array(BANNER_TYPE_JAVASCRIPT_AD, $RtbBidRequestImp->RtbBidRequestBanner->btype)
				|| in_array(BANNER_TYPE_XHTML_BANNER_AD, $RtbBidRequestImp->RtbBidRequestBanner->btype)
		):
		
			if (empty($RtbBidRequestImp->RtbBidRequestBanner->mimes)
				|| !is_array($RtbBidRequestImp->RtbBidRequestBanner->mimes)):
				return true;
			
			elseif (!empty($RtbBidRequestImp->RtbBidRequestBanner->mimes)
				&& is_array($RtbBidRequestImp->RtbBidRequestBanner->mimes)):
				
				if ($InsertionOrderLineItem->ImpressionType == 'image'):
					// image ad
					$adtag = $InsertionOrderLineItem->AdTag;
					
					preg_match('/< *img[^>]*src *= *["\']?([^"\']*)/i', $adtag, $matches);
					
					if (!isset($matches[1])):
						return false;
					endif;
					
					$imgUrl = $matches[1];
					
					$filename = parse_url($imgUrl, PHP_URL_PATH);

					$mime_type = \util\MimeHelper::get_mime_type($filename);
					
					return in_array($mime_type, $RtbBidRequestImp->RtbBidRequestBanner->mimes);
					
				else:
					// banner ad tag markup, look for JS mime type
					
					if (!in_array('application/javascript', $RtbBidRequestImp->RtbBidRequestBanner->mimes)):

						return false;
					
					endif;
					
				endif;
				
			endif;
				
			return true;
		else:
			return false;
		endif;
	}

}

