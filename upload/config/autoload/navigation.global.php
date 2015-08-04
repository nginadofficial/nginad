<?php
/**
 * NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2016 NginAd Foundation. All Rights Reserved
 * @license GPLv3
 */

return array(
		// ...

		'navigation' => array(
				'default' => array(
				    array('label' => 'Manager Home',
				    		'route' => 'manager',
				    		'controller' => 'DashboardManager\Controller\manager',
				    		'module' => 'DashboardManager',
				    		'action' => 'index',
				    		'id' => 'ManagerHomeLevel',
				        ),
						array('label' => 'Manager Home',
							'route' => 'private-exchange',
                            'controller' => 'DashboardManager\Controller\demand',
                            'module' => 'DashboardManager',
                            'action' => 'index',
                            'id' => 'ManagerHomeLevel',
						    'pages' => array(
							array('label' => 'Create Insertion Order',
							    	'route' => 'private-exchange',
							        'action' => 'createinsertionorder',
								),
						    array('label' => 'Edit Insertion Order',
						        	'route' => 'private-exchange',
						        	'action' => 'editinsertionorder',
						        ),
						    array('label' => 'Create Line Item',
						        'route' => 'private-exchange',
						        'action' => 'createlineitem',
                                'id' => 'CreateBannerLevel',
						        ),
						    array('label' => 'View Line Items',
						        		'route' => 'private-exchange',
						        		'action' => 'viewlineitem',
                                        'controller' => 'DashboardManager\Controller\demand',
                                        'module' => 'DashboardManager',
                                        'id' => 'ViewBannerLevel',
                                        'pages' => array(
							        		    array('label' => 'Edit Line Item',
							        		    		'action' => 'editlineitem',
                                                        'controller' => 'DashboardManager\Controller\demand',
                                                        'module' => 'DashboardManager',
							        		    ),
							        		    array('label' => 'Edit Delivery Filter',
							        		    		'action' => 'deliveryfilter',
							        		    ),
						        		    ),
						        ),

						),

                                ),
                            ),
		),
		'service_manager' => array(
				'factories' => array(
						'navigation' => 'Zend\Navigation\Service\DefaultNavigationFactory',
				),
		),

);
