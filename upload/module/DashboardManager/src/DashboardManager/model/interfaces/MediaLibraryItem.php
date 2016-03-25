<?php
/**
 * NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2016 NginAd Foundation. All Rights Reserved
 * @license GPLv3
 */

namespace model\interfaces;

interface MediaLibraryItem {
	
	/**
	 * Retrieve UserID
	 *
	 * @return UserID
	 */
	public function getUserID();
	
	/**
	 * Retrieve AdName
	 *
	 * @return AdName
	 */
	public function getAdName();
	
	/**
	 * Retrieve Description
	 *
	 * @return Description
	 */
	public function getDescription();
	
	/**
	 * Retrieve MediaType
	 *
	 * @return MediaType
	 */
	public function getMediaType();
	
	/**
	 * Retrieve DateCreated
	 *
	 * @return DateCreated
	 */
	public function getDateCreated();
	
	/**
	 * Retrieve DateUpdated
	 *
	 * @return DateUpdated
	 */
	public function getDateUpdated();
}
	