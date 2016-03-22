-- ----------------------------
-- Table structure for NativeAd
-- ----------------------------
DROP TABLE IF EXISTS `NativeAd`;
CREATE TABLE `NativeAd` (
  `NativeAdID` int(11) NOT NULL AUTO_INCREMENT,
  `InsertionOrderLineItemID` int(11) NOT NULL,
  `LinkUrl` char(255) NOT NULL,
  `LinkClickTrackerUrlsCommaSeparated` text DEFAULT NULL,
  `LinkFallback` char(255) DEFAULT NULL,
  `TrackerUrlsCommaSeparated` text DEFAULT NULL,
  `JsLinkTracker` text DEFAULT NULL,
  `AllowedLayoutsCommaSeparated` char(255) DEFAULT NULL,
  `AllowedAdUnitsCommaSeparated` char(255) DEFAULT NULL,
  `MaxPlacements` int(10) DEFAULT NULL,
  `MaxSequence` int(10) DEFAULT NULL,
  `DateCreated` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `DateUpdated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`NativeAdID`),
  UNIQUE KEY `IDX_NativeAd` (`NativeAdID`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for NativeAdPreview
-- ----------------------------
DROP TABLE IF EXISTS `NativeAdPreview`;
CREATE TABLE `NativeAdPreview` (
  `NativeAdPreviewID` int(11) NOT NULL AUTO_INCREMENT,
  `InsertionOrderLineItemPreviewID` int(11) NOT NULL,
  `LinkUrl` char(255) NOT NULL,
  `LinkClickTrackerUrlsCommaSeparated` text DEFAULT NULL,
  `LinkFallback` char(255) DEFAULT NULL,
  `TrackerUrlsCommaSeparated` text DEFAULT NULL,
  `JsLinkTracker` text DEFAULT NULL,
  `AllowedLayoutsCommaSeparated` char(255) DEFAULT NULL,
  `AllowedAdUnitsCommaSeparated` char(255) DEFAULT NULL,
  `MaxPlacements` int(10) DEFAULT NULL,
  `MaxSequence` int(10) DEFAULT NULL,
  `DateCreated` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `DateUpdated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`NativeAdPreviewID`),
  UNIQUE KEY `IDX_NativeAdPreview` (`NativeAdPreviewID`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for NativeAdAsset
-- ----------------------------
DROP TABLE IF EXISTS `NativeAdAsset`;
CREATE TABLE `NativeAdAsset` (
  `NativeAdAssetID` int(11) NOT NULL AUTO_INCREMENT,
  `NativeAdID` int(11) NOT NULL,
  `AssetType` enum('title','image','video','data') NOT NULL DEFAULT 'image',
  `AssetRequired` int(1) NOT NULL DEFAULT 0,
  `TitleText` char(255) DEFAULT NULL,
  `ImageUrl` char(255) DEFAULT NULL,
  `ImageWidth` int(10) DEFAULT NULL,
  `ImageHeight` int(10) DEFAULT NULL,
  `VideoVastTag` text DEFAULT NULL,
  `VideoDuration` int(10) DEFAULT NULL,
  `VideoMimesCommaSeparated` char(255) DEFAULT NULL,
  `VideoProtocolsCommaSeparated` char(255) DEFAULT NULL,
  `DataType` int(10) DEFAULT NULL,
  `DataLabel` char(255) DEFAULT NULL,
  `DataValue` char(255) DEFAULT NULL,
  `LinkUrl` char(255) DEFAULT NULL,
  `LinkClickTrackerUrlsCommaSeparated` text DEFAULT NULL,
  `LinkFallback` char(255) DEFAULT NULL,
  `DateCreated` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `DateUpdated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`NativeAdAssetID`),
  UNIQUE KEY `IDX_NativeAdAsset` (`NativeAdAssetID`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for NativeAdAssetPreview
-- ----------------------------
DROP TABLE IF EXISTS `NativeAdAssetPreview`;
CREATE TABLE `NativeAdAssetPreview` (
  `NativeAdAssetPreviewID` int(11) NOT NULL AUTO_INCREMENT,
  `NativeAdPreviewID` int(11) NOT NULL,
  `AssetType` enum('title','image','video','data') NOT NULL DEFAULT 'image',
  `AssetRequired` int(1) NOT NULL DEFAULT 0,
  `TitleText` char(255) DEFAULT NULL,
  `ImageUrl` char(255) DEFAULT NULL,
  `ImageWidth` int(10) DEFAULT NULL,
  `ImageHeight` int(10) DEFAULT NULL,
  `VideoVastTag` text DEFAULT NULL,
  `VideoDuration` int(10) DEFAULT NULL,
  `VideoMimesCommaSeparated` char(255) DEFAULT NULL,
  `VideoProtocolsCommaSeparated` char(255) DEFAULT NULL,
  `DataType` int(10) DEFAULT NULL,
  `DataLabel` char(255) DEFAULT NULL,
  `DataValue` char(255) DEFAULT NULL,
  `LinkUrl` char(255) DEFAULT NULL,
  `LinkClickTrackerUrlsCommaSeparated` text DEFAULT NULL,
  `LinkFallback` char(255) DEFAULT NULL,
  `DateCreated` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `DateUpdated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`NativeAdAssetPreviewID`),
  UNIQUE KEY `IDX_NativeAdAssetPreview` (`NativeAdAssetPreviewID`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
