<?php
/**
 * NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2016 NginAd Foundation. All Rights Reserved
 * @license GPLv3
 */



return array(
    'router' => array(
        'routes' => array(
            'maintenance' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/maintenance',
                    'defaults' => array(
                        'controller' => 'Maintenance\Controller\Index',
                        'action'     => 'index',
                    ),
                ),
            ),
            'daily' => array(
            		'type' => 'Zend\Mvc\Router\Http\Literal',
            		'options' => array(
            				'route'    => '/maintenance/daily',
            				'defaults' => array(
            						'controller' => 'Maintenance\Controller\Index',
            						'action'     => 'dailyMaintenance',
            				),
            		),
            ),
            'crontab' => array(
            		'type' => 'Zend\Mvc\Router\Http\Literal',
            		'options' => array(
            				'route'    => '/maintenance/crontab',
            				'defaults' => array(
            						'controller' => 'Maintenance\Controller\Index',
            						'action'     => 'crontab',
            				),
            		),
            ),
            'torlist' => array(
            		'type' => 'Zend\Mvc\Router\Http\Literal',
            		'options' => array(
            				'route'    => '/maintenance/torlist',
            				'defaults' => array(
            						'controller' => 'Maintenance\Controller\Index',
            						'action'     => 'torlist',
            				),
            		),
            ),
        ),
    ),
    'service_manager' => array(
        'factories' => array(
            'translator' => 'Zend\I18n\Translator\TranslatorServiceFactory',
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'Maintenance\Controller\Index' => 'Maintenance\Controller\IndexController',
        ),
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => array(
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
//            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    )
);
