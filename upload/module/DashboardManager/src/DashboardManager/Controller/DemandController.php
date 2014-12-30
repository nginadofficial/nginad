<?php
/**
 * CDNPAL NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2015 CDNPAL Ltd. All Rights Reserved
 * @license GPLv3
 */
namespace DashboardManager\Controller;

use DashboardManager\ParentControllers\DemandAbstractActionController;
use Zend\View\Model\ViewModel;
//use Zend\Session\Container; // We need this when using sessions (No longer used?)
use transformation;
use Zend\Mail\Message;
use Zend\Mime;

/**
 * @author Christopher Gu
 * This is the Demand Manager Controller class that controls the management
 * of demand functions.
 */
class DemandController extends DemandAbstractActionController {

    /**
     * Will Show the dashboard index page.
     * (non-PHPdoc)
     * @see \Zend\Mvc\Controller\AbstractActionController::indexAction()
     */
	public function indexAction() {

		$initialized = $this->initialize();
		if ($initialized !== true) return $initialized;

		$user_markup_rate = $this->config_handle['system']['default_demand_markup_rate'];
		$campaign_markup_rate_list = array();

		if ($this->is_admin):

		    // admin is logged in as a user, get the markup if any for that user
		    if ($this->ImpersonateID != 0 && !empty($this->DemandCustomerInfoID)):
		    
		    	$user_markup = \util\Markup::getMarkupForUser($this->auth->getEffectiveIdentityID(), $this->config_handle, false);
		    	if ($user_markup != null):
		   			$user_markup_rate = $user_markup->MarkupRate;
				endif;
		    endif;

		endif;

	    $AdCampaignFactory = \_factory\AdCampaign::get_instance();
	    $params = array();
	    $params["Active"] = 1;
	    // admin should see campaigns requiring approval and the user they belong to ONLY
	    $params["UserID"] = $this->auth->getEffectiveUserID();

	    $_ad_campaign_list = $AdCampaignFactory->get($params);
	    $AdCampaignPreviewFactory = \_factory\AdCampaignPreview::get_instance();

	    $ad_campaign_list = array();

	    // admin should see campaigns requiring approval and the user they belong to ONLY
	    foreach ($_ad_campaign_list as $ad_campaign):
		  	$is_preview = \transformation\TransformPreview::doesPreviewAdCampaignExistForAdCampaign($ad_campaign->AdCampaignID, $this->auth);
		 	if ($is_preview != true):
		    	$ad_campaign_list[] = $ad_campaign;
		 		$ad_campaign_markup = \util\Markup::getMarkupForAdCampaign($ad_campaign->AdCampaignID, $this->config_handle, false);

		 		if ($ad_campaign_markup != null):
		 			$campaign_markup_rate_list[$ad_campaign->AdCampaignID] = $ad_campaign_markup->MarkupRate * 100;
		 		else:
		 			$campaign_markup_rate_list[$ad_campaign->AdCampaignID] = $user_markup_rate * 100;
		 		endif;

		 	endif;
		endforeach;

	    // get previews
	    $params = array();
	    $params["Active"] = 1;
	    // $params["Deleted"] = 0;
	    if ($this->is_admin == true && $this->auth->getEffectiveIdentityID() != 0):
	   		$params["UserID"] = $this->auth->getEffectiveUserID();
	    elseif ($this->is_admin == false):
	    	$params["UserID"] = $this->auth->getUserID();
	    endif;
	    
	    $_ad_campaign_preview_list = $AdCampaignPreviewFactory->get($params);

	    foreach ($_ad_campaign_preview_list as $ad_campaign_preview):
		    if ($ad_campaign_preview != null):
		    	$ad_campaign_list[] = $ad_campaign_preview;
		    	if ($ad_campaign_preview->AdCampaignID != null):

				    $ad_campaign_markup = \util\Markup::getMarkupForAdCampaign($ad_campaign_preview->AdCampaignID, $this->config_handle, false);

				    if ($ad_campaign_markup != null):
				    	$campaign_markup_rate_list[$ad_campaign_preview->AdCampaignID] = $ad_campaign_markup->MarkupRate * 100;
				    else:
				    	$campaign_markup_rate_list[$ad_campaign_preview->AdCampaignID] = $user_markup_rate * 100;
				    endif;

			    endif;
		    endif;
	    endforeach;

	    $user_markup_rate *= 100;

	    $view = new ViewModel(array(
	    		'ad_campaigns' => $ad_campaign_list,
	    		'is_admin' => strpos($this->auth->getPrimaryRole(), $this->config_handle['roles']['admin']) !== false,
	    		'user_id_list' => $this->user_id_list_demand_customer,
	    		'effective_id' => $this->auth->getEffectiveIdentityID(),
	    		'campaign_markup_rate_list'=>$campaign_markup_rate_list,
	    		'user_markup_rate' => $user_markup_rate,
	    		'dashboard_view' => 'demand',
	    		'user_identity' => $this->identity(),
	    		'true_user_name' => $this->auth->getUserName(),
				'is_admin' => $this->is_admin,
				'effective_id' => $this->auth->getEffectiveIdentityID(),
				'impersonate_id' => $this->ImpersonateID

	    ));
	    
	    if ($this->is_admin == false 
	    	|| ($this->is_admin == true && $this->DemandCustomerInfoID != null && $this->auth->getEffectiveIdentityID() != 0)):
	    	
	    	$view->header_title = '<a href="/demand/createcampaign/">Create Campaign</a>';
	    else:
	   		$view->header_title = '&nbsp;';
	    endif;

	    return $view;
	}

	/**
	 * Allows an administrator to "login as another user", to impersonate a lower user to manage another user's objects.
	 * @return Ambigous <\Zend\Http\Response, \Zend\Stdlib\ResponseInterface>
	 */
	public function loginasAction()
	{
	    $this->ImpersonateUser();
		return $this->redirect()->toRoute('demand');
	}

	/**
	 * 
	 * @return Ambigous <\Zend\Http\Response, \Zend\Stdlib\ResponseInterface>
	 */
	public function changeusermarkupAction() {
		
		$initialized = $this->initialize();
		if ($initialized !== true) return $initialized;

		if (strpos($this->auth->getPrimaryRole(), $this->config_handle['roles']['admin']) === false):
			die("You do not have permission to access this page");
		endif;

		$user_id 		= $this->getRequest()->getQuery('markupuserid');
		$user_markup 	= $this->getRequest()->getQuery('user-markup');

		$UserMarkupDemandFactory = \_factory\UserMarkupDemand::get_instance();
		$params = array();
		$params["UserID"] = $user_id;
		$UserMarkupDemand = $UserMarkupDemandFactory->get_row($params);

		$user_markup = floatval($user_markup) / 100;

			if ($user_markup <= 0):
				die("User Markup can not be less than or equal to zero percent");
			endif;

			if ($user_markup >= 1):
				die("User Markup can not be greater than or equal to one hundred percent");
			endif;

		$user_markup = sprintf("%1.2f", $user_markup);

		$_UserMarkupDemand = new \model\UserMarkupDemand();
		$_UserMarkupDemand->UserID 	= $user_id;
		$_UserMarkupDemand->MarkupRate = $user_markup;

			if ($UserMarkupDemand != null):
	
				$UserMarkupDemandFactory->updateUserMarkupDemand($_UserMarkupDemand);
	
			else:
	
				$UserMarkupDemandFactory->insertUserMarkupDemand($_UserMarkupDemand);
	
			endif;

		return $this->redirect()->toRoute('demand');

	}

	/**
	 * 
	 * @return Ambigous <\Zend\Http\Response, \Zend\Stdlib\ResponseInterface>
	 */
	public function changecampaignmarkupAction() {
		
		$initialized = $this->initialize();
		if ($initialized !== true) return $initialized;

		if (strpos($this->auth->getPrimaryRole(), $this->config_handle['roles']['admin']) === false):
			die("You do not have permission to access this page");
		endif;

		$campaign_id 		= $this->getRequest()->getQuery('markupcampaignid');
		$campaign_markup 	= $this->getRequest()->getQuery('campaign-markup');

		$AdCampainMarkupFactory = \_factory\AdCampainMarkup::get_instance();
		$params = array();
		$params["AdCampaignID"] = $campaign_id;
		$AdCampainMarkup = $AdCampainMarkupFactory->get_row($params);

		$campaign_markup = floatval($campaign_markup) / 100;

			if ($campaign_markup <= 0):
				die("Campaign Markup can not be less than or equal to zero percent");
			endif;

			if ($campaign_markup >= 1):
				die("Campaign Markup can not be greater than or equal to one hundred percent");
			endif;

		$campaign_markup = sprintf("%1.2f", $campaign_markup);

		$_AdCampainMarkup = new \model\AdCampainMarkup();
		$_AdCampainMarkup->AdCampaignID 	= $campaign_id;
		$_AdCampainMarkup->MarkupRate 		= $campaign_markup;

			if ($AdCampainMarkup != null):
	
				$AdCampainMarkupFactory->updateAdCampainMarkup($_AdCampainMarkup);
	
			else:
	
				$AdCampainMarkupFactory->insertAdCampainMarkup($_AdCampainMarkup);
	
			endif;

		return $this->redirect()->toRoute('demand');

	}

	/**
	 * 
	 * @return Ambigous <\Zend\Http\Response, \Zend\Stdlib\ResponseInterface>
	 */
	public function approvecampaignAction() {
		
		$initialized = $this->initialize();
		if ($initialized !== true) return $initialized;

		if (strpos($this->auth->getPrimaryRole(), $this->config_handle['roles']['admin']) === false):
			die("You do not have permission to access this page");
		endif;

		$id = $this->getEvent()->getRouteMatch()->getParam('param1');
		if ($id == null):
			die("Invalid Campaign Preview ID");
		endif;

		// copy the preview campaign and its elements into the production campaign
		$ad_campaign_id = \transformation\TransformPreview::cloneAdCampaignPreviewIntoAdCampaign($id, $this->auth, $this->config_handle);
		// set the preview campaigns and its elements to inactive and mark the date and time they went live
		\transformation\TransformPreview::deletePreviewModeCampaign($id, $this->auth, true);

		$AdCampaignFactory = \_factory\AdCampaign::get_instance();
		$params = array();
		$params["AdCampaignID"] = $ad_campaign_id;
		$AdCampaign = $AdCampaignFactory->get_row($params);
		
		if ($AdCampaign == null):
			return $this->redirect()->toRoute('demand');
		endif;
		
        $authUsersFactory = \_factory\authUsers::get_instance();
        $params = array();
        $params["user_id"] = $AdCampaign->UserID; 
        $auth_User = $authUsersFactory->get_row($params);
		
        if ($auth_User !== null && $this->config_handle['mail']['subscribe']['user_ad_campaigns']):
			// approval, send out email
			$message = 'Your NginAd Exchange Demand Ad Campaign : ' . $AdCampaign->Name . ' was approved.<br /><br />Please login <a href="http://server.nginad.com/auth/login">here</a> with your email and password';
			
			$subject = "Your NginAd Exchange Demand Ad Campaign : " . $AdCampaign->Name . " was approved";
			 
			$transport = $this->getServiceLocator()->get('mail.transport');
			 
			$text = new Mime\Part($message);
			$text->type = Mime\Mime::TYPE_HTML;
			$text->charset = 'utf-8';
			 
			$mimeMessage = new Mime\Message();
			$mimeMessage->setParts(array($text));
			$zf_message = new Message();
			$zf_message->addTo($auth_User->user_email)
			->addFrom($this->config_handle['mail']['reply-to']['email'], $this->config_handle['mail']['reply-to']['name'])
			->setSubject($subject)
			->setBody($mimeMessage);
			$transport->send($zf_message);
		endif;
		
		return $this->redirect()->toRoute('demand');

	}

	/**
	 * 
	 * @return Ambigous <\Zend\Http\Response, \Zend\Stdlib\ResponseInterface>
	 */
	public function cancelcampaignAction() {
		$id = $this->getEvent()->getRouteMatch()->getParam('param1');
		if ($id == null):
			die("Invalid Campaign Preview ID");
		endif;

		$initialized = $this->initialize();
		if ($initialized !== true) return $initialized;

		// ACL PREVIEW PERMISSIONS CHECK
		transformation\CheckPermissions::checkEditPermissionAdCampaignPreview($id, $this->auth, $this->config_handle);

		// set the preview campaigns and its elements to inactive and mark the date and time they went live
		\transformation\TransformPreview::deletePreviewModeCampaign($id, $this->auth, false);

		return $this->redirect()->toRoute('demand');

	}

	/**
	 * 
	 * @return Ambigous <\Zend\Http\Response, \Zend\Stdlib\ResponseInterface>
	 */
	public function rejectcampaignAction() {
		
		$initialized = $this->initialize();
		if ($initialized !== true) return $initialized;

		if (strpos($this->auth->getPrimaryRole(), $this->config_handle['roles']['admin']) === false):
			die("You do not have permission to access this page");
		endif;

		$id = $this->getEvent()->getRouteMatch()->getParam('param1');
		if ($id == null):
			die("Invalid Campaign Preview ID");
		endif;

		$AdCampaignPreviewFactory = \_factory\AdCampaignPreview::get_instance();
		$params = array();
		$params["AdCampaignPreviewID"] = $id;
		$AdCampaignPreview = $AdCampaignPreviewFactory->get_row($params);
		
		if ($AdCampaignPreview == null):
			die("AdCampaignPreviewID not found");
		endif;	
		
		$ad_campaign_preview_name = $AdCampaignPreview->Name;
		$user_id = $AdCampaignPreview->UserID;
		
		// set the preview campaigns and its elements to inactive and mark the date and time they went live
		\transformation\TransformPreview::deletePreviewModeCampaign($id, $this->auth, false);
		
		$authUsersFactory = \_factory\authUsers::get_instance();
		$params = array();
		$params["user_id"] = $user_id; 
		$auth_User = $authUsersFactory->get_row($params);
		
		if ($auth_User !== null && $ad_campaign_preview_name && $this->config_handle['mail']['subscribe']['user_ad_campaigns']):
			// approval, send out email
			$message = 'Your NginAd Exchange Demand Ad Campaign : ' . $ad_campaign_preview_name . ' was rejected.<br /><br />Please login <a href="http://server.nginad.com/auth/login">here</a> with your email and password';
				
			$subject = "Your NginAd Exchange Demand Ad Campaign : " . $ad_campaign_preview_name . " was rejected";
			
			$transport = $this->getServiceLocator()->get('mail.transport');
			
			$text = new Mime\Part($message);
			$text->type = Mime\Mime::TYPE_HTML;
			$text->charset = 'utf-8';
			
			$mimeMessage = new Mime\Message();
			$mimeMessage->setParts(array($text));
			$zf_message = new Message();
			$zf_message->addTo($auth_User->user_email)
			->addFrom($this->config_handle['mail']['reply-to']['email'], $this->config_handle['mail']['reply-to']['name'])
			->setSubject($subject)
			->setBody($mimeMessage);
			$transport->send($zf_message);
		endif;
		
		return $this->redirect()->toRoute('demand');

	}

	/*
	 * BEGIN NGINAD AdCampaignBannerRestrictions Actions
	 */

	/**
	 * 
	 * @return Ambigous <\Zend\View\Model\ViewModel, \Zend\View\Model\ViewModel>
	 */
	public function deletedeliveryfilterAction() {
		
		$error_msg = null;
		$success = true;
		$id = $this->getEvent()->getRouteMatch()->getParam('param1');
		if ($id == null):
			//die("Invalid Banner ID");
			$error_msg = "Invalid Banner ID";
			$success = false;
			$data = array(
					'success' => $success,
					'data' => array('error_msg' => $error_msg)
			);
			
			return $this->getResponse()->setContent(json_encode($data));
		endif;

		$initialized = $this->initialize();
		if ($initialized !== true) return $initialized;

		// ACL PERMISSIONS CHECK
		//transformation\CheckPermissions::checkEditPermissionAdCampaignBanner($id, $auth, $config);
		$ispreview 				= $this->getRequest()->getQuery('ispreview');
		
		if ($ispreview != "true"):
			/*
			 * THIS METHOD CHECKS IF THERE IS AN EXISTING PREVIEW MODE CAMPAIGN
			* IF NOT, IT CHECKS THE ACL PERMISSIONS ON THE PRODUCTION BANNER/CAMPAIGN REFERENCED
			* THEN IT CREATES A PREVIEW VERSION OF THE AD CAMPAIGN
			*/
			$update_data = array('type'=>'AdCampaignBannerID', 'id'=>$id);
			$return_val = \transformation\TransformPreview::previewCheckBannerID($id, $this->auth, $this->config_handle, $this->getServiceLocator()->get('mail.transport'), $update_data);
			
			if ($return_val !== null):
				$id = $return_val["AdCampaignBannerPreviewID"];
			else:
				$success = false;
				$data = array(
						'success' => $success,
						'data' => array('error_msg' => 'id not found')
				);
				
				return $this->getResponse()->setContent(json_encode($data));
			endif;
		
		endif;
		
		$response = transformation\CheckPermissions::checkEditPermissionAdCampaignBannerPreview($id, $this->auth, $this->config_handle);
		
		if(array_key_exists("error", $response) > 0):
			$success = false;
			$data = array(
		       'success' => $success,
		       'data' => array('error_msg' => $response['error'])
	   		);
	   		
	   	   return $this->getResponse()->setContent(json_encode($data));
		endif;
		
		$AdCampaignBannerPreviewFactory = \_factory\AdCampaignBannerPreview::get_instance();
		$AdCampaignVideoRestrictionsPreviewFactory = \_factory\AdCampaignVideoRestrictionsPreview::get_instance();
		$AdCampaignBannerRestrictionsPreviewFactory = \_factory\AdCampaignBannerRestrictionsPreview::get_instance();
		
		$AdCampaignBannerRestrictionsPreviewFactory->deleteAdCampaignBannerRestrictionsPreview($id);
		$AdCampaignVideoRestrictionsPreviewFactory->deleteAdCampaignVideoRestrictionsPreview($id);
		
		$params = array();
		$params["AdCampaignBannerPreviewID"] = $id;
		$AdCampaignBannerPreview = $AdCampaignBannerPreviewFactory->get_row($params);
		
		$success = true;
		$data = array(
		     'success' => $success,
			 'location' => '/demand/viewbanner/',
			 'previewid' => $AdCampaignBannerPreview->AdCampaignPreviewID,
		     'data' => array('error_msg' => $error_msg)
	   	);
   		 
        return $this->getResponse()->setContent(json_encode($data));

	}

