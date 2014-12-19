nginad
=======

December 19, 2014
------------------
The NginAd OpenRTB Ad Server version 1.5 leads-unlimited branch has now been cut. 
This 1.5 release aims to do the following:

 * Create and implement a new standard specification for leads data API for buying and selling on lead exchanges implementing the API, based on JSON and new technologies not patented, and loosely based on the IAB OpenRTB protocol ( and submit it to the IAB after lead partners consortium review )
 * Create a location based data center which can match the location of lead buyers' geographical lead demand to sellers' lead data in a geographically correct way
 * Create timeout windows for certain lead types which can assure the freshness of sales and financial leads bought and sold through the exchange
 * Provide a CPM to Lead tasklet workflow which eliminates the need for a landing page or website for the user to complete sales lead information. This will allow direct lead conversions from DSP traffic.
 * Provide some type of free online business referral quality control mechanism or provide API to access a paid service for those who buy a SaaS solution
 * Provide a secure data transmission mechanism for leads of a financial nature such as Forex leads, large purchase leads, mortgage leads, insurance leads, loan leads, credit card leads, ect...
 * Provide lead submissions over most if not all IAB advertising verticals listed in the OpenRTB 2.2 specification
 * Provide an initial marketplace for NginAd 1.5 users to buy and sell leads, while they build other partners

December 12, 2014
------------------
NginAd OpenRTB Ad Server version 1.4 has now been released to the public. 

NginAd 1.4 Features:
 
 * VAST Video capabilities with LiveRail, ect... rev-share as well as raw VAST XML
 * Full OpenRTB 2.2 Object Architecture parsers and hierarchy according to the IAB spec.
 * Full Enterprise Java style Business Process Workflows and Activies called "tasklets"
 * Third party verification activities
 * Various parameters that were un-usable such as topframe, secure, and referrer were move to the new OpenRTB 2.2 objects
 * Reverse proxy functionality for VAST URLs to keep impression counts on video ad servers consistent
 * RTB Bid Notification (nurl) is now implemented with optional asychronous functionality
 * Descriptive comments highlighting the new features
 * Optional yearly Paid Support has now begun ( available at http://www.nginad.com/paidsupport/ )

It was lightly tested with limited smoke and regression tests, and developer tested.
If you find bugs please report them by going to www.nginad.com and filing a support ticket.
A support team member will review it and file a JIRA if needed.

December 6, 2014
------------------
Sadly the NginAd 1.4 release with VAST is not yet complete. The video tables are there and the persistence to the DB is there but 
the workflow and preview to prod table conversions are not yet complete. Also the OpenRTB 2.2 options like top level frame, referrer
and others are not yet mapped. Also the publisher and demand user approval lists are not showing all the rows. 
We will need to fix these bugs quickly and release NginAd 1.4 in the coming day or two. 
I don't think we can put pagination in for approvals because it will take too long and this release is a week behind already.

November 23, 2014
------------------
As a last task before the release of NginAd 1.4 we must add some micro-second timing to the logs for all Workflow processes
and tasklets. This way we know exactly how long each new process is taking and adding to the overall
latency of the new business rules processing engine.

September 30, 2014
------------------
Fixed some of Mike/Ukraine team's Reporting module bugs. I did not create JIRAs since they are so numerous it requires a re-factor.
Also I will add a lightbox with the publisher and demand agreement acceptance on login in a commit soon to come

September 3, 2014
------------------
Many changes and updates were made to the project. Unfortunately the commit log had to be wiped to make the project public.
All new changes will be logged.

January 29, 2014
------------------
As of the Commit ID 64979b6ff27ebed7e78ed2c728322367cf09f330 ("Completed authentication fix and refactoring"), there are directories that are deleted! You will also need to remove the following directories and its contents:
> upload/module/RTBManager/src/RTBManager/ZfcRbac
> upload/module/RTBManager/src/RTBManager/auth

And the following files were renamed:
> upload/config/autoload/{system.local.php → system.global.php}
> upload/config/autoload/{system.local.php.dist → system.global.php.dist}

Some files were removed from Git's version control, but remains relevant on the server itself.

This delete action may be necessary if your code synchronization/publishing techniques/methods do not include the removal of files, but simply appends new and updates existing files. This will help clean up the files and make your installation a bit sane.

------------------
January 16, 2014
------------------
As of this date, you will need to update the composer.json file with the one included in the upload folder. Then you will need to update both *zf-commons/zfc-rbac* and *Zend Framework 2* to the latest versions.

In the directory where the *composer.phar* and *composer.json* files are located:
```bash
php composer.phar self-update
php composer.phar update
```

This must be done for all commits on and after Commit ID 2df868ac5114caa72674b4e0c87dfd27a1f70fb7 ("Refactor manager class to subscriber class").

In addition, if you had **UPGRADED** PHP from a version (older than) **PHP <5.3**, you **_MUST_** install the **INTL** module for PHP, and restart the web server!
```bash
yum install php-intl
/sbin/service httpd restart
```

Failure to do so will result in the following error message:
> Fatal error: Uncaught exception 'Zend\I18n\Exception\ExtensionNotLoadedException' with message 'Zend\I18n\Translator component requires the intl PHP extension'

------------------
December 20, 2013
------------------
As of this date, you will also need PHP 5.4 or higher, as the latest *zf-commons/zfc-rbac* requires it.

If using CentOS, upgrade with YUM:
```bash
rpm -Uvh http://rpms.famillecollet.com/enterprise/remi-release-6.rpm
yum update
```

------------------
November 29, 2013
------------------
If you are upgrading from a version of NginAd that is older than Commit ID efab430f39a7833fd09711030ad4389627fe7e15 ("Something is wrong with the ZF2 *preDispatch* event stack, so I put the…") on November 28, 2013, please remember to update to the latest Commit ID 943c13583099f8fbba0c264e96876f67af588d44 ("Merge typo") on November 29, 2013. Then remember to run the database update scripts AND `php composer.phar update` to install *ZF-Commons/Rbac* and *Doctrine/ORM*.

This will also update to the latest Zend Framework.
