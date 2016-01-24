-- ----------------------------
-- Table structure for HeaderBiddingAdUnit
-- ----------------------------
DROP TABLE IF EXISTS `HeaderBiddingAdUnit`;
CREATE TABLE `HeaderBiddingAdUnit` (
  `HeaderBiddingAdUnitID` int(11) NOT NULL,
  `PublisherAdZoneID` int(11) NOT NULL,
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

