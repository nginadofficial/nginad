-- ----------------------------
-- Set up ad impression network loss damper percentages
-- A certain percentage of users will load the initial
-- ad tag, but bounce before the demand customer's inner
-- ad creatives will load. This will trigger impressions
-- on the NginAd instance, but the impressions will not
-- match on the SSPs. To compensate, we set up an
-- impressions lost damper percentage to compensate,
-- and to harmonize the imps number from the NginAd 
-- instance to the SSPs where they are being sold.
-- ----------------------------

-- ----------------------------
-- Table structure for PublisherImpressionsNetworkLoss
-- ----------------------------
DROP TABLE IF EXISTS `PublisherImpressionsNetworkLoss`;
CREATE TABLE `PublisherImpressionsNetworkLoss` (
  `PublisherInfoID` int(11) NOT NULL,
  `CorrectionRate` float NOT NULL,
  PRIMARY KEY (`PublisherInfoID`),
  UNIQUE KEY `PublisherInfoID` (`PublisherInfoID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


-- ----------------------------
-- Table structure for PublisherWebsiteImpressionsNetworkLoss
-- ----------------------------
DROP TABLE IF EXISTS `PublisherWebsiteImpressionsNetworkLoss`;
CREATE TABLE `PublisherWebsiteImpressionsNetworkLoss` (
  `PublisherWebsiteID` int(11) NOT NULL,
  `CorrectionRate` float NOT NULL,
  PRIMARY KEY (`PublisherWebsiteID`),
  UNIQUE KEY `PublisherWebsiteID` (`PublisherWebsiteID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;