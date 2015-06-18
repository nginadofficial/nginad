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
	
}