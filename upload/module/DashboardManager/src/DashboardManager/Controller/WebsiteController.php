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
		$initialized = $this->initialize();
		if ($initialized !== true) return $initialized;
    	
    	if (!$this->auth->hasIdentity()):
    		return $this->redirect()->toRoute('login');
		elseif (!$this->is_admin):
			return $this->redirect()->toRoute('publisher');
		endif;
	    
	    $PublisherWebsiteFactory = \_factory\PublisherWebsite::get_instance();
	    $PublisherInfoFactory = \_factory\PublisherInfo::get_instance();
	    
	    $orders = 'DateCreated DESC'; 	    
	    $pending_list = $PublisherWebsiteFactory->get(array('ApprovalFlag' => 0), $orders);
	    
	    $view = new ViewModel(array(
	    	'dashboard_view' => 'account',
	    	'pending_list' => $pending_list,
	    	'PublisherInfoFactory' => $PublisherInfoFactory,
	    	'vertical_map' => \util\DeliveryFilterOptions::$vertical_map,
	       	'user_id_list' => $this->user_id_list,
	      	'user_identity' => $this->identity(),
		  	'true_user_name' => $this->auth->getUserName(),
			'header_title' => 'New Websites for Approval',
			'is_admin' => $this->is_admin,
			'effective_id' => $this->auth->getEffectiveIdentityID(),
			'impersonate_id' => $this->ImpersonateID
	    ));
	    
	    return $view;
	}
	
	
	// publishers websites approved/denied by admin
	public function approvedeniedAction() {
		
		$initialized = $this->initialize();
		if ($initialized !== true) return $initialized;
    	
    	if (!$this->auth->hasIdentity()):
    		return $this->redirect()->toRoute('login');
		elseif (!$this->is_admin):
			return $this->redirect()->toRoute('publisher');
		endif;
    	
     	$success = false;
    	$msg = "";
    	
    	$PublisherWebsite = new \model\PublisherWebsite();
    	$PublisherWebsiteFactory = \_factory\PublisherWebsite::get_instance();
    	$PublisherInfoFactory = \_factory\PublisherInfo::get_instance();

    	$request = $this->getRequest();
    	if ($request->isPost()):
	        $q = $request->getPost('q');
	    	$website_ids = $request->getPost('website_ids');
	    	$denied_desciption = $request->getPost('description');
	    	$website_arry = explode(",", $website_ids);
	    	
	    	foreach($website_arry as $website_id):
	    		if($website_id == "d"):
	    			continue;
	    		endif;
	    		
	    		$website_id = intval($website_id);
	    		
	    		$params = array();
	    		$params["PublisherWebsiteID"] = $website_id;
	    		$PublisherWebsite = $PublisherWebsiteFactory->get_row_object($params);
	    		$params = array();
	    		$params["PublisherInfoID"] = $PublisherWebsite->DomainOwnerID;
	    		$publisher_obj = $PublisherInfoFactory->get_row_object($params);
	    		$PublisherWebsite->DateUpdated = date("Y-m-d H:i:s");
	    		if($q == 0):
	    			$PublisherWebsite->AutoApprove = 0;
	    			$PublisherWebsite->ApprovalFlag = 2;
	    			$PublisherWebsite->Description = $denied_desciption;
	    			$PublisherWebsiteFactory->save_domain($PublisherWebsite);
	    			
	    			$PublisherAdZoneFactory = \_factory\PublisherAdZone::get_instance();
	    			
	    			$params = array();
	    			$params["PublisherWebsiteID"] = $website_id;
	    			$PublisherAdZoneList = $PublisherAdZoneFactory->get($params);

	    			foreach ($PublisherAdZoneList as $PublisherAdZone):
	    			
	    				$PublisherAdZoneFactory->updatePublisherAdZonePublisherAdZoneStatus($PublisherAdZone->PublisherAdZoneID, 2);
		    			 
	    			endforeach;

	    			$subject = "Website Denied ".$PublisherWebsite->WebDomain;
	    			$message = '<b>Website Denied</b> : ';
	          		$message = $message." ".$PublisherWebsite->WebDomain;
	          		$message = $message."<p>".$denied_desciption."</p>";
	    			$this->batchmailAction($publisher_obj->Email, $subject, $message);
	    			$msg = "Websites denied successfully. And batch mails goes to publisher.";
	    		elseif($q == 1):
	    			$PublisherWebsite->ApprovalFlag = 1;
	    			$PublisherWebsiteFactory->save_domain($PublisherWebsite);
	    			$subject = "Website Approved ".$PublisherWebsite->WebDomain;
	    			$message = '<b>Website Approved</b> : ';
	          		$message = $message." ".$PublisherWebsite->WebDomain;
	          		$this->batchmailAction($publisher_obj->Email, $subject, $message);
	    			$msg = "Websites approved successfully. And batch mails goes to publisher.";
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
	
		$initialized = $this->initialize();
		if ($initialized !== true) return $initialized;
    	
    	if (!$this->auth->hasIdentity()):
    		return $this->redirect()->toRoute('login');
		elseif (!$this->is_admin):
			return $this->redirect()->toRoute('publisher');
		endif;
    	
		$publisher_id = intval($this->params()->fromRoute('param1', 0));

		$PublisherWebsiteFactory = \_factory\PublisherWebsite::get_instance();
	    $PublisherInfoFactory = \_factory\PublisherInfo::get_instance();
	    
	    $orders = 'DateCreated DESC'; 	    
	    $params = array();
	    $params["DomainOwnerID"] = $publisher_id;
	    $pending_list = $PublisherWebsiteFactory->get($params, $orders);
	    
	    $view = new ViewModel(array(
	    	'dashboard_view' => 'account',
	    	'pending_list' => $pending_list,
	    	'PublisherInfoFactory' => $PublisherInfoFactory,
	    	'vertical_map' => \util\DeliveryFilterOptions::$vertical_map,
	       	'user_id_list' => $this->user_id_list,
	      	'user_identity' => $this->identity(),
		  	'true_user_name' => $this->auth->getUserName(),
			'header_title' => 'Websites',
			'is_admin' => $this->is_admin,
			'effective_id' => $this->auth->getEffectiveIdentityID(),
			'impersonate_id' => $this->ImpersonateID
	    ));
   
	    return $view;
	
	}
}

?>