<?php
/**
 * NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2016 NginAd Foundation. All Rights Reserved
 * @license GPLv3
 */

namespace util;

class NativeAdsHelper {
	
	public static function getNativeAdDataTypes() {
		
		return array(
			(string)DATA_ASSET_SPONSORED	=> "Sponsored",
			(string)DATA_ASSET_DESC			=> "Description",
			(string)DATA_ASSET_RATING		=> "Rating",
			(string)DATA_ASSET_LIKES		=> "Likes",
			(string)DATA_ASSET_DOWNLOADS	=> "Downloads",
			(string)DATA_ASSET_PRICE		=> "Price",
			(string)DATA_ASSET_SALEPRICE	=> "Sales Price",
			(string)DATA_ASSET_PHONE		=> "Phone",
			(string)DATA_ASSET_ADDRESS		=> "Address",
			(string)DATA_ASSET_DESC2		=> "Description 2",
			(string)DATA_ASSET_DISPLAY_URL	=> "Display URL",
			(string)DATA_ASSET_CTATEXT		=> "CTA Description"
		);
	}
	
	public static function getNativeData($request, $key_name, $key_list) {
	
		$native_data_assets = array();

		$post = $request->getPost();
	
		foreach ($key_list as $id):
		
			$data_asset = array();

			$data_required 								= $request->getPost('DataRequired' . $key_name . $id);
			$data_asset['DataRequired'] 				= $data_required == 1 ? 1 : 0;
		
			$data_type 									= $request->getPost('DataType' . $key_name . $id);
			
			if (!empty($data_type)):
				$data_asset['DataType'] 				= intval($data_type);
			endif;
				
			$data_label 								= $request->getPost('DataLabel' . $key_name . $id);
			if (!empty($data_label)):
				$data_asset['DataLabel'] 					= trim($data_label);
			endif;
		
			$data_value 								= $request->getPost('DataValue' . $key_name . $id);
			
			if (!empty($data_value)):
				
				$data_asset['DataValue']						= trim($data_value);
			
				/*
				 * 7.6 Data Asset Types:
				 * http://www.iab.com/wp-content/uploads/2016/03/OpenRTB-Native-Ads-Specification-1-1_2016.pdf
				 *
				 * Hard Cast Integer Types: [ number formatted as string ]
				 */
					
				if ($data_asset['DataType'] == DATA_ASSET_RATING
					|| $data_asset['DataType'] == DATA_ASSET_LIKES
					|| $data_asset['DataType'] == DATA_ASSET_DOWNLOADS
					|| $data_asset['DataType'] == DATA_ASSET_PRICE
					|| $data_asset['DataType'] == DATA_ASSET_SALEPRICE):
			
					$data_asset['DataValue'] = (string)intval($data_asset['DataValue']);
						
				endif;
			
			endif;
	
			$native_data_assets[] 	= $data_asset;
		
		endforeach;

		return $native_data_assets;
	}

	public static function getNativeDataKeys($post, $key_name) {
	
		$all_keys = array();
	
		foreach ($post as $key => $value):
		
			$pos = strpos($key, $key_name);
		
			if ($pos === false):
				continue;
			endif;
		
			$id = substr($key, $pos + strlen($key_name));
		
			$all_keys[$id] = true;
		
		endforeach;
	
		return array_keys($all_keys);
	
	}
	
	public static function getPostDataByKey($post, $key_name) {
	
		$all_keys = array();
	
		foreach ($post as $key => $value):
		
			$pos = strpos($key, $key_name);
		
			if ($pos !== 0):
				continue;
			endif;
		
			$id = substr($key, strlen($key_name));
		
			$value = trim($value);
				
			if (!empty($value)):
				
				$all_keys[$id] = $value;
		
			endif;
				
		endforeach;
	
		return $all_keys;
	
	}
	
}