-- Private exchange publishers
-- 0 means they signed up via the public portal and do not belong to a demand customer
ALTER TABLE  `PublisherInfo` ADD  `ParentID` int NOT NULL DEFAULT 0 AFTER  `PublisherInfoID` ;

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
ALTER TABLE InsertionOrderLineItemPreview CHANGE AdCampaignBannerID InsertionOrderLineItemID int(11) NOT NULL;
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
ALTER TABLE InsertionOrderPreview CHANGE AdCampaignID InsertionOrderID int(11) NOT NULL;

-- Table structure for InsertionOrderType
ALTER TABLE InsertionOrderType CHANGE AdCampaignTypeID InsertionOrderTypeID int(11) unsigned NOT NULL;

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


