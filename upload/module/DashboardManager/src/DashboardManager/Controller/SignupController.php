<?php
/**
 * CDNPAL NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2015 CDNPAL Ltd. All Rights Reserved
 * @license GPLv3
 */
namespace DashboardManager\Controller;

use DashboardManager\ParentControllers\PublisherAbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Mail\Message;
use Zend\Mime;

/**
 * @author Kelvin Mok
 * This is the Signup Controller class that controls the management
 * of signup functions.
 */
class SignupController extends PublisherAbstractActionController {

    /**
     * Display the signup index page.
     * 
     * @return \Zend\View\Model\ViewModel
     */
	public function indexAction()
	{	    
	    $auth = $this->getServiceLocator()->get('AuthService');
		if ($auth->hasIdentity()):
     		return $this->redirect()->toRoute('publisher');
    	endif;
	    
	    $view = new ViewModel(array(
	    		'dashboard_view' => 'signup',
	    		'vertical_map' => \util\DeliveryFilterOptions::$vertical_map
	    ));
	    
	    return $view;
	}
	
	public function customerAction()
	{	    
		$auth = $this->getServiceLocator()->get('AuthService');
		$config = $this->getServiceLocator()->get('Config');
		if ($auth->hasIdentity()):
     		return $this->redirect()->toRoute('publisher');
    	endif;
    	
    	$error_msg = null;
		$success_msg = null;
    	$request = $this->getRequest();
		if ($request->isPost()):
			$Name	     = $request->getPost('customer_name');
			$Email		 = $request->getPost('email');
			$Website	 = $request->getPost('website');
			$Company	 = $request->getPost('company');
			$PartnerType = $request->getPost('partner_type');
			$Password	 = $request->getPost('password');
			$user_login	 = $request->getPost('user_login');
			
			$DemandCustomerInfo = new \model\DemandCustomerInfo();
			$DemandCustomerInfoFactory = \_factory\DemandCustomerInfo::get_instance();
			
			$DemandCustomerInfo->Name		    = 	$Name;
			$DemandCustomerInfo->Email			=	$Email;
			$DemandCustomerInfo->Website		=	$Website;
			$DemandCustomerInfo->Company		=	$Company;
			$DemandCustomerInfo->PartnerType	=	$PartnerType;
			$DemandCustomerInfo->DateCreated	=	date("Y-m-d H:i:s");
			
			$authUsers = new \model\authUsers();
			$authUsersFactory = \_factory\authUsers::get_instance();

			
			
			// Check if an entry exists with the same name. A NULL means there is no duplicate.
		    if ($DemandCustomerInfoFactory->get_row(array("Email" => $DemandCustomerInfo->Email)) === null && $authUsersFactory->get_row(array("user_login" => $user_login)) === null):

				$lastInsertID = $DemandCustomerInfoFactory->saveCustomerInfo($DemandCustomerInfo);
				
				$authUsers->DemandCustomerInfoID  = $lastInsertID;
				$authUsers->user_login		      = $user_login;
				$authUsers->user_email		      = $Email;
				$authUsers->user_password	      = \util\Password::md5_split_salt($Password);
				$authUsers->user_role		      = 3; //role as member
				$authUsers->user_enabled	      = 0; 
				$authUsers->user_verified         = 0; 
				$authUsers->create_date	   	      = date("Y-m-d H:i:s");
				
				$authUsersFactory->saveUser($authUsers);
				
				$partner_type = \util\DeliveryFilterOptions::$partner_type;

				$message = '<b>New Demand Customer Registered.</b><br /><br />';
				$message = $message.'<table border="0" width="10%">';
				$message = $message.'<tr><td><b>Name: </b></td><td>'.$Name.'</td></tr>';
				$message = $message.'<tr><td><b>Email: </b></td><td>'.$Email.'</td></tr>';
				$message = $message.'<tr><td><b>Website: </b></td><td>'.$Website.'</td></tr>';
				$message = $message.'<tr><td><b>Company: </b></td><td>'.$Company.'</td></tr>';
				$message = $message.'<tr><td><b>Partner Type: </b></td><td>'.$partner_type[$PartnerType].'</td></tr>';
				$message = $message.'</table>';
				
				$subject = "Duplicate Account Attempt";
				
				$transport = $this->getServiceLocator()->get('mail.transport');
				
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
				$transport->send($zf_message);
				
				$success_msg = 1;
			else:
				$error_msg = "ERROR: A duplicate Account may exist. Please try another.";
			endif;
		endif;
    	
	    
	    $view = new ViewModel(array(
	    		'dashboard_view' => 'signup',
	    		'error_msg' => $error_msg,
	    		'success_msg' => $success_msg,
	    		'partner_type' => \util\DeliveryFilterOptions::$partner_type
	    ));
	    
	    return $view;
	}
	
	
	public function newuserAction() {
		
		$request = $this->getRequest();
		if (!$request->isPost()):
			 return $this->redirect()->toRoute('signup');
		endif;
		
		$Name	     = $request->getPost('Name');
		$Email		 = $request->getPost('Email');
		$Domain		 = $request->getPost('Domain');
		$IABCategory = $request->getPost('IABCategory');
		$Password	 = $request->getPost('Password');
		$user_login	 = $request->getPost('user_login');
		
		$PublisherInfo = new \model\PublisherInfo();
		$PublisherInfoFactory = \_factory\PublisherInfo::get_instance();
		
		$PublisherInfo->Name		    = 	$Name;
		$PublisherInfo->Email			=	$Email;
		$PublisherInfo->Domain			=	$Domain;
		$PublisherInfo->IABCategory		=	$IABCategory;
		$PublisherInfo->DateCreated		=	date("Y-m-d H:i:s");
		
		$error_msg = null;
		$success_msg = null;
		
		
		$authUsers = new \model\authUsers();
		$authUsersFactory = \_factory\authUsers::get_instance();

		
		// Check if an entry exists with the same name. A NULL means there is no duplicate.
	    if ($PublisherInfoFactory->get_row(array("Email" => $PublisherInfo->Email)) === null && $authUsersFactory->get_row(array("user_login" => $user_login)) === null):
	            
			$lastInsertID = $PublisherInfoFactory->savePublisherInfo($PublisherInfo);
			
			$authUsers->PublisherInfoID  = $lastInsertID;
			$authUsers->user_login		 = $user_login;
			$authUsers->user_email		 = $Email;
			$authUsers->user_password	 = \util\Password::md5_split_salt($Password);
			$authUsers->user_role		 = 3; //role as member
			$authUsers->user_enabled     = 0; 
			$authUsers->user_verified    = 0;
			$authUsers->create_date	   	 = date("Y-m-d H:i:s");
			
			$authUsersFactory->saveUser($authUsers);
			$success_msg = 1;
		else:
			$error_msg = "ERROR: A duplicate Account may exist. Please try another.";
		endif;
		
		$view = new ViewModel(array(
	    		'error_msg' => $error_msg,
	    		'success_msg' => $success_msg,
	    		'vertical_map' => \util\DeliveryFilterOptions::$vertical_map,
	    		'dashboard_view' => 'signup'
	    ));
	    
	    return $view->setTemplate('dashboard-manager/signup/index.phtml');
		
	}
	
	
	// user account view and update
	public function accountAction() {

		 $auth = $this->getServiceLocator()->get('AuthService');
		 if (!$auth->hasIdentity()):
     	 	return $this->redirect()->toRoute('login');
    	 endif;
    	 
		$this->initialize();
		$success_msg = null;
		
		$authUsers = new \model\authUsers();
		$authUsersFactory = \_factory\authUsers::get_instance();
		
		$PublisherInfo = new \model\PublisherInfo();
		$PublisherInfoFactory = \_factory\PublisherInfo::get_instance();
				
		$userData = $authUsersFactory->get_row(array("user_id" => $this->auth->getUserID()));
		$userRole = $this->auth->getRoles();
		$userRole = $userRole[0];

		$request = $this->getRequest();
	    if ($request->isPost()):
	    	$user_id	 = $request->getPost('user_id');
	    	$name	     = $request->getPost('name');
			$description = $request->getPost('description');
			
			if($userRole == 'member'):
				$PublisherInfo = $PublisherInfoFactory->get_row_object(array("PublisherInfoID" => $userData->PublisherInfoID));
				$PublisherInfo->PublisherInfoID = $userData->PublisherInfoID;
				$PublisherInfo->Name		    = $name;
				$PublisherInfo->DateUpdated		= date("Y-m-d H:i:s");
				$PublisherInfoFactory->savePublisherInfo($PublisherInfo);
			endif;
			
			$authUsers = $authUsersFactory->get_row_object(array("user_id" => $this->auth->getUserID()));
			
			$authUsers->user_id 	     = $user_id;
			$authUsers->user_fullname 	 = $name;
			$authUsers->user_description = $description;
			$authUsers->update_date	   	 = date("Y-m-d H:i:s");
			$authUsersFactory->saveUser($authUsers);
			
			$success_msg = 1;
	    endif;
		
		$userData = $authUsersFactory->get_row(array("user_id" => $this->auth->getUserID()));
		$userRole = $this->auth->getRoles();
		$userRole = $userRole[0];
		
		// check if user-role is memeber (publisher)
		if($userRole == 'member'):
			$publisherData = $PublisherInfoFactory->get_row(array("PublisherInfoID" => $userData->PublisherInfoID));
			$userData['user_email'] = $publisherData['Email'];
			$userData['user_fullname'] = $publisherData['Name'];
		endif;
	
		$view = new ViewModel(array(
	    		'dashboard_view' => 'account',
	    		'user_identity' => $this->identity(),
	    		'success_msg' => $success_msg,
	    		'user_tab' => 'profile',
	    		'user_data' => $userData
	    ));
	    
	  return $view->setTemplate('dashboard-manager/auth/account.phtml');
	}
	
