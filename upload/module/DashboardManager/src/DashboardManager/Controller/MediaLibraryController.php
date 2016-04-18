<?php
/**
 * NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2016 NginAd Foundation. All Rights Reserved
 * @license GPLv3
 */
namespace DashboardManager\Controller;

use DashboardManager\ParentControllers\DemandAbstractActionController;
use Zend\View\Model\ViewModel;
use transformation;
use Zend\Mail\Message;
use Zend\Mime;

/**
 * @author Christopher Gu
 * This is the Media Library Controller class that controls the management
 * of media items used in IO Line Items
 */
class MediaLibraryController extends DemandAbstractActionController {
 
	protected $default_image_height			= 400;
	protected $default_image_width			= 400;
	
    /**
     * Will Show the dashboard index page.
     * (non-PHPdoc)
     * @see \Zend\Mvc\Controller\AbstractActionController::indexAction()
     */
	public function indexAction() {

		$initialized = $this->initialize();
		if ($initialized !== true) return $initialized;
		
		$ad_media_list = array();
		
		/*
		 * For right now just iterate over NativeAd items but eventually 
		 * we will iterate over display and video media library items 
		 * too which share the same interface: MediaLibraryItem
		 */
		
		$NativeAdResponseItemFactory = \_factory\NativeAdResponseItem::get_instance();
		$NativeAdResponseItemAssetFactory = \_factory\NativeAdResponseItemAsset::get_instance();
		
		$params = array();
	    if ($this->is_domain_admin):
	    	$params["UserID"] = $this->auth->getUserID();
	    else:
	    	$params["UserID"] = $this->auth->getEffectiveUserID();
		endif;
		$NativeAdResponseItemList = $NativeAdResponseItemFactory->get($params);
		
		foreach ($NativeAdResponseItemList as $NativeAdResponseItem):
			$params = array();
			$params["NativeAdResponseItemID"] 		= $NativeAdResponseItem->NativeAdResponseItemID;
			$params["AssetType"]					= 'data';
			$params["DataType"]						= DATA_ASSET_DESC;
			$NativeAdResponseItemAssetData 			= $NativeAdResponseItemAssetFactory->get_row($params);
			if ($NativeAdResponseItemAssetData !== null):
				$NativeAdResponseItem->Description 	= $NativeAdResponseItemAssetData->DataValue;
			else:
				$NativeAdResponseItem->Description 	= 'Not Available';
			endif;
			
			$ad_media_list[] = $NativeAdResponseItem;
		endforeach;

	    $view = new ViewModel(array(
	    		'is_super_admin' => $this->auth->isSuperAdmin($this->config_handle),
	    		'is_domain_admin' => $this->auth->isDomainAdmin($this->config_handle),
	    		'user_id_list' => $this->user_id_list_demand_customer,
	    		'effective_id' => $this->auth->getEffectiveIdentityID(),
	    		'dashboard_view' => 'asset-library',
	    		'user_identity' => $this->identity(),
	    		'true_user_name' => $this->auth->getUserName(),
				'is_super_admin' => $this->is_super_admin,
				'effective_id' => $this->auth->getEffectiveIdentityID(),
				'impersonate_id' => $this->ImpersonateID,
	    		'ad_media_list' => $ad_media_list,
	    		'header_title' => '<a href="/private-exchange-tools/media-library/createnativead">Create Native Ad</a>'

	    ));

	    return $view;
	}
	
	public function createnativeadAction() {
	
		$initialized = $this->initialize();
		
		$site_url = $this->config_handle['delivery']['site_url'];
		
	    $view = new ViewModel(array(
	    		'is_super_admin' => $this->auth->isSuperAdmin($this->config_handle),
	    		'is_domain_admin' => $this->auth->isDomainAdmin($this->config_handle),
	    		'user_id_list' => $this->user_id_list_demand_customer,
	    		'effective_id' => $this->auth->getEffectiveIdentityID(),
	    		'dashboard_view' => 'asset-library',
	    		'user_identity' => $this->identity(),
	    		'true_user_name' => $this->auth->getUserName(),
				'is_super_admin' => $this->is_super_admin,
				'effective_id' => $this->auth->getEffectiveIdentityID(),
				'impersonate_id' => $this->ImpersonateID,
	    		
	    		'protocols' => \util\BannerOptions::$protocols,
	    		'mimes' => \util\BannerOptions::$mimes,
	    		
	    		'site_url' => $site_url,
	    		'native_ad_data_types' => \util\NativeAdsHelper::getNativeAdDataTypes()

	    ));

	    return $view;
		
	}
	
