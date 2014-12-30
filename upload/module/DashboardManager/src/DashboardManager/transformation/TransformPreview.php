<?php
/**
 * CDNPAL NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2015 CDNPAL Ltd. All Rights Reserved
 * @license GPLv3
 */

namespace transformation;

use Zend\Mail\Message;
use Zend\Mime;

/*
 * Static class to transform AdCampaignBanner and AdCampaign and dependent objects
 * to and from their preview form
 */

class TransformPreview {

	public static function previewCheckBannerID($banner_id, $auth, $config, $mail_transport, $update_data = array()) {

		/*
		 * SHOULD WE CREATE A NEW PREVIEW MODE?
		*/
		if (self::doesPreviewBannerExist($banner_id, $auth) == false):

			$AdCampaignBannerFactory = \_factory\AdCampaignBanner::get_instance();
			$params = array();
			$params["AdCampaignBannerID"] = $banner_id;
			$params["Active"] = 1;
			$params["UserID"] = $auth->getEffectiveUserID();  // ACL permissions check equivalent
			$AdCampaignBanner = $AdCampaignBannerFactory->get_row($params);

			if ($AdCampaignBanner == null):
				//die("No Such Banner");
				$params["error"] = "No Such Banner";
				return $params;
			endif;

			return self::cloneAdCampaignIntoAdCampaignPreview($AdCampaignBanner->AdCampaignID, $auth, $config, $mail_transport, $update_data);

		else:

			return null;

		endif;

	}

	public static function previewCheckAdCampaignID($ad_campaign_id, $auth, $config, $mail_transport, $update_data = array()) {

		/*
		 * SHOULD WE CREATE A NEW PREVIEW MODE?
		*/
		if (self::doesPreviewAdCampaignExistForAdCampaign($ad_campaign_id, $auth) == false):

			$AdCampaignFactory = \_factory\AdCampaign::get_instance();
			$params = array();
			$params["AdCampaignID"] = $ad_campaign_id;
			$params["Active"] = 1;
			$params["UserID"] = $auth->getEffectiveUserID();  // ACL permissions check equivalent
			$AdCampaign = $AdCampaignFactory->get_row($params);

			if ($AdCampaign == null):
				//die("No Such Ad Campaign");
				$params["error"] = "No Such Ad Campaign";
				return $params;
			endif;

			return self::cloneAdCampaignIntoAdCampaignPreview($AdCampaign->AdCampaignID, $auth, $config, $mail_transport, $update_data);

		else:

			return null;

		endif;

	}

	public static function deletePreviewModeCampaign($ad_campaign_preview_id, $auth, $went_live = false) {

		$AdCampaignPreviewFactory = \_factory\AdCampaignPreview::get_instance();
		$params = array();
		$params["AdCampaignPreviewID"] = $ad_campaign_preview_id;
		$params["Active"] = 1;
		$AdCampaignPreview = $AdCampaignPreviewFactory->get_row($params);

		if ($AdCampaignPreview == null):
			die("No Such Preview Ad Campaign");
		endif;

		if ($went_live == true):
			$AdCampaignPreview->ChangeWentLive   	= 1;
			$AdCampaignPreview->WentLiveDate 		= date("Y-m-d H:i:s");
		else:
			$AdCampaignPreview->ChangeWentLive   	= 0;
			$AdCampaignPreview->WentLiveDate 		= date("Y-m-d H:i:s", 0);
		endif;

		$AdCampaignPreview->Active 					= 0;

		$AdCampaignPreviewCopy = new \model\AdCampaignPreview();

		$AdCampaignPreviewCopy->AdCampaignPreviewID 	= $ad_campaign_preview_id;

		if ($AdCampaignPreview->AdCampaignID != null):
			$AdCampaignPreviewCopy->AdCampaignID 		= $AdCampaignPreview->AdCampaignID;
		endif;

		$AdCampaignPreviewCopy->UserID 					= $AdCampaignPreview->UserID;
		$AdCampaignPreviewCopy->Name					= $AdCampaignPreview->Name;
		$AdCampaignPreviewCopy->StartDate				= $AdCampaignPreview->StartDate;
		$AdCampaignPreviewCopy->EndDate					= $AdCampaignPreview->EndDate;
		$AdCampaignPreviewCopy->Customer				= $AdCampaignPreview->Customer;
		$AdCampaignPreviewCopy->CustomerID 				= $AdCampaignPreview->CustomerID;
		$AdCampaignPreviewCopy->ImpressionsCounter 		= $AdCampaignPreview->ImpressionsCounter;
		$AdCampaignPreviewCopy->MaxImpressions 			= $AdCampaignPreview->MaxImpressions;
		$AdCampaignPreviewCopy->CurrentSpend 			= $AdCampaignPreview->CurrentSpend;
		$AdCampaignPreviewCopy->MaxSpend 				= $AdCampaignPreview->MaxSpend;
		$AdCampaignPreviewCopy->Active 					= 0;
		$AdCampaignPreviewCopy->DateUpdated   			= $AdCampaignPreview->DateUpdated;
		$AdCampaignPreviewCopy->ChangeWentLive   		= $AdCampaignPreview->ChangeWentLive;
		$AdCampaignPreviewCopy->WentLiveDate 			= $AdCampaignPreview->WentLiveDate;

		$AdCampaignPreviewFactory->saveAdCampaignPreview($AdCampaignPreviewCopy); // de-activate, not just deleted = 1

		$AdCampaignBannerPreviewFactory = \_factory\AdCampaignBannerPreview::get_instance();
		$params = array();
		$params["AdCampaignPreviewID"] = $AdCampaignPreview->AdCampaignPreviewID;
		$params["Active"] = 1;
		$AdCampaignBannerPreviewList = $AdCampaignBannerPreviewFactory->get($params);

		foreach ($AdCampaignBannerPreviewList as $AdCampaignBannerPreview):

			$AdCampaignBannerPreviewFactory->deActivateAdCampaignBannerPreview($AdCampaignBannerPreview->AdCampaignBannerPreviewID);

			$banner_preview = new \model\AdCampaignBannerPreview();

			$banner_preview->AdCampaignBannerPreviewID 	= $AdCampaignBannerPreview->AdCampaignBannerPreviewID;
			$banner_preview->AdCampaignPreviewID 		= $AdCampaignBannerPreview->AdCampaignPreviewID;
			$banner_preview->AdCampaignBannerID 		= $AdCampaignBannerPreview->AdCampaignBannerID;
			$banner_preview->ImpressionType 			= $AdCampaignBannerPreview->ImpressionType;
			$banner_preview->UserID 					= $AdCampaignBannerPreview->UserID;
			$banner_preview->Name 						= $AdCampaignBannerPreview->Name;
			$banner_preview->StartDate					= $AdCampaignBannerPreview->StartDate;
			$banner_preview->EndDate					= $AdCampaignBannerPreview->EndDate;
			$banner_preview->AdCampaignTypeID			= $AdCampaignBannerPreview->AdCampaignTypeID;
			$banner_preview->IsMobile					= $AdCampaignBannerPreview->IsMobile;
			$banner_preview->IABSize					= $AdCampaignBannerPreview->IABSize;
			$banner_preview->Height						= $AdCampaignBannerPreview->Height;
			$banner_preview->Width						= $AdCampaignBannerPreview->Width;
			$banner_preview->Weight						= $AdCampaignBannerPreview->Weight;
			$banner_preview->BidAmount					= $AdCampaignBannerPreview->BidAmount;
			$banner_preview->AdTag						= $AdCampaignBannerPreview->AdTag;
			$banner_preview->DeliveryType				= $AdCampaignBannerPreview->DeliveryType;
			$banner_preview->LandingPageTLD				= $AdCampaignBannerPreview->LandingPageTLD;
			$banner_preview->ImpressionsCounter			= $AdCampaignBannerPreview->ImpressionsCounter;
			$banner_preview->BidsCounter				= $AdCampaignBannerPreview->BidsCounter;
			$banner_preview->CurrentSpend				= $AdCampaignBannerPreview->CurrentSpend;
			$banner_preview->Active						= 0;
			$banner_preview->DateCreated				= $AdCampaignBannerPreview->DateCreated;
			$banner_preview->DateUpdated				= $AdCampaignBannerPreview->DateUpdated;
			if ($went_live == true):
				$banner_preview->ChangeWentLive				= 1;
				$banner_preview->WentLiveDate				= date("Y-m-d H:i:s");
			else:
				$banner_preview->ChangeWentLive   			= 0;
				$banner_preview->WentLiveDate 				= date("Y-m-d H:i:s", 0);
			endif;


			// de-active banner
			$AdCampaignBannerPreviewFactory->saveAdCampaignBannerPreview($banner_preview);

		endforeach;

	}

