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