<?php
/**
 * CDNPAL NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2015 CDNPAL Ltd. All Rights Reserved
 * @license GPLv3
 */

namespace buyrtb\parsers\openrtb\parselets\common;

class ParseData {
	
	public static function execute(&$Logger, \buyrtb\parsers\openrtb\OpenRTBParser &$Parser, \model\openrtb\RtbBidRequest &$RtbBidRequest, \model\openrtb\RtbBidRequestData &$RtbBidRequestData, &$data) {
	
		// data id
	
		\util\ParseHelper::parse_item(
				$RtbBidRequestData,
				$data,
				"id");
		
		// data name
		
		\util\ParseHelper::parse_item(
				$RtbBidRequestData,
				$data,
				"name");
		
		// data segment objects
		
		if (isset($data["segment"]) && is_array($data["segment"])):
		
			$data_segment_list = $data["segment"];
				
			foreach ($data_segment_list as $data_segment):
					
				$RtbBidRequestSegment = new \model\openrtb\RtbBidRequestSegment();
				\buyrtb\parsers\openrtb\parselets\common\ParseSegment::execute($Logger, $Parser, $RtbBidRequest, $RtbBidRequestSegment, $data_segment);
				$RtbBidRequestData->RtbBidRequestSegmentList[] = $RtbBidRequestSegment;
				
			endforeach;
			
		endif;
		
	}
	
}
