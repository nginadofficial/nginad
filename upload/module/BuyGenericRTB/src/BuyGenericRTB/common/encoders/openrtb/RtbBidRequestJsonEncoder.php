<?php
/**
 * CDNPAL NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2015 CDNPAL Ltd. All Rights Reserved
 * @license GPLv3
 */

namespace buyrtb\encoders\openrtb;

class RtbBidRequestJsonEncoder {
	
	public static function execute(\model\openrtb\RtbBidRequest &$RtbBidRequest) {
	
		$bid_request = array();
	
		$bid_request["id"] 		= $RtbBidRequest->id;
	
		$bid_request["imp"] 	= self::getRtbBidRequestImpList($RtbBidRequest);
	
		if ($RtbBidRequest->RtbBidRequestSite != null):
	
			$bid_request["site"] 	= self::getRtbBidRequestSite($RtbBidRequest->RtbBidRequestSite);
	
		endif;
	
		if ($RtbBidRequest->RtbBidRequestDevice != null):
	
			$bid_request["device"] 	= self::getRtbBidRequestDevice($RtbBidRequest->RtbBidRequestDevice);
	
		endif;
	
		if ($RtbBidRequest->RtbBidRequestUser != null):
	
			$bid_request["user"] 	= self::getRtbBidRequestUser($RtbBidRequest->RtbBidRequestUser);
	
		endif;
	
		self::setArrayParam($RtbBidRequest, $bid_request, 'at');
	
		if (!empty($RtbBidRequest->cur) && is_array($RtbBidRequest->cur)):
			$bid_request["cur"] = $RtbBidRequest->cur;
		endif;
	
		if ($RtbBidRequest->RtbBidRequestUser != null):
	
			$bid_request["regs"] 	= self::getRtbBidRequestRegs($RtbBidRequest->RtbBidRequestRegs);
	
		endif;
	
	
		$result = json_encode($bid_request);
	
		return $result;
	
	}
	
	private static function getRtbBidRequestImpList(\model\openrtb\RtbBidRequest &$RtbBidRequest) {
		
		$impression_list = array();
		
		foreach ($RtbBidRequest->RtbBidRequestImpList as $RtbBidRequestImp):
			
			$impression 				=  array();
			$impression['id']			= $RtbBidRequestImp->id;
			
			if ($RtbBidRequestImp->media_type == "banner"):
			
				$impression['banner'] =		self::getRtbBidRequestBanner($RtbBidRequestImp->RtbBidRequestBanner);
			
			elseif 	($RtbBidRequestImp->media_type == "video"):

				self::getRtbBidRequestVideo($RtbBidRequestImp->RtbBidRequestBanner);
			
				$impression['video'] =		self::getRtbBidRequestVideo($RtbBidRequestImp->RtbBidRequestVideo);
			
			endif;
			
			self::setArrayParam($RtbBidRequestImp, $impression, 'bidfloor');
				
			
			if ($RtbBidRequestImp->RtbBidRequestPmp != null && count($RtbBidRequestImp->RtbBidRequestPmp)):
			 
				$impression['pmp'] = array();
				self::setArrayParam($RtbBidRequestImp->RtbBidRequestPmp, $impression['pmp'], 'private_auction');
			
				foreach ($RtbBidRequestImp->RtbBidRequestPmp->RtbBidRequestPmpDealList as $RtbBidRequestPmpDeal):
			
					$impression['pmp']['deals'][] = self::getRtbBidRequestPmpDeal($RtbBidRequestPmpDeal);
			
				endforeach;
			
			endif;
			
			$impression_list[] = (object)$impression;
			
		endforeach;
		
		return $impression_list;
		
	}
	
	private static function getRtbBidRequestPmpDeal(&$RtbBidRequestPmpDeal) {
	
		$pmpdeal	 		=  array();
		$pmpdeal['id'] 		= $RtbBidRequestPmpDeal->id;
		
		self::setArrayParam($RtbBidRequestPmpDeal, $pmpdeal, 'bidfloor');
		self::setArrayParam($RtbBidRequestPmpDeal, $pmpdeal, 'at');
	
		return (object)$pmpdeal;
	
	}
	
