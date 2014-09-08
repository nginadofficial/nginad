<?php

namespace DashboardManager\ParentControllers;

abstract class DemandAbstractActionController extends DashboardAbstractActionController 
{

	protected $preview_query;
    
    /**
     * Set and update common user status function that must be loaded after each Controller Action.
     */
    protected function initialize()
    {
    	
    	$is_preview = $this->getRequest()->getQuery('ispreview');
    	$this->preview_query = $is_preview == true ? "?ispreview=true" : "";
    	
    	if($this->DemandCustomerInfoID != null):
	    	$this->dashboard_home = "demand";
    	endif;
    	
		parent::initialize();

		/*
		 * ADD STUFF HERE
		 */
    	 
    }
}

?>