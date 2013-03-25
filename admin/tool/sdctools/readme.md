# SDC Admin Tools

Moodle admin tool to do minor admin tasks at South Devon College. Currently it does:

* User email checking: blank emails prevent user login

Has been checked against the Code Checker local plugin for Moodle coding standards conformity.

## Requirements

Moodle 2.x. Tested with 2.4.3. 

## Installation

* Copy the 'sdctools' folder into /admin/tool/
* Visit your Moodle as admin and click on Notifications, follow prompts.

## Use 

After installation, you should see a new option 'SDC Tools' in the Site Administration &rarr; Development menu. There will also be a new option 'Empty email check' in the Site Administration &rarr; Reports menu (subject to change as the plugin develops).  Click on either of these to scan and report on any users without an email address.

## To do

* Non-standard / non-SDC email address checks.

## History

* 2013-03-25, version 0.1.1:    Added to Reports admin menu with different lang string; better readme.
* 2013-03-22, version 0.1:      Initial release.

## Author / Contact

&copy; 2013 Paul Vaughan, South Devon College. paulvaughan@southdevon.ac.uk
