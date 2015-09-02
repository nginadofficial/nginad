--
-- Use this SQL script to invoice private exchange admins monthly according to their usage
-- and the exchange wide mark-up rate
--

select 
aub.`user_login` AS `UserName`,
aub.`user_email` AS `Email`,
CONCAT('$', ROUND(sum(piash.`GrossExchangeRevenue`), 2)) AS `RevenueExchangeGross`,
CONCAT('$', ROUND(sum(piash.`GrossRevenue`), 2)) AS `RevenueGross`,
CONCAT('$', ROUND(sum(piash.`GrossExchangeRevenue`) - sum(piash.`GrossRevenue`), 2)) AS `Private_Exchange_Fee`,
FORMAT(sum(piash.`Impressions`), 0) AS `TotalImpressions`,
FORMAT(sum(piash.`Requests`), 0) AS `TotalRequests` 
from `PublisherImpressionsAndSpendHourly` piash
inner join PublisherInfo pi on piash.PublisherInfoID = pi.PublisherInfoID
inner join auth_Users aua on aua.PublisherInfoID = pi.PublisherInfoID
inner join auth_Users aub on aub.user_id = aua.parent_id
where piash.DateCreated >= '2015-08-01 00:00:00'
and piash.DateCreated < '2015-09-01 00:00:00'
-- and aub.`user_login` = 'username'
and piash.GrossExchangeRevenue != 0
group by aub.`user_login`
