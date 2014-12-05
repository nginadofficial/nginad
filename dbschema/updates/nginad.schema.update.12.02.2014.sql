-- ----------------------------
-- Table structure for PublisherAdZoneVideo
-- ----------------------------
DROP TABLE IF EXISTS `PublisherAdZoneVideo`;
CREATE TABLE `PublisherAdZoneVideo` (
  `PublisherAdZoneVideoID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `PublisherAdZoneID` int(11) NOT NULL,
  `MimesCommaSeparated` char(100) NOT NULL,
  `MinDuration` int(10) unsigned NOT NULL,
  `MaxDuration` int(10) unsigned NOT NULL,
  `ApisSupportedCommaSeparated` char(100) NOT NULL,
  `ProtocolsCommaSeparated` char(100) NOT NULL,
  `DeliveryCommaSeparated` char(100) NOT NULL,
  `PlaybackCommaSeparated` char(100) NOT NULL,
  `StartDelay` char(5) NOT NULL,
  `Linearity` int(10) NOT NULL,
  `FoldPos` int(10) NOT NULL,
  `DateCreated` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `DateUpdated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`PublisherAdZoneVideoID`),
  UNIQUE KEY `UQ_PublisherAdZone` (`PublisherAdZoneID`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for AdCampaignVideoRestrictions
-- ----------------------------
DROP TABLE IF EXISTS `AdCampaignVideoRestrictions`;
CREATE TABLE `AdCampaignVideoRestrictions` (
  `AdCampaignVideoRestrictionsID` int(11) NOT NULL AUTO_INCREMENT,
  `AdCampaignBannerID` int(11) NOT NULL,
  `GeoCountry` char(100) NOT NULL,
  `GeoState` char(100) NOT NULL,
  `GeoCity` char(255) NOT NULL,
  `MimesCommaSeparated` char(100) NOT NULL,
  `MinDuration` int(10) unsigned NOT NULL,
  `MaxDuration` int(10) unsigned NOT NULL,
  `ApisSupportedCommaSeparated` char(100) NOT NULL,
  `ProtocolsCommaSeparated` char(100) NOT NULL,
  `DeliveryCommaSeparated` char(100) NOT NULL,
  `PlaybackCommaSeparated` char(100) NOT NULL,
  `StartDelay` char(5) NOT NULL,
  `Linearity` int(10) NOT NULL,
  `FoldPos` int(10) NOT NULL,
  `PmpEnable` tinyint(1) NOT NULL,
  `Secure` tinyint(1) NOT NULL,
  `Optout` tinyint(1) NOT NULL,
  `Vertical` char(100) NOT NULL,
  `DateCreated` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `DateUpdated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`AdCampaignVideoRestrictionsID`),
  UNIQUE KEY `RTBVideoID` (`AdCampaignBannerID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of AdCampaignVideoRestrictions
-- ----------------------------

-- ----------------------------
-- Table structure for AdCampaignVideoRestrictionsPreview
-- ----------------------------
DROP TABLE IF EXISTS `AdCampaignVideoRestrictionsPreview`;
CREATE TABLE `AdCampaignVideoRestrictionsPreview` (
  `AdCampaignVideoRestrictionsPreviewID` int(11) NOT NULL AUTO_INCREMENT,
  `AdCampaignBannerPreviewID` int(11) NOT NULL,
  `GeoCountry` char(100) NOT NULL,
  `GeoState` char(100) NOT NULL,
  `GeoCity` char(255) NOT NULL,
  `MimesCommaSeparated` char(100) NOT NULL,
  `MinDuration` int(10) unsigned NOT NULL,
  `MaxDuration` int(10) unsigned NOT NULL,
  `ApisSupportedCommaSeparated` char(100) NOT NULL,
  `ProtocolsCommaSeparated` char(100) NOT NULL,
  `DeliveryCommaSeparated` char(100) NOT NULL,
  `PlaybackCommaSeparated` char(100) NOT NULL,
  `StartDelay` char(5) NOT NULL,
  `Linearity` int(10) NOT NULL,
  `FoldPos` int(10) NOT NULL,
  `PmpEnable` tinyint(1) NOT NULL,
  `Secure` tinyint(1) NOT NULL,
  `Optout` tinyint(1) NOT NULL,
  `Vertical` char(100) NOT NULL,
  `DateCreated` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `DateUpdated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`AdCampaignVideoRestrictionsPreviewID`),
  UNIQUE KEY `RTBVideoPreviewID` (`AdCampaignBannerPreviewID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


ALTER TABLE  `PublisherAdZone` ADD  `ImpressionType` char(10) NOT NULL DEFAULT 'banner' AFTER  `PublisherAdZoneTypeID` ;

ALTER TABLE  `AdCampaignBanner` ADD  `ImpressionType` char(10) NOT NULL DEFAULT 'banner' AFTER  `AdCampaignTypeID` ;

ALTER TABLE  `AdCampaignBannerPreview` ADD  `ImpressionType` char(10) NOT NULL DEFAULT 'banner' AFTER  `AdCampaignTypeID` ;




