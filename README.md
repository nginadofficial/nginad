nginad
=======

August 11, 2015
------------------

A Critical Signup bug was found by a user. The wrong field was getting assigned a MySQL date.
This requires a minor version update to version 1.6.2

August 8, 2015
------------------

One of the Ad Networks using NginAd for RTB found a massive bug with some of the new 1.6 classes which compile RTB statistics for RTB requests shown in the inventory choosers.
We have patched these bugs, with mutexes and semaphores that spin lock the data keys in APC while the existing data is flushed back to the DB in an upsert.

August 4, 2015
------------------

*NEWS:* The NginAd Project is in the process of filing for non-profit organization status in the United States and is now under the control of the NginAd Foundation.

July 31, 2015
------------------

NginAd 1.6 is being released and tagged today. 
This release features private exchanges, vanity domains, AJAX creatives uploads and much more.
There is a confluence page describing the features and differences here:
https://nginad.atlassian.net/wiki/display/NGIN/NginAd-Concepts

July 21, 2015
------------------

Vanity Domains and themes are now ostensibly done. 
That was the last thing that needed to be done for NginAd 1.6 to be complete.
However, we have to do some production testing before we release NginAd 1.6. 
This testing should not take more than a day or two.

July 20, 2015
------------------

Vanity Domains and Themes are now persisted in the DB and the CSS and Logo files are written out.
However, they need to be implemented in the layout.phtml. That is the last thing to do in version 1.6.
It should be very quick to complete, and will be completed tomorrow morning some time.

July 19, 2015
------------------

The creatives manager/image uploader is done! 
Finally dropzone.js was used for drag & drop style uploading similar to the new version of JIRA.

Also, 2 new params were set in the delivery.local.php.dist so that the exchange name and the base site url are automatic in user update emails and for the creatives uploader.

The last remaining item before 1.6 is ready to dev-test and release is to finish the vanity domains.
That should provide some similar functionality to the full featured version of AppNexus.

July 18, 2015
------------------

Something awesome just happened. Private Exchanges are all done. Some other minor fixes are also done.
The nomenclature changes for those crying out for Insertion Orders and Line Items are also done.

This is the remaining list for NginAd 1.6 and these things are relatively quick:

* Flash or AJAX image uploader for ad agency creatives in insertion order line items
* Vanity domains like http://preview.test.console.appnexus.com so that private exchange customers can white label from a single NginAd Exchange like AppNexus with their own publishers visiting their own login with their logo and CSS color scheme. This will be done with a wildcard virtual host in nginx, and a HTTP host name to private exchange user mapping in the database. The private exchange customers will need to set a DNS "A RECORD" to the IP Address of the NginAd load balancer. FAQ Instructions to add the DNS record (like Google does for Enterprise Apps) and the domain name will be requested in the dashboard so it's automated.

June 25, 2015
------------------

DO NOT CHECK OUT NGINAD FROM MASTER!

Master is now the development branch for NginAd 1.6!
This is the last time master will be used as a development branch, promised!

If you need to use NginAd, please check out the 1.5.2 tag here:
https://github.com/nginadfoundation/nginad/releases/tag/1.5.2

Also note that today at 1:30PM PST the lead developer Chris Gu is having double extraction 
oral surgery #1 and #16, so it's likely there will be no further commits until tomorrow.
The ETA on the 1.6 version being complete and tagged in master is 1 week at this point due to delays.
We had a 24 hour power loss in El Monte, California yesterday for instance: http://imagizer.imageshack.us/a/img905/2708/PFHmAX.png

Here is the comp on the new NginAd 1.6 Private Exchange SSP channel selector:
http://imageshack.com/a/img540/51/2lcR85.jpg

Note that the IFrame lightbox popup to SiteScout is just an example, the RTB channel selector lightbox will 
be comprised of the actual incoming SSP channels via OpenRTB

June 6, 2015
------------------

The 1.5.2 release adds support for the Fidelity Media OpenRTB bid responder module: http://fidelity-media.com
* It also adds nurl (bid win notice URL) tracking and logging and a JSON log aggregator in the new unit_test folder.

