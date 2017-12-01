<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Public Profile -- a user's public profile page
 *
 * - each user can currently have their own page (cloned from system and then customised)
 * - users can add any blocks they want
 * - the administrators can define a default site public profile for users who have
 *   not created their own public profile
 *
 * This script implements the user's view of the public profile, and allows editing
 * of the public profile.
 *
 * @package    core_user
 * @copyright  2010 Remote-Learner.net
 * @author     Hubert Chathi <hubert@remote-learner.net>
 * @author     Olav Jordan <olav.jordan@remote-learner.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . '/../config.php');
require_once($CFG->dirroot . '/my/lib.php');
require_once($CFG->dirroot . '/user/profile/lib.php');
require_once($CFG->dirroot . '/user/lib.php');
require_once($CFG->libdir.'/filelib.php');

$userid         = optional_param('id', 0, PARAM_INT);
$edit           = optional_param('edit', null, PARAM_BOOL);    // Turn editing on and off.
$reset          = optional_param('reset', null, PARAM_BOOL);

$PAGE->set_url('/user/profile.php', array('id' => $userid));

if (!empty($CFG->forceloginforprofiles)) {
    require_login();
    if (isguestuser()) {
        $PAGE->set_context(context_system::instance());
        echo $OUTPUT->header();
        echo $OUTPUT->confirm(get_string('guestcantaccessprofiles', 'error'),
                              get_login_url(),
                              $CFG->wwwroot);
        echo $OUTPUT->footer();
        die;
    }
} else if (!empty($CFG->forcelogin)) {
    require_login();
}

$userid = $userid ? $userid : $USER->id;       // Owner of the page.
if ((!$user = $DB->get_record('user', array('id' => $userid))) || ($user->deleted)) {
    $PAGE->set_context(context_system::instance());
    echo $OUTPUT->header();
    if (!$user) {
        echo $OUTPUT->notification(get_string('invaliduser', 'error'));
    } else {
        echo $OUTPUT->notification(get_string('userdeleted'));
    }
    echo $OUTPUT->footer();
    die;
}

$currentuser = ($user->id == $USER->id);
$context = $usercontext = context_user::instance($userid, MUST_EXIST);

if (!user_can_view_profile($user, null, $context)) {

    // Course managers can be browsed at site level. If not forceloginforprofiles, allow access (bug #4366).
    $struser = get_string('user');
    $PAGE->set_context(context_system::instance());
    $PAGE->set_title("$SITE->shortname: $struser");  // Do not leak the name.
    $PAGE->set_heading($struser);
    $PAGE->set_url('/user/profile.php', array('id' => $userid));
    $PAGE->navbar->add($struser);
    echo $OUTPUT->header();
    echo $OUTPUT->notification(get_string('usernotavailable', 'error'));
    echo $OUTPUT->footer();
    exit;
}

// Get the profile page.  Should always return something unless the database is broken.
if (!$currentpage = my_get_page($userid, MY_PAGE_PUBLIC)) {
    print_error('mymoodlesetup');
}

$PAGE->set_context($context);
$PAGE->set_pagelayout('mypublic');
$PAGE->set_pagetype('user-profile');

// Set up block editing capabilities.
if (isguestuser()) {     // Guests can never edit their profile.
    $USER->editing = $edit = 0;  // Just in case.
    $PAGE->set_blocks_editing_capability('moodle/my:configsyspages');  // unlikely :).
} else {
    if ($currentuser) {
        $PAGE->set_blocks_editing_capability('moodle/user:manageownblocks');
    } else {
        $PAGE->set_blocks_editing_capability('moodle/user:manageblocks');
    }
}

// Start setting up the page.
$strpublicprofile = get_string('publicprofile');

$PAGE->blocks->add_region('content');
$PAGE->set_subpage($currentpage->id);
$PAGE->set_title(fullname($user).": $strpublicprofile");
$PAGE->set_heading(fullname($user));