	//password update
	public function changepasswordAction() {

		 $auth = $this->getServiceLocator()->get('AuthService');
		 if (!$auth->hasIdentity()):
     	 	return $this->redirect()->toRoute('login');
    	 endif;
    	 
		$this->initialize();
		$success_msg = null;
		$success_msg1 = null;
		$error_msg = null;
		
		$authUsers = new \model\authUsers();
		$authUsersFactory = \_factory\authUsers::get_instance();
		
		$PublisherInfo = new \model\PublisherInfo();
		$PublisherInfoFactory = \_factory\PublisherInfo::get_instance();
				
		$userData = $authUsersFactory->get_row(array("user_id" => $this->auth->getUserID()));
		$userRole = $this->auth->getRoles();
		$userRole = $userRole[0];

		$request = $this->getRequest();
	    if ($request->isPost()):
	    	$user_id	 = $request->getPost('user_id');
	    	$old_password = trim($request->getPost('old_password'));
	    	$password =    trim($request->getPost('password'));

			$authUsers = $authUsersFactory->get_row_object(array("user_id" => $this->auth->getUserID()));
			$authUsers->user_id 	     = $user_id;
			$authUsers->user_password 	 = \util\Password::md5_split_salt($password);
			$authUsers->update_date	   	 = date("Y-m-d H:i:s");
			
			$userData = $authUsersFactory->get_row(array("user_id" => $authUsers->user_id));
			if($userData->user_password == \util\Password::md5_split_salt($old_password)):
				$authUsersFactory->saveUser($authUsers);
			    $success_msg1 = 1;
			else: 
				$error_msg = "Old password is incorrect.";
			endif;
	     endif;
		
		// check if user-role is memeber (publisher)
		if($userRole == 'member'):
			$publisherData = $PublisherInfoFactory->get_row(array("PublisherInfoID" => $userData->PublisherInfoID));
			$userData['user_email'] = $publisherData['Email'];
			$userData['user_fullname'] = $publisherData['Name'];
		endif;
	
		$view = new ViewModel(array(
	    		'dashboard_view' => 'account',
	    		'user_identity' => $this->identity(),
	    		'success_msg' => $success_msg,
	    		'success_msg1' => $success_msg1,
	    		'user_tab' => 'password',
	    		'error_msg' => $error_msg,
	    		'user_data' => $userData
	    ));
	    
	  return $view->setTemplate('dashboard-manager/auth/changepassword.phtml');
	}
	
