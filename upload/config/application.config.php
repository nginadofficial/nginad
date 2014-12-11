<?php
/**
 * CDNPAL NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2015 CDNPAL Ltd. All Rights Reserved
 * @license GPLv3
 */
$request_path = $_SERVER['REQUEST_URI'];

$is_company = strpos(strtolower($request_path), "/company") === 0;
$is_legal = strpos(strtolower($request_path), "/legal") === 0;
$is_users = strpos(strtolower($request_path), "/users") === 0;
$is_signup = strpos(strtolower($request_path), "/signup") === 0;
$is_manager = strpos(strtolower($request_path), "/demand") === 0;
$is_publisher = strpos(strtolower($request_path), "/publisher") === 0;
$is_website = strpos(strtolower($request_path), "/websites") === 0;
$is_maintenance = strpos(strtolower($request_path), "/maintenance") === 0;
$is_auth = strpos(strtolower($request_path), "/auth") === 0;
$is_success = strpos(strtolower($request_path), "/success") === 0;
$is_delivery = strpos(strtolower($request_path), "/delivery") === 0;
$is_bid = strpos(strtolower($request_path), "/bid") === 0;
$is_report = strpos(strtolower($request_path), "/report") === 0;

/*
 * Configure AdCampaignTypes
 */

define('AD_TYPE_ANY_REMNANT', 		1);
define('AD_TYPE_IN_HOUSE_REMNANT', 	2);
define('AD_TYPE_RTB_REMNANT', 		3);
define('AD_TYPE_CONTRACT', 			4);

/*
 * Configure Device Types
*/

define('DEVICE_DESKTOP', 			2);
define('DEVICE_MOBILE', 			1);
define('DEVICE_TABLET', 			5);

/*
 * No Bid Reasons
*/

define('NOBID_UNKNOWN_ERROR', 			0);
define('NOBID_TECHNICAL_ERROR', 		1);
define('NOBID_INVALID_REQUEST', 		2);
define('KNOWN_WEB_SPIDER', 				3);
define('NOBID_AD_FRAUD', 				4);
define('NOBID_PROXY_IP', 				5);
define('NOBID_BAD_DEVICE', 				6);
define('NOBID_BAD_PUBLISHER', 			7);
define('NOBID_UNMATCHED_USER', 			8);

/*
 * We do not want autoloading. It does not work 
 * according to the documentation.
 * 
 * Manually load based on what is needed ONLY.
 */ 

$subdomain = null;

if (isset($_SERVER['HTTP_HOST'])):
	$dot_count = substr_count($_SERVER['HTTP_HOST'], '.');
	$url_parts = explode('.', $_SERVER['HTTP_HOST']);
	if (count($url_parts) == $dot_count + 1):
		$subdomain = strtolower($url_parts[0]);
	endif;
endif;

$modules = array();
$config_glob_paths = array();

if ($is_delivery):
	
	$modules = array( 'Delivery', 'GenericBuysidePartner' );
	$config_glob_paths = array(
			'config/autoload/{global,local}.php',
			'config/autoload/database.{local,staging,production}.php',
			'config/autoload/delivery.{local,staging,production}.php',
			'config/autoload/rtb.config.{local,staging,production}.php',
	);

elseif ($is_legal):
	
	$modules = array( 'WebsiteCustom' );
	$config_glob_paths = array(
			'config/autoload/{global,local}.php',
	);
	
elseif ($is_maintenance):

	$modules = array( 'Maintenance' );
	$config_glob_paths = array(
			'config/autoload/{global,local}.php',
			'config/autoload/database.{local,staging,production}.php',
			'config/autoload/email.{local,staging,production}.php',
	);
/*
 * Buy Side RTB partners can either be added by a unique subdomain name
 * that maps to the module handing their requests, or it can be
 * added by the seat id ( rtb_seat_id HTTP GET PARAMETER in /bid?rtb_seat_id=0004 )
 */
