<?php 

/*
 * Configure Test User
*/

define('TEST_USER_PUBLISHER', 		20);
define('TEST_USER_DEMAND', 			21);

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
 * Configure OpenRTB Banner Types
*/

define('BANNER_TYPE_XHTML_TEXT_AD', 		1);
define('BANNER_TYPE_XHTML_BANNER_AD', 		2);
define('BANNER_TYPE_JAVASCRIPT_AD', 		3);
define('BANNER_TYPE_IFRAME_AD', 			4);


/*
 * Native Data Asset Types - 7.6 IAB Native Spec
 */

define('DATA_ASSET_SPONSORED', 		1);
define('DATA_ASSET_DESC', 			2);
define('DATA_ASSET_RATING', 		3);
define('DATA_ASSET_LIKES', 			4);
define('DATA_ASSET_DOWNLOADS', 		5);
define('DATA_ASSET_PRICE', 			6);
define('DATA_ASSET_SALEPRICE', 		7);
define('DATA_ASSET_PHONE', 			8);
define('DATA_ASSET_ADDRESS', 		9);
define('DATA_ASSET_DESC2', 			10);
define('DATA_ASSET_DISPLAY_URL', 	11);
define('DATA_ASSET_CTATEXT', 		12);

