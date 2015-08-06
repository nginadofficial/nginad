<?php
/**
 * NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2016 NginAd Foundation. All Rights Reserved
 * @license GPLv3
 */

namespace transformation;

class CheckPermissions {

	public static function checkEditPermissionInsertionOrderLineItem($banner_id, $auth, $config) {
		$params = array();
		$params["InsertionOrderLineItemID"] = $banner_id;
		if (!$auth->isSuperAdmin($config)):
			$params["UserID"] = $auth->getUserID();
		endif;
		$InsertionOrderLineItemFactory = \_factory\InsertionOrderLineItem::get_instance();
		$InsertionOrderLineItem = $InsertionOrderLineItemFactory->get_row($params);
		if ($InsertionOrderLineItem === null):
			//die("You are trying to view/edit an item you do not have permissions on");
			$params["error"] = "You are trying to view/edit an item you do not have permissions on";
		endif;
		return $params;
	}

	public static function checkEditPermissionInsertionOrder($campaign_id, $auth, $config) {
		$params = array();
		$params["InsertionOrderID"] = $campaign_id;
		if (!$auth->isSuperAdmin($config)):
			$params["UserID"] = $auth->getUserID();
		endif;
		$InsertionOrderFactory = \_factory\InsertionOrder::get_instance();
		$InsertionOrder = $InsertionOrderFactory->get_row($params);
		if ($InsertionOrder === null):
			die("You are trying to view/edit an item you do not have permissions on");
		endif;
	}

	public static function checkEditPermissionInsertionOrderLineItemPreview($banner_preview_id, $auth, $config) {
		$params = array();
		$params["InsertionOrderLineItemPreviewID"] = $banner_preview_id;
		if (!$auth->isSuperAdmin($config)):
			$params["UserID"] = $auth->getUserID();
		endif;
		$InsertionOrderLineItemPreviewFactory = \_factory\InsertionOrderLineItemPreview::get_instance();
		$InsertionOrderLineItemPreview = $InsertionOrderLineItemPreviewFactory->get_row($params);
		if ($InsertionOrderLineItemPreview === null):
			//die("You are trying to view/edit an item you do not have permissions on");
			$params["error"] = "You are trying to view/edit an item you do not have permissions on";
		endif;
		return $params;
	}

	public static function checkEditPermissionInsertionOrderPreview($campaign_preview_id, $auth, $config) {
		$params = array();
		$params["InsertionOrderPreviewID"] = $campaign_preview_id;
		if (!$auth->isSuperAdmin($config)):
			$params["UserID"] = $auth->getUserID();
		endif;
		$InsertionOrderPreviewFactory = \_factory\InsertionOrderPreview::get_instance();
		$InsertionOrderPreview = $InsertionOrderPreviewFactory->get_row($params);
		if ($InsertionOrderPreview === null):
			//die("You are trying to view/edit an item you do not have permissions on");
			$params["error"] = "You are trying to view/edit an item you do not have permissions on";
		endif;
		return $params;
	}

}