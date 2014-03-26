# SDC Admin Tools

Moodle admin tool to do minor admin tasks and summarise key server/Moodle information at South Devon College. Currently it does:

* Server details:
  * Operating system, Server load, Processes, Uptime and more
* Moodle details:
  * Version, users, courses, modules, backup status
* 'x' most recent user statistics
* 'x' most recent log entries
* Email address checks:
  * blank emails (prevent user login)
  * non-SDC email addresses being used
* User checks:
  * Password set to 'restored' (a user restored from a backup) which should not be in the users table
  * Users with 'anon' as part of their first and last name
* Course reports:
  * Full and short names
  * Created and modified dates
  * Format and access statistics
  * Enrollees in all roles (with optional pictures)
  * Backup, enrolment plugin and absence activity checks

Has been checked against the 'Code Checker' and 'Moodle PHPDoc Check' local plugins at various points for Moodle coding standards conformity and appropriate documentation of code.

## Requirements

Moodle 2.x. Tested with 2.4.3, 2.5.1 and 2.6.1.

## Installation

* Copy the 'sdctools' folder into /admin/tool/
* Visit your Moodle as admin and click on Notifications, follow prompts.

## Use 

After installation, you should see a new option 'SDC Tools' in the Site Administration &rarr; Development menu. There will also be a new option 'Empty email check' in the Site Administration &rarr; Reports menu (subject to change as the plugin develops).  Click on either of these to scan and report on any users without an email address.

## To do

* Make email domain check configurable.
* Make the absence activity checker configurable.
* More detailed breakdown of modules available in the course.

## History

* 2014-03-06, version 0.1.4:    Added number of modules visisble, hidden, total and average across courses.
* 2013-07-18, version 0.1.3:    Added many new features and changed to a multi-page format.
* 2013-03-26, version 0.1.2:    Check for non-SDC email addresses; change to code to better select/exclude email addresses.
* 2013-03-25, version 0.1.1:    Added to Reports admin menu with different lang string; better readme.
* 2013-03-22, version 0.1:      Initial release.

## Author / Contact

&copy; 2013, 2014 Paul Vaughan, South Devon College. paulvaughan@southdevon.ac.uk
