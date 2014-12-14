<?php

namespace DashboardManager\ParentControllers;

use Zend\Mvc\Controller\AbstractActionController as ZendAbstractActionController;

/**
 * @author Kelvin Mok
 * This class puts in common data structures for all DashboardManager action controller classes.
 * This class allows for easy extension of things repeatable across all action controllers.
 */
abstract class DashboardAbstractActionController extends ZendAbstractActionController
{
    
    // These variables are used to store authentication information regarding current user.
    /**
     * 
     * @var \auth\UserIdentityProvider
     */
    protected $auth = null;
    
    /**
     * 
     * @var array
     */
    protected $config_handle = null;
    
    /**
     * Is the logged in (actual/real) user an admin?
     * @var boolean
     */
    protected $is_admin = false;
    
    /**
     * 
     * @var array
     */
    protected $user_id_list = null;
    
    /**
     *
     * @var array
     */
    protected $dashboard_home = "publisher";
    
    /**
     *
     * @var array
     */
    protected $user_id_list_publisher = null;
    
    /**
     *
     * @var array
     */
    protected $user_id_list_demand_customer = null;
    
    /**
     * User's Effective ID, impersonated or real. This ID is whichever is active.
     * @var integer
     */
    protected $EffectiveID = null;
    
    /**
     * The currently impersonated ID. NULL if not impersonating a user.
     * @var integer
     */
    protected $ImpersonateID = null;
    
    /**
     * User's Demand Info ID
     * @var integer
     */
    protected $DemandCustomerInfoID = null;
    
    /**
     * User's Publisher Info ID
     * @var integer
     */
    protected $PublisherInfoID = null;
    
    /**
     * The real name of the user logged in to the system. NOT the impersonated user.
     * @var string
     */
    protected $true_user_name = "";
    
    /**
     * System Debug Flag
     * @var boolean
     */
    protected $debug = false;
    
    /**
     * System Verbose Debug Flag
     * @var boolean
     */
    protected $debug_verbose = false;
    
    /**
     * Return the configured debugging flag of this Action Controller.
     * @return boolean
     */
    public function get_debug()
    {
        return $this->debug;
    }
    
    /**
     * Return the configured debug verbosity flag of this Action Controller.
     * @return boolean
     */
    public function get_debug_verbose()
    {
        return $this->debug_verbose;
    }
    
