Database Schema scripts
=========
This directory is used to store various database Schema changes that may be needed between versions to upgrade or to install/initialize.

If you have no database data installed, run the latest version (highest number) SQL statement that has the "initialization" tag, then move up from there running each upgrade script until you are at the latest version of the SQL files. Do not run the same version upgrade script as the initialization script.

If you are upgrading the database, upgrade from the version immediately after the version in which your current database is set.