-- ----------------------------
-- View structure for PublisherImpressionsAndSpendHourlyTotals
-- ----------------------------
DROP VIEW IF EXISTS `PublisherImpressionsAndSpendHourlyTotals`;
CREATE VIEW `PublisherImpressionsAndSpendHourlyTotals` AS select PublisherAdZoneID, PublisherName, PublisherInfoID, SUM(Requests) as TotalRequests, SUM(Impressions) as TotalImpressions, SUM(Revenue) as TotalRevenue from PublisherImpressionsAndSpendHourly group by PublisherAdZoneID order by PublisherAdZoneID ;

ALTER TABLE `PublisherAdZone` CHANGE `TotalAsk` `TotalRequests` bigint(20) unsigned NOT NULL DEFAULT '0';
ALTER TABLE `PublisherAdZone` CHANGE `TotalImpressions` `TotalImpressionsFilled` bigint(20) unsigned NOT NULL DEFAULT '0';
