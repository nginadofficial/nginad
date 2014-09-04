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
use Zend\Session\Container; 
//use Zend\View\Model\ViewModel; // Not used? Extraneous?

/**
 * Authentication Action Controller class to handle user login of the
 * management dashboard.
 * 
 * @author Christopher Gu, Kelvin Mok
 *
 */
class AuthController extends AbstractActionController {
    protected $storage;
    protected $authservice;
    protected $config;

    protected function getAuthService()
    {
    	if (! $this->authservice): 
    		$this->authservice = $this->getServiceLocator()
    		->get('AuthService');
    	endif;
    	
    	return $this->authservice;
    }

    protected function getConfig()
    {
    	if (! $this->config): 
    		$this->config = $this->getServiceLocator()
    		->get('Config');
    	endif;

    	return $this->config;
    }

    protected function getSessionStorage()
    {
    	if (! $this->storage): 
    		$this->storage = $this->getAuthService()
    		->getStorage();
    	endif;

    	return $this->storage;
    }

    public function loginAction()
    {
    	$user_session = new Container('user');
    	
    	//if already login, redirect to success page
    	if ($this->getAuthService()->hasIdentity()):
    		$user_session->message = '';
    		return $this->redirect()->toRoute('demand');
    	endif;

    	return array(
    			'messages'  => $user_session->message,
    			'center_class' => 'centerj',
    			'dashboard_view' => 'login'
    	);
    }

    public function authenticateAction()
    {
        $debug = false;
        if ($debug):
        
        	echo "\n<div style=\"font-size: 90%;\">\n";
        	echo "\n<div style=\"font-weight: bold;\">Config: </div>";
        	print_r($this->getConfig());
        
        	echo "\n<div style=\"font-weight: bold;\">GLOBAL SESSION: </div>";
        	print_r($_SESSION);
        	echo "</div>\n";
        	die();
        endif;
        
    	$redirect = 'login';

    	$request = $this->getRequest();
    	if ($request->isPost()):
   			//check authentication...
   			$this->getAuthService()->getAdapter()
   			->setIdentity($request->getPost('username'))
   			->setCredential($request->getPost('password'));
   			
   			$user_session = new Container('user');
   			
   			if ($request->getPost('username') == null || $request->getPost('password') == ""):
               $user_session->message = 'Invalid username or password.';
               return $this->redirect()->toRoute($redirect);
   			endif;
   			
   			$result = $this->getAuthService()->authenticate();
   			
   			if (!$result->isValid()):
   			 	$user_session->message = 'Invalid username or password.';
                return $this->redirect()->toRoute($redirect);
   			endif;
   			
   			
    		/*foreach ($result->getMessages() as $message):
   			
   				//save message temporary into flashmessenger
   				$this->flashmessenger()->addMessage($message);
   			endforeach;*/
    			
   			if ($result->isValid()): 
   				$redirect = 'demand';
   				//check if it has rememberMe :
   				if ($request->getPost('rememberme') == 1 ): 
   					$this->getSessionStorage()
   					->setRememberMe(1);
   					//set storage again
   					$this->getAuthService()->setStorage($this->getSessionStorage());
   				endif;
   			endif;
    	endif;

    	return $this->redirect()->toRoute($redirect);
    }

    public function logoutAction()
    {
    	$this->getSessionStorage()->forgetMe();
    	$this->getAuthService()->clearIdentity();

    	$this->flashmessenger()->addMessage("You've been logged out");
    	return $this->redirect()->toRoute('login');
    }
}

?>