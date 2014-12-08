<?php
/**
 * CDNPAL NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2015 CDNPAL Ltd. All Rights Reserved
 * @license GPLv3
 */

namespace util;

class BannerOptions {

    public static $iab_banner_options = array(
    		"468x60"=>"IAB Full Banner (468 x 60)",
    		"120x600"=>"IAB Skyscraper (120 x 600)",
    		"728x90"=>"IAB Leaderboard (728 x 90)",
    		"120x90"=>"IAB Button 1 (120 x 90)",
            "120x60"=>"IAB Button 2 (120 x 60)",
            "234x60"=>"IAB Half Banner (234 x 60)",
            "88x31"=>"IAB Micro Bar (88 x 31)",
            "125x125"=>"IAB Square Button (125 x 125)",
            "120x240"=>"IAB Vertical Banner (120 x 240)",
            "180x150"=>"IAB Rectangle (180 x 150)",
            "300x250"=>"IAB Medium Rectangle (300 x 250)",
            "336x280"=>"IAB Large Rectangle (336 x 280)",
            "240x400"=>"IAB Vertical Rectangle (240 x 400)",
            "250x250"=>"IAB Square Pop-up (250 x 250)",
            "160x600"=>"IAB Wide Skyscraper (160 x 600)",
            "720x300"=>"IAB Pop-Under (720 x 300)",
            "300x100"=>"IAB 3:1 Rectangle (300 x 100)",
            "-"=>"Custom"
    );

    public static $iab_mobile_phone_banner_options = array(
    		"320x50"=>"Mobile Phone Banner (320 x 50)",
            "300x50"=>"Mobile Phone Thin Banner (300 x 50)",
    		"300x250"=>"Mobile Phone Medium Rectangle (300 x 250)",
    		"320x480"=>"Mobile Phone Full Screen (320 x 480)",
            "300x480"=>"Mobile Phone Thin Full Screen (300 x 480)",
    		"-"=>"Custom"
    );

    public static $iab_mobile_tablet_banner_options = array(
    		"728x90"=>"Mobile Tablet Leaderboard (728 x 90)",
    		"300x250"=>"Mobile Tablet Medium Rectangle (300 x 250)",
    		"300x50"=>"Mobile Tablet Banner (300 x 50)",
    		"728x1024"=>"Mobile Tablet Full Screen (728 x 1024)",
    		"-"=>"Custom"
    );

    public static $mobile_options = array(
    		"0"=>"Desktop",
    		"1"=>"Mobile Phone",
    		"2"=>"Mobile Tablet",
    		"3"=>"Native App ( iOS )",
    		"4"=>"Native App ( Android )"
    );

    public static $mimes = array(
    		"video/x-flv",
    		"video/mp4",
    		"application/x-shockwave-flash",
    		"application/javascript",
    		"video/ogg",
    		"video/webm",
    		"video/mp4",
    		"video/x-mswmv",
    		"video/x-flv"
    
    );

    public static $protocols = array(
    		"1" => "VAST 1.0",
    		"2" => "VAST 2.0",
    		"3" => "VAST 3.0",
    		"4" => "VAST 1.0 Wrapper",
    		"5" => "VAST 2.0 Wrapper",
    		"6" => "VAST 3.0 Wrapper"
    
    );
    
    public static $apis_supported = array(
		    "1" => "VPAID 1.0",
		    "2" => "VPAID 2.0",
		    "3" => "MRAID-1",
		    "4" => "ORMMA",
		    "5" => "MRAID-2"

    );

    public static $delivery_methods = array(
    		"1" => "Streaming",
    		"2" => "Progressive"
    );
    
    public static $playback_methods = array(
    		"1" => "Auto-play sound on",
    		"2" => "Auto-play sound off",
    		"3" => "Click-to-play",
    		"4" => "Mouse-over"
    );
    
    public static $start_delay = array(
    		"" => "[ CHOOSE ONE ]",
    		"0" => "Pre-roll",
    		"-1" => "Generic mid-roll",
    		"-2" => "Generic Post-roll"
    );
    
    
    public static $linearity = array(
    		"" => "[ CHOOSE ONE ]",
    		"1" => "Linear/In-stream",
    		"2" => "Non-Linear/Overlay"
    );
    
    public static $fold_pos = array(
    		"" => "[ CHOOSE ONE ]",
    		"0" => "Unknown",
    		"1" => "Above the fold",
    		"3" => "Below the fold",
    		"4" => "Header",
    		"5" => "Footer",
    		"6" => "Sidebar",
    		"7" => "Fullscreen",
    );
    
    
}
