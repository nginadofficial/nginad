-- ----------------------------
-- Table structure for NativeAdRequest
-- ----------------------------
DROP TABLE IF EXISTS `NativeAdRequest`;
CREATE TABLE `NativeAdRequest` (
  `NativeAdRequestID` int(11) NOT NULL AUTO_INCREMENT,
  `PublisherAdZoneID` int(11) NOT NULL,
  `LayoutId` int(11) DEFAULT NULL,
  `AdUnitId` int(11) DEFAULT NULL,
  `Context` int(11) DEFAULT NULL,
  `ContextSubtype` int(11) DEFAULT NULL,
  `PlacementType` int(11) DEFAULT NULL,
  `PlacementCount` int(11) DEFAULT NULL,
  `DateCreated` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `DateUpdated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`NativeAdRequestID`),
  UNIQUE KEY `IDX_NativeAdRequest` (`NativeAdRequestID`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for NativeAdRequestAsset
-- ----------------------------
DROP TABLE IF EXISTS `NativeAdRequestAsset`;
CREATE TABLE `NativeAdRequestAsset` (
  `NativeAdRequestAssetID` int(11) NOT NULL AUTO_INCREMENT,
  `NativeAdRequestID` int(11) NOT NULL,
  `AssetType` enum('title','image','video','data') NOT NULL DEFAULT 'image',
  `TitleMinLength` int(11) DEFAULT NULL,
  `ImageMinWidth` int(11) DEFAULT NULL,
  `ImageMinHeight` int(11) DEFAULT NULL,
  `ImageMimesCommaSeparated` char(255) DEFAULT NULL,
  `VideoMimesCommaSeparated` char(255) DEFAULT NULL,
  `VideoMinDuration` int(11) DEFAULT NULL,
  `VideoMaxDuration` int(11) DEFAULT NULL,
  `VideoProtocolsCommaSeparated` char(100) DEFAULT NULL,
  `DataType` int(11) DEFAULT NULL,
  `DataLength` int(11) DEFAULT NULL,
  `DateCreated` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `DateUpdated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`NativeAdRequestAssetID`),
  UNIQUE KEY `IDX_NativeAdRequestAsset` (`NativeAdRequestAssetID`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


-- ----------------------------
-- Table structure for NativeAdResponseItem
-- ----------------------------
DROP TABLE IF EXISTS `NativeAdResponseItem`;
CREATE TABLE `NativeAdResponseItem` (
  `NativeAdResponseItemID` int(11) NOT NULL AUTO_INCREMENT,
  `UserID` int(11) NOT NULL,
  `AdName` char(255) NOT NULL,
  `MediaType` enum('image','video') NOT NULL DEFAULT 'image',
  `LinkUrl` char(255) DEFAULT NULL,
  `TrackerUrlsCommaSeparated` text DEFAULT NULL,
  `JsLinkTracker` text DEFAULT NULL,
  `ImageHeight` int(11) DEFAULT NULL,
  `ImageWidth` int(11) DEFAULT NULL,
  `DateCreated` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `DateUpdated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`NativeAdResponseItemID`),
  UNIQUE KEY `UQ_NativeAdResponseItem` (`NativeAdResponseItemID`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for NativeAdResponseItemAsset
-- ----------------------------
DROP TABLE IF EXISTS `NativeAdResponseItemAsset`;
CREATE TABLE `NativeAdResponseItemAsset` (
  `NativeAdResponseItemAssetID` int(11) NOT NULL AUTO_INCREMENT,
  `NativeAdResponseItemID` int(11) NOT NULL,
  `AssetType` enum('title','image','video','data') NOT NULL DEFAULT 'image',
  `AssetRequired` int(1) NOT NULL DEFAULT 0,
  `TitleText` char(255) DEFAULT NULL,
  `ImageUrl` char(255) DEFAULT NULL,
  `ImageWidth` int(10) DEFAULT NULL,
  `ImageHeight` int(10) DEFAULT NULL,
  `VideoVastTag` text DEFAULT NULL,
  `VideoDuration` int(10) DEFAULT NULL,
  `VideoMimesCommaSeparated` char(100) DEFAULT NULL,
  `VideoProtocolsCommaSeparated` char(100) DEFAULT NULL,
  `DataType` int(10) DEFAULT NULL,
  `DataLabel` char(255) DEFAULT NULL,
  `DataValue` char(255) DEFAULT NULL,
  `LinkUrl` char(255) DEFAULT NULL,
  `LinkClickTrackerUrlsCommaSeparated` text DEFAULT NULL,
  `LinkFallback` char(255) DEFAULT NULL,
  `DateCreated` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `DateUpdated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`NativeAdResponseItemAssetID`),
  UNIQUE KEY `UQ_INativeAdResponseItemAsset` (`NativeAdResponseItemAssetID`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;