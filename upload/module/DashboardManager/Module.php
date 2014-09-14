<?php
/**
 * CDNPAL NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2015 CDNPAL Ltd. All Rights Reserved
 * @license GPLv3
 */
// force push to working revision
namespace DashboardManager;

// These commented out libraries are not being used, do not include them.
// use Zend\Session\Container; // We need this when using sessions
// use Zend\Authentication\Storage;
// use Zend\Mvc\Application;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use _factory;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\Authentication\Adapter\DbTable as DbTableAuthAdapter;
use DashboardManager\view\Helper\Loginhelper;
use auth\UserAuthenticationStorage;
use auth\UserIdentityProvider;
use Zend\Mail\Transport\Sendmail;
use Zend\Mail\Transport\Smtp;
use Zend\Mail\Transport\SmtpOptions;

/**
 * 
 * @author Christopher Gu
 * This is the RTB Management Panel module class where everything of this module enters.
 *
 */
class Module implements AutoloaderProviderInterface
{
    /**
     * The onBootstrap() method is called for every module on every page request and should only be used for performing lightweight tasks such as registering event listeners.
     * @link http://framework.zend.com/manual/2.0/en/modules/zend.mvc.examples.html#bootstrapping 
     * @param MvcEvent $e Must be an MvcEvent which is called and provides a handle on the calling source object.
     * @return mixed Usually, an empty response, but can be an HTML response to redirect and short circuit the application. 
     */
    public function onBootstrap(MvcEvent $e)
    {
        // Define all the shortcut variables to the various managers in the system.
        $e->getApplication()->getServiceManager()->get('translator');
        $eventManager   = $e->getApplication()->getEventManager();
        $serviceManager = $e->getApplication()->getServiceManager();
        $sm = $e->getApplication()->getServiceManager();
        
        // Bootstrap control of ZFC-RBAC module exception handling.
        // Decide how a person with no permission or not logged in should be handled.
        if($sm->get('config')['system']['debug']):
        
        	$zfcrbac_exception = $serviceManager->get('ZfcRbac\View\Strategy\UnauthorizedStrategy'); // This displays the 403 Unauthorized Page Error.
        
        else:
        
        	$zfcrbac_exception = $serviceManager->get('ZfcRbac\View\Strategy\RedirectStrategy'); // This displays the login page.
        endif;
        $eventManager ->attach(
            $zfcrbac_exception
        );

        // TODO: Find out what does this do?
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);

        // set static adapter for all module table gateways
        $dbAdapter = $serviceManager->get('Zend\Db\Adapter\Adapter');
        \Zend\Db\TableGateway\Feature\GlobalAdapterFeature::setStaticAdapter($dbAdapter);

    }

    /**
     * Module Pre Dispatch events.
     * Currently empty.....
     * 
     * @param \Zend\Mvc\MvcEvent $e
     */
    public function preDispatch(\Zend\Mvc\MvcEvent $e)
    {


    	//do something

    }

    /**
     * Get Module Configuration File
     * @return include config/module.config.php
     */
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    /**
     * (non-PHPdoc)
     * @see \Zend\ModuleManager\Feature\AutoloaderProviderInterface::getAutoloaderConfig()
     */
    public function getAutoloaderConfig()
    {

    	return array(
    			'Zend\Loader\StandardAutoloader' => array(
    					'namespaces' => array(
    							__NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
    							"_factory" => __DIR__ . '/src/DashboardManager/dao/_factory',
    							"model" => __DIR__ . '/src/DashboardManager/model',
    							"util" => __DIR__ . '/src/DashboardManager/util',
    					        "auth" => __DIR__ . '/src/DashboardManager/auth',
    							"transformation" => __DIR__ . '/src/DashboardManager/transformation',
    							'DashboardManager\view\Helper' => __DIR__ . '/src/DashboardManager/view/Helper',
    					),
    			),
    	);

    }

    /**
     * Setup the additional services attached to this module, particularly authentication.
     * @return multitype:multitype:NULL  |\auth\UserIdentityProvider
     */
    public function getServiceConfig()
    {
    	return array(
    			'factories'=>array(
    					'AuthService' => function($sm) {
    						//My assumption, you've alredy set dbAdapter
    						//and has users table with columns : user_name and pass_word
    						//that password hashed with md5
    						$dbAdapter           = $sm->get('Zend\Db\Adapter\Adapter');
    						$dbTableAuthAdapter  = new DbTableAuthAdapter($dbAdapter,
    								'auth_userslogin','user_email','user_password', 'MD5_SPLIT_SALT(?,`user_password_salt`)'); // Use SALTED MD5 passwords when possible.
       						;

       						// IMPORTANT: UserIdentityProvider is needed for ZFC-RBAC access control,
       						//            which REQUIRES a DB to authenticate!
    						$authService = new UserIdentityProvider();
    						$authService->setAdapter($dbTableAuthAdapter);
    						$authService->setStorage(new UserAuthenticationStorage('nginad'));
    						$authService->setConfigHandle($sm->get('config'));
    						
    						// If debugging is set, output the debugging data.
    						// NOTE: Verbose may break browser session handling!
    						if($sm->get('config')['system']['debug']):
    						
    						  echo "\n<div style=\"font-size: 90%;\">\n";
        						  if($sm->get('config')['system']['debug_verbose']):
        						  
            						  echo "\n<div style=\"font-weight: bold;\">Config: </div>";
            						  print_r($sm->get('config'));
            						  print_r(get_class_methods($sm));
            						  print_r($sm->getRegisteredServices());
        						  endif;  
        						  echo "\n<div style=\"font-weight: bold;\">GLOBAL SESSION: </div>";
        						  print_r($_SESSION);
    						  echo "</div>\n";
    						endif;
    						
    						return $authService;
    					},
                        'mail.transport' => function ($sm) {
                        	$config = $sm->get('Config'); 
                        	if ($config['mail']['transport']['smtp']):
	                       		$transport = new Smtp();                
	                        	$transport->setOptions(new SmtpOptions($config['mail']['transport']['options']));
	                        else:
	                        	$transport = new Sendmail();
                        	endif;

                        	return $transport;
                        },
    					
    			),
    	);

    }

    /**
     * 
     * @return multitype:multitype:NULL  |\DashboardManager\View\Helper\Loginhelper
     */
    public function getViewHelperConfig()
    {
    	return array(
    			'factories' => array(
    					'Login_bool' => function ($helperPluginManager) {
    						$serviceLocator = $helperPluginManager->getServiceLocator();
    						$viewHelper = new Loginhelper();
    						$viewHelper->setServiceLocator($serviceLocator);
    						return $viewHelper;
    					}
    			)
    	);
    }

}

?>