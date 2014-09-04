nginad
=======

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

As documented per [JIRA AL-59: Latest version of nginad causes FATAL ERROR on 404](https://cdnpal.atlassian.net/browse/AL-59)

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