	public function editnativeadAction() {
	
		$initialized = $this->initialize();
	
		$id = $this->getEvent()->getRouteMatch()->getParam('param1');
		if ($id == null):
			die("Invalid NativeAdResponseItemID");
		endif;
		
		$NativeAdResponseItemFactory = \_factory\NativeAdResponseItem::get_instance();
		$NativeAdResponseItemAssetFactory = \_factory\NativeAdResponseItemAsset::get_instance();
		
		$params = array();
		$params["NativeAdResponseItemID"] 		= $id;
		$NativeAdResponseItem		 			= $NativeAdResponseItemFactory->get_row($params);
		
		if ($NativeAdResponseItem == null):
			die("Invalid NativeAdResponseItemID");
		endif;
		
		$params = array();
		$params["NativeAdResponseItemID"] 		= $id;
		$NativeAdResponseItemAssetList		 	= $NativeAdResponseItemAssetFactory->get($params);
		
		foreach ($NativeAdResponseItemAssetList as $key => $NativeAdResponseItemAsset):
			
			$NativeAdResponseItemAssetList[$key]->VideoVastTag = rawurlencode($NativeAdResponseItemAssetList[$key]->VideoVastTag);
			
		endforeach;
			
		$current_native_ad_response_assets_json = json_encode($NativeAdResponseItemAssetList);
		
		$site_url = $this->config_handle['delivery']['site_url'];
	
		$view = new ViewModel(array(
				'is_super_admin' => $this->auth->isSuperAdmin($this->config_handle),
				'is_domain_admin' => $this->auth->isDomainAdmin($this->config_handle),
				'user_id_list' => $this->user_id_list_demand_customer,
				'effective_id' => $this->auth->getEffectiveIdentityID(),
				'dashboard_view' => 'asset-library',
				'user_identity' => $this->identity(),
				'true_user_name' => $this->auth->getUserName(),
				'is_super_admin' => $this->is_super_admin,
				'effective_id' => $this->auth->getEffectiveIdentityID(),
				'impersonate_id' => $this->ImpersonateID,
		   
				'protocols' => \util\BannerOptions::$protocols,
				'mimes' => \util\BannerOptions::$mimes,
		   
				'site_url' => $site_url,
				'ad_name' => $NativeAdResponseItem->AdName,
				'link_url' => $NativeAdResponseItem->LinkUrl,
				'ad_name' => $NativeAdResponseItem->AdName,
				'native_ad_response_item_id' => $NativeAdResponseItem->NativeAdResponseItemID,
				'native_ad_data_types' => \util\NativeAdsHelper::getNativeAdDataTypes(),
				
				'current_native_ad_response' => $NativeAdResponseItem,
				'current_native_ad_response_assets_json' => $current_native_ad_response_assets_json
				
		));
	
		return $view;
	
	}
	
