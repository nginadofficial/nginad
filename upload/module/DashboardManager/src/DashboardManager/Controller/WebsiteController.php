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
use Zend\Session\Container;
use Zend\Mail\Message;
use Zend\Mime;

/**
 * @author Kelvin Mok
 * This is the Website Controller class that controls the management
 * of websites functions.
 */
class WebsiteController extends PublisherAbstractActionController {

    /**
     * Display the Publisher websites index page.
     * 
     * @return \Zend\View\Model\ViewModel
     */
	public function indexAction()
	{	    
	    $auth = $this->getServiceLocator()->get('AuthService');
		if (!$auth->hasIdentity()):
     		return $this->redirect()->toRoute('publisher');
    	endif;
    	
    	$this->initialize();

    	/*$userRole = $this->auth->getRoles();
		$userRole = $userRole[0];
		if ($userRole == 'admin' || $userRole == 'superadmin') :*/
		if ($this->is_admin) :
			
		else:
			$this->redirect()->toRoute('publisher');
		endif;
	    
	    $WebsitesFactory = \_factory\Websites::get_instance();
	    $PublisherInfoFactory = \_factory\PublisherInfo::get_instance();
	    
	    $orders = 'DateCreated DESC'; 	    
	    $pending_list = $WebsitesFactory->get(array('ApprovalFlag' => 0), $orders);
	    
	    $view = new ViewModel(array(
	    	'dashboard_view' => 'account',
	    	'pending_list' => $pending_list,
	    	'PublisherInfoFactory' => $PublisherInfoFactory,
	    	'vertical_map' => \util\DeliveryFilterOptions::$vertical_map,
	    	'user_identity' => $this->identity()
	    ));
	    
	    return $view;
	}
	
	
	// publishers websites approved/denied by admin
	public function approvedeniedAction() {
		
		$auth = $this->getServiceLocator()->get('AuthService');
		if (!$auth->hasIdentity()):
     		return;
    	endif;
    	
    	$this->initialize();
    	
    	/*$userRole = $this->auth->getRoles();
		$userRole = $userRole[0];
		if ($userRole == 'admin' || $userRole == 'superadmin') :*/
		if ($this->is_admin) :
			
		else:
			return;
		endif;
    	
     	$success = false;
    	$msg = "";
    	
    	$Websites = new \model\Websites();
    	$WebsitesFactory = \_factory\Websites::get_instance();
    	$PublisherInfoFactory = \_factory\PublisherInfo::get_instance();

    	$request = $this->getRequest();
    	if ($request->isPost()):
	        $q = $request->getPost('q');
	    	$website_ids = $request->getPost('website_ids');
	    	$denied_desciption = $request->getPost('description');
	    	$website_arry = explode(",", $website_ids);
	    	
	    	foreach($website_arry as $website_id):
	    		if($website_id=="d"):
	    			continue;
	    		endif;
	    		$Websites = $WebsitesFactory->get_row_object(array("WebsiteID" => $website_id));
	    		$publisher_obj = $PublisherInfoFactory->get_row_object(array('PublisherInfoID'=>$Websites->PublisherInfoID));
	    		$Websites->DateUpdated = date("Y-m-d H:i:s");
	    		if($q == 0):
	    			$Websites->ApprovalFlag = 2;
	    			$Websites->Description = $denied_desciption;
	    			$WebsitesFactory->save_website($Websites);
	    			$subject = "Website Apporved ".$Websites->WebDomain;
	    			$message = '<b>Website Approved<b> : ';
	          		$message = $message." ".$Websites->WebDomain;
	          		$message = $message."<p>".$denied_desciption."</p>";
	    			$this->batchmailAction($publisher_obj->Email, $subject, $message);
	    			$msg = "Webistes denied successfully. And batch mails goes to publisher.";
	    		endif;
	    		if($q == 1):
	    			$Websites->ApprovalFlag = 1;
	    			$WebsitesFactory->save_website($Websites);
	    			$subject = "Website Denied ".$Websites->WebDomain;
	    			$message = '<b>Website Denied<b> : ';
	          		$message = $message." ".$Websites->WebDomain;
	          		$this->batchmailAction($publisher_obj->Email, $subject, $message);
	    			$msg = "Webistes approved successfully. And batch mails goes to publisher.";
	    		endif;
	    	endforeach;
	    	$success = true;
	    	
	    endif;	
	    
		$data = array(
		        'success' => $success,
		        'data' => array('msg' => $msg)
	   		 );

        return $this->getResponse()->setContent(json_encode($data));
	}
	
	
	private function batchmailAction($to, $subject, $message) {
		
		$subject = "cdnpal";
		
		 $transport = $this->getServiceLocator()->get('mail.transport');
		
		$text = new Mime\Part($message);
		$text->type = Mime\Mime::TYPE_HTML;
		$text->charset = 'utf-8';
		
		$mimeMessage = new Mime\Message();
		$mimeMessage->setParts(array($text));
		$zf_message = new Message();
		$zf_message->addTo($to)
			->addFrom($this->config_handle['mail']['reply-to']['email'], $this->config_handle['mail']['reply-to']['name'])
			->setSubject($subject)
			->setBody($mimeMessage);
		$transport->send($zf_message);
		
	}
	
	public function listAction() {
	
		$auth = $this->getServiceLocator()->get('AuthService');
		if (!$auth->hasIdentity()):
     		return $this->redirect()->toRoute('publisher');
    	endif;
    	
    	$this->initialize();
    	
    	/*$userRole = $this->auth->getRoles();
		$userRole = $userRole[0];
		if ($userRole == 'admin' || $userRole == 'superadmin') :*/
		if ($this->is_admin) :
			
		else:
			$this->redirect()->toRoute('publisher');
		endif;
		
		$publisher_id = intval($this->params()->fromRoute('param1', 0));
		//echo $publisher_id." nn";
	    //if ($publisher_id > 0):
		
		$WebsitesFactory = \_factory\Websites::get_instance();
	    $PublisherInfoFactory = \_factory\PublisherInfo::get_instance();
	    
	    $orders = 'DateCreated DESC'; 	    
	    $pending_list = $WebsitesFactory->get(array('PublisherInfoID' => $publisher_id), $orders);
	    
	    $view = new ViewModel(array(
	    	'dashboard_view' => 'account',
	    	'pending_list' => $pending_list,
	    	'PublisherInfoFactory' => $PublisherInfoFactory,
	    	'vertical_map' => \util\DeliveryFilterOptions::$vertical_map,
	    	'user_identity' => $this->identity()
	    ));
   
	    return $view;
	
	}
}

?>