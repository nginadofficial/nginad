ALTEDROP TABLE IF EXISTS `PublisherWebsite`;
CREATE TABLE `PublisherWebsite` (
  `PublisherWebsiteID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `WebDomain` varchar(255) NOT NULL,
  `DomainOwnerID` int(10) unsigned NOT NULL,
  `AutoApprove` smallint(6) NOT NULL DEFAULT '1',
  `ApprovalFlag` smallint(6) NOT NULL DEFAULT '0',
  `IABCategory` char(8) DEFAULT NULL,
  `IABSubCategory` char(8) DEFAULT NULL,
  `Description` text,
  `DateCreated` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `DateUpdated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`PublisherWebsiteID`),
  UNIQUE KEY `WebDomain_UNIQUE` (`WebDomain`,`DomainOwnerID`) USING BTREE,
  KEY `FK_Owner_User_ID` (`DomainOwnerID`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;