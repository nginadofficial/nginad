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
		$authUserChild		 	= $authUsersFactory->get_row($params);
		
		if (isset($authUserChild->PublisherInfoID) && $authUserChild->PublisherInfoID != null):
			
			$PublisherInfoFactory 		= \_factory\PublisherInfo::get_instance();
			$params = array();
			$params["ParentID"] 		= $parent_id;
			$params["PublisherInfoID"] 	= $authUserChild->PublisherInfoID;
			$PublisherInfo	 		= $PublisherInfoFactory->get_row($params);
				
			if ($PublisherInfo !== null):
				return true;
			endif;
			
		endif;

		die("You are not authorized to perform this action: CODE 101");
		
	}
	
}