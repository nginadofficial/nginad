<?php
/**
 * NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2016 NginAd Foundation. All Rights Reserved
 * @license GPLv3
 */

namespace buyrtbheaderbidding\encoders\openrtb;

class RtbBidRequestJsonEncoder {
	
	public static function execute(\model\openrtb\RtbBidRequest &$RtbBidRequest) {
	
		$bid_request = array();
		
		\util\ParseHelper::setArrayParam($RtbBidRequest, $bid_request, 'id', 'string');
	
		$bid_request["imp"] 	= self::getRtbBidRequestImpList($RtbBidRequest);
	
		if ($RtbBidRequest->RtbBidRequestApp != null):
		
			$bid_request["app"] 	= self::getRtbBidRequestApp($RtbBidRequest->RtbBidRequestApp);
		
		endif;
		
		if ($RtbBidRequest->RtbBidRequestSite != null):
	
			$bid_request["site"] 	= self::getRtbBidRequestSite($RtbBidRequest->RtbBidRequestSite);
	
		endif;
	
		if ($RtbBidRequest->RtbBidRequestDevice != null):
	
			$bid_request["device"] 	= self::getRtbBidRequestDevice($RtbBidRequest->RtbBidRequestDevice);
	
		endif;
	
		if ($RtbBidRequest->RtbBidRequestUser != null):
	
			$bid_request["user"] 	= self::getRtbBidRequestUser($RtbBidRequest->RtbBidRequestUser);
	
		endif;
	
		\util\ParseHelper::setArrayParam($RtbBidRequest, $bid_request, 'at', 'integer');
		
		\util\ParseHelper::setArrayParam($RtbBidRequest, $bid_request, 'tmax', 'integer');
		
		\util\ParseHelper::setArrayParam($RtbBidRequest, $bid_request, 'wseat', 'array');
		
		\util\ParseHelper::setArrayParam($RtbBidRequest, $bid_request, 'allimps', 'integer');
	
		\util\ParseHelper::setArrayParam($RtbBidRequest, $bid_request, 'cur', 'array');
		
		\util\ParseHelper::setArrayParam($RtbBidRequest, $bid_request, 'bcat', 'array');
		
		\util\ParseHelper::setArrayParam($RtbBidRequest, $bid_request, 'badv', 'array');
	
		if ($RtbBidRequest->RtbBidRequestRegulations != null):
	
			$bid_request["regs"] 	= self::getRtbBidRequestRegulations($RtbBidRequest->RtbBidRequestRegulations);
	
		endif;

		$result = json_encode($bid_request);
	
		return $result;
	
	}
	
	private static function getRtbBidRequestImpList(\model\openrtb\RtbBidRequest &$RtbBidRequest) {
		
		$impression_list = array();
		
		foreach ($RtbBidRequest->RtbBidRequestImpList as $RtbBidRequestImp):
			
			$impression 				=  array();
		
			\util\ParseHelper::setArrayParam($RtbBidRequestImp, $impression, 'id', 'string');

			if ($RtbBidRequestImp->media_type == "banner"):
			
				$impression['banner'] =		self::getRtbBidRequestBanner($RtbBidRequestImp->RtbBidRequestBanner, 'object');
			
			else:

				continue;
			
			endif;
			
			\util\ParseHelper::setArrayParam($RtbBidRequestImp, $impression, 'displaymanager', 'string');
			
			\util\ParseHelper::setArrayParam($RtbBidRequestImp, $impression, 'displaymanagerver', 'string');
			
			\util\ParseHelper::setArrayParam($RtbBidRequestImp, $impression, 'instl', 'integer');
			
			\util\ParseHelper::setArrayParam($RtbBidRequestImp, $impression, 'tagid', 'string');
			
			\util\ParseHelper::setArrayParam($RtbBidRequestImp, $impression, 'bidfloor', 'float');
			
			\util\ParseHelper::setArrayParam($RtbBidRequestImp, $impression, 'bidfloorcur', 'string');
			
			\util\ParseHelper::setArrayParam($RtbBidRequestImp, $impression, 'secure', 'integer');
			
			\util\ParseHelper::setArrayParam($RtbBidRequestImp, $impression, 'iframebuster', 'array');

			if ($RtbBidRequestImp->RtbBidRequestPmp != null && count($RtbBidRequestImp->RtbBidRequestPmp)
								&& count($RtbBidRequestImp->RtbBidRequestPmp->RtbBidRequestDirectDealsList)):
			 
				$impression['pmp'] = array();
			
				\util\ParseHelper::setArrayParam($RtbBidRequestImp->RtbBidRequestPmp, $impression['pmp'], 'private_auction', 'integer');
			
				foreach ($RtbBidRequestImp->RtbBidRequestPmp->RtbBidRequestDirectDealsList as $RtbBidRequestDirectDeals):
			
					$impression['pmp']['deals'][] = self::getRtbBidRequestDirectDeals($RtbBidRequestDirectDeals);
			
				endforeach;
			
			endif;
			
			$impression_list[] = (object)$impression;
			
		endforeach;
		
		return $impression_list;
		
	}
	
