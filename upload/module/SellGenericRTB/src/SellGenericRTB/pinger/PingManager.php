<?php
/**
 * CDNPAL NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2015 CDNPAL Ltd. All Rights Reserved
 * @license GPLv3
 */

namespace pinger;
use \Exception;

class PingManager {
	
	private $RTBPingerList = array();
	
	private $RTBLoopbackPing = null;
	
	private $config;
	
	private $ping_request;

	private $winning_partner_pinger;

	private $PublisherAdZoneID;
	
	private $PublisherInfoID;
	
	private $PublisherWebsiteID;
	
	private $FloorPrice;
	
	private $AdName;
	
	private $WebDomain;
	
	private $ImpressionType;
	
	private $is_second_price_auction;
	
	private $publisher_markup_rate = 40;
	
	private $skipped_partner_list = array();
	
	public function __construct($config, $ping_request, $PublisherInfoID, $PublisherWebsiteID, $FloorPrice, $PublisherAdZoneID, $AdName, $WebDomain, $ImpressionType) {
		
		$this->config 					= $config;
		$this->ping_request 			= json_decode($ping_request, true);
		$this->PublisherInfoID 			= $PublisherInfoID;
		$this->PublisherWebsiteID 		= $PublisherWebsiteID;
		$this->FloorPrice 				= floatval($FloorPrice);
		$this->PublisherAdZoneID 		= $PublisherAdZoneID;
		$this->AdName 					= $AdName;
		$this->WebDomain 				= $WebDomain;
		$this->ImpressionType			= $ImpressionType;
		$this->is_second_price_auction	= $this->config['settings']['rtb']['second_price_auction'];
		
		// is this a 1rst price or 2nd price auction?
		$this->ping_request["at"]		= ($this->is_second_price_auction === true) ? 2 : 1;
		
		$this->publisher_markup_rate = \util\Markup::getPublisherMarkupRate($this->PublisherWebsiteID, $this->PublisherInfoID, $this->config);
		
	}
	
	public function set_up_local_demand_ping_clients() {
		
		$request_id = "Not Given";
		try {
			
			/*
			 * First treat this bid request JSON as if it came in 
			 * from a regular remote buy side partner (DSP)
			 */
			
			\buyloopbackpartner\LoopbackPartnerInit::init();
			
			$LoopbackPartnerBid = new \buyloopbackpartner\LoopbackPartnerBid($this->config, $this->config['buyside_rtb']['supply_partners']['BuyLoopbackPartner']['buyer_id']);
			$LoopbackPartnerBid->is_local_request = true;
			$validated = $LoopbackPartnerBid->parse_incoming_request(json_encode($this->ping_request));
			if ($validated === true):
				$request_id = $LoopbackPartnerBid->RtbBidRequest->id;
				$LoopbackPartnerBid->process_business_logic();
				$LoopbackPartnerBid->convert_ads_to_bid_response();
			endif;
			$LoopbackPartnerBid->build_outgoing_bid_response();

			if ($LoopbackPartnerBid->had_bid_response == true):
			
				if (\buyloopbackpartner\LoopbackPartnerLogger::get_instance()->setting_only_log_bids == true):
				
					\buyloopbackpartner\LoopbackPartnerLogger::get_instance()->output_log($this->config);

				endif;
			
				/*
				 * Next hook up the bid responses from our local demand dashboard customers
				 * to the publishers' RTB bid request manager
				 */
	
				$sell_side_partners_dir = __DIR__ . '/../../../../SellSidePartners';
				
				require_once($sell_side_partners_dir . '/LoopbackPartner.php');
				
				$partner_class = new \LoopbackPartner();
				
				$RTBPinger = new RTBPinger(
					$partner_class->partner_name,
					$partner_class->partner_id,
					$partner_class->partner_rtb_url,  // no url needed for local demand check
					$LoopbackPartnerBid->bid_responses,
					$partner_class->rtb_connection_timeout_ms,
					$partner_class->rtb_timeout_ms,
					$partner_class->partner_quality_score,
					$partner_class->verify_ssl,
					false
				);
				$RTBPinger->is_loopback_pinger			= true;
				$this->RTBLoopbackPing 					= $RTBPinger;
				
			endif;
			
		} catch (Exception $e) {
			\buyloopbackpartner\LoopbackPartnerLogger::get_instance()->log[] = "BID EXCEPTION: ID: " . $request_id . " MESSAGE: " . $e->getMessage();
		}
		if (\buyloopbackpartner\LoopbackPartnerLogger::get_instance()->setting_only_log_bids == false):
			\buyloopbackpartner\LoopbackPartnerLogger::get_instance()->output_log($this->config);
		endif;

		
	}
	
	
	public function set_up_remote_rtb_ping_clients() {
		
		$sell_side_partners_dir = __DIR__ . '/../../../../SellSidePartners';

		foreach ($this->config['sellside_rtb']['demand_partners'] as $key => $demand_partner):
		
			if ($key == 'LoopbackPartner'):
			
				/*
				 * The loopback partner is only used for direct bidding with the 
				 * set_up_local_demand_ping_clients above.
				 */
				continue;
			
			endif;
		
			if ($demand_partner['ping_enabled'] == false):
				
				$this->skipped_partner_list[] = $demand_partner['partner_id'];
				continue;
				
			endif;
		
			require_once($sell_side_partners_dir . '/' . $demand_partner['class_name'] . '.php');
		
			$partner_class = new $demand_partner['class_name']();
			
			// optionally customize ping data for a specific demand partner
			$json_ping_data = $partner_class->customize($this->ping_request);
			
			$RTBPinger = new RTBPinger(
					$partner_class->partner_name,
					$partner_class->partner_id,
					$demand_partner['partner_rtb_url'],
					json_encode($json_ping_data),
					$partner_class->rtb_connection_timeout_ms,
					$partner_class->rtb_timeout_ms,
					$partner_class->partner_quality_score,
					$partner_class->verify_ssl,
					$demand_partner['timeout_enabled']
					);
			
			$this->RTBPingerList[]					= $RTBPinger;
			
		endforeach;

	}
	
