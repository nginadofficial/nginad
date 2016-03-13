<?php
/**
 * NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2016 NginAd Foundation. All Rights Reserved
 * @license GPLv3
 */

/**
 * This makes our life easier when dealing with paths. Everything is relative
 * to the application root now.
 */

/*
 * Some Zend Framework author got overzealous with warnings.
 * See: https://github.com/zendframework/zend-mvc/issues/87
 */
error_reporting(E_ALL & ~E_USER_DEPRECATED);

ini_set('display_errors', true);
chdir(dirname(__DIR__));

// Setup autoloading
require 'init_autoloader.php';

// Run the application!
Zend\Mvc\Application::init(require 'config/application.config.php')->run();