if (!$currentuser) {
    $PAGE->navigation->extend_for_user($user);
    if ($node = $PAGE->settingsnav->get('userviewingsettings'.$user->id)) {
        $node->forceopen = true;
    }
} else if ($node = $PAGE->settingsnav->get('dashboard', navigation_node::TYPE_CONTAINER)) {
    $node->forceopen = true;
}
if ($node = $PAGE->settingsnav->get('root')) {
    $node->forceopen = false;
}


// Toggle the editing state and switches.
if ($PAGE->user_allowed_editing()) {
    if ($reset !== null) {
        if (!is_null($userid)) {
            if (!$currentpage = my_reset_page($userid, MY_PAGE_PUBLIC, 'user-profile')) {
                print_error('reseterror', 'my');
            }
            redirect(new moodle_url('/user/profile.php', array('id' => $userid)));
        }
    } else if ($edit !== null) {             // Editing state was specified.
        $USER->editing = $edit;       // Change editing state.
    } else {                          // Editing state is in session.
        if ($currentpage->userid) {   // It's a page we can edit, so load from session.
            if (!empty($USER->editing)) {
                $edit = 1;
            } else {
                $edit = 0;
            }
        } else {
            // For the page to display properly with the user context header the page blocks need to
            // be copied over to the user context.
            if (!$currentpage = my_copy_page($userid, MY_PAGE_PUBLIC, 'user-profile')) {
                print_error('mymoodlesetup');
            }
            $PAGE->set_context($usercontext);
            $PAGE->set_subpage($currentpage->id);
            // It's a system page and they are not allowed to edit system pages.
            $USER->editing = $edit = 0;          // Disable editing completely, just to be safe.
        }
    }

    // Add button for editing page.
    $params = array('edit' => !$edit, 'id' => $userid);

    $resetbutton = '';
    $resetstring = get_string('resetpage', 'my');
    $reseturl = new moodle_url("$CFG->wwwroot/user/profile.php", array('edit' => 1, 'reset' => 1, 'id' => $userid));

    if (!$currentpage->userid) {
        // Viewing a system page -- let the user customise it.
        $editstring = get_string('updatemymoodleon');
        $params['edit'] = 1;
    } else if (empty($edit)) {
        $editstring = get_string('updatemymoodleon');
        $resetbutton = $OUTPUT->single_button($reseturl, $resetstring);
    } else {
        $editstring = get_string('updatemymoodleoff');
        $resetbutton = $OUTPUT->single_button($reseturl, $resetstring);
    }

    $url = new moodle_url("$CFG->wwwroot/user/profile.php", $params);
    $button = $OUTPUT->single_button($url, $editstring);
    $PAGE->set_button($resetbutton . $button);

} else {
    $USER->editing = $edit = 0;
}

// Trigger a user profile viewed event.
profile_view($user, $usercontext);

// TODO WORK OUT WHERE THE NAV BAR IS!
echo $OUTPUT->header();
echo '<div class="userprofile">';

if ($user->description && !isset($hiddenfields['description'])) {
    echo '<div class="description">';
    if (!empty($CFG->profilesforenrolledusersonly) && !$currentuser &&
        !$DB->record_exists('role_assignments', array('userid' => $user->id))) {
        echo get_string('profilenotshown', 'moodle');
    } else {
        $user->description = file_rewrite_pluginfile_urls($user->description, 'pluginfile.php', $usercontext->id, 'user',
                                                          'profile', null);
        $options = array('overflowdiv' => true);
        echo format_text($user->description, $user->descriptionformat, $options);
    }
}
echo '</div>';


// Print all the little details in a list.
echo html_writer::start_tag('dl', array('class' => 'list'));
if (!isset($hiddenfields['country']) && $user->country) {
    echo html_writer::tag('dt', get_string('country'));
    echo html_writer::tag('dd', get_string($user->country, 'countries'));
}

