<?php
/**
 * CDNPAL NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2015 CDNPAL Ltd. All Rights Reserved
 * @license GPLv3
 */

namespace buyrtb\parsers\openrtb;
use \Exception;

class OpenRTBParser {

	public $raw_post;
	public $json_post;
	public $RtbBidRequest;
	
	private $expeption_missing_min_bid_request_params = "Bid Request missing required parameter";
	private $missing_optional_bid_request_params = "Bid Request missing optional parameter";
	private $got_optional_bid_request_params = "Got Bid Request optional parameter";
	
	public function parse_request($raw_post = null) {

		// prepare the logger
		$logger =\rtbbuyv22\RtbBuyV22Logger::get_instance();
		
		// prepare the response object
		$this->RtbBidRequest = new \model\rtb\RtbBidRequest();
		
		// Initialize Data
		try {
			\buyrtb\parsers\openrtb\parselets\common\Init::execute($logger, $this, $this->RtbBidRequest);
		} catch (Exception $e) {	
			throw new Exception($e->getMessage(), $e->getCode(), $e->getPrevious());
        }
		
        // Parse Currency
        try {
        	\buyrtb\parsers\openrtb\parselets\common\ParseCurrency::execute($logger, $this, $this->RtbBidRequest);
        } catch (Exception $e) {
        	throw new Exception($e->getMessage(), $e->getCode(), $e->getPrevious());
        }
        
        // Parse Site
        if (!isset($this->json_post["site"])):
        	throw new Exception($this->expeption_missing_min_bid_request_params . ": at least 1 site object");
        endif;
        
        $ad_campaign_site = $this->json_post["site"];
        $RtbBidRequestSite = new \model\rtb\RtbBidRequestSite();
        
        try {
        	\buyrtb\parsers\openrtb\parselets\common\ParseWebsite::execute($logger, $this, $this->RtbBidRequest, $RtbBidRequestSite, $ad_campaign_site);
        } catch (Exception $e) {
        	throw new Exception($e->getMessage(), $e->getCode(), $e->getPrevious());
        }
        
        $this->RtbBidRequest->RtbBidRequestSite = $RtbBidRequestSite;
        
        // Parse Device
        if (!isset($this->json_post["device"])):
        	throw new Exception($this->expeption_missing_min_bid_request_params . ": at least 1 site object");
        endif;
        
        $device = $this->json_post["device"];
        $RtbBidRequestDevice = new \model\rtb\RtbBidRequestDevice();
        
        try {
        	\buyrtb\parsers\openrtb\parselets\common\device\ParseDevice::execute($logger, $this, $this->RtbBidRequest, $RtbBidRequestDevice, $device);
        } catch (Exception $e) {
        	throw new Exception($e->getMessage(), $e->getCode(), $e->getPrevious());
        }
        
        $this->RtbBidRequest->RtbBidRequestDevice = $RtbBidRequestDevice;
        
        $logger->log[] = "Is Mobile: " . $this->bid_request_device_type != 2;
        
        // Parse Regs

        if (isset($this->json_post["regs"])):
	        $ad_regs = $this->json_post["regs"];
	        $RtbBidRequestRegs = new \model\rtb\RtbBidRequestRegs();
	        
	        try {
	        	\buyrtb\parsers\openrtb\parselets\common\ParseRegs::execute($logger, $this, $this->RtbBidRequest, $RtbBidRequestRegs, $ad_regs);
	        } catch (Exception $e) {
	        	throw new Exception($e->getMessage(), $e->getCode(), $e->getPrevious());
	        }
	        
	        $this->RtbBidRequest->RtbBidRequestRegs = $RtbBidRequestRegs;
        endif;
        
        // process all ad impressions
        if (!isset($this->json_post["imp"][0])):
        	throw new Exception($this->expeption_missing_min_bid_request_params . ": at least 1 imp object");
        endif;
        
        $ad_impression_list = $this->json_post["imp"];

        foreach ($ad_impression_list as $ad_impression):
        
        	$RtbBidRequestImp = new \model\rtb\RtbBidRequestImp();

	        // Parse Imp ID
	        try {
	        	\buyrtb\parsers\openrtb\parselets\common\imp\ParseImpId::execute($logger, $this, $RtbBidRequestImp, $ad_impression);
	        } catch (Exception $e) {
	        	throw new Exception($e->getMessage(), $e->getCode(), $e->getPrevious());
	        }
	        
	        // Parse Imp Floor Price
	        try {
	        	\buyrtb\parsers\openrtb\parselets\common\imp\ParseFloor::execute($logger, $this, $RtbBidRequestImp, $ad_impression);
	        } catch (Exception $e) {
	        	throw new Exception($e->getMessage(), $e->getCode(), $e->getPrevious());
	        }

	        // Parse Private Markplace (PMP)
	        try {
	        	\buyrtb\parsers\openrtb\parselets\common\imp\ParsePrivateMarketPlace::execute($logger, $this, $RtbBidRequestImp, $ad_impression);
	        } catch (Exception $e) {
	        	throw new Exception($e->getMessage(), $e->getCode(), $e->getPrevious());
	        }
	        
	        if (isset($ad_impression["banner"])):
	        	// this is a banner
	        	$ad_impression_banner = $ad_impression["banner"];
	        	$RtbBidRequestImp->media_type = "banner";
	       		$RtbBidRequestImp->RtbBidRequestBanner = new \model\rtb\RtbBidRequestBanner();
	       		$DisplayParser = new \buyrtb\parsers\openrtb\DisplayParser();
	       		$DisplayParser->parse_request($logger, $this, $RtbBidRequestImp->RtbBidRequestBanner, $ad_impression_banner);

	       	elseif (isset($ad_impression["video"])):
	       		// this is a video
	       		$ad_impression_video = $ad_impression["video"];
	       		$RtbBidRequestImp->media_type = "video";
	       		$RtbBidRequestImp->RtbBidRequestVideo = new \model\rtb\RtbBidRequestVideo();	
	       		$VideoParser = new \buyrtb\parsers\openrtb\VideoParser();
	       		$VideoParser->parse_request($logger, $this, $RtbBidRequestImp->RtbBidRequestVideo, $ad_impression_video);
	       		
	        else:
	       		throw new Exception($this->expeption_missing_min_bid_request_params . ": at least one banner or video object in the imp");
	        endif;
	
	        $this->RtbBidRequest->RtbBidRequestImpList[] = $RtbBidRequestImp;
        	
        endforeach;
                
        return $this->RtbBidRequest;
	}
}
