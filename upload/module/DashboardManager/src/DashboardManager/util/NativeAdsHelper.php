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
	
	public static function getNativeAdsSelectedOptionsList($native_ad_response_items, $insertion_order_line_item_id) {
		
		$native_ad_response_item_select_option_list = array_keys($native_ad_response_items);
		
		$InsertionOrderLineItemToNativeAdFactory = \_factory\InsertionOrderLineItemToNativeAd::get_instance();
		
		$params = array();
		$params['NativeAdResponseItemID'] = $insertion_order_line_item_id;
		
		$InsertionOrderLineItemToNativeAdList = $InsertionOrderLineItemToNativeAdFactory->get($params);
		
		$native_ad_response_item_selected_option_list = array();
		
		foreach ($InsertionOrderLineItemToNativeAdList as $InsertionOrderLineItemToNativeAd):
			
			if (in_array($InsertionOrderLineItemToNativeAd->NativeAdResponseItemID, $native_ad_response_item_select_option_list)):
				$native_ad_response_item_selected_option_list[] = $InsertionOrderLineItemToNativeAd->NativeAdResponseItemID;
			endif;
			
		endforeach;
		
		return $native_ad_response_item_selected_option_list;
		
	}
	
	public static function getNativeAdsSelectedOptionsPreviewList($native_ad_response_items, $insertion_order_line_item_preview_id) {
		
		$native_ad_response_item_select_option_list = array_keys($native_ad_response_items);
		
		$InsertionOrderLineItemPreviewToNativeAdFactory = \_factory\InsertionOrderLineItemPreviewToNativeAd::get_instance();

		$params = array();
		$params['NativeAdResponseItemPreviewID'] = $insertion_order_line_item_preview_id;
		
		$InsertionOrderLineItemPreviewToNativeAdList = $InsertionOrderLineItemPreviewToNativeAdFactory->get($params);
		
		$native_ad_response_item_selected_option_list = array();
		
		foreach ($InsertionOrderLineItemPreviewToNativeAdList as $InsertionOrderLineItemPreviewToNativeAd):
				
			if (in_array($InsertionOrderLineItemPreviewToNativeAd->NativeAdResponseItemID, $native_ad_response_item_select_option_list)):
				$native_ad_response_item_selected_option_list[] = $InsertionOrderLineItemPreviewToNativeAd->NativeAdResponseItemID;
			endif;
				
		endforeach;
		
		return $native_ad_response_item_selected_option_list;
	}
	
	public static function getNativeAdsSelectOptionsList($user_id) {
		
			$NativeAdResponseItemFactory = \_factory\NativeAdResponseItem::get_instance();
			$NativeAdResponseItemAssetFactory = \_factory\NativeAdResponseItemAsset::get_instance();
			
			$params = array();
			$params["UserID"] = $user_id;

			$native_ad_response_item_select_option_list = array();
			
			$NativeAdResponseItemList = $NativeAdResponseItemFactory->get($params);
			
			foreach ($NativeAdResponseItemList as $NativeAdResponseItem):
			
				$option_description = $NativeAdResponseItem->AdName;
			
				$params = array();
				$params["NativeAdResponseItemID"] 		= $NativeAdResponseItem->NativeAdResponseItemID;
				$params["AssetType"]					= 'title';
				$NativeAdResponseItemAssetData 			= $NativeAdResponseItemAssetFactory->get_row($params);
				if ($NativeAdResponseItemAssetData !== null):
					$option_description.= " - " . $NativeAdResponseItemAssetData->TitleText;
				endif;
				
				$native_ad_response_item_select_option_list[$NativeAdResponseItem->NativeAdResponseItemID] = $option_description;

			endforeach;
			
			return $native_ad_response_item_select_option_list;
	}
	
}