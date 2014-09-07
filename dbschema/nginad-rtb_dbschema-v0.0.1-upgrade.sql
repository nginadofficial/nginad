ALTER TABLE `PublisherWebsite` ADD `AutoApprove` smallint(6) DEFAULT 1 NOT NULL AFTER `DomainOwnerID`;
DROP TABLE `Websites`;
ALTER TABLE `PublisherWebsite` DROP `DomainMarkupRate`