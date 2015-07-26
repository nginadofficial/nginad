-- Private exchange publishers
-- 0 means they signed up via the public portal and do not belong to a demand customer
ALTER TABLE  `auth_Users` ADD  `parent_id` int NOT NULL DEFAULT 0 AFTER  `user_id` ;

-- Update the roles to allow for domain admins
UPDATE rbac_role SET role_name = 'super_admin' WHERE role_id = 1;
UPDATE rbac_role SET role_name = 'domain_admin' WHERE role_id = 2;

-- demand users are now user_role id 2 - domain_admins
UPDATE auth_Users SET user_role = 2 WHERE DemandCustomerInfoID IS NOT NULL;

-- rename ad campaigns to IO and rename banner to line item
-- to satisfy all the 100 year old ad-ops out there urning for the years gone of IOs on fax machines

RENAME TABLE AdCampaign TO InsertionOrder;
RENAME TABLE AdCampaignBanner TO InsertionOrderLineItem;
RENAME TABLE AdCampaignBannerDomainExclusion TO InsertionOrderLineItemDomainExclusion;
RENAME TABLE AdCampaignBannerDomainExclusionPreview TO InsertionOrderLineItemDomainExclusionPreview;
RENAME TABLE AdCampaignBannerDomainExclusiveInclusion TO InsertionOrderLineItemDomainExclusiveInclusion;
RENAME TABLE AdCampaignBannerDomainExclusiveInclusionPreview TO InsertionOrderLineItemDomainExclusiveInclusionPreview;
RENAME TABLE AdCampaignBannerPreview TO InsertionOrderLineItemPreview;
RENAME TABLE AdCampaignBannerRestrictions TO InsertionOrderLineItemRestrictions;
RENAME TABLE AdCampaignBannerRestrictionsPreview TO InsertionOrderLineItemRestrictionsPreview;
RENAME TABLE AdCampaignPreview TO InsertionOrderPreview;
RENAME TABLE AdCampaignType TO InsertionOrderType;
RENAME TABLE AdCampaignVideoRestrictions TO InsertionOrderLineItemVideoRestrictions;
RENAME TABLE AdCampaignVideoRestrictionsPreview TO InsertionOrderLineItemVideoRestrictionsPreview;
RENAME TABLE AdCampainMarkup TO InsertionOrderMarkup;

-- Table structure for InsertionOrder
ALTER TABLE InsertionOrder CHANGE AdCampaignID InsertionOrderID int(11) NOT NULL AUTO_INCREMENT;
  
-- Table structure for InsertionOrderLineItem
ALTER TABLE InsertionOrderLineItem CHANGE AdCampaignBannerID InsertionOrderLineItemID int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE InsertionOrderLineItem CHANGE AdCampaignID InsertionOrderID int(11) NOT NULL;
ALTER TABLE InsertionOrderLineItem CHANGE AdCampaignTypeID InsertionOrderTypeID int(11) NOT NULL;

-- Table structure for InsertionOrderLineItemDomainExclusion

ALTER TABLE InsertionOrderLineItemDomainExclusion CHANGE AdCampaignBannerDomainExclusionID InsertionOrderLineItemDomainExclusionID int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE InsertionOrderLineItemDomainExclusion CHANGE AdCampaignBannerID InsertionOrderLineItemID int(11) NOT NULL;

-- Table structure for InsertionOrderLineItemDomainExclusionPreview

ALTER TABLE InsertionOrderLineItemDomainExclusionPreview CHANGE AdCampaignBannerDomainExclusionPreviewID InsertionOrderLineItemDomainExclusionPreviewID int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE InsertionOrderLineItemDomainExclusionPreview CHANGE AdCampaignBannerPreviewID InsertionOrderLineItemPreviewID int(11) NOT NULL;

-- Table structure for InsertionOrderLineItemDomainExclusiveInclusion

ALTER TABLE InsertionOrderLineItemDomainExclusiveInclusion CHANGE AdCampaignBannerDomainExclusiveInclusionID InsertionOrderLineItemDomainExclusiveInclusionID int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE InsertionOrderLineItemDomainExclusiveInclusion CHANGE AdCampaignBannerID InsertionOrderLineItemID int(11) NOT NULL;

-- Table structure for InsertionOrderLineItemDomainExclusiveInclusionPreview

ALTER TABLE InsertionOrderLineItemDomainExclusiveInclusionPreview CHANGE AdCampaignBannerDomainExclusiveInclusionPreviewID InsertionOrderLineItemDomainExclusiveInclusionPreviewID int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE InsertionOrderLineItemDomainExclusiveInclusionPreview CHANGE AdCampaignBannerPreviewID InsertionOrderLineItemPreviewID int(11) NOT NULL;

-- Table structure for InsertionOrderLineItemPreview
ALTER TABLE InsertionOrderLineItemPreview CHANGE AdCampaignBannerPreviewID InsertionOrderLineItemPreviewID int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE InsertionOrderLineItemPreview CHANGE AdCampaignPreviewID InsertionOrderPreviewID int(11) NOT NULL;
ALTER TABLE InsertionOrderLineItemPreview CHANGE AdCampaignBannerID InsertionOrderLineItemID int(11) DEFAULT NULL;
ALTER TABLE InsertionOrderLineItemPreview CHANGE AdCampaignTypeID InsertionOrderTypeID int(11) NOT NULL;

