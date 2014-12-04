-- ----------------------------
-- Table structure for PublisherAdZoneVideo
-- ----------------------------
DROP TABLE IF EXISTS `PublisherAdZoneVideo`;
CREATE TABLE `PublisherAdZoneVideo` (
  `PublisherAdZoneVideoID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `PublisherAdZoneID` int(11) NOT NULL,
  `MimesCommaSeparated` char(100) DEFAULT NULL,
  `MinDuration` int(10) unsigned NOT NULL,
  `MaxDuration` int(10) unsigned NOT NULL,
  `ApisSupportedCommaSeparated` char(100) DEFAULT NULL,
  `ProtocolsCommaSeparated` char(100) DEFAULT NULL,
  `DeliveryCommaSeparated` char(100) DEFAULT NULL,
  `PlaybackCommaSeparated` char(100) DEFAULT NULL,
  `StartDelay` char(5) DEFAULT NULL,
  `Linearity` int(10) DEFAULT NULL,
  `FoldPos` int(10) DEFAULT NULL,
  `VastPassbackXml` text NOT NULL,
  `Width` int(11) NOT NULL,
  `Height` int(11) NOT NULL,
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
  `GeoCountry` char(100) DEFAULT NULL,
  `GeoState` char(100) DEFAULT NULL,
  `GeoCity` char(255) DEFAULT NULL,
  `MimesCommaSeparated` char(100) DEFAULT NULL,
  `MinDuration` int(10) unsigned NOT NULL,
  `MaxDuration` int(10) unsigned NOT NULL,
  `ApisSupportedCommaSeparated` char(100) DEFAULT NULL,
  `ProtocolsCommaSeparated` char(100) DEFAULT NULL,
  `DeliveryCommaSeparated` char(100) DEFAULT NULL,
  `PlaybackCommaSeparated` char(100) DEFAULT NULL,
  `StartDelay` char(5) DEFAULT NULL,
  `Linearity` int(10) DEFAULT NULL,
  `FoldPos` int(10) DEFAULT NULL,
  `PmpEnable` tinyint(1) DEFAULT NULL,
  `Secure` tinyint(1) DEFAULT NULL,
  `Optout` tinyint(1) DEFAULT NULL,
  `Vertical` char(100) DEFAULT NULL,
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
  `GeoCountry` char(100) DEFAULT NULL,
  `GeoState` char(100) DEFAULT NULL,
  `GeoCity` char(255) DEFAULT NULL,
  `MimesCommaSeparated` char(100) DEFAULT NULL,
  `MinDuration` int(10) unsigned NOT NULL,
  `MaxDuration` int(10) unsigned NOT NULL,
  `ApisSupportedCommaSeparated` char(100) DEFAULT NULL,
  `ProtocolsCommaSeparated` char(100) DEFAULT NULL,
  `DeliveryCommaSeparated` char(100) DEFAULT NULL,
  `PlaybackCommaSeparated` char(100) DEFAULT NULL,
  `StartDelay` char(5) DEFAULT NULL,
  `Linearity` int(10) DEFAULT NULL,
  `FoldPos` int(10) DEFAULT NULL,
  `PmpEnable` tinyint(1) DEFAULT NULL,
  `Secure` tinyint(1) DEFAULT NULL,
  `Optout` tinyint(1) DEFAULT NULL,
  `Vertical` char(100) DEFAULT NULL,
  `DateCreated` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `DateUpdated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`AdCampaignVideoRestrictionsPreviewID`),
  UNIQUE KEY `RTBVideoPreviewID` (`AdCampaignBannerPreviewID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


ALTER TABLE  `PublisherAdZone` ADD  `ImpressionType` char(10) NOT NULL DEFAULT 'banner' AFTER  `PublisherAdZoneTypeID` ;

ALTER TABLE  `AdCampaignBanner` ADD  `ImpressionType` char(10) NOT NULL DEFAULT 'banner' AFTER  `AdCampaignTypeID` ;

ALTER TABLE  `AdCampaignBannerPreview` ADD  `ImpressionType` char(10) NOT NULL DEFAULT 'banner' AFTER  `AdCampaignTypeID` ;




