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
  `DateUpdated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`PublisherHourlyBidsID`),
  UNIQUE KEY `PublisherHourlyBids_IDX` (`PublisherAdZoneID`,`MDYH`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
