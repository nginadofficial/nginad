<?php

namespace sellrtb\workflows\tasklets\popo;

class AuctionPopo {
	
	public $PingerList = array();
	
	public $SelectedPingerList = array();
	
	public $winning_partner_pinger;
	public $winning_ad_tag;

	public $winning_partner_seat;
	
	public $winning_bid_price;
	public $winning_adjusted_bid_price;
	
	public $winning_partner_id;
	public $winning_seat;
	
	// second price
	public $is_second_price_auction;
	
	public $second_price_winning_bid_price;
	public $second_price_winning_adjusted_bid_price;
	
	public $highest_bid_price;
	public $highest_partner_score;
	
	public $auction_was_won;
	public $loopback_buyer_id;
	public $loopback_demand_partner_ad_campaign_banner_id;
	public $loopback_demand_partner_won;
	public $FloorPrice;
	public $publisher_markup_rate;
	
	public $bid_price_list;
	public $adjusted_bid_price_list;
	
	/*
	 * NOTICE URL
	 * 
	 * In the case of a display ad, this may compliment the adm containing the ad tag code
	 * 
	 * In the case of a VAST video pre-roll, this may either complement the VAST XML
	 * in the adm, or it may be a URL which loads the VAST XML in which case you need to
	 * call it and reverse proxy the contents
	 */
	public $nurl;
	
	public $ImpressionType;
	
	public $request_impid;
	
	public $vast_publisher_imp_obj;
}