May 20, 2015
------------------

The 1.5.1 release adds support for the Forensiq.com proprietary Ad Fraud detection service. 
It adds both the OpenRTB API implementation for real time ad fraud blocking and the pixel service for tracking
the success of the blocking campaign after the ad tag has already been shown, or is in the process of being loaded
by the end user's browser. Support for Forensiq is disabled by default just like the Project Honeypot check.
You need to go to http://www.forensiq.com and contact them if you wish to pay for their service.
Also, the paid 3rd party API was optimized in the work flow so it's only used when
an OpenRTB request has a valid response. You wouldn't want to run up your 3rd party service API bill if 
your instance of NginAd has no valid response to the bid request. Also added was a tasklet to check the floor 
price against the OpenRTB impression bidfloor price. That way it can respect the low bid of DSPs.
You may want to disable this, but it's enabled by default.

May 2, 2015
------------------

The 1.5 release fixes various OpenRTB specific bugs encountered when using the app versus the site object.
It also fixes a logging bug with the geo object.


January 18, 2015
------------------

The 1.4.52 release fixes many bugs with VAST/VPAID ad delivery. 

 * Default VAST tag for when no passback exists and the auction is not won
 * Video Zone input validation bug fix
 * Video Ad Campaign Restrictions on APIs bug fix
 * Video VAST URL Wrapper bug fix for secondary video servers like LiveRail
 * NginAd Ad Server now requires php-mcrypt to be installed to function due to the use of ZF2 encryption methods

January 10, 2015
------------------

The 1.4.51 release adds Google backed Project Honeypot ad fraud detection to NginAd, though it is disabled by default.

December 29, 2014
------------------

The 1.4.48 release to address the lack of a way to view statistics for an individual demand customer or publisher as admin, and provides a way to download excel stats for single users only for accounting purposes.
Also addresses the lack of admin email alerts and ways to turn those alerts on or off via the email config file.

 * Fixes statistics functions so if a publisher or demand user is selected, the statistics will be shown for that user only until they are deselected. The same functionality is applied to the Excel file download.
 * Also adds configurable email alerts for the following user events: user sign-ups, ad zone changes, website domain additions, and ad campaign changes. The new alerts are configurable in email.local.php in the dist file.
 * Users need to be notified by email when their domains, ad zones and ad campaigns are manually approved as opposed to auto-approval for publisher domains and ad zones. 
 * Also removed a needless comments in ReportHelper.php

December 28, 2014
------------------

The 1.4.47 release to address the missing Excel Report download button feature is complete. It was coded and added to all report types. Also there are some small reporting bug fixes in NginAd 1.4. This may be the last feature update in 1.4 until we merge the leads-unlimited branch and release NginAd 1.5

 * Implemented Excel file downloads for every type of statistics report
 * Also did some bug fixes to the statistics totals in the JSON.

December 26, 2014
------------------

Last 1.4 Post Fix to be implemented - Excel Spreadsheet Export in the Reporting tab for Publishers and Demand clients

Obviously publishers and demand customers need to be able to download an excel file for billing.
Right now they have to copy/paste the numbers off the screen due to the not-so-stellar performance of the Ukrainian development team that originally worked on this feature.
This crucial feature should be finished in the next couple days and a new 1.4.47 release will be created.

December 25, 2014
------------------

Christmas day - lost impressions due to client networks fix.

This fix aims to harmonize the numbers between SSPs which are sold ad inventory and the NginAd instance when a certain percentage of client impressions are lost in the ad tag loading chain or due to client bounces where the nested creatives were not completely loaded.

Set up ad impression network loss damper percentages. 
A certain percentage of users will load the initial ad tag, but bounce before the demand customer's inner ad creatives will load. This will trigger impressions on the NginAd instance, but the impressions will not match on the SSPs. To compensate, we set up an impressions lost damper percentage, and to harmonize the imps number from the NginAd instance to the SSPs where they are being sold.

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