-- Table structure for InsertionOrderLineItemRestrictions
ALTER TABLE InsertionOrderLineItemRestrictions CHANGE AdCampaignBannerRestrictionsID InsertionOrderLineItemRestrictionsID int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE InsertionOrderLineItemRestrictions CHANGE AdCampaignBannerID InsertionOrderLineItemID int(11) NOT NULL;

-- Table structure for InsertionOrderLineItemVideoRestrictions
ALTER TABLE InsertionOrderLineItemVideoRestrictions CHANGE AdCampaignVideoRestrictionsID InsertionOrderLineItemVideoRestrictionsID int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE InsertionOrderLineItemVideoRestrictions CHANGE AdCampaignBannerID InsertionOrderLineItemID int(11) NOT NULL;

-- Table structure for InsertionOrderLineItemRestrictionsPreview
ALTER TABLE InsertionOrderLineItemRestrictionsPreview CHANGE AdCampaignBannerRestrictionsPreviewID InsertionOrderLineItemRestrictionsPreviewID int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE InsertionOrderLineItemRestrictionsPreview CHANGE AdCampaignBannerPreviewID InsertionOrderLineItemPreviewID int(11) NOT NULL;

-- Table structure for InsertionOrderLineItemVideoRestrictionsPreview
ALTER TABLE InsertionOrderLineItemVideoRestrictionsPreview CHANGE AdCampaignVideoRestrictionsPreviewID InsertionOrderLineItemVideoRestrictionsPreviewID int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE InsertionOrderLineItemVideoRestrictionsPreview CHANGE AdCampaignBannerPreviewID InsertionOrderLineItemPreviewID int(11) NOT NULL;

-- Table structure for InsertionOrderPreview
ALTER TABLE InsertionOrderPreview CHANGE AdCampaignPreviewID InsertionOrderPreviewID int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE InsertionOrderPreview CHANGE AdCampaignID InsertionOrderID int(11) DEFAULT NULL;

-- Table structure for InsertionOrderMarkup
ALTER TABLE InsertionOrderMarkup CHANGE AdCampaignID InsertionOrderID int(11) unsigned NOT NULL;

-- Table structure for BuySideDailyImpressionsByTLD
ALTER TABLE BuySideDailyImpressionsByTLD CHANGE AdCampaignBannerID InsertionOrderLineItemID int(11) unsigned NOT NULL;
ALTER TABLE BuySideDailyImpressionsByTLD
DROP KEY `RTBBannerID_IDX`, 
ADD UNIQUE KEY `RTBBannerID_IDX` (`InsertionOrderLineItemID`,`MDY`,`PublisherTLD`);

-- Table structure for BuySideHourlyBidsCounter
ALTER TABLE BuySideHourlyBidsCounter CHANGE AdCampaignBannerID InsertionOrderLineItemID int(11) unsigned NOT NULL;
ALTER TABLE BuySideHourlyBidsCounter
DROP KEY `BuySideHourlyBid_IDX`, 
ADD UNIQUE KEY `BuySideHourlyBid_IDX` (`BuySidePartnerID`,`InsertionOrderLineItemID`,`MDYH`) USING BTREE;

-- Table structure for BuySideHourlyImpressionsByTLD
ALTER TABLE BuySideHourlyImpressionsByTLD CHANGE AdCampaignBannerID InsertionOrderLineItemID int(11) unsigned NOT NULL;
ALTER TABLE BuySideHourlyImpressionsByTLD
DROP KEY `AnyBannerID_IDX`, 
ADD UNIQUE KEY `AnyBannerID_IDX` (`InsertionOrderLineItemID`,`MDYH`,`PublisherTLD`);

-- Table structure for BuySideHourlyImpressionsCounterCurrentSpend
ALTER TABLE BuySideHourlyImpressionsCounterCurrentSpend CHANGE AdCampaignBannerID InsertionOrderLineItemID int(11) unsigned NOT NULL;
ALTER TABLE BuySideHourlyImpressionsCounterCurrentSpend
DROP KEY `BuySideHourlyIC_IDX`, 
ADD UNIQUE KEY `BuySideHourlyIC_IDX` (`BuySidePartnerID`,`InsertionOrderLineItemID`,`MDYH`) USING BTREE;

-- Table structure for ContractPublisherZoneHourlyImpressions
ALTER TABLE ContractPublisherZoneHourlyImpressions CHANGE AdCampaignBannerID InsertionOrderLineItemID int(11) unsigned NOT NULL;
ALTER TABLE ContractPublisherZoneHourlyImpressions
DROP KEY `ContractPublisherZoneHourlyImpression_IDX`, 
ADD UNIQUE KEY `ContractPublisherZoneHourlyImpression_IDX` (`ContractPublisherZoneHourlyImpressionsID`,`InsertionOrderLineItemID`,`PublisherAdZoneID`,`MDYH`) USING BTREE;
ALTER TABLE ContractPublisherZoneHourlyImpressions
DROP KEY `ContractPublisherZoneHourlyImpressions_IDX`, 
ADD UNIQUE KEY `ContractPublisherZoneHourlyImpressions_IDX` (`InsertionOrderLineItemID`,`PublisherAdZoneID`,`MDYH`);

