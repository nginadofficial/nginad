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

		if ($this->is_super_admin):

		    // admin is logged in as a user, get the markup if any for that user
		    if ($this->ImpersonateID != 0 && !empty($this->DemandCustomerInfoID)):
		    
		    	$user_markup = \util\Markup::getMarkupForUser($this->auth->getEffectiveIdentityID(), $this->config_handle, false);
		    	if ($user_markup != null):
		   			$user_markup_rate = $user_markup->MarkupRate;
				endif;
		    endif;

		endif;

	    $InsertionOrderFactory = \_factory\InsertionOrder::get_instance();
	    $params = array();
	    $params["Active"] = 1;
	    // admin should see campaigns requiring approval and the user they belong to ONLY
	    if ($this->is_domain_admin):
	   		// clear publisher impersonate, and set to the demand customer
	    	if ($this->auth->getEffectiveUserID() > 0):
	    		$this->ImpersonateUser();
	    	endif;
	    	$params["UserID"] = $this->auth->getUserID();
	    else:
	    	$params["UserID"] = $this->auth->getEffectiveUserID();
		endif;
		
	    $_ad_campaign_list = $InsertionOrderFactory->get($params);
	    $InsertionOrderPreviewFactory = \_factory\InsertionOrderPreview::get_instance();
	    
	    $ad_campaign_list = array();

	    // admin should see campaigns requiring approval and the user they belong to ONLY
	    foreach ($_ad_campaign_list as $ad_campaign):
		  	$is_preview = \transformation\TransformPreview::doesPreviewInsertionOrderExistForInsertionOrder($ad_campaign->InsertionOrderID, $this->auth);
		 	if ($is_preview != true):
		    	$ad_campaign_list[] = $ad_campaign;
		 		$ad_campaign_markup = \util\Markup::getMarkupForInsertionOrder($ad_campaign->InsertionOrderID, $this->config_handle, false);

		 		if ($ad_campaign_markup != null):
		 			$campaign_markup_rate_list[$ad_campaign->InsertionOrderID] = $ad_campaign_markup->MarkupRate * 100;
		 		else:
		 			$campaign_markup_rate_list[$ad_campaign->InsertionOrderID] = $user_markup_rate * 100;
		 		endif;

		 	endif;
		endforeach;

	    // get previews
	    $params = array();
	    $params["Active"] = 1;
	    // $params["Deleted"] = 0;
	    if ($this->is_super_admin == true && $this->auth->getEffectiveIdentityID() != 0):
	   		$params["UserID"] = $this->auth->getEffectiveUserID();
	    elseif ($this->is_super_admin == false):
	    	$params["UserID"] = $this->auth->getUserID();
	    endif;
	    
	    $_ad_campaign_preview_list = $InsertionOrderPreviewFactory->get($params);
	    
	    foreach ($_ad_campaign_preview_list as $ad_campaign_preview):
		    if ($ad_campaign_preview != null):
		    	$ad_campaign_list[] = $ad_campaign_preview;
		    	if ($ad_campaign_preview->InsertionOrderID != null):

				    $ad_campaign_markup = \util\Markup::getMarkupForInsertionOrder($ad_campaign_preview->InsertionOrderID, $this->config_handle, false);

				    if ($ad_campaign_markup != null):
				    	$campaign_markup_rate_list[$ad_campaign_preview->InsertionOrderID] = $ad_campaign_markup->MarkupRate * 100;
				    else:
				    	$campaign_markup_rate_list[$ad_campaign_preview->InsertionOrderID] = $user_markup_rate * 100;
				    endif;

			    endif;
		    endif;
	    endforeach;

	    $user_markup_rate *= 100;

	    $view = new ViewModel(array(
	    		'ad_campaigns' => $ad_campaign_list,
	    		'is_super_admin' => $this->auth->isSuperAdmin($this->config_handle),
	    		'is_domain_admin' => $this->auth->isDomainAdmin($this->config_handle),
	    		'user_id_list' => $this->user_id_list_demand_customer,
	    		'effective_id' => $this->auth->getEffectiveIdentityID(),
	    		'campaign_markup_rate_list'=>$campaign_markup_rate_list,
	    		'user_markup_rate' => $user_markup_rate,
	    		'dashboard_view' => 'private-exchange',
	    		'user_identity' => $this->identity(),
	    		'true_user_name' => $this->auth->getUserName(),
				'is_super_admin' => $this->is_super_admin,
				'effective_id' => $this->auth->getEffectiveIdentityID(),
				'impersonate_id' => $this->ImpersonateID

	    ));
	    
	    if ($this->is_super_admin == false 
	    	|| ($this->is_super_admin == true && $this->DemandCustomerInfoID != null && $this->auth->getEffectiveIdentityID() != 0)):
	    	
	    	$view->header_title = '<a href="/private-exchange/createinsertionorder/">Create Insertion Order</a>';
	    else:
	   		$view->header_title = '&nbsp;';
	    endif;

	    return $view;
	}

	public function editthemeAction()
	{
		$initialized = $this->initialize();
		if ($initialized !== true) return $initialized;
		
		$default_colors 	= $this->config_handle['themes']['default_colors'];
		$server_ip 			= $this->config_handle['vanity_domains']['server_ip'];
		
		if (empty($server_ip) || $server_ip == '127.0.0.1'):
			$server_ip = $_SERVER['SERVER_ADDR'];
		endif;
		
		$vanity_domain 		= '';
		$use_logo	 		= false;
		$use_vanity_domain	= false;
		
		$PrivateExchangeVanityDomainFactory = \_factory\PrivateExchangeVanityDomain::get_instance();
		
		$params = array();
		$params["UserID"] = $this->auth->getUserID();
		$PrivateExchangeVanityDomain = $PrivateExchangeVanityDomainFactory->get_row($params);
		
		if ($PrivateExchangeVanityDomain != null):
			$use_vanity_domain	= true;
			$vanity_domain 		= $PrivateExchangeVanityDomain->VanityDomain;
			$use_logo	 		= $PrivateExchangeVanityDomain->UseLogo == 1 ? true : false;
		endif;

		$PrivateExchangeThemeFactory = \_factory\PrivateExchangeTheme::get_instance();
		
		$params = array();
		$params["UserID"] = $this->auth->getUserID();
		$PrivateExchangeTheme = $PrivateExchangeThemeFactory->get_row($params);
		
		if ($PrivateExchangeTheme != null):
			$theme_params_serialized = $PrivateExchangeTheme->ThemeParamsSerialized;
			try {
				$theme_params = unserialize($theme_params_serialized);
				
				foreach ($default_colors as $key => $value):
					if (isset($theme_params[$key])):
						$default_colors[$key] = $theme_params[$key];
					endif;
				endforeach;
			} catch (Exception $e) { }
		endif;
		
		$assets_dir = 'public/vdomain/' . $this->auth->getUserID() . '/';
		
		$imageurl = '';
		
		if ($use_logo && file_exists($assets_dir . 'logo-lg.png')):
			$imageurl = str_replace('public/', '/', $assets_dir) . 'logo-lg.png';
		endif;
		
		$theme_color_params = array();
		
		foreach ($default_colors as $key => $value):
			
			$label_name = trim(str_replace('_', ' ', $key));
			
			$theme_color_params[] = array (
										"label_name"	=> $label_name,
										"key"			=> $key,
										"value"			=> $value,
									);
		
		endforeach;
		
		$success = $this->getRequest()->getQuery('success');
		$success = $success == 'true' ? true : false;
		
		return new ViewModel(array(
			'theme_color_params' => $theme_color_params,
			'server_ip' => $server_ip,
			'imageurl' => $imageurl,
			'vanity_domain' => $vanity_domain,
			'use_logo' => $use_logo,
			'use_vanity_domain' => $use_vanity_domain,
			'success_message_display' => $success,
	    	'is_super_admin' => $this->auth->isSuperAdmin($this->config_handle),
	    	'is_domain_admin' => $this->auth->isDomainAdmin($this->config_handle),
			'effective_id' => $this->auth->getEffectiveIdentityID(),
			'dashboard_view' => 'private-exchange',
			'user_identity' => $this->identity(),
			'true_user_name' => $this->auth->getUserName(),
			'is_super_admin' => $this->is_super_admin,
			'effective_id' => $this->auth->getEffectiveIdentityID(),
			'impersonate_id' => $this->ImpersonateID
		));
		
	}
	
	public function newvanitydomainAction()
	{
		$initialized = $this->initialize();
		if ($initialized !== true) return $initialized;
		
		$assets_dir = 'public/vdomain/' . $this->auth->getUserID() . '/';
		
		$theme_colors 	= $this->config_handle['themes']['default_colors'];
		
		$vanity_domain 	= $this->getRequest()->getPost("vanity-domain");
		$vd_enabled 	= $this->getRequest()->getPost("vd-enabled");
		$logo_enabled 	= $this->getRequest()->getPost("logo-enabled");
		$reset			= $this->getRequest()->getPost("B2");
		
		if ($reset != "Reset Colors"):
			foreach ($this->getRequest()->getPost() as $key => $value):
	
				if (isset($theme_colors[$key]) && strpos($value, '#') == 0 && strlen($value) <= 7):
					$theme_colors[$key] = $value;
				endif;
	
			endforeach;
		endif;
		
		$serialized_colors = serialize($theme_colors);
		
		$PrivateExchangeVanityDomainFactory = \_factory\PrivateExchangeVanityDomain::get_instance();
		$PrivateExchangeThemeFactory = \_factory\PrivateExchangeTheme::get_instance();
		
		$params = array();
		$params["UserID"] = $this->auth->getUserID();
		$PrivateExchangeTheme = $PrivateExchangeThemeFactory->get_row($params);
		
		$params = array();
		$params["UserID"] = $this->auth->getUserID();
		$_PrivateExchangeTheme = $PrivateExchangeThemeFactory->get_row($params);
			
		$PrivateExchangeTheme = new \model\PrivateExchangeTheme();
			
		$PrivateExchangeTheme->UserID 					= $this->auth->getUserID();
		$PrivateExchangeTheme->ThemeParamsSerialized 	= $serialized_colors;
			
		if ($_PrivateExchangeTheme != null):
			$PrivateExchangeThemeFactory->updatePrivateExchangeTheme($PrivateExchangeTheme);
		else:
			$PrivateExchangeThemeFactory->insertPrivateExchangeTheme($PrivateExchangeTheme);
		endif;
		
		if ($vd_enabled == 1):
			
			if (empty($vanity_domain)):
				die("Missing Vanity Domain: CODE 107");			
			endif;
		
			$vanity_domain = trim(strtolower(str_replace(array('https://', 'http://'), array('', ''), $vanity_domain)));
			
			$site_url = $this->config_handle['delivery']['site_url'];
			$site_url = trim(strtolower(str_replace(array('https://', 'http://'), array('', ''), $site_url)));
			
			if (empty($vanity_domain) || $vanity_domain == $site_url):
				die("Illegal Vanity Domain, try another domain: CODE 108");
			endif;
			
			if (!file_exists($assets_dir)):
				mkdir($assets_dir, 0755, true);
			endif;
			
			$css = file_get_contents('public/css/colorscheme/theme.css.template');

			foreach ($theme_colors as $key => $value):
				$css = str_replace($key, $value, $css);
			endforeach;
			
			$fh = fopen($assets_dir . "theme.css", "w");
			fwrite($fh, $css);
			fclose($fh);
		
			$params = array();
			$params["UserID"] = $this->auth->getUserID();
			$_PrivateExchangeVanityDomain = $PrivateExchangeVanityDomainFactory->get_row($params);
			
			$PrivateExchangeVanityDomain = new \model\PrivateExchangeVanityDomain();
			
			$PrivateExchangeVanityDomain->UserID 		= $this->auth->getUserID();
			$PrivateExchangeVanityDomain->VanityDomain 	= $vanity_domain;
			$PrivateExchangeVanityDomain->UseLogo 		= $logo_enabled == 1 ? 1 : 0;
			
			if ($_PrivateExchangeVanityDomain != null):
				$PrivateExchangeVanityDomainFactory->updatePrivateExchangeVanityDomain($PrivateExchangeVanityDomain);
			else:
				$PrivateExchangeVanityDomainFactory->insertPrivateExchangeVanityDomain($PrivateExchangeVanityDomain);
			endif;
			

			
		else:
		
			$PrivateExchangeVanityDomainFactory->deletePrivateExchangeVanityDomain($this->auth->getUserID());

		endif;
		
		return $this->redirect()->toUrl('/private-exchange/edittheme?success=true');
		
	}
	
	/**
	 *
	 * @return Ambigous <\Zend\View\Model\ViewModel, \Zend\View\Model\ViewModel>
	 */
	public function uploadlogoAction() {
	
		$initialized = $this->initialize();
		if ($initialized !== true) return $initialized;
	
		$vdomain_dir = 'public/vdomain/' . $this->auth->getUserID() . '/';
	
		if (!file_exists($vdomain_dir)):
			mkdir($vdomain_dir, 0755, true);
		endif;
	
		$site_url = $this->config_handle['delivery']['site_url'];
	
		if(substr($site_url, -1) == '/'):
			$site_url = substr($site_url, 0, -1);
		endif;
	
		$files =  $this->request->getFiles()->toArray();
		$httpadapter = new \Zend\File\Transfer\Adapter\Http();
		$filesize  = new \Zend\Validator\File\Size(array('max' => 2000000 )); //2MB
		$extension = new \Zend\Validator\File\Extension(array('extension' => array('png')));
		$httpadapter->setValidators(array($filesize, $extension), $files['file']['name']);
		$newName = 'logo-full.png';
		$httpadapter->addFilter('File\Rename', array(
				'target' => $vdomain_dir . $newName,
				'overwrite' => true
		));
		if($httpadapter->isValid()):
			if($httpadapter->receive($files['file']['name'])):
				$httpadapter->getFilter('File\Rename')->getFile();
				$newfile = $httpadapter->getFileName();
				
				$imagick_enabled 	= $this->config_handle['themes']['imagick_enabled'];
				
				$image_created = false;
				
				if ($imagick_enabled == true):
					try {
						/* Attempt to open */
		    			$img = new \Imagick($vdomain_dir . 'logo-full.png'); 
		    			$img->scaleImage(300,0);
		    			$img->writeImage($vdomain_dir . 'logo-lg.png');
					    $img->scaleImage(0,42); 
					    $img->writeImage($vdomain_dir . 'logo-sm.png'); 
					    $img->destroy();
		    			$image_created = true;
					} catch (Exception $e) { }
				endif;
				
				if ($image_created == false):
					copy($newfile, $vdomain_dir . 'logo-lg.png');
					copy($newfile, $vdomain_dir . 'logo-sm.png');
				endif;
				
				header("Content-type: text/plain");
				echo $site_url . substr($vdomain_dir . 'logo-lg.png', strlen('public'));
				exit;
			endif;
		endif;
		$error = array();
		$dataError = $httpadapter->getMessages();
		foreach($dataError as $key=>$row):
			$error[] = $row;
		endforeach;
		http_response_code(400);
		header("Content-type: text/plain");
		echo implode(',', $error);
		exit;
	
	}
	
	/**
	 * Allows an administrator to "login as another user", to impersonate a lower user to manage another user's objects.
	 * @return Ambigous <\Zend\Http\Response, \Zend\Stdlib\ResponseInterface>
	 */
	public function loginasAction()
	{
	    $this->ImpersonateUser();
		return $this->redirect()->toRoute('private-exchange');
	}

	/**
	 * 
	 * @return Ambigous <\Zend\Http\Response, \Zend\Stdlib\ResponseInterface>
	 */
	public function changeusermarkupAction() {
		
		$initialized = $this->initialize();
		if ($initialized !== true) return $initialized;

		if (!$this->auth->isSuperAdmin($this->config_handle)):
			die("You do not have permission to access this page");
		endif;

		$user_id 		= $this->getRequest()->getQuery('markupuserid');
		$user_markup 	= $this->getRequest()->getQuery('user-markup');

		$authUsersFactory = \_factory\authUsers::get_instance();
		$params = array();
		$params["user_id"] = $user_id;
		$authUser = $authUsersFactory->get_row($params);
		
		if ($authUser != null && $authUser->DemandCustomerInfoID != null):
		
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
	
			return $this->redirect()->toRoute('private-exchange');
			
		elseif ($authUser != null && $authUser->DemandCustomerInfoID == null):
			\util\RestHelper::dieHttpPostOrRestParam($this, "UserID belongs to a publisher");
		else:
			\util\RestHelper::dieHttpPostOrRestParam($this, "UserID does not exist");
		endif;
	}

	/**
	 * 
	 * @return Ambigous <\Zend\Http\Response, \Zend\Stdlib\ResponseInterface>
	 */
	public function changecampaignmarkupAction() {
		
		$initialized = $this->initialize();
		if ($initialized !== true) return $initialized;

		if (!$this->auth->isSuperAdmin($this->config_handle)):
			die("You do not have permission to access this page");
		endif;

		$campaign_id 		= $this->getRequest()->getQuery('markupcampaignid');
		$campaign_markup 	= $this->getRequest()->getQuery('campaign-markup');

		$InsertionOrderMarkupFactory = \_factory\InsertionOrderMarkup::get_instance();
		$params = array();
		$params["InsertionOrderID"] = $campaign_id;
		$InsertionOrderMarkup = $InsertionOrderMarkupFactory->get_row($params);

		$campaign_markup = floatval($campaign_markup) / 100;

			if ($campaign_markup <= 0):
				die("Campaign Markup can not be less than or equal to zero percent");
			endif;

			if ($campaign_markup >= 1):
				die("Campaign Markup can not be greater than or equal to one hundred percent");
			endif;

		$campaign_markup = sprintf("%1.2f", $campaign_markup);

		$InsertionOrderFactory = \_factory\InsertionOrder::get_instance();
		$params = array();
		$params["InsertionOrderID"] = $campaign_id;
		$InsertionOrder = $InsertionOrderFactory->get_row($params);
		
		if ($InsertionOrder != null):
		
			$_InsertionOrderMarkup = new \model\InsertionOrderMarkup();
			$_InsertionOrderMarkup->InsertionOrderID 	= $campaign_id;
			$_InsertionOrderMarkup->MarkupRate 		= $campaign_markup;
	
				if ($InsertionOrderMarkup != null):
		
					$InsertionOrderMarkupFactory->updateInsertionOrderMarkup($_InsertionOrderMarkup);
		
				else:
		
					$InsertionOrderMarkupFactory->insertInsertionOrderMarkup($_InsertionOrderMarkup);
		
				endif;
	
			return $this->redirect()->toRoute('private-exchange');
			
		else:
			\util\RestHelper::dieHttpPostOrRestParam($this, "InsertionOrderID does not exist");
		endif;
	}

	/**
	 * 
	 * @return Ambigous <\Zend\Http\Response, \Zend\Stdlib\ResponseInterface>
	 */
	public function approvecampaignAction() {
		
		$initialized = $this->initialize();
		if ($initialized !== true) return $initialized;

		if (!$this->auth->isSuperAdmin($this->config_handle) && !$this->auth->isDomainAdmin($this->config_handle)):
			die("You do not have permission to access this page");
		endif;

		$id = $this->getEvent()->getRouteMatch()->getParam('param1');
		if ($id == null):
			die("Invalid InsertionOrderID");
		endif;

		// copy the preview campaign and its elements into the production campaign
		$ad_campaign_id = \transformation\TransformPreview::cloneInsertionOrderPreviewIntoInsertionOrder($id, $this->auth, $this->config_handle);
		// set the preview campaigns and its elements to inactive and mark the date and time they went live
		\transformation\TransformPreview::deletePreviewModeCampaign($id, $this->auth, true);

		$InsertionOrderFactory = \_factory\InsertionOrder::get_instance();
		$params = array();
		if (!$this->auth->isSuperAdmin($this->config_handle)):
			$params["UserID"] = $this->auth->getUserID();
		endif;
		$params["InsertionOrderID"] = $ad_campaign_id;
		$InsertionOrder = $InsertionOrderFactory->get_row($params);
		
		if ($InsertionOrder == null):
			return $this->redirect()->toRoute('private-exchange');
		endif;
		
        $authUsersFactory = \_factory\authUsers::get_instance();
        $params = array();
        $params["user_id"] = $InsertionOrder->UserID; 
        $auth_User = $authUsersFactory->get_row($params);
		
		$site_url 		= $this->config_handle['delivery']['site_url'];
		$exchange_name 	= $this->config_handle['delivery']['exchange_name'];
        
        if ($auth_User !== null && $this->config_handle['mail']['subscribe']['user_ad_campaigns']):
			// approval, send out email
			$message = 'Your ' . $exchange_name . ' Demand Ad Campaign on Staging : ' . $InsertionOrder->Name . ' was promoted to production.<br /><br />Please login <a href="' . $site_url . '/auth/login">here</a> with your email and password to make changes.';
			
			$subject = "Your " . $exchange_name . " Demand Ad Campaign on Staging : " . $InsertionOrder->Name . " was promoted to production";
			 
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
		
		return $this->redirect()->toRoute('private-exchange');

	}

	/**
	 * 
	 * @return Ambigous <\Zend\Http\Response, \Zend\Stdlib\ResponseInterface>
	 */
	public function cancelcampaignAction() {
		$id = $this->getEvent()->getRouteMatch()->getParam('param1');
		if ($id == null):
			die("Invalid InsertionOrderID");
		endif;

		$initialized = $this->initialize();
		if ($initialized !== true) return $initialized;

		// ACL PREVIEW PERMISSIONS CHECK
		transformation\CheckPermissions::checkEditPermissionInsertionOrderPreview($id, $this->auth, $this->config_handle);

		// set the preview campaigns and its elements to inactive and mark the date and time they went live
		\transformation\TransformPreview::deletePreviewModeCampaign($id, $this->auth, false);

		return $this->redirect()->toRoute('private-exchange');

	}

	/**
	 * 
	 * @return Ambigous <\Zend\Http\Response, \Zend\Stdlib\ResponseInterface>
	 */
	public function rejectcampaignAction() {
		
		$initialized = $this->initialize();
		if ($initialized !== true) return $initialized;

		if (!$this->auth->isSuperAdmin($this->config_handle) && !$this->auth->isDomainAdmin($this->config_handle)):
			die("You do not have permission to access this page");
		endif;

		$id = $this->getEvent()->getRouteMatch()->getParam('param1');
		if ($id == null):
			die("Invalid InsertionOrderID");
		endif;

		$InsertionOrderPreviewFactory = \_factory\InsertionOrderPreview::get_instance();
		$params = array();
		if (!$this->auth->isSuperAdmin($this->config_handle)):
			$params["UserID"] = $this->auth->getUserID();
		endif;
		$params["InsertionOrderPreviewID"] = $id;
		$InsertionOrderPreview = $InsertionOrderPreviewFactory->get_row($params);
		
		if ($InsertionOrderPreview == null):
			return $this->redirect()->toRoute('private-exchange');
		endif;	
		
		$ad_campaign_preview_name = $InsertionOrderPreview->Name;
		$user_id = $InsertionOrderPreview->UserID;
		
		// set the preview campaigns and its elements to inactive and mark the date and time they went live
		\transformation\TransformPreview::deletePreviewModeCampaign($id, $this->auth, false);
		
		$authUsersFactory = \_factory\authUsers::get_instance();
		$params = array();
		$params["user_id"] = $user_id; 
		$auth_User = $authUsersFactory->get_row($params);
		
		$site_url 		= $this->config_handle['delivery']['site_url'];
		$exchange_name 	= $this->config_handle['delivery']['exchange_name'];
		
		if ($auth_User !== null && $ad_campaign_preview_name && $this->config_handle['mail']['subscribe']['user_ad_campaigns']):
			// approval, send out email
			$message = 'Your ' . $exchange_name . ' Demand Ad Campaign on Staging : ' . $ad_campaign_preview_name . ' was reset.<br /><br />Please login <a href="' . $site_url . '/auth/login">here</a> with your email and password to make changes.';
				
			$subject = "Your " . $exchange_name . " Demand Ad Campaign on Staging : " . $ad_campaign_preview_name . " was reset";
			
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
		
		return $this->redirect()->toRoute('private-exchange');

	}

	/*
	 * BEGIN NGINAD InsertionOrderLineItemRestrictions Actions
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
			$error_msg = "Invalid InsertionOrderLineItemID";
			$success = false;
			$data = array(
					'success' => $success,
					'data' => array('error_msg' => $error_msg)
			);
			
			$this->setJsonHeader();
			return $this->getResponse()->setContent(json_encode($data));
		endif;

		$initialized = $this->initialize();
		if ($initialized !== true) return $initialized;

		// ACL PERMISSIONS CHECK
		//transformation\CheckPermissions::checkEditPermissionInsertionOrderLineItem($id, $auth, $config);
		$ispreview 				= $this->getRequest()->getQuery('ispreview');
		
		if ($ispreview != "true"):
			/*
			 * THIS METHOD CHECKS IF THERE IS AN EXISTING PREVIEW MODE CAMPAIGN
			* IF NOT, IT CHECKS THE ACL PERMISSIONS ON THE PRODUCTION BANNER/CAMPAIGN REFERENCED
			* THEN IT CREATES A PREVIEW VERSION OF THE AD CAMPAIGN
			*/
			$update_data = array('type'=>'InsertionOrderLineItemID', 'id'=>$id);
			$return_val = \transformation\TransformPreview::previewCheckBannerID($id, $this->auth, $this->config_handle, $this->getServiceLocator()->get('mail.transport'), $update_data);
			
			if ($return_val !== null):
				$id = $return_val["InsertionOrderLineItemPreviewID"];
			else:
				$success = false;
				$data = array(
						'success' => $success,
						'data' => array('error_msg' => 'id not found')
				);
				
				$this->setJsonHeader();
				return $this->getResponse()->setContent(json_encode($data));
			endif;
		
		endif;
		
		$response = transformation\CheckPermissions::checkEditPermissionInsertionOrderLineItemPreview($id, $this->auth, $this->config_handle);
		
		if(array_key_exists("error", $response) > 0):
			$success = false;
			$data = array(
		       'success' => $success,
		       'data' => array('error_msg' => $response['error'])
	   		);
			
			$this->setJsonHeader();
	   	   return $this->getResponse()->setContent(json_encode($data));
		endif;
		
		$InsertionOrderLineItemPreviewFactory = \_factory\InsertionOrderLineItemPreview::get_instance();
		$InsertionOrderLineItemVideoRestrictionsPreviewFactory = \_factory\InsertionOrderLineItemVideoRestrictionsPreview::get_instance();
		$InsertionOrderLineItemRestrictionsPreviewFactory = \_factory\InsertionOrderLineItemRestrictionsPreview::get_instance();
		
		$InsertionOrderLineItemRestrictionsPreviewFactory->deleteInsertionOrderLineItemRestrictionsPreview($id);
		$InsertionOrderLineItemVideoRestrictionsPreviewFactory->deleteInsertionOrderLineItemVideoRestrictionsPreview($id);
		
		$params = array();
		$params["InsertionOrderLineItemPreviewID"] = $id;
		$InsertionOrderLineItemPreview = $InsertionOrderLineItemPreviewFactory->get_row($params);
		
		$success = true;
		$data = array(
		     'success' => $success,
			 'location' => '/private-exchange/viewlineitem/',
			 'previewid' => $InsertionOrderLineItemPreview->InsertionOrderPreviewID,
		     'data' => array('error_msg' => $error_msg)
	   	);
   		 
		$this->setJsonHeader();
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
			$update_data = array('type'=>'InsertionOrderLineItemID', 'id'=>$bannerid);
			$return_val = \transformation\TransformPreview::previewCheckBannerID($bannerid, $this->auth, $this->config_handle, $this->getServiceLocator()->get('mail.transport'), $update_data);
		
			if ($return_val !== null):
				$banner_preview_id = $return_val["InsertionOrderLineItemPreviewID"];
			endif;
		
		endif;
	
		// ACL PREVIEW PERMISSIONS CHECK
		transformation\CheckPermissions::checkEditPermissionInsertionOrderLineItemPreview($banner_preview_id, $this->auth, $this->config_handle);

		$start_delay 				= $this->getRequest()->getPost("StartDelay");
			
		$fold_pos 					= $this->getRequest()->getPost("foldpos");

		$vertical 					= $this->getRequest()->getPost("vertical");
		
		$geocountry 				= $this->getRequest()->getPost("geocountry");
		
		$geostate 					= $this->getRequest()->getPost("geostate");
		
		$geocity 					= $this->getRequest()->getPost("geocity");

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
	
		$InsertionOrderLineItemVideoRestrictionsPreviewFactory = \_factory\InsertionOrderLineItemVideoRestrictionsPreview::get_instance();
		$params = array();
		$params["InsertionOrderLineItemPreviewID"] = $banner_preview_id;
	
		$InsertionOrderLineItemVideoRestrictionsPreview = $InsertionOrderLineItemVideoRestrictionsPreviewFactory->get_row($params);
	
		$VideoRestrictionsPreview = new \model\InsertionOrderLineItemVideoRestrictionsPreview();
	
		if ($InsertionOrderLineItemVideoRestrictionsPreview != null):
	
			$VideoRestrictionsPreview->InsertionOrderLineItemVideoRestrictionsPreviewID            = $InsertionOrderLineItemVideoRestrictionsPreview->InsertionOrderLineItemVideoRestrictionsPreviewID;
	
		endif;
	
		$VideoRestrictionsPreview->InsertionOrderLineItemPreviewID                = $banner_preview_id;
		$VideoRestrictionsPreview->Vertical                                 = trim($vertical);
		$VideoRestrictionsPreview->GeoCountry                               = trim($geocountry);
		$VideoRestrictionsPreview->GeoState                                 = trim($geostate);
		$VideoRestrictionsPreview->GeoCity                                  = trim($geocity);
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

		$InsertionOrderLineItemVideoRestrictionsPreviewFactory = \_factory\InsertionOrderLineItemVideoRestrictionsPreview::get_instance();
		$InsertionOrderLineItemVideoRestrictionsPreviewFactory->saveInsertionOrderLineItemVideoRestrictionsPreview($VideoRestrictionsPreview);
	
		$InsertionOrderLineItemPreviewFactory = \_factory\InsertionOrderLineItemPreview::get_instance();
		$params = array();
		$params["InsertionOrderLineItemPreviewID"] = $banner_preview_id;
	
		$InsertionOrderLineItemPreview = $InsertionOrderLineItemPreviewFactory->get_row($params);
	
		$refresh_url = "/private-exchange/viewlineitem/" . $InsertionOrderLineItemPreview->InsertionOrderPreviewID . "?ispreview=true";
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
				$update_data = array('type'=>'InsertionOrderLineItemID', 'id'=>$bannerid);
				$return_val = \transformation\TransformPreview::previewCheckBannerID($bannerid, $this->auth, $this->config_handle, $this->getServiceLocator()->get('mail.transport'), $update_data);
	
				if ($return_val !== null):
					$banner_preview_id = $return_val["InsertionOrderLineItemPreviewID"];
				endif;
	
			endif;

		// ACL PREVIEW PERMISSIONS CHECK
		transformation\CheckPermissions::checkEditPermissionInsertionOrderLineItemPreview($banner_preview_id, $this->auth, $this->config_handle);

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
		$minscreenresolutionwidth = $this->getRequest()->getPost('minscreenresolutionwidth');
		$maxscreenresolutionwidth = $this->getRequest()->getPost('maxscreenresolutionwidth');
		$minscreenresolutionheight = $this->getRequest()->getPost('minscreenresolutionheight');
		$maxscreenresolutionheight = $this->getRequest()->getPost('maxscreenresolutionheight');
		$httplanguage = $this->getRequest()->getPost('httplanguage');
		$browseruseragentgrep = $this->getRequest()->getPost('browseruseragentgrep');
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

		$InsertionOrderLineItemRestrictionsPreviewFactory = \_factory\InsertionOrderLineItemRestrictionsPreview::get_instance();
		$params = array();
		$params["InsertionOrderLineItemPreviewID"] = $banner_preview_id;

		$InsertionOrderLineItemRestrictionsPreview = $InsertionOrderLineItemRestrictionsPreviewFactory->get_row($params);

		$BannerRestrictionsPreview = new \model\InsertionOrderLineItemRestrictionsPreview();

			if ($InsertionOrderLineItemRestrictionsPreview != null):
	
			      $BannerRestrictionsPreview->InsertionOrderLineItemRestrictionsPreviewID            = $InsertionOrderLineItemRestrictionsPreview->InsertionOrderLineItemRestrictionsPreviewID;
	
			endif;

		$BannerRestrictionsPreview->InsertionOrderLineItemPreviewID                       = $banner_preview_id;
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
		$BannerRestrictionsPreview->MinScreenResolutionWidth                 = trim($minscreenresolutionwidth);
		$BannerRestrictionsPreview->MaxScreenResolutionWidth                 = trim($maxscreenresolutionwidth);
		$BannerRestrictionsPreview->MinScreenResolutionHeight                = trim($minscreenresolutionheight);
		$BannerRestrictionsPreview->MaxScreenResolutionHeight                = trim($maxscreenresolutionheight);
		$BannerRestrictionsPreview->HttpLanguage                             = trim($httplanguage);
		$BannerRestrictionsPreview->BrowserUserAgentGrep                     = trim($browseruseragentgrep);
		$BannerRestrictionsPreview->Secure                                   = trim($secure);
		$BannerRestrictionsPreview->Optout                                   = trim($optout);
		$BannerRestrictionsPreview->Vertical                                 = trim($vertical);
		$BannerRestrictionsPreview->DateCreated                              = date("Y-m-d H:i:s");
		$BannerRestrictionsPreview->DateUpdated                              = date("Y-m-d H:i:s");

		$InsertionOrderLineItemRestrictionsPreviewFactory = \_factory\InsertionOrderLineItemRestrictionsPreview::get_instance();
		$InsertionOrderLineItemRestrictionsPreviewFactory->saveInsertionOrderLineItemRestrictionsPreview($BannerRestrictionsPreview);

		$InsertionOrderLineItemPreviewFactory = \_factory\InsertionOrderLineItemPreview::get_instance();
		$params = array();
		$params["InsertionOrderLineItemPreviewID"] = $banner_preview_id;

		$InsertionOrderLineItemPreview = $InsertionOrderLineItemPreviewFactory->get_row($params);

		$refresh_url = "/private-exchange/viewlineitem/" . $InsertionOrderLineItemPreview->InsertionOrderPreviewID . "?ispreview=true";
		$viewModel = new ViewModel(array('refresh_url' => $refresh_url));

		return $viewModel->setTemplate('dashboard-manager/demand/interstitial.phtml');
	}

	public function deliveryfiltervideoAction() {
		$id = $this->getEvent()->getRouteMatch()->getParam('param1');
		if ($id == null):
			die("Invalid InsertionOrderLineItemID");
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
			transformation\CheckPermissions::checkEditPermissionInsertionOrderLineItemPreview($id, $this->auth, $this->config_handle);
		
			$InsertionOrderLineItemVideoRestrictionsPreviewFactory = \_factory\InsertionOrderLineItemVideoRestrictionsPreview::get_instance();
			$params = array();
			$params["InsertionOrderLineItemPreviewID"] = $id;
			$banner_preview_id = $id;
			$id = "";
			$InsertionOrderLineItemVideoRestrictions = $InsertionOrderLineItemVideoRestrictionsPreviewFactory->get_row($params);
		
			$InsertionOrderLineItemPreviewFactory = \_factory\InsertionOrderLineItemPreview::get_instance();
			$params = array();
			$params["InsertionOrderLineItemPreviewID"] = $banner_preview_id;
			$InsertionOrderLineItemPreview = $InsertionOrderLineItemPreviewFactory->get_row($params);
			$campaign_preview_id = $InsertionOrderLineItemPreview->InsertionOrderPreviewID;
		
		else:
			// ACL PERMISSIONS CHECK
			transformation\CheckPermissions::checkEditPermissionInsertionOrderLineItem($id, $this->auth, $this->config_handle);
		
			$InsertionOrderLineItemVideoRestrictionsFactory = \_factory\InsertionOrderLineItemVideoRestrictions::get_instance();
			$params = array();
			$params["InsertionOrderLineItemID"] = $id;
		
			$InsertionOrderLineItemVideoRestrictions = $InsertionOrderLineItemVideoRestrictionsFactory->get_row($params);
		
			$InsertionOrderLineItemFactory = \_factory\InsertionOrderLineItem::get_instance();
			$params = array();
			$params["InsertionOrderLineItemID"] = $id;
			$InsertionOrderLineItem = $InsertionOrderLineItemFactory->get_row($params);
			$campaign_id = $InsertionOrderLineItem->InsertionOrderID;
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
		
		$current_secure 				= "";
		$current_optout 				= "";
		$current_vertical 				= array();
	
		$current_mimes_raw 				= "";
		$current_apis_supported_raw 	= "";
		$current_protocols_raw 			= "";
		$current_delivery_methods_raw 	= "";
		$current_playback_methods_raw 	= "";
		$current_vertical_raw 			= "";
		
		if ($InsertionOrderLineItemVideoRestrictions != null):
		
			$current_foldpos = $InsertionOrderLineItemVideoRestrictions->FoldPos == null ? "" : $InsertionOrderLineItemVideoRestrictions->FoldPos;
			$current_states = $InsertionOrderLineItemVideoRestrictions->GeoState == null ? "" : $InsertionOrderLineItemVideoRestrictions->GeoState;
			$current_country = $InsertionOrderLineItemVideoRestrictions->GeoCountry == null ? "" : $InsertionOrderLineItemVideoRestrictions->GeoCountry;
			$geocity_option = $InsertionOrderLineItemVideoRestrictions->GeoCity == null ? "" : $InsertionOrderLineItemVideoRestrictions->GeoCity;
			
			$current_mimes_raw = $InsertionOrderLineItemVideoRestrictions->MimesCommaSeparated == null ? "" : $InsertionOrderLineItemVideoRestrictions->MimesCommaSeparated;
			$current_apis_supported_raw = $InsertionOrderLineItemVideoRestrictions->ApisSupportedCommaSeparated == null ? "" : $InsertionOrderLineItemVideoRestrictions->ApisSupportedCommaSeparated;
			$current_protocols_raw = $InsertionOrderLineItemVideoRestrictions->ProtocolsCommaSeparated == null ? "" : $InsertionOrderLineItemVideoRestrictions->ProtocolsCommaSeparated;
			$current_delivery_methods_raw = $InsertionOrderLineItemVideoRestrictions->DeliveryCommaSeparated == null ? "" : $InsertionOrderLineItemVideoRestrictions->DeliveryCommaSeparated;
			$current_playback_methods_raw = $InsertionOrderLineItemVideoRestrictions->PlaybackCommaSeparated == null ? "" : $InsertionOrderLineItemVideoRestrictions->PlaybackCommaSeparated;
			
			$current_start_delay = $InsertionOrderLineItemVideoRestrictions->StartDelay == null ? "" : $InsertionOrderLineItemVideoRestrictions->StartDelay;
			$current_linearity = $InsertionOrderLineItemVideoRestrictions->Linearity == null ? "" : $InsertionOrderLineItemVideoRestrictions->Linearity;

			$current_min_duration = $InsertionOrderLineItemVideoRestrictions->MinDuration == null ? "" : $InsertionOrderLineItemVideoRestrictions->MinDuration;
			$current_max_duration = $InsertionOrderLineItemVideoRestrictions->MaxDuration == null ? "" : $InsertionOrderLineItemVideoRestrictions->MaxDuration;
			
			$current_min_height = $InsertionOrderLineItemVideoRestrictions->MinHeight == null ? "" : $InsertionOrderLineItemVideoRestrictions->MinHeight;
			$current_min_width = $InsertionOrderLineItemVideoRestrictions->MinWidth == null ? "" : $InsertionOrderLineItemVideoRestrictions->MinWidth;

			$current_secure = $InsertionOrderLineItemVideoRestrictions->Secure == null ? "" : $InsertionOrderLineItemVideoRestrictions->Secure;
			$current_optout = $InsertionOrderLineItemVideoRestrictions->Optout == null ? "" : $InsertionOrderLineItemVideoRestrictions->Optout;
			$current_vertical_raw = $InsertionOrderLineItemVideoRestrictions->Vertical == null ? "" : $InsertionOrderLineItemVideoRestrictions->Vertical;
			
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
				'is_super_admin' => $this->is_super_admin,
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
				die("Invalid InsertionOrderLineItemID");
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
				transformation\CheckPermissions::checkEditPermissionInsertionOrderLineItemPreview($id, $this->auth, $this->config_handle);
	
				$InsertionOrderLineItemRestrictionsPreviewFactory = \_factory\InsertionOrderLineItemRestrictionsPreview::get_instance();
				$params = array();
				$params["InsertionOrderLineItemPreviewID"] = $id;
				$banner_preview_id = $id;
				$id = "";
				$InsertionOrderLineItemRestrictions = $InsertionOrderLineItemRestrictionsPreviewFactory->get_row($params);
	
				$InsertionOrderLineItemPreviewFactory = \_factory\InsertionOrderLineItemPreview::get_instance();
				$params = array();
				$params["InsertionOrderLineItemPreviewID"] = $banner_preview_id;
				$InsertionOrderLineItemPreview = $InsertionOrderLineItemPreviewFactory->get_row($params);
				$campaign_preview_id = $InsertionOrderLineItemPreview->InsertionOrderPreviewID;
	
			else:
				// ACL PERMISSIONS CHECK
				transformation\CheckPermissions::checkEditPermissionInsertionOrderLineItem($id, $this->auth, $this->config_handle);
	
				$InsertionOrderLineItemRestrictionsFactory = \_factory\InsertionOrderLineItemRestrictions::get_instance();
				$params = array();
				$params["InsertionOrderLineItemID"] = $id;
	
				$InsertionOrderLineItemRestrictions = $InsertionOrderLineItemRestrictionsFactory->get_row($params);
	
				$InsertionOrderLineItemFactory = \_factory\InsertionOrderLineItem::get_instance();
				$params = array();
				$params["InsertionOrderLineItemID"] = $id;
				$InsertionOrderLineItem = $InsertionOrderLineItemFactory->get_row($params);
				$campaign_id = $InsertionOrderLineItem->InsertionOrderID;
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
		$minscreenresolutionwidth_option = "";
		$maxscreenresolutionwidth_option = "";
		$minscreenresolutionheight_option = "";
		$maxscreenresolutionheight_option = "";
		$httplanguage_option = "";
		$browseruseragentgrep_option = "";
		$current_secure = "";
		$current_optout = "";
		$current_vertical = array();


		if ($InsertionOrderLineItemRestrictions != null):

    		$current_states = $InsertionOrderLineItemRestrictions->GeoState == null ? "" : $InsertionOrderLineItemRestrictions->GeoState;
    		$current_country = $InsertionOrderLineItemRestrictions->GeoCountry == null ? "" : $InsertionOrderLineItemRestrictions->GeoCountry;
    		$current_foldpos = $InsertionOrderLineItemRestrictions->FoldPos == null ? "" : $InsertionOrderLineItemRestrictions->FoldPos;
    		$frequency_option = $InsertionOrderLineItemRestrictions->Freq == null ? "" : $InsertionOrderLineItemRestrictions->Freq;
    		$geocity_option = $InsertionOrderLineItemRestrictions->GeoCity == null ? "" : $InsertionOrderLineItemRestrictions->GeoCity;
    		$adpositionminleft_option = $InsertionOrderLineItemRestrictions->AdPositionMinLeft == null ? "" : $InsertionOrderLineItemRestrictions->AdPositionMinLeft;
    		$adpositionmaxleft_option = $InsertionOrderLineItemRestrictions->AdPositionMaxLeft == null ? "" : $InsertionOrderLineItemRestrictions->AdPositionMaxLeft;
    		$adpositionmintop_option = $InsertionOrderLineItemRestrictions->AdPositionMinTop == null ? "" : $InsertionOrderLineItemRestrictions->AdPositionMinTop;
    		$adpositionmaxtop_option = $InsertionOrderLineItemRestrictions->AdPositionMaxTop == null ? "" : $InsertionOrderLineItemRestrictions->AdPositionMaxTop;
    		$current_timezone = $InsertionOrderLineItemRestrictions->Timezone == null ? "" : $InsertionOrderLineItemRestrictions->Timezone;
    		$current_adtagtype = $InsertionOrderLineItemRestrictions->AdTagType == null ? "" : $InsertionOrderLineItemRestrictions->AdTagType;
    		$current_iniframe = $InsertionOrderLineItemRestrictions->InIframe == null ? "" : $InsertionOrderLineItemRestrictions->InIframe;
    		$minscreenresolutionwidth_option = $InsertionOrderLineItemRestrictions->MinScreenResolutionWidth == null ? "" : $InsertionOrderLineItemRestrictions->MinScreenResolutionWidth;
    		$maxscreenresolutionwidth_option = $InsertionOrderLineItemRestrictions->MaxScreenResolutionWidth == null ? "" : $InsertionOrderLineItemRestrictions->MaxScreenResolutionWidth;
    		$minscreenresolutionheight_option = $InsertionOrderLineItemRestrictions->MinScreenResolutionHeight == null ? "" : $InsertionOrderLineItemRestrictions->MinScreenResolutionHeight;
    		$maxscreenresolutionheight_option = $InsertionOrderLineItemRestrictions->MaxScreenResolutionHeight == null ? "" : $InsertionOrderLineItemRestrictions->MaxScreenResolutionHeight;
    		$httplanguage_option = $InsertionOrderLineItemRestrictions->HttpLanguage == null ? "" : $InsertionOrderLineItemRestrictions->HttpLanguage;
    		$browseruseragentgrep_option = $InsertionOrderLineItemRestrictions->BrowserUserAgentGrep == null ? "" : $InsertionOrderLineItemRestrictions->BrowserUserAgentGrep;
    		$current_secure = $InsertionOrderLineItemRestrictions->Secure == null ? "" : $InsertionOrderLineItemRestrictions->Secure;
    		$current_optout = $InsertionOrderLineItemRestrictions->Optout == null ? "" : $InsertionOrderLineItemRestrictions->Optout;
    		$current_vertical = $InsertionOrderLineItemRestrictions->Vertical == null ? "" : $InsertionOrderLineItemRestrictions->Vertical;

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
    		    'minscreenresolutionwidth_option' => $minscreenresolutionwidth_option,
    		    'maxscreenresolutionwidth_option' => $maxscreenresolutionwidth_option,
    		    'minscreenresolutionheight_option' => $minscreenresolutionheight_option,
    		    'maxscreenresolutionheight_option' => $maxscreenresolutionheight_option,
		        'httplanguage_option' => $httplanguage_option,
		        'browseruseragentgrep_option' => $browseruseragentgrep_option,
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
				'is_super_admin' => $this->is_super_admin,
				'effective_id' => $this->auth->getEffectiveIdentityID(),
				'impersonate_id' => $this->ImpersonateID
		));
	}

	/*
	 * END NGINAD InsertionOrderLineItemRestrictions Actions
	*/

	/*
	 * BEGIN NGINAD InsertionOrderLineItemDomainExclusiveInclusion Actions
	*/

	/**
	 * 
	 * @return \Zend\View\Model\ViewModel
	 */
	public function viewexclusiveinclusionAction() {
		$id = $this->getEvent()->getRouteMatch()->getParam('param1');
		if ($id == null):
			die("Invalid InsertionOrderLineItemID");
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
			transformation\CheckPermissions::checkEditPermissionInsertionOrderLineItemPreview($id, $this->auth, $this->config_handle);

			$InsertionOrderLineItemDomainExclusiveInclusionPreviewFactory = \_factory\InsertionOrderLineItemDomainExclusiveInclusionPreview::get_instance();
			$params = array();
			$params["InsertionOrderLineItemPreviewID"] = $id;
			$banner_preview_id = $id;
			$id = "";
			$rtb_domain_exclusive_inclusions = $InsertionOrderLineItemDomainExclusiveInclusionPreviewFactory->get($params);

			$InsertionOrderLineItemPreviewFactory = \_factory\InsertionOrderLineItemPreview::get_instance();
			$params = array();
			$params["InsertionOrderLineItemPreviewID"] = $banner_preview_id;

			$InsertionOrderLineItemPreview = $InsertionOrderLineItemPreviewFactory->get_row($params);
			$campaign_preview_id = $InsertionOrderLineItemPreview->InsertionOrderPreviewID;

		else:
			// ACL PERMISSIONS CHECK
			transformation\CheckPermissions::checkEditPermissionInsertionOrderLineItem($id, $this->auth, $this->config_handle);

			$InsertionOrderLineItemDomainExclusiveInclusionFactory = \_factory\InsertionOrderLineItemDomainExclusiveInclusion::get_instance();
			$params = array();
			$params["InsertionOrderLineItemID"] = $id;
			$rtb_domain_exclusive_inclusions = $InsertionOrderLineItemDomainExclusiveInclusionFactory->get($params);

			$InsertionOrderLineItemFactory = \_factory\InsertionOrderLineItem::get_instance();
			$params = array();
			$params["InsertionOrderLineItemID"] = $id;

			$InsertionOrderLineItem = $InsertionOrderLineItemFactory->get_row($params);
			$campaign_id = $InsertionOrderLineItem->InsertionOrderID;

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
				'header_title' => '<a href="/private-exchange/createexclusiveinclusion/' . $rtb_banner_id . $this->preview_query . '">Create Domain Exclusive Inclusion</a>',
				'is_super_admin' => $this->is_super_admin,
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
			$error_msg = "Invalid DomainExclusiveInclusion ID";
		    $success = false;
		    $data = array(
	         'success' => $success,
	         'data' => array('error_msg' => $error_msg)
   		   );
   		 
		  $this->setJsonHeader();
          return $this->getResponse()->setContent(json_encode($data));
		endif;

		$initialized = $this->initialize();
		if ($initialized !== true) return $initialized;

		$is_preview = $this->getRequest()->getQuery('ispreview');

		$exclusiveinclusion_preview_id = null;

		$InsertionOrderLineItemDomainExclusiveInclusionPreviewFactory = \_factory\InsertionOrderLineItemDomainExclusiveInclusionPreview::get_instance();

		// verify
		if ($is_preview != "true"):

			$InsertionOrderLineItemDomainExclusiveInclusionFactory = \_factory\InsertionOrderLineItemDomainExclusiveInclusion::get_instance();
			$params = array();
			$params["InsertionOrderLineItemDomainExclusiveInclusionID"] = $id;
			$rtb_domain_exclusive_inclusion = $InsertionOrderLineItemDomainExclusiveInclusionFactory->get_row($params);

			if ($rtb_domain_exclusive_inclusion == null):
				$error_msg = "Invalid InsertionOrderLineItemDomainExclusiveInclusion ID";
			    $success = false;
			    $data = array(
		         'success' => $success,
		         'data' => array('error_msg' => $error_msg)
	   		   );
			    
			    $this->setJsonHeader();
          		return $this->getResponse()->setContent(json_encode($data));
			endif;

			$banner_id = $rtb_domain_exclusive_inclusion->InsertionOrderLineItemID;

			// ACL PERMISSIONS CHECK
			$response = transformation\CheckPermissions::checkEditPermissionInsertionOrderLineItem($banner_id, $this->auth, $this->config_handle);
			
			if(array_key_exists("error", $response) > 0):
				$success = false;
				$data = array(
			       'success' => $success,
			       'data' => array('error_msg' => $response['error'])
		   		);
		   		
				$this->setJsonHeader();
		   	   return $this->getResponse()->setContent(json_encode($data));
			endif;
			
			/*
			 * THIS METHOD CHECKS IF THERE IS AN EXISTING PREVIEW MODE CAMPAIGN
			* IF NOT, IT CHECKS THE ACL PERMISSIONS ON THE PRODUCTION BANNER/CAMPAIGN REFERENCED
			* THEN IT CREATES A PREVIEW VERSION OF THE AD CAMPAIGN
			*/

			$update_data = array('type'=>'InsertionOrderLineItemDomainExclusiveInclusionID', 'id'=>$id);

			$return_val = \transformation\TransformPreview::previewCheckBannerID($banner_id, $this->auth, $this->config_handle, $this->getServiceLocator()->get('mail.transport'), $update_data);

			if ($return_val !== null && array_key_exists("error", $return_val)):

				$success = false;
				$data = array(
			       'success' => $success,
			       'data' => array('error_msg' => $return_val['error'])
		   		);
   		
			   $this->setJsonHeader();
		   	   return $this->getResponse()->setContent(json_encode($data));
			endif;
			
			if ($return_val !== null):
				$banner_preview_id 	= $return_val["InsertionOrderLineItemPreviewID"];
				$exclusiveinclusion_preview_id = $return_val["InsertionOrderLineItemDomainExclusiveInclusionPreviewID"];
			endif;

		else:

			$params = array();
			$params["InsertionOrderLineItemDomainExclusiveInclusionPreviewID"] = $id;
			$rtb_domain_exclusive_inclusion_preview = $InsertionOrderLineItemDomainExclusiveInclusionPreviewFactory->get_row($params);

			if ($rtb_domain_exclusive_inclusion_preview == null):
				$error_msg = "Invalid InsertionOrderLineItemDomainExclusiveInclusionPreview ID";
			    $success = false;
			    $data = array(
		         'success' => $success,
		         'data' => array('error_msg' => $error_msg)
	   		   );
			    $this->setJsonHeader();
          		return $this->getResponse()->setContent(json_encode($data));
			endif;

			$banner_preview_id = $rtb_domain_exclusive_inclusion_preview->InsertionOrderLineItemPreviewID;
			$exclusiveinclusion_preview_id = $rtb_domain_exclusive_inclusion_preview->InsertionOrderLineItemDomainExclusiveInclusionPreviewID;

			// ACL PREVIEW PERMISSIONS CHECK
			$response = transformation\CheckPermissions::checkEditPermissionInsertionOrderLineItemPreview($banner_preview_id, $this->auth, $this->config_handle);
			
			if(array_key_exists("error", $response) > 0):
				$success = false;
				$data = array(
			       'success' => $success,
			       'data' => array('error_msg' => $response['error'])
		   		);
				
			   $this->setJsonHeader();
		   	   return $this->getResponse()->setContent(json_encode($data));
			endif;

		endif;

		$InsertionOrderLineItemDomainExclusiveInclusionPreviewFactory->deleteInsertionOrderLineItemDomainExclusiveInclusionPreview($exclusiveinclusion_preview_id);


		  $data = array(
		     'success' => $success,
		  	 'location' => '/private-exchange/viewexclusiveinclusion/',
		  	 'previewid' => $banner_preview_id,
		     'data' => array('error_msg' => $error_msg)
	   	  );
   		 
		$this->setJsonHeader();
      	return $this->getResponse()->setContent(json_encode($data));
	}

	/**
	 * 
	 * @return \Zend\View\Model\ViewModel
	 */
	public function createexclusiveinclusionAction() {
		$id = $this->getEvent()->getRouteMatch()->getParam('param1');
		if ($id == null):
			die("Invalid InsertionOrderLineItemID");
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
			transformation\CheckPermissions::checkEditPermissionInsertionOrderLineItemPreview($id, $this->auth, $this->config_handle);
			$banner_preview_id = $id;
			$id = "";

			$InsertionOrderLineItemPreviewFactory = \_factory\InsertionOrderLineItemPreview::get_instance();
			$params = array();
			$params["InsertionOrderLineItemPreviewID"] = $banner_preview_id;
			$InsertionOrderLineItemPreview = $InsertionOrderLineItemPreviewFactory->get_row($params);
			$campaign_preview_id = $InsertionOrderLineItemPreview->InsertionOrderPreviewID;

		else:
			// ACL PERMISSIONS CHECK
			transformation\CheckPermissions::checkEditPermissionInsertionOrderLineItem($id, $this->auth, $this->config_handle);

			$InsertionOrderLineItemFactory = \_factory\InsertionOrderLineItem::get_instance();
			$params = array();
			$params["InsertionOrderLineItemID"] = $id;
			$InsertionOrderLineItem = $InsertionOrderLineItemFactory->get_row($params);
			$campaign_id = $InsertionOrderLineItem->InsertionOrderID;

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
				'is_super_admin' => $this->is_super_admin,
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
			$update_data = array('type'=>'InsertionOrderLineItemID', 'id'=>$bannerid);
			$return_val = \transformation\TransformPreview::previewCheckBannerID($bannerid, $this->auth, $this->config_handle, $this->getServiceLocator()->get('mail.transport'), $update_data);

			if ($return_val !== null):
				$banner_preview_id = $return_val["InsertionOrderLineItemPreviewID"];
			endif;

		endif;

		// ACL PREVIEW PERMISSIONS CHECK
		transformation\CheckPermissions::checkEditPermissionInsertionOrderLineItemPreview($banner_preview_id, $this->auth, $this->config_handle);

		$inclusiontype = $this->getRequest()->getPost('inclusiontype');
		$domainname = $this->getRequest()->getPost('domainname');

		$BannerDomainExclusiveInclusionPreview = new \model\InsertionOrderLineItemDomainExclusiveInclusionPreview();
		$BannerDomainExclusiveInclusionPreview->InsertionOrderLineItemPreviewID           = $banner_preview_id;
		$BannerDomainExclusiveInclusionPreview->InclusionType             = $inclusiontype;
		$BannerDomainExclusiveInclusionPreview->DomainName                = $domainname;
		$BannerDomainExclusiveInclusionPreview->DateCreated               = date("Y-m-d H:i:s");
		$BannerDomainExclusiveInclusionPreview->DateUpdated               = date("Y-m-d H:i:s");

		$InsertionOrderLineItemDomainExclusiveInclusionPreviewFactory = \_factory\InsertionOrderLineItemDomainExclusiveInclusionPreview::get_instance();
		$InsertionOrderLineItemDomainExclusiveInclusionPreviewFactory->saveInsertionOrderLineItemDomainExclusiveInclusionPreview($BannerDomainExclusiveInclusionPreview);

		$refresh_url = "/private-exchange/viewexclusiveinclusion/" . $banner_preview_id . "?ispreview=true";
		$viewModel = new ViewModel(array('refresh_url' => $refresh_url));

		return $viewModel->setTemplate('dashboard-manager/demand/interstitial.phtml');

	}

	/*
	 * END NGINAD InsertionOrderLineItemDomainExclusiveInclusion Actions
	*/

	/*
	 * BEGIN NGINAD InsertionOrderLineItemDomainExclusion Actions
	*/


	/**
	 * 
	 * @return \Zend\View\Model\ViewModel
	 */
	public function viewdomainexclusionAction() {
		$id = $this->getEvent()->getRouteMatch()->getParam('param1');
		if ($id == null):
			die("Invalid InsertionOrderLineItemID");
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
			transformation\CheckPermissions::checkEditPermissionInsertionOrderLineItemPreview($id, $this->auth, $this->config_handle);

			$InsertionOrderLineItemDomainExclusionPreviewFactory = \_factory\InsertionOrderLineItemDomainExclusionPreview::get_instance();
			$params = array();
			$params["InsertionOrderLineItemPreviewID"] = $id;
			$banner_preview_id = $id;
			$id = "";
			$rtb_domain_exclusions = $InsertionOrderLineItemDomainExclusionPreviewFactory->get($params);

			$InsertionOrderLineItemPreviewFactory = \_factory\InsertionOrderLineItemPreview::get_instance();
			$params = array();
			$params["InsertionOrderLineItemPreviewID"] = $banner_preview_id;

			$InsertionOrderLineItemPreview = $InsertionOrderLineItemPreviewFactory->get_row($params);
			$campaign_preview_id = $InsertionOrderLineItemPreview->InsertionOrderPreviewID;

		else:
			// ACL PERMISSIONS CHECK
			transformation\CheckPermissions::checkEditPermissionInsertionOrderLineItem($id, $this->auth, $this->config_handle);

			$InsertionOrderLineItemDomainExclusionFactory = \_factory\InsertionOrderLineItemDomainExclusion::get_instance();
			$params = array();
			$params["InsertionOrderLineItemID"] = $id;
			$rtb_domain_exclusions = $InsertionOrderLineItemDomainExclusionFactory->get($params);

			$InsertionOrderLineItemFactory = \_factory\InsertionOrderLineItem::get_instance();
			$params = array();
			$params["InsertionOrderLineItemID"] = $id;

			$InsertionOrderLineItem = $InsertionOrderLineItemFactory->get_row($params);
			$campaign_id = $InsertionOrderLineItem->InsertionOrderID;

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
				'header_title' => '<a href="/private-exchange/createdomainexclusion/' . $rtb_banner_id . $this->preview_query . '">Create Domain Exclusion</a>',
				'is_super_admin' => $this->is_super_admin,
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
			$error_msg = "Invalid Domain Exclusion ID";
		    $success = false;
		    $data = array(
	         'success' => $success,
	         'data' => array('error_msg' => $error_msg)
   		   );

		   $this->setJsonHeader();
           return $this->getResponse()->setContent(json_encode($data));
		endif;

		$initialized = $this->initialize();
		if ($initialized !== true) return $initialized;

		$is_preview = $this->getRequest()->getQuery('ispreview');

		$exclusion_preview_id = null;

		$InsertionOrderLineItemDomainExclusionPreviewFactory = \_factory\InsertionOrderLineItemDomainExclusionPreview::get_instance();

		// verify
		if ($is_preview != "true"):

			$InsertionOrderLineItemDomainExclusionFactory = \_factory\InsertionOrderLineItemDomainExclusion::get_instance();
			$params = array();
			$params["InsertionOrderLineItemDomainExclusionID"] = $id;
			$rtb_domain_exclusion = $InsertionOrderLineItemDomainExclusionFactory->get_row($params);

			if ($rtb_domain_exclusion == null):
				$error_msg = "Invalid InsertionOrderLineItemDomainExclusionID";
			    $success = false;
			    $data = array(
		         'success' => $success,
		         'data' => array('error_msg' => $error_msg)
	   		   );
	   		 
			    $this->setJsonHeader();
	           return $this->getResponse()->setContent(json_encode($data));
				
			endif;

			$banner_id = $rtb_domain_exclusion->InsertionOrderLineItemID;

			// ACL PERMISSIONS CHECK
			//transformation\CheckPermissions::checkEditPermissionInsertionOrderLineItem($banner_id, $auth, $config);
			$response = transformation\CheckPermissions::checkEditPermissionInsertionOrderLineItem($banner_id, $this->auth, $this->config_handle);
			
			if(array_key_exists("error", $response) > 0):
				$success = false;
				$data = array(
			       'success' => $success,
			       'data' => array('error_msg' => $response['error'])
		   		);
		   		
			   $this->setJsonHeader();
		   	   return $this->getResponse()->setContent(json_encode($data));
			endif;
			
			/*
			 * THIS METHOD CHECKS IF THERE IS AN EXISTING PREVIEW MODE CAMPAIGN
			* IF NOT, IT CHECKS THE ACL PERMISSIONS ON THE PRODUCTION BANNER/CAMPAIGN REFERENCED
			* THEN IT CREATES A PREVIEW VERSION OF THE AD CAMPAIGN
			*/

			$update_data = array('type'=>'InsertionOrderLineItemDomainExclusionID', 'id'=>$id);

			$return_val = \transformation\TransformPreview::previewCheckBannerID($banner_id, $this->auth, $this->config_handle, $this->getServiceLocator()->get('mail.transport'), $update_data);

			if ($return_val !== null && array_key_exists("error", $return_val)):

				$success = false;
				$data = array(
			       'success' => $success,
			       'data' => array('error_msg' => $return_val['error'])
		   		);
   		
			   $this->setJsonHeader();
		   	   return $this->getResponse()->setContent(json_encode($data));
			endif;
			
			if ($return_val !== null):
				$banner_preview_id 	= $return_val["InsertionOrderLineItemPreviewID"];
				$exclusion_preview_id = $return_val["InsertionOrderLineItemDomainExclusionPreviewID"];
			endif;

		else:

			$params = array();
			$params["InsertionOrderLineItemDomainExclusionPreviewID"] = $id;
			$rtb_domain_exclusion_preview = $InsertionOrderLineItemDomainExclusionPreviewFactory->get_row($params);

			if ($rtb_domain_exclusion_preview == null):
				$error_msg = "Invalid InsertionOrderLineItemDomainExclusionPreviewID";
			    $success = false;
			    $data = array(
		         'success' => $success,
		         'data' => array('error_msg' => $error_msg)
	   		   );
	   		 
			    $this->setJsonHeader();
	           return $this->getResponse()->setContent(json_encode($data));
			endif;

			$banner_preview_id = $rtb_domain_exclusion_preview->InsertionOrderLineItemPreviewID;
			$exclusion_preview_id = $rtb_domain_exclusion_preview->InsertionOrderLineItemDomainExclusionPreviewID;

			// ACL PREVIEW PERMISSIONS CHECK
			$response = transformation\CheckPermissions::checkEditPermissionInsertionOrderLineItemPreview($banner_preview_id, $this->auth, $this->config_handle);
			
			
			if(array_key_exists("error", $response) > 0):
				$success = false;
				$data = array(
			       'success' => $success,
			       'data' => array('error_msg' => $response['error'])
		   		);
		   		
			   $this->setJsonHeader();
		   	   return $this->getResponse()->setContent(json_encode($data));
			endif;

		endif;

		$InsertionOrderLineItemDomainExclusionPreviewFactory->deleteInsertionOrderLineItemDomainExclusionPreview($exclusion_preview_id);

		$data = array(
		         'success' => $success,
				 'location' => '/private-exchange/viewdomainexclusion/',
				 'previewid' => $banner_preview_id,
		         'data' => array('error_msg' => $error_msg)
	   		   );

		$this->setJsonHeader();
	    return $this->getResponse()->setContent(json_encode($data));
	    
	}

	/**
	 * 
	 * @return \Zend\View\Model\ViewModel
	 */
	public function createdomainexclusionAction() {
		$id = $this->getEvent()->getRouteMatch()->getParam('param1');
		if ($id == null):
			die("Invalid InsertionOrderLineItemID");
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
			transformation\CheckPermissions::checkEditPermissionInsertionOrderLineItemPreview($id, $this->auth, $this->config_handle);
			$banner_preview_id = $id;
			$id = "";

			$InsertionOrderLineItemPreviewFactory = \_factory\InsertionOrderLineItemPreview::get_instance();
			$params = array();
			$params["InsertionOrderLineItemPreviewID"] = $banner_preview_id;
			$InsertionOrderLineItemPreview = $InsertionOrderLineItemPreviewFactory->get_row($params);
			$campaign_preview_id = $InsertionOrderLineItemPreview->InsertionOrderPreviewID;

		else:
			// ACL PERMISSIONS CHECK
			transformation\CheckPermissions::checkEditPermissionInsertionOrderLineItem($id, $this->auth, $this->config_handle);

			$InsertionOrderLineItemFactory = \_factory\InsertionOrderLineItem::get_instance();
			$params = array();
			$params["InsertionOrderLineItemID"] = $id;
			$InsertionOrderLineItem = $InsertionOrderLineItemFactory->get_row($params);
			$campaign_id = $InsertionOrderLineItem->InsertionOrderID;
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
				'is_super_admin' => $this->is_super_admin,
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
			$update_data = array('type'=>'InsertionOrderLineItemID', 'id'=>$bannerid);
			$return_val = \transformation\TransformPreview::previewCheckBannerID($bannerid, $this->auth, $this->config_handle, $this->getServiceLocator()->get('mail.transport'), $update_data);

			if ($return_val !== null):
				$banner_preview_id = $return_val["InsertionOrderLineItemPreviewID"];
			endif;

		endif;

		// ACL PREVIEW PERMISSIONS CHECK
		transformation\CheckPermissions::checkEditPermissionInsertionOrderLineItemPreview($banner_preview_id, $this->auth, $this->config_handle);

		$exclusiontype = $this->getRequest()->getPost('exclusiontype');
		$domainname = $this->getRequest()->getPost('domainname');

		$BannerDomainExclusionPreview = new \model\InsertionOrderLineItemDomainExclusionPreview();
		$BannerDomainExclusionPreview->InsertionOrderLineItemPreviewID           = $banner_preview_id;
		$BannerDomainExclusionPreview->ExclusionType             = $exclusiontype;
		$BannerDomainExclusionPreview->DomainName                = $domainname;
		$BannerDomainExclusionPreview->DateCreated               = date("Y-m-d H:i:s");
		$BannerDomainExclusionPreview->DateUpdated               = date("Y-m-d H:i:s");

		$InsertionOrderLineItemDomainExclusionPreviewFactory = \_factory\InsertionOrderLineItemDomainExclusionPreview::get_instance();
		$InsertionOrderLineItemDomainExclusionPreviewFactory->saveInsertionOrderLineItemDomainExclusionPreview($BannerDomainExclusionPreview);

		$refresh_url = "/private-exchange/viewdomainexclusion/" . $banner_preview_id . "?ispreview=true";
		$viewModel = new ViewModel(array('refresh_url' => $refresh_url));

		return $viewModel->setTemplate('dashboard-manager/demand/interstitial.phtml');

	}

	/*
	 * END NGINAD InsertionOrderLineItemDomainExclusion Actions
	*/

	/*
	 * BEGIN NGINAD InsertionOrderLineItem Actions
	*/

	/**
	 * 
	 * @return Ambigous <\Zend\View\Model\ViewModel, \Zend\View\Model\ViewModel>
	 */
	public function deletelineitemAction() {
		
		$error_msg = null;
		$success = true;
		$id = $this->getEvent()->getRouteMatch()->getParam('param1');
		if ($id == null):
		   $error_msg = "Invalid InsertionOrderLineItemID";
		   $success = false;
		   $data = array(
	         'success' => $success,
	         'data' => array('error_msg' => $error_msg)
   		  );
   		 
		  $this->setJsonHeader();
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

			$update_data = array('type'=>'InsertionOrderLineItemID', 'id'=>$id);
			$return_val = \transformation\TransformPreview::previewCheckBannerID($id, $this->auth, $this->config_handle, $this->getServiceLocator()->get('mail.transport'), $update_data);

			if ($return_val !== null && array_key_exists("error", $return_val)):

				$success = false;
				$data = array(
			       'success' => $success,
			       'data' => array('error_msg' => $return_val['error'])
		   		);
				
			   $this->setJsonHeader();
		   	   return $this->getResponse()->setContent(json_encode($data));
			endif;
			
			if ($return_val !== null):
				$id 	= $return_val["InsertionOrderLineItemPreviewID"];
			endif;
	   endif;

		// ACL PREVIEW PERMISSIONS CHECK
		//transformation\CheckPermissions::checkEditPermissionInsertionOrderLineItemPreview($id, $auth, $config);
		$response = transformation\CheckPermissions::checkEditPermissionInsertionOrderLineItemPreview($id, $this->auth, $this->config_handle);
		if(array_key_exists("error", $response) > 0):
			$success = false;
			$data = array(
		       'success' => $success,
		       'data' => array('error_msg' => $response['error'])
	   		);
	   		
		   $this->setJsonHeader();
	   	   return $this->getResponse()->setContent(json_encode($data));
		endif;

		$InsertionOrderLineItemPreviewFactory = \_factory\InsertionOrderLineItemPreview::get_instance();
		$params = array();
		$params["InsertionOrderLineItemPreviewID"] = $id;

		$InsertionOrderLineItemPreview = $InsertionOrderLineItemPreviewFactory->get_row($params);

		if ($InsertionOrderLineItemPreview == null):
		  $error_msg = "Invalid InsertionOrderLineItemID";
		   $success = false;
		   $data = array(
	         'success' => $success,
	         'data' => array('error_msg' => $error_msg)
   		  );
   		 
		  $this->setJsonHeader();
          return $this->getResponse()->setContent(json_encode($data));
		endif;

		$campaign_preview_id = $InsertionOrderLineItemPreview->InsertionOrderPreviewID;

		$InsertionOrderLineItemPreviewFactory->deActivateInsertionOrderLineItemPreview($id);

		$data = array(
	        'success' => $success,
			'location' => '/private-exchange/viewlineitem/',
			'previewid' => $campaign_preview_id,
	        'data' => array('error_msg' => $error_msg)
   		 );

		$this->setJsonHeader();
        return $this->getResponse()->setContent(json_encode($data));
		
		/*$refresh_url = "/private-exchange/viewlineitem/" . $campaign_preview_id . "?ispreview=true";
		$viewModel = new ViewModel(array('refresh_url' => $refresh_url));

		return $viewModel->setTemplate('dashboard-manager/demand/interstitial.phtml');*/

	}

	/**
	 * 
	 * @return \Zend\View\Model\ViewModel
	 */
	public function viewlineitemAction() {
	    $id = $this->getEvent()->getRouteMatch()->getParam('param1');
        if ($id == null):
            die("Invalid InsertionOrderID");
        endif;

		$initialized = $this->initialize();
		if ($initialized !== true) return $initialized;

        $is_preview = $this->getRequest()->getQuery('ispreview');
        $campaign_preview_id = "";

        // verify
		if ($is_preview == "true"):
			$is_preview = \transformation\TransformPreview::doesPreviewInsertionOrderExist($id, $this->auth);
		endif;

		if ($is_preview == true):
			// ACL PERMISSIONS CHECK
			transformation\CheckPermissions::checkEditPermissionInsertionOrderPreview($id, $this->auth, $this->config_handle);

			$InsertionOrderLineItemPreviewFactory = \_factory\InsertionOrderLineItemPreview::get_instance();
			$params = array();
			$params["InsertionOrderPreviewID"] = $id;
			$params["Active"] = 1;

			$rtb_banner_list = $InsertionOrderLineItemPreviewFactory->get($params);
			$campaign_preview_id = $id;
			$id = "";
		else:
			// ACL PERMISSIONS CHECK
			transformation\CheckPermissions::checkEditPermissionInsertionOrder($id, $this->auth, $this->config_handle);

			$InsertionOrderLineItemFactory = \_factory\InsertionOrderLineItem::get_instance();
			$params = array();
			$params["InsertionOrderID"] = $id;
			$params["Active"] = 1;
			$rtb_banner_list = $InsertionOrderLineItemFactory->get($params);

		endif;

		$navigation = $this->getServiceLocator()->get('navigation');
                $page = $navigation->findBy('id', 'ViewBannerLevel');
                $page->set("label","View Banners (" . $this->getBreadCrumbInfoFromInsertionOrder($id, $campaign_preview_id, $is_preview)["BCInsertionOrder"] . ")");
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
				'bread_crumb_info' => $this->getBreadCrumbInfoFromInsertionOrder($id, $campaign_preview_id, $is_preview),
				'user_id_list' => $this->user_id_list_demand_customer,
	    		'user_identity' => $this->identity(),
				'true_user_name' => $this->auth->getUserName(),
				'header_title' => '<a href="/private-exchange/createlineitem/' . $ad_campaign_id . $this->preview_query . '">Create New Line Item</a>',
				'is_super_admin' => $this->is_super_admin,
				'effective_id' => $this->auth->getEffectiveIdentityID(),
				'impersonate_id' => $this->ImpersonateID
		));
	}

	/**
	 * 
	 * @return \Zend\View\Model\ViewModel
	 */
	public function createlineitemAction() {
	    $id = $this->getEvent()->getRouteMatch()->getParam('param1');
        if ($id == null):
            die("Invalid InsertionOrderID");
        endif;

		$initialized = $this->initialize();
		if ($initialized !== true) return $initialized;

        $is_preview = $this->getRequest()->getQuery('ispreview');

        // verify
        if ($is_preview == "true"):
        	$is_preview = \transformation\TransformPreview::doesPreviewInsertionOrderExist($id, $this->auth);
        endif;

        $campaignpreviewid = "";

        if ($is_preview == "true"):
	        // ACL PERMISSIONS CHECK
	        transformation\CheckPermissions::checkEditPermissionInsertionOrderPreview($id, $this->auth, $this->config_handle);
        	$campaignpreviewid = $id;
        	$id = "";
        	
        	$PmpDealPublisherWebsiteToInsertionOrderPreviewFactory = \_factory\PmpDealPublisherWebsiteToInsertionOrderPreview::get_instance();
        	$params = array();
        	$params["InsertionOrderPreviewID"] = $campaignpreviewid;
        	$PmpDealPublisherWebsiteToInsertionOrderList = $PmpDealPublisherWebsiteToInsertionOrderPreviewFactory->get($params);
        		
        	$SspRtbChannelToInsertionOrderPreviewFactory = \_factory\SspRtbChannelToInsertionOrderPreview::get_instance();
        	$params = array();
        	$params["InsertionOrderPreviewID"] = $campaignpreviewid;
        	$SspRtbChannelToInsertionOrderList = $SspRtbChannelToInsertionOrderPreviewFactory->get($params);
        	
        else:
	        // ACL PERMISSIONS CHECK
	        transformation\CheckPermissions::checkEditPermissionInsertionOrder($id, $this->auth, $this->config_handle);
        
	        $PmpDealPublisherWebsiteToInsertionOrderFactory = \_factory\PmpDealPublisherWebsiteToInsertionOrder::get_instance();
	        $params = array();
	        $params["InsertionOrderID"] = $id;
	        $PmpDealPublisherWebsiteToInsertionOrderList = $PmpDealPublisherWebsiteToInsertionOrderFactory->get($params);
	        
	        $SspRtbChannelToInsertionOrderFactory = \_factory\SspRtbChannelToInsertionOrder::get_instance();
	        $params = array();
	        $params["InsertionOrderID"] = $id;
	        $SspRtbChannelToInsertionOrderList = $SspRtbChannelToInsertionOrderFactory->get($params);
        
        endif;

        $current_mimes 					= array();
        $current_apis_supported 		= array();
        $current_protocols 				= array();
        $current_delivery_methods 		= array();
        $current_playback_methods 		= array();
        
        $current_start_delay 			= "";
        $current_linearity 				= "";

        $imageurl						= "";
        $landingpageurl					= "";
        
        return new ViewModel(array(
        		'imageurl'					=> $imageurl,
        		'landingpageurl'			=> $landingpageurl,
        		'ispreview'	  				=> $is_preview == true ? '1' : '0',
        		'campaignid'       			=> $id,
        		'campaignpreviewid' 		=> $campaignpreviewid,
                'mobile_options'    		=> \util\BannerOptions::$mobile_options,
                'size_list'         		=> \util\BannerOptions::$iab_banner_options,
        		'pmp_deal_list' 			=> $PmpDealPublisherWebsiteToInsertionOrderList,
        		'ssp_channel_list' 			=> $SspRtbChannelToInsertionOrderList,
				'bread_crumb_info' 			=> $this->getBreadCrumbInfoFromInsertionOrder($id, $campaignpreviewid, $is_preview),
        		'user_id_list' => $this->user_id_list_demand_customer,
    			'center_class' 				=> 'centerj',
	    		'user_identity' 			=> $this->identity(),
	    		'true_user_name' => $this->auth->getUserName(),
				'header_title' => 'Create Line Item',
				'is_super_admin' => $this->is_super_admin,
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
	
	/**
	 *
	 * @return Ambigous <\Zend\View\Model\ViewModel, \Zend\View\Model\ViewModel>
	 */
	public function uploadcreativeAction() {
	
		$initialized = $this->initialize();
		if ($initialized !== true) return $initialized;
	
		$creatives_dir = 'public/creatives/' . $this->auth->getUserID() . '/';
		
		if (!file_exists($creatives_dir)):
			mkdir($creatives_dir, 0755, true);
		endif;
		
		$site_url = $this->config_handle['delivery']['site_url'];
		
		if(substr($site_url, -1) == '/'):
			$site_url = substr($site_url, 0, -1);
		endif;
		
		$files =  $this->request->getFiles()->toArray();
		$httpadapter = new \Zend\File\Transfer\Adapter\Http();
		$filesize  = new \Zend\Validator\File\Size(array('max' => 2000000 )); //2MB
		$extension = new \Zend\Validator\File\Extension(array('extension' => array('jpg', 'jpeg', 'png', 'gif', 'swf')));
		$httpadapter->setValidators(array($filesize, $extension), $files['file']['name']);
		$ext = pathinfo($files['file']['name'], PATHINFO_EXTENSION);
		$newName = md5(rand() . $files['file']['name']) . '.' . $ext;
		$httpadapter->addFilter('File\Rename', array(
				'target' => $creatives_dir . $newName,
				'overwrite' => true
		));
		if($httpadapter->isValid()):
			if($httpadapter->receive($files['file']['name'])):
				$httpadapter->getFilter('File\Rename')->getFile();
				$newfile = $httpadapter->getFileName();
				header("Content-type: text/plain");
				echo $site_url . substr($newfile, strlen('public'));
				exit;
			endif;
		endif;
		$error = array();
		$dataError = $httpadapter->getMessages();
		foreach($dataError as $key=>$row):
			$error[] = $row;
		endforeach; 
		http_response_code(400);
		header("Content-type: text/plain");
		echo implode(',', $error);
		exit;

	}
	
	/**
	 * 
	 * @return Ambigous <\Zend\View\Model\ViewModel, \Zend\View\Model\ViewModel>
	 */
	public function newlineitemAction() {

		$initialized = $this->initialize();
		if ($initialized !== true) return $initialized;

		$ImpressionType = $this->getRequest()->getPost('ImpressionType');
		
		if ($ImpressionType != 'banner' && $ImpressionType != 'image' && $ImpressionType != 'video'):
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
		
		$adtag = $this->getRequest()->getPost('adtag');
		
		if ($ImpressionType == 'video'):
			$this->validateInput($needed_input_video);
		elseif ($ImpressionType == 'image'):
			$this->validateInput($needed_input_banner);
		
			preg_match('/< *img[^>]*src *= *["\']?([^"\']*)/i', $adtag, $matches);
			
			if (!isset($matches[1])):
				die("Required Field: <img src= attribute was missing");
			endif;
			
			preg_match('/href=[\'"]?([^\s\>\'"]*)[\'"\>]/', $adtag, $matches);
			
			if (!isset($matches[1])):
				die("Required Field: <a href= attribute was missing");
			endif;
			
		else:
			$this->validateInput($needed_input_banner);
		endif;
		
		$campaignid 			= $this->getRequest()->getPost('campaignid');
		$campaign_preview_id 	= $this->getRequest()->getPost('campaignpreviewid');
		$bannerid 				= $this->getRequest()->getPost('bannerid');
		$banner_preview_id 		= $this->getRequest()->getPost('bannerpreviewid');
		$ispreview 				= $this->getRequest()->getPost('ispreview');

		$px_feeds 					= $this->getRequest()->getPost('px-feeds');
		$pc_feeds 					= $this->getRequest()->getPost('pc-feeds');
		$ssp_feeds 					= $this->getRequest()->getPost('ssp-feeds');
		$pc_feeds 					= is_array($pc_feeds) ? $pc_feeds : array();
		$px_feeds 					= is_array($px_feeds) ? $px_feeds : array();
		$ssp_feeds 					= is_array($ssp_feeds) ? $ssp_feeds : array();
		$exchange_feeds 			= array_merge($pc_feeds, $px_feeds);
		
		if ($ispreview != true):
			/*
			 * THIS METHOD CHECKS IF THERE IS AN EXISTING PREVIEW MODE CAMPAIGN
			* IF NOT, IT CHECKS THE ACL PERMISSIONS ON THE PRODUCTION BANNER/CAMPAIGN REFERENCED
			* THEN IT CREATES A PREVIEW VERSION OF THE AD CAMPAIGN
			*/

			if ($bannerid != null):
				$update_data = array('type'=>'InsertionOrderLineItemID', 'id'=>$bannerid);
			else:
				$update_data = array('type'=>'InsertionOrderID', 'id'=>$campaignid);
			endif;

			$return_val = \transformation\TransformPreview::previewCheckInsertionOrderID($campaignid, $this->auth, $this->config_handle, $this->getServiceLocator()->get('mail.transport'), $update_data);
			
			if ($return_val !== null):
				if ($bannerid != null):
					$campaign_preview_id 	= $return_val["InsertionOrderPreviewID"];
					$banner_preview_id 		= $return_val["InsertionOrderLineItemPreviewID"];
				else:
					$campaign_preview_id 	= $return_val["InsertionOrderPreviewID"];
				endif;
			endif;
		endif;

		
		// ACL PREVIEW PERMISSIONS CHECK
		transformation\CheckPermissions::checkEditPermissionInsertionOrderPreview($campaign_preview_id, $this->auth, $this->config_handle);

		$bannername = $this->getRequest()->getPost('bannername');
		$startdate = $this->getRequest()->getPost('startdate');
		$enddate = $this->getRequest()->getPost('enddate');
		$ismobile = $this->getRequest()->getPost('ismobile');
		$iabsize = $this->getRequest()->getPost('iabsize');
		$height = $this->getRequest()->getPost('height');
		$width = $this->getRequest()->getPost('width');
		$weight = $this->getRequest()->getPost('weight');
		$bidamount = $this->getRequest()->getPost('bidamount');
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
		
		$BannerPreview = new \model\InsertionOrderLineItemPreview();
		if ($banner_preview_id != null):
		  $BannerPreview->InsertionOrderLineItemPreviewID             = $banner_preview_id;
		endif;

		$BannerPreview->UserID             	= $this->auth->getEffectiveUserID();

		$BannerPreview->Name                      = $bannername;
		$BannerPreview->InsertionOrderPreviewID       = $campaign_preview_id;
		$BannerPreview->StartDate                 = date("Y-m-d H:i:s", strtotime($startdate));
		$BannerPreview->EndDate                   = date("Y-m-d H:i:s", strtotime($enddate));
		
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
		$BannerPreview->WentLiveDate        	  = '0000-00-00 00:00:00';
		
		$InsertionOrderLineItemPreviewFactory = \_factory\InsertionOrderLineItemPreview::get_instance();
		$banner_preview_id_new = $InsertionOrderLineItemPreviewFactory->saveInsertionOrderLineItemPreview($BannerPreview);

		if ($banner_preview_id_new != null):
			$banner_preview_id = $banner_preview_id_new;
		endif;
		if ($BannerPreview->InsertionOrderLineItemPreviewID == null):
			$BannerPreview->InsertionOrderLineItemPreviewID = $banner_preview_id;
		endif;
		
		$InsertionOrderLineItemVideoRestrictionsPreviewFactory = \_factory\InsertionOrderLineItemVideoRestrictionsPreview::get_instance();
		$InsertionOrderLineItemRestrictionsPreviewFactory = \_factory\InsertionOrderLineItemRestrictionsPreview::get_instance();
		
		if ($ImpressionType == 'video'):

			$params = array();
			$params["InsertionOrderLineItemPreviewID"] = $banner_preview_id;
			$InsertionOrderLineItemVideoRestrictionsPreview = $InsertionOrderLineItemVideoRestrictionsPreviewFactory->get_row($params);
			
			if ($InsertionOrderLineItemVideoRestrictionsPreview == null):
			
				$InsertionOrderLineItemVideoRestrictionsPreview = new \model\InsertionOrderLineItemVideoRestrictionsPreview();
				
			endif;
			
			$InsertionOrderLineItemVideoRestrictionsPreview->InsertionOrderLineItemPreviewID 			= $banner_preview_id;

			$InsertionOrderLineItemVideoRestrictionsPreview->DateCreated               			= date("Y-m-d H:i:s");
			
			$InsertionOrderLineItemVideoRestrictionsPreview->MimesCommaSeparated 				= trim($mimes);
			$InsertionOrderLineItemVideoRestrictionsPreview->ProtocolsCommaSeparated 			= trim($protocols);
			$InsertionOrderLineItemVideoRestrictionsPreview->ApisSupportedCommaSeparated 		= trim($apis_supported);
			$InsertionOrderLineItemVideoRestrictionsPreview->DeliveryCommaSeparated 			= trim($delivery);
			$InsertionOrderLineItemVideoRestrictionsPreview->PlaybackCommaSeparated 			= trim($playback);
			
			$InsertionOrderLineItemVideoRestrictionsPreview->StartDelay 						= trim($start_delay);
			$InsertionOrderLineItemVideoRestrictionsPreview->Linearity 							= trim($linearity);

			
			$InsertionOrderLineItemVideoRestrictionsPreviewFactory->saveInsertionOrderLineItemVideoRestrictionsPreview($InsertionOrderLineItemVideoRestrictionsPreview);
			
			$InsertionOrderLineItemRestrictionsPreviewFactory->deleteInsertionOrderLineItemRestrictionsPreview($banner_preview_id);
			
		else:
		
			$InsertionOrderLineItemVideoRestrictionsPreviewFactory->deleteInsertionOrderLineItemVideoRestrictionsPreview($banner_preview_id);
			
		endif;

		
		/*
		 * Private Exchange Feeds
	     * 
	     * wipe out existing preview data first
	     */
		$PmpDealPublisherWebsiteToInsertionOrderLineItemPreviewFactory = \_factory\PmpDealPublisherWebsiteToInsertionOrderLineItemPreview::get_instance();
		
		$PmpDealPublisherWebsiteToInsertionOrderLineItemPreviewFactory->deletePmpDealPublisherWebsiteToInsertionOrderLineItemByInsertionOrderLineItemPreviewID($banner_preview_id);
		
		foreach ($exchange_feeds as $raw_feed_data):
		
			$raw_feed_data = rawurldecode($raw_feed_data);
		
			$exchange_feed_data = \util\AuthHelper::parse_feed_id($raw_feed_data);
			 
			if ($exchange_feed_data === null):
				continue;
			endif;
			
			$exchange_feed_id 			= intval($exchange_feed_data["id"]);
			$exchange_feed_description 	= $exchange_feed_data["description"];
			$is_local = false;
			
			if (!$this->is_super_admin):
			
				$authorized = \util\AuthHelper::domain_user_authorized_px_publisher_website_passthru($this->config_handle, $this->auth->getUserID(), $exchange_feed_id, $is_local);

				if (!$authorized):
					$viewModel = new ViewModel(array(
										'admin_email' => $this->config_handle['mail']['reply-to']['email'],
										'refresh_url' => '/private-exchange/editlineitem/' . $banner_preview_id . '?ispreview=true'
					));
					return $viewModel->setTemplate('dashboard-manager/demand/creditapp.phtml');
				endif;
			
			else:
	
				$PublisherWebsiteFactory 		= \_factory\PublisherWebsite::get_instance();
				$params = array();
				$params["PublisherWebsiteID"] 	= $exchange_feed_id;
				$PublisherWebsite	 			= $PublisherWebsiteFactory->get_row_cached($this->config_handle, $params);
				$ret_val = \util\AuthHelper::domain_user_authorized_publisher_passthru($this->auth->getEffectiveUserID(), $PublisherWebsite->DomainOwnerID);
				if ($ret_val === true):
					$is_local = true;
				endif;
			endif;
				
			$params = array();
			$params["PublisherWebsiteID"] = $exchange_feed_id;
			$_PmpDealPublisherWebsiteToInsertionOrderLineItemPreview = $PmpDealPublisherWebsiteToInsertionOrderLineItemPreviewFactory->get_row($params);
			
			$PmpDealPublisherWebsiteToInsertionOrderLineItemPreview = new \model\PmpDealPublisherWebsiteToInsertionOrderLineItemPreview();
			
			if ($_PmpDealPublisherWebsiteToInsertionOrderLineItemPreview != null):
				$PmpDealPublisherWebsiteToInsertionOrderLineItemPreview->PmpDealPublisherWebsiteToInsertionOrderPreviewID = $_PmpDealPublisherWebsiteToInsertionOrderLineItemPreview->PmpDealPublisherWebsiteToInsertionOrderLineItemPreviewID;
			endif;
			
			$PmpDealPublisherWebsiteToInsertionOrderLineItemPreview->PublisherWebsiteID 				= $exchange_feed_id;
			$PmpDealPublisherWebsiteToInsertionOrderLineItemPreview->PublisherWebsiteLocal 				= $is_local;
			$PmpDealPublisherWebsiteToInsertionOrderLineItemPreview->PublisherWebsiteDescription 		= $exchange_feed_description;
			$PmpDealPublisherWebsiteToInsertionOrderLineItemPreview->InsertionOrderLineItemPreviewID	= $banner_preview_id;
			$PmpDealPublisherWebsiteToInsertionOrderLineItemPreview->Enabled							= 1;
			
			$PmpDealPublisherWebsiteToInsertionOrderLineItemPreviewFactory->savePmpDealPublisherWebsiteToInsertionOrderLineItemPreview($PmpDealPublisherWebsiteToInsertionOrderLineItemPreview);
			
		endforeach;
		 
		 
		/*
		 * SSP RTB Feeds
	     * 
	     * wipe out existing preview data first
	     */
	    $SspRtbChannelToInsertionOrderLineItemPreviewFactory = \_factory\SspRtbChannelToInsertionOrderLineItemPreview::get_instance();
			
	    $SspRtbChannelToInsertionOrderLineItemPreviewFactory->deleteSspRtbChannelToInsertionOrderLineItemByInsertionOrderLineItemPreviewID($banner_preview_id);

	    /*
	     * If they are adding inventory from SSP RTB Channels
	     * make sure they are approved for that
	     */
	    if (!$this->is_super_admin && count($ssp_feeds) >= 1):
	    	if (!\util\CreditHelper::wasApprovedForSspRtbInventoryAuthUserID($this->auth->getUserID())):
					$viewModel = new ViewModel(array(
										'admin_email' => $this->config_handle['mail']['reply-to']['email'],
										'refresh_url' => '/private-exchange/editlineitem/' . $banner_preview_id . '?ispreview=true'
					));
					return $viewModel->setTemplate('dashboard-manager/demand/creditapp.phtml');
	    	endif;
	    endif;
	    
		foreach ($ssp_feeds as $raw_feed_data):
		
			$raw_feed_data = rawurldecode($raw_feed_data);
		
			$ssp_feed_data = \util\AuthHelper::parse_feed_id($raw_feed_data);
			 
			if ($ssp_feed_data === null):
				continue;
			endif;
			
			$ssp_feed_id 			= $ssp_feed_data["id"];
			$ssp_exchange 			= $ssp_feed_data["exchange"];
			
			$ssp_feed_id = str_replace('__COLON__', ':', $ssp_feed_id);
			$ssp_exchange = str_replace('__COLON__', ':', $ssp_exchange);
			
			$ssp_feed_description 	= $ssp_feed_data["description"];
			
			if (!$this->is_super_admin):
			
				$authorized = \util\AuthHelper::domain_user_authorized_ssp_passthru($this->auth->getUserID(), $ssp_feed_id);
				
				if (!$authorized):
					die("You are not authorized to add inventory from SSP RTB Channel: " . $ssp_feed_id . ' - ' . $ssp_feed_description . " <br />Please contact an administrator for more information.");
				endif;
			endif;
			
			$params = array();
			$params["SspPublisherChannelID"] = $ssp_feed_id;
			$_SspRtbChannelToInsertionOrderLineItemPreview = $SspRtbChannelToInsertionOrderLineItemPreviewFactory->get_row($params);
			
			$SspRtbChannelToInsertionOrderLineItemPreview = new \model\SspRtbChannelToInsertionOrderLineItemPreview();
			
			if ($_SspRtbChannelToInsertionOrderLineItemPreview != null):
				$SspRtbChannelToInsertionOrderLineItemPreview->SspRtbChannelToInsertionOrderLineItemPreviewID = $_SspRtbChannelToInsertionOrderLineItemPreview->SspRtbChannelToInsertionOrderLineItemPreviewID;
			endif;
			
			$SspRtbChannelToInsertionOrderLineItemPreview->SspPublisherChannelID 			= $ssp_feed_id;
			$SspRtbChannelToInsertionOrderLineItemPreview->SspPublisherChannelDescription 	= $ssp_feed_description;
			$SspRtbChannelToInsertionOrderLineItemPreview->SspExchange						= $ssp_exchange;
			$SspRtbChannelToInsertionOrderLineItemPreview->InsertionOrderLineItemPreviewID	= $banner_preview_id;
			$SspRtbChannelToInsertionOrderLineItemPreview->Enabled							= 1;
			
			$SspRtbChannelToInsertionOrderLineItemPreviewFactory->saveSspRtbChannelToInsertionOrderLineItemPreview($SspRtbChannelToInsertionOrderLineItemPreview);
			
		endforeach;		

		$refresh_url = "/private-exchange/viewlineitem/" . $BannerPreview->InsertionOrderPreviewID . "?ispreview=true";
		$viewModel = new ViewModel(array('refresh_url' => $refresh_url));

		return $viewModel->setTemplate('dashboard-manager/demand/interstitial.phtml');

	}

	/**
	 * 
	 * @return \Zend\View\Model\ViewModel
	 */
	public function editlineitemAction() {

		$id = $this->getEvent()->getRouteMatch()->getParam('param1');
		if ($id == null):
		  die("Invalid InsertionOrderLineItemID");
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
			transformation\CheckPermissions::checkEditPermissionInsertionOrderLineItemPreview($id, $this->auth, $this->config_handle);

			$InsertionOrderLineItemVideoRestrictionsPreviewFactory = \_factory\InsertionOrderLineItemVideoRestrictionsPreview::get_instance();
			$params = array();
			$params["InsertionOrderLineItemPreviewID"] = $id;
			$InsertionOrderLineItemVideoRestrictions = $InsertionOrderLineItemVideoRestrictionsPreviewFactory->get_row($params);
			
			$InsertionOrderLineItemPreviewFactory = \_factory\InsertionOrderLineItemPreview::get_instance();
			$params = array();
			$params["Active"] = 1;
			$params["InsertionOrderLineItemPreviewID"] = $id;
			$banner_preview_id = $id;

			$InsertionOrderLineItem = $InsertionOrderLineItemPreviewFactory->get_row($params);

			$PmpDealPublisherWebsiteToInsertionOrderLineItemPreviewFactory = \_factory\PmpDealPublisherWebsiteToInsertionOrderLineItemPreview::get_instance();
			$params = array();
			$params["InsertionOrderLineItemPreviewID"] = $banner_preview_id;
			$PmpDealPublisherWebsiteToInsertionOrderLineItemList = $PmpDealPublisherWebsiteToInsertionOrderLineItemPreviewFactory->get($params);
				
			$SspRtbChannelToInsertionOrderLineItemPreviewFactory = \_factory\SspRtbChannelToInsertionOrderLineItemPreview::get_instance();
			$params = array();
			$params["InsertionOrderLineItemPreviewID"] = $banner_preview_id;
			$SspRtbChannelToInsertionOrderLineItemList = $SspRtbChannelToInsertionOrderLineItemPreviewFactory->get($params);
			
		else:
			// ACL PERMISSIONS CHECK
			transformation\CheckPermissions::checkEditPermissionInsertionOrderLineItem($id, $this->auth, $this->config_handle);

			$InsertionOrderLineItemVideoRestrictionsFactory = \_factory\InsertionOrderLineItemVideoRestrictions::get_instance();
			$params = array();
			$params["InsertionOrderLineItemID"] = $id;
			
			$InsertionOrderLineItemVideoRestrictions = $InsertionOrderLineItemVideoRestrictionsFactory->get_row($params);

			$InsertionOrderLineItemFactory = \_factory\InsertionOrderLineItem::get_instance();
			$params = array();
			$params["Active"] = 1;
			$params["InsertionOrderLineItemID"] = $id;

			$InsertionOrderLineItem = $InsertionOrderLineItemFactory->get_row($params);

			$PmpDealPublisherWebsiteToInsertionOrderLineItemFactory = \_factory\PmpDealPublisherWebsiteToInsertionOrderLineItem::get_instance();
			$params = array();
			$params["InsertionOrderLineItemID"] = $id;
			$PmpDealPublisherWebsiteToInsertionOrderLineItemList = $PmpDealPublisherWebsiteToInsertionOrderLineItemFactory->get($params);
			
			$SspRtbChannelToInsertionOrderLineItemFactory = \_factory\SspRtbChannelToInsertionOrderLineItem::get_instance();
			$params = array();
			$params["InsertionOrderLineItemID"] = $id;
			$SspRtbChannelToInsertionOrderLineItemList = $SspRtbChannelToInsertionOrderLineItemFactory->get($params);
			
		endif;

		if ($InsertionOrderLineItem == null):
		  die("Invalid InsertionOrderLineItemID");
		endif;

		$campaignid               = isset($InsertionOrderLineItem->InsertionOrderID) ? $InsertionOrderLineItem->InsertionOrderID : "";
		$bannerid                 = isset($InsertionOrderLineItem->InsertionOrderLineItemID) ? $InsertionOrderLineItem->InsertionOrderLineItemID : "";
		$campaignpreviewid        = isset($InsertionOrderLineItem->InsertionOrderPreviewID) ? $InsertionOrderLineItem->InsertionOrderPreviewID : "";
		$bannerpreviewid          = isset($InsertionOrderLineItem->InsertionOrderLineItemPreviewID) ? $InsertionOrderLineItem->InsertionOrderLineItemPreviewID : "";
		$bannername               = $InsertionOrderLineItem->Name;
		$startdate                = date('m/d/Y', strtotime($InsertionOrderLineItem->StartDate));
		$enddate                  = date('m/d/Y', strtotime($InsertionOrderLineItem->EndDate));
		$current_mobile           = $InsertionOrderLineItem->IsMobile;
		if ($InsertionOrderLineItem->IsMobile == 2):
		      $size_list                = \util\BannerOptions::$iab_mobile_tablet_banner_options;
		elseif ($InsertionOrderLineItem->IsMobile > 0):
		      $size_list                = \util\BannerOptions::$iab_mobile_phone_banner_options;
		else:
		      $size_list                = \util\BannerOptions::$iab_banner_options;
		endif;
		$height                   = $InsertionOrderLineItem->Height;
		$width                    = $InsertionOrderLineItem->Width;
		$weight                   = $InsertionOrderLineItem->Weight;
		$bidamount                = $InsertionOrderLineItem->BidAmount;
		$adtag                    = $InsertionOrderLineItem->AdTag;
		$landingpagetld           = $InsertionOrderLineItem->LandingPageTLD;
		$current_iabsize          = $InsertionOrderLineItem->IABSize;
		
		$ImpressionType           = $InsertionOrderLineItem->ImpressionType;

		if ($ImpressionType == 'image'):
			preg_match('/< *img[^>]*src *= *["\']?([^"\']*)/i', $adtag, $matches);
			
			if (!isset($matches[1])):
				die("Required Field: <img src= attribute was missing");
			endif;
			
			preg_match('/href=[\'"]?([^\s\>\'"]*)[\'"\>]/', $adtag, $matches);
			
			if (!isset($matches[1])):
				die("Required Field: <a href= attribute was missing");
			endif;
		endif;
		
		$current_mimes 					= array();
		$current_apis_supported 		= array();
		$current_protocols 				= array();
		$current_delivery_methods 		= array();
		$current_playback_methods 		= array();
		
		$current_start_delay 			= "";
		$current_linearity 				= "";
		
		$impression_type				= "banner";
		
		if ($InsertionOrderLineItemVideoRestrictions != null):
		
			$current_mimes_raw = $InsertionOrderLineItemVideoRestrictions->MimesCommaSeparated;
			$current_apis_supported_raw = $InsertionOrderLineItemVideoRestrictions->ApisSupportedCommaSeparated;
			$current_protocols_raw = $InsertionOrderLineItemVideoRestrictions->ProtocolsCommaSeparated;
			$current_delivery_methods_raw = $InsertionOrderLineItemVideoRestrictions->DeliveryCommaSeparated;
			$current_playback_methods_raw = $InsertionOrderLineItemVideoRestrictions->PlaybackCommaSeparated;
			
			$current_start_delay = $InsertionOrderLineItemVideoRestrictions->StartDelay;
			$current_linearity = $InsertionOrderLineItemVideoRestrictions->Linearity;

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
		
		$imageurl			= "";
		$landingpageurl 	= "";
		
		if ($ImpressionType == "image"):
		
			preg_match('/< *img[^>]*src *= *["\']?([^"\']*)/i', $adtag, $matches);
			
			if (isset($matches[1])):
				$imageurl				= $matches[1];
			endif;
			
			preg_match('/href=[\'"]?([^\s\>\'"]*)[\'"\>]/', $adtag, $matches);
			
			if (isset($matches[1])):
				$landingpageurl			= $matches[1];
			endif;
			
		endif;
		
		return new ViewModel(array(
				'imageurl'				  => $imageurl,
				'landingpageurl'		  => $landingpageurl,
				'campaignid'              => $campaignid,
		        'bannerid'                => $bannerid,
				'pmp_deal_list' 		  => $PmpDealPublisherWebsiteToInsertionOrderLineItemList,
				'ssp_channel_list' 		  => $SspRtbChannelToInsertionOrderLineItemList,
				'campaignpreviewid'       => $campaignpreviewid,
				'bannerpreviewid'         => $bannerpreviewid,
				'ispreview' 			  => $is_preview == true ? '1' : '0',
    		    'bannername'              => $bannername,
    		    'startdate'               => $startdate,
    		    'enddate'                 => $enddate,
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
				'header_title' => 'Edit Insertion Order',
				'is_super_admin' => $this->is_super_admin,
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
	
	/*
	 * END NGINAD InsertionOrderLineItem Actions
	*/

	/*
	 * BEGIN NGINAD InsertionOrder Actions
	*/

	/**
	 * 
	 * @return Ambigous <\Zend\View\Model\ViewModel, \Zend\View\Model\ViewModel>
	 */
	public function deleteinsertionorderAction() {

		$error_msg = null;
		$success = true;
		$id = $this->getEvent()->getRouteMatch()->getParam('param1');
		if ($id == null):
		  $error_msg = "Invalid InsertionOrderID";
		  $success = false;
		  $data = array(
	        'success' => $success,
	        'data' => array('error_msg' => $error_msg)
   		 );
   		 
		 $this->setJsonHeader();
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

			$update_data = array('type'=>'InsertionOrderID', 'id'=>$id);
			$return_val = \transformation\TransformPreview::previewCheckInsertionOrderID($id, $this->auth, $this->config_handle, $this->getServiceLocator()->get('mail.transport'), $update_data);
			
			if ($return_val !== null && array_key_exists("error", $return_val)):

				$success = false;
				$data = array(
			       'success' => $success,
			       'data' => array('error_msg' => $return_val['error'])
		   		);
   		
			   $this->setJsonHeader();
		   	   return $this->getResponse()->setContent(json_encode($data));
			endif;

			if ($return_val !== null):
				$id = $return_val["InsertionOrderPreviewID"];
			endif;
		endif;

		// ACL PREVIEW PERMISSIONS CHECK
		//transformation\CheckPermissions::checkEditPermissionInsertionOrderPreview($id, $auth, $config);
		$response = transformation\CheckPermissions::checkEditPermissionInsertionOrderPreview($id, $this->auth, $this->config_handle);

		if(array_key_exists("error", $response) > 0):
			$success = false;
			$data = array(
		       'success' => $success,
		       'data' => array('error_msg' => $response['error'])
	   		);
	   		
		   $this->setJsonHeader();
	   	   return $this->getResponse()->setContent(json_encode($data));
		endif;

		$InsertionOrderPreviewFactory = \_factory\InsertionOrderPreview::get_instance();
		$params = array();
		$params["InsertionOrderPreviewID"] = $id;

		$InsertionOrderPreview = $InsertionOrderPreviewFactory->get_row($params);

		if ($InsertionOrderPreview == null):
		  $error_msg = "Invalid InsertionOrderPreviewID";
		  $success = false;
		  $data = array(
	        'success' => $success,
	        'data' => array('error_msg' => $error_msg)
   		 );
   		 
		 $this->setJsonHeader();
         return $this->getResponse()->setContent(json_encode($data));
		endif;

		$ad_campaign_preview_id = $InsertionOrderPreview->InsertionOrderPreviewID;

		$InsertionOrderLineItemPreviewFactory = \_factory\InsertionOrderLineItemPreview::get_instance();
		$params = array();
		$params["InsertionOrderPreviewID"] = $InsertionOrderPreview->InsertionOrderPreviewID;

		$InsertionOrderLineItemPreviewList = $InsertionOrderLineItemPreviewFactory->get($params);

        foreach ($InsertionOrderLineItemPreviewList as $InsertionOrderLineItemPreview):

            $banner_preview_id = $InsertionOrderLineItemPreview->InsertionOrderLineItemPreviewID;
    		$InsertionOrderLineItemPreviewFactory->deActivateInsertionOrderLineItemPreview($banner_preview_id);

		endforeach;

    	$InsertionOrderPreviewFactory->doDeletedInsertionOrderPreview($ad_campaign_preview_id);

		$data = array(
	        'success' => $success,
	        'data' => array('error_msg' => $error_msg)
   		 );
   		 
		 $this->setJsonHeader();
         return $this->getResponse()->setContent(json_encode($data));
		
		/*$refresh_url = "/private-exchange/?ispreview=true";
		$viewModel = new ViewModel(array('refresh_url' => $refresh_url));

		return $viewModel->setTemplate('dashboard-manager/demand/interstitial.phtml');*/

	}

	/**
	 * 
	 * @return \Zend\View\Model\ViewModel
	 */
	public function editinsertionorderAction() {
		$id = $this->getEvent()->getRouteMatch()->getParam('param1');
		if ($id == null):
			die("Invalid InsertionOrderID");
		endif;

		$initialized = $this->initialize();
		if ($initialized !== true) return $initialized;

		$is_preview = $this->getRequest()->getQuery('ispreview');

		// verify
		if ($is_preview == "true"):
			$is_preview = \transformation\TransformPreview::doesPreviewInsertionOrderExist($id, $this->auth);
		endif;
		$campaign_preview_id = "";

		if ($is_preview == true):

			// ACL PREVIEW PERMISSIONS CHECK
			transformation\CheckPermissions::checkEditPermissionInsertionOrderPreview($id, $this->auth, $this->config_handle);

			$InsertionOrderPreviewFactory = \_factory\InsertionOrderPreview::get_instance();
			$params = array();
			$params["InsertionOrderPreviewID"] = $id;
			$params["Active"] = 1;

			$InsertionOrder = $InsertionOrderPreviewFactory->get_row($params);

			$campaign_preview_id = $id;
			$id = "";

			$PmpDealPublisherWebsiteToInsertionOrderPreviewFactory = \_factory\PmpDealPublisherWebsiteToInsertionOrderPreview::get_instance();
			$params = array();
			$params["InsertionOrderPreviewID"] = $campaign_preview_id;
			$PmpDealPublisherWebsiteToInsertionOrderList = $PmpDealPublisherWebsiteToInsertionOrderPreviewFactory->get($params);
			
			$SspRtbChannelToInsertionOrderPreviewFactory = \_factory\SspRtbChannelToInsertionOrderPreview::get_instance();
			$params = array();
			$params["InsertionOrderPreviewID"] = $campaign_preview_id;
			$SspRtbChannelToInsertionOrderList = $SspRtbChannelToInsertionOrderPreviewFactory->get($params);
			
		else:
			// ACL PERMISSIONS CHECK
			transformation\CheckPermissions::checkEditPermissionInsertionOrder($id, $this->auth, $this->config_handle);

			$InsertionOrderFactory = \_factory\InsertionOrder::get_instance();
			$params = array();
			$params["InsertionOrderID"] = $id;
			$params["Active"] = 1;

			$InsertionOrder = $InsertionOrderFactory->get_row($params);

			$PmpDealPublisherWebsiteToInsertionOrderFactory = \_factory\PmpDealPublisherWebsiteToInsertionOrder::get_instance();
			$params = array();
			$params["InsertionOrderID"] = $id;
			$PmpDealPublisherWebsiteToInsertionOrderList = $PmpDealPublisherWebsiteToInsertionOrderFactory->get($params);
				
			$SspRtbChannelToInsertionOrderFactory = \_factory\SspRtbChannelToInsertionOrder::get_instance();
			$params = array();
			$params["InsertionOrderID"] = $id;
			$SspRtbChannelToInsertionOrderList = $SspRtbChannelToInsertionOrderFactory->get($params);
			
			
		endif;

		if ($InsertionOrder == null):
			die("Invalid InsertionOrderID");
		endif;

		$campaignname              = $InsertionOrder->Name;
		$startdate                 = date('m/d/Y', strtotime($InsertionOrder->StartDate));
		$enddate                   = date('m/d/Y', strtotime($InsertionOrder->EndDate));
		$customername              = $InsertionOrder->Customer;
		$customerid                = $InsertionOrder->CustomerID;
		$maximpressions            = $InsertionOrder->MaxImpressions;
		$maxspend                  = sprintf("%1.2f", $InsertionOrder->MaxSpend);

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
				'pmp_deal_list' => $PmpDealPublisherWebsiteToInsertionOrderList,
				'ssp_channel_list' => $SspRtbChannelToInsertionOrderList,
				'maxspend' => $maxspend,
				'bread_crumb_info' => $this->getBreadCrumbInfoFromInsertionOrder($id, $campaign_preview_id, $is_preview),
				'user_id_list' => $this->user_id_list_demand_customer,
    			'center_class' => 'centerj',
	    		'user_identity' => $this->identity(),
	    		'true_user_name' => $this->auth->getUserName(),
				'header_title' => 'Edit Insertion Order',
				'is_super_admin' => $this->is_super_admin,
				'effective_id' => $this->auth->getEffectiveIdentityID(),
				'impersonate_id' => $this->ImpersonateID
		));
	}

	/**
	 * This function does ZERO, right now. Empty.
	 */
	public function createinsertionorderAction() {

		$initialized = $this->initialize();
		if ($initialized !== true) return $initialized;
		
		return new ViewModel(array(
				'ispreview'	  => "true",
				'user_id_list' => $this->user_id_list_demand_customer,
				'user_identity' => $this->identity(),
	    		'true_user_name' => $this->auth->getUserName(),
				'header_title' => 'Create New Insertion Order',
				'is_super_admin' => $this->is_super_admin,
				'effective_id' => $this->auth->getEffectiveIdentityID(),
				'impersonate_id' => $this->ImpersonateID
		));
	    		
	}

	/**
	 * 
	 * @return Ambigous <\Zend\View\Model\ViewModel, \Zend\View\Model\ViewModel>
	 */
	public function newinsertionorderAction() {

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
	    $maximpressions = intval($this->getRequest()->getPost('maximpressions'));
	    $maxspend = $this->getRequest()->getPost('maxspend');
	    $campaignid = $this->getRequest()->getPost('campaignid');
	    $campaign_preview_id 		= $this->getRequest()->getPost('campaignpreviewid');
	    $ispreview 					= $this->getRequest()->getPost('ispreview');

	    $px_feeds 					= $this->getRequest()->getPost('px-feeds');
	    $pc_feeds 					= $this->getRequest()->getPost('pc-feeds');
	    $ssp_feeds 					= $this->getRequest()->getPost('ssp-feeds');
	    $pc_feeds 					= is_array($pc_feeds) ? $pc_feeds : array();
	    $px_feeds 					= is_array($px_feeds) ? $px_feeds : array();
	    $ssp_feeds 					= is_array($ssp_feeds) ? $ssp_feeds : array();
	    $exchange_feeds 			= array_merge($pc_feeds, $px_feeds);
	    
	    // 4 byte max int(11) check
	    if ($maximpressions < 1 || $maximpressions > 2147483647) $maximpressions = 2147483647;
	    
	    $InsertionOrderPreview = new \model\InsertionOrderPreview();

	    if ($campaignid != null && $ispreview != true):
		    /*
		     * THIS METHOD CHECKS IF THERE IS AN EXISTING PREVIEW MODE CAMPAIGN
		    * IF NOT, IT CHECKS THE ACL PERMISSIONS ON THE PRODUCTION BANNER/CAMPAIGN REFERENCED
		    * THEN IT CREATES A PREVIEW VERSION OF THE AD CAMPAIGN
		    */

		    $update_data = array('type'=>'InsertionOrderID', 'id'=>$campaignid);
		    $return_val = \transformation\TransformPreview::previewCheckInsertionOrderID($campaignid, $this->auth, $this->config_handle, $this->getServiceLocator()->get('mail.transport'), $update_data);

		    if ($return_val !== null):
			    $campaign_preview_id 	= $return_val["InsertionOrderPreviewID"];
		    endif;

		    $InsertionOrderPreview->InsertionOrderID 	= $campaignid;

	    endif;

	    if ($campaign_preview_id != null):
		    // ACL PREVIEW PERMISSIONS CHECK
			transformation\CheckPermissions::checkEditPermissionInsertionOrderPreview($campaign_preview_id, $this->auth, $this->config_handle);
		  	$InsertionOrderPreview->InsertionOrderPreviewID	= $campaign_preview_id;
	
			$params = array();
			$params["InsertionOrderPreviewID"] = $campaign_preview_id;
			$InsertionOrderPreviewFactory = \_factory\InsertionOrderPreview::get_instance();
		  	$_InsertionOrderPreview = $InsertionOrderPreviewFactory->get_row($params);
		  	$InsertionOrderPreview->InsertionOrderID 	= $_InsertionOrderPreview->InsertionOrderID;
		endif;
		
	    // else new campaign, ispreview is always true

	    $InsertionOrderPreview->UserID             		  = $this->auth->getEffectiveUserID();

    	$InsertionOrderPreview->Name                      = $campaignname;
    	$InsertionOrderPreview->StartDate                 = date("Y-m-d H:i:s", strtotime($startdate));
    	$InsertionOrderPreview->EndDate                   = date("Y-m-d H:i:s", strtotime($enddate));
    	$InsertionOrderPreview->Customer                  = $customername;
    	$InsertionOrderPreview->CustomerID                = $customerid;
    	$InsertionOrderPreview->ImpressionsCounter        = 0;
    	$InsertionOrderPreview->MaxImpressions            = $maximpressions;
    	$InsertionOrderPreview->CurrentSpend              = 0;
    	$InsertionOrderPreview->MaxSpend                  = $maxspend;
    	$InsertionOrderPreview->Active                    = 1;
    	$InsertionOrderPreview->DateCreated               = date("Y-m-d H:i:s");
    	$InsertionOrderPreview->DateUpdated               = date("Y-m-d H:i:s");
    	$InsertionOrderPreview->ChangeWentLive            = 0;
    	$InsertionOrderPreview->WentLiveDate              = '0000-00-00 00:00:00';
    	 
	    $InsertionOrderPreviewFactory = \_factory\InsertionOrderPreview::get_instance();
	    $PublisherWebsiteFactory = \_factory\PublisherWebsite::get_instance();
	    $new_campaign_preview_id = $InsertionOrderPreviewFactory->saveInsertionOrderPreview($InsertionOrderPreview);
	    
	    /*
	     * Private Exchange Feeds
	     * 
	     * wipe out existing preview data first
	     */
	    $PmpDealPublisherWebsiteToInsertionOrderPreviewFactory = \_factory\PmpDealPublisherWebsiteToInsertionOrderPreview::get_instance();
	    
	    $PmpDealPublisherWebsiteToInsertionOrderPreviewFactory->deletePmpDealPublisherWebsiteToInsertionOrderByInsertionOrderPreviewID($new_campaign_preview_id);
	    
	    foreach ($exchange_feeds as $raw_feed_data):
	     
	    	$raw_feed_data = rawurldecode($raw_feed_data);
	    
		    $exchange_feed_data = \util\AuthHelper::parse_feed_id($raw_feed_data);
		     
		    if ($exchange_feed_data === null):
		    	continue;
		    endif;
		    
		    $exchange_feed_id 			= intval($exchange_feed_data["id"]);
		    $exchange_feed_description 	= $exchange_feed_data["description"];
		    $is_local = false;
		    
		    if (!$this->is_super_admin):
			    
			    $authorized = \util\AuthHelper::domain_user_authorized_px_publisher_website_passthru($this->config_handle, $this->auth->getUserID(), $exchange_feed_id, $is_local);
			    
			    if (!$authorized):
					$viewModel = new ViewModel(array(
										'admin_email' => $this->config_handle['mail']['reply-to']['email'],
										'refresh_url' => '/private-exchange/editinsertionorder/' . $new_campaign_preview_id . '?ispreview=true'
					));
					return $viewModel->setTemplate('dashboard-manager/demand/creditapp.phtml');
			    endif;
			    
			else:
            	
     			$domain_object = new \model\PublisherWebsite();
         		$parameters = array("PublisherWebsiteID" => $exchange_feed_id);
             	$domain_object = $PublisherWebsiteFactory->get_row_object($parameters);
                                
				$ret_val = \util\AuthHelper::domain_user_authorized_publisher_passthru($this->auth->getEffectiveUserID(), $domain_object->DomainOwnerID);
				if ($ret_val === true):
					$is_local = true;
				endif;
			endif;
		    

		    $params = array();
		    $params["PublisherWebsiteID"] = $exchange_feed_id;
		    $_PmpDealPublisherWebsiteToInsertionOrderPreview = $PmpDealPublisherWebsiteToInsertionOrderPreviewFactory->get_row($params);
		    
		    $PmpDealPublisherWebsiteToInsertionOrderPreview = new \model\PmpDealPublisherWebsiteToInsertionOrderPreview();
		    
		    if ($_PmpDealPublisherWebsiteToInsertionOrderPreview != null):
		    	$PmpDealPublisherWebsiteToInsertionOrderPreview->PmpDealPublisherWebsiteToInsertionOrderPreviewID = $_PmpDealPublisherWebsiteToInsertionOrderPreview->PmpDealPublisherWebsiteToInsertionOrderPreviewID;
		    endif;
		    
		    $PmpDealPublisherWebsiteToInsertionOrderPreview->PublisherWebsiteID 			= $exchange_feed_id;
		    $PmpDealPublisherWebsiteToInsertionOrderPreview->PublisherWebsiteLocal 			= $is_local;
		    $PmpDealPublisherWebsiteToInsertionOrderPreview->PublisherWebsiteDescription 	= $exchange_feed_description;
		    $PmpDealPublisherWebsiteToInsertionOrderPreview->InsertionOrderPreviewID		= $new_campaign_preview_id;
		    $PmpDealPublisherWebsiteToInsertionOrderPreview->Enabled						= 1;
		    
		    $PmpDealPublisherWebsiteToInsertionOrderPreviewFactory->savePmpDealPublisherWebsiteToInsertionOrderPreview($PmpDealPublisherWebsiteToInsertionOrderPreview);
		    
	    endforeach;
	    
	    /*
	     * SSP RTB Feeds
	     * 
	     * wipe out existing preview data first
	     */
	    $SspRtbChannelToInsertionOrderPreviewFactory = \_factory\SspRtbChannelToInsertionOrderPreview::get_instance();
	    
	    $SspRtbChannelToInsertionOrderPreviewFactory->deleteSspRtbChannelToInsertionOrderByInsertionOrderPreviewID($new_campaign_preview_id);
	    
	    /*
	     * If they are adding inventory from SSP RTB Channels
	     * make sure they are approved for that
	     */
	    if (!$this->is_super_admin && count($ssp_feeds) >= 1):
		    if (!\util\CreditHelper::wasApprovedForSspRtbInventoryAuthUserID($this->auth->getUserID())):
					$viewModel = new ViewModel(array(
										'admin_email' => $this->config_handle['mail']['reply-to']['email'],
										'refresh_url' => '/private-exchange/editinsertionorder/' . $new_campaign_preview_id . '?ispreview=true'
					));
					return $viewModel->setTemplate('dashboard-manager/demand/creditapp.phtml');
		    endif;
	    endif;
	    
	    foreach ($ssp_feeds as $raw_feed_data):
	    
	    	$raw_feed_data = rawurldecode($raw_feed_data);
	    
		    $ssp_feed_data = \util\AuthHelper::parse_feed_id($raw_feed_data);
	    
		    if ($ssp_feed_data === null):
		    	continue;
		    endif;
		    
		    $ssp_feed_id 			= $ssp_feed_data["id"];
		    $ssp_exchange 			= $ssp_feed_data["exchange"];
		    
		    $ssp_feed_id = str_replace('__COLON__', ':', $ssp_feed_id);
		    $ssp_exchange = str_replace('__COLON__', ':', $ssp_exchange);
		    
		    $ssp_feed_description 	= $ssp_feed_data["description"];
		    
		    if (!$this->is_super_admin):
		    
			    $authorized = \util\AuthHelper::domain_user_authorized_ssp_passthru($this->auth->getUserID(), $ssp_feed_id);
			    
			    if (!$authorized):
			    	die("You are not authorized to add inventory from SSP RTB Channel: " . $ssp_feed_id . ' - ' . $ssp_feed_description . " <br />Please contact an administrator for more information.");
			    endif;
			endif;
			
		    $params = array();
		    $params["SspPublisherChannelID"] = $ssp_feed_id;
		    $_SspRtbChannelToInsertionOrderPreview = $SspRtbChannelToInsertionOrderPreviewFactory->get_row($params);
		    
		    $SspRtbChannelToInsertionOrderPreview = new \model\SspRtbChannelToInsertionOrderPreview();
		    
		    if ($_SspRtbChannelToInsertionOrderPreview != null):
		    	$SspRtbChannelToInsertionOrderPreview->SspRtbChannelToInsertionOrderPreviewID = $_SspRtbChannelToInsertionOrderPreview->SspRtbChannelToInsertionOrderPreviewID;
		    endif;
		    
		    $SspRtbChannelToInsertionOrderPreview->SspPublisherChannelID 			= $ssp_feed_id;
		    $SspRtbChannelToInsertionOrderPreview->SspPublisherChannelDescription 	= $ssp_feed_description;
		    $SspRtbChannelToInsertionOrderPreview->SspExchange						= $ssp_exchange;
		    $SspRtbChannelToInsertionOrderPreview->InsertionOrderPreviewID			= $new_campaign_preview_id;
		    $SspRtbChannelToInsertionOrderPreview->Enabled							= 1;

		    $SspRtbChannelToInsertionOrderPreviewFactory->saveSspRtbChannelToInsertionOrderPreview($SspRtbChannelToInsertionOrderPreview);
		    
	    endforeach;
	    
	    if (!$this->is_super_admin && $this->config_handle['mail']['subscribe']['campaigns'] === true):
	    
		    // if this ad campaign was not created/edited by the admin, then send out a notification email
		    $message = '<b>NginAd Insertion Order Added/Updated by ' . $this->true_user_name . '.</b><br /><br />';
		    $message = $message.'<table border="0" width="10%">';
		    $message = $message.'<tr><td><b>InsertionOrderID: </b></td><td>'.$new_campaign_preview_id.'</td></tr>';
		    $message = $message.'<tr><td><b>UserID: </b></td><td>'.$InsertionOrderPreview->UserID.'</td></tr>';
		    $message = $message.'<tr><td><b>Name: </b></td><td>'.$InsertionOrderPreview->Name.'</td></tr>';
		    $message = $message.'<tr><td><b>StartDate: </b></td><td>'.$InsertionOrderPreview->StartDate.'</td></tr>';
		    $message = $message.'<tr><td><b>EndDate: </b></td><td>'.$InsertionOrderPreview->EndDate.'</td></tr>';
		    $message = $message.'<tr><td><b>Customer: </b></td><td>'.$InsertionOrderPreview->Customer.'</td></tr>';
		    $message = $message.'<tr><td><b>CustomerID: </b></td><td>'.$InsertionOrderPreview->CustomerID.'</td></tr>';
		    $message = $message.'<tr><td><b>MaxImpressions: </b></td><td>'.$InsertionOrderPreview->MaxImpressions.'</td></tr>';
		    $message = $message.'<tr><td><b>MaxSpend: </b></td><td>'.$InsertionOrderPreview->MaxSpend.'</td></tr>';
		    $message = $message.'</table>';
		    	
		    $subject = "NginAd Insertion Order Added/Updated by " . $this->true_user_name;
		    
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
	    
		$refresh_url = "/private-exchange/?ispreview=true";
		$viewModel = new ViewModel(array('refresh_url' => $refresh_url));

		return $viewModel->setTemplate('dashboard-manager/demand/interstitial.phtml');

	}

	/*
	 * END NGINAD InsertionOrder Actions
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
	private function getBreadCrumbInfoFromInsertionOrder($campaign_id, $campaign_preview_id, $is_preview) {

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
				return $this->getBreadCrumbInfoFromInsertionOrderLineItemPreviewID($banner_preview_id);
			else:
				return $this->getBreadCrumbInfoFromInsertionOrderLineItemID($banner_id);
			endif;
	}

	/**
	 * 
	 * @param unknown $id
	 * @return unknown
	 */
	private function getBreadCrumbInfoFromInsertionOrderLineItemID($id) {

		$InsertionOrderLineItemFactory = \_factory\InsertionOrderLineItem::get_instance();
		$params = array();
		$params["InsertionOrderLineItemID"] = $id;

		$InsertionOrderLineItem = $InsertionOrderLineItemFactory->get_row($params);

		$bread_crumb_info = $this->getBreadCrumbInfoFromCampaignID($InsertionOrderLineItem->InsertionOrderID);
		$bread_crumb_info["BCBanner"] = $InsertionOrderLineItem->Name;

		return $bread_crumb_info;

	}

	/**
	 * 
	 * @param unknown $id
	 * @return unknown
	 */
	private function getBreadCrumbInfoFromInsertionOrderLineItemPreviewID($id) {

		$InsertionOrderLineItemPreviewFactory = \_factory\InsertionOrderLineItemPreview::get_instance();
		$params = array();
		$params["InsertionOrderLineItemPreviewID"] = $id;

		$InsertionOrderLineItemPreview = $InsertionOrderLineItemPreviewFactory->get_row($params);

		$bread_crumb_info = $this->getBreadCrumbInfoFromCampaignPreviewID($InsertionOrderLineItemPreview->InsertionOrderPreviewID);
		$bread_crumb_info["BCBanner"] = $InsertionOrderLineItemPreview->Name;

		return $bread_crumb_info;

	}

	/**
	 * 
	 * @param unknown $id
	 * @return multitype:NULL
	 */
	private function getBreadCrumbInfoFromCampaignID($id) {

		$InsertionOrderFactory = \_factory\InsertionOrder::get_instance();
		$params = array();
		$params["InsertionOrderID"] = $id;

		$InsertionOrder = $InsertionOrderFactory->get_row($params);

		return array("BCInsertionOrder"=>'<a href="/private-exchange/viewlineitem/' . $InsertionOrder->InsertionOrderID . '">' . $InsertionOrder->Name . "</a>");

	}

	/**
	 * 
	 * @param unknown $id
	 * @return multitype:NULL
	 */
	private function getBreadCrumbInfoFromCampaignPreviewID($id) {

		$InsertionOrderPreviewFactory = \_factory\InsertionOrderPreview::get_instance();
		$params = array();
		$params["InsertionOrderPreviewID"] = $id;

		$InsertionOrderPreview = $InsertionOrderPreviewFactory->get_row($params);

		return array("BCInsertionOrder"=>'<a href="/private-exchange/viewlineitem/' . $InsertionOrderPreview->InsertionOrderPreviewID . '?ispreview=true">' . $InsertionOrderPreview->Name . "</a>");

	}

	/*
	 * END NGINAD Helper Methods
	*/

}
?>
