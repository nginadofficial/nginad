-- ----------------------------
-- Table structure for HeaderBiddingPage
-- ----------------------------
DROP TABLE IF EXISTS `HeaderBiddingPage`;
CREATE TABLE `HeaderBiddingPage` (
  `HeaderBiddingPageID` int(11) NOT NULL AUTO_INCREMENT,
  `PublisherWebsiteID` int(11) NOT NULL,
  `PageName` char(100) NOT NULL,
  `JSHeaderFileUnqName` char(100) NOT NULL,
  `DateCreated` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `DateUpdated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`HeaderBiddingPageID`),
  UNIQUE KEY `HeaderBiddingPageID` (`HeaderBiddingPageID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for HeaderBiddingAdUnit
-- ----------------------------
DROP TABLE IF EXISTS `HeaderBiddingAdUnit`;
CREATE TABLE `HeaderBiddingAdUnit` (
  `HeaderBiddingAdUnitID` int(11) NOT NULL AUTO_INCREMENT,
  `HeaderBiddingPageID` int(11) NOT NULL,
  `PublisherAdZoneID` int(11) NOT NULL,
  `AdExchange` char(100) NOT NULL,
  `DivID` char(100) NOT NULL,
  `Height` int(11) NOT NULL,
  `Width` int(11) NOT NULL,
  `CustomParams` text NOT NULL,
  `AdTag` text NOT NULL,
  `DateCreated` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `DateUpdated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`HeaderBiddingAdUnitID`),
  UNIQUE KEY `HeaderBiddingAdUnitID` (`HeaderBiddingAdUnitID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE  `PublisherAdZone` ADD  `AuctionType` char(10) NOT NULL DEFAULT 'rtb' AFTER `ImpressionType` ;
ALTER TABLE  `PublisherAdZone` ADD  `HeaderBiddingAdUnitID` int(11) DEFAULT NULL AFTER `AuctionType` ;
