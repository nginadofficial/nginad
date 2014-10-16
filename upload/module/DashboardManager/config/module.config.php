<?php
/**
 * CDNPAL NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2015 CDNPAL Ltd. All Rights Reserved
 * @license GPLv3
 */

/**
 * @author Kelvin Mok
 * ROUTER NOTES: Please note that child routes are defined, and that certain functions are separated to
 * make code maintenance more sane. The trade off in eaiser code maintenance is that the route maintenance
 * is now more complicated, as you have to make sure that routes are defined in the correct order. Make
 * sure that "MORE SPECIFIC" (longer URIs) are specified lower/later in the tree/config array than a 
 * "LESS SPECIFIC" (general, shorter URIs)! Otherwise, a general URI will override a specific URI and
 * render the definition null.
 * 
 * As indicated in the ZF2 Routing documentation:
 * "Typically, routes should be queried in a LIFO order, and hence the reason behind the name RouteStack.
 * Zend Framework provides two implementations of this interface, SimpleRouteStack and TreeRouteStack.
 * In each, you register routes either one at a time using addRoute(), or in bulk using addRoutes()."
 * @link http://framework.zend.com/manual/2.0/en/modules/zend.mvc.routing.html
 * 
 * LIFO = Last In First Out
 */

return array(
    'router' => array(
        'routes' => array(
            'websites' => array(
            		'type'    => 'segment',
            		'options' => array(
            				'route'    =>  '/websites[/[:action[/[:param1[/]]]]]',
            				'defaults' => array(
            						'controller' => 'DashboardManager\Controller\Website',
            						'action'     => 'index',
            				),
            		),
            ),
            'users' => array(
            		'type'    => 'segment',
            		'options' => array(
            				'route'    =>  '/users[/[:action[/[:param1[/]]]]]',
            				'defaults' => array(
            						'controller' => 'DashboardManager\Controller\Signup',
            						'action'     => 'account',
            				),
            		),
            ),
            'company' => array(
            		'type'    => 'segment',
            		'options' => array(
            				'route'    =>  '/company',
            				'defaults' => array(
            						'controller' => 'DashboardManager\Controller\Company',
            						'action'     => 'index',
            				),
            		),
            		
            		'may_terminate' => true,
            		'child_routes' => array(
            		   'action' => array(
            			     'type' => 'segment',
            			     'options' => array(
            			     		'route'    =>  '/:action',
            			     		'constraints' => array(
            			     				//'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
            			     				'param1'     => '[0-9]+',
            			     		),
            			     		'defaults' => array(
            			     				'__NAMESPACE__' => 'DashboardManager\Controller',
            			     				'controller' => 'DashboardManager\Controller\Company',
            			     				'action'     => 'index',
            			     		),
            			     ),
            		    ),
            			'press' => array(
            			     'type' => 'segment',
            			     'options' => array(
            			     		'route'    =>  '/press/releases/:param1',
            			     		'constraints' => array(
            			     				//'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
            			     				'param1'     => '[0-9]+',
            			     		),
            			     		'defaults' => array(
            			     				'__NAMESPACE__' => 'DashboardManager\Controller',
            			     				'controller' => 'DashboardManager\Controller\Company',
            			     				'action'     => 'pressinner',
            			     		),
            			     ),
            		    ),
            		    'jobs' => array(
            			     'type' => 'segment',
            			     'options' => array(
            			     		'route'    =>  '/jobs/post/:param1',
            			     		'constraints' => array(
            			     				//'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
            			     				'param1'     => '[0-9]+',
            			     		),
            			     		'defaults' => array(
            			     				'__NAMESPACE__' => 'DashboardManager\Controller',
            			     				'controller' => 'DashboardManager\Controller\Company',
            			     				'action'     => 'jobsinner',
            			     		),
            			     ),
            		    ),
            		    'jobform' => array(
            			     'type' => 'segment',
            			     'options' => array(
            			     		'route'    =>  '/jobs/post/apply/:param1',
            			     		'constraints' => array(
            			     				//'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
            			     				'param1'     => '[0-9]+',
            			     		),
            			     		'defaults' => array(
            			     				'__NAMESPACE__' => 'DashboardManager\Controller',
            			     				'controller' => 'DashboardManager\Controller\Company',
            			     				'action'     => 'jobform',
            			     		),
            			     ),
            		    ),
            		    

            		    
            		),
            		
            		
            		
            		
            		
            ),
            'signup' => array(
            		'type'    => 'segment',
            		'options' => array(
            				'route'    =>  '/signup[/[:action[/[:param1[/]]]]]',
            				'constraints' => array(
            						'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
            						'param1'     => '[0-9]+',
            				),
            				'defaults' => array(
            				        '__NAMESPACE__' => 'DashboardManager\Controller',
            						'controller' => 'DashboardManager\Controller\Signup',
            						'action'     => 'index',
            				),
            		),
            ),
            'signup-index' => array(
            		'type'    => 'segment',
            		'options' => array(
            				'route'    =>  '/',
            				'constraints' => array(
            						'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
            						'param1'     => '[0-9]+',
            				),
            				'defaults' => array(
            						'__NAMESPACE__' => 'DashboardManager\Controller',
            						'controller' => 'DashboardManager\Controller\Signup',
            						'action'     => 'index',
            				),
            		),
            ),
            'manager' => array(
            		'type'    => 'segment',
            		'options' => array(
            				'route'    =>  '/manager[/[:action[/[:param1[/]]]]]',
            				'constraints' => array(
            						'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
            						'param1'     => '[0-9]+',
            				),
            				'defaults' => array(
            				        '__NAMESPACE__' => 'DashboardManager\Controller',
            						'controller' => 'DashboardManager\Controller\Manager',
            						'action'     => 'index',
            				),
            		),
            ),
			'report' => array(
                    'type'    => 'segment',
                    'options' => array(
                            'route'    =>  '/report[/[:action[/[:param1[/]]]]]',
                            'constraints' => array(
                                    'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                    'param1'     => '[0-9]+',
                            ),
                            'defaults' => array(
                                    '__NAMESPACE__' => 'DashboardManager\Controller',
                                    'controller' => 'DashboardManager\Controller\Report',
                                    'action'     => 'index',
                            ),
                    ),
            ),
            'demand' => array(
            		'type'    => 'segment',
            		'options' => array(
            				'route'    =>  '/demand[/[:action[/[:param1[/]]]]]',
            				'constraints' => array(
            						'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
            						'param1'     => '[0-9]+',
            				),
            				'defaults' => array(
            				        '__NAMESPACE__' => 'DashboardManager\Controller',
            						'controller' => 'DashboardManager\Controller\Demand',
            						'action'     => 'index',
            				),
            		),
            ),
            'publisher' => array(
            		'type'    => 'Literal',
            		'options' => array(
            				'route'    =>  '/publisher',
            				'defaults' => array(
            						'controller' => 'DashboardManager\Controller\Publisher',
            						'action'     => 'index',
            				),
            		),
            		'may_terminate' => true,
            		'child_routes' => array(
            			'process' => array(
            			     'type' => 'segment',
            			     'options' => array(
            			     		'route'    =>  '/[:action[/[:param1[/]]]]',
            			     		'constraints' => array(
            			     				'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
            			     				'param1'     => '[0-9]+',
            			     		),
            			     		'defaults' => array(
            			     				'__NAMESPACE__' => 'DashboardManager\Controller',
            			     				'controller' => 'DashboardManager\Controller\Publisher',
            			     				'action'     => 'index',
            			     		),
            			     ),
            		    ),
            			'zone' => array(
            			     'type' => 'segment',
            			     'options' => array(
            			           'route' => '/zone/:param1[/[:action[/[:id[/]]]]]',
            			           'constraints' => array(
                			             'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                			             'param1' => '[0-9]+',
                			             'id'     => '[0-9]+',
            		               ),
            		               'defaults' => array(
            		                      '__NAMESPACE__' => 'DashboardManager\Controller',
            		                      'controller' => 'DashboardManager\Controller\Zone',
            		                      'action'     => 'index',
            		               ),
            		          ),
            		    ),
            		),
            ),
            'login' => array(
            		'type'    => 'Literal',
            		'options' => array(
            				'route'    => '/auth',
            				'defaults' => array(
            						'controller' => 'DashboardManager\Controller\Auth',
            						'action'        => 'login',
            				),
            		),
            		'may_terminate' => true,
            		'child_routes' => array(
            				'process' => array(
            						'type'    => 'Segment',
            						'options' => array(
            								'route'    => '/[:action]',
            								'constraints' => array(
            										'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            										'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
            								),
            								'defaults' => array(
            								),
            						),
            				),
            		),
            ),
            'msa' => array(
            		'type'    => 'segment',
            		'options' => array(
            				'route'    =>  '/auth/msa[/[:param1[/]]]',
            				'constraints' => array(
            						'param1' => '[a-zA-Z][a-zA-Z0-9_-]*',
            				),
            				'defaults' => array(
            						'controller' => 'DashboardManager\Controller\Auth',
            						'action'     => 'msa',
            				),
            		),
            ),
        ),
    ),
    'service_manager' => array(
        'factories' => array(
            'translator' => 'Zend\I18n\Translator\TranslatorServiceFactory',
            'Zend\Authentication\AuthenticationService' => function($sm) {
                //return new Zend\Authentication\AuthenticationService(); // Built-in ZF2 Authentication Service.
                return $sm->get('AuthService'); // Custom Authentication Service.
            },
        ),
    ),
    'translator' => array(
        'locale' => 'en_US',
        'translation_file_patterns' => array(
            array(
                'type'     => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.mo',
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
          	'DashboardManager\Controller\Company' => 'DashboardManager\Controller\CompanyController',          
        	'DashboardManager\Controller\Signup' => 'DashboardManager\Controller\SignupController',
            'DashboardManager\Controller\Manager' => 'DashboardManager\Controller\ManagerController',
            'DashboardManager\Controller\Demand' => 'DashboardManager\Controller\DemandController',
            'DashboardManager\Controller\Publisher' => 'DashboardManager\Controller\PublisherController',
            'DashboardManager\Controller\Auth' => 'DashboardManager\Controller\AuthController',
            'DashboardManager\Controller\Success' => 'DashboardManager\Controller\SuccessController',
            'DashboardManager\Controller\Report' => 'DashboardManager\Controller\ReportController',
            'DashboardManager\Controller\Zone' => 'DashboardManager\Controller\ZoneController',
            'DashboardManager\Controller\Website' => 'DashboardManager\Controller\WebsiteController'
        ),
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => array(
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'unauthorized_template'   => __DIR__ . '/../view/error/403.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ),
        'template_path_stack' => array(
                    __DIR__ . '/../view',
//            'manager' => __DIR__ . '/../view',
//            'demand' => __DIR__ . '/../view',
        ),
    )
);

?>