	public static function doesPreviewBannerExist($banner_preview_id, $auth) {

		$AdCampaignBannerPreviewFactory = \_factory\AdCampaignBannerPreview::get_instance();
		$params = array();
		$params["AdCampaignBannerPreviewID"] = $banner_preview_id;
		$params["Active"] = 1;
		$params["UserID"] = $auth->getEffectiveUserID();
		$AdCampaignBannerPreview = $AdCampaignBannerPreviewFactory->get_row($params);

		return $AdCampaignBannerPreview !== null;
	}

	public static function doesPreviewAdCampaignExist($ad_campaign_preview_id, $auth) {

		$AdCampaignPreviewFactory = \_factory\AdCampaignPreview::get_instance();
		$params = array();
		$params["AdCampaignPreviewID"] = $ad_campaign_preview_id;
		$params["Active"] = 1;
		$params["UserID"] = $auth->getEffectiveUserID();

		$AdCampaignPreview = $AdCampaignPreviewFactory->get_row($params);

		return $AdCampaignPreview !== null;
	}

	public static function doesPreviewAdCampaignExistForAdCampaign($ad_campaign_id, $auth) {

		$AdCampaignPreviewFactory = \_factory\AdCampaignPreview::get_instance();
		$params = array();
		$params["AdCampaignID"] = $ad_campaign_id;
		$params["Active"] = 1;
		$params["UserID"] = $auth->getEffectiveUserID();

		$AdCampaignPreview = $AdCampaignPreviewFactory->get_row($params);

		return $AdCampaignPreview !== null;
	}


