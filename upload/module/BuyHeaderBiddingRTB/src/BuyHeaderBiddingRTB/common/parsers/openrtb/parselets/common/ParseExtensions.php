<?php
/**
 * NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2016 NginAd Foundation. All Rights Reserved
 * @license GPLv3
 */

namespace buyrtbheaderbidding\parsers\openrtb\parselets\common;

class ParseExtensions {
	
	public static function execute(&$Logger, \buyrtbheaderbidding\parsers\openrtb\OpenRTBParser &$Parser, \model\openrtb\RtbBidRequest &$RtbBidRequest, \model\openrtb\RtbBidRequestExtensions &$RtbBidRequestExtensions, &$rtb_extensions) {

		// Flag to mark Opera Mini traffic as such
		
		\util\ParseHelper::parse_item(
				$RtbBidRequestExtensions,
				$rtb_extensions,
				"operaminibrowser");
		
		// Flag to mark the coppa value, see http://business.ftc.gov/privacy-and-security/childrens-privacy
		
		\util\ParseHelper::parse_item(
				$RtbBidRequestExtensions,
				$rtb_extensions,
				"coppa");		
		
		// udi object
		
		if (isset($rtb_extensions["udi"])):
		
			$udi = $rtb_extensions["udi"];
			$RtbBidRequestExtensionsUdi = new \model\openrtb\RtbBidRequestExtensionsUdi(); 
			\buyrtbheaderbidding\parsers\openrtb\parselets\common\ParseUdi::execute($Logger, $Parser, $RtbBidRequest, $RtbBidRequestExtensionsUdi, $udi);
			$RtbBidRequestExtensions->RtbBidRequestExtensionsUdi = $RtbBidRequestExtensionsUdi;
				
		endif;
		
	}
	
}
