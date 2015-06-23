-- Private exchange publishers add website and ad zone visibility flags
-- Do they want the website or ad zone to be available in the platform connection?
-- Do they want the website and ad zone to be available to SSPs being pinged with RTB requests?
ALTER TABLE  `PublisherWebsite` ADD  `VisibilityTypeID` int NOT NULL DEFAULT 0 AFTER  `ApprovalFlag` ;
ALTER TABLE  `PublisherAdZone` CHANGE `PublisherAdZoneTypeID` `VisibilityTypeID` int NOT NULL DEFAULT 1;
ALTER TABLE  `InsertionOrder` ADD  `VisibilityTypeID` int NOT NULL DEFAULT 0 AFTER  `UserID` ;
ALTER TABLE  `InsertionOrderLineItem` CHANGE `InsertionOrderTypeID` `VisibilityTypeID` int NOT NULL DEFAULT 1;

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
INSERT INTO `VisibilityType` VALUES ('1', 'Public Deal', '2014-08-17 12:16:21', '2014-08-17 12:16:21');
INSERT INTO `VisibilityType` VALUES ('2', 'Platform Connection', '2014-08-17 12:16:21', '2014-08-17 12:16:21');
INSERT INTO `VisibilityType` VALUES ('3', 'Private Exchange', '2014-08-17 12:16:21', '2014-08-17 12:16:21');

-- build the new tables which will hold the PMP deals

