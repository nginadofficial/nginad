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
		
		$auth_Users_list 				= array();
		$authUsersFactory 				= \_factory\authUsers::get_instance();
		$params = array();
		$params["PublisherInfoID"] 		= $publisher_info_id;
		$params["parent_id"] 			= $parent_id;
		$authUserChild		 			= $authUsersFactory->get_row($params);
	
		if ($authUserChild !== null):
			return true;
		endif;
	
		die("You are not authorized to perform this action: CODE 101");
	
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
	
}