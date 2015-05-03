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
		
		\util\ParseHelper::setArrayParam($RtbBidRequest, $bid_request, 'id');
	
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
	
		\util\ParseHelper::setArrayParam($RtbBidRequest, $bid_request, 'at');
		
		\util\ParseHelper::setArrayParam($RtbBidRequest, $bid_request, 'tmax');
		
		\util\ParseHelper::setArrayParam($RtbBidRequest, $bid_request, 'wseat');
		
		\util\ParseHelper::setArrayParam($RtbBidRequest, $bid_request, 'allimps');
	
		\util\ParseHelper::setArrayParam($RtbBidRequest, $bid_request, 'cur');
		
		\util\ParseHelper::setArrayParam($RtbBidRequest, $bid_request, 'bcat');
		
		\util\ParseHelper::setArrayParam($RtbBidRequest, $bid_request, 'badv');
	
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
		
			\util\ParseHelper::setArrayParam($RtbBidRequestImp, $impression, 'id');

			if ($RtbBidRequestImp->media_type == "banner"):
			
				$impression['banner'] =		self::getRtbBidRequestBanner($RtbBidRequestImp->RtbBidRequestBanner);
			
			elseif 	($RtbBidRequestImp->media_type == "video"):

				$impression['video'] =		self::getRtbBidRequestVideo($RtbBidRequestImp->RtbBidRequestVideo);
			
			endif;
			
			\util\ParseHelper::setArrayParam($RtbBidRequestImp, $impression, 'displaymanager');
			
			\util\ParseHelper::setArrayParam($RtbBidRequestImp, $impression, 'displaymanagerver');
			
			\util\ParseHelper::setArrayParam($RtbBidRequestImp, $impression, 'instl');
			
			\util\ParseHelper::setArrayParam($RtbBidRequestImp, $impression, 'tagid');
			
			\util\ParseHelper::setArrayParam($RtbBidRequestImp, $impression, 'bidfloor');
			
			\util\ParseHelper::setArrayParam($RtbBidRequestImp, $impression, 'bidfloorcur');
			
			\util\ParseHelper::setArrayParam($RtbBidRequestImp, $impression, 'secure');
			
			\util\ParseHelper::setArrayParam($RtbBidRequestImp, $impression, 'iframebuster');

			if ($RtbBidRequestImp->RtbBidRequestPmp != null && count($RtbBidRequestImp->RtbBidRequestPmp)
								&& count($RtbBidRequestImp->RtbBidRequestPmp->RtbBidRequestDirectDealsList)):
			 
				$impression['pmp'] = array();
			
				\util\ParseHelper::setArrayParam($RtbBidRequestImp->RtbBidRequestPmp, $impression['pmp'], 'private_auction');
			
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

		\util\ParseHelper::setArrayParam($RtbBidRequestDirectDeals, $pmpdeal, 'id');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestDirectDeals, $pmpdeal, 'bidfloor');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestDirectDeals, $pmpdeal, 'bidfloorcur');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestDirectDeals, $pmpdeal, 'wseat');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestDirectDeals, $pmpdeal, 'wadomain');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestDirectDeals, $pmpdeal, 'at');
	
		return (object)$pmpdeal;
	
	}
	
	private static function getRtbBidRequestApp(&$RtbBidRequestApp) {
	
		$app 				= array();
	
		\util\ParseHelper::setArrayParam($RtbBidRequestApp, $app, 'id');
	
		\util\ParseHelper::setArrayParam($RtbBidRequestApp, $app, 'name');
	
		\util\ParseHelper::setArrayParam($RtbBidRequestApp, $app, 'domain');
	
		\util\ParseHelper::setArrayParam($RtbBidRequestApp, $app, 'cat');
	
		\util\ParseHelper::setArrayParam($RtbBidRequestApp, $app, 'sectioncat');
	
		\util\ParseHelper::setArrayParam($RtbBidRequestApp, $app, 'pagecat');
	
		\util\ParseHelper::setArrayParam($RtbBidRequestApp, $app, 'page');
	
		\util\ParseHelper::setArrayParam($RtbBidRequestApp, $app, 'privacypolicy');
	
		\util\ParseHelper::setArrayParam($RtbBidRequestApp, $app, 'ref');
	
		\util\ParseHelper::setArrayParam($RtbBidRequestApp, $app, 'search');
	
		\util\ParseHelper::setArrayParam($RtbBidRequestApp, $app, 'keywords');
	
		\util\ParseHelper::setArrayParam($RtbBidRequestApp, $app, 'storeurl');
	
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
		
		\util\ParseHelper::setArrayParam($RtbBidRequestBanner, $banner, 'w');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestBanner, $banner, 'h');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestBanner, $banner, 'wmax');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestBanner, $banner, 'hmax');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestBanner, $banner, 'wmin');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestBanner, $banner, 'hmin');

		\util\ParseHelper::setArrayParam($RtbBidRequestBanner, $banner, 'id');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestBanner, $banner, 'pos');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestBanner, $banner, 'btype');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestBanner, $banner, 'battr');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestBanner, $banner, 'mimes');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestBanner, $banner, 'topframe');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestBanner, $banner, 'expdir');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestBanner, $banner, 'api');
		
		return (object)$banner;
		
	}
	
	private static function getRtbBidRequestVideo(&$RtbBidRequestVideo) {
	
		$video 			= array();
		
		\util\ParseHelper::setArrayParam($RtbBidRequestVideo, $video, 'mimes');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestVideo, $video, 'minduration');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestVideo, $video, 'maxduration');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestVideo, $video, 'protocol');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestVideo, $video, 'protocols');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestVideo, $video, 'w');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestVideo, $video, 'h');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestVideo, $video, 'startdelay');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestVideo, $video, 'linearity');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestVideo, $video, 'sequence');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestVideo, $video, 'battr');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestVideo, $video, 'maxextended');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestVideo, $video, 'minbitrate');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestVideo, $video, 'maxbitrate');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestVideo, $video, 'boxingallowed');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestVideo, $video, 'playbackmethod');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestVideo, $video, 'delivery');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestVideo, $video, 'pos');
		
		if (count($RtbBidRequestVideo->RtbBidRequestBannerList)):
		
			foreach ($RtbBidRequestVideo->RtbBidRequestBannerList as $RtbBidRequestBanner):
				
				$video['companionad'][] = self::getRtbBidRequestBanner($RtbBidRequestBanner);
				
			endforeach;
		
		endif;
		
		\util\ParseHelper::setArrayParam($RtbBidRequestVideo, $video, 'api');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestVideo, $video, 'companiontype');

		return (object)$video;
	
	}
	
	private static function getRtbBidRequestSite(&$RtbBidRequestSite) {
		
		$site 				= array();

		\util\ParseHelper::setArrayParam($RtbBidRequestSite, $site, 'id');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestSite, $site, 'name');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestSite, $site, 'domain');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestSite, $site, 'cat');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestSite, $site, 'sectioncat');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestSite, $site, 'pagecat');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestSite, $site, 'page');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestSite, $site, 'privacypolicy');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestSite, $site, 'ref');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestSite, $site, 'search');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestSite, $site, 'keywords');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestSite, $site, 'storeurl');

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
	
		\util\ParseHelper::setArrayParam($RtbBidRequestDevice, $device, 'dnt');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestDevice, $device, 'ua');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestDevice, $device, 'ip');
		
		if ($RtbBidRequestDevice->RtbBidRequestGeo != null):
		
			$device['geo'] = self::getRtbBidRequestGeo($RtbBidRequestDevice->RtbBidRequestGeo);
		
		endif;
		
		\util\ParseHelper::setArrayParam($RtbBidRequestDevice, $device, 'didsha1');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestDevice, $device, 'didmd5');
	 
		\util\ParseHelper::setArrayParam($RtbBidRequestDevice, $device, 'dpidsha1');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestDevice, $device, 'dpidmd5');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestDevice, $device, 'macsha1');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestDevice, $device, 'macmd5');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestDevice, $device, 'ipv6');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestDevice, $device, 'carrier');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestDevice, $device, 'language');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestDevice, $device, 'make');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestDevice, $device, 'model');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestDevice, $device, 'os');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestDevice, $device, 'osv');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestDevice, $device, 'js');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestDevice, $device, 'connectiontype');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestDevice, $device, 'devicetype');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestDevice, $device, 'flashver');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestDevice, $device, 'ifa');
	
		return (object)$device;
	
	}
	
	private static function getRtbBidRequestGeo(&$RtbBidRequestGeo) {
	
		$geo 				= array();
	 
		\util\ParseHelper::setArrayParam($RtbBidRequestGeo, $geo, 'lat');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestGeo, $geo, 'lon');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestGeo, $geo, 'country');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestGeo, $geo, 'region');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestGeo, $geo, 'regionfips104');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestGeo, $geo, 'metro');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestGeo, $geo, 'city');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestGeo, $geo, 'zip');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestGeo, $geo, 'type');

		return (object)$geo;
	
	}
	
	private static function getRtbBidRequestUser(&$RtbBidRequestUser) {
	
		$user 				= array();
	
		\util\ParseHelper::setArrayParam($RtbBidRequestUser, $user, 'id');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestUser, $user, 'buyeruid');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestUser, $user, 'yob');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestUser, $user, 'gender');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestUser, $user, 'keywords');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestUser, $user, 'customdata');
		
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
	
		\util\ParseHelper::setArrayParam($RtbBidRequestData, $data, 'id');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestData, $data, 'name');
		
		if (count($RtbBidRequestData->RtbBidRequestSegmentList)):
		
			foreach ($RtbBidRequestData->RtbBidRequestSegmentList as $RtbBidRequestSegment):
			
				$data['segment'][] = self::getRtbBidRequestSegment($RtbBidRequestSegment);
			
			endforeach;
			
		endif;
		
		return (object)$data;
	
	}
	
	private static function getRtbBidRequestSegment(&$RtbBidRequestSegment) {
	
		$segment 				= array();
	
		\util\ParseHelper::setArrayParam($RtbBidRequestSegment, $segment, 'id');
	
		\util\ParseHelper::setArrayParam($RtbBidRequestSegment, $segment, 'name');
	
		\util\ParseHelper::setArrayParam($RtbBidRequestSegment, $segment, 'value');
	
		return (object)$segment;
	
	}
	
	private static function getRtbBidRequestProducer(&$RtbBidRequestProducer) {
	
		$producer 				= array();
	
		\util\ParseHelper::setArrayParam($RtbBidRequestProducer, $producer, 'id');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestProducer, $producer, 'name');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestProducer, $producer, 'cat');

		\util\ParseHelper::setArrayParam($RtbBidRequestProducer, $producer, 'domain');
		
		return (object)$producer;
	}
	
	
	private static function getRtbBidRequestContent(&$RtbBidRequestContent) {
	
		$content 				= array();
	
		\util\ParseHelper::setArrayParam($RtbBidRequestContent, $content, 'id');
	
		\util\ParseHelper::setArrayParam($RtbBidRequestContent, $content, 'episode');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestContent, $content, 'title');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestContent, $content, 'series');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestContent, $content, 'season');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestContent, $content, 'url');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestContent, $content, 'cat');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestContent, $content, 'videoquality');
	
		\util\ParseHelper::setArrayParam($RtbBidRequestContent, $content, 'keywords');
	
		\util\ParseHelper::setArrayParam($RtbBidRequestContent, $content, 'contentrating');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestContent, $content, 'userrating');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestContent, $content, 'context');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestContent, $content, 'livestream');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestContent, $content, 'sourcerelationship');
	
		if ($RtbBidRequestContent->RtbBidRequestProducer != null):
		
			$content['producer'] = self::getRtbBidRequestProducer($RtbBidRequestContent->RtbBidRequestProducer);
		
		endif;
		
		\util\ParseHelper::setArrayParam($RtbBidRequestContent, $content, 'len');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestContent, $content, 'qagmediarating');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestContent, $content, 'embeddable');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestContent, $content, 'language');
	
		return (object)$content;
	
	}	
	
	private static function getRtbBidRequestPublisher(&$RtbBidRequestPublisher) {
	
		$publisher 				= array();
		
		\util\ParseHelper::setArrayParam($RtbBidRequestPublisher, $publisher, 'id');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestPublisher, $publisher, 'name');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestPublisher, $publisher, 'cat');
		
		\util\ParseHelper::setArrayParam($RtbBidRequestPublisher, $publisher, 'domain');
	
		return (object)$publisher;
	
	}
	
	private static function getRtbBidRequestRegulations(&$RtbBidRequestRegulations) {
	
		$regs 				= array();
	
		\util\ParseHelper::setArrayParam($RtbBidRequestRegulations, $regs, 'coppa');

		return (object)$regs;
	
	}
	

	private static function convertToIabCategory(&$arr, $name) {
	
		if (isset($arr[$name])):
			$iab_cat = array_search($arr[$name],
					\buyrtb\parsers\openrtb\parselets\common\ParseWebsite::$vertical_map);
			if ($iab_cat != false):
				$arr[$name] = $iab_cat;
			else:
				unset($arr[$name]);
			endif;
		endif;
	
	}

}
