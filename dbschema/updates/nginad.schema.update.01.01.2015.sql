-- Change the country code and state code char lengths
-- to accommodate lists with more than 10 items

ALTER TABLE `AdCampaignBannerRestrictions` CHANGE `GeoCountry` `GeoCountry` char(255) DEFAULT NULL;
ALTER TABLE `AdCampaignBannerRestrictions` CHANGE `GeoState` `GeoState` char(255) DEFAULT NULL;

ALTER TABLE `AdCampaignBannerRestrictionsPreview` CHANGE `GeoCountry` `GeoCountry` char(255) DEFAULT NULL;
ALTER TABLE `AdCampaignBannerRestrictionsPreview` CHANGE `GeoState` `GeoState` char(255) DEFAULT NULL;

ALTER TABLE `AdCampaignVideoRestrictions` CHANGE `GeoCountry` `GeoCountry` char(255) DEFAULT NULL;
ALTER TABLE `AdCampaignVideoRestrictions` CHANGE `GeoState` `GeoState` char(255) DEFAULT NULL;

ALTER TABLE `AdCampaignVideoRestrictionsPreview` CHANGE `GeoCountry` `GeoCountry` char(255) DEFAULT NULL;
ALTER TABLE `AdCampaignVideoRestrictionsPreview` CHANGE `GeoState` `GeoState` char(255) DEFAULT NULL;

