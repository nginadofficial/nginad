-- build the new tables which will hold the PMP deals

-- ----------------------------
-- Table structure for PmpDealPublisherWebsiteToPrivateExchangeDomainAdmin
-- ----------------------------
DROP TABLE IF EXISTS `PmpDealPublisherWebsiteToPrivateExchangeDomainAdmin`;
CREATE TABLE `PmpDealPublisherWebsiteToPrivateExchangeDomainAdmin` (
  `PmpDealPublisherWebsiteToPrivateExchangeDomainAdminID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `PublisherWebsiteID` int(11) unsigned NOT NULL,
  `PrivateExchangeDomainAdminID` int(11) unsigned NOT NULL,
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
  `DateCreated` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `DateUpdated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`PmpDealPublisherAdZoneToInsertionOrderLineItemPreviewID`),
  UNIQUE KEY `PmpDealPublisherAdZoneToInsertionOrderLineItemPreview_UNIQUE` (`PmpDealPublisherAdZoneToInsertionOrderLineItemPreviewID`),
  KEY `FK_Publisher_AdZone_ID` (`PublisherAdZoneID`),
  KEY `FK_InsertionOrderLineItem_ID` (`InsertionOrderLineItemPreviewID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;






