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
  KEY `FK_Publisher_Website_ID` (`PublisherWebsiteID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- View structure for PrivateExchangeRtbChannelDailyStatsRollUp
-- ----------------------------
DROP VIEW IF EXISTS `PrivateExchangeRtbChannelDailyStatsRollUp`;
CREATE VIEW `PrivateExchangeRtbChannelDailyStatsRollUp` AS select `percds`.`PublisherWebsiteID` AS `PublisherWebsiteID`, `au`.`user_id` AS `UserID`, `percds`.`MDY` AS `MDY`, `pw`.`VisibilityTypeID` AS `VisibilityTypeID`, `pw`.`WebDomain` AS `WebDomain`, `pw`.`IABCategory` AS `IABCategory`, `pi`.`Name` AS `PublisherName`, `au`.`parent_id` AS `ParentID`, `dci`.`Company` AS `BuySidePartnerName`, `pw`.`Description` AS `RtbChannelSiteName`, sum(`percds`.`ImpressionsOfferedCounter`) AS `ImpressionsOfferedCounter`, sum(`percds`.`AuctionBidsCounter`) AS `AuctionBidsCounter`, round(ceil((sum(`percds`.`BidTotalAmount`) / sum(`percds`.`AuctionBidsCounter`)) * 100000) / 100, 2) AS `BidTotalAverage`, round(max(`percds`.`BidFloor`), 2) AS `BidFloor` from `PrivateExchangeRtbChannelDailyStats` `percds` join `PublisherWebsite` `pw` on `percds`.`PublisherWebsiteID` = `pw`.`PublisherWebsiteID` join `PublisherInfo` `pi` on `pi`.`PublisherInfoID` = `pw`.`DomainOwnerID` join `auth_Users` `au` on `au`.`PublisherInfoID` = `pi`.`PublisherInfoID` join `auth_Users` `au2` on `au2`.`user_id` = `au`.`parent_id` join `DemandCustomerInfo` `dci` on `au2`.`DemandCustomerInfoID` = `dci`.`DemandCustomerInfoID` group by `percds`.`MDY`, `percds`.`PublisherWebsiteID` order by `ImpressionsOfferedCounter` ;

-- ----------------------------
-- View structure for SspRtbChannelDailyStatsRollUp
-- ----------------------------
DROP VIEW IF EXISTS `SspRtbChannelDailyStatsRollUp`;
CREATE VIEW `SspRtbChannelDailyStatsRollUp` AS select `srcds`.`SspRtbChannelSiteID` AS `SspRtbChannelSiteID`, `srcds`.`MDY` AS `MDY`, `srcds`.`SspRtbChannelSiteDomain` AS `WebDomain`, `srcds`.`SspRtbChannelSiteIABCategory` AS `IABCategory`, `srcds`.`SspRtbChannelPublisherName` AS `PublisherName`, `srcds`.`SspRtbChannelSiteName` AS `RtbChannelSiteName`, `srcds`.`BuySidePartnerName` AS `BuySidePartnerName`, sum(`srcds`.`ImpressionsOfferedCounter`) AS `ImpressionsOfferedCounter`, sum(`srcds`.`AuctionBidsCounter`) AS `AuctionBidsCounter`, round(ceil((sum(`srcds`.`BidTotalAmount`) / sum(`srcds`.`AuctionBidsCounter`)) * 100000) / 100, 2) AS `BidTotalAverage`, round(max(`srcds`.`BidFloor`), 2) AS `BidFloor` from `SspRtbChannelDailyStats` `srcds` group by `srcds`.`MDY`, `srcds`.`SspRtbChannelSiteID` order by `ImpressionsOfferedCounter` ;