	public function editdeliveryfiltervideoAction() {
	
		$initialized = $this->initialize();
		if ($initialized !== true) return $initialized;
	
		$needed_input = array(
				'ispreview'
		);
	
		$this->validateInput($needed_input);
	
		$bannerid 				= $this->getRequest()->getPost('bannerid');
		$banner_preview_id 		= $this->getRequest()->getPost('bannerpreviewid');
		$ispreview 				= $this->getRequest()->getPost('ispreview');
	
		if ($ispreview != true):
			/*
			 * THIS METHOD CHECKS IF THERE IS AN EXISTING PREVIEW MODE CAMPAIGN
			* IF NOT, IT CHECKS THE ACL PERMISSIONS ON THE PRODUCTION BANNER/CAMPAIGN REFERENCED
			* THEN IT CREATES A PREVIEW VERSION OF THE AD CAMPAIGN
			*/
			$update_data = array('type'=>'AdCampaignBannerID', 'id'=>$bannerid);
			$return_val = \transformation\TransformPreview::previewCheckBannerID($bannerid, $this->auth, $this->config_handle, $this->getServiceLocator()->get('mail.transport'), $update_data);
		
			if ($return_val !== null):
				$banner_preview_id = $return_val["AdCampaignBannerPreviewID"];
			endif;
		
		endif;
	
		// ACL PREVIEW PERMISSIONS CHECK
		transformation\CheckPermissions::checkEditPermissionAdCampaignBannerPreview($banner_preview_id, $this->auth, $this->config_handle);

		$start_delay 				= $this->getRequest()->getPost("StartDelay");
			
		$fold_pos 					= $this->getRequest()->getPost("FoldPos");

		$vertical 					= $this->getRequest()->getPost("vertical");
		
		$geocountry 				= $this->getRequest()->getPost("geocountry");
		
		$geostate 					= $this->getRequest()->getPost("geostate");
		
		$geocity 					= $this->getRequest()->getPost("geocity");
		
		$pmpenable 					= $this->getRequest()->getPost("pmpenable");
		
		$secure 					= $this->getRequest()->getPost("secure");
			
		$optout 					= $this->getRequest()->getPost("optout");
		
		$min_duration 				= $this->getRequest()->getPost("MinDuration");
		
		$max_duration 				= $this->getRequest()->getPost("MaxDuration");
			
		$min_height 				= $this->getRequest()->getPost("MinHeight");
		
		$min_width 					= $this->getRequest()->getPost("MinWidth");

		$linearity 					= $this->getRequest()->getPost("Linearity");
		
		
		$mimes 						= $this->getRequest()->getPost("Mimes");
		if ($mimes && is_array($mimes) && count($mimes) > 0):
			$mimes = join(',', $mimes);
		endif;
			
		$protocols 					= $this->getRequest()->getPost("Protocols");
		if ($protocols && is_array($protocols) && count($protocols) > 0):
			$protocols = join(',', $protocols);
		endif;

		$apis_supported 			= $this->getRequest()->getPost("ApisSupported");
		if ($apis_supported && is_array($apis_supported) && count($apis_supported) > 0):
			$apis_supported = join(',', $apis_supported);
		endif;
			
		$delivery 					= $this->getRequest()->getPost("Delivery");
		if ($delivery && is_array($delivery) && count($delivery) > 0):
			$delivery = join(',', $delivery);
		endif;
			
		$playback 					= $this->getRequest()->getPost("Playback");
		if ($playback && is_array($playback) && count($playback) > 0):
			$playback = join(',', $playback);
		endif;
			
		if ($vertical && is_array($vertical) && count($vertical) > 0):
	
			$vertical = join(',', $vertical);
	
		endif;
	
		if ($geocountry && is_array($geocountry) && count($geocountry) > 0):
	
			$geocountry = join(',', $geocountry);
	
		endif;
	
		if ($geostate && is_array($geostate) && count($geostate) > 0):
	
			$geostate = join(',', $geostate);
	
		endif;
	
		if (strpos($geocity, ",") !== false):
		
			$geocities = explode(",", $geocity);
		
			$geocity_list_trimmed = array();
		
			foreach ($geocities as $geocityitem):
		
				$geocity_list_trimmed[] = trim($geocityitem);
		
			endforeach;
		
			$geocity = join(',', $geocity_list_trimmed);
		
		endif;
	
		$AdCampaignVideoRestrictionsPreviewFactory = \_factory\AdCampaignVideoRestrictionsPreview::get_instance();
		$params = array();
		$params["AdCampaignBannerPreviewID"] = $banner_preview_id;
	
		$AdCampaignVideoRestrictionsPreview = $AdCampaignVideoRestrictionsPreviewFactory->get_row($params);
	
		$VideoRestrictionsPreview = new \model\AdCampaignVideoRestrictionsPreview();
	
		if ($AdCampaignVideoRestrictionsPreview != null):
	
			$VideoRestrictionsPreview->AdCampaignVideoRestrictionsPreviewID            = $AdCampaignVideoRestrictionsPreview->AdCampaignVideoRestrictionsPreviewID;
	
		endif;
	
		$VideoRestrictionsPreview->AdCampaignBannerPreviewID                = $banner_preview_id;
		$VideoRestrictionsPreview->Vertical                                 = trim($vertical);
		$VideoRestrictionsPreview->GeoCountry                               = trim($geocountry);
		$VideoRestrictionsPreview->GeoState                                 = trim($geostate);
		$VideoRestrictionsPreview->GeoCity                                  = trim($geocity);
		$VideoRestrictionsPreview->PmpEnable                                = trim($pmpenable);
		$VideoRestrictionsPreview->Secure                                   = trim($secure);
		$VideoRestrictionsPreview->Optout                                   = trim($optout);
		$VideoRestrictionsPreview->MinDuration                              = trim($min_duration);
		$VideoRestrictionsPreview->MaxDuration                              = trim($max_duration);
		$VideoRestrictionsPreview->MinHeight                              	= trim($min_height);
		$VideoRestrictionsPreview->MinWidth                              	= trim($min_width);
		$VideoRestrictionsPreview->MimesCommaSeparated                      = trim($mimes);
		$VideoRestrictionsPreview->ProtocolsCommaSeparated                 	= trim($protocols);
		$VideoRestrictionsPreview->ApisSupportedCommaSeparated            	= trim($apis_supported);
		$VideoRestrictionsPreview->DeliveryCommaSeparated                 	= trim($delivery);
		$VideoRestrictionsPreview->PlaybackCommaSeparated              		= trim($playback);
		$VideoRestrictionsPreview->StartDelay                              	= trim($start_delay);
		$VideoRestrictionsPreview->Linearity                              	= trim($linearity);
		$VideoRestrictionsPreview->FoldPos                              	= trim($fold_pos);
		$VideoRestrictionsPreview->DateCreated                              = date("Y-m-d H:i:s");
		$VideoRestrictionsPreview->DateUpdated                              = date("Y-m-d H:i:s");

		$AdCampaignVideoRestrictionsPreviewFactory = \_factory\AdCampaignVideoRestrictionsPreview::get_instance();
		$AdCampaignVideoRestrictionsPreviewFactory->saveAdCampaignVideoRestrictionsPreview($VideoRestrictionsPreview);
	
		$AdCampaignBannerPreviewFactory = \_factory\AdCampaignBannerPreview::get_instance();
		$params = array();
		$params["AdCampaignBannerPreviewID"] = $banner_preview_id;
	
		$AdCampaignBannerPreview = $AdCampaignBannerPreviewFactory->get_row($params);
	
		$refresh_url = "/demand/viewbanner/" . $AdCampaignBannerPreview->AdCampaignPreviewID . "?ispreview=true";
		$viewModel = new ViewModel(array('refresh_url' => $refresh_url));
	
		return $viewModel->setTemplate('dashboard-manager/demand/interstitial.phtml');
	}
	
	/**
	 * 
	 * @return Ambigous <\Zend\View\Model\ViewModel, \Zend\View\Model\ViewModel>
	 */
	public function editdeliveryfilterAction() {

		$initialized = $this->initialize();
		if ($initialized !== true) return $initialized;

		$needed_input = array(
				'ispreview'
		);

		$this->validateInput($needed_input);

		$bannerid 				= $this->getRequest()->getPost('bannerid');
		$banner_preview_id 		= $this->getRequest()->getPost('bannerpreviewid');
		$ispreview 				= $this->getRequest()->getPost('ispreview');

			if ($ispreview != true):
				/*
				 * THIS METHOD CHECKS IF THERE IS AN EXISTING PREVIEW MODE CAMPAIGN
				* IF NOT, IT CHECKS THE ACL PERMISSIONS ON THE PRODUCTION BANNER/CAMPAIGN REFERENCED
				* THEN IT CREATES A PREVIEW VERSION OF THE AD CAMPAIGN
				*/
				$update_data = array('type'=>'AdCampaignBannerID', 'id'=>$bannerid);
				$return_val = \transformation\TransformPreview::previewCheckBannerID($bannerid, $this->auth, $this->config_handle, $this->getServiceLocator()->get('mail.transport'), $update_data);
	
				if ($return_val !== null):
					$banner_preview_id = $return_val["AdCampaignBannerPreviewID"];
				endif;
	
			endif;

		// ACL PREVIEW PERMISSIONS CHECK
		transformation\CheckPermissions::checkEditPermissionAdCampaignBannerPreview($banner_preview_id, $this->auth, $this->config_handle);

		$vertical = $this->getRequest()->getPost('vertical');
		$geocountry = $this->getRequest()->getPost('geocountry');
		$geostate = $this->getRequest()->getPost('geostate');
		$geocity = $this->getRequest()->getPost('geocity');
		$adtagtype = $this->getRequest()->getPost('adtagtype');
		$adpositionminleft = $this->getRequest()->getPost('adpositionminleft');
		$adpositionmaxleft = $this->getRequest()->getPost('adpositionmaxleft');
		$adpositionmintop = $this->getRequest()->getPost('adpositionmintop');
		$adpositionmaxtop = $this->getRequest()->getPost('adpositionmaxtop');
		$foldpos = $this->getRequest()->getPost('foldpos');
		$frequency = $this->getRequest()->getPost('frequency');
		$timezone = $this->getRequest()->getPost('timezone');
		$iniframe = $this->getRequest()->getPost('iniframe');
		$inmultiplenestediframes = $this->getRequest()->getPost('inmultiplenestediframes');
		$minscreenresolutionwidth = $this->getRequest()->getPost('minscreenresolutionwidth');
		$maxscreenresolutionwidth = $this->getRequest()->getPost('maxscreenresolutionwidth');
		$minscreenresolutionheight = $this->getRequest()->getPost('minscreenresolutionheight');
		$maxscreenresolutionheight = $this->getRequest()->getPost('maxscreenresolutionheight');
		$httplanguage = $this->getRequest()->getPost('httplanguage');
		$browseruseragentgrep = $this->getRequest()->getPost('browseruseragentgrep');
		$cookiegrep = $this->getRequest()->getPost('cookiegrep');
		$pmpenable = $this->getRequest()->getPost('pmpenable');
		$secure = $this->getRequest()->getPost('secure');
		$optout = $this->getRequest()->getPost('optout');


			if ($vertical && is_array($vertical) && count($vertical) > 0):
	
	            $vertical = join(',', $vertical);
	
			endif;

			if ($geocountry && is_array($geocountry) && count($geocountry) > 0):
	
			  $geocountry = join(',', $geocountry);
	
			endif;

			if ($geostate && is_array($geostate) && count($geostate) > 0):
	
			  $geostate = join(',', $geostate);
	
			endif;

			if (strpos($geocity, ",") !== false):
	
			  $geocities = explode(",", $geocity);
	
			  $geocity_list_trimmed = array();
	
			  foreach ($geocities as $geocityitem):
	
			      $geocity_list_trimmed[] = trim($geocityitem);
	
			  endforeach;
	
			  $geocity = join(',', $geocity_list_trimmed);
	
			endif;

			if ($timezone && is_array($timezone) && count($timezone) > 0):
	
			  $timezone = join(',', $timezone);
	
			endif;

		$AdCampaignBannerRestrictionsPreviewFactory = \_factory\AdCampaignBannerRestrictionsPreview::get_instance();
		$params = array();
		$params["AdCampaignBannerPreviewID"] = $banner_preview_id;

		$AdCampaignBannerRestrictionsPreview = $AdCampaignBannerRestrictionsPreviewFactory->get_row($params);

		$BannerRestrictionsPreview = new \model\AdCampaignBannerRestrictionsPreview();

			if ($AdCampaignBannerRestrictionsPreview != null):
	
			      $BannerRestrictionsPreview->AdCampaignBannerRestrictionsPreviewID            = $AdCampaignBannerRestrictionsPreview->AdCampaignBannerRestrictionsPreviewID;
	
			endif;

		$BannerRestrictionsPreview->AdCampaignBannerPreviewID                       = $banner_preview_id;
		$BannerRestrictionsPreview->GeoCountry                               = trim($geocountry);
		$BannerRestrictionsPreview->GeoState                                 = trim($geostate);
		$BannerRestrictionsPreview->GeoCity                                  = trim($geocity);
		$BannerRestrictionsPreview->AdTagType                                = trim($adtagtype);
		$BannerRestrictionsPreview->AdPositionMinLeft                        = trim($adpositionminleft);
		$BannerRestrictionsPreview->AdPositionMaxLeft                        = trim($adpositionmaxleft);
		$BannerRestrictionsPreview->AdPositionMinTop                         = trim($adpositionmintop);
		$BannerRestrictionsPreview->AdPositionMaxTop                         = trim($adpositionmaxtop);
		$BannerRestrictionsPreview->FoldPos                                  = trim($foldpos);
		$BannerRestrictionsPreview->Freq                                     = trim($frequency);
		$BannerRestrictionsPreview->Timezone                                 = trim($timezone);
		$BannerRestrictionsPreview->InIframe                                 = trim($iniframe);
		$BannerRestrictionsPreview->InMultipleNestedIframes                  = trim($inmultiplenestediframes);
		$BannerRestrictionsPreview->MinScreenResolutionWidth                 = trim($minscreenresolutionwidth);
		$BannerRestrictionsPreview->MaxScreenResolutionWidth                 = trim($maxscreenresolutionwidth);
		$BannerRestrictionsPreview->MinScreenResolutionHeight                = trim($minscreenresolutionheight);
		$BannerRestrictionsPreview->MaxScreenResolutionHeight                = trim($maxscreenresolutionheight);
		$BannerRestrictionsPreview->HttpLanguage                             = trim($httplanguage);
		$BannerRestrictionsPreview->BrowserUserAgentGrep                     = trim($browseruseragentgrep);
		$BannerRestrictionsPreview->CookieGrep                               = trim($cookiegrep);
		$BannerRestrictionsPreview->PmpEnable                                = trim($pmpenable);
		$BannerRestrictionsPreview->Secure                                   = trim($secure);
		$BannerRestrictionsPreview->Optout                                   = trim($optout);
		$BannerRestrictionsPreview->Vertical                                 = trim($vertical);
		$BannerRestrictionsPreview->DateCreated                              = date("Y-m-d H:i:s");
		$BannerRestrictionsPreview->DateUpdated                              = date("Y-m-d H:i:s");

		$AdCampaignBannerRestrictionsPreviewFactory = \_factory\AdCampaignBannerRestrictionsPreview::get_instance();
		$AdCampaignBannerRestrictionsPreviewFactory->saveAdCampaignBannerRestrictionsPreview($BannerRestrictionsPreview);

		$AdCampaignBannerPreviewFactory = \_factory\AdCampaignBannerPreview::get_instance();
		$params = array();
		$params["AdCampaignBannerPreviewID"] = $banner_preview_id;

		$AdCampaignBannerPreview = $AdCampaignBannerPreviewFactory->get_row($params);

		$refresh_url = "/demand/viewbanner/" . $AdCampaignBannerPreview->AdCampaignPreviewID . "?ispreview=true";
		$viewModel = new ViewModel(array('refresh_url' => $refresh_url));

		return $viewModel->setTemplate('dashboard-manager/demand/interstitial.phtml');
	}