-- Table structure for LinkedBannerToAdZone
ALTER TABLE LinkedBannerToAdZone CHANGE AdCampaignBannerID InsertionOrderLineItemID int(11) unsigned NOT NULL;
ALTER TABLE LinkedBannerToAdZone
DROP KEY `FK_Publisher_Zone_ID`, 
ADD KEY `FK_Publisher_Zone_ID` (`InsertionOrderLineItemID`);
ALTER TABLE LinkedBannerToAdZone
DROP KEY `FK_AdCampaign_Banner_ID`, 
ADD KEY `FK_InsertionOrder_Banner_ID` (`PublisherAdZoneID`);
  
-- Table structure for LinkedBannerToAdZonePreview
ALTER TABLE LinkedBannerToAdZonePreview CHANGE AdCampaignBannerPreviewID InsertionOrderLineItemPreviewID int(11) unsigned NOT NULL;

-- View structure for BidTotalsRollup
DROP VIEW IF EXISTS `BidTotalsRollup`;
CREATE VIEW `BidTotalsRollup` AS select `BuySideHourlyBidsCounter`.`InsertionOrderLineItemID` AS `InsertionOrderLineItemID`,sum(`BuySideHourlyBidsCounter`.`BidsCounter`) AS `TotalBids` from `BuySideHourlyBidsCounter` group by `BuySideHourlyBidsCounter`.`InsertionOrderLineItemID` ;

-- View structure for buySideHourlyBidsAvarage
DROP VIEW IF EXISTS `buySideHourlyBidsAvarage`;
CREATE VIEW `buySideHourlyBidsAvarage` AS select avg(`BuySideHourlyBidsCounter`.`BidsCounter`) AS `avg_bids`,sum(`BuySideHourlyBidsCounter`.`BidsCounter`) AS `total_bids`,`InsertionOrderLineItem`.`Name` AS `banner_name`,`InsertionOrder`.`Name` AS `Name`,`auth_Users`.`user_login` AS `user_login` from (((`BuySideHourlyBidsCounter` join `InsertionOrderLineItem` on((`BuySideHourlyBidsCounter`.`InsertionOrderLineItemID` = `InsertionOrderLineItem`.`InsertionOrderLineItemID`))) join `InsertionOrder` on((`InsertionOrderLineItem`.`InsertionOrderID` = `InsertionOrder`.`InsertionOrderID`))) join `auth_Users` on((`auth_Users`.`user_id` = `InsertionOrderLineItem`.`UserID`))) group by `BuySideHourlyBidsCounter`.`InsertionOrderLineItemID` order by `BuySideHourlyBidsCounter`.`InsertionOrderLineItemID` ;

-- View structure for buySideHourlyBidsAvarageAdmin
DROP VIEW IF EXISTS `buySideHourlyBidsAvarageAdmin`;
CREATE VIEW `buySideHourlyBidsAvarageAdmin` AS select `BuySideHourlyBidsCounter`.`BuySidePartnerID` AS `BuySidePartnerID`,avg(`BuySideHourlyBidsCounter`.`BidsCounter`) AS `avg_bids`,sum(`BuySideHourlyBidsCounter`.`BidsCounter`) AS `total_bids`,`InsertionOrderLineItem`.`Name` AS `banner_name`,`InsertionOrder`.`Name` AS `Name`,`auth_Users`.`user_login` AS `user_login` from (((`BuySideHourlyBidsCounter` join `InsertionOrderLineItem` on((`BuySideHourlyBidsCounter`.`InsertionOrderLineItemID` = `InsertionOrderLineItem`.`InsertionOrderLineItemID`))) join `InsertionOrder` on((`InsertionOrderLineItem`.`InsertionOrderID` = `InsertionOrder`.`InsertionOrderID`))) join `auth_Users` on((`auth_Users`.`user_id` = `InsertionOrderLineItem`.`UserID`))) group by `BuySideHourlyBidsCounter`.`InsertionOrderLineItemID`,`BuySideHourlyBidsCounter`.`BuySidePartnerID` order by `BuySideHourlyBidsCounter`.`InsertionOrderLineItemID` ;

-- View structure for buySideHourlyBidsPerTime
DROP VIEW IF EXISTS `buySideHourlyBidsPerTime`;
CREATE VIEW `buySideHourlyBidsPerTime` AS select `BuySideHourlyBidsCounter`.`BuySidePartnerID` AS `BuySidePartnerID`,`BuySideHourlyBidsCounter`.`MDYH` AS `MDYH`,`BuySideHourlyBidsCounter`.`BidsCounter` AS `BidsCounter`,`BuySideHourlyBidsCounter`.`DateCreated` AS `DateCreated`,`BuySideHourlyBidsCounter`.`DateUpdated` AS `DateUpdated`,`InsertionOrder`.`Name` AS `Name` from ((`BuySideHourlyBidsCounter` join `InsertionOrderLineItem` on((`BuySideHourlyBidsCounter`.`InsertionOrderLineItemID` = `InsertionOrderLineItem`.`InsertionOrderLineItemID`))) join `InsertionOrder` on((`InsertionOrderLineItem`.`InsertionOrderID` = `InsertionOrder`.`InsertionOrderID`))) order by `BuySideHourlyBidsCounter`.`InsertionOrderLineItemID` ;