	public function publishersAction() {
		
		$this->initialize();
		
		$authUsers = new \model\authUsers();
		$authUsersFactory = \_factory\authUsers::get_instance();
		
		$userData = $authUsersFactory->get_row(array("user_id" => $this->auth->getUserID()));
		
		/*$userRole = $this->auth->getRoles();
		$userRole = $userRole[0];
		if ($userRole == 'admin' || $userRole == 'superadmin') :*/
		if ($this->is_admin) :
		
		else:
			$this->redirect()->toRoute('publisher');
		endif;
		
		$PublisherInfo = new \model\PublisherInfo();
		$PublisherInfoFactory = \_factory\PublisherInfo::get_instance();
		
		$orders = 'DateCreated DESC'; 	    
		$userDetail = $PublisherInfoFactory->get(null, $orders);

		$view = new ViewModel(array(
	    	'dashboard_view' => 'account',
	    	'user_identity' => $this->identity(),
	    	'user_detail' => $userDetail,
	    	'authUsersFactory' => $authUsersFactory,
	    	'user_type' => 'publisher',
	    	'user_id' => $this->auth->getUserID()
	    ));
	    
	  return $view->setTemplate('dashboard-manager/auth/userlist.phtml');

	}
	
	public function customersAction() {
		
		$this->initialize();
		
		$authUsers = new \model\authUsers();
		$authUsersFactory = \_factory\authUsers::get_instance();
		
		$userData = $authUsersFactory->get_row(array("user_id" => $this->auth->getUserID()));
		
		/*$userRole = $this->auth->getRoles();
		$userRole = $userRole[0];
		if ($userRole == 'admin' || $userRole == 'superadmin') :*/
		if ($this->is_admin) :
		
		else:
			$this->redirect()->toRoute('publisher');
		endif;
		
		$DemandCustomerInfo = new \model\DemandCustomerInfo();
		$DemandCustomerInfoFactory = \_factory\DemandCustomerInfo::get_instance();
		
		$orders = 'DateCreated DESC'; 	    
		$userDetail = $DemandCustomerInfoFactory->get(null, $orders);

		$view = new ViewModel(array(
	    	'dashboard_view' => 'account',
	    	'user_identity' => $this->identity(),
	    	'user_detail' => $userDetail,
	    	'authUsersFactory' => $authUsersFactory,
	    	'user_type' => 'customer',
	    	'user_id' => $this->auth->getUserID()
	    ));
	    
	  return $view->setTemplate('dashboard-manager/auth/customers.phtml');

	}
	
	
	public function rejectuserAction() {
		
		$this->initialize();
		
		/*$userRole = $this->auth->getRoles();
		$userRole = $userRole[0];
		if ($userRole == 'admin' || $userRole == 'superadmin') :*/
		if ($this->is_admin) :
		
		else:
			$this->redirect()->toRoute('publisher');
		endif;
		
		$request = $this->getRequest();
		if ($request->isPost()):
			$user_id = $request->getPost('user_id');
	    	$description = $request->getPost('description');
	    	$user_type = $request->getPost('user_type');
	    	if($user_type == 'publisher'):
	    		return $this->rejectpublisherAction($user_id, $description, $user_type);
	    	endif;
	    	if($user_type == 'customer'):
	    		return $this->rejectcustomerAction($user_id, $description, $user_type);
	    	endif;
		endif;
	}
	
