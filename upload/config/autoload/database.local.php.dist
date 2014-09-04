<?php
/**
 * CDNPAL NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2015 CDNPAL Ltd. All Rights Reserved
 * @license GPLv3
 */

/**
 * Local Configuration Override
 *
 * This configuration override file is for overriding environment-specific and
 * security-sensitive configuration information. Copy this file without the
 * .dist extension at the end and populate values as needed.
 *
 * @NOTE: This file is ignored from Git by default with the .gitignore included
 * in ZendSkeletonApplication. This is a good practice, as it prevents sensitive
 * credentials from accidentally being committed into version control.
 */

//$this->getServiceLocator()->get('db');
$dbParams = array(
		'dbname' => 'nginad',
		'user'   => 'root',
		'pass'   => '',
		'host'   => 'localhost',
);

return array(
		'db' => array(
				'driver'         => 'Pdo',
                'dsn'            => 'mysql:dbname=' . $dbParams['dbname'] . ';host=' . $dbParams['host'],
                'username'       => $dbParams['user'],
                'password'       => $dbParams['pass'],
		),

        'doctrine' => array(
                'connection' => array(
                      'orm_default' => array(
                          'driverClass' => 'Doctrine\DBAL\Driver\PDOMySql\Driver',
                          'params' => array(

                              'host'     => $dbParams['host'],
                              'port'     => 3306,
                              'user'     => $dbParams['user'],
                              'password' => $dbParams['pass'],
                              'dbname'   => $dbParams['dbname'],
                          )
                     )
               )
        ),


		'service_manager' => array(
				'factories' => array(
						'Zend\Db\Adapter\Adapter' => function ($serviceManager) {
							$adapterFactory = new Zend\Db\Adapter\AdapterServiceFactory();
							$adapter = $adapterFactory->createService($serviceManager);

							\Zend\Db\TableGateway\Feature\GlobalAdapterFeature::setStaticAdapter($adapter);

							return $adapter;
						}
				),
		),

);
