<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace WebsiteCustom;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use _factory;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\Authentication\Storage;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Adapter\DbTable as DbTableAuthAdapter;
use Zend\Mvc\Application;
use WebsiteCustom\view\Helper\Loginhelper;

class Module implements AutoloaderProviderInterface
{

	private $is_delivery = false;

    public function onBootstrap(MvcEvent $e)
    {
    	$e->getApplication()->getServiceManager()->get('translator');
    	$eventManager        = $e->getApplication()->getEventManager();

    	$moduleRouteListener = new ModuleRouteListener();
    	$moduleRouteListener->attach($eventManager);


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
    					),
    			),
    	);
    }
    
}
