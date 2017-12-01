Changes in version 1.1 (2015012000)
-------------------------------------
- Added collapsible sections to help prevent scroll of death when making lots of checks on big courses.
- Added checking of assignments, to check whether the due date is before the course start date or creation date. 

Changes in version 1.0.4 (2014112700)
-------------------------------------
- Removed (hopefully) the last set of hardcoded text strings from the renderer. (Thanks to German Valero for reporting this)

Changes in version 1.0.3 (2014111000)
-------------------------------------

- Fixed an issue when checking whether guest access is enabled on a course, (Checking if guest access is an option is helpful) Thanks to Michael Buchanan & Bas Brands for reporting this.
- Removed hardcoded language strings in the renderer. (Thanks to Bas Brands for reporting this).
- Fixed an issue when a cleanup has been run on the course, the block still shows all of the sections, including those that have been deleted.


Changes in version 1.0.2 (2014101700)
-------------------------------------

- Fixed an issue that prevent the block (and moodle) from loading (Thanks to Michael Buchanan for reporting this).
- Cleaned up the way in which the blocks default settings, and block settings are handled.

Changes in version 1.0.1 (2014100800)
-------------------------------------

- Fixed issues with undefined variables being used
- Added Support for Moodle 2.6
- Removed legacy references to $CFG prevent errors with block checking on a course

Changes in version 1.0. (2014100700)
-------------------------------------
- Initial Release