	public static function cloneAdCampaignPreviewIntoAdCampaign($ad_campaign_preview_id, $auth, $config) {

		if ($ad_campaign_preview_id === null):
			return;
		endif;

		$AdCampaignPreviewFactory = \_factory\AdCampaignPreview::get_instance();
		$params = array();
		$params["AdCampaignPreviewID"] = $ad_campaign_preview_id;
		if (strpos($auth->getPrimaryRole(), $config['roles']['admin']) === false):
			die("You do not have permission to access this page");
		endif;
		$params["Active"] = 1;
		$AdCampaignPreview = $AdCampaignPreviewFactory->get_row($params);

		if ($AdCampaignPreview == null):
			die("Invalid AdCampaignPreview ID");
		endif;

		/*
		 * Clone AdCampaignPreview into AdCampaign
		*/

		$AdCampaignFactory = \_factory\AdCampaign::get_instance();
		$AdCampaign = new \model\AdCampaign();

		if ($AdCampaignPreview->AdCampaignID != null):
			$AdCampaign->AdCampaignID 		= $AdCampaignPreview->AdCampaignID;
		endif;

		$campaign_active = isset($AdCampaignPreview->Deleted) && $AdCampaignPreview->Deleted == 1 ? 0 : 1;

		$AdCampaign->UserID 			= $AdCampaignPreview->UserID;
		$AdCampaign->Name				= $AdCampaignPreview->Name;
		$AdCampaign->StartDate			= $AdCampaignPreview->StartDate;
		$AdCampaign->EndDate			= $AdCampaignPreview->EndDate;
		$AdCampaign->Customer			= $AdCampaignPreview->Customer;
		$AdCampaign->CustomerID 		= $AdCampaignPreview->CustomerID;
		$AdCampaign->ImpressionsCounter = 0;
		$AdCampaign->MaxImpressions 	= $AdCampaignPreview->MaxImpressions;
		$AdCampaign->CurrentSpend 		= 0;
		$AdCampaign->MaxSpend 			= $AdCampaignPreview->MaxSpend;
		$AdCampaign->Active 			= $campaign_active;
		$AdCampaign->DateUpdated   		= date("Y-m-d H:i:s");

		$ad_campaign_id = $AdCampaignFactory->saveAdCampaign($AdCampaign);

		$AdCampaignBannerPreviewFactory = \_factory\AdCampaignBannerPreview::get_instance();
		$params = array();
		$params["AdCampaignPreviewID"] = $ad_campaign_preview_id;
		/*
		 * get all banners, not just active ones, we want to set deleted banners to inactive on production also
		 * if they were flagged that way in preview mode
		 * $params["Active"] = 1;
		 */
		$AdCampaignBannerPreviewList = $AdCampaignBannerPreviewFactory->get($params);

		$AdCampaignBannerFactory = \_factory\AdCampaignBanner::get_instance();
		$AdCampaignBannerPreviewFactory = \_factory\AdCampaignBannerPreview::get_instance();
		$AdCampaignBannerRestrictionsFactory = \_factory\AdCampaignBannerRestrictions::get_instance();
		$AdCampaignBannerRestrictionsPreviewFactory = \_factory\AdCampaignBannerRestrictionsPreview::get_instance();
		
		$AdCampaignVideoRestrictionsFactory = \_factory\AdCampaignVideoRestrictions::get_instance();
		$AdCampaignVideoRestrictionsPreviewFactory = \_factory\AdCampaignVideoRestrictionsPreview::get_instance();
		
		$PublisherAdZoneFactory = \_factory\PublisherAdZone::get_instance();
		$LinkedBannerToAdZoneFactory = \_factory\LinkedBannerToAdZone::get_instance();
		$LinkedBannerToAdZonePreviewFactory = \_factory\LinkedBannerToAdZonePreview::get_instance();		
		$AdCampaignBannerDomainExclusionFactory = \_factory\AdCampaignBannerDomainExclusion::get_instance();
		$AdCampaignBannerDomainExclusionPreviewFactory = \_factory\AdCampaignBannerDomainExclusionPreview::get_instance();
		$AdCampaignBannerDomainExclusiveInclusionFactory = \_factory\AdCampaignBannerDomainExclusiveInclusion::get_instance();
		$AdCampaignBannerDomainExclusiveInclusionPreviewFactory = \_factory\AdCampaignBannerDomainExclusiveInclusionPreview::get_instance();

		foreach ($AdCampaignBannerPreviewList as $AdCampaignBannerPreview):

			$banner_preview_id = $AdCampaignBannerPreview->AdCampaignBannerPreviewID;

			$Banner = new \model\AdCampaignBanner();

			$Banner->AdCampaignID 				= $ad_campaign_id;

			if ($AdCampaignBannerPreview->AdCampaignBannerID != null):
				$Banner->AdCampaignBannerID 	= $AdCampaignBannerPreview->AdCampaignBannerID;
			endif;

			if ($campaign_active == 0):
				$banner_active = 0;
			else:
				$banner_active = $AdCampaignBannerPreview->Active;
			endif;

			$Banner->UserID 					= $AdCampaignBannerPreview->UserID;
			$Banner->Name 						= $AdCampaignBannerPreview->Name;
			$Banner->ImpressionType 			= $AdCampaignBannerPreview->ImpressionType;
			$Banner->StartDate 					= $AdCampaignBannerPreview->StartDate;
			$Banner->EndDate 					= $AdCampaignBannerPreview->EndDate;
			$Banner->AdCampaignTypeID 			= $AdCampaignBannerPreview->AdCampaignTypeID;
			$Banner->IsMobile 					= $AdCampaignBannerPreview->IsMobile;
			$Banner->IABSize 					= $AdCampaignBannerPreview->IABSize;
			$Banner->Height 					= $AdCampaignBannerPreview->Height;
			$Banner->Width 						= $AdCampaignBannerPreview->Width;
			$Banner->Weight 					= $AdCampaignBannerPreview->Weight;
			$Banner->BidAmount 					= $AdCampaignBannerPreview->BidAmount;
			$Banner->AdTag 						= $AdCampaignBannerPreview->AdTag;
			$Banner->DeliveryType 				= $AdCampaignBannerPreview->DeliveryType;
			$Banner->LandingPageTLD 			= $AdCampaignBannerPreview->LandingPageTLD;
			$Banner->ImpressionsCounter 		= $AdCampaignBannerPreview->ImpressionsCounter;
			$Banner->BidsCounter 				= $AdCampaignBannerPreview->BidsCounter;
			$Banner->CurrentSpend 				= $AdCampaignBannerPreview->CurrentSpend;
			$Banner->Active 					= $banner_active;
			$Banner->DateCreated 				= date("Y-m-d H:i:s");

			// if the banner was deleted and there is no corresponding production banner, don't save it
			if ($banner_active == 0 && $AdCampaignBannerPreview->AdCampaignBannerID == null):
				continue;
			endif;

			$banner_id = $AdCampaignBannerFactory->saveAdCampaignBanner($Banner);

			// if the banner was deleted there's no reason to continue to copy it's properties here
			if ($banner_active == 0):
				continue;
			endif;
			
			if ($Banner->ImpressionType == 'video'):

				/*
				 * VIDEO RESTRICTIONS
				*/
	
				$params = array();
				$params["AdCampaignBannerPreviewID"] = $banner_preview_id;
				$AdCampaignVideoRestrictionsPreview = $AdCampaignVideoRestrictionsPreviewFactory->get_row($params);
	
				if ($AdCampaignVideoRestrictionsPreview != null):
	
					$VideoRestrictions = new \model\AdCampaignVideoRestrictions();
					$VideoRestrictions->AdCampaignBannerID 					= $banner_id;
					$VideoRestrictions->GeoCountry 							= $AdCampaignVideoRestrictionsPreview->GeoCountry;
					$VideoRestrictions->GeoState 							= $AdCampaignVideoRestrictionsPreview->GeoState;
					$VideoRestrictions->GeoCity 							= $AdCampaignVideoRestrictionsPreview->GeoCity;
					$VideoRestrictions->MimesCommaSeparated 				= $AdCampaignVideoRestrictionsPreview->MimesCommaSeparated;
					$VideoRestrictions->MinDuration 						= $AdCampaignVideoRestrictionsPreview->MinDuration;
					$VideoRestrictions->MaxDuration 						= $AdCampaignVideoRestrictionsPreview->MaxDuration;
					$VideoRestrictions->ApisSupportedCommaSeparated 		= $AdCampaignVideoRestrictionsPreview->ApisSupportedCommaSeparated;
					$VideoRestrictions->ProtocolsCommaSeparated 			= $AdCampaignVideoRestrictionsPreview->ProtocolsCommaSeparated;
					$VideoRestrictions->DeliveryCommaSeparated 				= $AdCampaignVideoRestrictionsPreview->DeliveryCommaSeparated;
					$VideoRestrictions->PlaybackCommaSeparated 				= $AdCampaignVideoRestrictionsPreview->PlaybackCommaSeparated;
					$VideoRestrictions->StartDelay			 				= $AdCampaignVideoRestrictionsPreview->StartDelay;
					$VideoRestrictions->Linearity			 				= $AdCampaignVideoRestrictionsPreview->Linearity;
					$VideoRestrictions->FoldPos			 					= $AdCampaignVideoRestrictionsPreview->FoldPos;
					$VideoRestrictions->MinHeight 							= $AdCampaignVideoRestrictionsPreview->MinHeight;
					$VideoRestrictions->MinWidth 							= $AdCampaignVideoRestrictionsPreview->MinWidth;
					$VideoRestrictions->PmpEnable 							= $AdCampaignVideoRestrictionsPreview->PmpEnable;
					$VideoRestrictions->Secure 								= $AdCampaignVideoRestrictionsPreview->Secure;
					$VideoRestrictions->Optout 								= $AdCampaignVideoRestrictionsPreview->Optout;
					$VideoRestrictions->Vertical 							= $AdCampaignVideoRestrictionsPreview->Vertical;
					$VideoRestrictions->DateCreated 						= date("Y-m-d H:i:s");
					$VideoRestrictions->DateUpdated 						= date("Y-m-d H:i:s");
	
					$AdCampaignVideoRestrictionsFactory->saveAdCampaignVideoRestrictions($VideoRestrictions);
					$AdCampaignBannerRestrictionsFactory->deleteAdCampaignBannerRestrictions($banner_id);
				endif;
				
			else:
			
				/*
				 * BANNER RESTRICTIONS
				*/
				
				$params = array();
				$params["AdCampaignBannerPreviewID"] = $banner_preview_id;
				$AdCampaignBannerRestrictionsPreview = $AdCampaignBannerRestrictionsPreviewFactory->get_row($params);
				
				if ($AdCampaignBannerRestrictionsPreview != null):
					
					$BannerRestrictions = new \model\AdCampaignBannerRestrictions();
					$BannerRestrictions->AdCampaignBannerID 				= $banner_id;
					$BannerRestrictions->GeoCountry 						= $AdCampaignBannerRestrictionsPreview->GeoCountry;
					$BannerRestrictions->GeoState 							= $AdCampaignBannerRestrictionsPreview->GeoState;
					$BannerRestrictions->GeoCity 							= $AdCampaignBannerRestrictionsPreview->GeoCity;
					$BannerRestrictions->AdTagType 							= $AdCampaignBannerRestrictionsPreview->AdTagType;
					$BannerRestrictions->AdPositionMinLeft 					= $AdCampaignBannerRestrictionsPreview->AdPositionMinLeft;
					$BannerRestrictions->AdPositionMaxLeft 					= $AdCampaignBannerRestrictionsPreview->AdPositionMaxLeft;
					$BannerRestrictions->AdPositionMinTop 					= $AdCampaignBannerRestrictionsPreview->AdPositionMinTop;
					$BannerRestrictions->AdPositionMaxTop 					= $AdCampaignBannerRestrictionsPreview->AdPositionMaxTop;
					$BannerRestrictions->FoldPos 							= $AdCampaignBannerRestrictionsPreview->FoldPos;
					$BannerRestrictions->Freq 								= $AdCampaignBannerRestrictionsPreview->Freq;
					$BannerRestrictions->Timezone 							= $AdCampaignBannerRestrictionsPreview->Timezone;
					$BannerRestrictions->InIframe 							= $AdCampaignBannerRestrictionsPreview->InIframe;
					$BannerRestrictions->InMultipleNestedIframes 			= $AdCampaignBannerRestrictionsPreview->InMultipleNestedIframes;
					$BannerRestrictions->MinScreenResolutionWidth 			= $AdCampaignBannerRestrictionsPreview->MinScreenResolutionWidth;
					$BannerRestrictions->MaxScreenResolutionWidth 			= $AdCampaignBannerRestrictionsPreview->MaxScreenResolutionWidth;
					$BannerRestrictions->MinScreenResolutionHeight 			= $AdCampaignBannerRestrictionsPreview->MinScreenResolutionHeight;
					$BannerRestrictions->MaxScreenResolutionHeight 			= $AdCampaignBannerRestrictionsPreview->MaxScreenResolutionHeight;
					$BannerRestrictions->HttpLanguage 						= $AdCampaignBannerRestrictionsPreview->HttpLanguage;
					$BannerRestrictions->BrowserUserAgentGrep 				= $AdCampaignBannerRestrictionsPreview->BrowserUserAgentGrep;
					$BannerRestrictions->CookieGrep 						= $AdCampaignBannerRestrictionsPreview->CookieGrep;
					$BannerRestrictions->PmpEnable 							= $AdCampaignBannerRestrictionsPreview->PmpEnable;
					$BannerRestrictions->Secure 							= $AdCampaignBannerRestrictionsPreview->Secure;
					$BannerRestrictions->Optout 							= $AdCampaignBannerRestrictionsPreview->Optout;
					$BannerRestrictions->Vertical 							= $AdCampaignBannerRestrictionsPreview->Vertical;
					$BannerRestrictions->DateCreated 						= date("Y-m-d H:i:s");
					$BannerRestrictions->DateUpdated 						= date("Y-m-d H:i:s");
					
					$AdCampaignBannerRestrictionsFactory->saveAdCampaignBannerRestrictions($BannerRestrictions);
					$AdCampaignVideoRestrictionsFactory->deleteAdCampaignVideoRestrictions($banner_id);
				endif;
					
			endif;
			
			/*
			 * LINKED BANNER TO AD ZONE
			*/
			
			// first delete the existing ones, then re-insert
			$LinkedBannerToAdZoneFactory->deleteLinkedBannerToAdZone($banner_id);
			
			$params = array();
			$params["AdCampaignBannerPreviewID"] = $banner_preview_id;
			$LinkedBannerToAdZonePreviewList = $LinkedBannerToAdZonePreviewFactory->get($params);
			
			foreach ($LinkedBannerToAdZonePreviewList as $LinkedBannerToAdZonePreview):
			
				$LinkedBannerToAdZone = new \model\LinkedBannerToAdZone();
				
				$LinkedBannerToAdZone->AdCampaignBannerID 	= $banner_id;
				$LinkedBannerToAdZone->PublisherAdZoneID 	= $LinkedBannerToAdZonePreview->PublisherAdZoneID;
				$LinkedBannerToAdZone->Weight 				= $LinkedBannerToAdZonePreview->Weight;
				$LinkedBannerToAdZone->DateCreated 			= date("Y-m-d H:i:s");
				$LinkedBannerToAdZone->DateUpdated 			= date("Y-m-d H:i:s");
				
				$LinkedBannerToAdZoneFactory->saveLinkedBannerToAdZone($LinkedBannerToAdZone);
				
				$PublisherAdZoneFactory->updatePublisherAdZonePublisherAdZoneType($LinkedBannerToAdZone->PublisherAdZoneID, AD_TYPE_CONTRACT);

			endforeach;
			
			/*
			 * DOMAIN EXCLUSIONS
			*/

			// first delete the existing ones, then re-insert
			$AdCampaignBannerDomainExclusionFactory->deleteAdCampaignBannerDomainExclusionByBannerID($banner_id);

			$params = array();
			$params["AdCampaignBannerPreviewID"] = $banner_preview_id;
			$AdCampaignBannerDomainExclusionPreviewList = $AdCampaignBannerDomainExclusionPreviewFactory->get($params);

			foreach ($AdCampaignBannerDomainExclusionPreviewList as $AdCampaignBannerDomainExclusionPreview):

				$BannerDomainExclusion = new \model\AdCampaignBannerDomainExclusion();

				$BannerDomainExclusion->AdCampaignBannerID 	= $banner_id;
				$BannerDomainExclusion->ExclusionType 		= $AdCampaignBannerDomainExclusionPreview->ExclusionType;
				$BannerDomainExclusion->DomainName 			= $AdCampaignBannerDomainExclusionPreview->DomainName;
				$BannerDomainExclusion->DateCreated 		= date("Y-m-d H:i:s");
				$BannerDomainExclusion->DateUpdated 		= date("Y-m-d H:i:s");

				$AdCampaignBannerDomainExclusionFactory->saveAdCampaignBannerDomainExclusion($BannerDomainExclusion);

			endforeach;

			/*
			 * DOMAIN EXCLUSIVE INCLUSIONS
			*/

			// first delete the existing ones, then re-insert
			$AdCampaignBannerDomainExclusiveInclusionFactory->deleteAdCampaignBannerDomainExclusiveInclusionByBannerID($banner_id);

			$params = array();
			$params["AdCampaignBannerPreviewID"] = $banner_preview_id;
			$AdCampaignBannerDomainExclusiveInclusionPreviewList = $AdCampaignBannerDomainExclusiveInclusionPreviewFactory->get($params);

			foreach ($AdCampaignBannerDomainExclusiveInclusionPreviewList as $AdCampaignBannerDomainExclusiveInclusionPreview):

				$BannerDomainExclusiveInclusion = new \model\AdCampaignBannerDomainExclusiveInclusion();

				$BannerDomainExclusiveInclusion->AdCampaignBannerID 	= $banner_id;
				$BannerDomainExclusiveInclusion->InclusionType 			= $AdCampaignBannerDomainExclusiveInclusionPreview->InclusionType;
				$BannerDomainExclusiveInclusion->DomainName 			= $AdCampaignBannerDomainExclusiveInclusionPreview->DomainName;
				$BannerDomainExclusiveInclusion->DateCreated 			= date("Y-m-d H:i:s");
				$BannerDomainExclusiveInclusion->DateUpdated 			= date("Y-m-d H:i:s");

				$AdCampaignBannerDomainExclusiveInclusionFactory->saveAdCampaignBannerDomainExclusiveInclusion($BannerDomainExclusiveInclusion);

			endforeach;


		endforeach;

		return $ad_campaign_id;
		
	}


