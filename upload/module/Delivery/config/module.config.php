<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */



return array(
    'router' => array(
        'routes' => array(
            'delivery' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/delivery',
                    'defaults' => array(
                        'controller' => 'Delivery\Controller\Index',
                        'action'     => 'index',
                    ),
                ),
            ),
            'impress' => array(
            		'type' => 'Zend\Mvc\Router\Http\Literal',
            		'options' => array(
            				'route'    => '/delivery/impress',
            				'defaults' => array(
            						'controller' => 'Delivery\Controller\Index',
            						'action'     => 'impress',
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
            'Delivery\Controller\Index' => 'Delivery\Controller\IndexController',
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
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    )
);
