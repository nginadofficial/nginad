--
-- Use this SQL script to view revenue per publisher zone monthly
--

select 
pi.`Name` AS `UserName`,
pi.Email AS `Email`,
piash.`PublisherAdZoneID` AS `PublisherAdZoneID`,
piash.`AdName` AS `Banner Name`,
CONCAT('$', ROUND(sum(piash.`Revenue`), 2)) AS `RevenueNet`,
FORMAT(sum(piash.`Impressions`), 0) AS `TotalImpressions`,
FORMAT(sum(piash.`Requests`), 0) AS `TotalRequests` 
from `PublisherImpressionsAndSpendHourly` piash
inner join PublisherInfo pi on piash.PublisherInfoID = pi.PublisherInfoID
where piash.DateCreated >= '2015-01-01 00:00:00'
and piash.DateCreated < '2015-02-01 00:00:00'
and pi.`Name` = 'username'
group by piash.`PublisherAdZoneID`