	private static function getRtbBidRequestDirectDeals(&$RtbBidRequestDirectDeals) {
	
		$pmpdeal	 		=  array();

		\util\ParseHelper::setArrayParam($RtbBidRequestDirectDeals, $pmpdeal, 'id', 'string');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestDirectDeals, $pmpdeal, 'bidfloor', 'float');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestDirectDeals, $pmpdeal, 'bidfloorcur', 'string');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestDirectDeals, $pmpdeal, 'wseat', 'array');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestDirectDeals, $pmpdeal, 'wadomain', 'array');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestDirectDeals, $pmpdeal, 'at', 'integer');
	
		return (object)$pmpdeal;
	
	}
	
	private static function getRtbBidRequestApp(&$RtbBidRequestApp) {
	
		$app 				= array();
	
		\util\ParseHelper::setArrayParam($RtbBidRequestApp, $app, 'id', 'string');
	
		\util\ParseHelper::setArrayParam($RtbBidRequestApp, $app, 'name', 'string');
	
		\util\ParseHelper::setArrayParam($RtbBidRequestApp, $app, 'domain', 'string');
	
		\util\ParseHelper::setArrayParam($RtbBidRequestApp, $app, 'cat', 'array');
	
		\util\ParseHelper::setArrayParam($RtbBidRequestApp, $app, 'sectioncat', 'array');
	
		\util\ParseHelper::setArrayParam($RtbBidRequestApp, $app, 'pagecat', 'array');
	
		\util\ParseHelper::setArrayParam($RtbBidRequestApp, $app, 'page', 'string');
	
		\util\ParseHelper::setArrayParam($RtbBidRequestApp, $app, 'privacypolicy', 'integer');
	
		\util\ParseHelper::setArrayParam($RtbBidRequestApp, $app, 'paid', 'integer');
	
		\util\ParseHelper::setArrayParam($RtbBidRequestApp, $app, 'keywords', 'string');
	
		\util\ParseHelper::setArrayParam($RtbBidRequestApp, $app, 'storeurl', 'string');
	
		if ($RtbBidRequestApp->RtbBidRequestPublisher != null):
	
			$app['publisher'] = self::getRtbBidRequestPublisher($RtbBidRequestApp->RtbBidRequestPublisher);
	
		endif;
	
		if ($RtbBidRequestApp->RtbBidRequestContent != null):
	
			$app['content'] = self::getRtbBidRequestContent($RtbBidRequestApp->RtbBidRequestContent);
	
		endif;
	
		return (object)$app;
	
	}
	
	private static function getRtbBidRequestBanner(&$RtbBidRequestBanner) {
		
		$banner 			= array();
		
		\util\ParseHelper::setArrayParam($RtbBidRequestBanner, $banner, 'w', 'integer');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestBanner, $banner, 'h', 'integer');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestBanner, $banner, 'wmax', 'integer');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestBanner, $banner, 'hmax', 'integer');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestBanner, $banner, 'wmin', 'integer');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestBanner, $banner, 'hmin', 'integer');

		\util\ParseHelper::setArrayParam($RtbBidRequestBanner, $banner, 'id', 'string');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestBanner, $banner, 'pos', 'integer');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestBanner, $banner, 'btype', 'array');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestBanner, $banner, 'battr', 'array');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestBanner, $banner, 'mimes', 'array');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestBanner, $banner, 'topframe', 'integer');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestBanner, $banner, 'expdir', 'array');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestBanner, $banner, 'api', 'array');
		
		return (object)$banner;
		
	}
	
	private static function getRtbBidRequestVideo(&$RtbBidRequestVideo) {
	
		$video 			= array();
		
		\util\ParseHelper::setArrayParam($RtbBidRequestVideo, $video, 'mimes', 'array');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestVideo, $video, 'minduration', 'integer');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestVideo, $video, 'maxduration', 'integer');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestVideo, $video, 'protocol', 'integer');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestVideo, $video, 'protocols', 'array');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestVideo, $video, 'w', 'integer');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestVideo, $video, 'h', 'integer');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestVideo, $video, 'startdelay', 'integer');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestVideo, $video, 'linearity', 'integer');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestVideo, $video, 'sequence', 'integer');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestVideo, $video, 'battr', 'array');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestVideo, $video, 'maxextended', 'integer');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestVideo, $video, 'minbitrate', 'integer');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestVideo, $video, 'maxbitrate', 'integer');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestVideo, $video, 'boxingallowed', 'integer');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestVideo, $video, 'playbackmethod', 'array');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestVideo, $video, 'delivery', 'array');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestVideo, $video, 'pos', 'integer');
		
		if (count($RtbBidRequestVideo->RtbBidRequestBannerList)):
		
			foreach ($RtbBidRequestVideo->RtbBidRequestBannerList as $RtbBidRequestBanner):
				
				$video['companionad'][] = self::getRtbBidRequestBanner($RtbBidRequestBanner);
				
			endforeach;
		
		endif;
		
		\util\ParseHelper::setArrayParam($RtbBidRequestVideo, $video, 'api', 'array');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestVideo, $video, 'companiontype', 'array');

		return (object)$video;
	
	}
	
	private static function getRtbBidRequestSite(&$RtbBidRequestSite) {
		
		$site 				= array();

		\util\ParseHelper::setArrayParam($RtbBidRequestSite, $site, 'id', 'string');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestSite, $site, 'name', 'string');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestSite, $site, 'domain', 'string');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestSite, $site, 'cat', 'array');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestSite, $site, 'sectioncat', 'array');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestSite, $site, 'pagecat', 'array');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestSite, $site, 'page', 'string');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestSite, $site, 'privacypolicy', 'integer');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestSite, $site, 'ref', 'string');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestSite, $site, 'search', 'string');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestSite, $site, 'keywords', 'string');

		if ($RtbBidRequestSite->RtbBidRequestPublisher != null):
		
			$site['publisher'] = self::getRtbBidRequestPublisher($RtbBidRequestSite->RtbBidRequestPublisher);
		
		endif;
		
		if ($RtbBidRequestSite->RtbBidRequestContent != null):
		
			$site['content'] = self::getRtbBidRequestContent($RtbBidRequestSite->RtbBidRequestContent);
		
		endif;
		
		return (object)$site;
		
	}

	private static function getRtbBidRequestDevice(&$RtbBidRequestDevice) {
	
		$device 				= array();
	
		\util\ParseHelper::setArrayParam($RtbBidRequestDevice, $device, 'dnt', 'integer');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestDevice, $device, 'ua', 'string');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestDevice, $device, 'ip', 'string');
		
		if ($RtbBidRequestDevice->RtbBidRequestGeo != null):
		
			$device['geo'] = self::getRtbBidRequestGeo($RtbBidRequestDevice->RtbBidRequestGeo);
		
		endif;
		
		\util\ParseHelper::setArrayParam($RtbBidRequestDevice, $device, 'didsha1', 'string');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestDevice, $device, 'didmd5', 'string');
	 
		\util\ParseHelper::setArrayParam($RtbBidRequestDevice, $device, 'dpidsha1', 'string');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestDevice, $device, 'dpidmd5', 'string');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestDevice, $device, 'macsha1', 'string');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestDevice, $device, 'macmd5', 'string');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestDevice, $device, 'ipv6', 'string');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestDevice, $device, 'carrier', 'string');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestDevice, $device, 'language', 'string');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestDevice, $device, 'make', 'string');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestDevice, $device, 'model', 'string');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestDevice, $device, 'os', 'string');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestDevice, $device, 'osv', 'string');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestDevice, $device, 'js', 'integer');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestDevice, $device, 'connectiontype', 'integer');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestDevice, $device, 'devicetype', 'integer');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestDevice, $device, 'flashver', 'string');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestDevice, $device, 'ifa', 'string');
	
		return (object)$device;
	
	}
	
	private static function getRtbBidRequestGeo(&$RtbBidRequestGeo) {
	
		$geo 				= array();
	 
		\util\ParseHelper::setArrayParam($RtbBidRequestGeo, $geo, 'lat', 'float');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestGeo, $geo, 'lon', 'float');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestGeo, $geo, 'country', 'string');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestGeo, $geo, 'region', 'string');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestGeo, $geo, 'regionfips104', 'string');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestGeo, $geo, 'metro', 'string');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestGeo, $geo, 'city', 'string');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestGeo, $geo, 'zip', 'string');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestGeo, $geo, 'type', 'integer');

		return (object)$geo;
	
	}
	
	private static function getRtbBidRequestUser(&$RtbBidRequestUser) {
	
		$user 				= array();
	
		\util\ParseHelper::setArrayParam($RtbBidRequestUser, $user, 'id', 'string');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestUser, $user, 'buyeruid', 'string');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestUser, $user, 'yob', 'integer');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestUser, $user, 'gender', 'string');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestUser, $user, 'keywords', 'string');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestUser, $user, 'customdata', 'string');
		
		if ($RtbBidRequestUser->RtbBidRequestGeo != null):
		
			$user['geo'] = self::getRtbBidRequestGeo($RtbBidRequestUser->RtbBidRequestGeo);
		
		endif;
		
		if (count($RtbBidRequestUser->RtbBidRequestDataList)):
		
			foreach ($RtbBidRequestUser->RtbBidRequestDataList as $RtbBidRequestData):
			
				$video['data'][] = self::getRtbBidRequestData($RtbBidRequestData);
			
			endforeach;
			
		endif;
		
		return (object)$user;
	
	}
	
	private static function getRtbBidRequestData(&$RtbBidRequestData) {
	
		$data 				= array();
	
		\util\ParseHelper::setArrayParam($RtbBidRequestData, $data, 'id', 'string');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestData, $data, 'name', 'string');
		
		if (count($RtbBidRequestData->RtbBidRequestSegmentList)):
		
			foreach ($RtbBidRequestData->RtbBidRequestSegmentList as $RtbBidRequestSegment):
			
				$data['segment'][] = self::getRtbBidRequestSegment($RtbBidRequestSegment);
			
			endforeach;
			
		endif;
		
		return (object)$data;
	
	}
	
	private static function getRtbBidRequestSegment(&$RtbBidRequestSegment) {
	
		$segment 				= array();
	
		\util\ParseHelper::setArrayParam($RtbBidRequestSegment, $segment, 'id', 'string');
	
		\util\ParseHelper::setArrayParam($RtbBidRequestSegment, $segment, 'name', 'string');
	
		\util\ParseHelper::setArrayParam($RtbBidRequestSegment, $segment, 'value', 'string');
	
		return (object)$segment;
	
	}
	
	private static function getRtbBidRequestProducer(&$RtbBidRequestProducer) {
	
		$producer 				= array();
	
		\util\ParseHelper::setArrayParam($RtbBidRequestProducer, $producer, 'id', 'string');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestProducer, $producer, 'name', 'string');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestProducer, $producer, 'cat', 'array');

		\util\ParseHelper::setArrayParam($RtbBidRequestProducer, $producer, 'domain', 'string');
		
		return (object)$producer;
	}
	
	
	private static function getRtbBidRequestContent(&$RtbBidRequestContent) {
	
		$content 				= array();
	
		\util\ParseHelper::setArrayParam($RtbBidRequestContent, $content, 'id', 'string');
	
		\util\ParseHelper::setArrayParam($RtbBidRequestContent, $content, 'episode', 'integer');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestContent, $content, 'title', 'string');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestContent, $content, 'series', 'string');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestContent, $content, 'season', 'string');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestContent, $content, 'url', 'string');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestContent, $content, 'cat', 'array');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestContent, $content, 'videoquality', 'integer');
	
		\util\ParseHelper::setArrayParam($RtbBidRequestContent, $content, 'keywords', 'string');
	
		\util\ParseHelper::setArrayParam($RtbBidRequestContent, $content, 'contentrating', 'string');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestContent, $content, 'userrating', 'string');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestContent, $content, 'context', 'integer');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestContent, $content, 'livestream', 'integer');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestContent, $content, 'sourcerelationship', 'integer');
	
		if ($RtbBidRequestContent->RtbBidRequestProducer != null):
		
			$content['producer'] = self::getRtbBidRequestProducer($RtbBidRequestContent->RtbBidRequestProducer);
		
		endif;
		
		\util\ParseHelper::setArrayParam($RtbBidRequestContent, $content, 'len', 'integer');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestContent, $content, 'qagmediarating', 'integer');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestContent, $content, 'embeddable', 'integer');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestContent, $content, 'language', 'string');
	
		return (object)$content;
	
	}	
	
	private static function getRtbBidRequestPublisher(&$RtbBidRequestPublisher) {
	
		$publisher 				= array();
		
		\util\ParseHelper::setArrayParam($RtbBidRequestPublisher, $publisher, 'id', 'string');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestPublisher, $publisher, 'name', 'string');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestPublisher, $publisher, 'cat', 'array');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestPublisher, $publisher, 'domain', 'string');
	
		return (object)$publisher;
	
	}
	
	private static function getRtbBidRequestRegulations(&$RtbBidRequestRegulations) {
	
		$regs 				= array();
	
		\util\ParseHelper::setArrayParam($RtbBidRequestRegulations, $regs, 'coppa', 'integer');

		return (object)$regs;
	
	}
	

	private static function convertToIabCategory(&$arr, $name) {
	
		if (isset($arr[$name])):
			$iab_cat = array_search($arr[$name],
					\buyrtbheaderbidding\parsers\openrtb\parselets\common\ParseWebsite::$vertical_map);
			if ($iab_cat != false):
				$arr[$name] = $iab_cat;
			else:
				unset($arr[$name]);
			endif;
		endif;
	
	}

}
