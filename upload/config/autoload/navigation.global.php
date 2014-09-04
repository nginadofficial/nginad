<?php
/**
 * CDNPAL NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2015 CDNPAL Ltd. All Rights Reserved
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
							'route' => 'demand',
                            'controller' => 'DashboardManager\Controller\demand',
                            'module' => 'DashboardManager',
                            'action' => 'index',
                            'id' => 'ManagerHomeLevel',
						    'pages' => array(
							array('label' => 'Create Campaign',
							    	'route' => 'demand',
							        'action' => 'createcampaign',
								),
						    array('label' => 'Edit Campaign',
						        	'route' => 'demand',
						        	'action' => 'editcampaign',
						        ),
						    array('label' => 'Create Banner',
						        'route' => 'demand',
						        'action' => 'createbanner',
                                'id' => 'CreateBannerLevel',
						        ),
						    array('label' => 'View Banners',
						        		'route' => 'demand',
						        		'action' => 'viewbanner',
                                        'controller' => 'DashboardManager\Controller\demand',
                                        'module' => 'DashboardManager',
                                        'id' => 'ViewBannerLevel',
                                        'pages' => array(
							        		    array('label' => 'Edit Banner',
							        		    		'action' => 'editbanner',
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
