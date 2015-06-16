-- Private exchange publishers
-- 0 means they signed up via the public portal and do not belong to a demand customer
ALTER TABLE  `PublisherInfo` ADD  `ParentID` int NOT NULL DEFAULT 0 AFTER  `PublisherInfoID` ;
