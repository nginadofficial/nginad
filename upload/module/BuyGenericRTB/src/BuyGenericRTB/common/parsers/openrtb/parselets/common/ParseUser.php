<?php
/**
 * CDNPAL NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2015 CDNPAL Ltd. All Rights Reserved
 * @license GPLv3
 */

namespace buyrtb\parsers\openrtb\parselets\common;

class ParseUser {
	
	public static function execute(&$Logger, \buyrtb\parsers\openrtb\OpenRTBParser &$Parser, \model\openrtb\RtbBidRequest &$RtbBidRequest, \model\openrtb\RtbBidRequestUser &$RtbBidRequestUser, &$rtb_user) {
	
		// User ID
		
		\util\ParseHelper::parse_item(
				$RtbBidRequestUser,
				$rtb_user,
				"id");

		// BuyerID, alternative to User ID
		
		\util\ParseHelper::parse_item(
				$RtbBidRequestUser,
				$rtb_user,
				"buyeruid");
		
		// Year of Birth
		
		\util\ParseHelper::parse_item(
				$RtbBidRequestUser,
				$rtb_user,
				"yob");		
		
		// User Keywords Meta
		/*
		 * Apparently Neal and Jim not sure on this one,
		* saying it could be a string or an array of strings
		*/
		
		if (isset($rtb_user["keywords"])):
		
			if (is_array($rtb_user["keywords"])):
				 
				\util\ParseHelper::parse_item_list(
						$RtbBidRequestUser,
						$rtb_user,
						"keywords");
			else:
				 
				\util\ParseHelper::parse_item(
						$RtbBidRequestUser,
						$rtb_user,
						"keywords");
				 
			endif;
			
		endif;
		
		// Custom Data
		
		\util\ParseHelper::parse_item(
				$RtbBidRequestUser,
				$rtb_user,
				"customdata");
		
		// geo object
		
		if (isset($rtb_user["geo"])):
		
			$geo = $rtb_user["geo"];
			$RtbBidRequestGeo = new \model\openrtb\RtbBidRequestGeo();
			\buyrtb\parsers\openrtb\parselets\common\ParseGeo::execute($Logger, $Parser, $RtbBidRequest, $RtbBidRequestGeo, $geo);
			$RtbBidRequestUser->RtbBidRequestGeo = $RtbBidRequestGeo;
				
		endif;
		
		// user data objects
		
		if (isset($rtb_user["data"]) && is_array($rtb_user["data"])):
		
			$user_data_list = $rtb_user["data"];
			
			foreach ($user_data_list as $user_data):
			
				$RtbBidRequestData = new \model\openrtb\RtbBidRequestData();
				\buyrtb\parsers\openrtb\parselets\common\ParseData::execute($Logger, $Parser, $RtbBidRequest, $RtbBidRequestData, $user_data);
				$RtbBidRequestUser->RtbBidRequestDataList[] = $RtbBidRequestData;
				
			endforeach;
		
		endif;
		
	}
	
}
