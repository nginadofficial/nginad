<?php
/**
 * CDNPAL NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2015 CDNPAL Ltd. All Rights Reserved
 * @license GPLv3
 */

namespace util;

class Markup {

	public static function getMarkupRate($AdCampaign, $config, $cached = true) {

		// is this campaign exempt from being marked up?

		if (in_array($AdCampaign->UserID, $config['system']['markup_exempt_userid_list'])):
			return null;
		endif;

		// first try ad campaign specific markup

		$ad_campaign_markup = self::getMarkupForAdCampaign($AdCampaign->AdCampaignID, $config, $cached);

		if ($ad_campaign_markup != null):
			return $ad_campaign_markup->MarkupRate;
		endif;

		// next try user specific markup

		$user_markup = self::getMarkupForUser($AdCampaign->UserID, $config, $cached);

		if ($user_markup != null):
			return $user_markup->MarkupRate;
		endif;

		// next send back the default markup rate

		return $config['system']['default_demand_markup_rate'];
	}

	public static function getMarkupForAdCampaign($ad_campaign_id, $config, $cached = true) {

		$AdCampainMarkupFactory = \_factory\AdCampainMarkup::get_instance();
		$params = array();
		$params["AdCampaignID"] = $ad_campaign_id;
		if ($cached === true):
			$ad_campaign_markup = $AdCampainMarkupFactory->get_row_cached($config, $params);
		else:
			$ad_campaign_markup = $AdCampainMarkupFactory->get_row($params);
		endif;
		
		return $ad_campaign_markup;
	}

	public static function getMarkupForPublisherWebsite($publisher_website_id, $config, $cached = true) {
	
		$PublisherWebsiteMarkupFactory = \_factory\PublisherWebsiteMarkup::get_instance();
		$params = array();
		$params["PublisherWebsiteID"] = $publisher_website_id;
		if ($cached === true):
			$publisher_website_markup = $PublisherWebsiteMarkupFactory->get_row_cached($config, $params);
		else:
			$publisher_website_markup = $PublisherWebsiteMarkupFactory->get_row($params);
		endif;
		return $publisher_website_markup;
	}
	
	
	public static function getMarkupForUser($user_id, $config, $cached = true) {

		$UserMarkupDemandFactory = \_factory\UserMarkupDemand::get_instance();
		$params = array();
		$params["UserID"] = $user_id;
		if ($cached === true):
			$user_markup = $UserMarkupDemandFactory->get_row_cached($config, $params);
		else:
			$user_markup = $UserMarkupDemandFactory->get_row($params);
		endif;
		return $user_markup;

	}
	
	public static function getMarkupForPublisher($publisher_info_id, $config, $cached = true) {
	
		$PublisherMarkupFactory = \_factory\PublisherMarkup::get_instance();
		$params = array();
		$params["PublisherInfoID"] = $publisher_info_id;
		if ($cached === true):
			$user_markup = $PublisherMarkupFactory->get_row_cached($config, $params);
		else:
			$user_markup = $PublisherMarkupFactory->get_row($params);
		endif;
		return $user_markup;
	
	}
	
	public static function getPublisherMarkupRate($publisher_website_id, $publisher_info_id, $config, $cached = true) {
	
		// is this campaign exempt from being marked up?
	
		if (in_array($publisher_info_id, $config['system']['markup_exempt_publisher_info_id_list'])):
			return null;
		endif;
	
		// first try ad campaign specific markup
	
		$publisher_website_markup = self::getMarkupForPublisherWebsite($publisher_website_id, $config, $cached);
	
		if ($publisher_website_markup != null):
			return $publisher_website_markup->MarkupRate;
		endif;
	
		// next try user specific markup
	
		$publisher_markup = self::getMarkupForPublisher($publisher_info_id, $config, $cached);
	
		if ($publisher_markup != null):
			return $publisher_markup->MarkupRate;
		endif;
	
		// next send back the default markup rate
	
		return $config['system']['default_publisher_markup_rate'];
	}

}