	public function deliveryfiltervideoAction() {
		$id = $this->getEvent()->getRouteMatch()->getParam('param1');
		if ($id == null):
			die("Invalid Banner ID");
		endif;
	
		$initialized = $this->initialize();
		if ($initialized !== true) return $initialized;
	
		$is_preview = $this->getRequest()->getQuery('ispreview');
	
		// verify
		if ($is_preview == "true"):
			$is_preview = \transformation\TransformPreview::doesPreviewBannerExist($id, $this->auth);
		endif;
	
		$banner_preview_id = "";
		$campaign_id = "";
		$campaign_preview_id = "";
	
		if ($is_preview == true):
			// ACL PREVIEW PERMISSIONS CHECK
			transformation\CheckPermissions::checkEditPermissionAdCampaignBannerPreview($id, $this->auth, $this->config_handle);
		
			$AdCampaignVideoRestrictionsPreviewFactory = \_factory\AdCampaignVideoRestrictionsPreview::get_instance();
			$params = array();
			$params["AdCampaignBannerPreviewID"] = $id;
			$banner_preview_id = $id;
			$id = "";
			$AdCampaignVideoRestrictions = $AdCampaignVideoRestrictionsPreviewFactory->get_row($params);
		
			$AdCampaignBannerPreviewFactory = \_factory\AdCampaignBannerPreview::get_instance();
			$params = array();
			$params["AdCampaignBannerPreviewID"] = $banner_preview_id;
			$AdCampaignBannerPreview = $AdCampaignBannerPreviewFactory->get_row($params);
			$campaign_preview_id = $AdCampaignBannerPreview->AdCampaignPreviewID;
		
		else:
			// ACL PERMISSIONS CHECK
			transformation\CheckPermissions::checkEditPermissionAdCampaignBanner($id, $this->auth, $this->config_handle);
		
			$AdCampaignVideoRestrictionsFactory = \_factory\AdCampaignVideoRestrictions::get_instance();
			$params = array();
			$params["AdCampaignBannerID"] = $id;
		
			$AdCampaignVideoRestrictions = $AdCampaignVideoRestrictionsFactory->get_row($params);
		
			$AdCampaignBannerFactory = \_factory\AdCampaignBanner::get_instance();
			$params = array();
			$params["AdCampaignBannerID"] = $id;
			$AdCampaignBanner = $AdCampaignBannerFactory->get_row($params);
			$campaign_id = $AdCampaignBanner->AdCampaignID;
		endif;
	
		$current_states 				= "";
		$current_country 				= "";
		$geocity_option 				= "";

		$current_min_duration 			= "";
		$current_max_duration 			= "";
		
		$current_min_height 			= "";
		$current_min_width	 			= "";
		
		$current_mimes 					= array();
		$current_apis_supported 		= array();
		$current_protocols 				= array();
		$current_delivery_methods 		= array();
		$current_playback_methods 		= array();
		
		$current_start_delay 			= "";
		$current_linearity 				= array();
		$current_foldpos 				= "";
		
		$current_pmpenable 				= "";
		$current_secure 				= "";
		$current_optout 				= "";
		$current_vertical 				= array();
	
		$current_mimes_raw 				= "";
		$current_apis_supported_raw 	= "";
		$current_protocols_raw 			= "";
		$current_delivery_methods_raw 	= "";
		$current_playback_methods_raw 	= "";
		$current_vertical_raw 			= "";
		
		if ($AdCampaignVideoRestrictions != null):
		
			$current_foldpos = $AdCampaignVideoRestrictions->Vertical == null ? "" : $AdCampaignVideoRestrictions->Vertical;
			$current_states = $AdCampaignVideoRestrictions->GeoState == null ? "" : $AdCampaignVideoRestrictions->GeoState;
			$current_country = $AdCampaignVideoRestrictions->GeoCountry == null ? "" : $AdCampaignVideoRestrictions->GeoCountry;
			$geocity_option = $AdCampaignVideoRestrictions->GeoCity == null ? "" : $AdCampaignVideoRestrictions->GeoCity;
			
			$current_mimes_raw = $AdCampaignVideoRestrictions->MimesCommaSeparated == null ? "" : $AdCampaignVideoRestrictions->MimesCommaSeparated;
			$current_apis_supported_raw = $AdCampaignVideoRestrictions->ApisSupportedCommaSeparated == null ? "" : $AdCampaignVideoRestrictions->ApisSupportedCommaSeparated;
			$current_protocols_raw = $AdCampaignVideoRestrictions->ProtocolsCommaSeparated == null ? "" : $AdCampaignVideoRestrictions->ProtocolsCommaSeparated;
			$current_delivery_methods_raw = $AdCampaignVideoRestrictions->DeliveryCommaSeparated == null ? "" : $AdCampaignVideoRestrictions->DeliveryCommaSeparated;
			$current_playback_methods_raw = $AdCampaignVideoRestrictions->PlaybackCommaSeparated == null ? "" : $AdCampaignVideoRestrictions->PlaybackCommaSeparated;
			
			$current_start_delay = $AdCampaignVideoRestrictions->StartDelay == null ? "" : $AdCampaignVideoRestrictions->StartDelay;
			$current_linearity = $AdCampaignVideoRestrictions->Linearity == null ? "" : $AdCampaignVideoRestrictions->Linearity;
			$current_fold_pos = $AdCampaignVideoRestrictions->FoldPos == null ? "" : $AdCampaignVideoRestrictions->FoldPos;

			$current_min_duration = $AdCampaignVideoRestrictions->MinDuration == null ? "" : $AdCampaignVideoRestrictions->MinDuration;
			$current_max_duration = $AdCampaignVideoRestrictions->MaxDuration == null ? "" : $AdCampaignVideoRestrictions->MaxDuration;
			
			$current_min_height = $AdCampaignVideoRestrictions->MinHeight == null ? "" : $AdCampaignVideoRestrictions->MinHeight;
			$current_min_width = $AdCampaignVideoRestrictions->MinWidth == null ? "" : $AdCampaignVideoRestrictions->MinWidth;

			$current_pmpenable = $AdCampaignVideoRestrictions->PmpEnable == null ? "" : $AdCampaignVideoRestrictions->PmpEnable;
			$current_secure = $AdCampaignVideoRestrictions->Secure == null ? "" : $AdCampaignVideoRestrictions->Secure;
			$current_optout = $AdCampaignVideoRestrictions->Optout == null ? "" : $AdCampaignVideoRestrictions->Optout;
			$current_vertical_raw = $AdCampaignVideoRestrictions->Vertical == null ? "" : $AdCampaignVideoRestrictions->Vertical;
			
		endif;
	
		$current_mimes = array();
		
		if ($current_mimes_raw):
		
			$current_mimes = explode(',', $current_mimes_raw);
		
		endif;
		
		$current_apis_supported = array();
		
		if ($current_apis_supported_raw):
		
			$current_apis_supported = explode(',', $current_apis_supported_raw);
		
		endif;
		
		$current_protocols = array();
		
		if ($current_protocols_raw):
		
			$current_protocols = explode(',', $current_protocols_raw);
		
		endif;
		
		$current_delivery_methods = array();
		
		if ($current_delivery_methods_raw):
		
			$current_delivery_methods = explode(',', $current_delivery_methods_raw);
		
		endif;
		
		$current_playback_methods = array();
		
		if ($current_playback_methods_raw):
		
			$current_playback_methods = explode(',', $current_playback_methods_raw);
		
		endif;
		
		$current_verticals = array();
	
		if ($current_vertical_raw):
	
			$current_verticals = explode(',', $current_vertical_raw);
	
		endif;
	
		$current_countries = array();
	
		if ($current_country):
	
			$current_countries = explode(',', $current_country);
	
		endif;
		
		return new ViewModel(array(
				'bannerid' => $id,
				'bannerpreviewid' => $banner_preview_id,
				'campaignid' => $campaign_id,
				'campaignpreviewid' => $campaign_preview_id,
				'ispreview' => $is_preview == true ? '1' : '0',
				'countrylist' => \util\Countries::$allcountries,
				'current_states' => $current_states,
				'current_countries' => $current_countries,
				'foldpos_options' => \util\DeliveryFilterOptions::$foldpos_options,
				'current_foldpos' => $current_foldpos,
				'geocity_option' => $geocity_option,
				'pmpenable_options' => \util\DeliveryFilterOptions::$pmpenable_options,
				'current_pmpenable' => $current_pmpenable,
				'secure_options' => \util\DeliveryFilterOptions::$secure_options,
				'current_secure' => $current_secure,
				'optout_options' => \util\DeliveryFilterOptions::$optout_options,
				'current_optout' => $current_optout,
				'vertical_options' => \util\DeliveryFilterOptions::$vertical_options,
				'current_verticals' => $current_verticals,
				'bread_crumb_info' => $this->getBreadCrumbInfoFromBanner($id, $banner_preview_id, $is_preview),
				'user_id_list' => $this->user_id_list_demand_customer,
				'center_class' => 'centerj',
				'user_identity' => $this->identity(),
				'true_user_name' => $this->auth->getUserName(),
				'header_title' => 'Edit Delivery Filter',
				'is_admin' => $this->is_admin,
				'effective_id' => $this->auth->getEffectiveIdentityID(),
				'impersonate_id' => $this->ImpersonateID,
				
				'fold_pos' => \util\BannerOptions::$fold_pos,
				'linearity' => \util\BannerOptions::$linearity,
				'start_delay' => \util\BannerOptions::$start_delay,
				'playback_methods' => \util\BannerOptions::$playback_methods,
				'delivery_methods' => \util\BannerOptions::$delivery_methods,
				'apis_supported' => \util\BannerOptions::$apis_supported,
				'protocols' => \util\BannerOptions::$protocols,
				'mimes' => \util\BannerOptions::$mimes,
				
				'MinHeight' => '',
				'MinWidth' => '',
				
				'current_mimes' => $current_mimes,
				
				'MinDuration' => $current_min_duration,
				'MaxDuration' => $current_max_duration,
				
				'current_apis_supported' => $current_apis_supported,
				'current_protocols' => $current_protocols,
				'current_delivery_methods' => $current_delivery_methods,
				'current_playback_methods' => $current_playback_methods,
				'current_start_delay' => $current_start_delay,
				'current_linearity' => $current_linearity,
				'current_fold_pos' => $current_foldpos,
				
				'MinHeight' => $current_min_height,
				'MinWidth' => $current_min_width,
				
		));
	}
	
	/**
	 * 
	 * @return \Zend\View\Model\ViewModel
	 */
	public function deliveryfilterAction() {
		$id = $this->getEvent()->getRouteMatch()->getParam('param1');
			if ($id == null):
				die("Invalid Banner ID");
			endif;

		$initialized = $this->initialize();
		if ($initialized !== true) return $initialized;

		$is_preview = $this->getRequest()->getQuery('ispreview');

		// verify
			if ($is_preview == "true"):
				$is_preview = \transformation\TransformPreview::doesPreviewBannerExist($id, $this->auth);
			endif;

		$banner_preview_id = "";
		$campaign_id = "";
		$campaign_preview_id = "";

			if ($is_preview == true):
				// ACL PREVIEW PERMISSIONS CHECK
				transformation\CheckPermissions::checkEditPermissionAdCampaignBannerPreview($id, $this->auth, $this->config_handle);
	
				$AdCampaignBannerRestrictionsPreviewFactory = \_factory\AdCampaignBannerRestrictionsPreview::get_instance();
				$params = array();
				$params["AdCampaignBannerPreviewID"] = $id;
				$banner_preview_id = $id;
				$id = "";
				$AdCampaignBannerRestrictions = $AdCampaignBannerRestrictionsPreviewFactory->get_row($params);
	
				$AdCampaignBannerPreviewFactory = \_factory\AdCampaignBannerPreview::get_instance();
				$params = array();
				$params["AdCampaignBannerPreviewID"] = $banner_preview_id;
				$AdCampaignBannerPreview = $AdCampaignBannerPreviewFactory->get_row($params);
				$campaign_preview_id = $AdCampaignBannerPreview->AdCampaignPreviewID;
	
			else:
				// ACL PERMISSIONS CHECK
				transformation\CheckPermissions::checkEditPermissionAdCampaignBanner($id, $this->auth, $this->config_handle);
	
				$AdCampaignBannerRestrictionsFactory = \_factory\AdCampaignBannerRestrictions::get_instance();
				$params = array();
				$params["AdCampaignBannerID"] = $id;
	
				$AdCampaignBannerRestrictions = $AdCampaignBannerRestrictionsFactory->get_row($params);
	
				$AdCampaignBannerFactory = \_factory\AdCampaignBanner::get_instance();
				$params = array();
				$params["AdCampaignBannerID"] = $id;
				$AdCampaignBanner = $AdCampaignBannerFactory->get_row($params);
				$campaign_id = $AdCampaignBanner->AdCampaignID;
			endif;

		$current_states = "";
		$current_country = "";
		$current_foldpos = "";
		$frequency_option = "";
		$geocity_option = "";
		$adpositionminleft_option = "";
		$adpositionmaxleft_option = "";
		$adpositionmintop_option = "";
		$adpositionmaxtop_option = "";
		$current_timezone = "";
		$current_adtagtype = "";
		$current_iniframe = "";
		$current_inmultiplenestediframes = "";
		$minscreenresolutionwidth_option = "";
		$maxscreenresolutionwidth_option = "";
		$minscreenresolutionheight_option = "";
		$maxscreenresolutionheight_option = "";
		$httplanguage_option = "";
		$browseruseragentgrep_option = "";
		$cookiegrep_option = "";
		$current_pmpenable = "";
		$current_secure = "";
		$current_optout = "";
		$current_vertical = array();


		if ($AdCampaignBannerRestrictions != null):

    		$current_states = $AdCampaignBannerRestrictions->GeoState == null ? "" : $AdCampaignBannerRestrictions->GeoState;
    		$current_country = $AdCampaignBannerRestrictions->GeoCountry == null ? "" : $AdCampaignBannerRestrictions->GeoCountry;
    		$current_foldpos = $AdCampaignBannerRestrictions->FoldPos == null ? "" : $AdCampaignBannerRestrictions->FoldPos;
    		$frequency_option = $AdCampaignBannerRestrictions->Freq == null ? "" : $AdCampaignBannerRestrictions->Freq;
    		$geocity_option = $AdCampaignBannerRestrictions->GeoCity == null ? "" : $AdCampaignBannerRestrictions->GeoCity;
    		$adpositionminleft_option = $AdCampaignBannerRestrictions->AdPositionMinLeft == null ? "" : $AdCampaignBannerRestrictions->AdPositionMinLeft;
    		$adpositionmaxleft_option = $AdCampaignBannerRestrictions->AdPositionMaxLeft == null ? "" : $AdCampaignBannerRestrictions->AdPositionMaxLeft;
    		$adpositionmintop_option = $AdCampaignBannerRestrictions->AdPositionMinTop == null ? "" : $AdCampaignBannerRestrictions->AdPositionMinTop;
    		$adpositionmaxtop_option = $AdCampaignBannerRestrictions->AdPositionMaxTop == null ? "" : $AdCampaignBannerRestrictions->AdPositionMaxTop;
    		$current_timezone = $AdCampaignBannerRestrictions->Timezone == null ? "" : $AdCampaignBannerRestrictions->Timezone;
    		$current_adtagtype = $AdCampaignBannerRestrictions->AdTagType == null ? "" : $AdCampaignBannerRestrictions->AdTagType;
    		$current_iniframe = $AdCampaignBannerRestrictions->InIframe == null ? "" : $AdCampaignBannerRestrictions->InIframe;
    		$current_inmultiplenestediframes = $AdCampaignBannerRestrictions->InMultipleNestedIframes == null ? "" : $AdCampaignBannerRestrictions->InMultipleNestedIframes;
    		$minscreenresolutionwidth_option = $AdCampaignBannerRestrictions->MinScreenResolutionWidth == null ? "" : $AdCampaignBannerRestrictions->MinScreenResolutionWidth;
    		$maxscreenresolutionwidth_option = $AdCampaignBannerRestrictions->MaxScreenResolutionWidth == null ? "" : $AdCampaignBannerRestrictions->MaxScreenResolutionWidth;
    		$minscreenresolutionheight_option = $AdCampaignBannerRestrictions->MinScreenResolutionHeight == null ? "" : $AdCampaignBannerRestrictions->MinScreenResolutionHeight;
    		$maxscreenresolutionheight_option = $AdCampaignBannerRestrictions->MaxScreenResolutionHeight == null ? "" : $AdCampaignBannerRestrictions->MaxScreenResolutionHeight;
    		$httplanguage_option = $AdCampaignBannerRestrictions->HttpLanguage == null ? "" : $AdCampaignBannerRestrictions->HttpLanguage;
    		$browseruseragentgrep_option = $AdCampaignBannerRestrictions->BrowserUserAgentGrep == null ? "" : $AdCampaignBannerRestrictions->BrowserUserAgentGrep;
    		$cookiegrep_option = $AdCampaignBannerRestrictions->CookieGrep == null ? "" : $AdCampaignBannerRestrictions->CookieGrep;
    		$current_pmpenable = $AdCampaignBannerRestrictions->PmpEnable == null ? "" : $AdCampaignBannerRestrictions->PmpEnable;
    		$current_secure = $AdCampaignBannerRestrictions->Secure == null ? "" : $AdCampaignBannerRestrictions->Secure;
    		$current_optout = $AdCampaignBannerRestrictions->Optout == null ? "" : $AdCampaignBannerRestrictions->Optout;
    		$current_vertical = $AdCampaignBannerRestrictions->Vertical == null ? "" : $AdCampaignBannerRestrictions->Vertical;

		endif;

		$current_verticals = array();

		if ($current_vertical):

            $current_verticals = explode(',', $current_vertical);

		endif;

		$current_countries = array();

		if ($current_country):

		  $current_countries = explode(',', $current_country);

		endif;

		$current_timezones = array();

		if ($current_timezone):

		  $current_timezones = explode(',', $current_timezone);

		endif;

		//var_dump($current_country);
		//exit;

		return new ViewModel(array(
				'bannerid' => $id,
				'bannerpreviewid' => $banner_preview_id,
				'campaignid' => $campaign_id,
				'campaignpreviewid' => $campaign_preview_id,
				'ispreview' => $is_preview == true ? '1' : '0',
		        'countrylist' => \util\Countries::$allcountries,
		        'current_states' => $current_states,
		        'current_countries' => $current_countries,
		        'foldpos_options' => \util\DeliveryFilterOptions::$foldpos_options,
		        'current_foldpos' => $current_foldpos,
		        'frequency_option' => $frequency_option,
    		    'geocity_option' => $geocity_option,
    		    'adpositionminleft_option' => $adpositionminleft_option,
    		    'adpositionmaxleft_option' => $adpositionmaxleft_option,
    		    'adpositionmintop_option' => $adpositionmintop_option,
    		    'adpositionmaxtop_option' => $adpositionmaxtop_option,
		        'adtagtype_options' => \util\DeliveryFilterOptions::$adtagtype_options,
		        'current_adtagtype' => $current_adtagtype,
		        'timezone_options' => \util\DeliveryFilterOptions::$timezone_options,
		        'current_timezones' => $current_timezones,
    		    'iniframe_options' => \util\DeliveryFilterOptions::$iniframe_options,
    		    'current_iniframe' => $current_iniframe,
    		    'inmultiplenestediframes_options' => \util\DeliveryFilterOptions::$inmultiplenestediframes_options,
    		    'current_inmultiplenestediframes' => $current_inmultiplenestediframes,
    		    'minscreenresolutionwidth_option' => $minscreenresolutionwidth_option,
    		    'maxscreenresolutionwidth_option' => $maxscreenresolutionwidth_option,
    		    'minscreenresolutionheight_option' => $minscreenresolutionheight_option,
    		    'maxscreenresolutionheight_option' => $maxscreenresolutionheight_option,
		        'httplanguage_option' => $httplanguage_option,
		        'browseruseragentgrep_option' => $browseruseragentgrep_option,
		        'cookiegrep_option' => $cookiegrep_option,
    		    'pmpenable_options' => \util\DeliveryFilterOptions::$pmpenable_options,
    		    'current_pmpenable' => $current_pmpenable,
    		    'secure_options' => \util\DeliveryFilterOptions::$secure_options,
    		    'current_secure' => $current_secure,
    		    'optout_options' => \util\DeliveryFilterOptions::$optout_options,
    		    'current_optout' => $current_optout,
    		    'vertical_options' => \util\DeliveryFilterOptions::$vertical_options,
    		    'current_verticals' => $current_verticals,
				'bread_crumb_info' => $this->getBreadCrumbInfoFromBanner($id, $banner_preview_id, $is_preview),
				'user_id_list' => $this->user_id_list_demand_customer,
    			'center_class' => 'centerj',
    			'user_identity' => $this->identity(),
    			'true_user_name' => $this->auth->getUserName(),
				'header_title' => 'Edit Delivery Filter',
				'is_admin' => $this->is_admin,
				'effective_id' => $this->auth->getEffectiveIdentityID(),
				'impersonate_id' => $this->ImpersonateID
		));
	}