-- ----------------------------
-- Table structure for PmpDealPublisherInfoToPrivateExchangeDomainAdmin
-- ----------------------------
DROP TABLE IF EXISTS `PmpDealPublisherInfoToPrivateExchangeDomainAdmin`;
CREATE TABLE `PmpDealPublisherInfoToPrivateExchangeDomainAdmin` (
  `PmpDealPublisherInfoToPrivateExchangeDomainAdminID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `PublisherInfoID` int(11) unsigned NOT NULL,
  `PrivateExchangeDomainAdminID` int(11) unsigned NOT NULL,
  `Enabled` int(4) unsigned NOT NULL DEFAULT 0,
  `DateCreated` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `DateUpdated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`PmpDealPublisherInfoToPrivateExchangeDomainAdminID`),
  UNIQUE KEY `PmpDealPublisherInfoToPrivateExchangeDomainAdmin_UNIQUE` (`PmpDealPublisherInfoToPrivateExchangeDomainAdminID`),
  KEY `FK_Publisher_Info_ID` (`PublisherInfoID`),
  KEY `FK_PrivateExchangeDomainAdmin_ID` (`PrivateExchangeDomainAdminID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for PmpDealPublisherWebsiteToPrivateExchangeDomainAdmin
-- ----------------------------
DROP TABLE IF EXISTS `PmpDealPublisherWebsiteToPrivateExchangeDomainAdmin`;
CREATE TABLE `PmpDealPublisherWebsiteToPrivateExchangeDomainAdmin` (
  `PmpDealPublisherWebsiteToPrivateExchangeDomainAdminID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `PublisherWebsiteID` int(11) unsigned NOT NULL,
  `PrivateExchangeDomainAdminID` int(11) unsigned NOT NULL,
  `Enabled` int(4) unsigned NOT NULL DEFAULT 0,
  `DateCreated` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `DateUpdated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`PmpDealPublisherWebsiteToPrivateExchangeDomainAdminID`),
  UNIQUE KEY `PmpDealPublisherWebsiteToPrivateExchangeDomainAdmin_UNIQUE` (`PmpDealPublisherWebsiteToPrivateExchangeDomainAdminID`),
  KEY `FK_Publisher_Website_ID` (`PublisherWebsiteID`),
  KEY `FK_PrivateExchangeDomainAdmin_ID` (`PrivateExchangeDomainAdminID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for PmpDealPublisherWebsiteToInsertionOrder
-- ----------------------------
DROP TABLE IF EXISTS `PmpDealPublisherWebsiteToInsertionOrder`;
CREATE TABLE `PmpDealPublisherWebsiteToInsertionOrder` (
  `PmpDealPublisherWebsiteToInsertionOrderID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `PublisherWebsiteID` int(11) unsigned NOT NULL,
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
-- Table structure for PmpDealPublisherAdZoneToPrivateExchangeDomainAdmin
-- ----------------------------
DROP TABLE IF EXISTS `PmpDealPublisherAdZoneToPrivateExchangeDomainAdmin`;
CREATE TABLE `PmpDealPublisherAdZoneToPrivateExchangeDomainAdmin` (
  `PmpDealPublisherAdZoneToPrivateExchangeDomainAdminID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `PublisherAdZoneID` int(11) unsigned NOT NULL,
  `PrivateExchangeDomainAdminID` int(11) unsigned NOT NULL,
  `Enabled` int(4) unsigned NOT NULL DEFAULT 0,
  `DateCreated` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `DateUpdated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`PmpDealPublisherAdZoneToPrivateExchangeDomainAdminID`),
  UNIQUE KEY `PmpDealPublisherAdZoneToPrivateExchangeDomainAdmin_UNIQUE` (`PmpDealPublisherAdZoneToPrivateExchangeDomainAdminID`),
  KEY `FK_Publisher_AdZone_ID` (`PublisherAdZoneID`),
  KEY `FK_PrivateExchangeDomainAdmin_ID` (`PrivateExchangeDomainAdminID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for PmpDealPublisherAdZoneToInsertionOrder
-- ----------------------------
DROP TABLE IF EXISTS `PmpDealPublisherAdZoneToInsertionOrder`;
CREATE TABLE `PmpDealPublisherAdZoneToInsertionOrder` (
  `PmpDealPublisherAdZoneToInsertionOrderID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `PublisherAdZoneID` int(11) unsigned NOT NULL,
  `InsertionOrderID` int(11) unsigned NOT NULL,
  `Enabled` int(4) unsigned NOT NULL DEFAULT 0,
  `DateCreated` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `DateUpdated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`PmpDealPublisherAdZoneToInsertionOrderID`),
  UNIQUE KEY `PmpDealPublisherAdZoneToInsertionOrder_UNIQUE` (`PmpDealPublisherAdZoneToInsertionOrderID`),
  KEY `FK_Publisher_AdZone_ID` (`PublisherAdZoneID`),
  KEY `FK_InsertionOrder_ID` (`InsertionOrderID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for PmpDealPublisherAdZoneToInsertionOrderLineItem
-- ----------------------------
DROP TABLE IF EXISTS `PmpDealPublisherAdZoneToInsertionOrderLineItem`;
CREATE TABLE `PmpDealPublisherAdZoneToInsertionOrderLineItem` (
  `PmpDealPublisherAdZoneToInsertionOrderLineItemID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `PublisherAdZoneID` int(11) unsigned NOT NULL,
  `InsertionOrderLineItemID` int(11) unsigned NOT NULL,
  `Enabled` int(4) unsigned NOT NULL DEFAULT 0,
  `DateCreated` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `DateUpdated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`PmpDealPublisherAdZoneToInsertionOrderLineItemID`),
  UNIQUE KEY `PmpDealPublisherAdZoneToInsertionOrderLineItem_UNIQUE` (`PmpDealPublisherAdZoneToInsertionOrderLineItemID`),
  KEY `FK_Publisher_AdZone_ID` (`PublisherAdZoneID`),
  KEY `FK_InsertionOrderLineItem_ID` (`InsertionOrderLineItemID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for PmpDealPublisherWebsiteToInsertionOrderPreview
-- ----------------------------
DROP TABLE IF EXISTS `PmpDealPublisherWebsiteToInsertionOrderPreview`;
CREATE TABLE `PmpDealPublisherWebsiteToInsertionOrderPreview` (
  `PmpDealPublisherWebsiteToInsertionOrderPreviewID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `PublisherWebsiteID` int(11) unsigned NOT NULL,
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
-- Table structure for PmpDealPublisherAdZoneToInsertionOrderPreview
-- ----------------------------
DROP TABLE IF EXISTS `PmpDealPublisherAdZoneToInsertionOrderPreview`;
CREATE TABLE `PmpDealPublisherAdZoneToInsertionOrderPreview` (
  `PmpDealPublisherAdZoneToInsertionOrderPreviewID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `PublisherAdZoneID` int(11) unsigned NOT NULL,
  `InsertionOrderPreviewID` int(11) unsigned NOT NULL,
  `Enabled` int(4) unsigned NOT NULL DEFAULT 0,
  `DateCreated` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `DateUpdated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`PmpDealPublisherAdZoneToInsertionOrderPreviewID`),
  UNIQUE KEY `PmpDealPublisherAdZoneToInsertionOrderPreview_UNIQUE` (`PmpDealPublisherAdZoneToInsertionOrderPreviewID`),
  KEY `FK_Publisher_AdZone_ID` (`PublisherAdZoneID`),
  KEY `FK_InsertionOrder_ID` (`InsertionOrderPreviewID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for PmpDealPublisherAdZoneToInsertionOrderLineItemPreview
-- ----------------------------
DROP TABLE IF EXISTS `PmpDealPublisherAdZoneToInsertionOrderLineItemPreview`;
CREATE TABLE `PmpDealPublisherAdZoneToInsertionOrderLineItemPreview` (
  `PmpDealPublisherAdZoneToInsertionOrderLineItemPreviewID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `PublisherAdZoneID` int(11) unsigned NOT NULL,
  `InsertionOrderLineItemPreviewID` int(11) unsigned NOT NULL,
  `Enabled` int(4) unsigned NOT NULL DEFAULT 0,
  `DateCreated` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `DateUpdated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`PmpDealPublisherAdZoneToInsertionOrderLineItemPreviewID`),
  UNIQUE KEY `PmpDealPublisherAdZoneToInsertionOrderLineItemPreview_UNIQUE` (`PmpDealPublisherAdZoneToInsertionOrderLineItemPreviewID`),
  KEY `FK_Publisher_AdZone_ID` (`PublisherAdZoneID`),
  KEY `FK_InsertionOrderLineItem_ID` (`InsertionOrderLineItemPreviewID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;