-- View structure for ImpressionAndSpendTotalsRollup
DROP VIEW IF EXISTS `ImpressionAndSpendTotalsRollup`;
CREATE VIEW `ImpressionAndSpendTotalsRollup` AS select `BuySideHourlyImpressionsCounterCurrentSpend`.`InsertionOrderLineItemID` AS `InsertionOrderLineItemID`,sum(`BuySideHourlyImpressionsCounterCurrentSpend`.`CurrentSpendGross`) AS `TotalSpendGross`,sum(`BuySideHourlyImpressionsCounterCurrentSpend`.`CurrentSpendNet`) AS `TotalSpendNet`,sum(`BuySideHourlyImpressionsCounterCurrentSpend`.`ImpressionsCounter`) AS `TotalImpressions` from `BuySideHourlyImpressionsCounterCurrentSpend` group by `BuySideHourlyImpressionsCounterCurrentSpend`.`InsertionOrderLineItemID` ;

-- View structure for impressionsCurrentSpendPerTime
DROP VIEW IF EXISTS `impressionsCurrentSpendPerTime`;
CREATE VIEW `impressionsCurrentSpendPerTime` AS select `BuySideHourlyImpressionsCounterCurrentSpend`.`BuySidePartnerID` AS `BuySidePartnerID`,`BuySideHourlyImpressionsCounterCurrentSpend`.`MDYH` AS `MDYH`,`BuySideHourlyImpressionsCounterCurrentSpend`.`ImpressionsCounter` AS `ImpressionsCounter`,`BuySideHourlyImpressionsCounterCurrentSpend`.`CurrentSpendGross` AS `CurrentSpendGross`,`BuySideHourlyImpressionsCounterCurrentSpend`.`CurrentSpendNet` AS `CurrentSpendNet`,round((`BuySideHourlyImpressionsCounterCurrentSpend`.`CurrentSpendNet` / `BuySideHourlyImpressionsCounterCurrentSpend`.`ImpressionsCounter`),7) AS `AverageBidCurrentSpendNet`,round((`BuySideHourlyImpressionsCounterCurrentSpend`.`CurrentSpendGross` / `BuySideHourlyImpressionsCounterCurrentSpend`.`ImpressionsCounter`),7) AS `AverageBidCurrentSpendGross`,`BuySideHourlyImpressionsCounterCurrentSpend`.`DateCreated` AS `DateCreated`,`BuySideHourlyImpressionsCounterCurrentSpend`.`DateUpdated` AS `DateUpdated`,`InsertionOrder`.`Name` AS `Name` from ((`BuySideHourlyImpressionsCounterCurrentSpend` join `InsertionOrderLineItem` on((`BuySideHourlyImpressionsCounterCurrentSpend`.`InsertionOrderLineItemID` = `InsertionOrderLineItem`.`InsertionOrderLineItemID`))) join `InsertionOrder` on((`InsertionOrderLineItem`.`InsertionOrderID` = `InsertionOrder`.`InsertionOrderID`))) order by `BuySideHourlyImpressionsCounterCurrentSpend`.`BuySidePartnerID` ;

-- View structure for userImpressionsSpend
DROP VIEW IF EXISTS `userImpressionsSpend`;
CREATE VIEW `userImpressionsSpend` AS select round(sum(`BuySideHourlyImpressionsCounterCurrentSpend`.`CurrentSpendGross`),7) AS `TotalSpendGross`,round(sum(`BuySideHourlyImpressionsCounterCurrentSpend`.`CurrentSpendNet`),7) AS `TotalSpendNet`,`InsertionOrder`.`Name` AS `Name`,`auth_Users`.`user_login` AS `user_login` from (((`BuySideHourlyImpressionsCounterCurrentSpend` join `InsertionOrderLineItem` on((`BuySideHourlyImpressionsCounterCurrentSpend`.`InsertionOrderLineItemID` = `InsertionOrderLineItem`.`InsertionOrderLineItemID`))) join `InsertionOrder` on((`InsertionOrderLineItem`.`InsertionOrderID` = `InsertionOrder`.`InsertionOrderID`))) join `auth_Users` on((`auth_Users`.`user_id` = `InsertionOrderLineItem`.`UserID`))) group by `BuySideHourlyImpressionsCounterCurrentSpend`.`InsertionOrderLineItemID` order by `auth_Users`.`user_login` ;

-- View structure for userImpressionsSpendAdmin
DROP VIEW IF EXISTS `userImpressionsSpendAdmin`;
CREATE VIEW `userImpressionsSpendAdmin` AS select `BuySideHourlyImpressionsCounterCurrentSpend`.`BuySidePartnerID` AS `BuySidePartnerID`,round(sum(`BuySideHourlyImpressionsCounterCurrentSpend`.`CurrentSpendGross`),7) AS `TotalSpendGross`,round(sum(`BuySideHourlyImpressionsCounterCurrentSpend`.`CurrentSpendNet`),7) AS `TotalSpendNet`,`InsertionOrder`.`Name` AS `Name`,`auth_Users`.`user_login` AS `user_login` from (((`BuySideHourlyImpressionsCounterCurrentSpend` join `InsertionOrderLineItem` on((`BuySideHourlyImpressionsCounterCurrentSpend`.`InsertionOrderLineItemID` = `InsertionOrderLineItem`.`InsertionOrderLineItemID`))) join `InsertionOrder` on((`InsertionOrderLineItem`.`InsertionOrderID` = `InsertionOrder`.`InsertionOrderID`))) join `auth_Users` on((`auth_Users`.`user_id` = `InsertionOrderLineItem`.`UserID`))) group by `BuySideHourlyImpressionsCounterCurrentSpend`.`BuySidePartnerID`,`BuySideHourlyImpressionsCounterCurrentSpend`.`InsertionOrderLineItemID` order by `auth_Users`.`user_login` ;