	/*
	 * END NGINAD AdCampaignBannerRestrictions Actions
	*/

	/*
	 * BEGIN NGINAD AdCampaignBannerDomainExclusiveInclusion Actions
	*/

	/**
	 * 
	 * @return \Zend\View\Model\ViewModel
	 */
	public function viewexclusiveinclusionAction() {
		$id = $this->getEvent()->getRouteMatch()->getParam('param1');
		if ($id == null):
			die("Invalid Banner ID");
		endif;

		$initialized = $this->initialize();
		if ($initialized !== true) return $initialized;

		$is_preview = $this->getRequest()->getQuery('ispreview');

		// verify
		if ($is_preview == "true"):
			$is_preview = \transformation\TransformPreview::doesPreviewBannerExist($id, $this->auth);
		endif;

		$banner_preview_id = "";
		$campaign_id = "";
		$campaign_preview_id = "";

		if ($is_preview == true):
			// ACL PREVIEW PERMISSIONS CHECK
			transformation\CheckPermissions::checkEditPermissionAdCampaignBannerPreview($id, $this->auth, $this->config_handle);

			$AdCampaignBannerDomainExclusiveInclusionPreviewFactory = \_factory\AdCampaignBannerDomainExclusiveInclusionPreview::get_instance();
			$params = array();
			$params["AdCampaignBannerPreviewID"] = $id;
			$banner_preview_id = $id;
			$id = "";
			$rtb_domain_exclusive_inclusions = $AdCampaignBannerDomainExclusiveInclusionPreviewFactory->get($params);

			$AdCampaignBannerPreviewFactory = \_factory\AdCampaignBannerPreview::get_instance();
			$params = array();
			$params["AdCampaignBannerPreviewID"] = $banner_preview_id;

			$AdCampaignBannerPreview = $AdCampaignBannerPreviewFactory->get_row($params);
			$campaign_preview_id = $AdCampaignBannerPreview->AdCampaignPreviewID;

		else:
			// ACL PERMISSIONS CHECK
			transformation\CheckPermissions::checkEditPermissionAdCampaignBanner($id, $this->auth, $this->config_handle);

			$AdCampaignBannerDomainExclusiveInclusionFactory = \_factory\AdCampaignBannerDomainExclusiveInclusion::get_instance();
			$params = array();
			$params["AdCampaignBannerID"] = $id;
			$rtb_domain_exclusive_inclusions = $AdCampaignBannerDomainExclusiveInclusionFactory->get($params);

			$AdCampaignBannerFactory = \_factory\AdCampaignBanner::get_instance();
			$params = array();
			$params["AdCampaignBannerID"] = $id;

			$AdCampaignBanner = $AdCampaignBannerFactory->get_row($params);
			$campaign_id = $AdCampaignBanner->AdCampaignID;

		endif;

		if ($is_preview == true):
			$rtb_banner_id = $banner_preview_id;
			$ad_campaign_id = $campaign_preview_id;
		else:
			$rtb_banner_id = $id;
			$ad_campaign_id = $campaign_id;
		endif;
		
		return new ViewModel(array(
				'ispreview'	  => $is_preview == true ? '1' : '0',
				'rtb_domain_exclusive_inclusions' => $rtb_domain_exclusive_inclusions,
				'banner_id' => $id,
				'banner_preview_id' => $banner_preview_id,
				'campaign_id' => $campaign_id,
				'campaign_preview_id' => $campaign_preview_id,
				'bread_crumb_info' => $this->getBreadCrumbInfoFromBanner($id, $banner_preview_id, $is_preview),
				'user_id_list' => $this->user_id_list_demand_customer,
    			'center_class' => 'centerj',
				'user_identity' => $this->identity(),
				'true_user_name' => $this->auth->getUserName(),
				'header_title' => '<a href="/demand/createexclusiveinclusion/' . $rtb_banner_id . $this->preview_query . '">Create Domain Exclusive Inclusion</a>',
				'is_admin' => $this->is_admin,
				'effective_id' => $this->auth->getEffectiveIdentityID(),
				'impersonate_id' => $this->ImpersonateID
		));
	}

	/**
	 * 
	 * @return Ambigous <\Zend\View\Model\ViewModel, \Zend\View\Model\ViewModel>
	 */
	public function deleteexclusiveinclusionAction() {
		
		$error_msg = null;
		$success = true;

		$id = $this->getEvent()->getRouteMatch()->getParam('param1');
		if ($id == null):
			//die("Invalid DomainExclusiveInclusion ID");
			$error_msg = "Invalid DomainExclusiveInclusion ID";
		    $success = false;
		    $data = array(
	         'success' => $success,
	         'data' => array('error_msg' => $error_msg)
   		   );
   		 
          return $this->getResponse()->setContent(json_encode($data));
		endif;

		$initialized = $this->initialize();
		if ($initialized !== true) return $initialized;

		$is_preview = $this->getRequest()->getQuery('ispreview');

		$exclusiveinclusion_preview_id = null;

		$AdCampaignBannerDomainExclusiveInclusionPreviewFactory = \_factory\AdCampaignBannerDomainExclusiveInclusionPreview::get_instance();

		// verify
		if ($is_preview != "true"):

			$AdCampaignBannerDomainExclusiveInclusionFactory = \_factory\AdCampaignBannerDomainExclusiveInclusion::get_instance();
			$params = array();
			$params["AdCampaignBannerDomainExclusiveInclusionID"] = $id;
			$rtb_domain_exclusive_inclusion = $AdCampaignBannerDomainExclusiveInclusionFactory->get_row($params);

			if ($rtb_domain_exclusive_inclusion == null):
				//die("Invalid AdCampaignBannerDomainExclusiveInclusion ID");
				$error_msg = "Invalid AdCampaignBannerDomainExclusiveInclusion ID";
			    $success = false;
			    $data = array(
		         'success' => $success,
		         'data' => array('error_msg' => $error_msg)
	   		   );
   		 
          		return $this->getResponse()->setContent(json_encode($data));
			endif;

			$banner_id = $rtb_domain_exclusive_inclusion->AdCampaignBannerID;

			// ACL PERMISSIONS CHECK
			$response = transformation\CheckPermissions::checkEditPermissionAdCampaignBanner($banner_id, $this->auth, $this->config_handle);
			
			if(array_key_exists("error", $response) > 0):
				$success = false;
				$data = array(
			       'success' => $success,
			       'data' => array('error_msg' => $response['error'])
		   		);
		   		
		   	   return $this->getResponse()->setContent(json_encode($data));
			endif;
			
			/*
			 * THIS METHOD CHECKS IF THERE IS AN EXISTING PREVIEW MODE CAMPAIGN
			* IF NOT, IT CHECKS THE ACL PERMISSIONS ON THE PRODUCTION BANNER/CAMPAIGN REFERENCED
			* THEN IT CREATES A PREVIEW VERSION OF THE AD CAMPAIGN
			*/

			$update_data = array('type'=>'AdCampaignBannerDomainExclusiveInclusionID', 'id'=>$id);

			$return_val = \transformation\TransformPreview::previewCheckBannerID($banner_id, $this->auth, $this->config_handle, $this->getServiceLocator()->get('mail.transport'), $update_data);

			if ($return_val !== null && array_key_exists("error", $return_val)):

				$success = false;
				$data = array(
			       'success' => $success,
			       'data' => array('error_msg' => $return_val['error'])
		   		);
   		
		   	   return $this->getResponse()->setContent(json_encode($data));
			endif;
			
			if ($return_val !== null):
				$banner_preview_id 	= $return_val["AdCampaignBannerPreviewID"];
				$exclusiveinclusion_preview_id = $return_val["AdCampaignBannerDomainExclusiveInclusionPreviewID"];
			endif;

		else:

			$params = array();
			$params["AdCampaignBannerDomainExclusiveInclusionPreviewID"] = $id;
			$rtb_domain_exclusive_inclusion_preview = $AdCampaignBannerDomainExclusiveInclusionPreviewFactory->get_row($params);

			if ($rtb_domain_exclusive_inclusion_preview == null):
				//die("Invalid AdCampaignBannerDomainExclusiveInclusionPreview ID");
				$error_msg = "Invalid AdCampaignBannerDomainExclusiveInclusionPreview ID";
			    $success = false;
			    $data = array(
		         'success' => $success,
		         'data' => array('error_msg' => $error_msg)
	   		   );
   		 
          		return $this->getResponse()->setContent(json_encode($data));
			endif;

			$banner_preview_id = $rtb_domain_exclusive_inclusion_preview->AdCampaignBannerPreviewID;
			$exclusiveinclusion_preview_id = $rtb_domain_exclusive_inclusion_preview->AdCampaignBannerDomainExclusiveInclusionPreviewID;

			// ACL PREVIEW PERMISSIONS CHECK
			$response = transformation\CheckPermissions::checkEditPermissionAdCampaignBannerPreview($banner_preview_id, $this->auth, $this->config_handle);
			
			if(array_key_exists("error", $response) > 0):
				$success = false;
				$data = array(
			       'success' => $success,
			       'data' => array('error_msg' => $response['error'])
		   		);
		   		
		   	   return $this->getResponse()->setContent(json_encode($data));
			endif;

		endif;

		$AdCampaignBannerDomainExclusiveInclusionPreviewFactory->deleteAdCampaignBannerDomainExclusiveInclusionPreview($exclusiveinclusion_preview_id);


		  $data = array(
		     'success' => $success,
		  	 'location' => '/demand/viewexclusiveinclusion/',
		  	 'previewid' => $banner_preview_id,
		     'data' => array('error_msg' => $error_msg)
	   	  );
   		 
      	return $this->getResponse()->setContent(json_encode($data));
	}

	/**
	 * 
	 * @return \Zend\View\Model\ViewModel
	 */
	public function createexclusiveinclusionAction() {
		$id = $this->getEvent()->getRouteMatch()->getParam('param1');
		if ($id == null):
			die("Invalid Banner ID");
		endif;

		$initialized = $this->initialize();
		if ($initialized !== true) return $initialized;

		$is_preview = $this->getRequest()->getQuery('ispreview');

		// verify
		if ($is_preview == "true"):
			$is_preview = \transformation\TransformPreview::doesPreviewBannerExist($id, $this->auth);
		endif;

		$banner_preview_id = "";
		$campaign_preview_id = "";
		$campaign_id = "";

		if ($is_preview == "true"):
			// ACL PERMISSIONS CHECK
			transformation\CheckPermissions::checkEditPermissionAdCampaignBannerPreview($id, $this->auth, $this->config_handle);
			$banner_preview_id = $id;
			$id = "";

			$AdCampaignBannerPreviewFactory = \_factory\AdCampaignBannerPreview::get_instance();
			$params = array();
			$params["AdCampaignBannerPreviewID"] = $banner_preview_id;
			$AdCampaignBannerPreview = $AdCampaignBannerPreviewFactory->get_row($params);
			$campaign_preview_id = $AdCampaignBannerPreview->AdCampaignPreviewID;

		else:
			// ACL PERMISSIONS CHECK
			transformation\CheckPermissions::checkEditPermissionAdCampaignBanner($id, $this->auth, $this->config_handle);

			$AdCampaignBannerFactory = \_factory\AdCampaignBanner::get_instance();
			$params = array();
			$params["AdCampaignBannerID"] = $id;
			$AdCampaignBanner = $AdCampaignBannerFactory->get_row($params);
			$campaign_id = $AdCampaignBanner->AdCampaignID;

		endif;

		return new ViewModel(array(
				'ispreview' => $is_preview == true ? '1' : '0',
				'bannerid' => $id,
				'bannerpreviewid' => $banner_preview_id,
				'campaignid' => $campaign_id,
				'campaignpreviewid' => $campaign_preview_id,
				'bread_crumb_info' => $this->getBreadCrumbInfoFromBanner($id, $banner_preview_id, $is_preview),
				'user_id_list' => $this->user_id_list_demand_customer,
    			'center_class' => 'centerj',
    			'user_identity' => $this->identity(),
				'true_user_name' => $this->auth->getUserName(),
				'header_title' => 'Create Domain Exclusive Inclusion',
				'is_admin' => $this->is_admin,
				'effective_id' => $this->auth->getEffectiveIdentityID(),
				'impersonate_id' => $this->ImpersonateID
		));
	}

	/**
	 * 
	 * @return Ambigous <\Zend\View\Model\ViewModel, \Zend\View\Model\ViewModel>
	 */
	public function newexclusiveinclusionAction() {

		$needed_input = array(
				'inclusiontype',
				'domainname'
		);

		$initialized = $this->initialize();
		if ($initialized !== true) return $initialized;

		$this->validateInput($needed_input);

		$bannerid 				= $this->getRequest()->getPost('bannerid');
		$banner_preview_id 		= $this->getRequest()->getPost('bannerpreviewid');
		$ispreview 				= $this->getRequest()->getPost('ispreview');

		if ($ispreview != true):
			/*
			 * THIS METHOD CHECKS IF THERE IS AN EXISTING PREVIEW MODE CAMPAIGN
			* IF NOT, IT CHECKS THE ACL PERMISSIONS ON THE PRODUCTION BANNER/CAMPAIGN REFERENCED
			* THEN IT CREATES A PREVIEW VERSION OF THE AD CAMPAIGN
			*/
			$update_data = array('type'=>'AdCampaignBannerID', 'id'=>$bannerid);
			$return_val = \transformation\TransformPreview::previewCheckBannerID($bannerid, $this->auth, $this->config_handle, $this->getServiceLocator()->get('mail.transport'), $update_data);

			if ($return_val !== null):
				$banner_preview_id = $return_val["AdCampaignBannerPreviewID"];
			endif;

		endif;

		// ACL PREVIEW PERMISSIONS CHECK
		transformation\CheckPermissions::checkEditPermissionAdCampaignBannerPreview($banner_preview_id, $this->auth, $this->config_handle);

		$inclusiontype = $this->getRequest()->getPost('inclusiontype');
		$domainname = $this->getRequest()->getPost('domainname');

		$BannerDomainExclusiveInclusionPreview = new \model\AdCampaignBannerDomainExclusiveInclusionPreview();
		$BannerDomainExclusiveInclusionPreview->AdCampaignBannerPreviewID           = $banner_preview_id;
		$BannerDomainExclusiveInclusionPreview->InclusionType             = $inclusiontype;
		$BannerDomainExclusiveInclusionPreview->DomainName                = $domainname;
		$BannerDomainExclusiveInclusionPreview->DateCreated               = date("Y-m-d H:i:s");
		$BannerDomainExclusiveInclusionPreview->DateUpdated               = date("Y-m-d H:i:s");

		$AdCampaignBannerDomainExclusiveInclusionPreviewFactory = \_factory\AdCampaignBannerDomainExclusiveInclusionPreview::get_instance();
		$AdCampaignBannerDomainExclusiveInclusionPreviewFactory->saveAdCampaignBannerDomainExclusiveInclusionPreview($BannerDomainExclusiveInclusionPreview);

		$refresh_url = "/demand/viewexclusiveinclusion/" . $banner_preview_id . "?ispreview=true";
		$viewModel = new ViewModel(array('refresh_url' => $refresh_url));

		return $viewModel->setTemplate('dashboard-manager/demand/interstitial.phtml');

	}

