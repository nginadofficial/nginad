<?php
/**
 * NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2016 NginAd Foundation. All Rights Reserved
 * @license GPLv3
 */

namespace transformation;

use Zend\Mail\Message;
use Zend\Mime;

/*
 * Static class to transform InsertionOrderLineItem and InsertionOrder and dependent objects
 * to and from their preview form
 */

class TransformPreview {

	public static function previewCheckBannerID($banner_id, $auth, $config, $mail_transport, $update_data = array()) {

		/*
		 * SHOULD WE CREATE A NEW PREVIEW MODE?
		*/
		if (self::doesPreviewBannerExistForBanner($banner_id, $auth) == false):

			$InsertionOrderLineItemFactory = \_factory\InsertionOrderLineItem::get_instance();
			$params = array();
			$params["InsertionOrderLineItemID"] = $banner_id;
			$params["Active"] = 1;
			if (!$auth->isSuperAdmin($config)):
				$params["UserID"] = $auth->getUserID();
			endif;
			$InsertionOrderLineItem = $InsertionOrderLineItemFactory->get_row($params);

			if ($InsertionOrderLineItem == null):
				//die("No Such Banner");
				$params["error"] = "No Such Banner";
				return $params;
			endif;

			return self::cloneInsertionOrderIntoInsertionOrderPreview($InsertionOrderLineItem->InsertionOrderID, $auth, $config, $mail_transport, $update_data);

		else:

			$InsertionOrderLineItemPreviewFactory = \_factory\InsertionOrderLineItemPreview::get_instance();
			$params = array();
			$params["InsertionOrderLineItemID"] = $banner_id;
			$params["Active"] = 1;
			if (!$auth->isSuperAdmin($config)):
				$params["UserID"] = $auth->getUserID();
			endif;
			$InsertionOrderLineItemPreview = $InsertionOrderLineItemPreviewFactory->get_row($params);
			$params["InsertionOrderLineItemPreviewID"] = $InsertionOrderLineItemPreview->InsertionOrderLineItemPreviewID;
			return $params;
			
		endif;

	}

	public static function previewCheckInsertionOrderID($ad_campaign_id, $auth, $config, $mail_transport, $update_data = array()) {
		
		/*
		 * SHOULD WE CREATE A NEW PREVIEW MODE?
		*/
		if (self::doesPreviewInsertionOrderExistForInsertionOrder($ad_campaign_id, $auth) == false):

			$InsertionOrderFactory = \_factory\InsertionOrder::get_instance();
			$params = array();
			$params["InsertionOrderID"] = $ad_campaign_id;
			$params["Active"] = 1;
			if (!$auth->isSuperAdmin($config)):
				$params["UserID"] = $auth->getUserID();
			endif;
			
			$InsertionOrder = $InsertionOrderFactory->get_row($params);

			if ($InsertionOrder == null):
				$params["error"] = "No Such Insertion Order";
				return $params;
			endif;
			
			return self::cloneInsertionOrderIntoInsertionOrderPreview($InsertionOrder->InsertionOrderID, $auth, $config, $mail_transport, $update_data);

		else:

			$InsertionOrderPreviewFactory = \_factory\InsertionOrderPreview::get_instance();
			$params = array();
			$params["InsertionOrderID"] = $ad_campaign_id;
			$params["Active"] = 1;
			if (!$auth->isSuperAdmin($config)):
				$params["UserID"] = $auth->getUserID();
			endif;
	
			$InsertionOrderPreview = $InsertionOrderPreviewFactory->get_row($params);
	
			$params["InsertionOrderPreviewID"] = $InsertionOrderPreview->InsertionOrderPreviewID;
			$params["InsertionOrderLineItemPreviewID"] = null;
			
			return $params;

		endif;

	}

	public static function deletePreviewModeCampaign($ad_campaign_preview_id, $auth, $went_live = false) {

		$InsertionOrderPreviewFactory = \_factory\InsertionOrderPreview::get_instance();
		$params = array();
		$params["InsertionOrderPreviewID"] = $ad_campaign_preview_id;
		$params["Active"] = 1;
		$InsertionOrderPreview = $InsertionOrderPreviewFactory->get_row($params);

		if ($InsertionOrderPreview == null):
			die("No Such Preview Insertion Order");
		endif;

		if ($went_live == true):
			$InsertionOrderPreview->ChangeWentLive   	= 1;
			$InsertionOrderPreview->WentLiveDate 		= date("Y-m-d H:i:s");
		else:
			$InsertionOrderPreview->ChangeWentLive   	= 0;
			$InsertionOrderPreview->WentLiveDate 		= date("Y-m-d H:i:s", 0);
		endif;

		$InsertionOrderPreview->Active 					= 0;

		$InsertionOrderPreviewCopy = new \model\InsertionOrderPreview();

		$InsertionOrderPreviewCopy->InsertionOrderPreviewID 	= $ad_campaign_preview_id;

		if ($InsertionOrderPreview->InsertionOrderID != null):
			$InsertionOrderPreviewCopy->InsertionOrderID 		= $InsertionOrderPreview->InsertionOrderID;
		endif;

		$InsertionOrderPreviewCopy->UserID 					= $InsertionOrderPreview->UserID;
		$InsertionOrderPreviewCopy->Name					= $InsertionOrderPreview->Name;
		$InsertionOrderPreviewCopy->StartDate				= $InsertionOrderPreview->StartDate;
		$InsertionOrderPreviewCopy->EndDate					= $InsertionOrderPreview->EndDate;
		$InsertionOrderPreviewCopy->Customer				= $InsertionOrderPreview->Customer;
		$InsertionOrderPreviewCopy->CustomerID 				= $InsertionOrderPreview->CustomerID;
		$InsertionOrderPreviewCopy->ImpressionsCounter 		= $InsertionOrderPreview->ImpressionsCounter;
		$InsertionOrderPreviewCopy->MaxImpressions 			= $InsertionOrderPreview->MaxImpressions;
		$InsertionOrderPreviewCopy->CurrentSpend 			= $InsertionOrderPreview->CurrentSpend;
		$InsertionOrderPreviewCopy->MaxSpend 				= $InsertionOrderPreview->MaxSpend;
		$InsertionOrderPreviewCopy->Active 					= 0;
		$InsertionOrderPreviewCopy->DateUpdated   			= $InsertionOrderPreview->DateUpdated;
		$InsertionOrderPreviewCopy->ChangeWentLive   		= $InsertionOrderPreview->ChangeWentLive;
		$InsertionOrderPreviewCopy->WentLiveDate 			= $InsertionOrderPreview->WentLiveDate;

		$InsertionOrderPreviewFactory->saveInsertionOrderPreview($InsertionOrderPreviewCopy); // de-activate, not just deleted = 1

		$InsertionOrderLineItemPreviewFactory = \_factory\InsertionOrderLineItemPreview::get_instance();
		$params = array();
		$params["InsertionOrderPreviewID"] = $InsertionOrderPreview->InsertionOrderPreviewID;
		$params["Active"] = 1;
		$InsertionOrderLineItemPreviewList = $InsertionOrderLineItemPreviewFactory->get($params);

		foreach ($InsertionOrderLineItemPreviewList as $InsertionOrderLineItemPreview):

			$InsertionOrderLineItemPreviewFactory->deActivateInsertionOrderLineItemPreview($InsertionOrderLineItemPreview->InsertionOrderLineItemPreviewID);

			$banner_preview = new \model\InsertionOrderLineItemPreview();

			$banner_preview->InsertionOrderLineItemPreviewID 	= $InsertionOrderLineItemPreview->InsertionOrderLineItemPreviewID;
			$banner_preview->InsertionOrderPreviewID 		= $InsertionOrderLineItemPreview->InsertionOrderPreviewID;
			$banner_preview->InsertionOrderLineItemID 		= $InsertionOrderLineItemPreview->InsertionOrderLineItemID;
			$banner_preview->ImpressionType 			= $InsertionOrderLineItemPreview->ImpressionType;
			$banner_preview->UserID 					= $InsertionOrderLineItemPreview->UserID;
			$banner_preview->Name 						= $InsertionOrderLineItemPreview->Name;
			$banner_preview->StartDate					= $InsertionOrderLineItemPreview->StartDate;
			$banner_preview->EndDate					= $InsertionOrderLineItemPreview->EndDate;
			$banner_preview->IsMobile					= $InsertionOrderLineItemPreview->IsMobile;
			$banner_preview->IABSize					= $InsertionOrderLineItemPreview->IABSize;
			$banner_preview->Height						= $InsertionOrderLineItemPreview->Height;
			$banner_preview->Width						= $InsertionOrderLineItemPreview->Width;
			$banner_preview->Weight						= $InsertionOrderLineItemPreview->Weight;
			$banner_preview->BidAmount					= $InsertionOrderLineItemPreview->BidAmount;
			$banner_preview->AdTag						= $InsertionOrderLineItemPreview->AdTag;
			$banner_preview->DeliveryType				= $InsertionOrderLineItemPreview->DeliveryType;
			$banner_preview->LandingPageTLD				= $InsertionOrderLineItemPreview->LandingPageTLD;
			$banner_preview->ImpressionsCounter			= $InsertionOrderLineItemPreview->ImpressionsCounter;
			$banner_preview->BidsCounter				= $InsertionOrderLineItemPreview->BidsCounter;
			$banner_preview->CurrentSpend				= $InsertionOrderLineItemPreview->CurrentSpend;
			$banner_preview->Active						= 0;
			$banner_preview->DateCreated				= $InsertionOrderLineItemPreview->DateCreated;
			$banner_preview->DateUpdated				= $InsertionOrderLineItemPreview->DateUpdated;
			if ($went_live == true):
				$banner_preview->ChangeWentLive				= 1;
				$banner_preview->WentLiveDate				= date("Y-m-d H:i:s");
			else:
				$banner_preview->ChangeWentLive   			= 0;
				$banner_preview->WentLiveDate 				= date("Y-m-d H:i:s", 0);
			endif;


			// de-active banner
			$InsertionOrderLineItemPreviewFactory->saveInsertionOrderLineItemPreview($banner_preview);

		endforeach;

	}

