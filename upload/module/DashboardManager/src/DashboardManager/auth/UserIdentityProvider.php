<?php
/**
 * CDNPAL NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2015 CDNPAL Ltd. All Rights Reserved
 * @license GPLv3
 */
namespace auth;

use Zend\Authentication\AuthenticationService;
use ZfcRbac\Identity\IdentityProviderInterface;
use Zend\Authentication\Adapter;
use Zend\Authentication\Storage;
use DashboardManager\Exception;
use stdClass;
/**
 * User Identity Provider for project NGINAD.
 * This identity provider includes the necessary custom overrides
 * from the default Zend\Authentication\AuthenticationService libraries
 * as appropriate to implement the necessary ZfcRbac interfaces cleanly.
 * 
 * @author Kelvin Mok
 *
 */
class UserIdentityProvider extends AuthenticationService implements IdentityProviderInterface 
{

    private $ConfigHandle;
    
    /**
     * Constructor for the Identity Provider, with custom overrides
     * for the default Zend\Authentication\AuthenticationService class as
     * approriate to implement ZfcRbac interfaces.
     * 
     * This Identity Provider only supports the authentication via a DB Table
     * through Zend\Authentication\Adapter\DbTable.
     * 
     * @param UserAuthenticationStorage $storage
     * @param Zend\Authentication\Adapter\DbTable $adapter
     */
    public function __construct(UserAuthenticationStorage $storage = null, Adapter\DbTable $adapter = null)
    {
    	if (null !== $storage): 
    		$this->setStorage($storage);
    	endif;
    	if (null !== $adapter): 
    		$this->setAdapter($adapter);
    	endif;
    }
    
    /**
     * Returns the persistent storage handler
     *
     * Session storage is used by default unless a different storage adapter has been set.
     * 
     * @return UserAuthenticationStorage
     * 
     */
    public function getStorage()
    {
    	if (null === $this->storage): 
    		$this->setStorage(new UserAuthenticationStorage('nginad'));
    	endif;
    
    	return $this->storage;
    }
    
    /**
     * Sets the persistent storage handler.
     * While the signature requires just the interface class Storage\StorageInterface,
     * this implementation requires the specific implementation of
     * auth\UserAuthenticationStorage. Function will throw exception, if provided
     * otherwise. This is due to PHP polymorphism limitations.
     *
     * @param  auth\UserAuthenticationStorage $storage a auth\UserAuthenticationStorage class handle.
     * @return UserIdentityProvider Provides a fluent interface
     * @throws Exception\ClassTypeException Thrown when $storage is not of auth\UserAuthenticationStorage class.
     */
    public function setStorage(Storage\StorageInterface $storage)
    {
        if (!$storage instanceof UserAuthenticationStorage):
        
            throw new Exception\ClassTypeException("setStorage expects a UserAuthenticationStorage type.");
        endif;
    	$this->storage = $storage;
    	return $this;
    }
    
    /**
     * Sets the authentication adapter
     * While the signature requires just the interface class Adapter\AdapterInterface,
     * this implementation requires the specific implementation of Adapter\DbTable.
     * Function will throw an exception if provided otherwise.
     * This is due to PHP polymorphism limitations.
     *
     * @param  Zend\Authentication\Adapter\DbTable $adapter a Adapter\DbTable class handle.
     * @return UserIdentityProvider Provides a fluent interface
     * @throws Exception\AdapterException Thrown when $adapter is not of Adapter\DbTable class.
     */
    public function setAdapter(Adapter\AdapterInterface $adapter)
    {
        if (!$adapter instanceof Adapter\DbTable):
        
            throw new Exception\AdapterException("setAdapter expects a Database type adapter.");
        endif;
        
    	$this->adapter = $adapter;
    	return $this;
    }
        