	/*
	 * END NGINAD AdCampaignBannerDomainExclusiveInclusion Actions
	*/

	/*
	 * BEGIN NGINAD AdCampaignBannerDomainExclusion Actions
	*/


	/**
	 * 
	 * @return \Zend\View\Model\ViewModel
	 */
	public function viewdomainexclusionAction() {
		$id = $this->getEvent()->getRouteMatch()->getParam('param1');
		if ($id == null):
			die("Invalid Banner ID");
		endif;

		$initialized = $this->initialize();
		if ($initialized !== true) return $initialized;

		$is_preview = $this->getRequest()->getQuery('ispreview');

		// verify
		if ($is_preview == "true"):
			$is_preview = \transformation\TransformPreview::doesPreviewBannerExist($id, $this->auth);
		endif;

		$banner_preview_id = "";
		$campaign_id = "";
		$campaign_preview_id = "";

		if ($is_preview == true):
			// ACL PREVIEW PERMISSIONS CHECK
			transformation\CheckPermissions::checkEditPermissionAdCampaignBannerPreview($id, $this->auth, $this->config_handle);

			$AdCampaignBannerDomainExclusionPreviewFactory = \_factory\AdCampaignBannerDomainExclusionPreview::get_instance();
			$params = array();
			$params["AdCampaignBannerPreviewID"] = $id;
			$banner_preview_id = $id;
			$id = "";
			$rtb_domain_exclusions = $AdCampaignBannerDomainExclusionPreviewFactory->get($params);

			$AdCampaignBannerPreviewFactory = \_factory\AdCampaignBannerPreview::get_instance();
			$params = array();
			$params["AdCampaignBannerPreviewID"] = $banner_preview_id;

			$AdCampaignBannerPreview = $AdCampaignBannerPreviewFactory->get_row($params);
			$campaign_preview_id = $AdCampaignBannerPreview->AdCampaignPreviewID;

		else:
			// ACL PERMISSIONS CHECK
			transformation\CheckPermissions::checkEditPermissionAdCampaignBanner($id, $this->auth, $this->config_handle);

			$AdCampaignBannerDomainExclusionFactory = \_factory\AdCampaignBannerDomainExclusion::get_instance();
			$params = array();
			$params["AdCampaignBannerID"] = $id;
			$rtb_domain_exclusions = $AdCampaignBannerDomainExclusionFactory->get($params);

			$AdCampaignBannerFactory = \_factory\AdCampaignBanner::get_instance();
			$params = array();
			$params["AdCampaignBannerID"] = $id;

			$AdCampaignBanner = $AdCampaignBannerFactory->get_row($params);
			$campaign_id = $AdCampaignBanner->AdCampaignID;

		endif;

		if ($is_preview == true):
			$rtb_banner_id = $banner_preview_id;
			$ad_campaign_id = $campaign_preview_id;
		else:
			$rtb_banner_id = $id;
			$ad_campaign_id = $campaign_id;
		endif;
		
		return new ViewModel(array(
				'ispreview'	  => $is_preview == true ? '1' : '0',
				'rtb_domain_exclusions' => $rtb_domain_exclusions,
				'banner_id' => $id,
				'banner_preview_id' => $banner_preview_id,
				'campaign_id' => $campaign_id,
				'campaign_preview_id' => $campaign_preview_id,
				'bread_crumb_info' => $this->getBreadCrumbInfoFromBanner($id, $banner_preview_id, $is_preview),
				'user_id_list' => $this->user_id_list_demand_customer,
    			'center_class' => 'centerj',
    			'user_identity' => $this->identity(),
				'true_user_name' => $this->auth->getUserName(),
				'header_title' => '<a href="/demand/createdomainexclusion/' . $rtb_banner_id . $this->preview_query . '">Create Domain Exclusion</a>',
				'is_admin' => $this->is_admin,
				'effective_id' => $this->auth->getEffectiveIdentityID(),
				'impersonate_id' => $this->ImpersonateID
		));
	}

	/**
	 * 
	 * @return Ambigous <\Zend\View\Model\ViewModel, \Zend\View\Model\ViewModel>
	 */
	public function deletedomainexclusionAction() {
		
		$error_msg = null;
		$success = true;
		$id = $this->getEvent()->getRouteMatch()->getParam('param1');
		if ($id == null):
			//die("Invalid DomainExclusion ID");
			$error_msg = "Invalid Domain Exclusion ID";
		    $success = false;
		    $data = array(
	         'success' => $success,
	         'data' => array('error_msg' => $error_msg)
   		   );
   		 
           return $this->getResponse()->setContent(json_encode($data));
		endif;

		$initialized = $this->initialize();
		if ($initialized !== true) return $initialized;

		$is_preview = $this->getRequest()->getQuery('ispreview');

		$exclusion_preview_id = null;

		$AdCampaignBannerDomainExclusionPreviewFactory = \_factory\AdCampaignBannerDomainExclusionPreview::get_instance();

		// verify
		if ($is_preview != "true"):

			$AdCampaignBannerDomainExclusionFactory = \_factory\AdCampaignBannerDomainExclusion::get_instance();
			$params = array();
			$params["AdCampaignBannerDomainExclusionID"] = $id;
			$rtb_domain_exclusion = $AdCampaignBannerDomainExclusionFactory->get_row($params);

			if ($rtb_domain_exclusion == null):
				//die("Invalid AdCampaignBannerDomainExclusion ID");
				$error_msg = "Invalid AdCampaignBanner Domain Exclusion ID";
			    $success = false;
			    $data = array(
		         'success' => $success,
		         'data' => array('error_msg' => $error_msg)
	   		   );
	   		 
	           return $this->getResponse()->setContent(json_encode($data));
				
			endif;

			$banner_id = $rtb_domain_exclusion->AdCampaignBannerID;

			// ACL PERMISSIONS CHECK
			//transformation\CheckPermissions::checkEditPermissionAdCampaignBanner($banner_id, $auth, $config);
			$response = transformation\CheckPermissions::checkEditPermissionAdCampaignBanner($banner_id, $this->auth, $this->config_handle);
			
			if(array_key_exists("error", $response) > 0):
				$success = false;
				$data = array(
			       'success' => $success,
			       'data' => array('error_msg' => $response['error'])
		   		);
		   		
		   	   return $this->getResponse()->setContent(json_encode($data));
			endif;
			
			/*
			 * THIS METHOD CHECKS IF THERE IS AN EXISTING PREVIEW MODE CAMPAIGN
			* IF NOT, IT CHECKS THE ACL PERMISSIONS ON THE PRODUCTION BANNER/CAMPAIGN REFERENCED
			* THEN IT CREATES A PREVIEW VERSION OF THE AD CAMPAIGN
			*/

			$update_data = array('type'=>'AdCampaignBannerDomainExclusionID', 'id'=>$id);

			$return_val = \transformation\TransformPreview::previewCheckBannerID($banner_id, $this->auth, $this->config_handle, $this->getServiceLocator()->get('mail.transport'), $update_data);

			if ($return_val !== null && array_key_exists("error", $return_val)):

				$success = false;
				$data = array(
			       'success' => $success,
			       'data' => array('error_msg' => $return_val['error'])
		   		);
   		
		   	   return $this->getResponse()->setContent(json_encode($data));
			endif;
			
			if ($return_val !== null):
				$banner_preview_id 	= $return_val["AdCampaignBannerPreviewID"];
				$exclusion_preview_id = $return_val["AdCampaignBannerDomainExclusionPreviewID"];
			endif;

		else:

			$params = array();
			$params["AdCampaignBannerDomainExclusionPreviewID"] = $id;
			$rtb_domain_exclusion_preview = $AdCampaignBannerDomainExclusionPreviewFactory->get_row($params);

			if ($rtb_domain_exclusion_preview == null):
				//die("Invalid AdCampaignBannerDomainExclusionPreview ID");
				$error_msg = "Invalid AdCampaignBanner Domain Exclusion Preview ID";
			    $success = false;
			    $data = array(
		         'success' => $success,
		         'data' => array('error_msg' => $error_msg)
	   		   );
	   		 
	           return $this->getResponse()->setContent(json_encode($data));
			endif;

			$banner_preview_id = $rtb_domain_exclusion_preview->AdCampaignBannerPreviewID;
			$exclusion_preview_id = $rtb_domain_exclusion_preview->AdCampaignBannerDomainExclusionPreviewID;

			// ACL PREVIEW PERMISSIONS CHECK
			//transformation\CheckPermissions::checkEditPermissionAdCampaignBannerPreview($banner_preview_id, $auth, $config);
			$response = transformation\CheckPermissions::checkEditPermissionAdCampaignBannerPreview($banner_preview_id, $this->auth, $this->config_handle);
			
			
			if(array_key_exists("error", $response) > 0):
				$success = false;
				$data = array(
			       'success' => $success,
			       'data' => array('error_msg' => $response['error'])
		   		);
		   		
		   	   return $this->getResponse()->setContent(json_encode($data));
			endif;

		endif;

		$AdCampaignBannerDomainExclusionPreviewFactory->deleteAdCampaignBannerDomainExclusionPreview($exclusion_preview_id);

		$data = array(
		         'success' => $success,
				 'location' => '/demand/viewdomainexclusion/',
				 'previewid' => $banner_preview_id,
		         'data' => array('error_msg' => $error_msg)
	   		   );
	   		 
	    return $this->getResponse()->setContent(json_encode($data));
	    
	}

	/**
	 * 
	 * @return \Zend\View\Model\ViewModel
	 */
	public function createdomainexclusionAction() {
		$id = $this->getEvent()->getRouteMatch()->getParam('param1');
		if ($id == null):
			die("Invalid Banner ID");
		endif;

		$initialized = $this->initialize();
		if ($initialized !== true) return $initialized;

		$is_preview = $this->getRequest()->getQuery('ispreview');

		// verify
		if ($is_preview == "true"):
			$is_preview = \transformation\TransformPreview::doesPreviewBannerExist($id, $this->auth);
		endif;

		$banner_preview_id = "";
		$campaign_preview_id = "";
		$campaign_id = "";

		if ($is_preview == "true"):
			// ACL PERMISSIONS CHECK
			transformation\CheckPermissions::checkEditPermissionAdCampaignBannerPreview($id, $this->auth, $this->config_handle);
			$banner_preview_id = $id;
			$id = "";

			$AdCampaignBannerPreviewFactory = \_factory\AdCampaignBannerPreview::get_instance();
			$params = array();
			$params["AdCampaignBannerPreviewID"] = $banner_preview_id;
			$AdCampaignBannerPreview = $AdCampaignBannerPreviewFactory->get_row($params);
			$campaign_preview_id = $AdCampaignBannerPreview->AdCampaignPreviewID;

		else:
			// ACL PERMISSIONS CHECK
			transformation\CheckPermissions::checkEditPermissionAdCampaignBanner($id, $this->auth, $this->config_handle);

			$AdCampaignBannerFactory = \_factory\AdCampaignBanner::get_instance();
			$params = array();
			$params["AdCampaignBannerID"] = $id;
			$AdCampaignBanner = $AdCampaignBannerFactory->get_row($params);
			$campaign_id = $AdCampaignBanner->AdCampaignID;
		endif;

		return new ViewModel(array(
				'ispreview' => $is_preview == true ? '1' : '0',
				'bannerid' => $id,
				'bannerpreviewid' => $banner_preview_id,
				'campaignid' => $campaign_id,
				'campaignpreviewid' => $campaign_preview_id,
				'bread_crumb_info' => $this->getBreadCrumbInfoFromBanner($id, $banner_preview_id, $is_preview),
				'user_id_list' => $this->user_id_list_demand_customer,
    			'center_class' => 'centerj',
    			'user_identity' => $this->identity(),
				'true_user_name' => $this->auth->getUserName(),
				'header_title' => 'Create Domain Exclusion',
				'is_admin' => $this->is_admin,
				'effective_id' => $this->auth->getEffectiveIdentityID(),
				'impersonate_id' => $this->ImpersonateID
		));
	}

	/**
	 * 
	 * @return Ambigous <\Zend\View\Model\ViewModel, \Zend\View\Model\ViewModel>
	 */
	public function newdomainexclusionAction() {

		$needed_input = array(
				'exclusiontype',
				'domainname'
		);

		$initialized = $this->initialize();
		if ($initialized !== true) return $initialized;

		$this->validateInput($needed_input);

		$bannerid 				= $this->getRequest()->getPost('bannerid');
		$banner_preview_id 		= $this->getRequest()->getPost('bannerpreviewid');
		$ispreview 				= $this->getRequest()->getPost('ispreview');

		if ($ispreview != true):
			/*
			 * THIS METHOD CHECKS IF THERE IS AN EXISTING PREVIEW MODE CAMPAIGN
			* IF NOT, IT CHECKS THE ACL PERMISSIONS ON THE PRODUCTION BANNER/CAMPAIGN REFERENCED
			* THEN IT CREATES A PREVIEW VERSION OF THE AD CAMPAIGN
			*/
			$update_data = array('type'=>'AdCampaignBannerID', 'id'=>$bannerid);
			$return_val = \transformation\TransformPreview::previewCheckBannerID($bannerid, $this->auth, $this->config_handle, $this->getServiceLocator()->get('mail.transport'), $update_data);

			if ($return_val !== null):
				$banner_preview_id = $return_val["AdCampaignBannerPreviewID"];
			endif;

		endif;

		// ACL PREVIEW PERMISSIONS CHECK
		transformation\CheckPermissions::checkEditPermissionAdCampaignBannerPreview($banner_preview_id, $this->auth, $this->config_handle);

		$exclusiontype = $this->getRequest()->getPost('exclusiontype');
		$domainname = $this->getRequest()->getPost('domainname');

		$BannerDomainExclusionPreview = new \model\AdCampaignBannerDomainExclusionPreview();
		$BannerDomainExclusionPreview->AdCampaignBannerPreviewID           = $banner_preview_id;
		$BannerDomainExclusionPreview->ExclusionType             = $exclusiontype;
		$BannerDomainExclusionPreview->DomainName                = $domainname;
		$BannerDomainExclusionPreview->DateCreated               = date("Y-m-d H:i:s");
		$BannerDomainExclusionPreview->DateUpdated               = date("Y-m-d H:i:s");

		$AdCampaignBannerDomainExclusionPreviewFactory = \_factory\AdCampaignBannerDomainExclusionPreview::get_instance();
		$AdCampaignBannerDomainExclusionPreviewFactory->saveAdCampaignBannerDomainExclusionPreview($BannerDomainExclusionPreview);

		$refresh_url = "/demand/viewdomainexclusion/" . $banner_preview_id . "?ispreview=true";
		$viewModel = new ViewModel(array('refresh_url' => $refresh_url));

		return $viewModel->setTemplate('dashboard-manager/demand/interstitial.phtml');

	}

	/*
	 * END NGINAD AdCampaignBannerDomainExclusion Actions
	*/

	/*
	 * BEGIN NGINAD AdCampaignBanner Actions
	*/