	public static function doesPreviewBannerExist($banner_preview_id, $auth) {

		$InsertionOrderLineItemPreviewFactory = \_factory\InsertionOrderLineItemPreview::get_instance();
		$params = array();
		$params["InsertionOrderLineItemPreviewID"] = $banner_preview_id;
		$params["Active"] = 1;

		$InsertionOrderLineItemPreview = $InsertionOrderLineItemPreviewFactory->get_row($params);

		return $InsertionOrderLineItemPreview !== null;
	}
	
	public static function doesPreviewBannerExistForBanner($banner_id, $auth) {
	
		$InsertionOrderLineItemPreviewFactory = \_factory\InsertionOrderLineItemPreview::get_instance();
		$params = array();
		$params["InsertionOrderLineItemID"] = $banner_id;
		$params["Active"] = 1;

		$InsertionOrderLineItemPreview = $InsertionOrderLineItemPreviewFactory->get_row($params);
	
		return $InsertionOrderLineItemPreview !== null;
	}

	public static function doesPreviewInsertionOrderExist($ad_campaign_preview_id, $auth) {

		$InsertionOrderPreviewFactory = \_factory\InsertionOrderPreview::get_instance();
		$params = array();
		$params["InsertionOrderPreviewID"] = $ad_campaign_preview_id;
		$params["Active"] = 1;
		
		$InsertionOrderPreview = $InsertionOrderPreviewFactory->get_row($params);

		return $InsertionOrderPreview !== null;
	}

	public static function doesPreviewInsertionOrderExistForInsertionOrder($ad_campaign_id, $auth) {

		$InsertionOrderPreviewFactory = \_factory\InsertionOrderPreview::get_instance();
		$params = array();
		$params["InsertionOrderID"] = $ad_campaign_id;
		$params["Active"] = 1;

		$InsertionOrderPreview = $InsertionOrderPreviewFactory->get_row($params);

		return $InsertionOrderPreview !== null;
	}


