<?php
/**
 * CDNPAL NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2015 CDNPAL Ltd. All Rights Reserved
 * @license GPLv3
 */

namespace Maintenance;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use _factory;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\Authentication\Storage;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Adapter\DbTable as DbTableAuthAdapter;
use Zend\Mvc\Application;
use Zend\Mail\Transport\Smtp;
use Zend\Mail\Transport\SmtpOptions;

class Module implements AutoloaderProviderInterface
{

	private $is_maintenance = false;

    public function onBootstrap(MvcEvent $e)
    {
    	$e->getApplication()->getServiceManager()->get('translator');
    	$eventManager        = $e->getApplication()->getEventManager();

    	$moduleRouteListener = new ModuleRouteListener();
    	$moduleRouteListener->attach($eventManager);

    	// set static adapter for all module table gateways

    	$serviceManager = $e->getApplication()->getServiceManager();

    	$dbAdapter = $serviceManager->get('Zend\Db\Adapter\Adapter');

    	\Zend\Db\TableGateway\Feature\GlobalAdapterFeature::setStaticAdapter($dbAdapter);

    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {

    	return array(
    			'Zend\Loader\StandardAutoloader' => array(
    					'namespaces' => array(
    							__NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
    							"_factory" => __DIR__ . '/../DashboardManager/src/DashboardManager/dao/_factory',
    							"model" => __DIR__ . '/../DashboardManager/src/DashboardManager/model',
    							"util" => __DIR__ . '/../DashboardManager/src/DashboardManager/util',
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
                                            $transport = new Smtp();
                                            $transport->setOptions(new SmtpOptions($config['mail']['transport']['options']));

                                            return $transport;
                                        },
    					
    			),
    	);

    }

}
