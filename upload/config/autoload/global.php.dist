<?php
/**
 * CDNPAL NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2015 CDNPAL Ltd. All Rights Reserved
 * @license GPLv3
 */

/**
 * Global Configuration Override
 *
 * You can use this file for overriding configuration values from modules, etc.
 * You would place values in here that are agnostic to the environment and not
 * sensitive to security.
 *
 * @NOTE: In practice, this file will typically be INCLUDED in your source
 * control, so do not include passwords or other sensitive information in this
 * file.
 */

return array(

		'roles' => array(
				'member' => "member",
				'admin' => "admin",
		),
		'settings' => array(
				'publisher' => array(
						'auto_approve_zones' 	=> true,
						'auto_approve_websites' => true
				),
				'rtb' => array(
						// rtb encrytion key
						'encryption_key'				=> 'my_encryption_key',
						// default US dollars
						'auction_currency' 				=> "USD",
						'second_price_auction' 			=> false,
						// integral traq score 500+
						'third_party_traq'				=> false,
						// enable project honeypot protection
						'project_honeypot_protected'	=> false,
						/*
						 * Project Honey Pot key, register at http://www.projecthoneypot.org for free to get one
						 * Then visit: http://www.projecthoneypot.org/httpbl_configure.php
						 * to get the API key to paste here
						 */ 
						'project_honeypot_api_key'		=> '[MY_API_KEY]'
				),
				'shell' => array(
						'has_curl_on_path'		=> false
				)
		),
		'maintenance' => array(
				'secret_key_crontab' => 'nginadxyz',
				'tasks' => array(
					// all maintenance tasks should be in minutes
					'daily' => array(
						'interval_in_minutes' => 1440,
						'maintenance_function' => 'dailyMaintenanceAction'
					),
					'ten_minute' => array(
						'interval_in_minutes' => 10,
						'maintenance_function' => 'tenMinuteMaintenanceAction'
					),
				)
		),
		'system' => array(
				'debug' 												=> false, // simple debug information to track errors/status.
				'debug_verbose'                     					=> false, // maximum debug information.
				// delay between maintenance updates in minutes
				'maintenance_updates_minutes' 							=> 15,
				// default demand 30% markup
				'default_demand_markup_rate' 							=> 0.30,
				// default publisher 30% markup
				'default_publisher_markup_rate' 						=> 0.30,
				// our house user ids are exempt from being marked up
				'markup_exempt_userid_list'								=> array(1),
				// our house publisher ids are exempt from being marked up
				'markup_exempt_publisher_info_id_list'					=> array(),
				// default publisher 0% impressions lost to client network bounce rate
				'default_publisher_impressions_network_loss_rate' 		=> 0.00,
				// our house publisher ids are exempt from being scrubbed as network loss
				'network_loss_exempt_publisher_info_id_list'			=> array(),
				// version
				'version_id'											=> '1.5'
		),
);
