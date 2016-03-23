-- ----------------------------
-- Table structure for InsertionOrderLineItemToNativeAd
-- ----------------------------
DROP TABLE IF EXISTS `InsertionOrderLineItemToNativeAd`;
CREATE TABLE `InsertionOrderLineItemToNativeAd` (
  `InsertionOrderLineItemToNativeAdID` int(11) NOT NULL AUTO_INCREMENT,
  `InsertionOrderLineItemID` int(11) NOT NULL,
  `NativeAdID` int(11) NOT NULL,
  `DateCreated` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `DateUpdated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`InsertionOrderLineItemToNativeAdID`),
  UNIQUE KEY `IDX_IolmToNa` (`InsertionOrderLineItemID`,`NativeAdID`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for InsertionOrderLineItemPreviewToNativeAd
-- ----------------------------
DROP TABLE IF EXISTS `InsertionOrderLineItemPreviewToNativeAd`;
CREATE TABLE `InsertionOrderLineItemPreviewToNativeAd` (
  `InsertionOrderLineItemPreviewToNativeAdID` int(11) NOT NULL AUTO_INCREMENT,
  `InsertionOrderLineItemPreviewID` int(11) NOT NULL,
  `NativeAdID` int(11) NOT NULL,
  `DateCreated` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `DateUpdated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`InsertionOrderLineItemPreviewToNativeAdID`),
  UNIQUE KEY `IDX_IolmToNap` (`InsertionOrderLineItemPreviewID`,`NativeAdID`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