elseif (isset($_GET["secret_key"]) 
		&& (isset($_GET["rtb_seat_id"]) || $subdomain !== null)):
	
	$config 		= Zend\Config\Factory::fromFile('config/autoload/rtb.config.local.php');
	$secret_key 	= $_GET["secret_key"];
	$seat_bid_id 	= isset($_GET["rtb_seat_id"]) ? $_GET["rtb_seat_id"] : $subdomain;

	foreach ($config['buyside_rtb']['supply_partners'] as $partner_key => $partner_entry):
		if ($seat_bid_id == $partner_entry['buyer_id']
				&& $secret_key == $partner_entry['secret_key']):
				
			$response_seat_id = $partner_entry['response_seat_id'];
			/*
			 * Grab the buy side module corresponding to the 
			 * rtb_seat_bid GET param out of the rtb.config.php settings
			 * 
			 * http://server.nginad.com/bid?rtb_seat_id=0001
			 * maps to: GenericBuysidePartner
			 */
			$modules = array( $partner_entry['module_name'] );
			
		endif;
	endforeach;
	
	$config_glob_paths = array(
			'config/autoload/{global,local}.php',
			'config/autoload/database.{local,staging,production}.php',
			'config/autoload/delivery.{local,staging,production}.php',
			'config/autoload/rtb.config.{local,staging,production}.php',
	);
	
endif;

if (count($modules) == 0):
	// dashboard manager is default
	$modules = array(
			'DashboardManager',
			'ZfcRbac',
			'DoctrineModule',
			'DoctrineORMModule'
	);
	
	$config_glob_paths = array(
			'config/autoload/{global,local}.php',
			'config/autoload/database.{local,staging,production}.php',
			'config/autoload/delivery.{local,staging,production}.php',
			'config/autoload/zfcbac.{local,staging,production}.php',
			'config/autoload/navigation.{local,global}.php',
			'config/autoload/email.{local,staging,production}.php',
	);
	
endif;

return array(
    // This should be an array of module namespaces used in the application.
    'modules' => $modules,

    // These are various options for the listeners attached to the ModuleManager
    'module_listener_options' => array(
        // This should be an array of paths in which modules reside.
        // If a string key is provided, the listener will consider that a module
        // namespace, the value of that key the specific path to that module's
        // Module class.
        'module_paths' => array(
            './module',
        	'./module/BuySidePartners',
        	'./module/SellSidePartners',
            './vendor',
        ),

        // An array of paths from which to glob configuration files after
        // modules are loaded. These effectively overide configuration
        // provided by modules themselves. Paths may use GLOB_BRACE notation.
        
    	'config_glob_paths' => $config_glob_paths,

        // Whether or not to enable a configuration cache.
        // If enabled, the merged configuration will be cached and used in
        // subsequent requests.
        //'config_cache_enabled' => $booleanValue,

        // The key used to create the configuration cache file name.
        //'config_cache_key' => $stringKey,

        // Whether or not to enable a module class map cache.
        // If enabled, creates a module class map cache which will be used
        // by in future requests, to reduce the autoloading process.
        //'module_map_cache_enabled' => $booleanValue,

        // The key used to create the class map cache file name.
        //'module_map_cache_key' => $stringKey,

        // The path in which to cache merged configuration.
        //'cache_dir' => $stringPath,

        // Whether or not to enable modules dependency checking.
        // Enabled by default, prevents usage of modules that depend on other modules
        // that weren't loaded.
        // 'check_dependencies' => true,
    ),

    // Used to create an own service manager. May contain one or more child arrays.
    //'service_listener_options' => array(
    //     array(
    //         'service_manager' => $stringServiceManagerName,
    //         'config_key'      => $stringConfigKey,
    //         'interface'       => $stringOptionalInterface,
    //         'method'          => $stringRequiredMethodName,
    //     ),
    // )

   // Initial configuration with which to seed the ServiceManager.
   // Should be compatible with Zend\ServiceManager\Config.
   // 'service_manager' => array(),
	'rtb_seat_id'=> isset($seat_bid_id) ? $seat_bid_id : null,
		
	'response_seat_id'=> isset($response_seat_id) ? $response_seat_id : null
);