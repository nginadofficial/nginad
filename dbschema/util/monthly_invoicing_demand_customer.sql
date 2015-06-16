--
-- Use this SQL script to invoice demand users monthly
--

select 
au.user_login AS `UserName`,
au.user_email AS `Email`,
bshiccs.`InsertionOrderLineItemID` AS `InsertionOrderLineItemID`,
acb.`Name` AS `Banner Name`,
CONCAT('$', ROUND(sum(bshiccs.`CurrentSpendGross`), 2)) AS `TotalSpendGross`,
FORMAT(sum(bshiccs.`ImpressionsCounter`), 0) AS `TotalImpressions` 
from `BuySideHourlyImpressionsCounterCurrentSpend` bshiccs
inner join InsertionOrderLineItem acb on acb.`InsertionOrderLineItemID` = bshiccs.`InsertionOrderLineItemID`
inner join auth_Users au on acb.UserID = au.user_id
where bshiccs.DateCreated >= '2015-02-01 00:00:00'
and bshiccs.DateCreated < '2015-03-01 00:00:00'
-- and LOWER(au.user_login) = 'userlogin'
group by LOWER(au.user_login)