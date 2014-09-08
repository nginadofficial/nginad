<?php 
/**
 * CDNPAL NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2015 CDNPAL Ltd. All Rights Reserved
 * @license GPLv3
 */
namespace DashboardManager\ParentControllers;

/**
 * @author Kelvin Mok
 * This class puts in common data structures for all DashboardManager action controller classes.
 * This class allows for easy extension of things repeatable across all action controllers.
 */
abstract class PublisherAbstractActionController extends DashboardAbstractActionController
{
    
    /**
     * Set and update common user status function that must be loaded after each Controller Action.
     */
    protected function initialize()
    {
    	
    	if ($this->PublisherInfoID != null):
	    	$this->dashboard_home = "publisher";
    	endif;
		parent::initialize();

		/*
		 * ADD STUFF HERE
		*/
		
    }
}
?>