	public function acceptuserAction() {
		
		$this->initialize();
		
		/*$userRole = $this->auth->getRoles();
		$userRole = $userRole[0];
		if ($userRole == 'admin' || $userRole == 'superadmin') :*/
		if ($this->is_admin) :
		
		else:
			$this->redirect()->toRoute('publisher');
		endif;
		
		$request = $this->getRequest();
		if ($request->isPost()):
			$user_id = $request->getPost('user_id');
	    	$user_type = $request->getPost('user_type');
	    	if($user_type == 'publisher'):
	    		return $this->acceptpublisherAction($user_id, $user_type);
	    	endif;
	    	if($user_type == 'customer'):
	    		return $this->acceptcustomerAction($user_id, $user_type);
	    	endif;
		endif;
	}
	
	
	public function rejectpublisherAction($publisher_id, $description, $user_type) {

		$this->initialize();
		
		/*$userRole = $this->auth->getRoles();
		$userRole = $userRole[0];
		if ($userRole == 'admin' || $userRole == 'superadmin') :*/
		if ($this->is_admin) :
		
		else:
			$this->redirect()->toRoute('publisher');
		endif;
		
		$msg = null;
		$success = false;
		$PublisherInfoFactory = \_factory\PublisherInfo::get_instance();
		
			$publisher_obj = $PublisherInfoFactory->get_row_object(array('PublisherInfoID'=>$publisher_id));
	        $bol = $this->userApprovalToggle(0, $publisher_id, $user_type);
	        if($bol == true):
	          
	          $message = '<b>Publisher Rejected.<b><br />';
	          $message = $message.$description;

			  $subject = "Publisher rejected.";
			  
			  $transport = $this->getServiceLocator()->get('mail.transport');
			  
			  $text = new Mime\Part($message);
			  $text->type = Mime\Mime::TYPE_HTML;
			  $text->charset = 'utf-8';
			  
			  $mimeMessage = new Mime\Message();
			  $mimeMessage->setParts(array($text));
			  $zf_message = new Message();
			  $zf_message->addTo($publisher_obj->Email)
				  ->addFrom($this->config_handle['mail']['reply-to']['email'], $this->config_handle['mail']['reply-to']['name'])
				  ->setSubject($subject)
				  ->setBody($mimeMessage);
			  $transport->send($zf_message);
			  
	          $success = true;
	          $msg = 'Publisher rejected. Email sent successfully.';
	       endif;
        
        //endif;
		
		$data = array(
	        'success' => $success,
	        'data' => array('msg' => $msg)
   		 );

        return $this->getResponse()->setContent(json_encode($data));
	}
	
	
	public function acceptpublisherAction($publisher_id, $user_type) {

		$this->initialize();
		
		if (!$this->is_admin) :
			$this->redirect()->toRoute('publisher');
		endif;
		
		$msg = null;
		$success = false;
		$PublisherInfoFactory = \_factory\PublisherInfo::get_instance();
		
	        $bol = $this->userApprovalToggle(1, $publisher_id, $user_type);
	        
	        if($bol == true):
	          $publisher_obj = $PublisherInfoFactory->get_row_object(array('PublisherInfoID'=>$publisher_id));
	          
				$message = 'Publisher approved.';
	
				$subject = "Publisher approved.";
					
				$transport = $this->getServiceLocator()->get('mail.transport');
					
				$text = new Mime\Part($message);
				$text->type = Mime\Mime::TYPE_HTML;
				$text->charset = 'utf-8';
					
				$mimeMessage = new Mime\Message();
				$mimeMessage->setParts(array($text));
				$zf_message = new Message();
				$zf_message->addTo($publisher_obj->Email)
					->addFrom($this->config_handle['mail']['reply-to']['email'], $this->config_handle['mail']['reply-to']['name'])
					->setSubject($subject)
					->setBody($mimeMessage);
				$transport->send($zf_message);
				
		        $success = true;
		        $msg = 'Publisher approved. Email sent successfully.';
	       endif;
        
        //endif;
		
		$data = array(
	        'success' => $success,
	        'data' => array('msg' => $msg)
   		 );

        return $this->getResponse()->setContent(json_encode($data));
	}
	
	
	public function rejectcustomerAction($customer_id, $description, $user_type) {

		$this->initialize();
		
		if (!$this->is_admin) :
			$this->redirect()->toRoute('publisher');
		endif;
		
		$msg = null;
		$success = false;
		$DemandCustomerFactory = \_factory\DemandCustomerInfo::get_instance();
		
			$customer_obj = $DemandCustomerFactory->get_row_object(array('DemandCustomerInfoID'=>$customer_id));
	        $bol = $this->userApprovalToggle(0, $customer_id, $user_type);
	        if($bol == true):
	          
	          $message = '<b>Customer Rejected.<b><br />';
	          $message = $message.$description;

			  $subject = "Customer rejected.";
			  
			  $transport = $this->getServiceLocator()->get('mail.transport');
			  
			  $text = new Mime\Part($message);
			  $text->type = Mime\Mime::TYPE_HTML;
			  $text->charset = 'utf-8';
			  
			  $mimeMessage = new Mime\Message();
			  $mimeMessage->setParts(array($text));
			  $zf_message = new Message();
			  $zf_message->addTo($customer_obj->Email)
				  ->addFrom($this->config_handle['mail']['reply-to']['email'], $this->config_handle['mail']['reply-to']['name'])
				  ->setSubject($subject)
				  ->setBody($mimeMessage);
			  $transport->send($zf_message);
			  
	          $success = true;
	          $msg = 'Customer rejected. Email sent successfully.';
	       endif;
        
        //endif;
		
		$data = array(
	        'success' => $success,
	        'data' => array('msg' => $msg)
   		 );

        return $this->getResponse()->setContent(json_encode($data));
	}
	
	
	public function acceptcustomerAction($customer_id, $user_type) {

		$this->initialize();
		
		if (!$this->is_admin) :
			$this->redirect()->toRoute('publisher');
		endif;
		
		$msg = null;
		$success = false;
		$DemandCustomerFactory = \_factory\DemandCustomerInfo::get_instance();
		
	        $bol = $this->userApprovalToggle(1, $customer_id, $user_type);
	        
	        if($bol == true):
	           $customer_obj = $DemandCustomerFactory->get_row_object(array('DemandCustomerInfoID'=>$customer_id));
	          
				$message = 'Customer approved.';
	
				$subject = "Customer approved.";
					
				$transport = $this->getServiceLocator()->get('mail.transport');
					
				$text = new Mime\Part($message);
				$text->type = Mime\Mime::TYPE_HTML;
				$text->charset = 'utf-8';
					
				$mimeMessage = new Mime\Message();
				$mimeMessage->setParts(array($text));
				$zf_message = new Message();
				$zf_message->addTo($customer_obj->Email)
					->addFrom($this->config_handle['mail']['reply-to']['email'], $this->config_handle['mail']['reply-to']['name'])
					->setSubject($subject)
					->setBody($mimeMessage);
				$transport->send($zf_message);
				
		        $success = true;
		        $msg = 'Customer approved. Email sent successfully.';
	       endif;
        
        //endif;
		
		$data = array(
	        'success' => $success,
	        'data' => array('msg' => $msg)
   		 );

        return $this->getResponse()->setContent(json_encode($data));
	}
	
	
	
	
	private function userApprovalToggle($flag, $user_id, $user_type)
	{
	    	         
	    $this->initialize();
	
	    if ($this->is_admin && $user_id > 0 && ($flag === 1 || $flag === 0)):
	    
	    	$authUsers = new \model\authUsers();
			$authUsersFactory = \_factory\authUsers::get_instance();
			
			if($user_type=='publisher'):
				$PublisherInfoFactory = \_factory\PublisherInfo::get_instance();
				$authUsers = $authUsersFactory->get_row_object(array("PublisherInfoID" => $user_id));
	        endif;
	        if($user_type=='customer'):
				$DemandCustomerFactory = \_factory\DemandCustomerInfo::get_instance();
				$authUsers = $authUsersFactory->get_row_object(array("DemandCustomerInfoID" => $user_id));
	        endif;
		    
	   		
	   		$authUsers->user_enabled = $flag;
    		$authUsers->user_verified = $flag;
	    		
    		if($flag === 1):
    			$authUsersFactory->saveUser($authUsers);
    			return true;
    		endif;
    		if($flag === 0):
    		  if($user_type=='publisher'):
    			$PublisherInfoFactory->deletePublisherInfo($user_id);
    		  endif;
    		  if($user_type=='customer'):
    			$DemandCustomerFactory->deleteCustomerInfo($user_id);
    		  endif;		
    		    $authUsersFactory->delete_user($authUsers->user_id);
    			return true;
    		endif;
    	endif;	
	    return false;
	}
	
	
	public function websitesAction() {
		
		$auth = $this->getServiceLocator()->get('AuthService');
		if (!$auth->hasIdentity()):
     		return $this->redirect()->toRoute('login');
    	endif;
		
		$this->initialize();
		
		$error_msg = null;
		$success_msg = null;
		$PublisherWebsite = new \model\PublisherWebsite();
		$PublisherWebsiteFactory = \_factory\PublisherWebsite::get_instance();
			
		
		$request = $this->getRequest();
	    if ($request->isPost()):
	        
	    	$website = $request->getPost('website');
	    	$category = $request->getPost('category');
	    	$PublisherWebsite->WebDomain = $website;
	    	$PublisherWebsite->IABCategory = $category;
	    	$PublisherWebsite->DomainOwnerID = $this->auth->getEffectiveIdentityID();
	    	$PublisherWebsite->DateCreated = date("Y-m-d H:i:s");
	    	$PublisherWebsite->Description = "";
	    	
	    	$auto_approve_websites = $this->config_handle['settings']['publisher']['auto_approve_websites'];
	    	
	    	$PublisherWebsite->AutoApprove = ($auto_approve_websites == true) ? 1 : 0;

	    	// Disapprove the changes if not admin.
	    	if ($this->is_admin || $auto_approve_websites == true):
	    		$PublisherWebsite->ApprovalFlag = 1;
	    	else:
	    		$PublisherWebsite->ApprovalFlag = 0;
	    	endif;
	    	
	    	$PublisherWebsite->IABCategory = $category;
	    	
	    	$params = array();
	    	$params["WebDomain"] = $PublisherWebsite->WebDomain;
	    	$params["DomainOwnerID"] = $this->auth->getEffectiveIdentityID();
	    	
	    	if ($PublisherWebsiteFactory->get_row($params) === null):
	    	  	$PublisherWebsiteFactory->save_domain($PublisherWebsite);
	    	  	$message = "New website for approval.<br /><b>".$website."</b>";
				
				$subject = "New website for approval";
				
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
				
		  		$success_msg = 1;
		  	else:
		  		$error_msg = '"' . $website . '" duplicate entry.';
		  	endif;	
		endif;
		
    
	    $pending_list = $PublisherWebsiteFactory->get(array('PublisherInfoID' => $this->auth->getEffectiveIdentityID(), 'ApprovalFlag' => 0));
	    $approved_list = $PublisherWebsiteFactory->get(array('PublisherInfoID' => $this->auth->getEffectiveIdentityID(), 'ApprovalFlag' => 1));
	    $denied_list = $PublisherWebsiteFactory->get(array('PublisherInfoID' => $this->auth->getEffectiveIdentityID(), 'ApprovalFlag' => 2));
		
		$view = new ViewModel(array(
	    	'dashboard_view' => 'account',
	    	'pending_list' => $pending_list,
	    	'approved_list' => $approved_list,
	    	'denied_list' => $denied_list,
	    	'user_identity' => $this->identity(),
	    	'success_msg' => $success_msg,
	    	'error_msg' => $error_msg,
	    	'vertical_map' => \util\DeliveryFilterOptions::$vertical_map,
	    	'user_id' => $this->auth->getUserID()
	    ));
	    
	    return $view;
	}
	
	
	public function deletewebsiteAction() {
		
		$this->initialize();
		
		$auth = $this->getServiceLocator()->get('AuthService');
		if (!$auth->hasIdentity()):
     		return $this->redirect()->toRoute('login');
    	endif;
		
		$success = false;
		$msg = null;
		$PublisherWebsiteFactory = \_factory\PublisherWebsite::get_instance();
		$PublisherAdZoneFactory = \_factory\PublisherAdZone::get_instance();
		
		$request = $this->getRequest();
	    if ($request->isPost()):
	        
	    	$website_id = $request->getPost('website_id');
	    
	    	$params = array();
	    	$params["PublisherWebsiteID"] 	= $website_id;
	    	$params["AdOwnerID"] 			= $this->auth->getEffectiveIdentityID();
	    	$publisher_website_data = $PublisherWebsiteFactory->get_row($params);
	    	if($publisher_website_data->ApprovalFlag == 0):
	    		$success = true;
	    		$PublisherWebsiteFactory->delete_website($website_id);
	    		
	    		$params = array();
	    		$params['PublisherWebsiteID'] = $website_id;
	    		$PublisherAdZoneList = $PublisherAdZoneFactory->get($params);
	    		
	    		foreach ($PublisherAdZoneList as $PublisherAdZone):
	    			
	    			$PublisherAdZoneFactory->delete_zone($PublisherAdZone->PublisherAdZoneID);
	    			
	    		endforeach;
	    		
	    		$msg = '"' . $website_data->WebDomain . '" removed successfully.';
	    	else:
	    		$msg = '"' . $website_data->WebDomain . '" in progress. please refresh page.';
	    	endif;
	    	
	    endif;	
		
		$data = array(
	        'success' => $success,
	        'data' => array('msg' => $msg)
   		 );

        return $this->getResponse()->setContent(json_encode($data));
	} 
	
	
}

?>