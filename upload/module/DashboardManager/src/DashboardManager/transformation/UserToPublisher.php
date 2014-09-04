<?php

namespace transformation;

class UserToPublisher {
	
	public static function user_id_to_publisher_info_id($effective_id) {
		
		$authUsersFactory = \_factory\authUsers::get_instance();
		$params["user_id"] = $effective_id;
		$auth_user = $authUsersFactory->get_row($params);
		return $auth_user->PublisherInfoID;
	}
	
}