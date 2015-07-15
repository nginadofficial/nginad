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

ALTER TABLE  `SellSidePartnerHourlyBids` ADD `SpendTotalPrivateExchangeNet` float NOT NULL AFTER `SpendTotalGross` ;
ALTER TABLE  `PublisherHourlyBids` ADD `SpendTotalPrivateExchangeNet` float NOT NULL AFTER `SpendTotalGross` ;