-- View structure for DemandImpressionsAndSpendHourlyPre
DROP VIEW IF EXISTS `DemandImpressionsAndSpendHourlyPre`;
CREATE VIEW `DemandImpressionsAndSpendHourlyPre` AS select `bshiccs`.`MDYH`, `bshiccs`.`InsertionOrderLineItemID`, `dci`.`Name` as DemandCustomerName, `dci`.`DemandCustomerInfoID` as DemandCustomerInfoID, `acb`.`Name` as BannerName, sum(`bshiccs`.`ImpressionsCounter`) as `Impressions`, round(sum(`bshiccs`.`CurrentSpendNet`),7) as `Cost`, round(sum(`bshiccs`.`CurrentSpendGross`),7) as `GrossCost`, round(((sum(`bshiccs`.`CurrentSpendNet`) / sum(`bshiccs`.`ImpressionsCounter`)) * 1000),7) AS `CPM`, round(((sum(`bshiccs`.`CurrentSpendGross`) / sum(`bshiccs`.`ImpressionsCounter`)) * 1000),7) AS `GrossCPM`, `bshiccs`.`DateCreated` from `BuySideHourlyImpressionsCounterCurrentSpend` bshiccs inner join `InsertionOrderLineItem` acb on bshiccs.`InsertionOrderLineItemID` = acb.`InsertionOrderLineItemID` inner join `auth_Users` au on au.`user_id` = acb.`UserID` inner join `DemandCustomerInfo` dci on au.`DemandCustomerInfoID` = dci.`DemandCustomerInfoID` group by `bshiccs`.`InsertionOrderLineItemID`, `bshiccs`.`MDYH` ;

-- View structure for DemandImpressionsAndSpendHourly
DROP VIEW IF EXISTS `DemandImpressionsAndSpendHourly`;
CREATE VIEW `DemandImpressionsAndSpendHourly` AS select diashp.MDYH, diashp.InsertionOrderLineItemID, diashp.DemandCustomerName, diashp.DemandCustomerInfoID, diashp.BannerName, group_concat(distinct `bshibt`.`PublisherTLD` separator ', ') as PublisherTLDs, diashp.Impressions, diashp.Cost, diashp.GrossCost, diashp.CPM, diashp.GrossCPM, diashp.DateCreated from DemandImpressionsAndSpendHourlyPre diashp left outer join `BuySideHourlyImpressionsByTLD` bshibt on diashp.`InsertionOrderLineItemID` = `bshibt`.`InsertionOrderLineItemID` and diashp.`MDYH` = `bshibt`.`MDYH` group by `diashp`.`InsertionOrderLineItemID`, `diashp`.`MDYH` ;


-- Private exchange publishers add website and ad zone visibility flags
-- Do they want the website or ad zone to be available in the platform connection?
-- Do they want the website and ad zone to be available to SSPs being pinged with RTB requests?
ALTER TABLE  `PublisherWebsite` ADD `VisibilityTypeID` int(4) NOT NULL DEFAULT 1 AFTER  `DomainOwnerID` ;

