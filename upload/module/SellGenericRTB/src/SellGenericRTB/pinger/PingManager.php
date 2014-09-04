<?php
/**
 * CDNPAL NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2015 CDNPAL Ltd. All Rights Reserved
 * @license GPLv3
 */

namespace pinger;

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
	
	public $winning_ad_tag;
	
	private $auction_was_won = false;
	
	private $skipped_partner_list = array();
	
	public function __construct($config, $ping_request, $PublisherInfoID, $PublisherWebsiteID, $FloorPrice, $PublisherAdZoneID, $AdName, $WebDomain) {
		
		$this->config 					= $config;
		$this->ping_request 			= $ping_request;
		$this->PublisherInfoID 			= $PublisherInfoID;
		$this->PublisherWebsiteID 		= $PublisherWebsiteID;
		$this->FloorPrice 				= floatval($FloorPrice);
		$this->PublisherAdZoneID 		= $PublisherAdZoneID;
		$this->AdName 					= $AdName;
		$this->WebDomain 				= $WebDomain;
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
			$LoopbackPartnerBid->parse_incoming_request(json_encode($this->ping_request));
			$request_id = $LoopbackPartnerBid->bid_request_id;
			$LoopbackPartnerBid->process_business_logic();
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
					json_encode($LoopbackPartnerBid->bid_responses),
					$partner_class->rtb_connection_timeout_ms,
					$partner_class->rtb_timeout_ms,
					$partner_class->partner_quality_score,
					$partner_class->verify_ssl,
					false
				);
						
				$this->RTBLoopbackPing = $RTBPinger;
				
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
			usleep(100); // micro-seconds = 1/1000th of a millisecond
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
	
	private function validate_top_level_ping_response($ping_response) {
		
		// check the bid id
		if (!isset($ping_response->id) || $ping_response->id != $this->ping_request["id"]):
			
			return false;
			
		endif;
		
		// check the currency
		if (!isset($ping_response->cur) || strtoupper($ping_response->cur) != 'USD'):
			
			return false;
			
		endif;
		
		return true;
		
	}
	
	private function validate_bid_response_object($bid_list) {
		
		$required_fields = array("id", "impid", "price", "adm");
		
		foreach ($bid_list as $bid):
			
			foreach ($required_fields as $required_field):
		
				if (!isset($bid->$required_field) || empty($bid->$required_field)):
					
					return false;
					
				endif;
				
			endforeach;
			
		endforeach;

		return true;
	
	}
	
	public function process_rtb_ping_responses() {

		foreach ($this->RTBPingerList as $RTBPinger):

			$error = "";
		
			if ($RTBPinger->ping_success == true):
	
				$json_response_data = $RTBPinger->ping_response;
				
				$ping_response 		= json_decode($json_response_data);
				
				if ($ping_response == null):
				
					$error = "Invalid JSON Response";
					continue;
					
				endif;
				
				// validate
				if ($this->validate_top_level_ping_response($ping_response) == false):
					
					$RTBPinger->ping_success 			= false;
					$RTBPinger->ping_error_message 		= "OpenRTB Ping Response Base Validation Error";
					continue;
					
				endif;
				

				
				foreach ($ping_response->seatbid as $bid_data):
				
					/*
					 * process RTB bid responses and choose a winner
					 * based on bid price.
					 */
				
					$bid_list = $bid_data->bid;
				
					// validate
					if ($this->validate_bid_response_object($bid_list) == false):

						$RTBPinger->ping_success 			= false;
						$RTBPinger->ping_error_message 		= "OpenRTB Ping Response Bid Object Validation Error";
						continue;
						
					endif;
					
					foreach ($bid_list as $bid):
	
						$bid_id 		= $bid->id;
						$bid_impid 		= $bid->impid;
						$bid_price 		= floatval($bid->price);
						$bid_adm 		= $bid->adm;		
					
						/*
						 * Check the passback tag's floor price
						 * against the bid amount
						 * Also make sure it's greater than zero
						 */
						if ($this->FloorPrice > $bid_price || $bid_price <= 0):
							continue;
						endif;
						
						
						if ($this->winning_partner_pinger === null
								||
								$bid_price > $this->winning_partner_pinger->winning_bid
								|| (
										$bid_price == $this->winning_partner_pinger->winning_bid
										&& $RTBPinger->partner_quality_score > $this->winning_partner_pinger->partner_quality_score
								)
								):
							
								// unset the last highest bidder
								if ($this->winning_partner_pinger !== null):
									$this->winning_partner_pinger->won_auction 		= false;
									$this->winning_partner_pinger->winning_bid 		= null;
								endif;
								
								// set the current highest bidder
								$this->winning_partner_pinger = $RTBPinger;
								$this->winning_partner_pinger->won_auction 			= true;
								$this->winning_partner_pinger->winning_bid 			= $bid_price;
								$this->winning_ad_tag								= $bid_adm;		
								$this->auction_was_won 								= true; 
						endif;			
					endforeach;
				
				endforeach;
				
			else:
				
				$error = $RTBPinger->ping_error_message;
				
			endif;
	
		endforeach;
		
		return $this->auction_was_won;
		
	}
	
	public function process_rtb_ping_statistics() {
		
		/*
		 * COLLECT STATS FOR THE BID LOGS
		 */
		
		$bids_total 				= 0;
		$bids_won				 	= 0;
		$bids_lost					= 0;
		$bid_errors				 	= 0;
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
			
				$bids_total++;

				if ($RTBPinger->won_auction === true):
				
					$bids_won 									= 1;
					$SellSidePartnerHourlyBids->BidsWonCounter 	= 1;
					$SellSidePartnerHourlyBids->SpendTotalGross	= floatval($RTBPinger->winning_bid) / 1000;
					
					// Subtract Ad Exchange Publisher markup
					
					$publisher_markup_rate = \util\Markup::getPublisherMarkupRate($this->PublisherWebsiteID, $this->PublisherInfoID, $this->config);
					
					$mark_down = floatval($SellSidePartnerHourlyBids->SpendTotalGross) * floatval($publisher_markup_rate);
					$adusted_amount = floatval($SellSidePartnerHourlyBids->SpendTotalGross) - floatval($mark_down);
					$SellSidePartnerHourlyBids->SpendTotalNet = $adusted_amount;

				else:
				
					$bids_lost++;
					$SellSidePartnerHourlyBids->BidsLostCounter 	= 1;
				
				endif;
				
			else:
				
				$bid_errors++;
				$SellSidePartnerHourlyBids->BidsErrorCounter 		= 1;
				$error_list[] = "PartnerID: " . $RTBPinger->partner_id . " Error Message: " . $RTBPinger->ping_error_message;
			
			endif;
		
			\util\CachedStatsWrites::incrementSellSideBidsCounterCached($this->config, $SellSidePartnerHourlyBids);
			
		endforeach;
		
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

