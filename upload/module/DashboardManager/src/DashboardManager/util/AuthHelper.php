<?php

namespace util;

class AuthHelper {
	
	/*
	 * Make a domain admin is authorized to switch user
	*/
	public static function domain_user_authorized($parent_id, $child_id) {
		

		$auth_Users_list 		= array();
		$authUsersFactory 		= \_factory\authUsers::get_instance();
		$params = array();
		$params["user_id"] 		= $child_id;
		$params["parent_id"] 	= $parent_id;
		$authUserChild		 	= $authUsersFactory->get_row($params);
		
		if ($authUserChild !== null):
			return true;
		endif;

		die("You are not authorized to perform this action: CODE 101");
		
	}
	
	public static function domain_user_authorized_publisher($parent_id, $publisher_info_id) {
		
		$ret_val = self::domain_user_authorized_publisher_passthru($parent_id, $publisher_info_id);
	
		if ($ret_val === false):
			die("You are not authorized to perform this action: CODE 101");
		else:
			return $ret_val;
		endif;
	}

	public static function isPrivateExchangePublisher($publisher_info_id) {
	
		$auth_Users_list 				= array();
		$authUsersFactory 				= \_factory\authUsers::get_instance();
		$params = array();
		$params["PublisherInfoID"] 		= $publisher_info_id;
		$authUserChild		 			= $authUsersFactory->get_row($params);
	
		if ($authUserChild === null || $authUserChild->parent_id < 1):
			return false;
		else:
			return true;
		endif;
	
		
	}
	
	public static function domain_user_authorized_publisher_passthru($parent_id, $publisher_info_id) {
	
		$auth_Users_list 				= array();
		$authUsersFactory 				= \_factory\authUsers::get_instance();
		$params = array();
		$params["PublisherInfoID"] 		= $publisher_info_id;
		$params["parent_id"] 			= $parent_id;
		$authUserChild		 			= $authUsersFactory->get_row($params);
	
		if ($authUserChild !== null):
			return true;
		endif;
	
		return false;
	
	}
	
	public static function domain_user_authorized_publisher_website_passthru($parent_id, $publisher_website_id, &$is_local) {
	
		$PublisherWebsiteFactory 		= \_factory\PublisherWebsite::get_instance();
		$params = array();
		$params["PublisherWebsiteID"] 	= $publisher_website_id;
		$PublisherWebsite	 			= $PublisherWebsiteFactory->get_row_cached($params);

		if ($PublisherWebsite !== null):
			if ($PublisherWebsite->VisibilityTypeID == 1):
				/*
				 * optionally add some flag checking for platform connection
				 * being turned on for the user here
				 */ 
				return true;
			else:
				$ret_val = self::domain_user_authorized_publisher_passthru($parent_id, $PublisherWebsite->DomainOwnerID);
				if ($ret_val === true):
					$is_local = true;
					return true;
				else:
					return false;
				endif;
			endif;
		endif;
	
		return false;
	
	}
	
	public static function domain_user_authorized_ssp_passthru($parent_id, $ssp_channel_id) {
	
		/*
		 * optionally add some flag checking for platform connection
		 * being turned on for the user here
		 */
		
		return true;
	
	}
	
	public static function parse_feed_id($raw_feed_data) {
		
		$start = strpos($raw_feed_data, ':');
		if ($start === false):
			return null;
		endif;
		
		$feed_id = substr($raw_feed_data, 0, $start);
		$next_string = substr($raw_feed_data, $start + 1);
		
		$start = strpos($next_string, ':');
		if ($start === false):
			return null;
		endif;
		
		$feed_exchange = substr($next_string, 0, $start);
		$feed_description = substr($next_string, $start + 1);
		
		return array(
			"id" 			=> $feed_id,
			"exchange" 		=> $feed_exchange,
			"description" 	=> $feed_description
		);
		
	}
	
}