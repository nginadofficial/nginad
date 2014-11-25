<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Delivery;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use _factory;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\Authentication\Storage;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Adapter\DbTable as DbTableAuthAdapter;
use Zend\Mvc\Application;

class Module implements AutoloaderProviderInterface
{

	private $is_delivery = false;

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
    							"mobileutil" => __DIR__ . '/../BuyGenericRTB/src/BuyGenericRTB/_mobile',
    							"rtbsellv22" => __DIR__ . '/../SellGenericRTB/src/SellGenericRTB/rtbsellv22',
    							"rtbsell" => __DIR__ . '/../SellGenericRTB/src/SellGenericRTB/rtbsell',
    							"sellrtb" => __DIR__ . '/../SellGenericRTB/src/SellGenericRTB/common',
	   							"pinger" => __DIR__ . '/../SellGenericRTB/src/SellGenericRTB/pinger',
    							// LOAD THE GENERIC RTB PARENT CLASSES
    							"rtbbuyv22" => __DIR__ . '/../BuyGenericRTB/src/BuyGenericRTB/rtbbuyv22',
    							"rtbbuy" => __DIR__ . '/../../BuyGenericRTB/src/BuyGenericRTB/rtbbuy',
    							"buyrtb" => __DIR__ . '/../../BuyGenericRTB/src/BuyGenericRTB/common',
    							// LOAD THE REST OF THE STUFF
    							"buyloopbackpartner" => __DIR__ . '/../BuySidePartners/BuyLoopbackPartner/src/BuyLoopbackPartner/loopbackpartner',
    							"mobileutil" => __DIR__ . '/../BuyGenericRTB/src/BuyGenericRTB/_mobile',
    							"geoip" => __DIR__ . '/../BuyGenericRTB/src/BuyGenericRTB/geoip',
    					),
    			),
    	);
    }

}