    /**
     * Set and update common user status function that must be loaded after each Controller Action.
     */
    protected function initialize()
    {
        // The getServiceLocator() will not be populated until just after the action function is called.
        // As such, you can't put it in the __construct(), and preDispatch() is not working. Do not know why.
    	$this->auth = $this->getServiceLocator()->get('AuthService');
    	$this->config_handle = $this->getServiceLocator()->get('Config');

    	if (!$this->auth->hasIdentity()):
     		return $this->redirect()->toRoute('login');
    	endif;
    	
    	$this->is_admin = false;
    	$this->user_id_list = $this->user_id_list_publisher = $this->user_id_list_demand_customer = array();
    	$this->PublisherInfoID = $this->auth->getPublisherInfoID();
    	$this->DemandCustomerInfoID = $this->auth->getDemandCustomerInfoID();
    	$this->EffectiveID = $this->auth->getEffectiveUserID();
    	$this->true_user_name = $this->auth->getUserName();
    	$this->ImpersonateID = $this->auth->getImpersonatedIdentityID();
    	$this->debug = $this->config_handle['system']['debug'];
    	$this->debug_verbose = $this->config_handle['system']['debug_verbose'];

    	// If Administrator, populate and set adminitrator options.
    	if (strpos($this->auth->getPrimaryRole(), $this->config_handle['roles']['admin']) !== false):

            $this->is_admin = true;
    		// Get a list of all members.
    		$auth_Users_list = $this->auth->getRoleUsers($this->config_handle['roles']['member']);
    		 
    		foreach ($auth_Users_list as $auth_User):

    			// skip disabled users
    			if ($auth_User->user_enabled != 1):
    				continue;
    			endif;	
    		
    			$this->user_id_list[$auth_User->user_id] = $auth_User->user_login;
    			
				if ($auth_User->PublisherInfoID != null):
					
					$this->user_id_list_publisher[$auth_User->user_id] = $auth_User->user_login;
					
					if ($auth_User->user_id == $this->EffectiveID):
						$this->PublisherInfoID = intval($auth_User->PublisherInfoID);
					endif;
				
				endif;
    		
				if ($auth_User->DemandCustomerInfoID != null):
					
					$this->user_id_list_demand_customer[$auth_User->user_id] = $auth_User->user_login;
					
					if ($auth_User->user_id == $this->EffectiveID):
						$this->DemandCustomerInfoID = intval($auth_User->DemandCustomerInfoID);
					endif;	
				
				endif;

    		endforeach;
    		
    	endif;

    	if ($this->PublisherInfoID != null):
    		$this->user_id_list = $this->user_id_list_publisher;
    		$this->dashboard_home = "publisher";
    	elseif($this->DemandCustomerInfoID != null):
    		$this->user_id_list = $this->user_id_list_demand_customer;
    		$this->dashboard_home = "demand";
    	endif;
    	
    	natcasesort($this->user_id_list_publisher);
    	natcasesort($this->user_id_list_demand_customer);
    	
    	return true;
    	 
    }
    
    
    /**
     * Format the query list to only contain data to be displayed, and in the order specified.
     * The function will only act on the $meta_data and $list_data input. The $headers is provided
     * as a convenience to pass variables around.
     *
     * @param array $meta_data A (number) ordered array of database field values to sort the list.
     * @param array $list_data A two dimensional array for rows of data.
     * @param array $headers An optional array of table header names to display in order.
     * @return array A multidimentional array of "meta" for the meta_data input and "data" for the data sorted according to the order of the metadata.
     */
    protected function order_data_table(array $meta_data, array $list_data, array $headers = null)
    {
    	if ($headers === null):
    	
    		$headers = array();
    	endif;
    	$return_object = array(
    			"meta" => $meta_data,
    			"meta_flip" => array_flip($meta_data),
    			"data" => array(),
    			"header" => $headers,
    			"header_flip" => array_flip($headers),
    	);
    	$formatted_output = array();
    	$sorted_row = array();
    	$rowdata = null;
    
    	if (count($list_data) <= 0):
    	
    		// No data, return empty data array.
    		return $return_object;
    	endif;
    
    	foreach ($list_data as $row_key => $row_data):
    	
    		for($counter = 0; $counter < count($meta_data); $counter++):
    			if (!isset($row_data[$meta_data[$counter]])):
    				$sorted_row[$counter] = null;
    			else:
    				$sorted_row[$counter] = $row_data[$meta_data[$counter]];
    			endif;
    		endfor;
    		$formatted_output[$row_key] = $sorted_row; // This is done for logic clarity, not code compactness.
    	endforeach;
    
    	$return_object["data"] = $formatted_output;
    
    	return $return_object;
    }
    

    /**
     * Validate the existence of all required fields in a submission form.
     * You must specify $legacy_style as FALSE explicitly to not use the die() function on failure.
     * 
     * @param array $needed_input An array of input that is required in the submission form.
     * @param boolean $legacy_style If TRUE, this function will behave in a legacy die() fashion. If FALSE, error messages are logged.
     * @param string $error_msg A string of the last field that did not pass basic validation.
     * @return boolean Returns TRUE if all required fields are present. FALSE if any one is missing.
     */
   	protected function validateInput(array $needed_input, $legacy_style = true, &$error_msg = null)
   	{
   	    $return_flag = true;
   	
   		foreach ($needed_input as $form_field):
   		
   	
       		if ($this->getRequest()->getPost($form_field) == null || $this->getRequest()->getPost($form_field) == ""):
       		
                if ($legacy_style):
                
           		   die("Required Field: " . $form_field . " was missing");
                
           		else:
           		
           		    $error_msg = "Required Field: " . $form_field . " was missing";
           		    $return_flag = false;
           		endif;
       		endif;
   	
   		endforeach;
   		
   		return $return_flag;
   	
   	}
   	
   	/**
   	 * Perform impersonation.
   	 * 
   	 * @return boolean TRUE if success, FALSE otherwise.
   	 */
   	protected function ImpersonateUser()
   	{
   	    $this->initialize();
   	    if ($this->is_admin):
   	    
   	    	// TODO: Hummm... ANY admin can login as ANY OTHER admin, even if the pull down does not allow.
   	    	$this->auth->impersonateUserID($this->getRequest()->getQuery('userid'));
   	        return TRUE;
   	    endif;
   	    
   	    // If debugging and user has no permission, then die to say so.
   	    if ($this->debug && !$this->is_admin):
   	    
   	    	die("You do not have permission to access this page");
   	    endif;
   	    return FALSE;
   	}
   	
}
?>