    /**
     * Authenticates against the supplied adapter
     * While the signature requires just the interface class Adapter\AdapterInterface,
     * this implementation requires the specific implementation of Adapter\DbTable.
     * Function will throw an exception if provided otherwise.
     * This is due to PHP polymorphism limitations.
     *
     * @param  Zend\Authentication\Adapter\DbTable $adapter an optional Adapter\DbTable class handle.
     * @return Result bool TRUE if authentication is successful and a user is set. FALSE otherwise.
     * @throws Exception\RuntimeException Thrown when $adapter is not of Adapter\DbTable class, or when $adapter is not set.
     */
    public function authenticate(Adapter\AdapterInterface $adapter = null)
    {
    	if (!$adapter || !$adapter instanceof Adapter\DbTable): 
    		if (!$adapter = $this->getAdapter()): 
    			throw new Exception\RuntimeException('An adapter must be set or passed prior to calling authenticate()');
    		endif;
    	endif;
    	$result = $adapter->authenticate();
        
    	/**
    	 * ZF-7546 - prevent multiple successive calls from storing inconsistent results
    	 * Ensure storage has clean state
    	*/
    	if ($this->hasIdentity()): 
    		$this->clearIdentity();
    	endif;
    
    	if ($result->isValid()): 
    	    // Set the identity given using the resultant object.
    	    $identityObject = $this->getStorage()->getContainer();
    	    $identityObject->setIdentityInfo($adapter->getResultRowObject(),$this->ConfigHandle);
    	    $this->getStorage()->save();
    	    
    	endif;
    
    	return $result;
    }
    
    public function authenticateTrusted(stdClass $userDetails)
    {
    	$identityObject = $this->getStorage()->getContainer();
    	
    	$identityObject->setIdentityInfo($userDetails,$this->ConfigHandle);
    	$this->getStorage()->save();
    }
    
    /**
     * Set the handle/array containing the system configuration.
     * 
     * @param array $handle the array or its pointer to the configuration file contents.
     * @return array The handle that was given.
     */
    public function setConfigHandle($handle)
    {
        $this->ConfigHandle = $handle;
        
        return $this->ConfigHandle;
    }
    
    /**
     * Get the array of configuration settings.
     * 
     * @return array
     */
    public function getConfigHandle()
    {
        return $this->ConfigHandle;
    }
    
    // Below are shortcut to identity objects.
    
    /**
     * Get the NAME of the user that is actually logged in.
     * Invokes getUserName of the UserIdentity class.
     * Shortcut to identity object function.
     *
     * Ignores impersonation.
     *
     * @return string The actual name of the user logged in, usually not a number but a login/call name.
     */
    public function getUserName()
    {
        return $this->getIdentity()->getUserName();
    }
    
    /**
     * Returns the user ID of the current identity object.
     * Invokes getUserID of the UserIdentity class.
     * Deprecated/Shortcut to identity object function.
     * 
     * @return integer Currently logged in user ID.
     */
    public function getUserID()
    {
        return $this->getIdentity()->getUserID();
    }
    
    /**
     * Returns the roles associated with the identity object (user).
     * Invokes getRoles of the UserIdentity class.
     * Deprecated/Shortcut to identity object function.
     * 
     * @return array The roles assigned to the current identity object.
     */
    public function getRoles()
    {
        return $this->getIdentity()->getRoles();
    }
    
    /**
     * This will have the current user impersonate another user of the given ID,
     * provided that the user is allowed to do so. The return will be the resulting
     * user ID impersonated, NULL if there is no permission to do so.
     * Invokes setImpersonatedIdentityID of the UserIdentity class.
     * Deprecated/Shortcut to identity objection function.
     * 
     * @param string|integer $id The user ID to attempt to impersonate.
     * @return integer|NULL Returns the identity that was finally applied.
     */
    public function impersonateUserID($id)
    {
        $result = $this->getIdentity()->setImpersonatedIdentityID($id);
        $this->getStorage()->save();
        
        return $result;
    }
    
    /**
     * Get the user's (identity object) primary role.
     * Invokes the getPrimaryRole of the UserIdentity class.
     * Deprecated/Shortcut to identity objection function.
     * 
     * @return string The string value of the primary role.
     */
    public function getPrimaryRole() {
    	return $this->getIdentity()->getPrimaryRole();
    }
    
