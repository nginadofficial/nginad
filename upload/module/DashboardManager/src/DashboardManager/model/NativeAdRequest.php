<?php
/**
 * NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2016 NginAd Foundation. All Rights Reserved
 * @license GPLv3
 */

namespace model;

class NativeAdRequest
{
    public $NativeAdRequestID;
    public $PublisherAdZoneID;
    public $LinkUrl;
    public $LinkClickTrackerUrlsCommaSeparated;
    public $LinkFallback;
    public $TrackerUrlsCommaSeparated;
    public $JsLinkTracker;
    public $AllowedLayoutsCommaSeparated;
    public $AllowedAdUnitsCommaSeparated;
    public $MaxPlacements								= 1;
    public $MaxSequence 								= 0;
    public $DateCreated;
    public $DateUpdated;

}