DROP TABLE IF EXISTS `ContractPublisherZoneHourlyImpressions`;

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
  UNIQUE KEY `PmpDealPublisherWebsiteToInsertionOrder_UNQ_IDX` (`PublisherWebsiteID`,`InsertionOrderID`),
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
  UNIQUE KEY `PmpDealPublisherWebsiteToInsertionOrderLineItem_UNQ_IDX` (`PublisherWebsiteID`,`InsertionOrderLineItemID`),
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
  UNIQUE KEY `PmpDealPublisherWebsiteToInsertionOrderPreview_UNQ_IDX` (`PublisherWebsiteID`,`InsertionOrderPreviewID`),
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
  UNIQUE KEY `PmpDealPublisherWebsiteToInsertionOrderLineItemPreview_UNQ_IDX` (`PublisherWebsiteID`,`InsertionOrderLineItemPreviewID`),
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
  `SspExchange` char(100) NOT NULL,
  `InsertionOrderID` int(11) unsigned NOT NULL,
  `Enabled` int(4) unsigned NOT NULL DEFAULT 0,
  `DateCreated` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `DateUpdated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`SspRtbChannelToInsertionOrderID`),
  UNIQUE KEY `SspRtbChannelToIO_UNIQUE` (`SspRtbChannelToInsertionOrderID`),
  UNIQUE KEY `SspRtbChannelToIO_UNQ_IDX` (`SspPublisherChannelID`,`SspExchange`,`InsertionOrderID`),
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
  `SspExchange` char(100) NOT NULL,
  `InsertionOrderLineItemID` int(11) unsigned NOT NULL,
  `Enabled` int(4) unsigned NOT NULL DEFAULT 0,
  `DateCreated` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `DateUpdated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`SspRtbChannelToInsertionOrderLineItemID`),
  UNIQUE KEY `SspRtbChannelToIOLineItem_UNIQUE` (`SspRtbChannelToInsertionOrderLineItemID`),
  UNIQUE KEY `SspRtbChannelToIOLineItem_UNQ_IDX` (`SspPublisherChannelID`,`SspExchange`,`InsertionOrderLineItemID`),
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
  `SspExchange` char(100) NOT NULL,
  `InsertionOrderPreviewID` int(11) unsigned NOT NULL,
  `Enabled` int(4) unsigned NOT NULL DEFAULT 0,
  `DateCreated` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `DateUpdated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`SspRtbChannelToInsertionOrderPreviewID`),
  UNIQUE KEY `SspRtbChannelToIOPreview_UNIQUE` (`SspRtbChannelToInsertionOrderPreviewID`),
  UNIQUE KEY `SspRtbChannelToIOPreview_UNQ_IDX` (`SspPublisherChannelID`,`SspExchange`,`InsertionOrderPreviewID`),
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
  `SspExchange` char(100) NOT NULL,
  `InsertionOrderLineItemPreviewID` int(11) unsigned NOT NULL,
  `Enabled` int(4) unsigned NOT NULL DEFAULT 0,
  `DateCreated` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `DateUpdated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`SspRtbChannelToInsertionOrderLineItemPreviewID`),
  UNIQUE KEY `SspRtbChannelToIOLineItemPreview_UNIQUE` (`SspRtbChannelToInsertionOrderLineItemPreviewID`),
  UNIQUE KEY `SspRtbChannelToIOLineItemPreview_UNQ_IDX` (`SspPublisherChannelID`,`SspExchange`,`InsertionOrderLineItemPreviewID`),
  KEY `FK_InsertionOrderLineItem_ID` (`InsertionOrderLineItemPreviewID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for SspRtbChannelDailyStats
--
-- Provides SiteScout like stats for each 
-- SSP RTB channel by RTB Site ID
--
-- RTB Requests Total by day
-- RTB Bid responses total by day
-- ----------------------------
DROP TABLE IF EXISTS `SspRtbChannelDailyStats`;
CREATE TABLE `SspRtbChannelDailyStats` (
  `SspRtbChannelDailyStatsID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `BuySidePartnerName` char(100) NOT NULL,
  `SspRtbChannelSiteID` char(100) NOT NULL,
  `SspRtbChannelSiteName` char(100) NOT NULL,
  `SspRtbChannelSiteDomain` char(100) NOT NULL,
  `SspRtbChannelSiteIABCategory` char(8) NOT NULL,
  `SspRtbChannelPublisherName` char(100) NOT NULL,
  `MDY` char(15) NOT NULL,
  `MDYH` char(15) NOT NULL,
  `ImpressionsOfferedCounter` int(11) unsigned NOT NULL DEFAULT 0,
  `AuctionBidsCounter` int(11) unsigned NOT NULL DEFAULT 0,
  `BidTotalAmount` float NOT NULL DEFAULT 0,
  `BidFloor` float NOT NULL DEFAULT 0,
  `DateCreated` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `DateUpdated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`SspRtbChannelDailyStatsID`),
  UNIQUE KEY `SspRtbChannelDailyStats_UNQ_IDX` (`BuySidePartnerName`,`SspRtbChannelSiteID`,`MDYH`),
  UNIQUE KEY `SspRtbChannelDailyStats_UNIQUE` (`SspRtbChannelDailyStatsID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `PrivateExchangeRtbChannelDailyStats`;
CREATE TABLE `PrivateExchangeRtbChannelDailyStats` (
  `PrivateExchangeRtbChannelDailyStatsID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `PublisherWebsiteID` int(11) unsigned NOT NULL,
  `RtbChannelSiteName` char(100) NOT NULL,
  `MDY` char(15) NOT NULL,
  `MDYH` char(15) NOT NULL,
  `ImpressionsOfferedCounter` int(11) unsigned NOT NULL DEFAULT 0,
  `AuctionBidsCounter` int(11) unsigned NOT NULL DEFAULT 0,
  `BidTotalAmount` float NOT NULL DEFAULT 0,
  `BidFloor` float NOT NULL DEFAULT 0,
  `DateCreated` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `DateUpdated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`PrivateExchangeRtbChannelDailyStatsID`),
  UNIQUE KEY `PrivateExchangeRtbChannelDailyStats_UNIQUE` (`PrivateExchangeRtbChannelDailyStatsID`),
  UNIQUE KEY `PrivateExchangeRtbChannelDailyStats_UNQ_IDX` (`PublisherWebsiteID`,`MDYH`),
  KEY `FK_Publisher_Website_ID` (`PublisherWebsiteID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- View structure for PrivateExchangeRtbChannelDailyStatsRollUp
-- ----------------------------
DROP VIEW IF EXISTS `PrivateExchangeRtbChannelDailyStatsRollUp`;
CREATE VIEW `PrivateExchangeRtbChannelDailyStatsRollUp` AS select `percds`.`PublisherWebsiteID` AS `PublisherWebsiteID`, `au`.`user_id` AS `UserID`,`percds`.`MDY` AS `MDY`, `pw`.`VisibilityTypeID` AS `VisibilityTypeID`, `pw`.`WebDomain` AS `WebDomain`, `pw`.`IABCategory` AS `IABCategory`, `pi`.`Name` AS `PublisherName`,`au`.`parent_id` AS `ParentID`, ifnull (`dci`.`Company`, 'In-House') AS `BuySidePartnerName`, `pw`.`Description` AS `RtbChannelSiteName`, sum(`percds`.`ImpressionsOfferedCounter`) AS `ImpressionsOfferedCounter`, sum(`percds`.`AuctionBidsCounter`) AS `AuctionBidsCounter`, ifnull (round((ceiling(((sum(`percds`.`BidTotalAmount`) / sum(`percds`.`AuctionBidsCounter`)) * 100000)) / 100),2), 0) AS `BidTotalAverage`, round(max(`percds`.`BidFloor`),2) AS `BidFloor` from (((((`PrivateExchangeRtbChannelDailyStats` `percds` join `PublisherWebsite` `pw` on((`percds`.`PublisherWebsiteID` = `pw`.`PublisherWebsiteID`))) join `PublisherInfo` `pi` on((`pi`.`PublisherInfoID` = `pw`.`DomainOwnerID`))) join `auth_Users` `au` on((`au`.`PublisherInfoID` = `pi`.`PublisherInfoID`))) left outer join `auth_Users` `au2` on((`au2`.`user_id` = `au`.`parent_id`))) left outer join `DemandCustomerInfo` `dci` on((`au2`.`DemandCustomerInfoID` = `dci`.`DemandCustomerInfoID`))) group by `percds`.`MDY`,`percds`.`PublisherWebsiteID` order by sum(`percds`.`ImpressionsOfferedCounter`) ;

-- ----------------------------
-- View structure for SspRtbChannelDailyStatsRollUp
-- ----------------------------
DROP VIEW IF EXISTS `SspRtbChannelDailyStatsRollUp`;
CREATE VIEW `SspRtbChannelDailyStatsRollUp` AS select `srcds`.`SspRtbChannelSiteID` AS `SspRtbChannelSiteID`, `srcds`.`MDY` AS `MDY`, `srcds`.`SspRtbChannelSiteDomain` AS `WebDomain`, `srcds`.`SspRtbChannelSiteIABCategory` AS `IABCategory`, `srcds`.`SspRtbChannelPublisherName` AS `PublisherName`, `srcds`.`SspRtbChannelSiteName` AS `RtbChannelSiteName`, `srcds`.`BuySidePartnerName` AS `BuySidePartnerName`, sum(`srcds`.`ImpressionsOfferedCounter`) AS `ImpressionsOfferedCounter`, sum(`srcds`.`AuctionBidsCounter`) AS `AuctionBidsCounter`, round(ceil((sum(`srcds`.`BidTotalAmount`) / sum(`srcds`.`AuctionBidsCounter`)) * 100000) / 100, 2) AS `BidTotalAverage`, round(max(`srcds`.`BidFloor`), 2) AS `BidFloor` from `SspRtbChannelDailyStats` `srcds` group by `srcds`.`MDY`, `srcds`.`SspRtbChannelSiteID` order by `ImpressionsOfferedCounter` ;

-- ----------------------------
-- View structure for PrivateExchangeRtbChannelDailyStatsRollUpPxFilter
-- ----------------------------
DROP VIEW IF EXISTS `PrivateExchangeRtbChannelDailyStatsRollUpPxFilter`;
CREATE VIEW `PrivateExchangeRtbChannelDailyStatsRollUpPxFilter` AS select `percdsru`.`PublisherWebsiteID` AS `PublisherWebsiteID`, `percdsru`.`UserID` AS `UserID`, max(`percdsru`.`MDY`) AS `MDY`, `percdsru`.`VisibilityTypeID` AS `VisibilityTypeID`, `percdsru`.`WebDomain` AS `WebDomain`, `percdsru`.`IABCategory` AS `IABCategory`, `percdsru`.`PublisherName` AS `PublisherName`, `percdsru`.`ParentID` AS `ParentID`, `percdsru`.`BuySidePartnerName` AS `BuySidePartnerName`, `percdsru`.`RtbChannelSiteName` AS `RtbChannelSiteName`, `percdsru`.`ImpressionsOfferedCounter` AS `ImpressionsOfferedCounter`, `percdsru`.`AuctionBidsCounter` AS `AuctionBidsCounter`, `percdsru`.`BidTotalAverage` AS `BidTotalAverage`, `percdsru`.`BidFloor` AS `BidFloor` from `PrivateExchangeRtbChannelDailyStatsRollUp` `percdsru` group by `percdsru`.`PublisherWebsiteID` order by `ImpressionsOfferedCounter` ;


DROP TABLE IF EXISTS `LinkedBannerToAdZone`;
DROP TABLE IF EXISTS `LinkedBannerToAdZonePreview`;
DROP TABLE IF EXISTS `PublisherAdZoneType`;
DROP TABLE IF EXISTS `ContractPublisherZoneHourlyImpressions`;
-- ALTER TABLE PublisherAdZone DROP COLUMN `VisibilityTypeID`;
ALTER TABLE PublisherAdZone DROP COLUMN `PublisherAdZoneTypeID`;

-- ----------------------------
-- Table structure for PrivateExchangePublisherMarkup
-- ----------------------------
DROP TABLE IF EXISTS `PrivateExchangePublisherMarkup`;
CREATE TABLE `PrivateExchangePublisherMarkup` (
  `PublisherInfoID` int(11) NOT NULL,
  `MarkupRate` float NOT NULL,
  PRIMARY KEY (`PublisherInfoID`),
  UNIQUE KEY `PublisherInfoID` (`PublisherInfoID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for PrivateExchangePublisherWebsiteMarkup
-- ----------------------------
DROP TABLE IF EXISTS `PrivateExchangePublisherWebsiteMarkup`;
CREATE TABLE `PrivateExchangePublisherWebsiteMarkup` (
  `PublisherWebsiteID` int(11) NOT NULL,
  `MarkupRate` float NOT NULL,
  PRIMARY KEY (`PublisherWebsiteID`),
  UNIQUE KEY `PublisherWebsiteID` (`PublisherWebsiteID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE  `SellSidePartnerHourlyBids` ADD `SpendTotalPrivateExchangeGross` float NOT NULL AFTER `SpendTotalGross` ;
ALTER TABLE  `PublisherHourlyBids` ADD `SpendTotalPrivateExchangeGross` float NOT NULL AFTER `SpendTotalGross` ;

-- ----------------------------
-- View structure for PublisherImpressionsAndSpendHourly
-- ----------------------------
DROP VIEW IF EXISTS `PublisherImpressionsAndSpendHourly`;
CREATE VIEW `PublisherImpressionsAndSpendHourly` AS select `phb`.`MDYH` AS `MDYH`,`phb`.`PublisherAdZoneID` AS `PublisherAdZoneID`,`pi`.`Name` AS `PublisherName`,`pad`.`AdOwnerID` AS `PublisherInfoID`,`pad`.`AdName` AS `AdName`,`phb`.`AuctionCounter` AS `Requests`,`phb`.`BidsWonCounter` AS `Impressions`,round(((`phb`.`SpendTotalNet` / `phb`.`BidsWonCounter`) * 1000),7) AS `eCPM`,round(((`phb`.`SpendTotalPrivateExchangeGross` / `phb`.`BidsWonCounter`) * 1000),7) AS `GrossECPM`,round(((`phb`.`SpendTotalGross` / `phb`.`BidsWonCounter`) * 1000),7) AS `GrossExchangeECPM`,concat(round(((`phb`.`BidsWonCounter` / `phb`.`AuctionCounter`) * 100),2),'%') AS `FillRate`,round(`phb`.`SpendTotalNet`,7) AS `Revenue`,round(`phb`.`SpendTotalPrivateExchangeGross`,7) AS `GrossRevenue`,round(`phb`.`SpendTotalGross`,7) AS `GrossExchangeRevenue`,`phb`.`DateCreated` AS `DateCreated` from ((`PublisherHourlyBids` `phb` join `PublisherAdZone` `pad` on((`phb`.`PublisherAdZoneID` = `pad`.`PublisherAdZoneID`))) join `PublisherInfo` `pi` on((`pad`.`AdOwnerID` = `pi`.`PublisherInfoID`))) ;

-- Add Credit app data and bits for PlatformConnection and SSP Inventory approvals

ALTER TABLE  `DemandCustomerInfo` ADD `ApprovedForPlatformConnectionInventory` tinyint(4) NOT NULL DEFAULT '0' AFTER `PartnerType` ;
ALTER TABLE  `DemandCustomerInfo` ADD `ApprovedForSspRtbInventory` tinyint(4) NOT NULL DEFAULT '0' AFTER `ApprovedForPlatformConnectionInventory` ;
ALTER TABLE  `DemandCustomerInfo` ADD `CreditApplicationWasSent` tinyint(4) NOT NULL DEFAULT '0' AFTER `ApprovedForSspRtbInventory` ;
ALTER TABLE  `DemandCustomerInfo` ADD `DateCreditApplicationWasSent` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `CreditApplicationWasSent` ;

-- Remove columns we can't support in the near future with OpenRTB
-- Most of these are from Pubmatic
-- Keep Frequency and other columns not dependent on OpenRTB data exclusively

ALTER TABLE InsertionOrderLineItemRestrictions DROP COLUMN `InMultipleNestedIframes`;
ALTER TABLE InsertionOrderLineItemRestrictions DROP COLUMN `CookieGrep`;
ALTER TABLE InsertionOrderLineItemRestrictions DROP COLUMN `PmpEnable`;

ALTER TABLE InsertionOrderLineItemVideoRestrictions DROP COLUMN `PmpEnable`;

ALTER TABLE InsertionOrderLineItemRestrictionsPreview DROP COLUMN `InMultipleNestedIframes`;
ALTER TABLE InsertionOrderLineItemRestrictionsPreview DROP COLUMN `CookieGrep`;
ALTER TABLE InsertionOrderLineItemRestrictionsPreview DROP COLUMN `PmpEnable`;

ALTER TABLE InsertionOrderLineItemVideoRestrictionsPreview DROP COLUMN `PmpEnable`;

ALTER TABLE InsertionOrderLineItem DROP COLUMN `InsertionOrderTypeID`;
ALTER TABLE InsertionOrderLineItemPreview DROP COLUMN `InsertionOrderTypeID`;

DROP TABLE IF EXISTS `InsertionOrderType`;

-- ----------------------------
-- Table structure for PrivateExchangeTheme
-- ----------------------------
DROP TABLE IF EXISTS `PrivateExchangeTheme`;
CREATE TABLE `PrivateExchangeTheme` (
  `UserID` int(11) NOT NULL,
  `ThemeParamsSerialized` text NOT NULL,
  PRIMARY KEY (`UserID`),
  UNIQUE KEY `UserIDKey` (`UserID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for PrivateExchangeVanityDomain
-- ----------------------------
DROP TABLE IF EXISTS `PrivateExchangeVanityDomain`;
CREATE TABLE `PrivateExchangeVanityDomain` (
  `UserID` int(11) NOT NULL,
  `VanityDomain` char(255) NOT NULL,
  `UseLogo` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`UserID`),
  UNIQUE KEY `UserIDKey` (`UserID`),
  UNIQUE KEY `VanityDomainKey` (`VanityDomain`),
  INDEX `VanityDomainIdx` (`VanityDomain`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;