if (!isset($hiddenfields['city']) && $user->city) {
    echo html_writer::tag('dt', get_string('city'));
    echo html_writer::tag('dd', $user->city);
}

if (isset($identityfields['address']) && $user->address) {
    echo html_writer::tag('dt', get_string('address'));
    echo html_writer::tag('dd', $user->address);
}

if (isset($identityfields['phone1']) && $user->phone1) {
    echo html_writer::tag('dt', get_string('phone'));
    echo html_writer::tag('dd', $user->phone1);
}

if (isset($identityfields['phone2']) && $user->phone2) {
    echo html_writer::tag('dt', get_string('phone2'));
    echo html_writer::tag('dd', $user->phone2);
}

if (isset($identityfields['institution']) && $user->institution) {
    echo html_writer::tag('dt', get_string('institution'));
    echo html_writer::tag('dd', $user->institution);
}

if (isset($identityfields['department']) && $user->department) {
    echo html_writer::tag('dt', get_string('department'));
    echo html_writer::tag('dd', $user->department);
}

if (isset($identityfields['idnumber']) && $user->idnumber) {
    echo html_writer::tag('dt', get_string('idnumber'));
    echo html_writer::tag('dd', $user->idnumber);
}

if (isset($identityfields['email']) and ($currentuser
  or $user->maildisplay == 1
  or has_capability('moodle/course:useremail', $context)
  or ($user->maildisplay == 2 and enrol_sharing_course($user, $USER)))) {
    echo html_writer::tag('dt', get_string('email'));
    echo html_writer::tag('dd', obfuscate_mailto($user->email, ''));
}


$ebsid = explode( '@', $user->username );
echo html_writer::tag( 'dt', 'Leap' );
echo html_writer::tag( 'dd', html_writer::link('https://leap.southdevon.ac.uk/people/' . $ebsid[0] , 'Leap profile for ' . fullname($user), array( 'target' => '_blank' ) ) );


if ($user->url && !isset($hiddenfields['webpage'])) {
    $url = $user->url;
    if (strpos($user->url, '://') === false) {
        $url = 'http://'. $url;
    }
    $webpageurl = new moodle_url($url);
    echo html_writer::tag('dt', get_string('webpage'));
    echo html_writer::tag('dd', html_writer::link($webpageurl, s($user->url)));
}

if ($user->icq && !isset($hiddenfields['icqnumber'])) {
    $imurl = new moodle_url('http://web.icq.com/wwp', array('uin' => $user->icq) );
    $iconurl = new moodle_url('http://web.icq.com/whitepages/online', array('icq' => $user->icq, 'img' => '5'));
    $statusicon = html_writer::tag('img', '', array('src' => $iconurl, 'class' => 'icon icon-post', 'alt' => get_string('status')));
    echo html_writer::tag('dt', get_string('icqnumber'));
    echo html_writer::tag('dd', html_writer::link($imurl, s($user->icq) . $statusicon));
}

if ($user->skype && !isset($hiddenfields['skypeid'])) {
    $imurl = 'skype:'.urlencode($user->skype).'?call';
    $iconurl = new moodle_url('http://mystatus.skype.com/smallicon/'.urlencode($user->skype));
    if (is_https()) {
        // Bad luck, skype devs are lazy to set up SSL on their servers - see MDL-37233.
        $statusicon = '';
    } else {
        $statusicon = html_writer::empty_tag('img',
            array('src' => $iconurl, 'class' => 'icon icon-post', 'alt' => get_string('status')));
    }
    echo '</div>';
}

echo $OUTPUT->custom_block_region('content');

// Render custom blocks.
$renderer = $PAGE->get_renderer('core_user', 'myprofile');
$tree = core_user\output\myprofile\manager::build_tree($user, $currentuser);
echo $renderer->render($tree);

echo '</div>';  // Userprofile class.

echo $OUTPUT->footer();
