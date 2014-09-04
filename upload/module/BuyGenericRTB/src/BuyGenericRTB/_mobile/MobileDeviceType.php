<?php
/**
 * CDNPAL NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2015 CDNPAL Ltd. All Rights Reserved
 * @license GPLv3
 */

namespace mobileutil;

class MobileDeviceType
{

    public static function isPhone($model_name) {

        $model_name = strtolower($model_name);

        foreach (self::$phone_devices as $phone_device):

            if (strpos($model_name, strtolower($phone_device)) !== false):

                return true;

            endif;

        endforeach;

        return false;

    }

    public static function isTablet($model_name) {

        $model_name = strtolower($model_name);

        foreach (self::$tablet_devices as $tablet_device):

            if (strpos($model_name, strtolower($tablet_device)) !== false):

                return true;

            endif;

        endforeach;

        return false;

    }

    public static $phone_devices = array(
            'Phone',
    		'iPhone',
    		'BlackBerry',
    		'HTC',
    		'Nexus',
    		'Dell',
    		'Motorola',
    		'Samsung',
    		'LG',
    		'Sony',
    		'Asus',
    		'Micromax',
    		'Palm',
    		'Vertu',
    		'Pantech',
    		'Fly',
    		'SimValley',
    		'GenericPhone'
    );

    /**
     * List of tablet devices.
     *
     * @var array
    */
    public static $tablet_devices = array(
            'Tablet',
    		'iPad',
    		'NexusTablet',
    		'SamsungTablet',
            'Kindle',
            'SurfaceTablet',
            'HPTablet',
            'AsusTablet',
            'BlackBerryTablet',
            'HTCtablet',
            'MotorolaTablet',
            'NookTablet',
            'AcerTablet',
            'ToshibaTablet',
            'LGTablet',
            'FujitsuTablet',
            'PrestigioTablet',
            'LenovoTablet',
            'YarvikTablet',
            'MedionTablet',
            'ArnovaTablet',
            'IRUTablet',
            'MegafonTablet',
            'EbodaTablet',
            'AllViewTablet',
            'ArchosTablet',
            'AinolTablet',
            'SonyTablet',
            'CubeTablet',
            'CobyTablet',
            'MIDTablet',
            'SMiTTablet',
            'RockChipTablet',
            'FlyTablet',
            'bqTablet',
            'HuaweiTablet',
            'NecTablet',
            'PantechTablet',
            'BronchoTablet',
            'VersusTablet',
            'ZyncTablet',
            'PositivoTablet',
            'NabiTablet',
            'KoboTablet',
            'DanewTablet',
            'TexetTablet',
            'PlaystationTablet',
            'GalapadTablet',
            'MicromaxTablet',
            'KarbonnTablet',
            'AllFineTablet',
            'PROSCANTablet',
            'YONESTablet',
            'ChangJiaTablet',
            'GUTablet',
            'PointOfViewTablet',
            'OvermaxTablet',
            'TelstraTablet',
            'GenericTablet'
    );


}