	/**
	 * 
	 * @return Ambigous <\Zend\View\Model\ViewModel, \Zend\View\Model\ViewModel>
	 */
	public function deletebannerAction() {
		
		$error_msg = null;
		$success = true;
		$id = $this->getEvent()->getRouteMatch()->getParam('param1');
		if ($id == null):
		  //die("Invalid Banner ID");
		   $error_msg = "Invalid Banner ID";
		   $success = false;
		   $data = array(
	         'success' => $success,
	         'data' => array('error_msg' => $error_msg)
   		  );
   		 
          return $this->getResponse()->setContent(json_encode($data));
		endif;

		$initialized = $this->initialize();
		if ($initialized !== true) return $initialized;

		$is_preview = $this->getRequest()->getQuery('ispreview');

		// verify
		if ($is_preview != "true"):

			/*
			 * THIS METHOD CHECKS IF THERE IS AN EXISTING PREVIEW MODE CAMPAIGN
			* IF NOT, IT CHECKS THE ACL PERMISSIONS ON THE PRODUCTION BANNER/CAMPAIGN REFERENCED
			* THEN IT CREATES A PREVIEW VERSION OF THE AD CAMPAIGN
			*/

			$update_data = array('type'=>'AdCampaignBannerID', 'id'=>$id);
			$return_val = \transformation\TransformPreview::previewCheckBannerID($id, $this->auth, $this->config_handle, $this->getServiceLocator()->get('mail.transport'), $update_data);

			if ($return_val !== null && array_key_exists("error", $return_val)):

				$success = false;
				$data = array(
			       'success' => $success,
			       'data' => array('error_msg' => $return_val['error'])
		   		);
   		
		   	   return $this->getResponse()->setContent(json_encode($data));
			endif;
			
			if ($return_val !== null):
				$id 	= $return_val["AdCampaignBannerPreviewID"];
			endif;
	   endif;

		// ACL PREVIEW PERMISSIONS CHECK
		//transformation\CheckPermissions::checkEditPermissionAdCampaignBannerPreview($id, $auth, $config);
		$response = transformation\CheckPermissions::checkEditPermissionAdCampaignBannerPreview($id, $this->auth, $this->config_handle);
		if(array_key_exists("error", $response) > 0):
			$success = false;
			$data = array(
		       'success' => $success,
		       'data' => array('error_msg' => $response['error'])
	   		);
	   		
	   	   return $this->getResponse()->setContent(json_encode($data));
		endif;

		$AdCampaignBannerPreviewFactory = \_factory\AdCampaignBannerPreview::get_instance();
		$params = array();
		$params["AdCampaignBannerPreviewID"] = $id;

		$AdCampaignBannerPreview = $AdCampaignBannerPreviewFactory->get_row($params);

		if ($AdCampaignBannerPreview == null):
		  //die("Invalid Banner ID");
		  $error_msg = "Invalid Banner ID";
		   $success = false;
		   $data = array(
	         'success' => $success,
	         'data' => array('error_msg' => $error_msg)
   		  );
   		 
          return $this->getResponse()->setContent(json_encode($data));
		endif;

		$campaign_preview_id = $AdCampaignBannerPreview->AdCampaignPreviewID;

		$AdCampaignBannerPreviewFactory->deActivateAdCampaignBannerPreview($id);

		$data = array(
	        'success' => $success,
			'location' => '/demand/viewbanner/',
			'previewid' => $campaign_preview_id,
	        'data' => array('error_msg' => $error_msg)
   		 );
   		 
        return $this->getResponse()->setContent(json_encode($data));
		
		/*$refresh_url = "/demand/viewbanner/" . $campaign_preview_id . "?ispreview=true";
		$viewModel = new ViewModel(array('refresh_url' => $refresh_url));

		return $viewModel->setTemplate('dashboard-manager/demand/interstitial.phtml');*/

	}

	/**
	 * 
	 * @return \Zend\View\Model\ViewModel
	 */
	public function viewbannerAction() {
	    $id = $this->getEvent()->getRouteMatch()->getParam('param1');
        if ($id == null):
            die("Invalid Campaign ID");
        endif;

		$initialized = $this->initialize();
		if ($initialized !== true) return $initialized;

        $is_preview = $this->getRequest()->getQuery('ispreview');
        $campaign_preview_id = "";

        // verify
		if ($is_preview == "true"):
			$is_preview = \transformation\TransformPreview::doesPreviewAdCampaignExist($id, $this->auth);
		endif;

		if ($is_preview == true):
			// ACL PERMISSIONS CHECK
			transformation\CheckPermissions::checkEditPermissionAdCampaignPreview($id, $this->auth, $this->config_handle);

			$AdCampaignBannerPreviewFactory = \_factory\AdCampaignBannerPreview::get_instance();
			$params = array();
			$params["AdCampaignPreviewID"] = $id;
			$params["Active"] = 1;

			$rtb_banner_list = $AdCampaignBannerPreviewFactory->get($params);
			$campaign_preview_id = $id;
			$id = "";
		else:
			// ACL PERMISSIONS CHECK
			transformation\CheckPermissions::checkEditPermissionAdCampaign($id, $this->auth, $this->config_handle);

			$AdCampaignBannerFactory = \_factory\AdCampaignBanner::get_instance();
			$params = array();
			$params["AdCampaignID"] = $id;
			$params["Active"] = 1;
			$rtb_banner_list = $AdCampaignBannerFactory->get($params);

		endif;

		$navigation = $this->getServiceLocator()->get('navigation');
                $page = $navigation->findBy('id', 'ViewBannerLevel');
                $page->set("label","View Banners (" . $this->getBreadCrumbInfoFromAdCampaign($id, $campaign_preview_id, $is_preview)["BCAdCampaign"] . ")");
                $page->set("params", array("param1" => $id));

        if ($is_preview == true):
        	$ad_campaign_id = $campaign_preview_id;
   		else:
        	$ad_campaign_id = $id;
     	endif;
                
		return new ViewModel(array(
				'ispreview'	  => $is_preview == true ? '1' : '0',
				'rtb_banners' => $rtb_banner_list,
		        'campaign_id' => $id,
				'campaign_preview_id' => $campaign_preview_id,
				'bread_crumb_info' => $this->getBreadCrumbInfoFromAdCampaign($id, $campaign_preview_id, $is_preview),
				'user_id_list' => $this->user_id_list_demand_customer,
	    		'user_identity' => $this->identity(),
				'true_user_name' => $this->auth->getUserName(),
				'header_title' => '<a href="/demand/createbanner/' . $ad_campaign_id . $this->preview_query . '">Create New Ad Campaign Banner</a>',
				'is_admin' => $this->is_admin,
				'effective_id' => $this->auth->getEffectiveIdentityID(),
				'impersonate_id' => $this->ImpersonateID
		));
	}

	/**
	 * 
	 * @return \Zend\View\Model\ViewModel
	 */
	public function createbannerAction() {
	    $id = $this->getEvent()->getRouteMatch()->getParam('param1');
        if ($id == null):
            die("Invalid Campaign ID");
        endif;

		$initialized = $this->initialize();
		if ($initialized !== true) return $initialized;

        $is_preview = $this->getRequest()->getQuery('ispreview');

        // verify
        if ($is_preview == "true"):
        	$is_preview = \transformation\TransformPreview::doesPreviewAdCampaignExist($id, $this->auth);
        endif;

        $campaignpreviewid = "";

        if ($is_preview == "true"):
	        // ACL PERMISSIONS CHECK
	        transformation\CheckPermissions::checkEditPermissionAdCampaignPreview($id, $this->auth, $this->config_handle);
        	$campaignpreviewid = $id;
        	$id = "";
        else:
	        // ACL PERMISSIONS CHECK
	        transformation\CheckPermissions::checkEditPermissionAdCampaign($id, $this->auth, $this->config_handle);
        endif;

        $current_mimes 					= array();
        $current_apis_supported 		= array();
        $current_protocols 				= array();
        $current_delivery_methods 		= array();
        $current_playback_methods 		= array();
        
        $current_start_delay 			= "";
        $current_linearity 				= "";
        
        return new ViewModel(array(
        		'ispreview'	  				=> $is_preview == true ? '1' : '0',
        		'campaignid'       			=> $id,
        		'campaignpreviewid' 		=> $campaignpreviewid,
        		'adcampaigntype_options'   	=> $this->getAdCampaignTypeOptions(),
                'mobile_options'    		=> \util\BannerOptions::$mobile_options,
                'size_list'         		=> \util\BannerOptions::$iab_banner_options,
				'bread_crumb_info' 			=> $this->getBreadCrumbInfoFromAdCampaign($id, $campaignpreviewid, $is_preview),
        		'user_id_list' => $this->user_id_list_demand_customer,
    			'center_class' 				=> 'centerj',
	    		'user_identity' 			=> $this->identity(),
	    		'true_user_name' => $this->auth->getUserName(),
				'header_title' => 'Create Ad Campaign Banner',
				'is_admin' => $this->is_admin,
				'effective_id' => $this->auth->getEffectiveIdentityID(),
				'impersonate_id' => $this->ImpersonateID,
        		
        		'linearity' => \util\BannerOptions::$linearity,
        		'start_delay' => \util\BannerOptions::$start_delay,
        		'playback_methods' => \util\BannerOptions::$playback_methods,
        		'delivery_methods' => \util\BannerOptions::$delivery_methods,
        		'apis_supported' => \util\BannerOptions::$apis_supported,
        		'protocols' => \util\BannerOptions::$protocols,
        		'mimes' => \util\BannerOptions::$mimes,
        		
        		'current_mimes' => $current_mimes,
        		'current_apis_supported' => $current_apis_supported,
        		'current_protocols' => $current_protocols,
        		'current_delivery_methods' => $current_delivery_methods,
        		'current_playback_methods' => $current_playback_methods,
        		'current_start_delay' => $current_start_delay,
        		'current_linearity' => $current_linearity,
        ));
	}

	private function getAdCampaignTypeOptions() {
		
		$AdCampaignTypeFactory = \_factory\AdCampaignType::get_instance();
		$params = array();
		$AdCampaignTypeList = $AdCampaignTypeFactory->get($params);
		
		$adcampaigntype_options = array();
		
		foreach ($AdCampaignTypeList as $AdCampaignType):

			$adcampaigntype_options[$AdCampaignType->AdCampaignTypeID] = $AdCampaignType->Description;
		
		endforeach;
		
		return $adcampaigntype_options;
	}
	
	/**
	 * 
	 * @return Ambigous <\Zend\View\Model\ViewModel, \Zend\View\Model\ViewModel>
	 */
	public function newbannerAction() {

		$initialized = $this->initialize();
		if ($initialized !== true) return $initialized;

		$ImpressionType = $this->getRequest()->getPost('ImpressionType');
		
		if ($ImpressionType != 'banner' && $ImpressionType != 'video'):
			die("Required Field: ImpressionType was missing");
		endif;
		
		$needed_input_banner = array(
				'bannername',
				'startdate',
				'enddate',
				'ismobile',
				'iabsize',
				'height',
				'width',
				'bidamount',
				'adtag',
				'landingpagetld'
		);
		
		$needed_input_video = array(
				'bannername',
				'startdate',
				'enddate',
				'bidamount',
				'adtag',
				'landingpagetld'
		);
		
		if ($ImpressionType == 'video'):
			$this->validateInput($needed_input_video);
		else:
			$this->validateInput($needed_input_banner);
		endif;
		
		$adcampaigntype 		= AD_TYPE_ANY_REMNANT;
		$linkedzones 			= array();
		
		$campaignid 			= $this->getRequest()->getPost('campaignid');
		$campaign_preview_id 	= $this->getRequest()->getPost('campaignpreviewid');
		$bannerid 				= $this->getRequest()->getPost('bannerid');
		$banner_preview_id 		= $this->getRequest()->getPost('bannerpreviewid');
		$ispreview 				= $this->getRequest()->getPost('ispreview');

		if ($ispreview != true):
			/*
			 * THIS METHOD CHECKS IF THERE IS AN EXISTING PREVIEW MODE CAMPAIGN
			* IF NOT, IT CHECKS THE ACL PERMISSIONS ON THE PRODUCTION BANNER/CAMPAIGN REFERENCED
			* THEN IT CREATES A PREVIEW VERSION OF THE AD CAMPAIGN
			*/

			if ($bannerid != null):
				$update_data = array('type'=>'AdCampaignBannerID', 'id'=>$bannerid);
			else:
				$update_data = array('type'=>'AdCampaignID', 'id'=>$campaignid);
			endif;

			$return_val = \transformation\TransformPreview::previewCheckAdCampaignID($campaignid, $this->auth, $this->config_handle, $this->getServiceLocator()->get('mail.transport'), $update_data);

			if ($return_val !== null):
				if ($bannerid != null):
					$campaign_preview_id 	= $return_val["AdCampaignPreviewID"];
					$banner_preview_id 		= $return_val["AdCampaignBannerPreviewID"];
				else:
					$campaign_preview_id 	= $return_val["AdCampaignPreviewID"];
				endif;
			endif;
		endif;

		// ACL PREVIEW PERMISSIONS CHECK
		transformation\CheckPermissions::checkEditPermissionAdCampaignPreview($campaign_preview_id, $this->auth, $this->config_handle);

		$bannername = $this->getRequest()->getPost('bannername');
		$startdate = $this->getRequest()->getPost('startdate');
		$enddate = $this->getRequest()->getPost('enddate');
		if ($this->is_admin):
			$adcampaigntype 		= $this->getRequest()->getPost('adcampaigntype');
			$linkedzones 			= $this->getRequest()->getPost('linkedzones');
		endif;
		$ismobile = $this->getRequest()->getPost('ismobile');
		$iabsize = $this->getRequest()->getPost('iabsize');
		$height = $this->getRequest()->getPost('height');
		$width = $this->getRequest()->getPost('width');
		$weight = $this->getRequest()->getPost('weight');
		$bidamount = $this->getRequest()->getPost('bidamount');
		$adtag = $this->getRequest()->getPost('adtag');
		$landingpagetld = $this->getRequest()->getPost('landingpagetld');
		$bannerid = $this->getRequest()->getPost('bannerid');

		if ($ImpressionType == 'video'):

			$mimes 						= $this->getRequest()->getPost("Mimes");
			if ($mimes && is_array($mimes) && count($mimes) > 0):
				$mimes = join(',', $mimes);
			else:
				$mimes = "";
			endif;
					
			$protocols 					= $this->getRequest()->getPost("Protocols");
			if ($protocols && is_array($protocols) && count($protocols) > 0):
				$protocols = join(',', $protocols);
			else:
				$protocols = "";
			endif;
					
			$apis_supported 			= $this->getRequest()->getPost("ApisSupported");
			if ($apis_supported && is_array($apis_supported) && count($apis_supported) > 0):
				$apis_supported = join(',', $apis_supported);
			else:
				$apis_supported = "";
			endif;
					
			$delivery 					= $this->getRequest()->getPost("Delivery");
			if ($delivery && is_array($delivery) && count($delivery) > 0):
				$delivery = join(',', $delivery);
			else:
				$delivery = "";
			endif;
					
			$playback 					= $this->getRequest()->getPost("Playback");
			if ($playback && is_array($playback) && count($playback) > 0):
				$playback = join(',', $playback);
			else:
				$playback = "";
			endif;
					
			$start_delay 				= $this->getRequest()->getPost("StartDelay");
					
			$linearity 					= $this->getRequest()->getPost("Linearity");

		endif;
		
		$BannerPreview = new \model\AdCampaignBannerPreview();
		if ($banner_preview_id != null):
		  $BannerPreview->AdCampaignBannerPreviewID             = $banner_preview_id;
		endif;

		$BannerPreview->UserID             	= $this->auth->getEffectiveUserID();

		$BannerPreview->Name                      = $bannername;
		$BannerPreview->AdCampaignPreviewID       = $campaign_preview_id;
		$BannerPreview->StartDate                 = date("Y-m-d H:i:s", strtotime($startdate));
		$BannerPreview->EndDate                   = date("Y-m-d H:i:s", strtotime($enddate));
		if ($this->is_admin || $banner_preview_id == null):
			$BannerPreview->AdCampaignTypeID      = $adcampaigntype;
		endif;
		
		$BannerPreview->ImpressionType			  = $ImpressionType;
		$BannerPreview->IsMobile                  = $ismobile;
		$BannerPreview->IABSize                   = $iabsize;
		$BannerPreview->Height                    = $height;
		$BannerPreview->Width                     = $width;
		$BannerPreview->Weight          		  = $weight == null ? 5 : $weight;
		$BannerPreview->BidAmount                 = $bidamount;
		$BannerPreview->AdTag                     = trim($adtag);
		$BannerPreview->DeliveryType              = 'js';
		$BannerPreview->LandingPageTLD            = $landingpagetld;
		$BannerPreview->ImpressionsCounter        = 0;
		$BannerPreview->BidsCounter               = 0;
		$BannerPreview->CurrentSpend              = 0;
		$BannerPreview->Active                    = 1;
		$BannerPreview->DateCreated               = date("Y-m-d H:i:s");
		$BannerPreview->DateUpdated               = date("Y-m-d H:i:s");
		$BannerPreview->ChangeWentLive       	  = 0;

		$AdCampaignBannerPreviewFactory = \_factory\AdCampaignBannerPreview::get_instance();
		$banner_preview_id_new = $AdCampaignBannerPreviewFactory->saveAdCampaignBannerPreview($BannerPreview);

		if ($banner_preview_id_new != null):
			$banner_preview_id = $banner_preview_id_new;
		elseif ($BannerPreview->AdCampaignBannerPreviewID == null):
			$BannerPreview->AdCampaignBannerPreviewID = $banner_preview_id;
		endif;
		
		$AdCampaignVideoRestrictionsPreviewFactory = \_factory\AdCampaignVideoRestrictionsPreview::get_instance();
		$AdCampaignBannerRestrictionsPreviewFactory = \_factory\AdCampaignBannerRestrictionsPreview::get_instance();
		
		if ($ImpressionType == 'video'):

			$params = array();
			$params["AdCampaignBannerPreviewID"] = $banner_preview_id;
			$AdCampaignVideoRestrictionsPreview = $AdCampaignVideoRestrictionsPreviewFactory->get_row($params);
			
			if ($AdCampaignVideoRestrictionsPreview == null):
			
				$AdCampaignVideoRestrictionsPreview = new \model\AdCampaignVideoRestrictionsPreview();
				
			endif;
			
			$AdCampaignVideoRestrictionsPreview->AdCampaignBannerPreviewID 			= $banner_preview_id;

			$AdCampaignVideoRestrictionsPreview->DateCreated               			= date("Y-m-d H:i:s");
			
			$AdCampaignVideoRestrictionsPreview->MimesCommaSeparated 				= trim($mimes);
			$AdCampaignVideoRestrictionsPreview->ProtocolsCommaSeparated 			= trim($protocols);
			$AdCampaignVideoRestrictionsPreview->ApisSupportedCommaSeparated 		= trim($apis_supported);
			$AdCampaignVideoRestrictionsPreview->DeliveryCommaSeparated 			= trim($delivery);
			$AdCampaignVideoRestrictionsPreview->PlaybackCommaSeparated 			= trim($playback);
			
			$AdCampaignVideoRestrictionsPreview->StartDelay 						= trim($start_delay);
			$AdCampaignVideoRestrictionsPreview->Linearity 							= trim($linearity);

			
			$AdCampaignVideoRestrictionsPreviewFactory->saveAdCampaignVideoRestrictionsPreview($AdCampaignVideoRestrictionsPreview);
			
			$AdCampaignBannerRestrictionsPreviewFactory->deleteAdCampaignBannerRestrictionsPreview($banner_preview_id);
			
		else:
		
			$AdCampaignVideoRestrictionsPreviewFactory->deleteAdCampaignVideoRestrictionsPreview($banner_preview_id);
			
		endif;
		
		if ($this->is_admin):
		
			$LinkedBannerToAdZonePreviewFactory = \_factory\LinkedBannerToAdZonePreview::get_instance();
			$LinkedBannerToAdZonePreviewFactory->deleteLinkedBannerToAdZonePreview($BannerPreview->AdCampaignBannerPreviewID);
			
			// campaigntype AD_TYPE_CONTRACT case
			if ($adcampaigntype == AD_TYPE_CONTRACT && $linkedzones != null && count($linkedzones) > 0):
			
				foreach($linkedzones as $linked_zone_id):
					
					$LinkedBannerToAdZonePreview = new \model\LinkedBannerToAdZonePreview();
					$LinkedBannerToAdZonePreview->AdCampaignBannerPreviewID = $BannerPreview->AdCampaignBannerPreviewID;
					$LinkedBannerToAdZonePreview->PublisherAdZoneID			= intval($linked_zone_id);
					$LinkedBannerToAdZonePreview->Weight					= intval($weight);
					$LinkedBannerToAdZonePreview->DateCreated				= date("Y-m-d H:i:s");
					$LinkedBannerToAdZonePreview->DateUpdated				= date("Y-m-d H:i:s");
					$LinkedBannerToAdZonePreviewFactory->saveLinkedBannerToAdZonePreview($LinkedBannerToAdZonePreview);
				endforeach;
			
			endif;
			
		endif;

		$refresh_url = "/demand/viewbanner/" . $BannerPreview->AdCampaignPreviewID . "?ispreview=true";
		$viewModel = new ViewModel(array('refresh_url' => $refresh_url));

		return $viewModel->setTemplate('dashboard-manager/demand/interstitial.phtml');

	}