	public static function cloneInsertionOrderPreviewIntoInsertionOrder($ad_campaign_preview_id, $auth, $config) {

		if ($ad_campaign_preview_id === null):
			return;
		endif;

		$InsertionOrderPreviewFactory = \_factory\InsertionOrderPreview::get_instance();
		$params = array();
		$params["InsertionOrderPreviewID"] = $ad_campaign_preview_id;

		if (!$auth->isSuperAdmin($config) && !$auth->isDomainAdmin($config)):
			die("You do not have permission to access this page");
		endif;
		if (!$auth->isSuperAdmin($config)):
			$params["UserID"] = $auth->getUserID();
		endif;
		$params["Active"] = 1;
		$InsertionOrderPreview = $InsertionOrderPreviewFactory->get_row($params);

		if ($InsertionOrderPreview == null):
			die("Invalid InsertionOrderPreviewID");
		endif;

		/*
		 * Clone InsertionOrderPreview into InsertionOrder
		*/

		$InsertionOrderFactory = \_factory\InsertionOrder::get_instance();
		$InsertionOrder = new \model\InsertionOrder();

		if ($InsertionOrderPreview->InsertionOrderID != null):
			$InsertionOrder->InsertionOrderID 		= $InsertionOrderPreview->InsertionOrderID;
		endif;

		$campaign_active = isset($InsertionOrderPreview->Deleted) && $InsertionOrderPreview->Deleted == 1 ? 0 : 1;

		$InsertionOrder->UserID 			= $InsertionOrderPreview->UserID;
		$InsertionOrder->Name				= $InsertionOrderPreview->Name;
		$InsertionOrder->StartDate			= $InsertionOrderPreview->StartDate;
		$InsertionOrder->EndDate			= $InsertionOrderPreview->EndDate;
		$InsertionOrder->Customer			= $InsertionOrderPreview->Customer;
		$InsertionOrder->CustomerID 		= $InsertionOrderPreview->CustomerID;
		$InsertionOrder->ImpressionsCounter = 0;
		$InsertionOrder->MaxImpressions 	= $InsertionOrderPreview->MaxImpressions;
		$InsertionOrder->CurrentSpend 		= 0;
		$InsertionOrder->MaxSpend 			= $InsertionOrderPreview->MaxSpend;
		$InsertionOrder->Active 			= $campaign_active;
		$InsertionOrder->DateUpdated   		= date("Y-m-d H:i:s");

		$ad_campaign_id = $InsertionOrderFactory->saveInsertionOrder($InsertionOrder);
		
		$PmpDealPublisherWebsiteToInsertionOrderPreviewFactory = \_factory\PmpDealPublisherWebsiteToInsertionOrderPreview::get_instance();
		$SspRtbChannelToInsertionOrderPreviewFactory = \_factory\SspRtbChannelToInsertionOrderPreview::get_instance();
		$PmpDealPublisherWebsiteToInsertionOrderFactory = \_factory\PmpDealPublisherWebsiteToInsertionOrder::get_instance();
		$SspRtbChannelToInsertionOrderFactory = \_factory\SspRtbChannelToInsertionOrder::get_instance();

		/*
		 * PMP DEALS
		*/
			
		// first delete the existing ones, then re-insert
		$PmpDealPublisherWebsiteToInsertionOrderFactory->deletePmpDealPublisherWebsiteToInsertionOrderByInsertionOrderID($ad_campaign_id);
		
		$params = array();
		$params["InsertionOrderPreviewID"] 					= $ad_campaign_preview_id;
		$PmpDealPublisherWebsiteToInsertionOrderPreviewList = $PmpDealPublisherWebsiteToInsertionOrderPreviewFactory->get($params);
		
		foreach ($PmpDealPublisherWebsiteToInsertionOrderPreviewList as $PmpDealPublisherWebsiteToInsertionOrderPreview):
			
			$PmpDealPublisherWebsiteToInsertionOrder = new \model\PmpDealPublisherWebsiteToInsertionOrder();
			
			$PmpDealPublisherWebsiteToInsertionOrder->PublisherWebsiteID 			= $PmpDealPublisherWebsiteToInsertionOrderPreview->PublisherWebsiteID;
			$PmpDealPublisherWebsiteToInsertionOrder->PublisherWebsiteLocal 		= $PmpDealPublisherWebsiteToInsertionOrderPreview->PublisherWebsiteLocal;
			$PmpDealPublisherWebsiteToInsertionOrder->PublisherWebsiteDescription 	= $PmpDealPublisherWebsiteToInsertionOrderPreview->PublisherWebsiteDescription;
			$PmpDealPublisherWebsiteToInsertionOrder->InsertionOrderID 				= $ad_campaign_id;
			$PmpDealPublisherWebsiteToInsertionOrder->Enabled 						= $PmpDealPublisherWebsiteToInsertionOrderPreview->Enabled;
			
			$PmpDealPublisherWebsiteToInsertionOrderFactory->savePmpDealPublisherWebsiteToInsertionOrder($PmpDealPublisherWebsiteToInsertionOrder);
			
		endforeach;
		
		/*
		 * SSP RTB CHANNELS
		*/
			
		// first delete the existing ones, then re-insert
		$SspRtbChannelToInsertionOrderFactory->deleteSspRtbChannelToInsertionOrderByInsertionOrderID($ad_campaign_id);
		
		$params = array();
		$params["InsertionOrderPreviewID"] 			= $ad_campaign_preview_id;
		$SspRtbChannelToInsertionOrderPreviewList 	= $SspRtbChannelToInsertionOrderPreviewFactory->get($params);
		
		foreach ($SspRtbChannelToInsertionOrderPreviewList as $SspRtbChannelToInsertionOrderPreview):
				
			$SspRtbChannelToInsertionOrder = new \model\SspRtbChannelToInsertionOrder();
				
			$SspRtbChannelToInsertionOrder->SspPublisherChannelID 				= $SspRtbChannelToInsertionOrderPreview->SspPublisherChannelID;
			$SspRtbChannelToInsertionOrder->SspPublisherChannelDescription 		= $SspRtbChannelToInsertionOrderPreview->SspPublisherChannelDescription;
			$SspRtbChannelToInsertionOrder->SspExchange 						= $SspRtbChannelToInsertionOrderPreview->SspExchange;
			$SspRtbChannelToInsertionOrder->InsertionOrderID 					= $ad_campaign_id;
			$SspRtbChannelToInsertionOrder->Enabled 							= $SspRtbChannelToInsertionOrderPreview->Enabled;
				
			$SspRtbChannelToInsertionOrderFactory->saveSspRtbChannelToInsertionOrder($SspRtbChannelToInsertionOrder);
				
		endforeach;
		
		$InsertionOrderLineItemPreviewFactory = \_factory\InsertionOrderLineItemPreview::get_instance();
		$params = array();
		$params["InsertionOrderPreviewID"] = $ad_campaign_preview_id;
		/*
		 * get all banners, not just active ones, we want to set deleted banners to inactive on production also
		 * if they were flagged that way in preview mode
		 * $params["Active"] = 1;
		 */
		$InsertionOrderLineItemPreviewList = $InsertionOrderLineItemPreviewFactory->get($params);

		$InsertionOrderLineItemFactory = \_factory\InsertionOrderLineItem::get_instance();
		$InsertionOrderLineItemPreviewFactory = \_factory\InsertionOrderLineItemPreview::get_instance();
		$InsertionOrderLineItemRestrictionsFactory = \_factory\InsertionOrderLineItemRestrictions::get_instance();
		$InsertionOrderLineItemRestrictionsPreviewFactory = \_factory\InsertionOrderLineItemRestrictionsPreview::get_instance();
		
		$InsertionOrderLineItemVideoRestrictionsFactory = \_factory\InsertionOrderLineItemVideoRestrictions::get_instance();
		$InsertionOrderLineItemVideoRestrictionsPreviewFactory = \_factory\InsertionOrderLineItemVideoRestrictionsPreview::get_instance();
		
		$PmpDealPublisherWebsiteToInsertionOrderLineItemPreviewFactory = \_factory\PmpDealPublisherWebsiteToInsertionOrderLineItemPreview::get_instance();
		$SspRtbChannelToInsertionOrderLineItemPreviewFactory = \_factory\SspRtbChannelToInsertionOrderLineItemPreview::get_instance();
		$PmpDealPublisherWebsiteToInsertionOrderLineItemFactory = \_factory\PmpDealPublisherWebsiteToInsertionOrderLineItem::get_instance();
		$SspRtbChannelToInsertionOrderLineItemFactory = \_factory\SspRtbChannelToInsertionOrderLineItem::get_instance();
		
		$PublisherAdZoneFactory = \_factory\PublisherAdZone::get_instance();	
		$InsertionOrderLineItemDomainExclusionFactory = \_factory\InsertionOrderLineItemDomainExclusion::get_instance();
		$InsertionOrderLineItemDomainExclusionPreviewFactory = \_factory\InsertionOrderLineItemDomainExclusionPreview::get_instance();
		$InsertionOrderLineItemDomainExclusiveInclusionFactory = \_factory\InsertionOrderLineItemDomainExclusiveInclusion::get_instance();
		$InsertionOrderLineItemDomainExclusiveInclusionPreviewFactory = \_factory\InsertionOrderLineItemDomainExclusiveInclusionPreview::get_instance();

		foreach ($InsertionOrderLineItemPreviewList as $InsertionOrderLineItemPreview):

			$banner_preview_id = $InsertionOrderLineItemPreview->InsertionOrderLineItemPreviewID;

			$Banner = new \model\InsertionOrderLineItem();

			$Banner->InsertionOrderID 				= $ad_campaign_id;

			if ($InsertionOrderLineItemPreview->InsertionOrderLineItemID != null):
				$Banner->InsertionOrderLineItemID 	= $InsertionOrderLineItemPreview->InsertionOrderLineItemID;
			endif;

			if ($campaign_active == 0):
				$banner_active = 0;
			else:
				$banner_active = $InsertionOrderLineItemPreview->Active;
			endif;

			$Banner->UserID 					= $InsertionOrderLineItemPreview->UserID;
			$Banner->Name 						= $InsertionOrderLineItemPreview->Name;
			$Banner->ImpressionType 			= $InsertionOrderLineItemPreview->ImpressionType;
			$Banner->StartDate 					= $InsertionOrderLineItemPreview->StartDate;
			$Banner->EndDate 					= $InsertionOrderLineItemPreview->EndDate;
			$Banner->IsMobile 					= $InsertionOrderLineItemPreview->IsMobile;
			$Banner->IABSize 					= $InsertionOrderLineItemPreview->IABSize;
			$Banner->Height 					= $InsertionOrderLineItemPreview->Height;
			$Banner->Width 						= $InsertionOrderLineItemPreview->Width;
			$Banner->Weight 					= $InsertionOrderLineItemPreview->Weight;
			$Banner->BidAmount 					= $InsertionOrderLineItemPreview->BidAmount;
			$Banner->AdTag 						= $InsertionOrderLineItemPreview->AdTag;
			$Banner->DeliveryType 				= $InsertionOrderLineItemPreview->DeliveryType;
			$Banner->LandingPageTLD 			= $InsertionOrderLineItemPreview->LandingPageTLD;
			$Banner->ImpressionsCounter 		= $InsertionOrderLineItemPreview->ImpressionsCounter;
			$Banner->BidsCounter 				= $InsertionOrderLineItemPreview->BidsCounter;
			$Banner->CurrentSpend 				= $InsertionOrderLineItemPreview->CurrentSpend;
			$Banner->Active 					= $banner_active;
			$Banner->DateCreated 				= date("Y-m-d H:i:s");

			// if the banner was deleted and there is no corresponding production banner, don't save it
			if ($banner_active == 0 && $InsertionOrderLineItemPreview->InsertionOrderLineItemID == null):
				continue;
			endif;

			$banner_id = $InsertionOrderLineItemFactory->saveInsertionOrderLineItem($Banner);

			// if the banner was deleted there's no reason to continue to copy it's properties here
			if ($banner_active == 0):
				continue;
			endif;
			
			if ($Banner->ImpressionType == 'video'):

				/*
				 * VIDEO RESTRICTIONS
				*/
	
				$params = array();
				$params["InsertionOrderLineItemPreviewID"] = $banner_preview_id;
				$InsertionOrderLineItemVideoRestrictionsPreview = $InsertionOrderLineItemVideoRestrictionsPreviewFactory->get_row($params);
	
				if ($InsertionOrderLineItemVideoRestrictionsPreview != null):
	
					$VideoRestrictions = new \model\InsertionOrderLineItemVideoRestrictions();
					$VideoRestrictions->InsertionOrderLineItemID 					= $banner_id;
					$VideoRestrictions->GeoCountry 							= $InsertionOrderLineItemVideoRestrictionsPreview->GeoCountry;
					$VideoRestrictions->GeoState 							= $InsertionOrderLineItemVideoRestrictionsPreview->GeoState;
					$VideoRestrictions->GeoCity 							= $InsertionOrderLineItemVideoRestrictionsPreview->GeoCity;
					$VideoRestrictions->MimesCommaSeparated 				= $InsertionOrderLineItemVideoRestrictionsPreview->MimesCommaSeparated;
					$VideoRestrictions->MinDuration 						= $InsertionOrderLineItemVideoRestrictionsPreview->MinDuration;
					$VideoRestrictions->MaxDuration 						= $InsertionOrderLineItemVideoRestrictionsPreview->MaxDuration;
					$VideoRestrictions->ApisSupportedCommaSeparated 		= $InsertionOrderLineItemVideoRestrictionsPreview->ApisSupportedCommaSeparated;
					$VideoRestrictions->ProtocolsCommaSeparated 			= $InsertionOrderLineItemVideoRestrictionsPreview->ProtocolsCommaSeparated;
					$VideoRestrictions->DeliveryCommaSeparated 				= $InsertionOrderLineItemVideoRestrictionsPreview->DeliveryCommaSeparated;
					$VideoRestrictions->PlaybackCommaSeparated 				= $InsertionOrderLineItemVideoRestrictionsPreview->PlaybackCommaSeparated;
					$VideoRestrictions->StartDelay			 				= $InsertionOrderLineItemVideoRestrictionsPreview->StartDelay;
					$VideoRestrictions->Linearity			 				= $InsertionOrderLineItemVideoRestrictionsPreview->Linearity;
					$VideoRestrictions->FoldPos			 					= $InsertionOrderLineItemVideoRestrictionsPreview->FoldPos;
					$VideoRestrictions->MinHeight 							= $InsertionOrderLineItemVideoRestrictionsPreview->MinHeight;
					$VideoRestrictions->MinWidth 							= $InsertionOrderLineItemVideoRestrictionsPreview->MinWidth;
					$VideoRestrictions->Secure 								= $InsertionOrderLineItemVideoRestrictionsPreview->Secure;
					$VideoRestrictions->Optout 								= $InsertionOrderLineItemVideoRestrictionsPreview->Optout;
					$VideoRestrictions->Vertical 							= $InsertionOrderLineItemVideoRestrictionsPreview->Vertical;
					$VideoRestrictions->DateCreated 						= date("Y-m-d H:i:s");
					$VideoRestrictions->DateUpdated 						= date("Y-m-d H:i:s");
	
					$InsertionOrderLineItemVideoRestrictionsFactory->saveInsertionOrderLineItemVideoRestrictions($VideoRestrictions);
				endif;
				
			else:
			
				/*
				 * BANNER RESTRICTIONS
				*/
				
				$params = array();
				$params["InsertionOrderLineItemPreviewID"] = $banner_preview_id;
				$InsertionOrderLineItemRestrictionsPreview = $InsertionOrderLineItemRestrictionsPreviewFactory->get_row($params);
				
				if ($InsertionOrderLineItemRestrictionsPreview != null):
					
					$BannerRestrictions = new \model\InsertionOrderLineItemRestrictions();
					$BannerRestrictions->InsertionOrderLineItemID 				= $banner_id;
					$BannerRestrictions->GeoCountry 						= $InsertionOrderLineItemRestrictionsPreview->GeoCountry;
					$BannerRestrictions->GeoState 							= $InsertionOrderLineItemRestrictionsPreview->GeoState;
					$BannerRestrictions->GeoCity 							= $InsertionOrderLineItemRestrictionsPreview->GeoCity;
					$BannerRestrictions->AdTagType 							= $InsertionOrderLineItemRestrictionsPreview->AdTagType;
					$BannerRestrictions->AdPositionMinLeft 					= $InsertionOrderLineItemRestrictionsPreview->AdPositionMinLeft;
					$BannerRestrictions->AdPositionMaxLeft 					= $InsertionOrderLineItemRestrictionsPreview->AdPositionMaxLeft;
					$BannerRestrictions->AdPositionMinTop 					= $InsertionOrderLineItemRestrictionsPreview->AdPositionMinTop;
					$BannerRestrictions->AdPositionMaxTop 					= $InsertionOrderLineItemRestrictionsPreview->AdPositionMaxTop;
					$BannerRestrictions->FoldPos 							= $InsertionOrderLineItemRestrictionsPreview->FoldPos;
					$BannerRestrictions->Freq 								= $InsertionOrderLineItemRestrictionsPreview->Freq;
					$BannerRestrictions->Timezone 							= $InsertionOrderLineItemRestrictionsPreview->Timezone;
					$BannerRestrictions->InIframe 							= $InsertionOrderLineItemRestrictionsPreview->InIframe;
					$BannerRestrictions->MinScreenResolutionWidth 			= $InsertionOrderLineItemRestrictionsPreview->MinScreenResolutionWidth;
					$BannerRestrictions->MaxScreenResolutionWidth 			= $InsertionOrderLineItemRestrictionsPreview->MaxScreenResolutionWidth;
					$BannerRestrictions->MinScreenResolutionHeight 			= $InsertionOrderLineItemRestrictionsPreview->MinScreenResolutionHeight;
					$BannerRestrictions->MaxScreenResolutionHeight 			= $InsertionOrderLineItemRestrictionsPreview->MaxScreenResolutionHeight;
					$BannerRestrictions->HttpLanguage 						= $InsertionOrderLineItemRestrictionsPreview->HttpLanguage;
					$BannerRestrictions->BrowserUserAgentGrep 				= $InsertionOrderLineItemRestrictionsPreview->BrowserUserAgentGrep;
					$BannerRestrictions->Secure 							= $InsertionOrderLineItemRestrictionsPreview->Secure;
					$BannerRestrictions->Optout 							= $InsertionOrderLineItemRestrictionsPreview->Optout;
					$BannerRestrictions->Vertical 							= $InsertionOrderLineItemRestrictionsPreview->Vertical;
					$BannerRestrictions->DateCreated 						= date("Y-m-d H:i:s");
					$BannerRestrictions->DateUpdated 						= date("Y-m-d H:i:s");
					
					$InsertionOrderLineItemRestrictionsFactory->saveInsertionOrderLineItemRestrictions($BannerRestrictions);
				endif;
					
			endif;
			
			/*
			 * PMP DEALS
			*/
				
			// first delete the existing ones, then re-insert
			$PmpDealPublisherWebsiteToInsertionOrderLineItemFactory->deletePmpDealPublisherWebsiteToInsertionOrderLineItemByInsertionOrderLineItemID($banner_id);
			
			$params = array();
			$params["InsertionOrderLineItemPreviewID"] = $banner_preview_id;
			$PmpDealPublisherWebsiteToInsertionOrderLineItemPreviewList = $PmpDealPublisherWebsiteToInsertionOrderLineItemPreviewFactory->get($params);
			
			foreach ($PmpDealPublisherWebsiteToInsertionOrderLineItemPreviewList as $PmpDealPublisherWebsiteToInsertionOrderLineItemPreview):
				
				$PmpDealPublisherWebsiteToInsertionOrderLineItem = new \model\PmpDealPublisherWebsiteToInsertionOrderLineItem();
					
				$PmpDealPublisherWebsiteToInsertionOrderLineItem->PublisherWebsiteID 			= $PmpDealPublisherWebsiteToInsertionOrderLineItemPreview->PublisherWebsiteID;
				$PmpDealPublisherWebsiteToInsertionOrderLineItem->PublisherWebsiteLocal 		= $PmpDealPublisherWebsiteToInsertionOrderLineItemPreview->PublisherWebsiteLocal;
				$PmpDealPublisherWebsiteToInsertionOrderLineItem->PublisherWebsiteDescription 	= $PmpDealPublisherWebsiteToInsertionOrderLineItemPreview->PublisherWebsiteDescription;
				$PmpDealPublisherWebsiteToInsertionOrderLineItem->InsertionOrderLineItemID 		= $banner_id;
				$PmpDealPublisherWebsiteToInsertionOrderLineItem->Enabled 						= $PmpDealPublisherWebsiteToInsertionOrderLineItemPreview->Enabled;
					
				$PmpDealPublisherWebsiteToInsertionOrderLineItemFactory->savePmpDealPublisherWebsiteToInsertionOrderLineItem($PmpDealPublisherWebsiteToInsertionOrderLineItem);
					
			endforeach;
			
			/*
			 * SSP RTB CHANNELS
			*/
				
			// first delete the existing ones, then re-insert
			$SspRtbChannelToInsertionOrderLineItemFactory->deleteSspRtbChannelToInsertionOrderLineItemByInsertionOrderLineItemID($banner_id);
			
			$params = array();
			$params["InsertionOrderLineItemPreviewID"] = $banner_preview_id;
			$SspRtbChannelToInsertionOrderLineItemPreviewList 	= $SspRtbChannelToInsertionOrderLineItemPreviewFactory->get($params);
			
			foreach ($SspRtbChannelToInsertionOrderLineItemPreviewList as $SspRtbChannelToInsertionOrderLineItemPreview):
			
				$SspRtbChannelToInsertionOrderLineItem = new \model\SspRtbChannelToInsertionOrderLineItem();
				
				$SspRtbChannelToInsertionOrderLineItem->SspPublisherChannelID 				= $SspRtbChannelToInsertionOrderLineItemPreview->SspPublisherChannelID;
				$SspRtbChannelToInsertionOrderLineItem->SspPublisherChannelDescription 		= $SspRtbChannelToInsertionOrderLineItemPreview->SspPublisherChannelDescription;
				$SspRtbChannelToInsertionOrderLineItem->SspExchange 						= $SspRtbChannelToInsertionOrderLineItemPreview->SspExchange;
				$SspRtbChannelToInsertionOrderLineItem->InsertionOrderLineItemID 			= $banner_id;
				$SspRtbChannelToInsertionOrderLineItem->Enabled 							= $SspRtbChannelToInsertionOrderLineItemPreview->Enabled;
				
				$SspRtbChannelToInsertionOrderLineItemFactory->saveSspRtbChannelToInsertionOrderLineItem($SspRtbChannelToInsertionOrderLineItem);
				
			endforeach;
			
			/*
			 * DOMAIN EXCLUSIONS
			*/

			// first delete the existing ones, then re-insert
			$InsertionOrderLineItemDomainExclusionFactory->deleteInsertionOrderLineItemDomainExclusionByBannerID($banner_id);

			$params = array();
			$params["InsertionOrderLineItemPreviewID"] = $banner_preview_id;
			$InsertionOrderLineItemDomainExclusionPreviewList = $InsertionOrderLineItemDomainExclusionPreviewFactory->get($params);

			foreach ($InsertionOrderLineItemDomainExclusionPreviewList as $InsertionOrderLineItemDomainExclusionPreview):

				$BannerDomainExclusion = new \model\InsertionOrderLineItemDomainExclusion();

				$BannerDomainExclusion->InsertionOrderLineItemID 	= $banner_id;
				$BannerDomainExclusion->ExclusionType 		= $InsertionOrderLineItemDomainExclusionPreview->ExclusionType;
				$BannerDomainExclusion->DomainName 			= $InsertionOrderLineItemDomainExclusionPreview->DomainName;
				$BannerDomainExclusion->DateCreated 		= date("Y-m-d H:i:s");
				$BannerDomainExclusion->DateUpdated 		= date("Y-m-d H:i:s");

				$InsertionOrderLineItemDomainExclusionFactory->saveInsertionOrderLineItemDomainExclusion($BannerDomainExclusion);

			endforeach;

			/*
			 * DOMAIN EXCLUSIVE INCLUSIONS
			*/

			// first delete the existing ones, then re-insert
			$InsertionOrderLineItemDomainExclusiveInclusionFactory->deleteInsertionOrderLineItemDomainExclusiveInclusionByBannerID($banner_id);

			$params = array();
			$params["InsertionOrderLineItemPreviewID"] = $banner_preview_id;
			$InsertionOrderLineItemDomainExclusiveInclusionPreviewList = $InsertionOrderLineItemDomainExclusiveInclusionPreviewFactory->get($params);

			foreach ($InsertionOrderLineItemDomainExclusiveInclusionPreviewList as $InsertionOrderLineItemDomainExclusiveInclusionPreview):

				$BannerDomainExclusiveInclusion = new \model\InsertionOrderLineItemDomainExclusiveInclusion();

				$BannerDomainExclusiveInclusion->InsertionOrderLineItemID 	= $banner_id;
				$BannerDomainExclusiveInclusion->InclusionType 			= $InsertionOrderLineItemDomainExclusiveInclusionPreview->InclusionType;
				$BannerDomainExclusiveInclusion->DomainName 			= $InsertionOrderLineItemDomainExclusiveInclusionPreview->DomainName;
				$BannerDomainExclusiveInclusion->DateCreated 			= date("Y-m-d H:i:s");
				$BannerDomainExclusiveInclusion->DateUpdated 			= date("Y-m-d H:i:s");

				$InsertionOrderLineItemDomainExclusiveInclusionFactory->saveInsertionOrderLineItemDomainExclusiveInclusion($BannerDomainExclusiveInclusion);

			endforeach;


		endforeach;

		return $ad_campaign_id;
		
	}