    /**
     * Get the user's PublisherInfoID if one exists
     *
     * Invokes getPublisherInfoID of the UserIdentity class.
     * Shortcut to identity object function.
     *
     * @return integer|NULL Returns the current active/selected user ID.
     */
    public function getPublisherInfoID()
    {
    	return $this->getIdentity()->getPublisherInfoID();
    	 
    }
    
    /**
     * Get the user's DemandCustomerInfoID if one exists
     *
     * Invokes getDemandCustomerInfoID of the UserIdentity class.
     * Shortcut to identity object function.
     *
     * @return integer|NULL Returns the current active/selected user ID.
     */
    public function getDemandCustomerInfoID()
    {
    	return $this->getIdentity()->getDemandCustomerInfoID();
    
    }
    
    /**
     * Get the user's (identity object) ID that is to be used,
     * impersonated or real, depending on mode specified prior
     * or during the session. NULL will be returned if no user
     * is logged in.
     * 
     * Invokes getEffectiveUserID of the UserIdentity class.
     * Shortcut to identity object function.
     * 
     * @return integer|NULL Returns the current active/selected user ID.
     */
    public function getEffectiveUserID()
    {
        return $this->getIdentity()->getEffectiveUserID();
    	
    }
    
    /**
     * Get the user ID of the user being set to be impersonated.
     * NULL will be returned if not set to impersonate any users, or
     * if the logged in user has no permission to impersonate anyone,
     * or if the user is not logged in.
     * 
     * Invokes getImpersonatedIdentityID of the UserIdentity class.
     * Shortcut to identity objection function.
     * 
     * @return integer|NULL Returns the configured user ID being impersonated.
     */
    public function getImpersonatedIdentityID()
    {
        return $this->getIdentity()->getImpersonatedIdentityID();
    }
    
    /**
     * Get the user ID of the user being set to be impersonated.
     * NULL will be returned if not set to impersonate any users, or
     * if the logged in user has no permission to impersonate anyone.
     * Invokes getImpersonatedIdentityID of the UserIdentity class.
     * 
     * Deprecated function, name is confusing with getEffectiveUserID.
     * Please use getImpersonatedIdentityID() instead for the NULL behavior.
     * 
     * @return integer|NULL Returns the configured user ID being impersonated.
     * 
     */
    public function getEffectiveIdentityID()
    {
    	return $this->getIdentity()->getImpersonatedIdentityID();
    }
    
    /**
     * Determine if the identity object has administrator status and privileges.
     * Invokes getIsAdmin of the UserIdentity class.
     * Deprecated/Shortcut to identity object function.
     * 
     * @return bool TRUE if user has administrator status, FALSE otherwise.
     */
    public function getIsAdmin()
    {
    	return $this->getIdentity()->getIsAdmin();
    }
    
    /**
     * Obtain the role ID number given the role name.
     * 
     * @param string $roleName The string name of the role.
     * @return int The integer ID of the role name provided.
     */
    public function getRoleID($roleName)
    {
        $rbacRoleFactory = \_factory\rbacRole::get_instance();
        $params = array();
        $params["role_name"] =$roleName;
        $rbac_member_role = $rbacRoleFactory->get_row($params);
        
        return $rbac_member_role->role_id;
    }
    
    /**
     * Given a role name, return a result set of all users with that role.
     * 
     * @param string $roleName
     * @return multitype:Ambigous <\Zend\Db\ResultSet\ResultSet, NULL, \Zend\Db\ResultSet\ResultSetInterface>
     */
    public function getRoleUsers($roleName)
    {
        $authUsersFactory = \_factory\authUsers::get_instance();
        $params = array();
        $params["user_role"] = $this->getRoleID($roleName); // First get the role ID of the role "member"
        $auth_Users_list = $authUsersFactory->get($params);
        
        return $auth_Users_list;
    }
    
}

?>