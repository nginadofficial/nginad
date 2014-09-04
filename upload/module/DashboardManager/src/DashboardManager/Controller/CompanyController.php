<?php
/**
 * CDNPAL NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2015 CDNPAL Ltd. All Rights Reserved
 * @license GPLv3
 */
namespace DashboardManager\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

/**
 * @author Kelvin Mok
 * This is the Company Controller class that controls the static
 * pages.
 */
class CompanyController extends AbstractActionController {

    /**
     * Display the company index page.
     * 
     * @return \Zend\View\Model\ViewModel
     */
	public function indexAction()
	{	    
	    return array('dashboard_view' => 'company',
	    		'user_identity' => $this->identity(),
	    		'sidebar_active' => 'company'
        );

	}
	
	public function pressAction()
	{	    
	    return array('dashboard_view' => 'company',
	    		'user_identity' => $this->identity(),
	    		'sidebar_active' => 'press'
        );
	}
	
	public function pressinnerAction() {
		return array('dashboard_view' => 'company',
	    		'user_identity' => $this->identity(),
	    		'sidebar_active' => 'press'
        );
	}
	
	public function testimonialsAction()
	{	    
	    return array('dashboard_view' => 'company',
	    		'user_identity' => $this->identity(),
	    		'sidebar_active' => 'testimonials'
        );
	}
	
	public function jobsAction()
	{	    
	  return array('dashboard_view' => 'company',
	    		'user_identity' => $this->identity(),
	    		'sidebar_active' => 'jobs'
        );  
	}
	
	public function jobsinnerAction()
	{	    
	  return array('dashboard_view' => 'company',
	    		'user_identity' => $this->identity(),
	    		'sidebar_active' => 'jobs'
        );  
	}
	
	
	public function jobformAction()
	{	    
	  return array('dashboard_view' => 'company',
	    		'user_identity' => $this->identity(),
	    		'sidebar_active' => 'jobs'
        );  
	}
	
	public function leadershipAction()
	{	    
	    return array('dashboard_view' => 'company',
	    		'user_identity' => $this->identity(),
	    		'sidebar_active' => 'leadership'
        );

	}

}

?>