	public function newnativeadAction() {

		$initialized = $this->initialize();
		
		$request 													= $this->getRequest();
			
		$post 														= $request->getPost();
		
		$native_media_type 											= $request->getPost("MediaType");
		
		if ($native_media_type != 'image' && $native_media_type != 'video'):
			die("Required Field: MediaType was missing");
		endif;
		
		$needed_input_image = array(
				'nativeadname',
				'data_title',
				'data_description',
				'data_sponsored',
				'imageurl',
				'landingpageurl'
		);
		
		$needed_input_video = array(
				'nativeadname',
				'data_title',
				'data_description',
				'data_sponsored',
				'video_vast_tag',
				'video_duration',
				'video_mimes'
		);
		
		if ($native_media_type == 'video'):
			$this->validateInput($needed_input_video);
		else:
			$this->validateInput($needed_input_image);
		endif;
		
		$native_ad_name 											= $request->getPost("nativeadname");
		
		$native_data_title 											= $request->getPost("data_title");

		$native_image_url 											= $request->getPost("imageurl");
		
		$native_landing_page_url 									= $request->getPost("landingpageurl");
		
		$native_video_vast_tag 										= $request->getPost("video_vast_tag");
		
		$native_video_duration 										= $request->getPost("video_duration");
		
		$native_video_mimes 										= $request->getPost("video_mimes");
		
		$native_video_protocols 									= $request->getPost("video_protocols");
		
		$native_data_description 									= $request->getPost("data_description");
		
		$native_data_sponsored 										= $request->getPost("data_sponsored");
		
		$native_ad_response_item_id									= $request->getPost("nativeadresponseitemid");
		
		/*
		 * _NEW_ for new native data
		*/
		$key_type = '_NEW_';
			
		// returns an array of new native data ids
		$new_keys = \util\NativeAdsHelper::getNativeDataKeys($post, $key_type);

		// returns an associative array of new native data
		$native_data_new_list = \util\NativeAdsHelper::getNativeData($request, $key_type, $new_keys);
		
		if ($this->is_domain_admin):
			$user_id = $this->auth->getUserID();
		else:
			$user_id = $this->auth->getEffectiveUserID();
		endif;
		
		$NativeAdResponseItemFactory 							= new \_factory\NativeAdResponseItem();
		$NativeAdResponseItemAssetFactory 						= new \_factory\NativeAdResponseItemAsset();
		
		$params = array();
		$params["UserID"] 										= $user_id;
		$params["NativeAdResponseItemID"] 						= $native_ad_response_item_id;
		$_NativeAdResponseItem	 								= $NativeAdResponseItemFactory->get_row($params);
		
		$NativeAdResponseItem 									= new \model\NativeAdResponseItem();
		
		if ($_NativeAdResponseItem == null):
			$NativeAdResponseItem->DateCreated						= date("Y-m-d H:i:s");
			$NativeAdResponseItem->UserID							= $user_id;
		else: 

			$NativeAdResponseItemAssetFactory->delete_assets($native_ad_response_item_id);
		
			$NativeAdResponseItem->NativeAdResponseItemID			= $_NativeAdResponseItem->NativeAdResponseItemID;
			$NativeAdResponseItem->UserID							= $_NativeAdResponseItem->UserID;
			$NativeAdResponseItem->AdName							= $_NativeAdResponseItem->AdName;
			$NativeAdResponseItem->MediaType						= $_NativeAdResponseItem->MediaType;
			$NativeAdResponseItem->LinkUrl							= $_NativeAdResponseItem->LinkUrl;
			$NativeAdResponseItem->TrackerUrlsCommaSeparated		= $_NativeAdResponseItem->TrackerUrlsCommaSeparated;
			$NativeAdResponseItem->JsLinkTracker					= $_NativeAdResponseItem->JsLinkTracker;
			$NativeAdResponseItem->ImageHeight						= $_NativeAdResponseItem->ImageHeight;
			$NativeAdResponseItem->ImageWidth						= $_NativeAdResponseItem->ImageWidth;
			$NativeAdResponseItem->DateCreated						= $_NativeAdResponseItem->DateCreated;
			$NativeAdResponseItem->DateUpdated						= $_NativeAdResponseItem->DateUpdated;
			
		endif;
					
		$NativeAdResponseItem->AdName							= $native_ad_name;
		$NativeAdResponseItem->LinkUrl							= $native_landing_page_url;
		$NativeAdResponseItem->MediaType						= $native_media_type;

		$native_ad_response_item_id 							= $NativeAdResponseItemFactory->saveNativeAdResponseItem($NativeAdResponseItem);
		
		$NativeAdResponseItemAsset 								= new \model\NativeAdResponseItemAsset();
		$NativeAdResponseItemAsset->NativeAdResponseItemID 		= $native_ad_response_item_id;
		$NativeAdResponseItemAsset->AssetRequired				= 1;
		$NativeAdResponseItemAsset->DateCreated					= date("Y-m-d H:i:s");
		
		if ($native_media_type == 'video'):

			if ($native_video_mimes && is_array($native_video_mimes) && count($native_video_mimes) > 0):
				$native_video_mimes = join(',', $native_video_mimes);
			endif;

			if ($native_video_protocols && is_array($native_video_protocols) && count($native_video_protocols) > 0):
				$native_video_protocols = join(',', $native_video_protocols);
			endif;
			
			$NativeAdResponseItemAsset->AssetType 						= 'video';
			$NativeAdResponseItemAsset->VideoVastTag 					= $native_video_vast_tag;
			$NativeAdResponseItemAsset->VideoDuration 					= $native_video_duration;
			$NativeAdResponseItemAsset->VideoMimesCommaSeparated 		= $native_video_mimes;
			$NativeAdResponseItemAsset->VideoProtocolsCommaSeparated 	= $native_video_protocols;
		else:
			$NativeAdResponseItemAsset->AssetType 						= 'image';
			$NativeAdResponseItemAsset->ImageUrl 						= $native_image_url;
			
			try {
				list($width, $height) 									= getimagesize($native_image_url);
				$NativeAdResponseItemAsset->ImageWidth 					= $width;
				$NativeAdResponseItemAsset->ImageHeight 				= $height;
				$NativeAdResponseItem->ImageWidth 						= $width;
				$NativeAdResponseItem->ImageHeight						= $height;
				$NativeAdResponseItem->NativeAdResponseItemID 			= $native_ad_response_item_id;
				$NativeAdResponseItemFactory->saveNativeAdResponseItem($NativeAdResponseItem);
			} catch (Exception $e) {
				
			}
			
		endif;
		
		$NativeAdResponseItemAssetFactory->saveNativeAdResponseItemAsset($NativeAdResponseItemAsset);

		$NativeAdResponseItemAsset 								= new \model\NativeAdResponseItemAsset();
		$NativeAdResponseItemAsset->NativeAdResponseItemID 		= $native_ad_response_item_id;
		$NativeAdResponseItemAsset->AssetRequired				= 1;
		$NativeAdResponseItemAsset->AssetType 					= 'title';
		$NativeAdResponseItemAsset->TitleText 					= $native_data_title;
		$NativeAdResponseItemAsset->DateCreated					= date("Y-m-d H:i:s");
		
		$NativeAdResponseItemAssetFactory->saveNativeAdResponseItemAsset($NativeAdResponseItemAsset);
		
		$native_ad_data_types 		= \util\NativeAdsHelper::getNativeAdDataTypes();
		
		$data_asset = array();
		$data_asset['DataRequired'] = 0;
		$data_asset['DataType']		= DATA_ASSET_SPONSORED;
		$data_asset['DataLabel']	= $native_ad_data_types[(string)DATA_ASSET_SPONSORED];
		$data_asset['DataValue']	= $native_data_sponsored;
		$native_data_new_list[]		= $data_asset;
		
		$data_asset = array();
		$data_asset['DataRequired'] = 0;
		$data_asset['DataType']		= DATA_ASSET_DESC;
		$data_asset['DataLabel']	= $native_ad_data_types[(string)DATA_ASSET_DESC];
		$data_asset['DataValue']	= $native_data_description;
		$native_data_new_list[]		= $data_asset;
		
		if ($native_ad_response_item_id):
				
			foreach ($native_data_new_list as $native_data):
				$NativeAdResponseItemAsset = new \model\NativeAdResponseItemAsset();
				$NativeAdResponseItemAsset->NativeAdResponseItemID = $native_ad_response_item_id;
				$NativeAdResponseItemAsset->AssetType = 'data';
					
				if (isset($native_data["DataRequired"])):
					$NativeAdResponseItemAsset->AssetRequired						= $native_data["DataRequired"];
				endif;
				if (isset($native_data["TitleText"])):
					$NativeAdResponseItemAsset->TitleText							= $native_data["TitleText"];
				endif;
				if (isset($native_data["ImageUrl"])):
					$NativeAdResponseItemAsset->ImageUrl							= $native_data["ImageUrl"];
				endif;
				if (isset($native_data["ImageWidth"])):
					$NativeAdResponseItemAsset->ImageWidth							= $native_data["ImageWidth"];
				endif;
				if (isset($native_data["ImageHeight"])):
					$NativeAdResponseItemAsset->ImageHeight							= $native_data["ImageHeight"];
				endif;
				if (isset($native_data["VideoVastTag"])):
					$NativeAdResponseItemAsset->VideoVastTag						= $native_data["VideoVastTag"];
				endif;
				if (isset($native_data["VideoDuration"])):
					$NativeAdResponseItemAsset->VideoDuration						= $native_data["VideoDuration"];
				endif;
				if (isset($native_data["VideoMimesCommaSeparated"])):
					$NativeAdResponseItemAsset->VideoMimesCommaSeparated			= $native_data["VideoMimesCommaSeparated"];
				endif;
				if (isset($native_data["VideoProtocolsCommaSeparated"])):
					$NativeAdResponseItemAsset->VideoProtocolsCommaSeparated		= $native_data["VideoProtocolsCommaSeparated"];
				endif;
				if (isset($native_data["DataType"])):
					$NativeAdResponseItemAsset->DataType							= $native_data["DataType"];
				endif;
				if (isset($native_data["DataLabel"])):
					$NativeAdResponseItemAsset->DataLabel							= $native_data["DataLabel"];
				endif;
				if (isset($native_data["DataValue"])):
					$NativeAdResponseItemAsset->DataValue							= $native_data["DataValue"];
				endif;
				if (isset($native_data["LinkUrl"])):
					$NativeAdResponseItemAsset->LinkUrl								= $native_data["LinkUrl"];
				endif;
				if (isset($native_data["LinkClickTrackerUrlsCommaSeparated"])):
					$NativeAdResponseItemAsset->LinkClickTrackerUrlsCommaSeparated 	= $native_data["LinkClickTrackerUrlsCommaSeparated"];
				endif;
				if (isset($native_data["LinkFallback"])):
					$NativeAdResponseItemAsset->LinkFallback						= $native_data["LinkFallback"];
				endif;
					
				$NativeAdResponseItemAsset->DateCreated								= date("Y-m-d H:i:s");
					
				$NativeAdResponseItemAssetFactory->saveNativeAdResponseItemAsset($NativeAdResponseItemAsset);
					
			endforeach;
			
		endif;
			
		$refresh_url = "/private-exchange-tools/media-library/?filter=nativeads";
		$viewModel = new ViewModel(array('refresh_url' => $refresh_url));
		
		return $viewModel->setTemplate('dashboard-manager/demand/interstitial.phtml');
		
	}

}
