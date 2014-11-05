-- ----------------------------
-- View structure for DemandImpressionsAndSpendHourly
-- ----------------------------
DROP VIEW IF EXISTS `DemandImpressionsAndSpendHourly`;
CREATE VIEW `DemandImpressionsAndSpendHourly` AS select diashp.MDYH, diashp.AdCampaignBannerID, diashp.DemandCustomerName, diashp.DemandCustomerInfoID, diashp.BannerName, group_concat(distinct `bshibt`.`PublisherTLD` separator ', ') as PublisherTLDs, diashp.Impressions, diashp.Cost, diashp.GrossCost, diashp.CPM, diashp.GrossCPM, diashp.DateCreated from DemandImpressionsAndSpendHourlyPre diashp left outer join `BuySideHourlyImpressionsByTLD` bshibt on diashp.`AdCampaignBannerID` = `bshibt`.`AdCampaignBannerID` and diashp.`MDYH` = `bshibt`.`MDYH` group by `diashp`.`AdCampaignBannerID`, `diashp`.`MDYH` ;
