<?php
/**
 * NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2016 NginAd Foundation. All Rights Reserved
 * @license GPLv3
 */

namespace util;

class Countries {

    public static $allcountries = array("US"=>"United States", "CA"=>"Canada", "GB"=>"United Kingdom", "AU"=>"Australia", "AD"=>"Andorra", "AE"=>"United ArabEmirates", "AF"=>"Afghanistan", "AG"=>"Antigua and Barbuda", "AI"=>"Anguilla", "AL"=>"Albania", "AM"=>"Armenia", "AN"=>"Netherlands Antilles", "AO"=>"Angola", "AQ"=>"Antarctica", "AR"=>"Argentina", "AS"=>"American Samoa", "AT"=>"Austria", "AU"=>"Australia", "AW"=>"Aruba", "AZ"=>"Azerbaijan",
    		"BA"=>"Bosnia and Herzegowina", "BB"=>"Barbados", "BD"=>"Bangladesh", "BE"=>"Belgium", "BF"=>"Burkina Faso", "BG"=>"Bulgaria", "BH"=>"Bahrain", "BI"=>"Burundi", "BJ"=>"Benin", "BM"=>"Bermuda", "BN"=>"Brunei Darussalam", "BO"=>"Bolivia", "BR"=>"Brazil", "BS"=>"Bahamas", "BT"=>"Bhutan", "BV"=>"Bouvet Island", "BW"=>"Botswana",
    		"BY"=>"Belarus", "BZ"=>"Belize", "CA"=>"Canada", "CC"=>"Cocoa (Keeling) Islands", "CF"=>"Central African Republic", "CG"=>"Congo", "CH"=>"Switzerland", "CI"=>"Cote DivoireCroatia (Hrvatska)", "CK"=>"Cook Islands", "CL"=>"Chile", "CM"=>"Cameroon", "CN"=>"China", "CO"=>"Colombia", "CR"=>"Costa Rica", "CU"=>"Cuba", "CV"=>"Cape Verde",
    		"CX"=>"Christmas Island", "CY"=>"Cyprus", "CZ"=>"Czech Republic", "DE"=>"Germany", "DJ"=>"Djibouti", "DK"=>"Denmark", "DM"=>"Dominica", "DO"=>"Dominican Republic", "DZ"=>"Algeria", "EC"=>"Ecuador", "EE"=>"Estonia", "EG"=>"Egypt", "EH"=>"Western Sahara", "ER"=>"Eritrea", "ES"=>"Spain", "ET"=>"Ethiopia", "FI"=>"Finland", "FJ"=>"Fiji",
    		"FK"=>"Falkland Islands (Malvinas)", "FM"=>"Micronesia, Federated States of", "FO"=>"Faroe Islands", "FR"=>"France", "FX"=>"France, Metropolitan", "GA"=>"Gabon", "GD"=>"Grenada", "GE"=>"Georgia", "GF"=>"French Guiana", "GH"=>"Ghana", "GI"=>"Gibraltar", "GL"=>"Greenland", "GM"=>"Gambia", "GN"=>"Guinea", "GP"=>"Guadeloupe",
    		"GQ"=>"Equatorial Guinea", "GR"=>"Greece", "GS"=>"SouthGeorgia", "GT"=>"Guatemala", "GU"=>"Guam", "GW"=>"Guinea-Bissau", "GY"=>"Guyana", "HK"=>"Hong Kong", "HM"=>"Heard and Mc Donald Islands", "HN"=>"Honduras", "HT"=>"Haiti", "HU"=>"Hungary", "ID"=>"Indonesia", "IE"=>"Ireland", "IL"=>"Israel",
    		"IN"=>"India", "IO"=>"British Indian Ocean Territory", "IQ"=>"Iraq", "IR"=>"Iran (Islamic Republic of)", "IS"=>"Iceland", "IT"=>"Italy", "JM"=>"Jamaica", "JO"=>"Jordan", "JP"=>"Japan", "KE"=>"Kenya", "KG"=>"Kyrgyzstan", "KH"=>"Cambodia", "KI"=>"Kiribati", "KM"=>"Comoros", "KN"=>"Saint Kitts and Nevis", "KP"=>"Korea, Democratic PR",
    		"KR"=>"Korea, Republic of", "KW"=>"Kuwait", "KY"=>"Cayman Islands", "KZ"=>"Kazakhstan", "LA"=>"Lao Peoples Democratic Republic", "LB"=>"Lebanon", "LC"=>"Saint Lucia", "LI"=>"Liechtenstein", "LK"=>"Sri Lanka", "LR"=>"Liberia", "LS"=>"Lesotho", "LT"=>"Lithuania", "LU"=>"Luxembourg", "LV"=>"Latvia", "LY"=>"Libyan Arab Jamahiriya", "MA"=>"Morocco",
    		"MC"=>"Monaco", "MD"=>"Moldova, Republic of", "MG"=>"Madagascar", "MH"=>"Marshall Islands", "MK"=>"Macedonia", "ML"=>"Mali", "MM"=>"Myanmar", "MN"=>"Mongolia", "MO"=>"Macau", "MP"=>"Northern Mariana Islands", "MQ"=>"Martinique", "MR"=>"Mauritania", "MS"=>"Montserrat", "MT"=>"Malta", "MU"=>"Mauritius", "MV"=>"Maldives",
    		"MW"=>"Malawi", "MX"=>"Mexico", "MY"=>"Malaysia", "MZ"=>"Mozambique", "NA"=>"Namibia", "NC"=>"New Caledonia", "NE"=>"Niger", "NF"=>"Norfolk Island", "NG"=>"Nigeria", "NI"=>"Nicaragua", "NL"=>"Netherlands", "NO"=>"Norway", "NP"=>"Nepal", "NR"=>"Nauru", "NU"=>"Niue", "NZ"=>"New Zealand", "OM"=>"Oman", "PA"=>"Panama", "PE"=>"Peru", "PF"=>"French Polynesia",
    		"PG"=>"Papua New Guinea", "PH"=>"Philippines", "PK"=>"Pakistan", "PL"=>"Poland", "PM"=>"St. Pierre andMiquelon", "PN"=>"Pitcairn", "PR"=>"PuertoRico", "PT"=>"Portugal", "PW"=>"Palau", "PY"=>"Paraguay", "QA"=>"Qatar", "RE"=>"Reunion", "RO"=>"Romania", "RU"=>"Russian Federation", "RW"=>"Rwanda", "SA"=>"Saudi Arabia", "Sb"=>"Solomon Islands", "SC"=>"Seychelles",
    		"SD"=>"Sudan", "SE"=>"Sweden", "SG"=>"Singapore", "SH"=>"St. Helena", "SI"=>"Slovenia", "SJ"=>"Svalbard and Jan Mayen Islands", "SK"=>"Slovakia (Slovak Republic)", "SL"=>"Sierra Leone", "SM"=>"SanMarino", "SN"=>"Senegal", "SO"=>"Somalia", "SR"=>"Suriname", "ST"=>"Sao Tome and Principe", "SV"=>"El Salvador", "SY"=>"Syrian ArabRepublic", "SZ"=>"Swaziland",
    		"TC"=>"Turks and Caicos Islands", "TD"=>"Chad", "TF"=>"French Southern Territories", "TG"=>"Togo", "TH"=>"Thailand", "TJ"=>"Tajikistan", "TK"=>"Tokelau", "TM"=>"Turkmenistan", "TN"=>"Tunisia", "TO"=>"Tonga", "TP"=>"East Timor", "TR"=>"Turkey", "TT"=>"Trinidad and Tobago", "TV"=>"Tuvalu", "TW"=>"Taiwan", "TZ"=>"Tanzania, United Republic of",
    		"UA"=>"Ukraine", "UG"=>"Uganda", "UK"=>"United Kingdom", "UM"=>"United States Minor Outlying Islands", "US"=>"United States", "UY"=>"Uruguay", "UZ"=>"Uzbekistan", "VA"=>"Vatican City State(Holy See)", "VC"=>"Saint Vincent and the Grenadines", "VE"=>"Venezuela", "VG"=>"Virgin Islands (British)", "VI"=>"Virgin Islands (U.S.)", "VN"=>"Viet Nam",
    		"VU"=>"Vanuatu", "WF"=>"Wallis and Futuna Islands", "WS"=>"Samoa", "YE"=>"Yeman", "YT"=>"Mayotte", "YU"=>"Yugoslavia", "ZA"=>"South Africa", "ZM"=>"Zambia", "ZR"=>"Zaire", "ZW"=>"Zimbabwe");

}
