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
		
		$NativeAdFactory = \_factory\NativeAd::get_instance();
		
		$params = array();
	    if ($this->is_domain_admin):
	    	$params["UserID"] = $this->auth->getUserID();
	    else:
	    	$params["UserID"] = $this->auth->getEffectiveUserID();
		endif;
		$NativeAdList = $NativeAdFactory->get($params);
		
		foreach ($NativeAdList as $NativeAd):
			$ad_media_list[] = $NativeAd;
		endforeach;

	    $view = new ViewModel(array(
	    		'is_super_admin' => $this->auth->isSuperAdmin($this->config_handle),
	    		'is_domain_admin' => $this->auth->isDomainAdmin($this->config_handle),
	    		'user_id_list' => $this->user_id_list_demand_customer,
	    		'effective_id' => $this->auth->getEffectiveIdentityID(),
	    		'dashboard_view' => 'private-exchange',
	    		'user_identity' => $this->identity(),
	    		'true_user_name' => $this->auth->getUserName(),
				'is_super_admin' => $this->is_super_admin,
				'effective_id' => $this->auth->getEffectiveIdentityID(),
				'impersonate_id' => $this->ImpersonateID,
	    		'ad_media_list' => $ad_media_list,

	    ));
	    
	    return $view;
	}

}
