<?php
/**
 * CDNPAL NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2015 CDNPAL Ltd. All Rights Reserved
 * @license GPLv3
 */
namespace auth;

use stdClass;
use ZfcRbac\Identity\IdentityInterface;
use Zend\Db\Sql\Ddl\Column\Boolean;
//use Zend\Session\Container; // Extending the class makes the session store explode.

/**
 * UserIdentity object holding the specific data of the current authenticated user.
 * 
 * While object is intended to extend the Zend\Session\Container class since it should
 * be stored like a container object in the Zend\Authentication\Storage implementation;
 * However, doing so will cause all the recursive system autoload pointers to load into
 * the session save state, which is generally too much on most installations and in
 * almost all use cases. So, do not uncomment the Container related attributes until
 * a "graceful" solution to this problem is found.
 * 
 * @author Kelvin Mok, Christopher Gu
 *
 */
class UserIdentity 
//extends Container
implements IdentityInterface
{

        /**
         * 
         * @var array
         */
        protected $TrueIdentityRole;
        
        /**
         * 
         * @var integer
         */
        protected $TrueIdentityID;
        
        /**
         * @var string
         */
        protected $TrueIdentityName;
        
        /**
         * 
         * @var boolean
         */
        protected $TrueIdentityAdmin;
        
        /**
         * This variable contains a class object of the DB row result
         * of the authenticated user, without any sensitive credential
         * information.
         * 
         * @var stdClass
         */
        protected $TrueIdentityDetails;
        
        /**
         * This variable contains a class object of DB row result
         * of the authenticated user and MAY contain sensitive
         * password/credentials information.
         * 
         * @var stdClass
         */
        private $TrueRawUserDetails;
        
        /**
         * 
         * @var integer
         */
        protected $ImpersonateIdentityID;
        
        /**
         *
         * @var integer
         */
        protected $PublisherInfoID;
        
        /**
         *
         * @var integer
         */
        protected $DemandCustomerInfoID;
        
        /**
         * 
         * @var string|array
         */
        protected $AdminRolesConfig;
        
        /**
         * 
         * @var boolean
         */
        private $debug;
        
        /**
         * 
         * @var Boolean
         */
        private $debug_verbose;

        /**
         * Serialize this object for storage into another medium.
         * 
         * @return string
         */
        public function serialize()
        {
            return serialize($this->TrueIdentityDetails);
        }
        
        /**
         * Deserialize the object from storage into memory.
         * 
         * @param string $sessionData
         * @return \auth\UserIdentity This identity object that has been populated with the storage data.
         */
        public function deserialize($sessionData)
        {
            //echo "WHAT?";
            //print_r(unserialize($sessionData));
            //die();
            if (unserialize($sessionData) == null):
                return $this;
            endif;
            
            return $this->setIdentityInfo(unserialize($sessionData), null);
        }
        
        /**
         * Set the identity information from the provided authenticated user.
         * 
         * @param stdClass $userDetails A class object of the DB row data describing the authenticated user attributes.
         * @param array $config An optional system configuration file handle or array.
         * @return \auth\UserIdentity Resulting populated identity object of the authenticated user provided.
         */
        public function setIdentityInfo(stdClass $userDetails, $config = null)
        {
            if ($config !== null):
            
                $this->AdminRolesConfig = $config['roles']['admin'];
                $this->debug = $config['system']['debug'];
                $this->debug_verbose = $config['system']['debug_verbose'];
            endif;
            
            if ($this->debug && $this->debug_verbose):
            
                var_dump( $userDetails);
                echo "My role in this application is: ";
                print_r($userDetails->user_primary_role);
            endif;
            
            if ($userDetails instanceof stdClass):
            
                //TODO: Add exception checking, I.E.: when user details are invalid.
                $this->rawUserDetails = $userDetails; // Save raw details WITH sensitive information.
                
                // Strip sensitive password data, if exists.
                unset($userDetails->user_password);
                unset($userDetails->user_password_salt);
                unset($userDetails->user_2factor_secret);
                $this->TrueIdentityDetails = $userDetails; // Save inheritable identity details.
                $this->TrueIdentityRole = array($userDetails->user_role_name);
                $this->TrueIdentityID = intval($userDetails->user_id);
                $this->TrueIdentityName = $userDetails->user_login;
                $this->PublisherInfoID = intval($userDetails->PublisherInfoID);
                $this->DemandCustomerInfoID = intval($userDetails->DemandCustomerInfoID);
                
                if (strpos($this->getPrimaryRole(), $this->AdminRolesConfig) !== false):
                
                    $this->TrueIdentityAdmin = true;
                
                else: 
                
                    $this->TrueIdentityAdmin = false;
                endif;
                
                $this->setImpersonatedIdentityID(0,null); // Initialize the value.
                
            endif;
            
            return $this;
            
        }
        
        /**
         * Get the NAME of the user that is actually logged in.
         * 
         * Ignores impersonation.
         * 
         * @return string The actual name of the user logged in, usually not a number but a login/call name.
         */
        public function getUserName()
        {
            return $this->TrueIdentityName;
        }
        
        /**
         * Get the role of the true and actually logged on user.
         * 
         * @return array An array of all the roles assigned to the logged in user.
         * @see \ZfcRbac\Identity\IdentityInterface::getRoles()
         */
        public function getRoles()
        {
                return $this->TrueIdentityRole;
        }

        /**
         * Get the ID of the user that is actually logged in.
         * 
         * Ignores impersonation, oppsite of getImpersonatedIdentityID().
         * 
         * @return integer|string The ID number of the user logged in, in string or integer format (DB dependent).
         */
        public function getUserID()
        {
                return $this->TrueIdentityID;
        }

        /**
         * Is the logged in user an administrator or has the status/privileges.
         * 
         * @return boolean TRUE if the user has the administrator status or privileges. FALSE otherwise.
         */
        public function getIsAdmin()
        {
                return $this->TrueIdentityAdmin;
        }
        
        /**
         * This will have the current user impersonate another user of the given ID,
         * provided that the user is allowed to do so. The return will be the resulting
         * user ID impersonated, NULL if there is no permission to do so.
         * 
         * @param integer $identity The user ID number of the user to be impersonated.
         * @return integer|NULL Returns the identity that was finally applied.
         */
        public function setImpersonatedIdentityID($identity)
        {
            
            if (!is_int($identity)): // Don't change anything if it is not an integer!
            
                $identity = intval($identity);
                if (intval($identity) < 0):
                
                    return $this->ImpersonateIdentityID;
                endif;
            endif;
            
            if ($this->TrueIdentityAdmin):
            
                if ($this->debug && $this->debug_verbose):
                
                	echo "\n<div style=\"font-size: 75%;\"><a style=\"font-weight: bold;\">Setting Impersonation to User ID:</a> " .
                			$identity . "</div>\n";
                endif;
            	$this->ImpersonateIdentityID = $identity; //Initialize the value.
            
            else:
            
            	$this->ImpersonateIdentityID = null; // null the value.
            endif;
            
            return $this->ImpersonateIdentityID;
        }
        
        /**
         * Get the user ID of the user being set to be impersonated.
         * NULL will be returned if not set to impersonate any users, or
         * if the logged in user has no permission to impersonate anyone,
         * or if no user is logged in.
         * 
         * Ignores real user ID, opposite of getUserID().
         * 
         * @return integer|NULL Returns the configured user ID being impersonated.
         */
        public function getImpersonatedIdentityID()
        {
                return $this->ImpersonateIdentityID;
        }

        /**
         * Get the user's (identity object) ID that is to be used,
         * impersonated or real, depending on mode specified prior
         * or during the session.  NULL will be returned ONLY if no user
         * is logged in.
         * 
         * @return integer|NULL Returns the current active/selected user ID.
         */
        public function getEffectiveUserID()
        {
            if ($this->debug && $this->debug_verbose):
            
                echo "\n<div style=\"font-size: 75%;\"><a style=\"font-weight: bold;\">Attempting Impersonation to User ID:</a> " . 
                        $this->ImpersonateIdentityID . "</div>\n";
            endif;
            
            if (strpos($this->getPrimaryRole(), $this->AdminRolesConfig) !== false &&
                    $this->ImpersonateIdentityID != null &&
                    $this->ImpersonateIdentityID != 0):
                    
                        if ($this->debug):
                        
                            echo "\n<div style=\"font-weight: bold; font-size: 75%;\">Impersonated User ID: " . 
                                    $this->ImpersonateIdentityID . "</div>\n";
                        endif; 
                        return $this->ImpersonateIdentityID;
                    
            else: 
            
                if ($this->debug && $this->debug_verbose):
                
                	echo "\n<div style=\"font-size: 75%;\"><a style=\"font-weight: bold;\">Impersonation FAILED!</a></div>\n";
                endif;
                  return $this->TrueIdentityID;
            endif;
            
        }

        /**
         * Get the user's (identity object) primary role.
         * 
         * @return string|NULL The main role of the logged in user, NULL if none.
         */
        public function getPrimaryRole() {
                $roles = $this->getRoles();
                return $roles[0];
        }
	
	/**
	 * @return the $PublisherInfoID
	 */
	public function getPublisherInfoID() {
		return $this->PublisherInfoID;
	}

		/**
	 * @return the $DemandCustomerInfoID
	 */
	public function getDemandCustomerInfoID() {
		return $this->DemandCustomerInfoID;
	}

		/**
	 * @param number $PublisherInfoID
	 */
	public function setPublisherInfoID($PublisherInfoID) {
		$this->PublisherInfoID = $PublisherInfoID;
	}

		/**
	 * @param number $DemandCustomerInfoID
	 */
	public function setDemandCustomerInfoID($DemandCustomerInfoID) {
		$this->DemandCustomerInfoID = $DemandCustomerInfoID;
	}

        
    
        
}
?>