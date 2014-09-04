<?php
/**
 * CDNPAL NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2015 CDNPAL Ltd. All Rights Reserved
 * @license GPLv3
 */

namespace util;

class DeliveryFilterOptions {

    public static $foldpos_options = array(
    		""=>"Not available/applicable",
    		"1"=>"Completely Above the Fold",
    		"2"=>"Completely Below the Fold",
    		"3"=>"Partially Above the Fold"
    );

    public static $adtagtype_options = array(
    		""=>"None",
    		"JavaScript"=>"JavaScript",
    		"Iframe"=>"IFrame"
    );

    public static $iniframe_options = array(
    		""=>"No Preference",
    		"0"=>"No IFrame Ads",
    		"1"=>"Yes IFrame Ads"
    );

    public static $inmultiplenestediframes_options = array(
    		""=>"No Preference",
    		"0"=>"No Multiple Nested IFrame Ads",
    		"1"=>"Yes Multiple Nested IFrame Ads"
    );

    public static $timezone_options = array(
    		""=>"None",
    		"-12"=>"(GMT -12:00) Eniwetok, Kwajalein",
    		"-11"=>"(GMT -11:00) Midway Island, Samoa",
    		"-10"=>"(GMT -10:00) Hawaii",
    		"-9"=>"(GMT -9:00) Alaska",
    		"-8"=>"(GMT -8:00) Pacific Time (US &amp; Canada)",
    		"-7"=>"(GMT -7:00) Mountain Time (US &amp; Canada)",
    		"-6"=>"(GMT -6:00) Central Time (US &amp; Canada), Mexico City",
    		"-5"=>"(GMT -5:00) Eastern Time (US &amp; Canada), Bogota, Lima",
    		"-4"=>"(GMT -4:00) Atlantic Time (Canada), Caracas, La Paz",
    		"-3.5"=>"(GMT -3:30) Newfoundland",
    		"-3"=>"(GMT -3:00) Brazil, Buenos Aires, Georgetown",
    		"-2"=>"(GMT -2:00) Mid-Atlantic",
    		"-1"=>"(GMT -1:00 hour) Azores, Cape Verde Islands",
    		"0"=>"(GMT) Western Europe Time, London, Lisbon, Casablanca",
    		"1"=>"(GMT +1:00 hour) Brussels, Copenhagen, Madrid, Paris",
    		"2"=>"(GMT +2:00) Kaliningrad, South Africa",
    		"3"=>"(GMT +3:00) Baghdad, Riyadh, Moscow, St. Petersburg",
    		"3"=>"(GMT +3:30) Tehran",
    		"4"=>"(GMT +4:00) Abu Dhabi, Muscat, Baku, Tbilisi",
    		"4.5"=>"(GMT +4:30) Kabul",
    		"5"=>"(GMT +5:00) Ekaterinburg, Islamabad, Karachi, Tashkent",
    		"5.5"=>"(GMT +5:30) Bombay, Calcutta, Madras, New Delhi",
    		"5.75"=>"(GMT +5:45) Kathmandu",
    		"6"=>"(GMT +6:00) Almaty, Dhaka, Colombo",
    		"7"=>"(GMT +7:00) Bangkok, Hanoi, Jakarta",
    		"8"=>"(GMT +8:00) Beijing, Perth, Singapore, Hong Kong",
    		"9"=>"(GMT +9:00) Tokyo, Seoul, Osaka, Sapporo, Yakutsk",
    		"9.5"=>"(GMT +9:30) Adelaide, Darwin",
    		"10"=>"(GMT +10:00) Eastern Australia, Guam, Vladivostok",
    		"11"=>"(GMT +11:00) Magadan, Solomon Islands, New Caledonia",
    		"12"=>"(GMT +12:00) Auckland, Wellington, Fiji, Kamchatka"
    );

    public static $pmpenable_options = array(
    		""=>"No Preference",
    		"0"=>"PMP Enable Off",
    		"1"=>"PMP Enable On"
    );

    public static $secure_options = array(
    		""=>"No Preference",
    		"0"=>"https:// Secure Off",
    		"1"=>"https:// Secure On"
    );

    public static $optout_options = array(
    		""=>"No Preference",
    		"0"=>"Accept Users who have not Opted Out",
    		"1"=>"Accept OptOut Users"
    );

    public static $vertical_options = array(
    		""=>"No Preference",
    		"1"=>"Not Applicable",
            "2"=>"Automotive",
            "3"=>"Business and Finance",
            "8"=>"Education",
            "9"=>"Employment and Career",
            "10"=>"Entertainment and Leisure",
            "12"=>"Gaming",
            "14"=>"Health and Fitness",
            "16"=>"Home and Garden",
            "18"=>"Men's Interest",
            "21"=>"Music",
            "23"=>"News",
            "24"=>"Parenting and Family",
            "27"=>"Real Estate",
            "28"=>"Reference",
            "29"=>"Food and Dining",
            "31"=>"Shopping",
            "32"=>"Social Networking",
            "33"=>"Sports",
            "34"=>"Technology",
            "36"=>"Travel",
            "38"=>"Women's Interest"
    );
    
    public static $vertical_map = array(
    		/*
			 * also covers 1-x for sub-categories
			 * get the first 4-5 chars substring and
			 * only compare the main categories
			 *
			 * Ex: IAB10-2 becomes IAB10 and matches to 16
			 */
				
			"IAB1"=>"Arts & Entertainment",  			
			"IAB2"=>"Automotive",			
			"IAB3"=>"Business",			 
			"IAB4"=>"Careers",			
			"IAB5"=>"Education",			
			"IAB6"=>"Family & Parenting",			
			"IAB7"=>"Health & Fitness",			
			"IAB8"=>"Food & Drink",			
			"IAB9"=>"Hobbies & Interests",			
			"IAB10"=>"Home & Garden",			
			"IAB11"=>"Law, Gov't & Politics",		
			"IAB12"=>"News",			
			"IAB13"=>"Personal Finance",			
			"IAB14"=>"Society",			 
			"IAB15"=>"Science",			
			"IAB16"=>"Pets",			
			"IAB16"=>"Sports",			
			"IAB18"=>"Style & Fashion",			
			"IAB19"=>"Technology & Computing",			
			"IAB20"=>"Travel",			
			"IAB21"=>"Real Estate",			
			"IAB22"=>"Shopping",			
			"IAB23"=>"Religion & Spirituality",			
			"IAB24"=>"Uncategorized",			
			"IAB25"=>"Non-Standard Content",			
			"IAB26"=>"Illegal Content"
    );
    
     public static $optout_options3 = array(
    		""=>"No Preference",
    		"0"=>"Accept Users who have not Opted Out",
    		"1"=>"Accept OptOut Users"
    );
    
    public static $partner_type = array(
    
			 	"1" =>"ad network",
			 	"2" =>"brand manager for digital",
			 	"3" =>"self-serve",
			 	"4" =>"marketing manager"
    );

}

?>