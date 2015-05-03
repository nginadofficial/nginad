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

	public $config;
	public $raw_post;
	public $json_post;
	public $RtbBidRequest;
	
	public $expeption_missing_min_bid_request_params = "Bid Request missing required parameter";
	public $missing_optional_bid_request_params = "Bid Request missing optional parameter";
	public $got_optional_bid_request_params = "Got Bid Request optional parameter";
	
	public function parse_request($config, $is_local, $raw_post = null) {

		$this->config = $config;
		
		$this->raw_post = $raw_post;
		
		// prepare the logger
		$logger =\rtbbuyv22\RtbBuyV22Logger::get_instance();
		
		// prepare the response object
		$this->RtbBidRequest = new \model\openrtb\RtbBidRequest();
		$this->RtbBidRequest->is_local_request = $is_local;
		
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
        
        // Parse Second Price
		\util\ParseHelper::parse_item(
				$this->RtbBidRequest,
				$this->json_post,
				"at");
		
		// Parse Max Timeout on RTB Bid Response
		\util\ParseHelper::parse_item(
				$this->RtbBidRequest,
				$this->json_post,
				"tmax");
		
		// Parse Allowed Buyer Seat IDs
		\util\ParseHelper::parse_item_list(
				$this->RtbBidRequest,
				$this->json_post,
				"wseat");
		
		// Parse All Available impressions for this publisher boolean
		\util\ParseHelper::parse_item(
				$this->RtbBidRequest,
				$this->json_post,
				"allimps");
        
		// Parse Blocked Advertiser Categories
		\util\ParseHelper::parse_item_list(
				$this->RtbBidRequest,
				$this->json_post,
				"bcat");
		
		// Parse Blocked TLDs for this RTB request, Publisher Black Listed
		\util\ParseHelper::parse_item_list(
				$this->RtbBidRequest,
				$this->json_post,
				"badv");
		
        // Parse Site
        if (isset($this->json_post["site"])):
	
	        $ad_campaign_site = $this->json_post["site"];
	        $RtbBidRequestSite = new \model\openrtb\RtbBidRequestSite();
	        
	        try {
	        	\buyrtb\parsers\openrtb\parselets\common\ParseWebsite::execute($logger, $this, $this->RtbBidRequest, $RtbBidRequestSite, $ad_campaign_site);
	        	$this->RtbBidRequest->RtbBidRequestSite = $RtbBidRequestSite;
	        } catch (Exception $e) {
	        	throw new Exception($e->getMessage(), $e->getCode(), $e->getPrevious());
	        }
	        
        endif;
       
        // Parse App
        if (isset($this->json_post["app"])):
        
	        $rtb_app = $this->json_post["app"];
	        $RtbBidRequestApp = new \model\openrtb\RtbBidRequestApp();
	         
	        try {
	        	\buyrtb\parsers\openrtb\parselets\common\ParseApp::execute($logger, $this, $this->RtbBidRequest, $RtbBidRequestApp, $rtb_app);
	        	$this->RtbBidRequest->RtbBidRequestApp = $RtbBidRequestApp;
	        } catch (Exception $e) {
	        	throw new Exception($e->getMessage(), $e->getCode(), $e->getPrevious());
	        }

        endif;
        
        // Parse User
        if (isset($this->json_post["user"])):
        
	        $rtb_user = $this->json_post["user"];
	        $RtbBidRequestUser = new \model\openrtb\RtbBidRequestUser();
	        
	        try {
	        	\buyrtb\parsers\openrtb\parselets\common\ParseUser::execute($logger, $this, $this->RtbBidRequest, $RtbBidRequestUser, $rtb_user);
	        	$this->RtbBidRequest->RtbBidRequestUser = $RtbBidRequestUser;
	        } catch (Exception $e) {
	        	throw new Exception($e->getMessage(), $e->getCode(), $e->getPrevious());
	        }       

        endif;
        
        // Parse Device
        if (isset($this->json_post["device"])):
	
	        $device = $this->json_post["device"];
	        $RtbBidRequestDevice = new \model\openrtb\RtbBidRequestDevice();
	        
	        try {
	        	\buyrtb\parsers\openrtb\parselets\common\device\ParseDevice::execute($logger, $this, $this->RtbBidRequest, $RtbBidRequestDevice, $device);
	        	$this->RtbBidRequest->RtbBidRequestDevice = $RtbBidRequestDevice;
	        	$logger->log[] = "Is Mobile: " . $this->RtbBidRequest->RtbBidRequestDevice->devicetype != 2;
	        
	        } catch (Exception $e) {
	        	throw new Exception($e->getMessage(), $e->getCode(), $e->getPrevious());
	        }
        endif;
        
        // Parse Regs

        if (isset($this->json_post["regs"])):
	        $ad_regs = $this->json_post["regs"];
	        $RtbBidRequestRegulations = new \model\openrtb\RtbBidRequestRegulations();
	        
	        try {
	        	\buyrtb\parsers\openrtb\parselets\common\ParseRegs::execute($logger, $this, $this->RtbBidRequest, $RtbBidRequestRegulations, $ad_regs);
	        	$this->RtbBidRequest->RtbBidRequestRegulations = $RtbBidRequestRegulations;
	        } catch (Exception $e) {
	        	throw new Exception($e->getMessage(), $e->getCode(), $e->getPrevious());
	        }

        endif;
        
        // process all ad impressions
        if (!isset($this->json_post["imp"][0])):
        	throw new Exception($this->expeption_missing_min_bid_request_params . ": at least 1 imp object");
        endif;
        
        $ad_impression_list = $this->json_post["imp"];

        foreach ($ad_impression_list as $ad_impression):
        
        	$RtbBidRequestImp = new \model\openrtb\RtbBidRequestImp();

	        // Parse Imp ID

	        try {
		        \util\ParseHelper::parse_with_exception(
		        		$RtbBidRequestImp,
		        		$ad_impression,
		        		$this->expeption_missing_min_bid_request_params . ": imp_id",
		        		"id");
	        } catch (Exception $e) {
	        	throw new Exception($e->getMessage(), $e->getCode(), $e->getPrevious());
	        }
	        
	        // Parse Imp Display Manager
	         
	        \util\ParseHelper::parse_item(
	        		$RtbBidRequestImp,
	        		$ad_impression,
	        		"displaymanager");
	        
	        // Parse Imp Display Manager Version
	        
	        \util\ParseHelper::parse_item(
	        		$RtbBidRequestImp,
	        		$ad_impression,
	        		"displaymanagerver"); 
	        
	        // Parse Imp Is Interstitial
	         
	        \util\ParseHelper::parse_item(
	        		$RtbBidRequestImp,
	        		$ad_impression,
	        		"instl");
	        
	        // Parse DSP tag id DOM parent of RTB Auction Ad Zone in Publisher web page
	        
	        \util\ParseHelper::parse_item(
	        		$RtbBidRequestImp,
	        		$ad_impression,
	        		"tagid");
	        
	        // Parse Imp Floor Price
	        
			\util\ParseHelper::parse_item(
					$RtbBidRequestImp,
					$ad_impression,
					"bidfloor");
			
			// Parse Imp Floor Currency
			 
			\util\ParseHelper::parse_item(
					$RtbBidRequestImp,
					$ad_impression,
					"bidfloorcur");
			
			// Parse Imp https:// SSL flag
			
			\util\ParseHelper::parse_item(
					$RtbBidRequestImp,
					$ad_impression,
					"secure");
			
			// Parse Imp IFRAME Buster list
				
			\util\ParseHelper::parse_item_list(
					$RtbBidRequestImp,
					$ad_impression,
					"secure");

	        // Parse Private Markplace (PMP)
	        
			if (isset($ad_impression["pmp"])):
				$pmp = $ad_impression["pmp"];
				$RtbBidRequestPmp = new \model\openrtb\RtbBidRequestPmp();
				 
				try {
					\buyrtb\parsers\openrtb\parselets\common\imp\ParsePrivateMarketPlace::execute($logger, $this, $this->RtbBidRequest, $RtbBidRequestPmp, $pmp);
					$RtbBidRequestImp->RtbBidRequestPmp = $RtbBidRequestPmp;
				} catch (Exception $e) {
					throw new Exception($e->getMessage(), $e->getCode(), $e->getPrevious());
				}
			
			endif;
			
	        if (isset($ad_impression["banner"])):
	        	// this is a banner
	        	$ad_impression_banner = $ad_impression["banner"];
	        	$RtbBidRequestImp->media_type = "banner";
	       		$RtbBidRequestImp->RtbBidRequestBanner = new \model\openrtb\RtbBidRequestBanner();
	       		$DisplayParser = new \buyrtb\parsers\openrtb\DisplayParser();
	       		$DisplayParser->parse_request($logger, $this, $RtbBidRequestImp->RtbBidRequestBanner, $ad_impression_banner);

	       	elseif (isset($ad_impression["video"])):
	       		// this is a video
	       		$ad_impression_video = $ad_impression["video"];
	       		$RtbBidRequestImp->media_type = "video";
	       		$RtbBidRequestImp->RtbBidRequestVideo = new \model\openrtb\RtbBidRequestVideo();	
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
