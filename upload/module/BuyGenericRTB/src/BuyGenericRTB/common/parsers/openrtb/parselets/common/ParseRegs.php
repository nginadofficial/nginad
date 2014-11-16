<?php
/**
 * CDNPAL NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2015 CDNPAL Ltd. All Rights Reserved
 * @license GPLv3
 */

namespace buyrtb\parsers\openrtb\parselets\common;

class ParseRegs {
	
	public static function execute(&$Logger, \buyrtb\parsers\openrtb\OpenRTBParser &$Parser, \model\rtb\RtbBidRequest &$RtbBidRequest, \model\rtb\RtbBidRequestRegs &$RtbBidRequestRegs, &$ad_regs) {
	
		/*
		 * Opt Out Coppa?
		 */
		$this->bid_request_regs_coppa 		= $ad_regs;

	}
}
