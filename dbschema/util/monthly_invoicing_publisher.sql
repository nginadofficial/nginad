--
-- Use this SQL script to pay publishers monthly
--

select 
pi.`Name` AS `UserName`,
pi.Email AS `Email`,
GROUP_CONCAT(piash.`AdName`),
CONCAT('$', ROUND(sum(piash.`Revenue`), 2)) AS `RevenueNet`,
FORMAT(sum(piash.`Impressions`), 0) AS `TotalImpressions`,
FORMAT(sum(piash.`Requests`), 0) AS `TotalRequests` 
from `PublisherImpressionsAndSpendHourly` piash
inner join PublisherInfo pi on piash.PublisherInfoID = pi.PublisherInfoID
where piash.DateCreated >= '2015-02-01 00:00:00'
and piash.DateCreated < '2015-03-01 00:00:00'
-- and pi.`Name` = 'username'
and piash.Revenue != 0
group by pi.`Name`