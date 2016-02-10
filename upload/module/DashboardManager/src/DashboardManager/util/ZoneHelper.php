<?php
/**
 * NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2016 NginAd Foundation. All Rights Reserved
 * @license GPLv3
 */

namespace util;

class ZoneHelper {

	public static $header_bidding_providers = array(
			
			"appnexus"		=> 'AppNexus',
			"rubicon"		=> 'Rubicon',
			"openx"			=> 'OpenX',
			"pubmatic"		=> 'Pubmatic',
			"criteo"		=> 'Criteo',
			"yieldbot"		=> 'Yieldbot',
			"indexExchange" => 'Casale',
			"sovrn"			=> 'Sovrn',
			"aol"			=> 'AOL',
			"pulsepoint"	=> 'PulsePoint'
	);
	
    public static $header_bidding_adxs = array(
    		"appnexus" => array(
    			'placementId',
    			'randomeKey'
    		),
    		"rubicon" => array(
    			'rp_account',
    			'rp_site',
    			'rp_zonesize',
    		),
    		"openx" => array(
    			'jstag_url',
    			'pgid',
    			'unit'
    		),
    		"pubmatic" => array(
    			'publisherId',
    			'adSlot'
    		),
    		"criteo" => array(
    			'nid',
    			'cookiename',
    			'varname'
    		),
    		"yieldbot" => array(
    			'psn',
    			'slot'
    		),
    		"indexExchange" => array(
    			'id',
    			'siteID',
    			'tier2SiteID',
    			'tier3SiteID'
    		),
    		"sovrn" => array(
    			'tagid'
    		),
    		"aol" => array(
    			'placement',
    			'network',
    			'sizeId',
    			'alias'
    		),
    		"pulsepoint" => array(
    			'cf',
    			'cp',
    			'ct'
    		)
    );
    
