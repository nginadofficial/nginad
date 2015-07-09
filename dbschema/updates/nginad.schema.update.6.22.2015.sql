-- Private exchange publishers add website and ad zone visibility flags
-- Do they want the website or ad zone to be available in the platform connection?
-- Do they want the website and ad zone to be available to SSPs being pinged with RTB requests?
ALTER TABLE  `PublisherWebsite` ADD `VisibilityTypeID` int(4) NOT NULL DEFAULT 1 AFTER  `DomainOwnerID` ;

-- ----------------------------
-- Table structure for VisibilityType
-- ----------------------------
DROP TABLE IF EXISTS `VisibilityType`;
CREATE TABLE `VisibilityType` (
  `VisibilityTypeID` int(11) unsigned NOT NULL,
  `Description` varchar(255) NOT NULL,
  `DateCreated` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `DateUpdated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`VisibilityTypeID`),
  UNIQUE KEY `VisibilityType_UNIQUE` (`VisibilityTypeID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of PublisherAdZoneType
-- ----------------------------
INSERT INTO `VisibilityType` VALUES ('1', 'Platform Connection', '2014-08-17 12:16:21', '2014-08-17 12:16:21');
INSERT INTO `VisibilityType` VALUES ('2', 'Private Exchange', '2014-08-17 12:16:21', '2014-08-17 12:16:21');

-- build the new tables which will hold the PMP deals

-- ----------------------------
-- Table structure for PmpDealPublisherWebsiteToInsertionOrder
-- ----------------------------
DROP TABLE IF EXISTS `PmpDealPublisherWebsiteToInsertionOrder`;
CREATE TABLE `PmpDealPublisherWebsiteToInsertionOrder` (
  `PmpDealPublisherWebsiteToInsertionOrderID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `PublisherWebsiteID` int(11) unsigned NOT NULL,
  `PublisherWebsiteLocal` int(4) unsigned NOT NULL,
  `PublisherWebsiteDescription` char(50) NOT NULL,
  `InsertionOrderID` int(11) unsigned NOT NULL,
  `Enabled` int(4) unsigned NOT NULL DEFAULT 0,
  `DateCreated` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `DateUpdated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`PmpDealPublisherWebsiteToInsertionOrderID`),
  UNIQUE KEY `PmpDealPublisherWebsiteToInsertionOrder_UNIQUE` (`PmpDealPublisherWebsiteToInsertionOrderID`),
  KEY `FK_Publisher_Website_ID` (`PublisherWebsiteID`),
  KEY `FK_InsertionOrder_ID` (`InsertionOrderID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for PmpDealPublisherWebsiteToInsertionOrderLineItem
-- ----------------------------
DROP TABLE IF EXISTS `PmpDealPublisherWebsiteToInsertionOrderLineItem`;
CREATE TABLE `PmpDealPublisherWebsiteToInsertionOrderLineItem` (
  `PmpDealPublisherWebsiteToInsertionOrderLineItemID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `PublisherWebsiteID` int(11) unsigned NOT NULL,
  `PublisherWebsiteLocal` int(4) unsigned NOT NULL,
  `PublisherWebsiteDescription` char(50) NOT NULL,
  `InsertionOrderLineItemID` int(11) unsigned NOT NULL,
  `Enabled` int(4) unsigned NOT NULL DEFAULT 0,
  `DateCreated` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `DateUpdated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`PmpDealPublisherWebsiteToInsertionOrderLineItemID`),
  UNIQUE KEY `PmpDealPublisherWebsiteToInsertionOrderLineItem_UNIQUE` (`PmpDealPublisherWebsiteToInsertionOrderLineItemID`),
  KEY `FK_Publisher_Website_ID` (`PublisherWebsiteID`),
  KEY `FK_InsertionOrderLineItem_ID` (`InsertionOrderLineItemID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for PmpDealPublisherWebsiteToInsertionOrderPreview
-- ----------------------------
DROP TABLE IF EXISTS `PmpDealPublisherWebsiteToInsertionOrderPreview`;
CREATE TABLE `PmpDealPublisherWebsiteToInsertionOrderPreview` (
  `PmpDealPublisherWebsiteToInsertionOrderPreviewID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `PublisherWebsiteID` int(11) unsigned NOT NULL,
  `PublisherWebsiteLocal` int(4) unsigned NOT NULL,
  `PublisherWebsiteDescription` char(50) NOT NULL,
  `InsertionOrderPreviewID` int(11) unsigned NOT NULL,
  `Enabled` int(4) unsigned NOT NULL DEFAULT 0,
  `DateCreated` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `DateUpdated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`PmpDealPublisherWebsiteToInsertionOrderPreviewID`),
  UNIQUE KEY `PmpDealPublisherWebsiteToInsertionOrderPreview_UNIQUE` (`PmpDealPublisherWebsiteToInsertionOrderPreviewID`),
  KEY `FK_Publisher_Website_ID` (`PublisherWebsiteID`),
  KEY `FK_InsertionOrder_ID` (`InsertionOrderPreviewID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for PmpDealPublisherWebsiteToInsertionOrderLineItemPreview
-- ----------------------------
DROP TABLE IF EXISTS `PmpDealPublisherWebsiteToInsertionOrderLineItemPreview`;
CREATE TABLE `PmpDealPublisherWebsiteToInsertionOrderLineItemPreview` (
  `PmpDealPublisherWebsiteToInsertionOrderLineItemPreviewID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `PublisherWebsiteID` int(11) unsigned NOT NULL,
  `PublisherWebsiteLocal` int(4) unsigned NOT NULL,
  `PublisherWebsiteDescription` char(50) NOT NULL,
  `InsertionOrderLineItemPreviewID` int(11) unsigned NOT NULL,
  `Enabled` int(4) unsigned NOT NULL DEFAULT 0,
  `DateCreated` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `DateUpdated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`PmpDealPublisherWebsiteToInsertionOrderLineItemPreviewID`),
  UNIQUE KEY `PmpDealPublisherWebsiteToInsertionOrderLineItemPreview_UNIQUE` (`PmpDealPublisherWebsiteToInsertionOrderLineItemPreviewID`),
  KEY `FK_Publisher_Website_ID` (`PublisherWebsiteID`),
  KEY `FK_InsertionOrderLineItem_ID` (`InsertionOrderLineItemPreviewID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for SspRtbChannelToInsertionOrder
-- ----------------------------
DROP TABLE IF EXISTS `SspRtbChannelToInsertionOrder`;
CREATE TABLE `SspRtbChannelToInsertionOrder` (
  `SspRtbChannelToInsertionOrderID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `SspPublisherChannelID` char(100) NOT NULL,
  `SspPublisherChannelDescription` char(100) NOT NULL,
  `InsertionOrderID` int(11) unsigned NOT NULL,
  `Enabled` int(4) unsigned NOT NULL DEFAULT 0,
  `DateCreated` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `DateUpdated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`SspRtbChannelToInsertionOrderID`),
  UNIQUE KEY `SspRtbChannelToIO_UNIQUE` (`SspRtbChannelToInsertionOrderID`),
  KEY `FK_InsertionOrder_ID` (`InsertionOrderID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for SspRtbChannelToInsertionOrderLineItem
-- ----------------------------
DROP TABLE IF EXISTS `SspRtbChannelToInsertionOrderLineItem`;
CREATE TABLE `SspRtbChannelToInsertionOrderLineItem` (
  `SspRtbChannelToInsertionOrderLineItemID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `SspPublisherChannelID` char(100) NOT NULL,
  `SspPublisherChannelDescription` char(100) NOT NULL,
  `InsertionOrderLineItemID` int(11) unsigned NOT NULL,
  `Enabled` int(4) unsigned NOT NULL DEFAULT 0,
  `DateCreated` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `DateUpdated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`SspRtbChannelToInsertionOrderLineItemID`),
  UNIQUE KEY `SspRtbChannelToIOLineItem_UNIQUE` (`SspRtbChannelToInsertionOrderLineItemID`),
  KEY `FK_InsertionOrderLineItem_ID` (`InsertionOrderLineItemID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for SspRtbChannelToInsertionOrderPreview
-- ----------------------------
DROP TABLE IF EXISTS `SspRtbChannelToInsertionOrderPreview`;
CREATE TABLE `SspRtbChannelToInsertionOrderPreview` (
  `SspRtbChannelToInsertionOrderPreviewID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `SspPublisherChannelID` char(100) NOT NULL,
  `SspPublisherChannelDescription` char(100) NOT NULL,
  `InsertionOrderPreviewID` int(11) unsigned NOT NULL,
  `Enabled` int(4) unsigned NOT NULL DEFAULT 0,
  `DateCreated` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `DateUpdated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`SspRtbChannelToInsertionOrderPreviewID`),
  UNIQUE KEY `SspRtbChannelToIOPreview_UNIQUE` (`SspRtbChannelToInsertionOrderPreviewID`),
  KEY `FK_InsertionOrder_ID` (`InsertionOrderPreviewID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for SspRtbChannelToInsertionOrderLineItemPreview
-- ----------------------------
DROP TABLE IF EXISTS `SspRtbChannelToInsertionOrderLineItemPreview`;
CREATE TABLE `SspRtbChannelToInsertionOrderLineItemPreview` (
  `SspRtbChannelToInsertionOrderLineItemPreviewID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `SspPublisherChannelID` char(100) NOT NULL,
  `SspPublisherChannelDescription` char(100) NOT NULL,
  `InsertionOrderLineItemPreviewID` int(11) unsigned NOT NULL,
  `Enabled` int(4) unsigned NOT NULL DEFAULT 0,
  `DateCreated` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `DateUpdated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`SspRtbChannelToInsertionOrderLineItemPreviewID`),
  UNIQUE KEY `SspRtbChannelToIOLineItemPreview_UNIQUE` (`SspRtbChannelToInsertionOrderLineItemPreviewID`),
  KEY `FK_InsertionOrderLineItem_ID` (`InsertionOrderLineItemPreviewID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



