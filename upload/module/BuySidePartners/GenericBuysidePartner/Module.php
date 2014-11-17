<?php
/**
 * CDNPAL NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2015 CDNPAL Ltd. All Rights Reserved
 * @license GPLv3
 */

namespace GenericBuysidePartner;

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

	private $is_bid = false;

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
    							"_factory" => __DIR__ . '/../../DashboardManager/src/DashboardManager/dao/_factory',
    							"model" => __DIR__ . '/../../DashboardManager/src/DashboardManager/model',
    							"util" => __DIR__ . '/../../DashboardManager/src/DashboardManager/util',
    							"geoip" => __DIR__ . '/../../BuyGenericRTB/src/BuyGenericRTB/geoip',
    							// LOAD THE GENERIC RTB PARENT CLASSES
    							"rtbbuyv22" => __DIR__ . '/../../BuyGenericRTB/src/BuyGenericRTB/rtbbuyv22',
    							"rtbbuy" => __DIR__ . '/../../BuyGenericRTB/src/BuyGenericRTB/rtbbuy',
    							"buyrtb" => __DIR__ . '/../../BuyGenericRTB/src/BuyGenericRTB/common',
    							// LOAD THE REST OF THE STUFF
    							"buygenericbuysidepartner" => __DIR__ . '/src/GenericBuysidePartner/genericbuysidepartner',
    							"mobileutil" => __DIR__ . '/../../BuyGenericRTB/src/BuyGenericRTB/_mobile'
    					),
    			),
    	);
    }

}