	public static function cloneInsertionOrderIntoInsertionOrderPreview($ad_campaign_id, $auth, $config, $mail_transport, $update_data) {

		$return_val = array();

		if ($ad_campaign_id === null):
			return null;
		endif;

		$InsertionOrderFactory = \_factory\InsertionOrder::get_instance();
		$params = array();
		$params["InsertionOrderID"] = $ad_campaign_id;
		if (!$auth->isSuperAdmin($config)):
			$params["UserID"] = $auth->getUserID();
		endif;
		$params["Active"] = 1;
		$InsertionOrder = $InsertionOrderFactory->get_row($params);

		if ($InsertionOrder == null):
			//die("Invalid InsertionOrder ID");
			$params["error"] = "Invalid InsertionOrder ID";
			return $params;
		endif;

		/*
		 * Clone InsertionOrder into InsertionOrderPreview
		*/
		
		$InsertionOrderPreviewFactory = \_factory\InsertionOrderPreview::get_instance();
		$InsertionOrderPreview = new \model\InsertionOrderPreview();

		$InsertionOrderPreview->InsertionOrderID 	= $InsertionOrder->InsertionOrderID;
		$InsertionOrderPreview->UserID 				= $InsertionOrder->UserID;
		$InsertionOrderPreview->Name				= $InsertionOrder->Name;
		$InsertionOrderPreview->StartDate			= $InsertionOrder->StartDate;
		$InsertionOrderPreview->EndDate				= $InsertionOrder->EndDate;
		$InsertionOrderPreview->Customer			= $InsertionOrder->Customer;
		$InsertionOrderPreview->CustomerID 			= $InsertionOrder->CustomerID;
		$InsertionOrderPreview->ImpressionsCounter  = 0;
		$InsertionOrderPreview->MaxImpressions 		= $InsertionOrder->MaxImpressions;
		$InsertionOrderPreview->CurrentSpend 		= 0;
		$InsertionOrderPreview->MaxSpend 			= $InsertionOrder->MaxSpend;
		$InsertionOrderPreview->Active 				= 1;
		$InsertionOrderPreview->DateCreated   		= date("Y-m-d H:i:s");
		$InsertionOrderPreview->DateUpdated   		= date("Y-m-d H:i:s");
		$InsertionOrderPreview->ChangeWentLive   	= 0;
		$InsertionOrderPreview->WentLiveDate        = '0000-00-00 00:00:00';
		
		$InsertionOrderPreviewID = $InsertionOrderPreviewFactory->saveInsertionOrderPreview($InsertionOrderPreview);

		if ($update_data['type'] == 'InsertionOrderID'):
			$return_val = array('InsertionOrderPreviewID'=>$InsertionOrderPreviewID);
		endif;

		$InsertionOrderLineItemFactory = \_factory\InsertionOrderLineItem::get_instance();
		$params = array();
		$params["InsertionOrderID"] = $InsertionOrder->InsertionOrderID;
		$params["Active"] = 1;
		$InsertionOrderLineItemList = $InsertionOrderLineItemFactory->get($params);

		$InsertionOrderLineItemPreviewFactory = \_factory\InsertionOrderLineItemPreview::get_instance();
		$InsertionOrderLineItemRestrictionsFactory = \_factory\InsertionOrderLineItemRestrictions::get_instance();
		$InsertionOrderLineItemRestrictionsPreviewFactory = \_factory\InsertionOrderLineItemRestrictionsPreview::get_instance();
		
		$InsertionOrderLineItemVideoRestrictionsFactory = \_factory\InsertionOrderLineItemVideoRestrictions::get_instance();
		$InsertionOrderLineItemVideoRestrictionsPreviewFactory = \_factory\InsertionOrderLineItemVideoRestrictionsPreview::get_instance();
		
		$InsertionOrderLineItemDomainExclusionFactory = \_factory\InsertionOrderLineItemDomainExclusion::get_instance();
		$InsertionOrderLineItemDomainExclusionPreviewFactory = \_factory\InsertionOrderLineItemDomainExclusionPreview::get_instance();
		$InsertionOrderLineItemDomainExclusiveInclusionFactory = \_factory\InsertionOrderLineItemDomainExclusiveInclusion::get_instance();
		$InsertionOrderLineItemDomainExclusiveInclusionPreviewFactory = \_factory\InsertionOrderLineItemDomainExclusiveInclusionPreview::get_instance();

		$SspRtbChannelToInsertionOrderLineItemPreviewFactory = \_factory\SspRtbChannelToInsertionOrderLineItemPreview::get_instance();
		$SspRtbChannelToInsertionOrderLineItemFactory = \_factory\SspRtbChannelToInsertionOrderLineItem::get_instance();
		$PmpDealPublisherWebsiteToInsertionOrderLineItemFactory = \_factory\PmpDealPublisherWebsiteToInsertionOrderLineItem::get_instance();
		$PmpDealPublisherWebsiteToInsertionOrderLineItemPreviewFactory = \_factory\PmpDealPublisherWebsiteToInsertionOrderLineItemPreview::get_instance();
		
		$SspRtbChannelToInsertionOrderPreviewFactory = \_factory\SspRtbChannelToInsertionOrderPreview::get_instance();
		$SspRtbChannelToInsertionOrderFactory = \_factory\SspRtbChannelToInsertionOrder::get_instance();
		$PmpDealPublisherWebsiteToInsertionOrderFactory = \_factory\PmpDealPublisherWebsiteToInsertionOrder::get_instance();
		$PmpDealPublisherWebsiteToInsertionOrderPreviewFactory = \_factory\PmpDealPublisherWebsiteToInsertionOrderPreview::get_instance();
		
		/*
		 * PMP INVENTORY
		*/
		
		$params = array();
		$params["InsertionOrderID"] = $InsertionOrder->InsertionOrderID;
		$PmpDealPublisherWebsiteToInsertionOrder = $PmpDealPublisherWebsiteToInsertionOrderFactory->get_row($params);
		
		if ($PmpDealPublisherWebsiteToInsertionOrder != null):
		
			$PmpDealPublisherWebsiteToInsertionOrderPreview = new \model\PmpDealPublisherWebsiteToInsertionOrderPreview();
			
			$PmpDealPublisherWebsiteToInsertionOrderPreview->PublisherWebsiteID 									= $PmpDealPublisherWebsiteToInsertionOrder->PublisherWebsiteID;
			$PmpDealPublisherWebsiteToInsertionOrderPreview->PublisherWebsiteLocal 									= $PmpDealPublisherWebsiteToInsertionOrder->PublisherWebsiteLocal;
			$PmpDealPublisherWebsiteToInsertionOrderPreview->PublisherWebsiteDescription 							= $PmpDealPublisherWebsiteToInsertionOrder->PublisherWebsiteDescription;
			$PmpDealPublisherWebsiteToInsertionOrderPreview->InsertionOrderPreviewID 								= $InsertionOrderPreviewID;
			$PmpDealPublisherWebsiteToInsertionOrderPreview->Enabled 												= $PmpDealPublisherWebsiteToInsertionOrder->Enabled;
			$PmpDealPublisherWebsiteToInsertionOrderPreview->DateCreated 											= date("Y-m-d H:i:s");
			$PmpDealPublisherWebsiteToInsertionOrderPreview->DateUpdated 											= date("Y-m-d H:i:s");
			
			$PmpDealPublisherWebsiteToInsertionOrderPreviewFactory->savePmpDealPublisherWebsiteToInsertionOrderPreview($PmpDealPublisherWebsiteToInsertionOrderPreview);
			
		endif;

		/*
		 * SSP INVENTORY
		*/
		
		$params = array();
		$params["InsertionOrderID"] = $InsertionOrder->InsertionOrderID;
		$SspRtbChannelToInsertionOrder = $SspRtbChannelToInsertionOrderFactory->get_row($params);
		
		if ($SspRtbChannelToInsertionOrder != null):
		
			$SspRtbChannelToInsertionOrderPreview = new \model\SspRtbChannelToInsertionOrderPreview();
			
			$SspRtbChannelToInsertionOrderPreview->SspPublisherChannelID 								= $SspRtbChannelToInsertionOrder->SspPublisherChannelID;
			$SspRtbChannelToInsertionOrderPreview->SspPublisherChannelDescription 						= $SspRtbChannelToInsertionOrder->SspPublisherChannelDescription;
			$SspRtbChannelToInsertionOrderPreview->SspExchange 											= $SspRtbChannelToInsertionOrder->SspExchange;
			$SspRtbChannelToInsertionOrderPreview->InsertionOrderPreviewID 								= $InsertionOrderPreviewID;
			$SspRtbChannelToInsertionOrderPreview->Enabled 												= $SspRtbChannelToInsertionOrder->Enabled;
			$SspRtbChannelToInsertionOrderPreview->DateCreated 											= date("Y-m-d H:i:s");
			$SspRtbChannelToInsertionOrderPreview->DateUpdated 											= date("Y-m-d H:i:s");
			
			$SspRtbChannelToInsertionOrderPreviewFactory->saveSspRtbChannelToInsertionOrderPreview($SspRtbChannelToInsertionOrderPreview);
			
		endif;

		foreach ($InsertionOrderLineItemList as $InsertionOrderLineItem):

			$banner_id = $InsertionOrderLineItem->InsertionOrderLineItemID;

			
			$BannerPreview = new \model\InsertionOrderLineItemPreview();

			$BannerPreview->InsertionOrderPreviewID 	= $InsertionOrderPreviewID;
			$BannerPreview->InsertionOrderLineItemID 	= $InsertionOrderLineItem->InsertionOrderLineItemID;
			$BannerPreview->UserID 						= $InsertionOrderLineItem->UserID;
			$BannerPreview->Name 						= $InsertionOrderLineItem->Name;
			$BannerPreview->ImpressionType 				= $InsertionOrderLineItem->ImpressionType;
			$BannerPreview->StartDate 					= $InsertionOrderLineItem->StartDate;
			$BannerPreview->EndDate 					= $InsertionOrderLineItem->EndDate;
			$BannerPreview->IsMobile 					= $InsertionOrderLineItem->IsMobile;
			$BannerPreview->IABSize 					= $InsertionOrderLineItem->IABSize;
			$BannerPreview->Height 						= $InsertionOrderLineItem->Height;
			$BannerPreview->Width 						= $InsertionOrderLineItem->Width;
			$BannerPreview->Weight 						= $InsertionOrderLineItem->Weight;
			$BannerPreview->BidAmount 					= $InsertionOrderLineItem->BidAmount;
			$BannerPreview->AdTag 						= $InsertionOrderLineItem->AdTag;
			$BannerPreview->DeliveryType				= $InsertionOrderLineItem->DeliveryType;
			$BannerPreview->LandingPageTLD 				= $InsertionOrderLineItem->LandingPageTLD;
			$BannerPreview->ImpressionsCounter 			= $InsertionOrderLineItem->ImpressionsCounter;
			$BannerPreview->BidsCounter 				= $InsertionOrderLineItem->BidsCounter;
			$BannerPreview->CurrentSpend 				= $InsertionOrderLineItem->CurrentSpend;
			$BannerPreview->Active 						= $InsertionOrderLineItem->Active;
			$BannerPreview->DateCreated 				= date("Y-m-d H:i:s");
			$BannerPreview->DateUpdated 				= date("Y-m-d H:i:s");
			$BannerPreview->ChangeWentLive 				= 0;
			$BannerPreview->WentLiveDate        	  	= '0000-00-00 00:00:00';
			
			$InsertionOrderLineItemPreviewID = $InsertionOrderLineItemPreviewFactory->saveInsertionOrderLineItemPreview($BannerPreview);

			if ($update_data['type'] == 'InsertionOrderLineItemID' && $update_data['id'] == $banner_id):
				$return_val = array('InsertionOrderLineItemPreviewID'=>$InsertionOrderLineItemPreviewID,
									'InsertionOrderPreviewID'=>$InsertionOrderPreviewID);

			endif;

			
			if ($BannerPreview->ImpressionType == 'video'):
			
				/*
				 * VIDEO RESTRICTIONS
				*/
			
				$params = array();
				$params["InsertionOrderLineItemID"] = $banner_id;
				$InsertionOrderLineItemVideoRestrictions = $InsertionOrderLineItemVideoRestrictionsFactory->get_row($params);

				if ($InsertionOrderLineItemVideoRestrictions != null):
				
					$VideoRestrictionsPreview = new \model\InsertionOrderLineItemVideoRestrictionsPreview();
					$VideoRestrictionsPreview->InsertionOrderLineItemPreviewID 				= $InsertionOrderLineItemPreviewID;
					$VideoRestrictionsPreview->GeoCountry 						= $InsertionOrderLineItemVideoRestrictions->GeoCountry;
					$VideoRestrictionsPreview->GeoState 						= $InsertionOrderLineItemVideoRestrictions->GeoState;
					$VideoRestrictionsPreview->GeoCity 							= $InsertionOrderLineItemVideoRestrictions->GeoCity;
					$VideoRestrictionsPreview->MimesCommaSeparated 				= $InsertionOrderLineItemVideoRestrictions->MimesCommaSeparated;
					$VideoRestrictionsPreview->MinDuration 						= $InsertionOrderLineItemVideoRestrictions->MinDuration;
					$VideoRestrictionsPreview->MaxDuration 						= $InsertionOrderLineItemVideoRestrictions->MaxDuration;
					$VideoRestrictionsPreview->ApisSupportedCommaSeparated 		= $InsertionOrderLineItemVideoRestrictions->ApisSupportedCommaSeparated;
					$VideoRestrictionsPreview->ProtocolsCommaSeparated 			= $InsertionOrderLineItemVideoRestrictions->ProtocolsCommaSeparated;
					$VideoRestrictionsPreview->DeliveryCommaSeparated 			= $InsertionOrderLineItemVideoRestrictions->DeliveryCommaSeparated;
					$VideoRestrictionsPreview->PlaybackCommaSeparated 			= $InsertionOrderLineItemVideoRestrictions->PlaybackCommaSeparated;
					$VideoRestrictionsPreview->StartDelay			 			= $InsertionOrderLineItemVideoRestrictions->StartDelay;
					$VideoRestrictionsPreview->Linearity			 			= $InsertionOrderLineItemVideoRestrictions->Linearity;
					$VideoRestrictionsPreview->FoldPos			 				= $InsertionOrderLineItemVideoRestrictions->FoldPos;
					$VideoRestrictionsPreview->MinHeight 						= $InsertionOrderLineItemVideoRestrictions->MinHeight;
					$VideoRestrictionsPreview->MinWidth 						= $InsertionOrderLineItemVideoRestrictions->MinWidth;
					$VideoRestrictionsPreview->Secure 							= $InsertionOrderLineItemVideoRestrictions->Secure;
					$VideoRestrictionsPreview->Optout 							= $InsertionOrderLineItemVideoRestrictions->Optout;
					$VideoRestrictionsPreview->Vertical 						= $InsertionOrderLineItemVideoRestrictions->Vertical;
					$VideoRestrictionsPreview->DateCreated 						= date("Y-m-d H:i:s");
					$VideoRestrictionsPreview->DateUpdated 						= date("Y-m-d H:i:s");
					
			    	$InsertionOrderLineItemVideoRestrictionsPreviewID = $InsertionOrderLineItemVideoRestrictionsPreviewFactory->saveInsertionOrderLineItemVideoRestrictionsPreview($VideoRestrictionsPreview);
	
					if ($update_data['type'] == 'InsertionOrderLineItemVideoRestrictionsID' && $update_data['id'] == $InsertionOrderLineItemVideoRestrictions->InsertionOrderLineItemVideoRestrictionsID):
					$return_val = array('InsertionOrderLineItemVideoRestrictionsPreviewID'=>$InsertionOrderLineItemVideoRestrictionsPreviewID,
							'InsertionOrderLineItemPreviewID'=>$InsertionOrderLineItemPreviewID,
							'InsertionOrderPreviewID'=>$InsertionOrderPreviewID);
					endif;
				endif;
				
			else:
			
				/*
				 * BANNER RESTRICTIONS
				 */
	
				$params = array();
				$params["InsertionOrderLineItemID"] = $banner_id;
				$InsertionOrderLineItemRestrictions = $InsertionOrderLineItemRestrictionsFactory->get_row($params);
	
				// may not be present
				if ($InsertionOrderLineItemRestrictions != null):
					$BannerRestrictionsPreview = new \model\InsertionOrderLineItemRestrictionsPreview();
	
				    $BannerRestrictionsPreview->InsertionOrderLineItemPreviewID 			= $InsertionOrderLineItemPreviewID;
				    $BannerRestrictionsPreview->GeoCountry 							= $InsertionOrderLineItemRestrictions->GeoCountry;
				    $BannerRestrictionsPreview->GeoState 							= $InsertionOrderLineItemRestrictions->GeoState;
				    $BannerRestrictionsPreview->GeoCity 							= $InsertionOrderLineItemRestrictions->GeoCity;
				    $BannerRestrictionsPreview->AdTagType 							= $InsertionOrderLineItemRestrictions->AdTagType;
				    $BannerRestrictionsPreview->AdPositionMinLeft 					= $InsertionOrderLineItemRestrictions->AdPositionMinLeft;
				    $BannerRestrictionsPreview->AdPositionMaxLeft 					= $InsertionOrderLineItemRestrictions->AdPositionMaxLeft;
				    $BannerRestrictionsPreview->AdPositionMinTop 					= $InsertionOrderLineItemRestrictions->AdPositionMinTop;
				    $BannerRestrictionsPreview->AdPositionMaxTop 					= $InsertionOrderLineItemRestrictions->AdPositionMaxTop;
				    $BannerRestrictionsPreview->FoldPos 							= $InsertionOrderLineItemRestrictions->FoldPos;
				    $BannerRestrictionsPreview->Freq 								= $InsertionOrderLineItemRestrictions->Freq;
				    $BannerRestrictionsPreview->Timezone 							= $InsertionOrderLineItemRestrictions->Timezone;
				    $BannerRestrictionsPreview->InIframe 							= $InsertionOrderLineItemRestrictions->InIframe;
				    $BannerRestrictionsPreview->MinScreenResolutionWidth 			= $InsertionOrderLineItemRestrictions->MinScreenResolutionWidth;
				    $BannerRestrictionsPreview->MaxScreenResolutionWidth 			= $InsertionOrderLineItemRestrictions->MaxScreenResolutionWidth;
				    $BannerRestrictionsPreview->MinScreenResolutionHeight 			= $InsertionOrderLineItemRestrictions->MinScreenResolutionHeight;
				    $BannerRestrictionsPreview->MaxScreenResolutionHeight 			= $InsertionOrderLineItemRestrictions->MaxScreenResolutionHeight;
				    $BannerRestrictionsPreview->HttpLanguage 						= $InsertionOrderLineItemRestrictions->HttpLanguage;
				    $BannerRestrictionsPreview->BrowserUserAgentGrep 				= $InsertionOrderLineItemRestrictions->BrowserUserAgentGrep;
				    $BannerRestrictionsPreview->Secure 								= $InsertionOrderLineItemRestrictions->Secure;
				    $BannerRestrictionsPreview->Optout 								= $InsertionOrderLineItemRestrictions->Optout;
				    $BannerRestrictionsPreview->Vertical 							= $InsertionOrderLineItemRestrictions->Vertical;
				    $BannerRestrictionsPreview->DateCreated 						= date("Y-m-d H:i:s");
				    $BannerRestrictionsPreview->DateUpdated 						= date("Y-m-d H:i:s");
	
				    $InsertionOrderLineItemRestrictionsPreviewID = $InsertionOrderLineItemRestrictionsPreviewFactory->saveInsertionOrderLineItemRestrictionsPreview($BannerRestrictionsPreview);
	
				    if ($update_data['type'] == 'InsertionOrderLineItemRestrictionsID' && $update_data['id'] == $InsertionOrderLineItemRestrictions->InsertionOrderLineItemRestrictionsID):
				    	$return_val = array('InsertionOrderLineItemRestrictionsPreviewID'=>$InsertionOrderLineItemRestrictionsPreviewID,
				    						'InsertionOrderLineItemPreviewID'=>$InsertionOrderLineItemPreviewID,
											'InsertionOrderPreviewID'=>$InsertionOrderPreviewID);
				    endif;
				    
			    endif;
			endif;
			
			/*
			 * PMP INVENTORY
			*/
			
			$params = array();
			$params["InsertionOrderLineItemID"] = $banner_id;
			$PmpDealPublisherWebsiteToInsertionOrderLineItemList = $PmpDealPublisherWebsiteToInsertionOrderLineItemFactory->get($params);
			
			if ($PmpDealPublisherWebsiteToInsertionOrderLineItemList != null):
			
				foreach ($PmpDealPublisherWebsiteToInsertionOrderLineItemList as $PmpDealPublisherWebsiteToInsertionOrderLineItem):
				
					$PmpDealPublisherWebsiteToInsertionOrderLineItemPreview = new \model\PmpDealPublisherWebsiteToInsertionOrderLineItemPreview();
					
					$PmpDealPublisherWebsiteToInsertionOrderLineItemPreview->PublisherWebsiteID 									= $PmpDealPublisherWebsiteToInsertionOrderLineItem->PublisherWebsiteID;
					$PmpDealPublisherWebsiteToInsertionOrderLineItemPreview->PublisherWebsiteLocal 									= $PmpDealPublisherWebsiteToInsertionOrderLineItem->PublisherWebsiteLocal;
					$PmpDealPublisherWebsiteToInsertionOrderLineItemPreview->PublisherWebsiteDescription 							= $PmpDealPublisherWebsiteToInsertionOrderLineItem->PublisherWebsiteDescription;
					$PmpDealPublisherWebsiteToInsertionOrderLineItemPreview->InsertionOrderLineItemPreviewID 						= $InsertionOrderLineItemPreviewID;
					$PmpDealPublisherWebsiteToInsertionOrderLineItemPreview->Enabled 												= $PmpDealPublisherWebsiteToInsertionOrderLineItem->Enabled;
					$PmpDealPublisherWebsiteToInsertionOrderLineItemPreview->DateCreated 											= date("Y-m-d H:i:s");
					$PmpDealPublisherWebsiteToInsertionOrderLineItemPreview->DateUpdated 											= date("Y-m-d H:i:s");
					
					$PmpDealPublisherWebsiteToInsertionOrderLineItemPreviewFactory->savePmpDealPublisherWebsiteToInsertionOrderLineItemPreview($PmpDealPublisherWebsiteToInsertionOrderLineItemPreview);
					
				endforeach;

			endif;
			
			/*
			 * SSP INVENTORY
			*/
			
			$params = array();
			$params["InsertionOrderLineItemID"] = $banner_id;
			$SspRtbChannelToInsertionOrderLineItemList = $SspRtbChannelToInsertionOrderLineItemFactory->get_row($params);
			
			if ($SspRtbChannelToInsertionOrderLineItemList != null):
				
				foreach ($SspRtbChannelToInsertionOrderLineItemList as $SspRtbChannelToInsertionOrderLineItem):
				
					$SspRtbChannelToInsertionOrderLineItemPreview = new \model\SspRtbChannelToInsertionOrderLineItemPreview();
					
					$SspRtbChannelToInsertionOrderLineItemPreview->SspPublisherChannelID 								= $SspRtbChannelToInsertionOrderLineItem->SspPublisherChannelID;
					$SspRtbChannelToInsertionOrderLineItemPreview->SspPublisherChannelDescription 						= $SspRtbChannelToInsertionOrderLineItem->SspPublisherChannelDescription;
					$SspRtbChannelToInsertionOrderLineItemPreview->SspExchange 											= $SspRtbChannelToInsertionOrderLineItem->SspExchange;
					$SspRtbChannelToInsertionOrderLineItemPreview->InsertionOrderLineItemPreviewID 						= $InsertionOrderLineItemPreviewID;
					$SspRtbChannelToInsertionOrderLineItemPreview->Enabled 												= $SspRtbChannelToInsertionOrderLineItem->Enabled;
					$SspRtbChannelToInsertionOrderLineItemPreview->DateCreated 											= date("Y-m-d H:i:s");
					$SspRtbChannelToInsertionOrderLineItemPreview->DateUpdated 											= date("Y-m-d H:i:s");
					
					$SspRtbChannelToInsertionOrderLineItemPreviewFactory->saveSspRtbChannelToInsertionOrderLineItemPreview($SspRtbChannelToInsertionOrderLineItemPreview);
					
				endforeach;
					
			endif;
			
		    /*
		     * DOMAIN EXCLUSIONS
		    */

		    $params = array();
		    $params["InsertionOrderLineItemID"] = $banner_id;
		    $InsertionOrderLineItemDomainExclusionList = $InsertionOrderLineItemDomainExclusionFactory->get($params);

		    foreach ($InsertionOrderLineItemDomainExclusionList as $InsertionOrderLineItemDomainExclusion):

		    	$BannerDomainExclusionPreview = new \model\InsertionOrderLineItemDomainExclusionPreview();

		    	$BannerDomainExclusionPreview->InsertionOrderLineItemPreviewID 		= $InsertionOrderLineItemPreviewID;
		    	$BannerDomainExclusionPreview->ExclusionType 					= $InsertionOrderLineItemDomainExclusion->ExclusionType;
		    	$BannerDomainExclusionPreview->DomainName 						= $InsertionOrderLineItemDomainExclusion->DomainName;
		    	$BannerDomainExclusionPreview->DateCreated 						= date("Y-m-d H:i:s");
		    	$BannerDomainExclusionPreview->DateUpdated 						= date("Y-m-d H:i:s");

		    	$InsertionOrderLineItemDomainExclusionPreviewID = $InsertionOrderLineItemDomainExclusionPreviewFactory->saveInsertionOrderLineItemDomainExclusionPreview($BannerDomainExclusionPreview);

		    	if ($update_data['type'] == 'InsertionOrderLineItemDomainExclusionID' && $update_data['id'] == $InsertionOrderLineItemDomainExclusion->InsertionOrderLineItemDomainExclusionID):
		    		$return_val = array('InsertionOrderLineItemDomainExclusionPreviewID'=>$InsertionOrderLineItemDomainExclusionPreviewID,
		    							'InsertionOrderLineItemPreviewID'=>$InsertionOrderLineItemPreviewID,
										'InsertionOrderPreviewID'=>$InsertionOrderPreviewID);
		    	endif;

		    endforeach;

		    /*
		     * DOMAIN EXCLUSIVE INCLUSIONS
		    */

		    $params = array();
		    $params["InsertionOrderLineItemID"] = $banner_id;
		    $InsertionOrderLineItemDomainExclusiveInclusionList = $InsertionOrderLineItemDomainExclusiveInclusionFactory->get($params);

		    foreach ($InsertionOrderLineItemDomainExclusiveInclusionList as $InsertionOrderLineItemDomainExclusiveInclusion):

			    $BannerDomainExclusiveInclusionPreview = new \model\InsertionOrderLineItemDomainExclusiveInclusionPreview();

			    $BannerDomainExclusiveInclusionPreview->InsertionOrderLineItemPreviewID 		= $InsertionOrderLineItemPreviewID;
			    $BannerDomainExclusiveInclusionPreview->InclusionType 					= $InsertionOrderLineItemDomainExclusiveInclusion->InclusionType;
			    $BannerDomainExclusiveInclusionPreview->DomainName 						= $InsertionOrderLineItemDomainExclusiveInclusion->DomainName;
			    $BannerDomainExclusiveInclusionPreview->DateCreated 					= date("Y-m-d H:i:s");
			    $BannerDomainExclusiveInclusionPreview->DateUpdated 					= date("Y-m-d H:i:s");

			    $InsertionOrderLineItemDomainExclusiveInclusionPreviewID = $InsertionOrderLineItemDomainExclusiveInclusionPreviewFactory->saveInsertionOrderLineItemDomainExclusiveInclusionPreview($BannerDomainExclusiveInclusionPreview);

			    if ($update_data['type'] == 'InsertionOrderLineItemDomainExclusiveInclusionID' && $update_data['id'] == $InsertionOrderLineItemDomainExclusiveInclusion->InsertionOrderLineItemDomainExclusiveInclusionID):
			    	$return_val = array('InsertionOrderLineItemDomainExclusiveInclusionPreviewID'=>$InsertionOrderLineItemDomainExclusiveInclusionPreviewID,
		    							'InsertionOrderLineItemPreviewID'=>$InsertionOrderLineItemPreviewID,
										'InsertionOrderPreviewID'=>$InsertionOrderPreviewID);
			    endif;


		    endforeach;


		endforeach;

		if (!$auth->getIsSuperAdmin($config) && $config['mail']['subscribe']['campaigns'] === true):
			// if this ad campaign was not created/edited by the admin, then send out a notification email
			$message = '<b>NginAd Demand Customer Campaign Edited by ' . $auth->getUserName() . '.</b><br /><br />';
			$message = $message.'<table border="0" width="10%">';
			$message = $message.'<tr><td><b>InsertionOrderID: </b></td><td>'.$InsertionOrder->InsertionOrderID.'</td></tr>';
			$message = $message.'<tr><td><b>UserID: </b></td><td>'.$InsertionOrder->UserID.'</td></tr>';
			$message = $message.'<tr><td><b>Name: </b></td><td>'.$InsertionOrder->Name.'</td></tr>';
			$message = $message.'<tr><td><b>StartDate: </b></td><td>'.$InsertionOrder->StartDate.'</td></tr>';
			$message = $message.'<tr><td><b>EndDate: </b></td><td>'.$InsertionOrder->EndDate.'</td></tr>';
			$message = $message.'<tr><td><b>Customer: </b></td><td>'.$InsertionOrder->Customer.'</td></tr>';
			$message = $message.'<tr><td><b>CustomerID: </b></td><td>'.$InsertionOrder->CustomerID.'</td></tr>';
			$message = $message.'<tr><td><b>MaxImpressions: </b></td><td>'.$InsertionOrder->MaxImpressions.'</td></tr>';
			$message = $message.'<tr><td><b>MaxSpend: </b></td><td>'.$InsertionOrder->MaxSpend.'</td></tr>';
			$message = $message.'</table>';
			
			$subject = "NginAd Demand Customer Campaign Edited by " . $auth->getUserName();

			$text = new Mime\Part($message);
			$text->type = Mime\Mime::TYPE_HTML;
			$text->charset = 'utf-8';
				
			$mimeMessage = new Mime\Message();
			$mimeMessage->setParts(array($text));
			$zf_message = new Message();
				
			$zf_message->addTo($config['mail']['admin-email']['email'], $config['mail']['admin-email']['name'])
			->addFrom($config['mail']['reply-to']['email'], $config['mail']['reply-to']['name'])
			->setSubject($subject)
			->setBody($mimeMessage);
			$mail_transport->send($zf_message);
		endif;
		
		return $return_val;

	}
}

?>