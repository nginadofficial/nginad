<?php
/**
 * CDNPAL NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2015 CDNPAL Ltd. All Rights Reserved
 * @license GPLv3
 */
namespace auth;

use Zend\Authentication\Storage\Session;
use Zend\Authentication\Storage\StorageInterface;

/**
 * User Authentication Storage classes, a rebuild of the old MyAuthStorage class
 * with the necessary narrower class calls to implement the interfaces from
 * ZfcRbac\Identity\IdentityInterface.
 * 
 * @author Kelvin Mok, Christopher Gu
 *
 */
class UserAuthenticationStorage extends Session implements StorageInterface
{
    /**
     * 
     * @var UserIdentity the container holding the current session identity.
     */
    protected $container;
    
    /**
     * Write the in memory identity object into the session storage.
     * 
     * @return \auth\UserIdentity returns the saved identity object.
     */
    public function save()
    {
        if (!isset($this->container)):
        
            $this->getContainer();
        endif;
        
        $this->write($this->container);
        
        return $this->container;
    }
    
    /**
     * Load the identity object saved in session storage into the memory.
     * 
     * @param array $configuration The array or its handle of the system configuration loaded. 
     * @return \Zend\Authentication\Storage\mixed The object stored in session storage. Should be \auth\UserIdentity.
     */
    public function load($configuration = null)
    {

        $IdentityContainer = $this->read();
        
        if ($configuration !== null && $IdentityContainer !== null):
        
           $IdentityContainer->setConfig($configuration);
        endif;
        
        $this->container = $IdentityContainer;
        
        return $IdentityContainer;
    }
    
    /**
     * Set the UserIdentity object in memory.
     * 
     * @param UserIdentity $container
     * @return UserIdentity
     */
    public function setContainer(UserIdentity $container) {
    	$this->container = $container;
    	return $this->container;
    }
    
    /**
     * Get the User Identity object in memory.
     * 
     * @return UserIdentity
     */
    public function getContainer() {
    	if (!isset($this->container)): 
    	    if ($this->load(null) == null):
    	    
    		  $this->setContainer(new UserIdentity('authAuthStorage'));
    	    endif;
    	endif;
    	return $this->container;
    }
    
    /**
     * Places a cookie to hold onto the credentials between browser sessions
     * for the specified limited amount of time.
     * 
     * @param number $rememberMe Default value is 0
     * @param number $time Default value is 1209600
     */
    public function setRememberMe($rememberMe = 0, $time = 1209600)
    {
    	if ($rememberMe == 1): 
    		$this->session->getManager()->rememberMe($time);
    	endif;
    }
    
    /**
     * Log out user and reverse the cookie holding onto the credentials
     * between browser sessions.
     */
    public function forgetMe()
    {
    	$this->session->getManager()->forgetMe();
    }
}
?>
