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
  `BuySidePartnerID` int(11) unsigned NOT NULL,
  `SspRtbChannelSiteID` int(11) unsigned NOT NULL,
  `MDYH` char(15) NOT NULL,
  `ImpressionsOfferedCounter` int(11) unsigned NOT NULL DEFAULT 0,
  `AuctionBidsCounter` int(11) unsigned NOT NULL DEFAULT 0,
  `DateCreated` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `DateUpdated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`SspRtbChannelDailyStatsID`),
  UNIQUE KEY `SspRtbChannelDailyStats_UNIQUE` (`SspRtbChannelDailyStatsID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;