	/**
	 * 
	 * @return \Zend\View\Model\ViewModel
	 */
	public function editbannerAction() {

		$id = $this->getEvent()->getRouteMatch()->getParam('param1');
		if ($id == null):
		  die("Invalid Banner ID");
		endif;

		$initialized = $this->initialize();
		if ($initialized !== true) return $initialized;

		$is_preview = $this->getRequest()->getQuery('ispreview');
		
		// verify
		if ($is_preview == "true"):
			$is_preview = \transformation\TransformPreview::doesPreviewBannerExist($id, $this->auth);
		endif;
		$banner_preview_id = "";

		if ($is_preview == true):

			// ACL PREVIEW PERMISSIONS CHECK
			transformation\CheckPermissions::checkEditPermissionAdCampaignBannerPreview($id, $this->auth, $this->config_handle);

			$AdCampaignVideoRestrictionsPreviewFactory = \_factory\AdCampaignVideoRestrictionsPreview::get_instance();
			$params = array();
			$params["AdCampaignBannerPreviewID"] = $id;
			$AdCampaignVideoRestrictions = $AdCampaignVideoRestrictionsPreviewFactory->get_row($params);
			
			$AdCampaignBannerPreviewFactory = \_factory\AdCampaignBannerPreview::get_instance();
			$params = array();
			$params["Active"] = 1;
			$params["AdCampaignBannerPreviewID"] = $id;
			$banner_preview_id = $id;

			$AdCampaignBanner = $AdCampaignBannerPreviewFactory->get_row($params);

		else:
			// ACL PERMISSIONS CHECK
			transformation\CheckPermissions::checkEditPermissionAdCampaignBanner($id, $this->auth, $this->config_handle);

			$AdCampaignVideoRestrictionsFactory = \_factory\AdCampaignVideoRestrictions::get_instance();
			$params = array();
			$params["AdCampaignBannerID"] = $id;
			
			$AdCampaignVideoRestrictions = $AdCampaignVideoRestrictionsFactory->get_row($params);

			$AdCampaignBannerFactory = \_factory\AdCampaignBanner::get_instance();
			$params = array();
			$params["Active"] = 1;
			$params["AdCampaignBannerID"] = $id;

			$AdCampaignBanner = $AdCampaignBannerFactory->get_row($params);

		endif;

		if ($AdCampaignBanner == null):
		  die("Invalid $AdCampaignBanner ID");
		endif;

		$campaignid               = isset($AdCampaignBanner->AdCampaignID) ? $AdCampaignBanner->AdCampaignID : "";
		$bannerid                 = isset($AdCampaignBanner->AdCampaignBannerID) ? $AdCampaignBanner->AdCampaignBannerID : "";
		$campaignpreviewid        = isset($AdCampaignBanner->AdCampaignPreviewID) ? $AdCampaignBanner->AdCampaignPreviewID : "";
		$bannerpreviewid          = isset($AdCampaignBanner->AdCampaignBannerPreviewID) ? $AdCampaignBanner->AdCampaignBannerPreviewID : "";
		$bannername               = $AdCampaignBanner->Name;
		$startdate                = date('m/d/Y', strtotime($AdCampaignBanner->StartDate));
		$enddate                  = date('m/d/Y', strtotime($AdCampaignBanner->EndDate));
		$current_adcampaigntype   = $AdCampaignBanner->AdCampaignTypeID;
		$current_mobile           = $AdCampaignBanner->IsMobile;
		if ($AdCampaignBanner->IsMobile == 2):
		      $size_list                = \util\BannerOptions::$iab_mobile_tablet_banner_options;
		elseif ($AdCampaignBanner->IsMobile > 0):
		      $size_list                = \util\BannerOptions::$iab_mobile_phone_banner_options;
		else:
		      $size_list                = \util\BannerOptions::$iab_banner_options;
		endif;
		$height                   = $AdCampaignBanner->Height;
		$width                    = $AdCampaignBanner->Width;
		$weight                   = $AdCampaignBanner->Weight;
		$bidamount                = $AdCampaignBanner->BidAmount;
		$adtag                    = $AdCampaignBanner->AdTag;
		$landingpagetld           = $AdCampaignBanner->LandingPageTLD;
		$current_iabsize          = $AdCampaignBanner->IABSize;
		
		$ImpressionType           = $AdCampaignBanner->ImpressionType;

		$current_mimes 					= array();
		$current_apis_supported 		= array();
		$current_protocols 				= array();
		$current_delivery_methods 		= array();
		$current_playback_methods 		= array();
		
		$current_start_delay 			= "";
		$current_linearity 				= "";
		
		$impression_type				= "banner";
		
		if ($AdCampaignVideoRestrictions != null):
		
			$current_mimes_raw = $AdCampaignVideoRestrictions->MimesCommaSeparated;
			$current_apis_supported_raw = $AdCampaignVideoRestrictions->ApisSupportedCommaSeparated;
			$current_protocols_raw = $AdCampaignVideoRestrictions->ProtocolsCommaSeparated;
			$current_delivery_methods_raw = $AdCampaignVideoRestrictions->DeliveryCommaSeparated;
			$current_playback_methods_raw = $AdCampaignVideoRestrictions->PlaybackCommaSeparated;
			
			$current_start_delay = $AdCampaignVideoRestrictions->StartDelay;
			$current_linearity = $AdCampaignVideoRestrictions->Linearity;

			$current_mimes = array();
			
			if ($current_mimes_raw):
			
				$current_mimes = explode(',', $current_mimes_raw);
			
			endif;
			
			$current_apis_supported = array();
			
			if ($current_apis_supported_raw):
			
				$current_apis_supported = explode(',', $current_apis_supported_raw);
			
			endif;
			
			$current_protocols = array();
			
			if ($current_protocols_raw):
			
				$current_protocols = explode(',', $current_protocols_raw);
			
			endif;
			
			$current_delivery_methods = array();
			
			if ($current_delivery_methods_raw):
			
				$current_delivery_methods = explode(',', $current_delivery_methods_raw);
			
			endif;
			
			$current_playback_methods = array();
			
			if ($current_playback_methods_raw):
			
				$current_playback_methods = explode(',', $current_playback_methods_raw);
			
			endif;
			
		endif;

		$is_vast_url = \util\ParseHelper::isVastURL($adtag);
		$vast_type = $is_vast_url == true ? "url" : "xml";
		
		return new ViewModel(array(
				'campaignid'              => $campaignid,
		        'bannerid'                => $bannerid,
				'campaignpreviewid'       => $campaignpreviewid,
				'bannerpreviewid'         => $bannerpreviewid,
				'ispreview' 			  => $is_preview == true ? '1' : '0',
    		    'bannername'              => $bannername,
    		    'startdate'               => $startdate,
    		    'enddate'                 => $enddate,
				'current_adcampaigntype'  => $current_adcampaigntype,
				'adcampaigntype_options'  => $this->getAdCampaignTypeOptions(),
				'current_mobile'          => $current_mobile,
		        'mobile_options'          => \util\BannerOptions::$mobile_options,
    		    'size_list'               => $size_list,
    		    'height'                  => $height,
    		    'width'                   => $width,
				'weight'                  => $weight,
    		    'bidamount'               => $bidamount,
    		    'adtag'                   => $adtag,
				'vast_type'			      => $vast_type,
		        'landingpagetld'          => $landingpagetld,
    		    'current_iabsize'         => $current_iabsize,
				'bread_crumb_info'		  => $this->getBreadCrumbInfoFromBanner($bannerid, $bannerpreviewid, $is_preview),
				'user_id_list' => $this->user_id_list_demand_customer,
    			'center_class' => 'centerj',
	    		'user_identity' => $this->identity(),
				'true_user_name' => $this->auth->getUserName(),
				'header_title' => 'Edit Ad Campaign Banner',
				'is_admin' => $this->is_admin,
				'effective_id' => $this->auth->getEffectiveIdentityID(),
				'impersonate_id' => $this->ImpersonateID,
				'ImpressionType' => $ImpressionType,
				
				'linearity' => \util\BannerOptions::$linearity,
				'start_delay' => \util\BannerOptions::$start_delay,
				'playback_methods' => \util\BannerOptions::$playback_methods,
				'delivery_methods' => \util\BannerOptions::$delivery_methods,
				'apis_supported' => \util\BannerOptions::$apis_supported,
				'protocols' => \util\BannerOptions::$protocols,
				'mimes' => \util\BannerOptions::$mimes,
				
				'current_mimes' => $current_mimes,
				'current_apis_supported' => $current_apis_supported,
				'current_protocols' => $current_protocols,
				'current_delivery_methods' => $current_delivery_methods,
				'current_playback_methods' => $current_playback_methods,
				'current_start_delay' => $current_start_delay,
				'current_linearity' => $current_linearity,
				
				'impression_type' => $impression_type
		));
	}

	/**
	 *
	 * @return JSON encoded data for AJAX call
	 */
	public function editlinkedzoneAction() {

		$id 		= $this->getEvent()->getRouteMatch()->getParam('param1');
		$height 	= $this->getRequest()->getQuery('height');
		$width 		= $this->getRequest()->getQuery('width');
		$is_preview = $this->getRequest()->getQuery('is_preview');
				
		if ($height == null || $width == null):
			$data = array(
					'success' => false,
					'linked_ad_zones' => "", 
					'complete_zone_list' => array()
			);
			return $this->getResponse()->setContent(json_encode($data));
		endif;
	
		$initialized = $this->initialize();
		if ($initialized !== true) return $initialized;
		
		if (!$this->is_admin):
			$data = array(
					'success' => false,
					'linked_ad_zones' => "", 
					'complete_zone_list' => array()
			);
			return $this->getResponse()->setContent(json_encode($data));
		endif;

		// verify
		if ($is_preview == "true"):
			$is_preview = \transformation\TransformPreview::doesPreviewBannerExist($id, $this->auth);
		endif;
		$banner_preview_id = "";
		$linked_ad_zones = array();

		if ($id != null):

			if ($is_preview === true):

				// ACL PREVIEW PERMISSIONS CHECK
				transformation\CheckPermissions::checkEditPermissionAdCampaignBannerPreview($id, $this->auth, $this->config_handle);
			
				$AdCampaignBannerPreviewFactory = \_factory\AdCampaignBannerPreview::get_instance();
				$params = array();
				$params["Active"] = 1;
				$params["AdCampaignBannerPreviewID"] = $id;
				$banner_preview_id = $id;
			
				$AdCampaignBanner = $AdCampaignBannerPreviewFactory->get_row($params);
				
				$LinkedBannerToAdZonePreviewFactory = \_factory\LinkedBannerToAdZonePreview::get_instance();
				$params = array();
				$params["AdCampaignBannerPreviewID"] = $id;

				$linked_ad_zones = $LinkedBannerToAdZonePreviewFactory->get($params);
				
			else:
			
				// ACL PERMISSIONS CHECK
				transformation\CheckPermissions::checkEditPermissionAdCampaignBanner($id, $this->auth, $this->config_handle);
			
				$AdCampaignBannerFactory = \_factory\AdCampaignBanner::get_instance();
				$params = array();
				$params["Active"] = 1;
				$params["AdCampaignBannerID"] = $id;
			
				$AdCampaignBanner = $AdCampaignBannerFactory->get_row($params);
	
				$LinkedBannerToAdZoneFactory = \_factory\LinkedBannerToAdZone::get_instance();
				$params = array();
				$params["AdCampaignBannerID"] = $id;
				$linked_ad_zones = $LinkedBannerToAdZoneFactory->get($params);
				
			endif;
		endif;
		
		$params = array();
		$params["Height"] = $height;
		$params["Width"] = $width;
		// $params["AdOwnerID"] = \transformation\UserToPublisher::user_id_to_publisher_info_id($this->auth->getEffectiveUserID());
		
		$PublisherAdZoneFactory = \_factory\PublisherAdZone::get_instance();
		$PublisherAdZoneList = $PublisherAdZoneFactory->get($params);
		if ($PublisherAdZoneList === null):
			$PublisherAdZoneList = array();
		endif;
		
		$complete_zone_list = array();
		
		foreach ($PublisherAdZoneList as $PublisherAdZone):
		
			$complete_zone_list[] = array(
									"zone_id"	=>$PublisherAdZone->PublisherAdZoneID,
									"ad_name"	=>$PublisherAdZone->AdName
			);
		
		endforeach;
		
		$linked_zone_list = array();
		
		foreach ($linked_ad_zones as $linked_ad_zone):
		
			$linked_zone_list[] = $linked_ad_zone->PublisherAdZoneID;
			
		endforeach;
		
		$data = array(
				'success' => count($PublisherAdZoneList) > 0,
				'linked_ad_zones' => implode(',', $linked_zone_list), 
				'complete_zone_list' => $complete_zone_list
		);
		
		return $this->getResponse()->setContent(json_encode($data));

	}
	
	/*
	 * END NGINAD AdCampaignBanner Actions
	*/

	/*
	 * BEGIN NGINAD AdCampaign Actions
	*/