	public static function getHeaderBiddingItems($request, $PublisherAdZone, $publisher_ad_zone_page_header_id, $header_bidding_types, $key_name) {
	
		$HeaderBiddingAdUnitFactory = \_factory\HeaderBiddingAdUnit::get_instance();
		
		$header_bidding_item_list = array();
	
		$div_id = self::unique_js_filename() . '-' . self::unique_js_filename();
		
		foreach ($header_bidding_types as $id => $bidder_type):
		
			$HeaderBiddingAdUnitID				= $request->getPost('HeaderBiddingAdUnitID' . $key_name . $id);
			$params = array();
			$params['HeaderBiddingAdUnitID'] 	= $HeaderBiddingAdUnitID;
			$HeaderBiddingAdUnit 				= $HeaderBiddingAdUnitFactory->get_row($params);
			
			if ($HeaderBiddingAdUnit != null):
			
				$div_id = $HeaderBiddingAdUnit->DivID;
				break;
				
			endif;
 		
		endforeach;
		
		foreach ($header_bidding_types as $id => $bidder_type):
		
			$header_bidding_item = array();
				
			$HeaderBiddingAdUnitID							= $request->getPost('HeaderBiddingAdUnitID' . $key_name . $id);
		
			if ($HeaderBiddingAdUnitID != null && $HeaderBiddingAdUnitID != 'new' && intval($HeaderBiddingAdUnitID)):
			
				$params = array();
				$params['HeaderBiddingAdUnitID'] 			= $HeaderBiddingAdUnitID;
				$HeaderBiddingAdUnit 						= $HeaderBiddingAdUnitFactory->get_row($params);
				
				if ($HeaderBiddingAdUnit->PublisherAdZoneID != $PublisherAdZone->PublisherAdZoneID):
					
					$HeaderBiddingAdUnitID = 'new';
					
				endif;
				
			else:
				
				$HeaderBiddingAdUnitID = 'new';
				
			endif;

			$header_bidding_item['HeaderBiddingPageID'] 	= $publisher_ad_zone_page_header_id;
			$header_bidding_item['HeaderBiddingAdUnitID']	= $HeaderBiddingAdUnitID;
			$header_bidding_item['PublisherAdZoneID'] 		= $PublisherAdZone->PublisherAdZoneID;
			$header_bidding_item['DivID'] 					= $div_id;
			$header_bidding_item['Height'] 					= $PublisherAdZone->Height;
			$header_bidding_item['Width'] 					= $PublisherAdZone->Width;
			$header_bidding_item['AdTag'] 					= $request->getPost('NetworkAdTag' . $key_name . $id);

			if ($bidder_type == 'appnexus'):
				
				$header_bidding_item['AdExchange'] 			= 'appnexus';
			
				$placement_id 								= $request->getPost('PlacementID' . $key_name . $id);
				if (!empty($placement_id)):
					$header_bidding_item['CustomParams']['placementId']					= $placement_id;
				else:
					// required
					continue;
				endif;
				
				$random_e_key 								= $request->getPost('RandomKey' . $key_name . $id);
				if (!empty($random_e_key)):
					$header_bidding_item['CustomParams']['randomeKey']					= $random_e_key;
				endif;
			
			elseif ($bidder_type == 'rubicon'):
			
				$header_bidding_item['AdExchange'] 			= 'rubicon';
			
				$rp_account 								= $request->getPost('RpAccount' . $key_name . $id);
				if (!empty($rp_account)):
					$header_bidding_item['CustomParams']['rp_account']					= $rp_account;
				else:
					// required
					continue;
				endif;
				
				$rp_site 									= $request->getPost('RpSite' . $key_name . $id);
				if (!empty($rp_site)):
					$header_bidding_item['CustomParams']['rp_site']						= $rp_site;
				else:
					// required
					continue;
				endif;
				
				$rp_zonesize 								= $request->getPost('RpZoneSize' . $key_name . $id);
				if (!empty($rp_zonesize)):
					$header_bidding_item['CustomParams']['rp_zonesize']					= $rp_zonesize;
				else:
					// required
					continue;
				endif;
				
			elseif ($bidder_type == 'openx'):
			
				$header_bidding_item['AdExchange'] 			= 'openx';
			
				$jstag_url 									= $request->getPost('JsTagUrl' . $key_name . $id);
				if (!empty($jstag_url)):
					$header_bidding_item['CustomParams']['jstag_url']					= $jstag_url;
				else:
					// required
					continue;
				endif;
				
				$pgid 										= $request->getPost('PgId' . $key_name . $id);
				if (!empty($pgid)):
					$header_bidding_item['CustomParams']['pgid']						= $pgid;
				else:
					// required
					continue;
				endif;
				
				$unit 										= $request->getPost('Unit' . $key_name . $id);
				if (!empty($unit)):
					$header_bidding_item['CustomParams']['unit']						= $unit;
				else:
					// required
					continue;
				endif;
				
			elseif ($bidder_type == 'pubmatic'):
			
				$header_bidding_item['AdExchange'] 			= 'pubmatic';
			
				$publisher_id 								= $request->getPost('PublisherID' . $key_name . $id);
				if (!empty($publisher_id)):
					$header_bidding_item['CustomParams']['publisherId']					= $publisher_id;
				else:
					// required
					continue;
				endif;
				
				$ad_slot 									= $request->getPost('AdSlot' . $key_name . $id);
				if (!empty($ad_slot)):
					$header_bidding_item['CustomParams']['adSlot']						= $ad_slot;
				else:
					// required
					continue;
				endif;

			elseif ($bidder_type == 'criteo'):
			
				$header_bidding_item['AdExchange'] 			= 'criteo';
			
				$nid 										= $request->getPost('NID' . $key_name . $id);
				if (!empty($nid)):
					$header_bidding_item['CustomParams']['nid']							= $nid;
				else:
					// required
					continue;
				endif;
				
				$cookie_name 								= $request->getPost('CookieName' . $key_name . $id);
				if (!empty($cookie_name)):
					$header_bidding_item['CustomParams']['cookiename']					= $cookie_name;
				else:
					// required
					continue;
				endif;

				$var_name 									= $request->getPost('VarName' . $key_name . $id);
				if (!empty($var_name)):
					$header_bidding_item['CustomParams']['varname']						= $var_name;
				endif;
				
			elseif ($bidder_type == 'yieldbot'):
			
				$header_bidding_item['AdExchange'] 			= 'yieldbot';
			
				$psn 										= $request->getPost('Psn' . $key_name . $id);
				if (!empty($psn)):
					$header_bidding_item['CustomParams']['psn']							= $psn;
				else:
					// required
					continue;
				endif;
				
				$slot 										= $request->getPost('Slot' . $key_name . $id);
				if (!empty($slot)):
					$header_bidding_item['CustomParams']['slot']						= $slot;
				else:
					// required
					continue;
				endif;
				
			elseif ($bidder_type == 'indexExchange'):
			
				$header_bidding_item['AdExchange'] 			= 'indexExchange';
			
				$_id 										= $request->getPost('ID' . $key_name . $id);
				if (!empty($_id)):
					$header_bidding_item['CustomParams']['id']							= $_id;
				else:
					// required
					continue;
				endif;
				
				$site_id									= $request->getPost('SiteID' . $key_name . $id);
				if (!empty($site_id)):
					$header_bidding_item['CustomParams']['siteID']						= $site_id;
				else:
					// required
					continue;
				endif;
				
				$tier2_site_id 								= $request->getPost('Tier2SiteID' . $key_name . $id);
				if (!empty($tier2_site_id)):
					$header_bidding_item['CustomParams']['tier2SiteID']					= $tier2_site_id;
				endif;
				
				$tier3_site_id 								= $request->getPost('Tier3SiteID' . $key_name . $id);
				if (!empty($tier3_site_id)):
					$header_bidding_item['CustomParams']['tier3SiteID']					= $tier3_site_id;
				endif;
				
			elseif ($bidder_type == 'sovrn'):
				
				$header_bidding_item['AdExchange'] 			= 'sovrn';
					
				$tag_id 									= $request->getPost('TagID' . $key_name . $id);
				if (!empty($tag_id)):
					$header_bidding_item['CustomParams']['tagid']						= $tag_id;
				else:
					// required
					continue;
				endif;
				
			elseif ($bidder_type == 'aol'):
			
				$header_bidding_item['AdExchange'] 			= 'aol';
			
				$placement 									= $request->getPost('Placement' . $key_name . $id);
				if (!empty($placement)):
					$header_bidding_item['CustomParams']['placement']					= $placement;
				else:
					// required
					continue;
				endif;
				
				$network									= $request->getPost('Network' . $key_name . $id);
				if (!empty($network)):
					$header_bidding_item['CustomParams']['network']						= $network;
				else:
					// required
					continue;
				endif;
				
				$size_id 									= $request->getPost('SizeID' . $key_name . $id);
				if (!empty($size_id)):
					$header_bidding_item['CustomParams']['sizeId']						= $size_id;
				endif;
				
				$alias 										= $request->getPost('Alias' . $key_name . $id);
				if (!empty($alias)):
					$header_bidding_item['CustomParams']['alias']						= $alias;
				endif;

			elseif ($bidder_type == 'pulsepoint'):
			
				$header_bidding_item['AdExchange'] 			= 'pulsepoint';
			
				$cf		 									= $request->getPost('Cf' . $key_name . $id);
				if (!empty($cf)):
					$header_bidding_item['CustomParams']['cf']							= $cf;
				else:
					// required
					continue;
				endif;
				
				$cp											= $request->getPost('Cp' . $key_name . $id);
				if (!empty($cp)):
					$header_bidding_item['CustomParams']['cp']							= $cp;
				else:
					// required
					continue;
				endif;
				
				$ct											= $request->getPost('Ct' . $key_name . $id);
				if (!empty($ct)):
					$header_bidding_item['CustomParams']['ct']							= $ct;
				else:
					// required
					continue;
				endif;

			endif;

			$header_bidding_item['CustomParams']				= serialize($header_bidding_item['CustomParams']);
			
			$header_bidding_item_list[$header_bidding_item['AdExchange']][] 				= $header_bidding_item;
			
		endforeach;

		return $header_bidding_item_list;
	}
	
	public static function getHeaderBiddingTypes($request, $keys, $key_name) {
		
		$native_asset_types = array();
		
		foreach ($keys as $id):
		
			$asset_type = $request->getPost('HBType' . $key_name . $id);
			
			if ($asset_type == null):
				continue;
			endif;
		
			$native_asset_types[$id] = $asset_type;
			
		endforeach;
		
		return $native_asset_types;
	}
	
	public static function getHeaderBiddingKeys($post, $key_name) {
		
		$all_keys = array();
		
		foreach ($post as $key => $value):
	
			$pos = strpos($key, $key_name);
			
			if ($pos === false):
				continue;
			endif;
			 
			$id = substr($key, $pos + strlen($key_name));
			
			$all_keys[$id] = true;
			
		endforeach;
		
		return array_keys($all_keys);
		
	}
	
	public static function unique_js_filename() {
		$str = self::rand_letter();
		$str.= rand(100, 999);
		$str.= self::rand_letter();
		$str.= rand(100, 999);
	
		return $str;
	}
	
	private static function rand_letter() {
		$int = rand(0,24);
		$a_z = "abcdefghijklmnopqrstuvwxyz";
		$rand_letter = $a_z[$int];
		return $rand_letter;
	}

}