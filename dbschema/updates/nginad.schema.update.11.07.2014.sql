-- ----------------------------
-- Table structure for PublisherHourlyBids
-- ----------------------------
DROP TABLE IF EXISTS `PublisherHourlyBids`;
CREATE TABLE `PublisherHourlyBids` (
  `PublisherHourlyBidsID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `PublisherAdZoneID` int(10) unsigned NOT NULL,
  `MDYH` char(15) NOT NULL,
  `AuctionCounter` bigint(20) NOT NULL,
  `BidsWonCounter` bigint(20) NOT NULL,
  `BidsLostCounter` bigint(20) NOT NULL,
  `BidsErrorCounter` bigint(20) NOT NULL,
  `SpendTotalGross` float NOT NULL,
  `SpendTotalNet` float NOT NULL,
  `DateCreated` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `DateUpdated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`PublisherHourlyBidsID`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- ----------------------------
-- View structure for PublisherImpressionsAndSpendHourly
-- ----------------------------
DROP VIEW IF EXISTS `PublisherImpressionsAndSpendHourly`;
CREATE VIEW `PublisherImpressionsAndSpendHourly` AS select `phb`.`MDYH`, `phb`.`PublisherAdZoneID`, `pi`.`Name` as PublisherName, pad.`AdOwnerID` as PublisherInfoID, `pad`.`AdName`, `phb`.`AuctionCounter` as `Requests`, `phb`.`BidsWonCounter` as `Impressions`, round(((`phb`.`SpendTotalNet` / `phb`.`BidsWonCounter`) * 1000),7) AS `eCPM`, round(((`phb`.`SpendTotalGross` / `phb`.`BidsWonCounter`) * 1000),7) AS `GrossECPM`, concat(round((`phb`.`BidsWonCounter` / `phb`.`AuctionCounter`) * 100, 2), '%') as `FillRate`, round(`phb`.`SpendTotalNet`, 7) as `Revenue`, round(`phb`.`SpendTotalGross`, 7) as `GrossRevenue`, `phb`.`DateCreated` from `PublisherHourlyBids` phb inner join `PublisherAdZone` pad on phb.`PublisherAdZoneID` = pad.`PublisherAdZoneID` inner join `PublisherInfo` pi on pad.`AdOwnerID` = pi.`PublisherInfoID` ;