	/**
	 * 
	 * @return Ambigous <\Zend\View\Model\ViewModel, \Zend\View\Model\ViewModel>
	 */
	public function deletecampaignAction() {

		$error_msg = null;
		$success = true;
		$id = $this->getEvent()->getRouteMatch()->getParam('param1');
		if ($id == null):
		  //die("Invalid Campaign ID");
		  $error_msg = "Invalid Campaign ID";
		  $success = false;
		  $data = array(
	        'success' => $success,
	        'data' => array('error_msg' => $error_msg)
   		 );
   		 
         return $this->getResponse()->setContent(json_encode($data));
		endif;

		$initialized = $this->initialize();
		if ($initialized !== true) return $initialized;

		$is_preview = $this->getRequest()->getQuery('ispreview');

		// verify
		if ($is_preview != "true"):
			/*
			 * THIS METHOD CHECKS IF THERE IS AN EXISTING PREVIEW MODE CAMPAIGN
			* IF NOT, IT CHECKS THE ACL PERMISSIONS ON THE PRODUCTION BANNER/CAMPAIGN REFERENCED
			* THEN IT CREATES A PREVIEW VERSION OF THE AD CAMPAIGN
			*/

			$update_data = array('type'=>'AdCampaignID', 'id'=>$id);
			$return_val = \transformation\TransformPreview::previewCheckAdCampaignID($id, $this->auth, $this->config_handle, $this->getServiceLocator()->get('mail.transport'), $update_data);
			
			if ($return_val !== null && array_key_exists("error", $return_val)):

				$success = false;
				$data = array(
			       'success' => $success,
			       'data' => array('error_msg' => $return_val['error'])
		   		);
   		
		   	   return $this->getResponse()->setContent(json_encode($data));
			endif;

			if ($return_val !== null):
				$id = $return_val["AdCampaignPreviewID"];
			endif;
		endif;

		// ACL PREVIEW PERMISSIONS CHECK
		//transformation\CheckPermissions::checkEditPermissionAdCampaignPreview($id, $auth, $config);
		$response = transformation\CheckPermissions::checkEditPermissionAdCampaignPreview($id, $this->auth, $this->config_handle);

		if(array_key_exists("error", $response) > 0):
			$success = false;
			$data = array(
		       'success' => $success,
		       'data' => array('error_msg' => $response['error'])
	   		);
	   		
	   	   return $this->getResponse()->setContent(json_encode($data));
		endif;

		$AdCampaignPreviewFactory = \_factory\AdCampaignPreview::get_instance();
		$params = array();
		$params["AdCampaignPreviewID"] = $id;

		$AdCampaignPreview = $AdCampaignPreviewFactory->get_row($params);

		if ($AdCampaignPreview == null):
		  //die("Invalid AdCampaign Preview ID");
		  $error_msg = "Invalid AdCampaign Preview ID";
		  $success = false;
		  $data = array(
	        'success' => $success,
	        'data' => array('error_msg' => $error_msg)
   		 );
   		 
         return $this->getResponse()->setContent(json_encode($data));
		endif;

		$ad_campaign_preview_id = $AdCampaignPreview->AdCampaignPreviewID;

		$AdCampaignBannerPreviewFactory = \_factory\AdCampaignBannerPreview::get_instance();
		$params = array();
		$params["AdCampaignPreviewID"] = $AdCampaignPreview->AdCampaignPreviewID;

		$AdCampaignBannerPreviewList = $AdCampaignBannerPreviewFactory->get($params);

        foreach ($AdCampaignBannerPreviewList as $AdCampaignBannerPreview):

            $banner_preview_id = $AdCampaignBannerPreview->AdCampaignBannerPreviewID;
    		$AdCampaignBannerPreviewFactory->deActivateAdCampaignBannerPreview($banner_preview_id);

		endforeach;

    	$AdCampaignPreviewFactory->doDeletedAdCampaignPreview($ad_campaign_preview_id);

		$data = array(
	        'success' => $success,
	        'data' => array('error_msg' => $error_msg)
   		 );
   		 
         return $this->getResponse()->setContent(json_encode($data));
		
		/*$refresh_url = "/demand/?ispreview=true";
		$viewModel = new ViewModel(array('refresh_url' => $refresh_url));

		return $viewModel->setTemplate('dashboard-manager/demand/interstitial.phtml');*/

	}

	/**
	 * 
	 * @return \Zend\View\Model\ViewModel
	 */
	public function editcampaignAction() {
		$id = $this->getEvent()->getRouteMatch()->getParam('param1');
		if ($id == null):
			die("Invalid Campaign ID");
		endif;

		$initialized = $this->initialize();
		if ($initialized !== true) return $initialized;

		$is_preview = $this->getRequest()->getQuery('ispreview');

		// verify
		if ($is_preview == "true"):
			$is_preview = \transformation\TransformPreview::doesPreviewAdCampaignExist($id, $this->auth);
		endif;
		$campaign_preview_id = "";

		if ($is_preview == true):

			// ACL PREVIEW PERMISSIONS CHECK
			transformation\CheckPermissions::checkEditPermissionAdCampaignPreview($id, $this->auth, $this->config_handle);

			$AdCampaignPreviewFactory = \_factory\AdCampaignPreview::get_instance();
			$params = array();
			$params["AdCampaignPreviewID"] = $id;
			$params["Active"] = 1;

			$AdCampaign = $AdCampaignPreviewFactory->get_row($params);

			$campaign_preview_id = $id;
			$id = "";

		else:
			// ACL PERMISSIONS CHECK
			transformation\CheckPermissions::checkEditPermissionAdCampaign($id, $this->auth, $this->config_handle);

			$AdCampaignFactory = \_factory\AdCampaign::get_instance();
			$params = array();
			$params["AdCampaignID"] = $id;
			$params["Active"] = 1;

			$AdCampaign = $AdCampaignFactory->get_row($params);

		endif;

		if ($AdCampaign == null):
			die("Invalid AdCampaign ID");
		endif;

		$campaignname              = $AdCampaign->Name;
		$startdate                 = date('m/d/Y', strtotime($AdCampaign->StartDate));
		$enddate                   = date('m/d/Y', strtotime($AdCampaign->EndDate));
		$customername              = $AdCampaign->Customer;
		$customerid                = $AdCampaign->CustomerID;
		$maximpressions            = $AdCampaign->MaxImpressions;
		$maxspend                  = sprintf("%1.2f", $AdCampaign->MaxSpend);


		return new ViewModel(array(
				'campaignid' => $id,
				'campaignpreviewid' => $campaign_preview_id,
				'ispreview' => $is_preview == true ? '1' : '0',
				'campaignname' => $campaignname,
				'startdate' => $startdate,
				'enddate' => $enddate,
				'customername' => $customername,
				'customerid' => $customerid,
				'maximpressions' => $maximpressions,
				'maxspend' => $maxspend,
				'bread_crumb_info' => $this->getBreadCrumbInfoFromAdCampaign($id, $campaign_preview_id, $is_preview),
				'user_id_list' => $this->user_id_list_demand_customer,
    			'center_class' => 'centerj',
	    		'user_identity' => $this->identity(),
	    		'true_user_name' => $this->auth->getUserName(),
				'header_title' => 'Edit Ad Campaign',
				'is_admin' => $this->is_admin,
				'effective_id' => $this->auth->getEffectiveIdentityID(),
				'impersonate_id' => $this->ImpersonateID
		));
	}

	/**
	 * This function does ZERO, right now. Empty.
	 */
	public function createcampaignAction() {

		$initialized = $this->initialize();
		if ($initialized !== true) return $initialized;
		
		return new ViewModel(array(
				'ispreview'	  => "true",
				'user_id_list' => $this->user_id_list_demand_customer,
				'user_identity' => $this->identity(),
	    		'true_user_name' => $this->auth->getUserName(),
				'header_title' => 'Create New Ad Campaign',
				'is_admin' => $this->is_admin,
				'effective_id' => $this->auth->getEffectiveIdentityID(),
				'impersonate_id' => $this->ImpersonateID
		));
	    		
	}

	/**
	 * 
	 * @return Ambigous <\Zend\View\Model\ViewModel, \Zend\View\Model\ViewModel>
	 */
	public function newcampaignAction() {

	    $needed_input = array(
	        'campaignname',
	        'startdate',
	        'enddate',
	        'maximpressions',
	        'maxspend'
	    );

		$initialized = $this->initialize();
		if ($initialized !== true) return $initialized;

	    $this->validateInput($needed_input);

	    $campaignname = $this->getRequest()->getPost('campaignname');
	    $startdate = $this->getRequest()->getPost('startdate');
	    $enddate = $this->getRequest()->getPost('enddate');
	    $customername = $this->getRequest()->getPost('customername');
	    $customerid = $this->getRequest()->getPost('customerid');
	    if (!$customerid) $customerid = "001";
	    $maximpressions = $this->getRequest()->getPost('maximpressions');
	    $maxspend = $this->getRequest()->getPost('maxspend');
	    $campaignid = $this->getRequest()->getPost('campaignid');
	    $campaign_preview_id 		= $this->getRequest()->getPost('campaignpreviewid');
	    $ispreview 					= $this->getRequest()->getPost('ispreview');

	    $AdCampaignPreview = new \model\AdCampaignPreview();

	    if ($campaignid != null && $ispreview != true):
		    /*
		     * THIS METHOD CHECKS IF THERE IS AN EXISTING PREVIEW MODE CAMPAIGN
		    * IF NOT, IT CHECKS THE ACL PERMISSIONS ON THE PRODUCTION BANNER/CAMPAIGN REFERENCED
		    * THEN IT CREATES A PREVIEW VERSION OF THE AD CAMPAIGN
		    */

		    $update_data = array('type'=>'AdCampaignID', 'id'=>$campaignid);
		    $return_val = \transformation\TransformPreview::previewCheckAdCampaignID($campaignid, $this->auth, $this->config_handle, $this->getServiceLocator()->get('mail.transport'), $update_data);

		    if ($return_val !== null):
			    $campaign_preview_id 	= $return_val["AdCampaignPreviewID"];
		    endif;

		    $AdCampaignPreview->AdCampaignID 	= $campaignid;

	    endif;


	    if ($campaign_preview_id != null):

		    // ACL PREVIEW PERMISSIONS CHECK
		    transformation\CheckPermissions::checkEditPermissionAdCampaignPreview($campaign_preview_id, $this->auth, $this->config_handle);
	       	$AdCampaignPreview->AdCampaignPreviewID               = $campaign_preview_id;

	       	$params = array();
	       	$params["AdCampaignPreviewID"] = $campaign_preview_id;
	       	$AdCampaignPreviewFactory = \_factory\AdCampaignPreview::get_instance();
	       	$_AdCampaignPreview = $AdCampaignPreviewFactory->get_row($params);
	       	$AdCampaignPreview->AdCampaignID 	= $_AdCampaignPreview->AdCampaignID;
	    endif;

	    // else new campaign, ispreview is always true

	    $AdCampaignPreview->UserID             			= $this->auth->getEffectiveUserID();

    	$AdCampaignPreview->Name                      = $campaignname;
    	$AdCampaignPreview->StartDate                 = date("Y-m-d H:i:s", strtotime($startdate));
    	$AdCampaignPreview->EndDate                   = date("Y-m-d H:i:s", strtotime($enddate));
    	$AdCampaignPreview->Customer                  = $customername;
    	$AdCampaignPreview->CustomerID                = $customerid;
    	$AdCampaignPreview->ImpressionsCounter        = 0;
    	$AdCampaignPreview->MaxImpressions            = $maximpressions;
    	$AdCampaignPreview->CurrentSpend              = 0;
    	$AdCampaignPreview->MaxSpend                  = $maxspend;
    	$AdCampaignPreview->Active                    = 1;
    	$AdCampaignPreview->DateCreated               = date("Y-m-d H:i:s");
    	$AdCampaignPreview->DateUpdated               = date("Y-m-d H:i:s");
    	$AdCampaignPreview->ChangeWentLive            = 0;

	    $AdCampaignPreviewFactory = \_factory\AdCampaignPreview::get_instance();
	    $new_campaign_preview_id = $AdCampaignPreviewFactory->saveAdCampaignPreview($AdCampaignPreview);

	    if (!$this->is_admin && $new_campaign_preview_id !== null && $this->config_handle['mail']['subscribe']['campaigns'] === true):
	    
		    // if this ad campaign was not created/edited by the admin, then send out a notification email
		    $message = '<b>NginAd Demand Customer Campaign Added by ' . $this->true_user_name . '.</b><br /><br />';
		    $message = $message.'<table border="0" width="10%">';
		    $message = $message.'<tr><td><b>AdCampaignID: </b></td><td>'.$new_campaign_preview_id.'</td></tr>';
		    $message = $message.'<tr><td><b>UserID: </b></td><td>'.$AdCampaignPreview->UserID.'</td></tr>';
		    $message = $message.'<tr><td><b>Name: </b></td><td>'.$AdCampaignPreview->Name.'</td></tr>';
		    $message = $message.'<tr><td><b>StartDate: </b></td><td>'.$AdCampaignPreview->StartDate.'</td></tr>';
		    $message = $message.'<tr><td><b>EndDate: </b></td><td>'.$AdCampaignPreview->EndDate.'</td></tr>';
		    $message = $message.'<tr><td><b>Customer: </b></td><td>'.$AdCampaignPreview->Customer.'</td></tr>';
		    $message = $message.'<tr><td><b>CustomerID: </b></td><td>'.$AdCampaignPreview->CustomerID.'</td></tr>';
		    $message = $message.'<tr><td><b>MaxImpressions: </b></td><td>'.$AdCampaignPreview->MaxImpressions.'</td></tr>';
		    $message = $message.'<tr><td><b>MaxSpend: </b></td><td>'.$AdCampaignPreview->MaxSpend.'</td></tr>';
		    $message = $message.'</table>';
		    	
		    $subject = "NginAd Demand Customer Campaign Added by " . $this->true_user_name;
		    
		    $transport = $this->getServiceLocator()->get('mail.transport');
		    
		    $text = new Mime\Part($message);
		    $text->type = Mime\Mime::TYPE_HTML;
		    $text->charset = 'utf-8';
		    
		    $mimeMessage = new Mime\Message();
		    $mimeMessage->setParts(array($text));
		    $zf_message = new Message();
		    
		    $zf_message->addTo($this->config_handle['mail']['admin-email']['email'], $this->config_handle['mail']['admin-email']['name'])
		    ->addFrom($this->config_handle['mail']['reply-to']['email'], $this->config_handle['mail']['reply-to']['name'])
		    ->setSubject($subject)
		    ->setBody($mimeMessage);
		    $transport->send($zf_message);
		    
	    endif;
	    
		$refresh_url = "/demand/?ispreview=true";
		$viewModel = new ViewModel(array('refresh_url' => $refresh_url));

		return $viewModel->setTemplate('dashboard-manager/demand/interstitial.phtml');

	}

	/*
	 * END NGINAD AdCampaign Actions
	*/

	/*
	 * BEGIN NGINAD Helper Methods
	*/

	/**
	 * 
	 * @param unknown $campaign_id
	 * @param unknown $campaign_preview_id
	 * @param unknown $is_preview
	 * @return multitype:NULL
	 */
	private function getBreadCrumbInfoFromAdCampaign($campaign_id, $campaign_preview_id, $is_preview) {

			if ($is_preview == true):
				return $this->getBreadCrumbInfoFromCampaignPreviewID($campaign_preview_id);
			else:
				return $this->getBreadCrumbInfoFromCampaignID($campaign_id);
			endif;
	}

	/**
	 * 
	 * @param unknown $banner_id
	 * @param unknown $banner_preview_id
	 * @param unknown $is_preview
	 */
	private function getBreadCrumbInfoFromBanner($banner_id, $banner_preview_id, $is_preview) {

			if ($is_preview == true):
				return $this->getBreadCrumbInfoFromAdCampaignBannerPreviewID($banner_preview_id);
			else:
				return $this->getBreadCrumbInfoFromAdCampaignBannerID($banner_id);
			endif;
	}

	/**
	 * 
	 * @param unknown $id
	 * @return unknown
	 */
	private function getBreadCrumbInfoFromAdCampaignBannerID($id) {

		$AdCampaignBannerFactory = \_factory\AdCampaignBanner::get_instance();
		$params = array();
		$params["AdCampaignBannerID"] = $id;

		$AdCampaignBanner = $AdCampaignBannerFactory->get_row($params);

		$bread_crumb_info = $this->getBreadCrumbInfoFromCampaignID($AdCampaignBanner->AdCampaignID);
		$bread_crumb_info["BCBanner"] = $AdCampaignBanner->Name;

		return $bread_crumb_info;

	}

	/**
	 * 
	 * @param unknown $id
	 * @return unknown
	 */
	private function getBreadCrumbInfoFromAdCampaignBannerPreviewID($id) {

		$AdCampaignBannerPreviewFactory = \_factory\AdCampaignBannerPreview::get_instance();
		$params = array();
		$params["AdCampaignBannerPreviewID"] = $id;

		$AdCampaignBannerPreview = $AdCampaignBannerPreviewFactory->get_row($params);

		$bread_crumb_info = $this->getBreadCrumbInfoFromCampaignPreviewID($AdCampaignBannerPreview->AdCampaignPreviewID);
		$bread_crumb_info["BCBanner"] = $AdCampaignBannerPreview->Name;

		return $bread_crumb_info;

	}

	/**
	 * 
	 * @param unknown $id
	 * @return multitype:NULL
	 */
	private function getBreadCrumbInfoFromCampaignID($id) {

		$AdCampaignFactory = \_factory\AdCampaign::get_instance();
		$params = array();
		$params["AdCampaignID"] = $id;

		$AdCampaign = $AdCampaignFactory->get_row($params);

		return array("BCAdCampaign"=>'<a href="/demand/viewbanner/' . $AdCampaign->AdCampaignID . '">' . $AdCampaign->Name . "</a>");

	}

	/**
	 * 
	 * @param unknown $id
	 * @return multitype:NULL
	 */
	private function getBreadCrumbInfoFromCampaignPreviewID($id) {

		$AdCampaignPreviewFactory = \_factory\AdCampaignPreview::get_instance();
		$params = array();
		$params["AdCampaignPreviewID"] = $id;

		$AdCampaignPreview = $AdCampaignPreviewFactory->get_row($params);

		return array("BCAdCampaign"=>'<a href="/demand/viewbanner/' . $AdCampaignPreview->AdCampaignPreviewID . '?ispreview=true">' . $AdCampaignPreview->Name . "</a>");

	}

	/*
	 * END NGINAD Helper Methods
	*/

}
?>