	public function ping_rtb_ping_clients() {
		
		/*
		 * TODO:
		 * 
		 * Find a way to time each individual ping with curl_multi_exec
		 * http://php.net/manual/en/function.microtime.php
		 * Then add that to the stats.
		 */
		
		$mh = curl_multi_init();
		$curl_array = array();
		for ($i = 0; $i < count($this->RTBPingerList); $i++):
			$curl_array[$i] = $this->RTBPingerList[$i]->get_rtb_ping_curl_request();
			curl_multi_add_handle($mh, $curl_array[$i]);
		endfor;
		$active = null;
		do {
			// usleep(100); // micro-seconds = 1/1000th of a millisecond
			$status = curl_multi_exec($mh, $active);
		} while($status === CURLM_CALL_MULTI_PERFORM || $active);
		
		for ($i = 0; $i < count($this->RTBPingerList); $i++):
			// Check for errors
			$curl_error = curl_error($curl_array[$i]);
			if (!$curl_error):
				$this->RTBPingerList[$i]->ping_success			= true;
				$this->RTBPingerList[$i]->ping_response 		= curl_multi_getcontent($curl_array[$i]);
			else:
				$this->RTBPingerList[$i]->ping_success			= false;
				$this->RTBPingerList[$i]->ping_response 		= null;
				$this->RTBPingerList[$i]->ping_error_message 	= $curl_error;
			endif;
		endfor;
		
		for ($i = 0; $i < count($this->RTBPingerList); $i++):
			curl_multi_remove_handle($mh, $curl_array[$i]);
		endfor;
		
		curl_multi_close($mh);
		
		/*
		 * If loopback rtb is enabled for demand partners
		 * then add it here
		 */
		
		if ($this->RTBLoopbackPing != null):
			$this->RTBLoopbackPing->ping_success			= true;
			/*
			 * ping_response is the same as ping_data in the case of a 
			 * local remnant demand Loopback partner
			 */ 
			$this->RTBLoopbackPing->ping_response 			= $this->RTBLoopbackPing->get_ping_data();
			$this->RTBPingerList[] = $this->RTBLoopbackPing;
		endif;

	}
	

	
	public function process_rtb_ping_responses() {

		/*
		 * Plain Old PHP Object (POPO)
		 * http://en.wikipedia.org/wiki/Plain_Old_Java_Object
		 * 
		 * Initialize POPO! to send around the Workflows
		 */
		
		$AuctionPopo = new \sellrtb\workflows\tasklets\popo\AuctionPopo();
		$AuctionPopo->publisher_markup_rate 	= $this->publisher_markup_rate;
		$AuctionPopo->FloorPrice 				= $this->FloorPrice;
		$AuctionPopo->is_second_price_auction 	= $this->is_second_price_auction;
		$AuctionPopo->ImpressionType 			= $this->ImpressionType;
		
		/*
		 * Get the single impid. If and when we start sending multiple 
		 * impressions we will send an array of these impids to match
		 * to the multiple banner responses.
		 */

		$AuctionPopo->request_impid = $this->ping_request["imp"][0]["id"];

		$logger = \rtbsellv22\RtbSellV22Logger::get_instance();
		$OpenRTBWorkflow = new \sellrtb\workflows\OpenRTBWorkflow();
		 
		$this->winning_partner_pinger = $OpenRTBWorkflow->process_business_rules_workflow($logger, $this->config, $this->RTBPingerList, $AuctionPopo);

		return $AuctionPopo;
		
	}
	
