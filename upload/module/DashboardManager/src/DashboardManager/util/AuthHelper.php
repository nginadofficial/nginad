<?php

namespace util;
use Zend\Session\Container;
use Zend\View\Model\ViewModel;

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
	
	public static function login($view) {
		$user_session = new Container('user');
		 
		//if already login, redirect to success page
		if ($view->getAuthService()->hasIdentity()):
			$user_session->message = '';
			if ($view->getAuthService()->getPublisherInfoID() != null):
				return $view->redirect()->toRoute('publisher');
			else:
				return $view->redirect()->toRoute('private-exchange');
			endif;
		endif;
		
		$logo_url = null;
		 
		$http_host = @$_SERVER['HTTP_HOST'];
		if ($http_host != null):
			$PrivateExchangeVanityDomainFactory = \_factory\PrivateExchangeVanityDomain::get_instance();
			
			$params = array();
			$params["VanityDomain"] = strtolower($http_host);
			$PrivateExchangeVanityDomain = $PrivateExchangeVanityDomainFactory->get_row($params);
			
			if ($PrivateExchangeVanityDomain != null):
				$theme_path = '/vdomain/' . $PrivateExchangeVanityDomain->UserID . '/theme.css';
				if ($PrivateExchangeVanityDomain->UseLogo == 1):
					$logo_url = '/vdomain/' . $PrivateExchangeVanityDomain->UserID . '/logo-lg.png';
				endif;
			endif;
		endif;
		 
		$viewModel = new ViewModel(array(
				'messages'  => $user_session->message,
				'center_class' => 'centerj',
				'logo_url' => $logo_url,
				'dashboard_view' => 'login'
		));
		
		return $viewModel->setTemplate('dashboard-manager/auth/login.phtml');
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
	
	public static function domain_user_authorized_px_publisher_website_passthru($config, $parent_id, $publisher_website_id, &$is_local) {
	
		$PublisherWebsiteFactory 		= \_factory\PublisherWebsite::get_instance();
		$params = array();
		$params["PublisherWebsiteID"] 	= $publisher_website_id;
		$PublisherWebsite	 			= $PublisherWebsiteFactory->get_row_cached($config, $params);

		if ($PublisherWebsite !== null):
		
			$ret_val = self::domain_user_authorized_publisher_passthru($parent_id, $PublisherWebsite->DomainOwnerID);
			if ($ret_val === true):
				$is_local = true;
				return true;
			elseif ($PublisherWebsite->VisibilityTypeID == 1):
				/*
				 * add a flag checking for platform connection
				 * being turned on for the user here
				 */
				if (\util\CreditHelper::wasApprovedForPlatfromConnectionInventoryAuthUserID($parent_id)):
					return true;
				else:
					return false;
				endif;
			else:
				return false;
			endif;
		endif;
	
		return false;
	
	}
	
	public static function domain_user_authorized_publisher_website($parent_id, $publisher_website_id) {
	
		$PublisherWebsiteFactory 		= \_factory\PublisherWebsite::get_instance();
		$params = array();
		$params["PublisherWebsiteID"] 	= $publisher_website_id;
		$PublisherWebsite	 			= $PublisherWebsiteFactory->get_row($params);
	
		if (!\util\AuthHelper::domain_user_authorized_publisher($parent_id, $PublisherWebsite->DomainOwnerID)):
			die("You are not authorized to perform this action: CODE 101");
		endif;
		
		return true;
	
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