--
-- Use this SQL script to invoice demand users monthly
--

select 
au.user_login AS `UserName`,
bshiccs.`AdCampaignBannerID` AS `AdCampaignBannerID`,
acb.`Name` AS `Banner Name`,
CONCAT('$', ROUND(sum(bshiccs.`CurrentSpendGross`), 2)) AS `TotalSpendGross`,
FORMAT(sum(bshiccs.`ImpressionsCounter`), 0) AS `TotalImpressions` 
from `BuySideHourlyImpressionsCounterCurrentSpend` bshiccs
inner join AdCampaignBanner acb on acb.`AdCampaignBannerID` = bshiccs.`AdCampaignBannerID`
inner join auth_Users au on acb.UserID = au.user_id
where bshiccs.DateCreated >= '2015-01-01 00:00:00'
and bshiccs.DateCreated < '2015-02-01 00:00:00'
and LOWER(au.user_login) = 'user_login'
group by bshiccs.`AdCampaignBannerID`