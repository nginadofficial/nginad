-- ----------------------------
-- View structure for PublisherImpressionsAndSpendHourly
-- ----------------------------
DROP VIEW IF EXISTS `PublisherImpressionsAndSpendHourly`;
CREATE VIEW `PublisherImpressionsAndSpendHourly` AS select `ssphb`.`MDYH`, `ssphb`.`PublisherAdZoneID`, `pi`.`Name` as PublisherName, pad.`AdOwnerID` as PublisherInfoID, `pad`.`AdName`, sum(`ssphb`.`BidsWonCounter`) as `Impressions`, round(sum(`ssphb`.`SpendTotalNet`), 7) as `Revenue`, round(sum(`ssphb`.`SpendTotalGross`), 7) as `GrossRevenue`, round((sum(`ssphb`.`SpendTotalNet`) / sum(`ssphb`.`BidsWonCounter`)),7) AS `eCPM`, round((sum(`ssphb`.`SpendTotalGross`) / sum(`ssphb`.`BidsWonCounter`)),7) AS `GrossECPM`, `ssphb`.`DateCreated` from `SellSidePartnerHourlyBids` ssphb inner join `PublisherAdZone` pad on ssphb.`PublisherAdZoneID` = pad.`PublisherAdZoneID` inner join `PublisherInfo` pi on pad.`AdOwnerID` = pi.`PublisherInfoID` group by ssphb.`PublisherAdZoneID`, ssphb.`MDYH` ;

-- ----------------------------
-- View structure for DemandImpressionsAndSpendHourlyPre
-- ----------------------------
DROP VIEW IF EXISTS `DemandImpressionsAndSpendHourlyPre`;
CREATE VIEW `DemandImpressionsAndSpendHourlyPre` AS select `bshiccs`.`MDYH`, `bshiccs`.`AdCampaignBannerID`, `dci`.`Name` as DemandCustomerName, `dci`.`DemandCustomerInfoID` as DemandCustomerInfoID, `acb`.`Name` as BannerName, sum(`bshiccs`.`ImpressionsCounter`) as `Impressions`, round(sum(`bshiccs`.`CurrentSpendNet`),7) as `Cost`, round(sum(`bshiccs`.`CurrentSpendGross`),7) as `GrossCost`, round((sum(`bshiccs`.`CurrentSpendNet`) / sum(`bshiccs`.`ImpressionsCounter`)),7) AS `CPM`, round((sum(`bshiccs`.`CurrentSpendGross`) / sum(`bshiccs`.`ImpressionsCounter`)),7) AS `GrossCPM`, `bshiccs`.`DateCreated` from `BuySideHourlyImpressionsCounterCurrentSpend` bshiccs inner join `AdCampaignBanner` acb on bshiccs.`AdCampaignBannerID` = acb.`AdCampaignBannerID` inner join `auth_Users` au on au.`user_id` = acb.`UserID` inner join `DemandCustomerInfo` dci on au.`DemandCustomerInfoID` = dci.`DemandCustomerInfoID` group by `bshiccs`.`AdCampaignBannerID`, `bshiccs`.`MDYH` ;

-- ----------------------------
-- View structure for DemandImpressionsAndSpendHourly
-- ----------------------------
DROP VIEW IF EXISTS `DemandImpressionsAndSpendHourly`;
CREATE VIEW `DemandImpressionsAndSpendHourly` AS select diashp.MDYH, diashp.AdCampaignBannerID, diashp.DemandCustomerName, diashp.DemandCustomerInfoID, diashp.BannerName, group_concat(distinct bshibt.PublisherTLD) as PublisherTLDs, diashp.Impressions, diashp.Cost, diashp.GrossCost, diashp.CPM, diashp.GrossCPM, diashp.DateCreated from DemandImpressionsAndSpendHourlyPre diashp left outer join `BuySideHourlyImpressionsByTLD` bshibt on diashp.`AdCampaignBannerID` = `bshibt`.`AdCampaignBannerID` and diashp.`MDYH` = `bshibt`.`MDYH` group by `diashp`.`AdCampaignBannerID`, `diashp`.`MDYH` ;