	public static function cloneAdCampaignIntoAdCampaignPreview($ad_campaign_id, $auth, $config, $mail_transport, $update_data) {

		$return_val = array();

		if ($ad_campaign_id === null):
			return null;
		endif;

		$AdCampaignFactory = \_factory\AdCampaign::get_instance();
		$params = array();
		$params["AdCampaignID"] = $ad_campaign_id;
		$params["UserID"] = $auth->getEffectiveUserID();
		$params["Active"] = 1;
		$AdCampaign = $AdCampaignFactory->get_row($params);

		if ($AdCampaign == null):
			//die("Invalid AdCampaign ID");
			$params["error"] = "Invalid AdCampaign ID";
			return $params;
		endif;

		/*
		 * Clone AdCampaign into AdCampaignPreview
		 */

		$AdCampaignPreviewFactory = \_factory\AdCampaignPreview::get_instance();
		$AdCampaignPreview = new \model\AdCampaignPreview();

		$AdCampaignPreview->AdCampaignID 		= $AdCampaign->AdCampaignID;
		$AdCampaignPreview->UserID 				= $AdCampaign->UserID;
		$AdCampaignPreview->Name				= $AdCampaign->Name;
		$AdCampaignPreview->StartDate			= $AdCampaign->StartDate;
		$AdCampaignPreview->EndDate				= $AdCampaign->EndDate;
		$AdCampaignPreview->Customer			= $AdCampaign->Customer;
		$AdCampaignPreview->CustomerID 			= $AdCampaign->CustomerID;
		$AdCampaignPreview->ImpressionsCounter  = 0;
		$AdCampaignPreview->MaxImpressions 		= $AdCampaign->MaxImpressions;
		$AdCampaignPreview->CurrentSpend 		= 0;
		$AdCampaignPreview->MaxSpend 			= $AdCampaign->MaxSpend;
		$AdCampaignPreview->Active 				= 1;
		$AdCampaignPreview->DateCreated   		= date("Y-m-d H:i:s");
		$AdCampaignPreview->DateUpdated   		= date("Y-m-d H:i:s");
		$AdCampaignPreview->ChangeWentLive   	= 0;

		$AdCampaignPreviewID = $AdCampaignPreviewFactory->saveAdCampaignPreview($AdCampaignPreview);

		if ($update_data['type'] == 'AdCampaignID'):
			$return_val = array('AdCampaignPreviewID'=>$AdCampaignPreviewID);
		endif;

		$AdCampaignBannerFactory = \_factory\AdCampaignBanner::get_instance();
		$params = array();
		$params["AdCampaignID"] = $AdCampaign->AdCampaignID;
		$params["Active"] = 1;
		$AdCampaignBannerList = $AdCampaignBannerFactory->get($params);

		$AdCampaignBannerPreviewFactory = \_factory\AdCampaignBannerPreview::get_instance();
		$AdCampaignBannerRestrictionsFactory = \_factory\AdCampaignBannerRestrictions::get_instance();
		$AdCampaignBannerRestrictionsPreviewFactory = \_factory\AdCampaignBannerRestrictionsPreview::get_instance();
		
		$AdCampaignVideoRestrictionsFactory = \_factory\AdCampaignVideoRestrictions::get_instance();
		$AdCampaignVideoRestrictionsPreviewFactory = \_factory\AdCampaignVideoRestrictionsPreview::get_instance();
		
		$LinkedBannerToAdZoneFactory = \_factory\LinkedBannerToAdZone::get_instance();
		$LinkedBannerToAdZonePreviewFactory = \_factory\LinkedBannerToAdZonePreview::get_instance();
		$AdCampaignBannerDomainExclusionFactory = \_factory\AdCampaignBannerDomainExclusion::get_instance();
		$AdCampaignBannerDomainExclusionPreviewFactory = \_factory\AdCampaignBannerDomainExclusionPreview::get_instance();
		$AdCampaignBannerDomainExclusiveInclusionFactory = \_factory\AdCampaignBannerDomainExclusiveInclusion::get_instance();
		$AdCampaignBannerDomainExclusiveInclusionPreviewFactory = \_factory\AdCampaignBannerDomainExclusiveInclusionPreview::get_instance();

		foreach ($AdCampaignBannerList as $AdCampaignBanner):

			$banner_id = $AdCampaignBanner->AdCampaignBannerID;

			
			$BannerPreview = new \model\AdCampaignBannerPreview();

			$BannerPreview->AdCampaignPreviewID 		= $AdCampaignPreviewID;
			$BannerPreview->AdCampaignBannerID 			= $AdCampaignBanner->AdCampaignBannerID;
			$BannerPreview->UserID 						= $AdCampaignBanner->UserID;
			$BannerPreview->Name 						= $AdCampaignBanner->Name;
			$BannerPreview->ImpressionType 				= $AdCampaignBanner->ImpressionType;
			$BannerPreview->StartDate 					= $AdCampaignBanner->StartDate;
			$BannerPreview->EndDate 					= $AdCampaignBanner->EndDate;
			$BannerPreview->AdCampaignTypeID 			= $AdCampaignBanner->AdCampaignTypeID;
			$BannerPreview->IsMobile 					= $AdCampaignBanner->IsMobile;
			$BannerPreview->IABSize 					= $AdCampaignBanner->IABSize;
			$BannerPreview->Height 						= $AdCampaignBanner->Height;
			$BannerPreview->Width 						= $AdCampaignBanner->Width;
			$BannerPreview->Weight 						= $AdCampaignBanner->Weight;
			$BannerPreview->BidAmount 					= $AdCampaignBanner->BidAmount;
			$BannerPreview->AdTag 						= $AdCampaignBanner->AdTag;
			$BannerPreview->DeliveryType				= $AdCampaignBanner->DeliveryType;
			$BannerPreview->LandingPageTLD 				= $AdCampaignBanner->LandingPageTLD;
			$BannerPreview->ImpressionsCounter 			= $AdCampaignBanner->ImpressionsCounter;
			$BannerPreview->BidsCounter 				= $AdCampaignBanner->BidsCounter;
			$BannerPreview->CurrentSpend 				= $AdCampaignBanner->CurrentSpend;
			$BannerPreview->Active 						= $AdCampaignBanner->Active;
			$BannerPreview->DateCreated 				= date("Y-m-d H:i:s");
			$BannerPreview->DateUpdated 				= date("Y-m-d H:i:s");
			$BannerPreview->ChangeWentLive 				= 0;

			$AdCampaignBannerPreviewID = $AdCampaignBannerPreviewFactory->saveAdCampaignBannerPreview($BannerPreview);

			if ($update_data['type'] == 'AdCampaignBannerID' && $update_data['id'] == $banner_id):
				$return_val = array('AdCampaignBannerPreviewID'=>$AdCampaignBannerPreviewID,
									'AdCampaignPreviewID'=>$AdCampaignPreviewID);

			endif;

			
			if ($BannerPreview->ImpressionType == 'video'):
			
				/*
				 * VIDEO RESTRICTIONS
				*/
			
				$params = array();
				$params["AdCampaignBannerID"] = $banner_id;
				$AdCampaignVideoRestrictions = $AdCampaignVideoRestrictionsFactory->get_row($params);

				if ($AdCampaignVideoRestrictions != null):
				
					$VideoRestrictionsPreview = new \model\AdCampaignVideoRestrictionsPreview();
					$VideoRestrictionsPreview->AdCampaignBannerPreviewID 				= $AdCampaignBannerPreviewID;
					$VideoRestrictionsPreview->GeoCountry 						= $AdCampaignVideoRestrictions->GeoCountry;
					$VideoRestrictionsPreview->GeoState 						= $AdCampaignVideoRestrictions->GeoState;
					$VideoRestrictionsPreview->GeoCity 							= $AdCampaignVideoRestrictions->GeoCity;
					$VideoRestrictionsPreview->MimesCommaSeparated 				= $AdCampaignVideoRestrictions->MimesCommaSeparated;
					$VideoRestrictionsPreview->MinDuration 						= $AdCampaignVideoRestrictions->MinDuration;
					$VideoRestrictionsPreview->MaxDuration 						= $AdCampaignVideoRestrictions->MaxDuration;
					$VideoRestrictionsPreview->ApisSupportedCommaSeparated 		= $AdCampaignVideoRestrictions->ApisSupportedCommaSeparated;
					$VideoRestrictionsPreview->ProtocolsCommaSeparated 			= $AdCampaignVideoRestrictions->ProtocolsCommaSeparated;
					$VideoRestrictionsPreview->DeliveryCommaSeparated 			= $AdCampaignVideoRestrictions->DeliveryCommaSeparated;
					$VideoRestrictionsPreview->PlaybackCommaSeparated 			= $AdCampaignVideoRestrictions->PlaybackCommaSeparated;
					$VideoRestrictionsPreview->StartDelay			 			= $AdCampaignVideoRestrictions->StartDelay;
					$VideoRestrictionsPreview->Linearity			 			= $AdCampaignVideoRestrictions->Linearity;
					$VideoRestrictionsPreview->FoldPos			 				= $AdCampaignVideoRestrictions->FoldPos;
					$VideoRestrictionsPreview->MinHeight 						= $AdCampaignVideoRestrictions->MinHeight;
					$VideoRestrictionsPreview->MinWidth 						= $AdCampaignVideoRestrictions->MinWidth;
					$VideoRestrictionsPreview->PmpEnable 						= $AdCampaignVideoRestrictions->PmpEnable;
					$VideoRestrictionsPreview->Secure 							= $AdCampaignVideoRestrictions->Secure;
					$VideoRestrictionsPreview->Optout 							= $AdCampaignVideoRestrictions->Optout;
					$VideoRestrictionsPreview->Vertical 						= $AdCampaignVideoRestrictions->Vertical;
					$VideoRestrictionsPreview->DateCreated 						= date("Y-m-d H:i:s");
					$VideoRestrictionsPreview->DateUpdated 						= date("Y-m-d H:i:s");
					
			    	$AdCampaignVideoRestrictionsPreviewID = $AdCampaignVideoRestrictionsPreviewFactory->saveAdCampaignVideoRestrictionsPreview($VideoRestrictionsPreview);
	
					if ($update_data['type'] == 'AdCampaignVideoRestrictionsID' && $update_data['id'] == $AdCampaignVideoRestrictions->AdCampaignVideoRestrictionsID):
					$return_val = array('AdCampaignVideoRestrictionsPreviewID'=>$AdCampaignVideoRestrictionsPreviewID,
							'AdCampaignBannerPreviewID'=>$AdCampaignBannerPreviewID,
							'AdCampaignPreviewID'=>$AdCampaignPreviewID);
					endif;
				endif;
				
			else:
			
				/*
				 * BANNER RESTRICTIONS
				 */
	
				$params = array();
				$params["AdCampaignBannerID"] = $banner_id;
				$AdCampaignBannerRestrictions = $AdCampaignBannerRestrictionsFactory->get_row($params);
	
				// may not be present
				if ($AdCampaignBannerRestrictions != null):
					$BannerRestrictionsPreview = new \model\AdCampaignBannerRestrictionsPreview();
	
				    $BannerRestrictionsPreview->AdCampaignBannerPreviewID 			= $AdCampaignBannerPreviewID;
				    $BannerRestrictionsPreview->GeoCountry 							= $AdCampaignBannerRestrictions->GeoCountry;
				    $BannerRestrictionsPreview->GeoState 							= $AdCampaignBannerRestrictions->GeoState;
				    $BannerRestrictionsPreview->GeoCity 							= $AdCampaignBannerRestrictions->GeoCity;
				    $BannerRestrictionsPreview->AdTagType 							= $AdCampaignBannerRestrictions->AdTagType;
				    $BannerRestrictionsPreview->AdPositionMinLeft 					= $AdCampaignBannerRestrictions->AdPositionMinLeft;
				    $BannerRestrictionsPreview->AdPositionMaxLeft 					= $AdCampaignBannerRestrictions->AdPositionMaxLeft;
				    $BannerRestrictionsPreview->AdPositionMinTop 					= $AdCampaignBannerRestrictions->AdPositionMinTop;
				    $BannerRestrictionsPreview->AdPositionMaxTop 					= $AdCampaignBannerRestrictions->AdPositionMaxTop;
				    $BannerRestrictionsPreview->FoldPos 							= $AdCampaignBannerRestrictions->FoldPos;
				    $BannerRestrictionsPreview->Freq 								= $AdCampaignBannerRestrictions->Freq;
				    $BannerRestrictionsPreview->Timezone 							= $AdCampaignBannerRestrictions->Timezone;
				    $BannerRestrictionsPreview->InIframe 							= $AdCampaignBannerRestrictions->InIframe;
				    $BannerRestrictionsPreview->InMultipleNestedIframes 			= $AdCampaignBannerRestrictions->InMultipleNestedIframes;
				    $BannerRestrictionsPreview->MinScreenResolutionWidth 			= $AdCampaignBannerRestrictions->MinScreenResolutionWidth;
				    $BannerRestrictionsPreview->MaxScreenResolutionWidth 			= $AdCampaignBannerRestrictions->MaxScreenResolutionWidth;
				    $BannerRestrictionsPreview->MinScreenResolutionHeight 			= $AdCampaignBannerRestrictions->MinScreenResolutionHeight;
				    $BannerRestrictionsPreview->MaxScreenResolutionHeight 			= $AdCampaignBannerRestrictions->MaxScreenResolutionHeight;
				    $BannerRestrictionsPreview->HttpLanguage 						= $AdCampaignBannerRestrictions->HttpLanguage;
				    $BannerRestrictionsPreview->BrowserUserAgentGrep 				= $AdCampaignBannerRestrictions->BrowserUserAgentGrep;
				    $BannerRestrictionsPreview->CookieGrep 							= $AdCampaignBannerRestrictions->CookieGrep;
				    $BannerRestrictionsPreview->PmpEnable 							= $AdCampaignBannerRestrictions->PmpEnable;
				    $BannerRestrictionsPreview->Secure 								= $AdCampaignBannerRestrictions->Secure;
				    $BannerRestrictionsPreview->Optout 								= $AdCampaignBannerRestrictions->Optout;
				    $BannerRestrictionsPreview->Vertical 							= $AdCampaignBannerRestrictions->Vertical;
				    $BannerRestrictionsPreview->DateCreated 						= date("Y-m-d H:i:s");
				    $BannerRestrictionsPreview->DateUpdated 						= date("Y-m-d H:i:s");
	
				    $AdCampaignBannerRestrictionsPreviewID = $AdCampaignBannerRestrictionsPreviewFactory->saveAdCampaignBannerRestrictionsPreview($BannerRestrictionsPreview);
	
				    if ($update_data['type'] == 'AdCampaignBannerRestrictionsID' && $update_data['id'] == $AdCampaignBannerRestrictions->AdCampaignBannerRestrictionsID):
				    	$return_val = array('AdCampaignBannerRestrictionsPreviewID'=>$AdCampaignBannerRestrictionsPreviewID,
				    						'AdCampaignBannerPreviewID'=>$AdCampaignBannerPreviewID,
											'AdCampaignPreviewID'=>$AdCampaignPreviewID);
				    endif;
				    
			    endif;
			endif;
			
			/*
			 * LINKED BANNER TO AD ZONE
			*/
				
			$params = array();
			$params["AdCampaignBannerID"] = $banner_id;
			$LinkedBannerToAdZoneList = $LinkedBannerToAdZoneFactory->get($params);
				
			foreach ($LinkedBannerToAdZoneList as $LinkedBannerToAdZone):
					
				$LinkedBannerToAdZonePreview = new \model\LinkedBannerToAdZonePreview();
				
				$LinkedBannerToAdZonePreview->AdCampaignBannerPreviewID 	= $AdCampaignBannerPreviewID;
				$LinkedBannerToAdZonePreview->PublisherAdZoneID 			= $LinkedBannerToAdZone->PublisherAdZoneID;
				$LinkedBannerToAdZonePreview->Weight 						= $LinkedBannerToAdZone->Weight;
				$LinkedBannerToAdZonePreview->DateCreated 					= date("Y-m-d H:i:s");
				$LinkedBannerToAdZonePreview->DateUpdated 					= date("Y-m-d H:i:s");
				
				$LinkedBannerToAdZonePreviewFactory->saveLinkedBannerToAdZonePreview($LinkedBannerToAdZonePreview);
				
			endforeach;
			
		    /*
		     * DOMAIN EXCLUSIONS
		    */

		    $params = array();
		    $params["AdCampaignBannerID"] = $banner_id;
		    $AdCampaignBannerDomainExclusionList = $AdCampaignBannerDomainExclusionFactory->get($params);

		    foreach ($AdCampaignBannerDomainExclusionList as $AdCampaignBannerDomainExclusion):

		    	$BannerDomainExclusionPreview = new \model\AdCampaignBannerDomainExclusionPreview();

		    	$BannerDomainExclusionPreview->AdCampaignBannerPreviewID 		= $AdCampaignBannerPreviewID;
		    	$BannerDomainExclusionPreview->ExclusionType 					= $AdCampaignBannerDomainExclusion->ExclusionType;
		    	$BannerDomainExclusionPreview->DomainName 						= $AdCampaignBannerDomainExclusion->DomainName;
		    	$BannerDomainExclusionPreview->DateCreated 						= date("Y-m-d H:i:s");
		    	$BannerDomainExclusionPreview->DateUpdated 						= date("Y-m-d H:i:s");

		    	$AdCampaignBannerDomainExclusionPreviewID = $AdCampaignBannerDomainExclusionPreviewFactory->saveAdCampaignBannerDomainExclusionPreview($BannerDomainExclusionPreview);

		    	if ($update_data['type'] == 'AdCampaignBannerDomainExclusionID' && $update_data['id'] == $AdCampaignBannerDomainExclusion->AdCampaignBannerDomainExclusionID):
		    		$return_val = array('AdCampaignBannerDomainExclusionPreviewID'=>$AdCampaignBannerDomainExclusionPreviewID,
		    							'AdCampaignBannerPreviewID'=>$AdCampaignBannerPreviewID,
										'AdCampaignPreviewID'=>$AdCampaignPreviewID);
		    	endif;

		    endforeach;

		    /*
		     * DOMAIN EXCLUSIVE INCLUSIONS
		    */

		    $params = array();
		    $params["AdCampaignBannerID"] = $banner_id;
		    $AdCampaignBannerDomainExclusiveInclusionList = $AdCampaignBannerDomainExclusiveInclusionFactory->get($params);

		    foreach ($AdCampaignBannerDomainExclusiveInclusionList as $AdCampaignBannerDomainExclusiveInclusion):

			    $BannerDomainExclusiveInclusionPreview = new \model\AdCampaignBannerDomainExclusiveInclusionPreview();

			    $BannerDomainExclusiveInclusionPreview->AdCampaignBannerPreviewID 		= $AdCampaignBannerPreviewID;
			    $BannerDomainExclusiveInclusionPreview->InclusionType 					= $AdCampaignBannerDomainExclusiveInclusion->InclusionType;
			    $BannerDomainExclusiveInclusionPreview->DomainName 						= $AdCampaignBannerDomainExclusiveInclusion->DomainName;
			    $BannerDomainExclusiveInclusionPreview->DateCreated 					= date("Y-m-d H:i:s");
			    $BannerDomainExclusiveInclusionPreview->DateUpdated 					= date("Y-m-d H:i:s");

			    $AdCampaignBannerDomainExclusiveInclusionPreviewID = $AdCampaignBannerDomainExclusiveInclusionPreviewFactory->saveAdCampaignBannerDomainExclusiveInclusionPreview($BannerDomainExclusiveInclusionPreview);

			    if ($update_data['type'] == 'AdCampaignBannerDomainExclusiveInclusionID' && $update_data['id'] == $AdCampaignBannerDomainExclusiveInclusion->AdCampaignBannerDomainExclusiveInclusionID):
			    	$return_val = array('AdCampaignBannerDomainExclusiveInclusionPreviewID'=>$AdCampaignBannerDomainExclusiveInclusionPreviewID,
		    							'AdCampaignBannerPreviewID'=>$AdCampaignBannerPreviewID,
										'AdCampaignPreviewID'=>$AdCampaignPreviewID);
			    endif;


		    endforeach;


		endforeach;

		if (!$auth->getIsAdmin() && $config['mail']['subscribe']['campaigns'] === true):
			// if this ad campaign was not created/edited by the admin, then send out a notification email
			$message = '<b>NginAd Demand Customer Campaign Edited by ' . $auth->getUserName() . '.</b><br /><br />';
			$message = $message.'<table border="0" width="10%">';
			$message = $message.'<tr><td><b>AdCampaignID: </b></td><td>'.$AdCampaign->AdCampaignID.'</td></tr>';
			$message = $message.'<tr><td><b>UserID: </b></td><td>'.$AdCampaign->UserID.'</td></tr>';
			$message = $message.'<tr><td><b>Name: </b></td><td>'.$AdCampaign->Name.'</td></tr>';
			$message = $message.'<tr><td><b>StartDate: </b></td><td>'.$AdCampaign->StartDate.'</td></tr>';
			$message = $message.'<tr><td><b>EndDate: </b></td><td>'.$AdCampaign->EndDate.'</td></tr>';
			$message = $message.'<tr><td><b>Customer: </b></td><td>'.$AdCampaign->Customer.'</td></tr>';
			$message = $message.'<tr><td><b>CustomerID: </b></td><td>'.$AdCampaign->CustomerID.'</td></tr>';
			$message = $message.'<tr><td><b>MaxImpressions: </b></td><td>'.$AdCampaign->MaxImpressions.'</td></tr>';
			$message = $message.'<tr><td><b>MaxSpend: </b></td><td>'.$AdCampaign->MaxSpend.'</td></tr>';
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