	public function process_rtb_ping_statistics(&$AuctionPopo) {
		
		/*
		 * COLLECT STATS FOR THE BID LOGS
		 */
		
		$bids_total 				= 0;
		$bids_won				 	= 0;
		$bids_lost					= 0;
		$bid_errors				 	= 0;
		$spend_total_gross			= 0;
		$spend_total_net			= 0;
		$error_list				= array();
		
		foreach ($this->RTBPingerList as $RTBPinger):
		
			$SellSidePartnerHourlyBids = new \model\SellSidePartnerHourlyBids();

			$SellSidePartnerHourlyBids->SellSidePartnerID		= $RTBPinger->partner_id;
			$SellSidePartnerHourlyBids->PublisherAdZoneID		= $this->PublisherAdZoneID;
			$SellSidePartnerHourlyBids->BidsWonCounter			= 0;
			$SellSidePartnerHourlyBids->BidsLostCounter			= 0;
			$SellSidePartnerHourlyBids->BidsErrorCounter		= 0;
			$SellSidePartnerHourlyBids->SpendTotalGross			= 0;
			$SellSidePartnerHourlyBids->SpendTotalNet			= 0;
			
			if ($RTBPinger->ping_success == true):
			
				$bids_total	+= $RTBPinger->total_bids;

				if ($RTBPinger->won_auction === true):
				
					$bids_won									+= $RTBPinger->won_bids;
					$bids_lost									+= $RTBPinger->lost_bids;
					$SellSidePartnerHourlyBids->BidsWonCounter 	= $RTBPinger->won_bids;
					
					if ($AuctionPopo->is_second_price_auction === true):
						$SellSidePartnerHourlyBids->SpendTotalGross	= floatval($AuctionPopo->second_price_winning_bid_price) / 1000;
					else:
						$SellSidePartnerHourlyBids->SpendTotalGross	= floatval($RTBPinger->winning_bid) / 1000;
					endif;
					
					$spend_total_gross = $SellSidePartnerHourlyBids->SpendTotalGross;
					
					// Subtract Ad Exchange Publisher markup

					$mark_down = floatval($SellSidePartnerHourlyBids->SpendTotalGross) * floatval($this->publisher_markup_rate);
					$adusted_amount = floatval($SellSidePartnerHourlyBids->SpendTotalGross) - floatval($mark_down);
					$SellSidePartnerHourlyBids->SpendTotalNet = $adusted_amount;
					
					$spend_total_net = $SellSidePartnerHourlyBids->SpendTotalNet;

				else:
					$bids_lost										+= $RTBPinger->lost_bids;
					$SellSidePartnerHourlyBids->BidsLostCounter 	= $RTBPinger->lost_bids;
				
				endif;
				
			else:
				
				$bid_errors++;
				$SellSidePartnerHourlyBids->BidsErrorCounter 		= 1;
				$error_list[] = "PartnerID: " . $RTBPinger->partner_id . " Error Message: " . $RTBPinger->ping_error_message;
			
			endif;
		
			\util\CachedStatsWrites::incrementSellSideBidsCounterCached($this->config, $SellSidePartnerHourlyBids);
			
		endforeach;
		
		$PublisherHourlyBids = new \model\PublisherHourlyBids();
			
		$PublisherHourlyBids->PublisherAdZoneID		= $this->PublisherAdZoneID;
		$PublisherHourlyBids->AuctionCounter		= 1;
		$PublisherHourlyBids->BidsWonCounter		= $bids_won;
		$PublisherHourlyBids->BidsLostCounter		= $bids_lost;
		$PublisherHourlyBids->BidsErrorCounter		= $bid_errors;
		$PublisherHourlyBids->SpendTotalGross		= $spend_total_gross;
		$PublisherHourlyBids->SpendTotalNet			= $spend_total_net;
		
		if ($AuctionPopo->ImpressionType == "video" && $AuctionPopo->auction_was_won && \util\ParseHelper::isVastURL($AuctionPopo->winning_ad_tag) === true):
			
			/*
			 * If this is a video impression record the winning auction 
			 * information when the VASTAdTagURI is loaded from the 
			 * publisher's video player.
			 */
			$PublisherHourlyBidsCopy = new \model\PublisherHourlyBids();
		
			$PublisherHourlyBidsCopy->PublisherAdZoneID	= $this->PublisherAdZoneID;
			$PublisherHourlyBidsCopy->AuctionCounter	= 0;
			$PublisherHourlyBidsCopy->BidsWonCounter	= 1;
			$PublisherHourlyBidsCopy->BidsLostCounter	= 0;
			$PublisherHourlyBidsCopy->BidsErrorCounter	= 0;
			$PublisherHourlyBidsCopy->SpendTotalGross	= $spend_total_gross;
			$PublisherHourlyBidsCopy->SpendTotalNet		= $spend_total_net;
			
			$AuctionPopo->vast_publisher_imp_obj 	= $PublisherHourlyBidsCopy;
			
			/*
			 * Record the general impression auction information here now.
			 */
			
			$PublisherHourlyBids->BidsWonCounter		= 0;
			$PublisherHourlyBids->SpendTotalGross		= 0;
			$PublisherHourlyBids->SpendTotalNet			= 0;
		endif;
		
		\util\CachedStatsWrites::incrementPublisherBidsCounterCached($this->config, $PublisherHourlyBids);
		
		$log_header = "----------------------------------------------------------------\n";
		$log_header.= "NEW BID RESPONSE, WEBSITE: " . $this->WebDomain . ", PubZoneID: " . $this->PublisherAdZoneID . ", AD: " . $this->AdName;

		\rtbsellv22\RtbSellV22Logger::get_instance()->log[] = $log_header;
		
		$log_header = "NEW BID RESPONSE, WEBSITE: " . $this->WebDomain . ", PubZoneID: " . $this->PublisherAdZoneID . ", AD: " . $this->AdName;
		
		\rtbsellv22\RtbSellV22Logger::get_instance()->min_log[] = $log_header;
		
		$log = "----------------------------------------------------------------";
		$log.= "\nDate: " 		. date('m-d-Y H:i:s');
		$log.= "\nTotal Bids: " 	. $bids_total;
		$log.= "\nBids Won: " 	. $bids_won;
		$log.= "\nBids Lost: " 	. $bids_lost;
		$log.= "\nBid Errors: " 	. $bid_errors;
		$log.= "\nError List: " 	. implode(",", $error_list);

		foreach ($this->skipped_partner_list as $skipped_partner):			
			$log.= "\nSkipped Partner: " . $skipped_partner;
		endforeach;
		
		$log.= "\n----------------------------------------------------------------\n";
		
		\rtbsellv22\RtbSellV22Logger::get_instance()->log[] = $log;
		\rtbsellv22\RtbSellV22Logger::get_instance()->min_log[] = $log;
		
	}
}

