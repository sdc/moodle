<?php // $Id: block_sharing_cart.php 905 2012-12-05 05:36:52Z malu $

$string['pluginname'] = 'Sharing Cart';
$string['sharing_cart:addinstance'] = 'Add a new Sharing Cart block';

$string['backup'] = 'Copy to Sharing Cart';
$string['restore'] = 'Copy to course';
$string['movedir'] = 'Move into folder';
$string['copyhere'] = 'Copy here';
$string['notarget'] = 'Target not found';
$string['clipboard'] = 'Copying this shared item';
$string['bulkdelete'] = 'Bulk delete';
$string['confirm_backup'] = 'Do you want to copy this activity into Sharing Cart?';
$string['confirm_userdata'] = 'Do you want to include user data in a copy of this activity?';
$string['confirm_restore'] = 'Do you want to copy this item to course?';
$string['confirm_delete'] = 'Are you sure you want to delete?';
$string['confirm_delete_selected'] = 'Are you sure you want to delete all selected items?';
$string['download'] = 'Download';

$string['conf:userdata_copyable_modtypes'] = 'User data copyable module types';
$string['conf:userdata_copyable_modtypes_desc'] = 'While copying an activity into the Sharing Cart,
a dialog shows an option whether a copy of an activity includes its user data or not,
if its module type is checked in the above and an operator has <strong>moodle/backup:userinfo</strong> capability.
(By default, only manager role has <strong>moodle/backup:userinfo</strong> capability.)';
$string['conf:workaround_qtypes'] = 'Workaround for question types';
$string['conf:workaround_qtypes_desc'] = 'The workaround for question restore issue will be performed if its question type is checked.
When the questions to be restored already exist, however, those data look like inconsistent,
this workaround will try to make another duplicates instead of reusing existing data.
It may be useful for avoiding some restore errors, such as <i>error_question_match_sub_missing_in_db</i>.';

$string['err:invalid'] = 'An invalid operation detected';
$string['err:record_id'] = 'Shared item ID was incorrect (not found)';
$string['err:course_id'] = 'Course ID was incorrect (not found)';
$string['err:section_id'] = 'Section ID was incorrect (not found)';
$string['err:module_id'] = 'Module ID was incorrect (not found)';
$string['err:capability'] = 'You don\'t have any permissions to access this shared item';
$string['err:backup'] = 'An error occurred while backing up';
$string['err:restore'] = 'An error occurred while restoring';
$string['err:move'] = 'An error occurred while moving shared item';
$string['err:delete'] = 'An error occurred while deleting shared item';
$string['err:record'] = 'An error occurred while operating data record';
$string['err:tempdir'] = 'An error occurred while creating temporary directory';
$string['err:cleanup'] = 'An error occurred while cleaning up temporary data';
$string['err:unsupported'] = 'Specified module does not support backup function';
$string['err:requireajax'] = 'Sharing Cart requires AJAX';

$string['sharing_cart'] = $string['pluginname'];
$string['sharing_cart_help'] = file_get_contents(__DIR__.'/help/sharing_cart.html');
