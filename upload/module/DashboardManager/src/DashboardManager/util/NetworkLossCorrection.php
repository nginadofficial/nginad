<?php
/**
 * CDNPAL NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2015 CDNPAL Ltd. All Rights Reserved
 * @license GPLv3
 */

namespace util;

class NetworkLossCorrection {

	public static function getNetworkLossCorrectionRateForPublisherWebsite($publisher_website_id, $config, $cached = true) {
	
		$PublisherWebsiteImpressionsNetworkLossFactory = \_factory\PublisherWebsiteImpressionsNetworkLoss::get_instance();
		$params = array();
		$params["PublisherWebsiteID"] = $publisher_website_id;
		if ($cached === true):
			$publisher_website_impressions_network_loss = $PublisherWebsiteImpressionsNetworkLossFactory->get_row_cached($config, $params);
		else:
			$publisher_website_impressions_network_loss = $PublisherWebsiteImpressionsNetworkLossFactory->get_row($params);
		endif;
		return $publisher_website_impressions_network_loss;
	}
	
	public static function getNetworkLossCorrectionRateForPublisher($publisher_info_id, $config, $cached = true) {
	
		$PublisherImpressionsNetworkLossFactory = \_factory\PublisherImpressionsNetworkLoss::get_instance();
		$params = array();
		$params["PublisherInfoID"] = $publisher_info_id;
		if ($cached === true):
			$publisher_impression_network_loss = $PublisherImpressionsNetworkLossFactory->get_row_cached($config, $params);
		else:
			$publisher_impression_network_loss = $PublisherImpressionsNetworkLossFactory->get_row($params);
		endif;
		return $publisher_impression_network_loss;
	
	}
	
	public static function correctAmountWithNetworkLossCorrectionRateInteger($publisher_impressions_network_loss_rate, $value) {
		
		$correction_amount		= floatval($value) * floatval($publisher_impressions_network_loss_rate);
		$corrected_amount 		= floatval($value) - floatval($correction_amount);
		
		$corrected_amount 		= intval($corrected_amount);
		
		return (string)$corrected_amount;
	}
	
	public static function correctAmountWithNetworkLossCorrectionRateMoney($publisher_impressions_network_loss_rate, $value) {
		
		$correction_amount		= floatval($value) * floatval($publisher_impressions_network_loss_rate);
		$corrected_amount 		= floatval($value) - floatval($correction_amount);
		
		return sprintf("%1.7f", $corrected_amount);
	}
	
	public static function getNetworkLossCorrectionRateFromPublisherAdZone($config, $publisher_ad_zone_id, &$network_loss_rate_list) {
		
		$PublisherAdZoneFactory = \_factory\PublisherAdZone::get_instance();

		$params = array();
		$params['PublisherAdZoneID'] = $publisher_ad_zone_id;
		$PublisherAdZone = $PublisherAdZoneFactory->get_row($params);
		
		if ($PublisherAdZone === null):
			return 0;
		endif;
		
		$publisher_info_id 		= $PublisherAdZone->AdOwnerID;
		$publisher_website_id 	= $PublisherAdZone->PublisherWebsiteID;
		
		$hash_key = $publisher_info_id . '-' . $publisher_website_id;
		
		if (isset($network_loss_rate_list[$hash_key])):
		
			$publisher_impressions_network_loss_rate = $network_loss_rate_list[$hash_key];
		
		else:
		
			$publisher_impressions_network_loss_rate = \util\NetworkLossCorrection::getPublisherNetworkLossCorrectionRate($publisher_website_id, $publisher_info_id, $config, false);
			$network_loss_rate_list[$hash_key] = $publisher_impressions_network_loss_rate;
			
		endif;
		
		return $publisher_impressions_network_loss_rate;
		
	}
	
	public static function getPublisherNetworkLossCorrectionRate($publisher_website_id, $publisher_info_id, $config, $cached = true) {
	
		// is this campaign exempt from being marked up?
	
		if (in_array($publisher_info_id, $config['system']['network_loss_exempt_publisher_info_id_list'])):
			return null;
		endif;
	
		// first try ad campaign specific markup
	
		$publisher_website_impressions_network_loss = self::getNetworkLossCorrectionRateForPublisherWebsite($publisher_website_id, $config, $cached);
	
		if ($publisher_website_impressions_network_loss != null):
			return $publisher_website_impressions_network_loss->CorrectionRate;
		endif;
	
		// next try user specific markup
	
		$publisher_impression_network_loss = self::getNetworkLossCorrectionRateForPublisher($publisher_info_id, $config, $cached);
	
		if ($publisher_impression_network_loss != null):
			return $publisher_impression_network_loss->CorrectionRate;
		endif;
	
		// next send back the default impressions network loss rate
	
		return $config['system']['default_publisher_impressions_network_loss_rate'];
	}

}