	private static function getRtbBidRequestBanner(&$RtbBidRequestBanner) {
		
		$banner 			= array();
		
		self::setArrayParam($RtbBidRequestBanner, $banner, 'id');
		
		/*
		 * Sorry, Dr Neal, height and width are required for a banner
		 */
		$banner['h']		= $RtbBidRequestBanner->h;
		$banner['w']		= $RtbBidRequestBanner->w;
		
		self::setArrayParam($RtbBidRequestBanner, $banner, 'pos');
		
		return (object)$banner;
		
	}
	
	private static function getRtbBidRequestVideo(&$RtbBidRequestBanner) {
	
	
	}
	
	private static function getRtbBidRequestSite(&$RtbBidRequestSite) {
		
		$site 				= array();

		self::setArrayParam($RtbBidRequestSite, $site, 'id');
		self::setArrayParam($RtbBidRequestSite, $site, 'domain');
		self::setArrayParam($RtbBidRequestSite, $site, 'cat');
		self::setArrayParam($RtbBidRequestSite, $site, 'page');
		
		if (isset($site['cat'])):
			$iab_cat = array_search($site['cat'],
					\buyrtb\parsers\openrtb\parselets\common\ParseWebsite::$vertical_map);
			if ($iab_cat != false):
				$site['cat'] = $iab_cat;
			else:
				unset($site['cat']);
			endif;
		endif;
		
		if ($RtbBidRequestSite->RtbBidRequestPublisher != null):
			$site['publisher'] = self::getRtbBidRequestPublisher($RtbBidRequestSite->RtbBidRequestPublisher);
		endif;
		
		return (object)$site;
		
	}
	
	private static function getRtbBidRequestDevice(&$RtbBidRequestDevice) {
	
		$device 				= array();
	
		self::setArrayParam($RtbBidRequestDevice, $device, 'ua');
		self::setArrayParam($RtbBidRequestDevice, $device, 'ip');
		self::setArrayParam($RtbBidRequestDevice, $device, 'language');
		self::setArrayParam($RtbBidRequestDevice, $device, 'type');
	 
		if ($RtbBidRequestDevice->RtbBidRequestGeo != null):
			$device['geo'] = self::getRtbBidRequestGeo($RtbBidRequestDevice->RtbBidRequestGeo);
		endif;
	
		return (object)$device;
	
	}
	
	private static function getRtbBidRequestGeo(&$RtbBidRequestGeo) {
	
		$geo 				= array();
	 
		self::setArrayParam($RtbBidRequestGeo, $geo, 'country');
		self::setArrayParam($RtbBidRequestGeo, $geo, 'state');
		self::setArrayParam($RtbBidRequestGeo, $geo, 'city');

		return (object)$geo;
	
	}
	
	private static function getRtbBidRequestUser(&$RtbBidRequestUser) {
	
		$user 				= array();
	
		self::setArrayParam($RtbBidRequestUser, $user, 'id');
		self::setArrayParam($RtbBidRequestUser, $user, 'buyeruid');
		
		return (object)$user;
	
	}
	
	private static function getRtbBidRequestPublisher(&$RtbBidRequestPublisher) {
	
		$publisher 				= array();
		
		self::setArrayParam($RtbBidRequestPublisher, $publisher, 'id');
		self::setArrayParam($RtbBidRequestPublisher, $publisher, 'name');
		self::setArrayParam($RtbBidRequestPublisher, $publisher, 'cat');
		self::setArrayParam($RtbBidRequestPublisher, $publisher, 'domain');
	
		if (isset($publisher['cat'])):
				$iab_cat = array_search($publisher['cat'],
					\buyrtb\parsers\openrtb\parselets\common\ParseWebsite::$vertical_map);
			if ($iab_cat != false):
				$publisher['cat'] = $iab_cat;
			else:
				unset($publisher['cat']);
			endif;
		endif;
		
		return (object)$publisher;
	
	}
	
	private static function getRtbBidRequestRegs(&$RtbBidRequestRegs) {
	
		$regs 				= array();
	
		self::setArrayParam($RtbBidRequestRegs, $regs, 'coppa');

		return (object)$regs;
	
	}
	
	private static function setArrayParam(&$obj, &$arr, $name) {
		if (!empty($obj->$name) || 
			(isset($obj->$name) && is_numeric($obj->$name))):
			$arr[$name] = $obj->$name;
		endif;
	}

}
