Sharing Cart
============

version 2.4, release 1 patch 2 - 2013.02.13

The "master" branch is no longer compatible with Moodle 2.2 or earlier.

* Moodle 2.2 => "MOODLE_22_STABLE" branch
* Moodle 1.9 => "MOODLE_19_STABLE" branch

Change Log
----------

* 2.4, release 1 patch 2
  * Limit applicable formats (issue #2)
  * lib.php is no longer required
* 2.4, release 1 patch 1
  * Set instance_can_be_docked to false
* 2.4, release 1
  * Support Moodle 2.4
* 2.3, release 2
  * New feature: Workaround for question bank restore issue (error_question_match_sub_missing_in_db)
* 2.3, release 1
  * Some minor fixes
* 2.3, release candidate 1
  * New feature: Option to copy with user data (for Wiki, Forum, Database, etc.)
  * Improvement: Ajaxify


Purpose
-------

The Sharing Cart is a block that enables sharing of Moodle content
(resources, activities) between multiple courses on your site.
You can share among teachers or among your own courses.
It copies and moves single course items without user data
-- similar to the "Import" function in Course Administration.
Items can be collected and saved on the Sharing Cart indefinitely,
serving as a library of frequently used course items available for duplication.


Requirements
------------

Moodle 2.3.1 or later, with AJAX enabled


License
-------

GPL v3
