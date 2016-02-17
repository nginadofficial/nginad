<?php
/**
 * NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2016 NginAd Foundation. All Rights Reserved
 * @license GPLv3
 */

namespace buyrtbheaderbidding\parsers\openrtb\parselets\common;

class ParseRegs {
	
	public static function execute(&$Logger, \buyrtbheaderbidding\parsers\openrtb\OpenRTBParser &$Parser, \model\openrtb\RtbBidRequest &$RtbBidRequest, \model\openrtb\RtbBidRequestRegulations &$RtbBidRequestRegulations, &$ad_regs) {
	
		// Opt Out Coppa?
		
		\util\ParseHelper::parse_item(
				$RtbBidRequestRegulations,
				$ad_regs,
				"coppa");

	}
}
