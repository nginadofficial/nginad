About this directory:
=====================

By default, this application is configured to load all configs in
`./config/autoload/{,*.}{global,local}.php`. Doing this provides a
location for a developer to drop in configuration override files provided by
modules, as well as cleanly provide individual, application-wide config files
for things like database connections, etc.

---------------------
LOCAL and GLOBAL configs
=====================

**IMPORTANT:** All files ending in `local.php` ***MUST NOT BE UPLOADED IN THE REPOSITORY!!!*** Instead, a sanitized `local.php.dist` should be saved and uploaded in the repository instead. When you define a "LOCAL" configuration file, it means the settings in those files will vary between servers and between installations, if you push it to the central repository without renaming the file, you risk causing other people's installation to be broken by it due to an unexpected configuration change when they copy the files over to update their installation with the latest code changes! (I.E.: wiping out their DB credentials in `database.local.php`.)

Local configuration files are files that are usually customized on each installation, where the lack of changes is the exception.

Global configuration files are opposite of the local configuration files in that in most installations, there is a lack of changes form the default, and changes in the global configuration is an exception.
