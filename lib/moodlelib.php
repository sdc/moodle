<?PHP // $Id$

///////////////////////////////////////////////////////////////////////////
//                                                                       //
// moodlelib.php                                                         //
//                                                                       //
// Main library file of miscellaneous general-purpose Moodle functions   //
//                                                                       //
// Other main libraries:                                                 //
//                                                                       //
//   weblib.php      - functions that produce web output                 //
//   datalib.php     - functions that access the database                //
//                                                                       //
///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.org                                            //
//                                                                       //
// Copyright (C) 1999-2004  Martin Dougiamas  http://dougiamas.com       //
//                                                                       //
// This program is free software; you can redistribute it and/or modify  //
// it under the terms of the GNU General Public License as published by  //
// the Free Software Foundation; either version 2 of the License, or     //
// (at your option) any later version.                                   //
//                                                                       //
// This program is distributed in the hope that it will be useful,       //
// but WITHOUT ANY WARRANTY; without even the implied warranty of        //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         //
// GNU General Public License for more details:                          //
//                                                                       //
//          http://www.gnu.org/copyleft/gpl.html                         //
//                                                                       //
///////////////////////////////////////////////////////////////////////////

/// CONSTANTS /////////////////////////////////////////////////////////////

define('MOODLE_INTERNAL', true);  // Used by some scripts to check they
                                  // are being called by Moodle

define('NOGROUPS', 0);
define('SEPARATEGROUPS', 1);
define('VISIBLEGROUPS', 2);

define('PARAM_RAW',     0x0000);
define('PARAM_CLEAN',   0x0001);
define('PARAM_INT',     0x0002);
define('PARAM_INTEGER', 0x0002);  // Alias for PARAM_INT
define('PARAM_ALPHA',   0x0004);
define('PARAM_ACTION',  0x0004);  // Alias for PARAM_ALPHA
define('PARAM_FORMAT',  0x0004);  // Alias for PARAM_ALPHA
define('PARAM_NOTAGS',  0x0008);
define('PARAM_FILE',    0x0010);
define('PARAM_PATH',    0x0020);
define('PARAM_HOST',    0x0040);  // FQDN or IPv4 dotted quad
define('PARAM_URL',     0x0080);  
define('PARAM_LOCALURL',0x0180);  // NOT orthogonal to the others! Implies PARAM_URL!
define('PARAM_CLEANFILE',0x0200);
define('PARAM_ALPHANUM',0x0400);  //numbers or letters only
define('PARAM_BOOL',    0x0800);  //convert to value 1 or 0 using empty()

/// PARAMETER HANDLING ////////////////////////////////////////////////////

function required_param($varname, $options=PARAM_CLEAN) {
/// This function will replace require_variable over time
/// It returns a value for a given variable name.

    if (isset($_POST[$varname])) {       // POST has precedence
        $param = $_POST[$varname];
    } else if (isset($_GET[$varname])) {
        $param = $_GET[$varname];
    } else {
        error('A required parameter ('.$varname.') was missing');
    }

    return clean_param($param, $options);
}

function optional_param($varname, $default=NULL, $options=PARAM_CLEAN) {
/// This function will replace both of the above two functions over time.
/// It returns a value for a given variable name.

    if (isset($_POST[$varname])) {       // POST has precedence
        $param = $_POST[$varname];
    } else if (isset($_GET[$varname])) {
        $param = $_GET[$varname];
    } else {
        return $default;
    }

    return clean_param($param, $options);
}

function clean_param($param, $options) {
/// Given a parameter and a bitfield of options, this function
/// will clean it up and give it the required type, etc.

    global $CFG;

    if (!$options) {
        return $param;                   // Return raw value
    }

    if ((string)$param == (string)(int)$param) {  // It's just an integer
        return $param;
    }

    if ($options & PARAM_CLEAN) {
        $param = clean_text($param);     // Sweep for scripts, etc
    }

    if ($options & PARAM_INT) {
        $param = (int)$param;            // Convert to integer
    }

    if ($options & PARAM_ALPHA) {        // Remove everything not a-zA-Z, coverts to lowercase
        $param = eregi_replace('[^a-zA-Z]', '', $param);
    }

    if ($options & PARAM_ALPHANUM) {     // Remove everything not a-zA-Z0-9
        $param = eregi_replace('[^A-Za-z0-9]', '', $param);
    }

    if ($options & PARAM_BOOL) {         // Convert to 1 or 0
        $param = empty($param) ? 0 : 1;
    }

    if ($options & PARAM_NOTAGS) {       // Strip all tags completely
        $param = strip_tags($param);
    }

    if ($options & PARAM_CLEANFILE) {    // allow only safe characters
        $param = clean_filename($param);
    }

    if ($options & PARAM_FILE) {         // Strip all suspicious characters from filename
        $param = ereg_replace('[[:cntrl:]]|[<>"`\|\':\\/]', '', $param);
        $param = ereg_replace('\.\.+', '', $param);
    }

    if ($options & PARAM_PATH) {         // Strip all suspicious characters from file path
        $param = str_replace('\\\'', '\'', $param);
        $param = str_replace('\\"', '"', $param);
        $param = str_replace('\\', '/', $param);
        $param = ereg_replace('[[:cntrl:]]|[<>"`\|\':]', '', $param);
        $param = ereg_replace('\.\.+', '', $param);
        $param = ereg_replace('//+', '/', $param);
    }

    if ($options & PARAM_HOST) {         // allow FQDN or IPv4 dotted quad
        preg_replace('/[^\.\d\w-]/','', $param ); // only allowed chars 
	    // match ipv4 dotted quad
        if (preg_match('/(\d{1,3})\.(\d{1,3})\.(\d{1,3})\.(\d{1,3})/',$param, $match)){
            // confirm values are ok
            if ( $match[0] > 255
                 || $match[1] > 255
                 || $match[3] > 255 
                 || $match[4] > 255 ) {
                // hmmm, what kind of dotted quad is this?
                $param = '';
            }
        } elseif ( preg_match('/^[\w\d\.-]+$/', $param) // dots, hyphens, numbers
                   && !preg_match('/^[\.-]/',  $param) // no leading dots/hyphens
                   && !preg_match('/[\.-]$/',  $param) // no trailing dots/hyphens
                   ) {
            // all is ok - $param is respected
        } else {
            // all is not ok...
            $param='';               
        } 
    }

    if ($options & PARAM_URL) { // allow safe ftp, http, mailto urls

        include_once($CFG->dirroot . '/lib/validateurlsyntax.php');

        //
        // Parameters to validateurlsyntax()
        //
        // s? scheme is optional
        //   H? http optional
        //   S? https optional
        //   F? ftp   optional
        //   E? mailto optional
        // u- user section not allowed
        //   P- password not allowed
        // a? address optional
        //   I? Numeric IP address optional (can use IP or domain)
        //   p-  port not allowed -- restrict to default port
        // f? "file" path section optional
        //   q? query section optional
        //   r? fragment (anchor) optional
        //
        if (!empty($param) && validateUrlSyntax($param, 's?H?S?F?E?u-P-a?I?p-f?q?r?')) {
            // all is ok, param is respected
        } else {
            $param =''; // not really ok
        }
        $options ^= PARAM_URL; // Turn off the URL bit so that simple PARAM_URLs don't test true for PARAM_LOCALURL
    }

    if ($options & PARAM_LOCALURL) {
        // assume we passed the PARAM_URL test...
        // allow http absolute, root relative and relative URLs within wwwroot
        if (!empty($param)) {
            if (preg_match(':^/:', $param)) { 
                // root-relative, ok!
            } elseif (preg_match('/^'.preg_quote($CFG->wwwroot, '/').'/i',$param)) {
                // absolute, and matches our wwwroot
            } else { 
                // relative - let's make sure there are no tricks
                if (validateUrlSyntax($param, 's-u-P-a-p-f+q?r?')) {
                    // looks ok.
                } else {
                    $param = '';
                }                
            }
        }
    }

    return $param;
}

function confirm_sesskey($sesskey=NULL) {
/// For security purposes, this function will check that the currently
/// given sesskey (passed as a parameter to the script or this function)
/// matches that of the current user.
    global $USER;

    if (empty($sesskey)) {
        $sesskey = required_param('sesskey');  // Check script parameters
    }

    if (!isset($USER->sesskey)) {
        return false;
    }

    return ($USER->sesskey == $sesskey);
}

function require_variable($var) {
/// Variable must be present
/// This old function is retained for backward compatibility
    if (! isset($var)) {
        error("A required parameter was missing");
    }
}

function optional_variable(&$var, $default=0) {
/// Variable may be present, if not then set a default
/// This old function is retained for backward compatibility
    if (! isset($var)) {
        $var = $default;
    }
}

function set_config($name, $value) {
/// No need for get_config because they are usually always available in $CFG

    global $CFG;

    $CFG->$name = $value;  // So it's defined for this invocation at least

    if (get_field("config", "name", "name", $name)) {
        return set_field("config", "value", $value, "name", $name);
    } else {
        $config->name = $name;
        $config->value = $value;
        return insert_record("config", $config);
    }
}


function reload_user_preferences() {
/// Refresh current USER with all their current preferences

    global $USER;

    unset($USER->preference);

    if ($preferences = get_records('user_preferences', 'userid', $USER->id)) {
        foreach ($preferences as $preference) {
            $USER->preference[$preference->name] = $preference->value;
        }
    }
}

function set_user_preference($name, $value) {
/// Sets a preference for the current user

    global $USER;

    if (empty($name)) {
        return false;
    }

    if ($preference = get_record('user_preferences', 'userid', $USER->id, 'name', $name)) {
        if (set_field("user_preferences", "value", $value, "id", $preference->id)) {
            $USER->preference[$name] = $value;
            return true;
        } else {
            return false;
        }

    } else {
        $preference->userid = $USER->id;
        $preference->name   = $name;
        $preference->value  = (string)$value;
        if (insert_record('user_preferences', $preference)) {
            $USER->preference[$name] = $value;
            return true;
        } else {
            return false;
        }
    }
}

function set_user_preferences($prefarray) {
/// Sets a whole array of preferences for the current user

    if (!is_array($prefarray) or empty($prefarray)) {
        return false;
    }

    $return = true;
    foreach ($prefarray as $name => $value) {
        // The order is important; if the test for return is done first,
        // then if one function call fails all the remaining ones will
        // be "optimized away"
        $return = set_user_preference($name, $value) and $return;
    }
    return $return;
}

function get_user_preferences($name=NULL, $default=NULL) {
/// Without arguments, returns all the current user preferences
/// as an array.  If a name is specified, then this function
/// attempts to return that particular preference value.  If
/// none is found, then the optional value $default is returned,
/// otherwise NULL.

    global $USER;

    if (empty($USER->preference)) {
        return $default;              // Default value (or NULL)
    }
    if (empty($name)) {
        return $USER->preference;     // Whole array
    }
    if (!isset($USER->preference[$name])) {
        return $default;              // Default value (or NULL)
    }
    return $USER->preference[$name];  // The single value
}


/// FUNCTIONS FOR HANDLING TIME ////////////////////////////////////////////

function make_timestamp($year, $month=1, $day=1, $hour=0, $minute=0, $second=0, $timezone=99) {
/// Given date parts in user time, produce a GMT timestamp

    $timezone = get_user_timezone($timezone);

    if (abs($timezone) > 13) {
        return mktime((int)$hour,(int)$minute,(int)$second,(int)$month,(int)$day,(int)$year);
    } else {
        $time = gmmktime((int)$hour,(int)$minute,(int)$second,(int)$month,(int)$day,(int)$year);
        return usertime($time, $timezone);  // This is GMT
    }
}

function format_time($totalsecs, $str=NULL) {
/// Given an amount of time in seconds, returns string
/// formatted nicely as months, days, hours etc as needed

    $totalsecs = abs($totalsecs);

    if (!$str) {  // Create the str structure the slow way
        $str->day   = get_string("day");
        $str->days  = get_string("days");
        $str->hour  = get_string("hour");
        $str->hours = get_string("hours");
        $str->min   = get_string("min");
        $str->mins  = get_string("mins");
        $str->sec   = get_string("sec");
        $str->secs  = get_string("secs");
    }

    $days      = floor($totalsecs/86400);
    $remainder = $totalsecs - ($days*86400);
    $hours     = floor($remainder/3600);
    $remainder = $remainder - ($hours*3600);
    $mins      = floor($remainder/60);
    $secs      = $remainder - ($mins*60);

    $ss = ($secs == 1)  ? $str->sec  : $str->secs;
    $sm = ($mins == 1)  ? $str->min  : $str->mins;
    $sh = ($hours == 1) ? $str->hour : $str->hours;
    $sd = ($days == 1)  ? $str->day  : $str->days;

    $odays = "";
    $ohours = "";
    $omins = "";
    $osecs = "";

    if ($days)  $odays  = "$days $sd";
    if ($hours) $ohours = "$hours $sh";
    if ($mins)  $omins  = "$mins $sm";
    if ($secs)  $osecs  = "$secs $ss";

    if ($days)  return "$odays $ohours";
    if ($hours) return "$ohours $omins";
    if ($mins)  return "$omins $osecs";
    if ($secs)  return "$osecs";
    return get_string("now");
}

function userdate($date, $format="", $timezone=99, $fixday = true) {
/// Returns a formatted string that represents a date in user time
/// WARNING: note that the format is for strftime(), not date().
/// Because of a bug in most Windows time libraries, we can't use
/// the nicer %e, so we have to use %d which has leading zeroes.
/// A lot of the fuss below is just getting rid of these leading
/// zeroes as efficiently as possible.
///
/// If parammeter fixday = true (default), then take off leading
/// zero from %d, else mantain it.

    if ($format == "") {
        $format = get_string("strftimedaydatetime");
    }

    $formatnoday = str_replace("%d", "DD", $format);
    if ($fixday) {
        $fixday = ($formatnoday != $format);
    }

    $timezone = get_user_timezone($timezone);

    if (abs($timezone) > 13) {
        if ($fixday) {
            $datestring = strftime($formatnoday, $date);
            $daystring  = str_replace(" 0", "", strftime(" %d", $date));
            $datestring = str_replace("DD", $daystring, $datestring);
        } else {
            $datestring = strftime($format, $date);
        }
    } else {
        $date = $date + (int)($timezone * 3600);
        if ($fixday) {
            $datestring = gmstrftime($formatnoday, $date);
            $daystring  = str_replace(" 0", "", gmstrftime(" %d", $date));
            $datestring = str_replace("DD", $daystring, $datestring);
        } else {
            $datestring = gmstrftime($format, $date);
        }
    }

    return $datestring;
}

function usergetdate($date, $timezone=99) {
/// Given a $date timestamp in GMT, returns an array
/// that represents the date in user time

    $timezone = get_user_timezone($timezone);

    if (abs($timezone) > 13) {
        return getdate($date);
    }
    //There is no gmgetdate so I have to fake it...
    $date = $date + (int)($timezone * 3600);
    $getdate["seconds"] = gmstrftime("%S", $date);
    $getdate["minutes"] = gmstrftime("%M", $date);
    $getdate["hours"]   = gmstrftime("%H", $date);
    $getdate["mday"]    = gmstrftime("%d", $date);
    $getdate["wday"]    = gmstrftime("%u", $date);
    $getdate["mon"]     = gmstrftime("%m", $date);
    $getdate["year"]    = gmstrftime("%Y", $date);
    $getdate["yday"]    = gmstrftime("%j", $date);
    $getdate["weekday"] = gmstrftime("%A", $date);
    $getdate["month"]   = gmstrftime("%B", $date);
    return $getdate;
}

function usertime($date, $timezone=99) {
/// Given a GMT timestamp (seconds since epoch), offsets it by
/// the timezone.  eg 3pm in India is 3pm GMT - 7 * 3600 seconds

    $timezone = get_user_timezone($timezone);
    if (abs($timezone) > 13) {
        return $date;
    }
    return $date - (int)($timezone * 3600);
}

function usergetmidnight($date, $timezone=99) {
/// Given a time, return the GMT timestamp of the most recent midnight
/// for the current user.

    $timezone = get_user_timezone($timezone);
    $userdate = usergetdate($date, $timezone);

    if (abs($timezone) > 13) {
        return mktime(0, 0, 0, $userdate["mon"], $userdate["mday"], $userdate["year"]);
    }

    $timemidnight = gmmktime (0, 0, 0, $userdate["mon"], $userdate["mday"], $userdate["year"]);
    return usertime($timemidnight, $timezone); // Time of midnight of this user's day, in GMT

}

function usertimezone($timezone=99) {
/// Returns a string that prints the user's timezone

    $timezone = get_user_timezone($timezone);

    if (abs($timezone) > 13) {
        return "server time";
    }
    if (abs($timezone) < 0.5) {
        return "GMT";
    }
    if ($timezone > 0) {
        return "GMT+$timezone";
    } else {
        return "GMT$timezone";
    }
}

function get_user_timezone($tz = 99) {
// Returns a float which represents the user's timezone difference from GMT in hours
// Checks various settings and picks the most dominant of those which have a value

    // Variables declared explicitly global here so that if we add
    // something later we won't forget to global it...
    $timezones = array(
        isset($GLOBALS['USER']->timezone) ? $GLOBALS['USER']->timezone : 99,
        isset($GLOBALS['CFG']->timezone) ? $GLOBALS['CFG']->timezone : 99,
        );
    while($tz == 99 && $next = each($timezones)) {
        $tz = (float)$next['value'];
    }

    return $tz;
}

/// USER AUTHENTICATION AND LOGIN ////////////////////////////////////////

// Makes sure that $USER->sesskey exists, if $USER itself exists. It sets a new sesskey
// if one does not already exist, but does not overwrite existing sesskeys. Returns the
// sesskey string if $USER exists, or boolean false if not.
function set_user_sesskey() {
    global $USER;

    if(!isset($USER)) {
        return false;
    }

    if (empty($USER->sesskey)) {
        $USER->sesskey = random_string(10);
    }

    return $USER->sesskey;
}

function require_login($courseid=0, $autologinguest=true) {
/// This function checks that the current user is logged in, and optionally
/// whether they are "logged in" or allowed to be in a particular course.
/// If not, then it redirects them to the site login or course enrolment.
/// $autologinguest determines whether visitors should automatically be
/// logged in as guests provide $CFG->autologinguests is set to 1

    global $CFG, $SESSION, $USER, $FULLME, $MoodleSession;

    // First check that the user is logged in to the site.
    if (! (isset($USER->loggedin) and $USER->confirmed and ($USER->site == $CFG->wwwroot)) ) { // They're not
        $SESSION->wantsurl = $FULLME;
        if (!empty($_SERVER["HTTP_REFERER"])) {
            $SESSION->fromurl  = $_SERVER["HTTP_REFERER"];
        }
        $USER = NULL;
        if ($autologinguest and $CFG->autologinguests and $courseid and get_field('course','guest','id',$courseid)) {
            $loginguest = '?loginguest=true';
        } else {
            $loginguest = '';
        }
        if (empty($CFG->loginhttps)) {
            redirect("$CFG->wwwroot/login/index.php$loginguest");
        } else {
            $wwwroot = str_replace('http','https',$CFG->wwwroot);
            redirect("$wwwroot/login/index.php$loginguest");
        }
        die;
    }

    // Check that the user account is properly set up
    if (user_not_fully_set_up($USER)) {
        $site = get_site();
        redirect("$CFG->wwwroot/user/edit.php?id=$USER->id&course=$site->id");
        die;
    }

    // Make sure the USER has a sesskey set up.  Used for checking script parameters.
    set_user_sesskey();

    // Next, check if the user can be in a particular course
    if ($courseid) {
        if ($courseid == SITEID) {   
            return;   // Anyone can be in the site course
        }
        if (!empty($USER->student[$courseid]) or !empty($USER->teacher[$courseid]) or !empty($USER->admin)) {
            if (isset($USER->realuser)) {   // Make sure the REAL person can also access this course
                if (!isteacher($courseid, $USER->realuser)) {
                    print_header();
                    notice(get_string("studentnotallowed", "", fullname($USER, true)), "$CFG->wwwroot/");
                }

            } else {  // just update their last login time
                update_user_in_db();
            }
            return;   // user is a member of this course.
        }
        if (! $course = get_record("course", "id", $courseid)) {
            error("That course doesn't exist");
        }
        if (!$course->visible) {
            print_header();
            notice(get_string("studentnotallowed", "", fullname($USER, true)), "$CFG->wwwroot/");
        }
        if ($USER->username == "guest") {
            switch ($course->guest) {
                case 0: // Guests not allowed
                    print_header();
                    notice(get_string("guestsnotallowed", "", $course->fullname));
                    break;
                case 1: // Guests allowed
                    update_user_in_db();
                    return;
                case 2: // Guests allowed with key (drop through)
                    break;
            }
        }

        // Currently not enrolled in the course, so see if they want to enrol
        $SESSION->wantsurl = $FULLME;
        redirect("$CFG->wwwroot/course/enrol.php?id=$courseid");
        die;
    }
}

function require_course_login($course, $autologinguest=true) {
// This is a weaker version of require_login which only requires login
// when called from within a course rather than the site page, unless
// the forcelogin option is turned on.
    global $CFG;
    if ($CFG->forcelogin) {
      require_login();
    }
    if ($course->category) {
      require_login($course->id, $autologinguest);
    }
}

function update_user_login_times() {
    global $USER;

    $USER->lastlogin = $user->lastlogin = $USER->currentlogin;
    $USER->currentlogin = $user->currentlogin = time();

    $user->id = $USER->id;

    return update_record("user", $user);
}

function user_not_fully_set_up($user) {
    return ($user->username != "guest" and (empty($user->firstname) or empty($user->lastname) or empty($user->email)));
}

function update_login_count() {
/// Keeps track of login attempts

    global $SESSION;

    $max_logins = 10;

    if (empty($SESSION->logincount)) {
        $SESSION->logincount = 1;
    } else {
        $SESSION->logincount++;
    }

    if ($SESSION->logincount > $max_logins) {
        unset($SESSION->wantsurl);
        error(get_string("errortoomanylogins"));
    }
}

function reset_login_count() {
/// Resets login attempts
    global $SESSION;

    $SESSION->logincount = 0;
}

function check_for_restricted_user($username=NULL, $redirect="") {
    global $CFG, $USER;

    if (!$username) {
        if (!empty($USER->username)) {
            $username = $USER->username;
        } else {
            return false;
        }
    }

    if (!empty($CFG->restrictusers)) {
        $names = explode(',', $CFG->restrictusers);
        if (in_array($username, $names)) {
            error(get_string("restricteduser", "error", fullname($USER)), $redirect);
        }
    }
}

function isadmin($userid=0) {
/// Is the user an admin?
    global $USER;
    static $admins = array();
    static $nonadmins = array();

    if (!$userid){
        if (empty($USER->id)) {
            return false;
        }
        $userid = $USER->id;
    }

    if (in_array($userid, $admins)) {
        return true;
    } else if (in_array($userid, $nonadmins)) {
        return false;
    } else if (record_exists("user_admins", "userid", $userid)){
        $admins[] = $userid;
        return true;
    } else {
        $nonadmins[] = $userid;
        return false;
    }
}

function isteacher($courseid=0, $userid=0, $includeadmin=true) {
/// Is the user a teacher or admin?
    global $USER, $CFG;

    if ($includeadmin and isadmin($userid)) {  // admins can do anything the teacher can
        return true;
    }

    if (empty($courseid)) {
        if (isadmin() or $CFG->debug > 7) {
            notify('Coding error: isteacher() should not be used without a valid course id as argument.  Please notify a developer.');
        }
        return isteacherinanycourse($userid, $includeadmin);
    }

    if (!$userid) {
        if ($courseid) {
            return !empty($USER->teacher[$courseid]);
        }
        if (!isset($USER->id)) {
            return false;
        }
        $userid = $USER->id;
    }

    return record_exists("user_teachers", "userid", $userid, "course", $courseid);
}

function isteacherinanycourse($userid = 0, $includeadmin = true) {
    global $USER;

    if(empty($userid)) {
        if(empty($USER) || empty($USER->id)) {
            return false;
        }
        $userid = $USER->id;
    }

    if (isadmin($userid) && $includeadmin) {  // admins can do anything
        return true;
    }

    return record_exists('user_teachers', 'userid', $userid);
}

function isteacheredit($courseid, $userid=0) {
/// Is the user allowed to edit this course?
    global $USER;

    if (isadmin($userid)) {  // admins can do anything
        return true;
    }

    if (!$userid) {
        return !empty($USER->teacheredit[$courseid]);
    }

    return get_field("user_teachers", "editall", "userid", $userid, "course", $courseid);
}

function iscreator ($userid=0) {
/// Can user create new courses?
    global $USER;
    if (empty($USER->id)) {
        return false;
    }
    if (isadmin($userid)) {  // admins can do anything
        return true;
    }
    if (empty($userid)) {
        return record_exists("user_coursecreators", "userid", $USER->id);
    }

    return record_exists("user_coursecreators", "userid", $userid);
}

function isstudent($courseid, $userid=0) {
/// Is the user a student in this course?
/// If course is site, is the user a confirmed user on the site?
    global $USER, $CFG;

    if (empty($USER->id) and !$userid) {
        return false;
    }

    if ($courseid == SITEID) {
        if (!$userid) {
            $userid = $USER->id;
        }
        if (isguest($userid)) {
            return false;
        }
        // a site teacher can never be a site student
        if (isteacher($courseid, $userid)) {
            return false;
        }
        if ($CFG->allusersaresitestudents) {
            return record_exists('user', 'id', $userid);
        } else {
            return (record_exists('user_students', 'userid', $userid)
                     or record_exists('user_teachers', 'userid', $userid));
        }
    }  

    if (!$userid) {
        return !empty($USER->student[$courseid]);
    }

  //  $timenow = time();   // todo:  add time check below

    return record_exists("user_students", "userid", $userid, "course", $courseid);
}

function isguest($userid=0) {
/// Is the user a guest?
    global $USER;

    if (!$userid) {
        if (empty($USER->username)) {
            return false;
        }
        return ($USER->username == "guest");
    }

    return record_exists("user", "id", $userid, "username", "guest");
}


function isediting($courseid, $user=NULL) {
/// Is the current user in editing mode?
    global $USER;
    if (!$user){
        $user = $USER;
    }
    if (empty($user->editing)) {
        return false;
    }
    return ($user->editing and isteacher($courseid, $user->id));
}

function ismoving($courseid) {
/// Is the current user currently moving an activity?
    global $USER;

    if (!empty($USER->activitycopy)) {
        return ($USER->activitycopycourse == $courseid);
    }
    return false;
}

function fullname($user, $override=false) {
/// Given an object containing firstname and lastname
/// values, this function returns a string with the
/// full name of the person.
/// The result may depend on system settings
/// or language.  'override' will force both names
/// to be used even if system settings specify one.

    global $CFG, $SESSION;

    if (!isset($user->firstname) and !isset($user->lastname)) {
        return '';
    }

    if (!empty($SESSION->fullnamedisplay)) {
        $CFG->fullnamedisplay = $SESSION->fullnamedisplay;
    }

    if ($CFG->fullnamedisplay == 'firstname lastname') {
        return "$user->firstname $user->lastname";

    } else if ($CFG->fullnamedisplay == 'lastname firstname') {
        return "$user->lastname $user->firstname";

    } else if ($CFG->fullnamedisplay == 'firstname') {
        if ($override) {
            return get_string('fullnamedisplay', '', $user);
        } else {
            return $user->firstname;
        }
    }

    return get_string('fullnamedisplay', '', $user);
}


function set_moodle_cookie($thing) {
/// Sets a moodle cookie with an encrypted string
    global $CFG;

    $cookiename = 'MOODLEID_'.$CFG->sessioncookie;

    $days = 60;
    $seconds = 60*60*24*$days;

    setCookie($cookiename, "", time() - 3600, "/");
    setCookie($cookiename, rc4encrypt($thing), time()+$seconds, "/");
}


function get_moodle_cookie() {
/// Gets a moodle cookie with an encrypted string
    global $CFG;

    $cookiename = 'MOODLEID_'.$CFG->sessioncookie;

    if (empty($_COOKIE[$cookiename])) {
        return "";
    } else {
        return rc4decrypt($_COOKIE[$cookiename]);
    }
}

function is_internal_auth($auth='') {
/// Returns true if an internal authentication method is being used.
/// if method not specified then, global default is assumed

    global $CFG;

    $method = $CFG->auth;

    if (!empty($auth)) {
        $method = $auth;
    }

    return ($method == "email" || $method == "none" || $method == "manual");
}

function create_user_record($username, $password, $auth='') {
/// Creates a bare-bones user record
    global $REMOTE_ADDR, $CFG;

    //just in case check text case
    $username = trim(moodle_strtolower($username));

    if (function_exists('auth_get_userinfo')) {
        if ($newinfo = auth_get_userinfo($username)) {
            $newinfo = truncate_userinfo($newinfo);
            foreach ($newinfo as $key => $value){
                $newuser->$key = addslashes(stripslashes($value)); // Just in case
            }
        }
    }

    if (!empty($newuser->email)) {
        if (email_is_not_allowed($newuser->email)) {
            unset($newuser->email);
        }
    }

    $newuser->auth = (empty($auth)) ? $CFG->auth : $auth;
    $newuser->username = $username;
    $newuser->password = md5($password);
    $newuser->lang = $CFG->lang;
    $newuser->confirmed = 1;
    $newuser->lastIP = $REMOTE_ADDR;
    $newuser->timemodified = time();

    if (insert_record("user", $newuser)) {
        return get_user_info_from_db("username", $username);
    }
    return false;
}


function truncate_userinfo($info) {
/// will truncate userinfo as it comes from auth_get_userinfo (from external auth)
/// which may have large fields

    // define the limits
    $limit = array(
                    'username'    => 100,
                    'idnumber'    =>  12,
                    'firstname'   =>  20,
                    'lastname'    =>  20,
                    'email'       => 100,
                    'icq'         =>  15,
                    'phone1'      =>  20,
                    'phone2'      =>  20,
                    'institution' =>  40,
                    'department'  =>  30,
                    'address'     =>  70,
                    'city'        =>  20,
                    'country'     =>   2,
                    'url'         => 255,
                    );
    
    // apply where needed
    foreach (array_keys($info) as $key) {
        if (!empty($limit[$key])) {
            $info[$key] = substr(trim($info[$key]),0, $limit[$key]);
        } 
    }
    
    return $info;
}

function guest_user() {
    global $CFG;

    if ($newuser = get_record("user", "username", "guest")) {
        $newuser->loggedin = true;
        $newuser->confirmed = 1;
        $newuser->site = $CFG->wwwroot;
        $newuser->lang = $CFG->lang;
    }

    return $newuser;
}

function authenticate_user_login($username, $password) {
/// Given a username and password, this function looks them
/// up using the currently selected authentication mechanism,
/// and if the authentication is successful, it returns a
/// valid $user object from the 'user' table.
///
/// Uses auth_ functions from the currently active auth module

    global $CFG;

    $md5password = md5($password);

    // First try to find the user in the database

    $user = get_user_info_from_db("username", $username);

    // Sort out the authentication method we are using.

    if (empty($CFG->auth)) {
        $CFG->auth = "manual";     // Default authentication module
    }

    if (empty($user->auth)) {      // For some reason it isn't set yet
        if (isadmin($user->id) or isguest($user->id)) {
            $auth = 'manual';    // Always assume these guys are internal
        } else {
            $auth = $CFG->auth;  // Normal users default to site method
        }
    } else {
        $auth = $user->auth;
    }
    
    if (detect_munged_arguments($auth, 0)) {   // For safety on the next require
        return false;
    }

    if (!file_exists("$CFG->dirroot/auth/$auth/lib.php")) {
        $auth = "manual";    // Can't find auth module, default to internal
    }

    require_once("$CFG->dirroot/auth/$auth/lib.php");

    if (auth_user_login($username, $password)) {  // Successful authentication
        if ($user) {                              // User already exists in database
            if (empty($user->auth)) {             // For some reason auth isn't set yet
                set_field('user', 'auth', $auth, 'username', $username);
            }
            if ($md5password <> $user->password) {   // Update local copy of password for reference
                set_field('user', 'password', $md5password, 'username', $username);
            }
        } else {
            $user = create_user_record($username, $password, $auth);
        }

        if (function_exists('auth_iscreator')) {    // Check if the user is a creator
            $useriscreator=auth_iscreator($username);            
            if(!is_null($useriscreator)) {
                if ($useriscreator) {
                    if (! record_exists("user_coursecreators", "userid", $user->id)) {
                        $cdata->userid = $user->id;
                        if (! insert_record("user_coursecreators", $cdata)) {
                            error("Cannot add user to course creators.");
                        }
                    }
                } else {
                    if ( record_exists("user_coursecreators", "userid", $user->id)) {
                        if (! delete_records("user_coursecreators", "userid", $user->id)) {
                            error("Cannot remove user from course creators.");
                        }
                    }
                }
            }    
        }
        return $user;

    } else {
        add_to_log(0, 'login', 'error', $_SERVER['HTTP_REFERER'], $username);
        error_log('[client '.$_SERVER['REMOTE_ADDR']."]\t$CFG->wwwroot\tFailed Login:\t$username\t".$_SERVER['HTTP_USER_AGENT']);
        return false;
    }
}

function enrol_student($userid, $courseid, $timestart=0, $timeend=0) {
/// Enrols (or re-enrols) a student in a given course

    if (!$course = get_record("course", "id", $courseid)) {  // Check course
        return false;
    }
    if (!$user = get_record("user", "id", $userid)) {        // Check user
        return false;
    }
    if ($student = get_record("user_students", "userid", $userid, "course", $courseid)) {
        $student->timestart = $timestart;
        $student->timeend = $timeend;
        $student->time = time();
        return update_record("user_students", $student);
        
    } else {
        $student->userid = $userid;
        $student->course = $courseid;
        $student->timestart = $timestart;
        $student->timeend = $timeend;
        $student->time = time();
        return insert_record("user_students", $student);
    }
}

function unenrol_student($userid, $courseid=0) {
/// Unenrols a student from a given course

    if ($courseid) {
        /// First delete any crucial stuff that might still send mail
        if ($forums = get_records("forum", "course", $courseid)) {
            foreach ($forums as $forum) {
                delete_records("forum_subscriptions", "forum", $forum->id, "userid", $userid);
            }
        }
        if ($groups = get_groups($courseid, $userid)) {
            foreach ($groups as $group) {
                delete_records("groups_members", "groupid", $group->id, "userid", $userid);
            }
        }
        return delete_records("user_students", "userid", $userid, "course", $courseid);

    } else {
        delete_records("forum_subscriptions", "userid", $userid);
        delete_records("groups_members", "userid", $userid);
        return delete_records("user_students", "userid", $userid);
    }
}

function add_teacher($userid, $courseid, $editall=1, $role="", $timestart=0, $timeend=0) {
/// Add a teacher to a given course

    if ($teacher = get_record('user_teachers', 'userid', $userid, 'course', $courseid)) {
        $newteacher = NULL;
        $newteacher->id = $teacher->id;
        $newteacher->editall = $editall;
        if ($role) {
            $newteacher->role = $role;
        }
        if ($timestart) {
            $newteacher->timestart = $timestart;
        }
        if ($timeend) {
            $newteacher->timeend = $timeend;
        }
        return update_record('user_teachers', $newteacher);
    }

    if (!record_exists("user", "id", $userid)) {
        return false;   // no such user
    }

    if (!record_exists("course", "id", $courseid)) {
        return false;   // no such course
    }

    $teacher = NULL;
    $teacher->userid  = $userid;
    $teacher->course  = $courseid;
    $teacher->editall = $editall;
    $teacher->role    = $role;

    if (record_exists("user_teachers", "course", $courseid)) {
        $teacher->authority = 2;
    } else {
        $teacher->authority = 1;
    }
    delete_records("user_students", "userid", $userid, "course", $courseid); // Unenrol as student

    return insert_record("user_teachers", $teacher);

}

function remove_teacher($userid, $courseid=0) {
/// Removes a teacher from a given course (or ALL courses)
/// Does not delete the user account
    if ($courseid) {
        /// First delete any crucial stuff that might still send mail
        if ($forums = get_records("forum", "course", $courseid)) {
            foreach ($forums as $forum) {
                delete_records("forum_subscriptions", "forum", $forum->id, "userid", $userid);
            }
        }

        /// Next if the teacher is not registered as a student, but is
        /// a member of a group, remove them from the group.
        if (!isstudent($courseid, $userid)) {
            if ($groups = get_groups($courseid, $userid)) {
                foreach ($groups as $group) {
                    delete_records("groups_members", "groupid", $group->id, "userid", $userid);
                }
            }
        }

        return delete_records("user_teachers", "userid", $userid, "course", $courseid);
    } else {
        delete_records("forum_subscriptions", "userid", $userid);
        return delete_records("user_teachers", "userid", $userid);
    }
}


function add_creator($userid) {
/// Add a creator to the site

    if (!record_exists("user_admins", "userid", $userid)) {
        if (record_exists("user", "id", $userid)) {
            $creator->userid = $userid;
            return insert_record("user_coursecreators", $creator);
        }
        return false;
    }
    return true;
}

function remove_creator($userid) {
/// Removes a creator from a site
    global $db;

    return delete_records("user_coursecreators", "userid", $userid);
}

function add_admin($userid) {
/// Add an admin to the site

    if (!record_exists("user_admins", "userid", $userid)) {
        if (record_exists("user", "id", $userid)) {
            $admin->userid = $userid;
            
            // any admin is also a teacher on the site course
            $site = get_site();
            if (!record_exists('user_teachers', 'course', $site->id, 'userid', $userid)) {
                if (!add_teacher($userid, $site->id)) {
                    return false;
                }
            }
            
            return insert_record("user_admins", $admin);
        }
        return false;
    }
    return true;
}

function remove_admin($userid) {
/// Removes an admin from a site
    global $db;

    // remove also from the list of site teachers
    $site = get_site();
    remove_teacher($userid, $site->id);

    return delete_records("user_admins", "userid", $userid);
}


function remove_course_contents($courseid, $showfeedback=true) {
/// Clear a course out completely, deleting all content
/// but don't delete the course itself

    global $CFG, $THEME, $USER, $SESSION;

    $result = true;

    if (! $course = get_record("course", "id", $courseid)) {
        error("Course ID was incorrect (can't find it)");
    }

    $strdeleted = get_string("deleted");

    // First delete every instance of every module

    if ($allmods = get_records("modules") ) {
        foreach ($allmods as $mod) {
            $modname = $mod->name;
            $modfile = "$CFG->dirroot/mod/$modname/lib.php";
            $moddelete = $modname."_delete_instance";       // Delete everything connected to an instance
            $moddeletecourse = $modname."_delete_course";   // Delete other stray stuff (uncommon)
            $count=0;
            if (file_exists($modfile)) {
                include_once($modfile);
                if (function_exists($moddelete)) {
                    if ($instances = get_records($modname, "course", $course->id)) {
                        foreach ($instances as $instance) {
                            if ($moddelete($instance->id)) {
                                $count++;
                            } else {
                                notify("Could not delete $modname instance $instance->id ($instance->name)");
                                $result = false;
                            }
                        }
                    }
                } else {
                    notify("Function $moddelete() doesn't exist!");
                    $result = false;
                }

                if (function_exists($moddeletecourse)) {
                    $moddeletecourse($course);
                }
            }
            if ($showfeedback) {
                notify("$strdeleted $count x $modname");
            }
        }
    } else {
        error("No modules are installed!");
    }

    // Delete any user stuff

    if (delete_records("user_students", "course", $course->id)) {
        if ($showfeedback) {
            notify("$strdeleted user_students");
        }
    } else {
        $result = false;
    }

    if (delete_records("user_teachers", "course", $course->id)) {
        if ($showfeedback) {
            notify("$strdeleted user_teachers");
        }
    } else {
        $result = false;
    }

    // Delete any groups

    if ($groups = get_records("groups", "courseid", $course->id)) {
        foreach ($groups as $group) {
            if (delete_records("groups_members", "groupid", $group->id)) {
                if ($showfeedback) {
                    notify("$strdeleted groups_members");
                }
            } else {
                $result = false;
            }
            if (delete_records("groups", "id", $group->id)) {
                if ($showfeedback) {
                    notify("$strdeleted groups");
                }
            } else {
                $result = false;
            }
        }
    }

    // Delete events

    if (delete_records("event", "courseid", $course->id)) {
        if ($showfeedback) {
            notify("$strdeleted event");
        }
    } else {
        $result = false;
    }

    // Delete logs

    if (delete_records("log", "course", $course->id)) {
        if ($showfeedback) {
            notify("$strdeleted log");
        }
    } else {
        $result = false;
    }

    // Delete any course stuff

    if (delete_records("course_sections", "course", $course->id)) {
        if ($showfeedback) {
            notify("$strdeleted course_sections");
        }
    } else {
        $result = false;
    }

    if (delete_records("course_modules", "course", $course->id)) {
        if ($showfeedback) {
            notify("$strdeleted course_modules");
        }
    } else {
        $result = false;
    }

    return $result;

}

function remove_course_userdata($courseid, $showfeedback=true,
                                $removestudents=true, $removeteachers=false, $removegroups=true,
                                $removeevents=true, $removelogs=false) {
/// This function will empty a course of USER data as much as 
/// possible.   It will retain the activities and the structure 
/// of the course.

    global $CFG, $THEME, $USER, $SESSION;

    $result = true;

    if (! $course = get_record("course", "id", $courseid)) {
        error("Course ID was incorrect (can't find it)");
    }

    $strdeleted = get_string("deleted");

    // Look in every instance of every module for data to delete

    if ($allmods = get_records("modules") ) {
        foreach ($allmods as $mod) {
            $modname = $mod->name;
            $modfile = "$CFG->dirroot/mod/$modname/lib.php";
            $moddeleteuserdata = $modname."_delete_userdata";   // Function to delete user data
            $count=0;
            if (file_exists($modfile)) {
                @include_once($modfile);
                if (function_exists($moddeleteuserdata)) {
                    $moddeleteuserdata($course, $showfeedback);
                }
            }
        }
    } else {
        error("No modules are installed!");
    }

    // Delete other stuff

    if ($removestudents) {
        /// Delete student enrolments
        if (delete_records("user_students", "course", $course->id)) {
            if ($showfeedback) {
                notify("$strdeleted user_students");
            }
        } else {
            $result = false;
        }
        /// Delete group members (but keep the groups)
        if ($groups = get_records("groups", "courseid", $course->id)) {
            foreach ($groups as $group) {
                if (delete_records("groups_members", "groupid", $group->id)) {
                    if ($showfeedback) {
                        notify("$strdeleted groups_members");
                    }
                } else {
                    $result = false;
                }
            }
        }
    }

    if ($removeteachers) {
        if (delete_records("user_teachers", "course", $course->id)) {
            if ($showfeedback) {
                notify("$strdeleted user_teachers");
            }
        } else {
            $result = false;
        }
    }

    if ($removegroups) {
        if ($groups = get_records("groups", "courseid", $course->id)) {
            foreach ($groups as $group) {
                if (delete_records("groups", "id", $group->id)) {
                    if ($showfeedback) {
                        notify("$strdeleted groups");
                    }
                } else {
                    $result = false;
                }
            }
        }
    }

    if ($removeevents) {
        if (delete_records("event", "courseid", $course->id)) {
            if ($showfeedback) {
                notify("$strdeleted event");
            }
        } else {
            $result = false;
        }
    }

    if ($removelogs) {
        if (delete_records("log", "course", $course->id)) {
            if ($showfeedback) {
                notify("$strdeleted log");
            }
        } else {
            $result = false;
        }
    }

    return $result;

}



/// GROUPS /////////////////////////////////////////////////////////


/**
* Returns a boolean: is the user a member of the given group?
*
* @param    type description
*/
function ismember($groupid, $userid=0) {
    global $USER;

    if (!$groupid) {   // No point doing further checks
        return false;
    }

    if (!$userid) {
        if (empty($USER->groupmember)) {
            return false;
        }
        foreach ($USER->groupmember as $courseid => $mgroupid) {
            if ($mgroupid == $groupid) {
                return true;
            }
        }
        return false;
    }

    return record_exists("groups_members", "groupid", $groupid, "userid", $userid);
}

/**
* Returns the group ID of the current user in the given course
*
* @param    type description
*/
function mygroupid($courseid) {
    global $USER;

    if (empty($USER->groupmember[$courseid])) {
        return 0;
    } else {
        return $USER->groupmember[$courseid];
    }
}

/**
* For a given course, and possibly course module, determine
* what the current default groupmode is:
* NOGROUPS, SEPARATEGROUPS or VISIBLEGROUPS
*
* @param    type description
*/
function groupmode($course, $cm=null) {

    if ($cm and !$course->groupmodeforce) {
        return $cm->groupmode;
    }
    return $course->groupmode;
}


/**
* Sets the current group in the session variable
*
* @param    type description
*/
function set_current_group($courseid, $groupid) {
    global $SESSION;

    return $SESSION->currentgroup[$courseid] = $groupid;
}


/**
* Gets the current group for the current user as an id or an object
*
* @param    type description
*/
function get_current_group($courseid, $full=false) {
    global $SESSION, $USER;

    if (!isset($SESSION->currentgroup[$courseid])) {
        if (empty($USER->groupmember[$courseid])) {
            return 0;
        } else {
            $SESSION->currentgroup[$courseid] = $USER->groupmember[$courseid];
        }
    }

    if ($full) {
        return get_record('groups', 'id', $SESSION->currentgroup[$courseid]);
    } else {
        return $SESSION->currentgroup[$courseid];
    }
}

/**
* A combination function to make it easier for modules
* to set up groups.
*
* It will use a given "groupid" parameter and try to use
* that to reset the current group for the user.
*
* @param    type description
*/
function get_and_set_current_group($course, $groupmode, $groupid=-1) {

    if (!$groupmode) {   // Groups don't even apply
        return false;
    }

    $currentgroupid = get_current_group($course->id);

    if ($groupid < 0) {  // No change was specified
        return $currentgroupid;
    }

    if ($groupid) {      // Try to change the current group to this groupid
        if ($group = get_record('groups', 'id', $groupid, 'courseid', $course->id)) { // Exists
            if (isteacheredit($course->id)) {          // Sets current default group
                $currentgroupid = set_current_group($course->id, $group->id);

            } else if ($groupmode == VISIBLEGROUPS) {  // All groups are visible
                $currentgroupid = $group->id;
            }
        }
    } else {             // When groupid = 0 it means show ALL groups
        if (isteacheredit($course->id)) {          // Sets current default group
            $currentgroupid = set_current_group($course->id, 0);

        } else if ($groupmode == VISIBLEGROUPS) {  // All groups are visible
            $currentgroupid = 0;
        }
    }

    return $currentgroupid;
}


/**
* A big combination function to make it easier for modules
* to set up groups.
*
* Terminates if the current user shouldn't be looking at this group
* Otherwise returns the current group if there is one
* Otherwise returns false if groups aren't relevant
*
* @param    type description
*/
function setup_and_print_groups($course, $groupmode, $urlroot) {

    if (isset($_GET['group'])) {
        $changegroup = $_GET['group'];  /// 0 or higher
    } else {
        $changegroup = -1;              /// This means no group change was specified
    }

    $currentgroup = get_and_set_current_group($course, $groupmode, $changegroup);

    if ($currentgroup === false) {
        return false;
    }

    if ($groupmode == SEPARATEGROUPS and !isteacheredit($course->id) and !$currentgroup) {
        print_heading(get_string('notingroup'));
        print_footer($course);
        exit;
    }

    if ($groupmode == VISIBLEGROUPS or ($groupmode and isteacheredit($course->id))) {
        if ($groups = get_records_menu("groups", "courseid", $course->id, "name ASC", "id,name")) {
            echo '<div align="center">';
            print_group_menu($groups, $groupmode, $currentgroup, $urlroot);
            echo '</div>';
        }
    }

    return $currentgroup;
}



/// CORRESPONDENCE  ////////////////////////////////////////////////

function email_to_user($user, $from, $subject, $messagetext, $messagehtml="", $attachment="", $attachname="", $usetrueaddress=true) {
///  user        - a user record as an object
///  from        - a user record as an object
///  subject     - plain text subject line of the email
///  messagetext - plain text version of the message
///  messagehtml - complete html version of the message (optional)
///  attachment  - a file on the filesystem, relative to $CFG->dataroot
///  attachname  - the name of the file (extension indicates MIME)
///  usetrueaddress - determines whether $from email address should be sent out.
///                   Will be overruled by user profile setting for maildisplay
///
///  Returns "true" if mail was sent OK, "emailstop" if email was blocked by user
///  and "false" if there was another sort of error.

    global $CFG, $FULLME;

    global $course;                // This is a bit of an ugly hack to be gotten rid of later
    if (!empty($course->lang)) {   // Course language is defined
        $CFG->courselang = $course->lang;
    }

    include_once("$CFG->libdir/phpmailer/class.phpmailer.php");

    if (empty($user)) {
        return false;
    }

    if (!empty($user->emailstop)) {
        return 'emailstop';
    }

    $mail = new phpmailer;

    $mail->Version = "Moodle $CFG->version";           // mailer version
    $mail->PluginDir = "$CFG->libdir/phpmailer/";      // plugin directory (eg smtp plugin)


    if (current_language() != "en") {
        $mail->CharSet = get_string("thischarset");
    }

    if ($CFG->smtphosts == "qmail") {
        $mail->IsQmail();                              // use Qmail system

    } else if (empty($CFG->smtphosts)) {
        $mail->IsMail();                               // use PHP mail() = sendmail

    } else {
        $mail->IsSMTP();                               // use SMTP directly
        if ($CFG->debug > 7) {
            echo "<pre>\n";
            $mail->SMTPDebug = true;
        }
        $mail->Host = "$CFG->smtphosts";               // specify main and backup servers

        if ($CFG->smtpuser) {                          // Use SMTP authentication
            $mail->SMTPAuth = true;
            $mail->Username = $CFG->smtpuser;
            $mail->Password = $CFG->smtppass;
        }
    }

    $adminuser = get_admin();

    $mail->Sender   = "$adminuser->email";

    if (is_string($from)) { // So we can pass whatever we want if there is need
        $mail->From     = $CFG->noreplyaddress;
        $mail->FromName = $from;
    } else if ($usetrueaddress and $from->maildisplay) {
        $mail->From     = "$from->email";
        $mail->FromName = fullname($from);
    } else {
        $mail->From     = "$CFG->noreplyaddress";
        $mail->FromName = fullname($from);
    }
    $mail->Subject  =  stripslashes($subject);

    $mail->AddAddress("$user->email", fullname($user) );

    $mail->WordWrap = 79;                               // set word wrap

    if (!empty($from->customheaders)) {                 // Add custom headers
        if (is_array($from->customheaders)) {
            foreach ($from->customheaders as $customheader) {
                $mail->AddCustomHeader($customheader);
            }
        } else {
            $mail->AddCustomHeader($from->customheaders);
        }
    }

    if ($messagehtml) {
        $mail->IsHTML(true);
        $mail->Encoding = "quoted-printable";           // Encoding to use
        $mail->Body    =  $messagehtml;
        $mail->AltBody =  "\n$messagetext\n";
    } else {
        $mail->IsHTML(false);
        $mail->Body =  "\n$messagetext\n";
    }

    if ($attachment && $attachname) {
        if (ereg( "\\.\\." ,$attachment )) {    // Security check for ".." in dir path
            $mail->AddAddress("$adminuser->email", fullname($adminuser) );
            $mail->AddStringAttachment("Error in attachment.  User attempted to attach a filename with a unsafe name.", "error.txt", "8bit", "text/plain");
        } else {
            include_once("$CFG->dirroot/files/mimetypes.php");
            $mimetype = mimeinfo("type", $attachname);
            $mail->AddAttachment("$CFG->dataroot/$attachment", "$attachname", "base64", "$mimetype");
        }
    }

    if ($mail->Send()) {
        return true;
    } else {
        mtrace("ERROR: $mail->ErrorInfo");
        $site = get_site();
        add_to_log($site->id, "library", "mailer", $FULLME, "ERROR: $mail->ErrorInfo");
        return false;
    }
}

function reset_password_and_mail($user) {

    global $CFG;

    $site  = get_site();
    $from = get_admin();

    $newpassword = generate_password();

    if (! set_field("user", "password", md5($newpassword), "id", $user->id) ) {
        error("Could not set user password!");
    }

    $a->firstname = $user->firstname;
    $a->sitename = $site->fullname;
    $a->username = $user->username;
    $a->newpassword = $newpassword;
    $a->link = "$CFG->wwwroot/login/change_password.php";
    $a->signoff = fullname($from, true)." ($from->email)";

    $message = get_string("newpasswordtext", "", $a);

    $subject  = "$site->fullname: ".get_string("changedpassword");

    return email_to_user($user, $from, $subject, $message);

}

function send_confirmation_email($user) {

    global $CFG;

    $site = get_site();
    $from = get_admin();

    $data->firstname = $user->firstname;
    $data->sitename = $site->fullname;
    $data->link = "$CFG->wwwroot/login/confirm.php?p=$user->secret&s=$user->username";
    $data->admin = fullname($from)." ($from->email)";

    $message = get_string("emailconfirmation", "", $data);
    $subject = get_string("emailconfirmationsubject", "", $site->fullname);

    $messagehtml = text_to_html($message, false, false, true);

    return email_to_user($user, $from, $subject, $message, $messagehtml);

}

function send_password_change_confirmation_email($user) {

    global $CFG;

    $site = get_site();
    $from = get_admin();

    $data->firstname = $user->firstname;
    $data->sitename = $site->fullname;
    $data->link = "$CFG->wwwroot/login/forgot_password.php?p=$user->secret&s=$user->username";
    $data->admin = fullname($from)." ($from->email)";

    $message = get_string("emailpasswordconfirmation", "", $data);
    $subject = get_string("emailpasswordconfirmationsubject", "", $site->fullname);

    return email_to_user($user, $from, $subject, $message);

}


function email_is_not_allowed($email) {
/// Check that an email is allowed.  It returns an error message if there 
/// was a problem.

    global $CFG;

    if (!empty($CFG->allowemailaddresses)) {
        $allowed = explode(' ', $CFG->allowemailaddresses);
        foreach ($allowed as $allowedpattern) {
            $allowedpattern = trim($allowedpattern);
            if (!$allowedpattern) {
                continue;
            }
            if (strpos($email, $allowedpattern) !== false) {  // Match!
                return false;
            }
        }
        return get_string("emailonlyallowed", '', $CFG->allowemailaddresses);

    } else if (!empty($CFG->denyemailaddresses)) {
        $denied = explode(' ', $CFG->denyemailaddresses);
        foreach ($denied as $deniedpattern) {
            $deniedpattern = trim($deniedpattern);
            if (!$deniedpattern) {
                continue;
            }
            if (strpos($email, $deniedpattern) !== false) {   // Match!
                return get_string("emailnotallowed", '', $CFG->denyemailaddresses);
            }
        }
    }

    return false;
}


/// FILE HANDLING  /////////////////////////////////////////////


function make_upload_directory($directory, $shownotices=true) {
/// $directory = a string of directory names under $CFG->dataroot
/// eg  stuff/assignment/1
/// Returns full directory if successful, false if not

    global $CFG;

    $currdir = $CFG->dataroot;

    umask(0000);

    if (!file_exists($currdir)) {
        if (! mkdir($currdir, $CFG->directorypermissions)) {
            if ($shownotices) {
                notify("ERROR: You need to create the directory $currdir with web server write access");
            }
            return false;
        }
    }

    $dirarray = explode("/", $directory);

    foreach ($dirarray as $dir) {
        $currdir = "$currdir/$dir";
        if (! file_exists($currdir)) {
            if (! mkdir($currdir, $CFG->directorypermissions)) {
                if ($shownotices) {
                    notify("ERROR: Could not find or create a directory ($currdir)");
                }
                return false;
            }
            @chmod($currdir, $CFG->directorypermissions);  // Just in case mkdir didn't do it
        }
    }

    return $currdir;
}


function make_mod_upload_directory($courseid) {
/// Makes an upload directory for a particular module
    global $CFG;

    if (! $moddata = make_upload_directory("$courseid/$CFG->moddata")) {
        return false;
    }

    $strreadme = get_string("readme");

    if (file_exists("$CFG->dirroot/lang/$CFG->lang/docs/module_files.txt")) {
        copy("$CFG->dirroot/lang/$CFG->lang/docs/module_files.txt", "$moddata/$strreadme.txt");
    } else {
        copy("$CFG->dirroot/lang/en/docs/module_files.txt", "$moddata/$strreadme.txt");
    }
    return $moddata;
}


function valid_uploaded_file($newfile) {
/// Returns current name of file on disk if true
    if (empty($newfile)) {
        return "";
    }
    if (is_uploaded_file($newfile['tmp_name']) and $newfile['size'] > 0) {
        return $newfile['tmp_name'];
    } else {
        return "";
    }
}

function get_max_upload_file_size($sitebytes=0, $coursebytes=0, $modulebytes=0) {
/// Returns the maximum size for uploading files
/// There are seven possible upload limits:
///
/// 1) in Apache using LimitRequestBody (no way of checking or changing this)
/// 2) in php.ini for 'upload_max_filesize' (can not be changed inside PHP)
/// 3) in .htaccess for 'upload_max_filesize' (can not be changed inside PHP)
/// 4) in php.ini for 'post_max_size' (can not be changed inside PHP)
/// 5) by the Moodle admin in $CFG->maxbytes
/// 6) by the teacher in the current course $course->maxbytes
/// 7) by the teacher for the current module, eg $assignment->maxbytes
///
/// These last two are passed to this function as arguments (in bytes).
/// Anything defined as 0 is ignored.
/// The smallest of all the non-zero numbers is returned.

    if (! $filesize = ini_get("upload_max_filesize")) {
        $filesize = "5M";
    }
    $minimumsize = get_real_size($filesize);

    if ($postsize = ini_get("post_max_size")) {
        $postsize = get_real_size($postsize);
        if ($postsize < $minimumsize) {
            $minimumsize = $postsize;
        }
    }

    if ($sitebytes and $sitebytes < $minimumsize) {
        $minimumsize = $sitebytes;
    }

    if ($coursebytes and $coursebytes < $minimumsize) {
        $minimumsize = $coursebytes;
    }

    if ($modulebytes and $modulebytes < $minimumsize) {
        $minimumsize = $modulebytes;
    }

    return $minimumsize;
}

function get_max_upload_sizes($sitebytes=0, $coursebytes=0, $modulebytes=0) {
/// Related to the above function - this function returns an
/// array of possible sizes in an array, translated to the
/// local language.

    if (!$maxsize = get_max_upload_file_size($sitebytes, $coursebytes, $modulebytes)) {
        return array();
    }

    $filesize[$maxsize] = display_size($maxsize);

    $sizelist = array(10240, 51200, 102400, 512000, 1048576, 2097152,
                      5242880, 10485760, 20971520, 52428800, 104857600);

    foreach ($sizelist as $sizebytes) {
       if ($sizebytes < $maxsize) {
           $filesize[$sizebytes] = display_size($sizebytes);
       }
    }

    krsort($filesize, SORT_NUMERIC);

    return $filesize;
}

function get_directory_list($rootdir, $excludefile="", $descend=true, $getdirs=false, $getfiles=true) {
/// Returns an array with all the filenames in
/// all subdirectories, relative to the given rootdir.
/// If excludefile is defined, then that file/directory is ignored
/// If getdirs is true, then (sub)directories are included in the output
/// If getfiles is true, then files are included in the output
/// (at least one of these must be true!)

    $dirs = array();

    if (!$getdirs and !$getfiles) {   // Nothing to show
        return $dirs;
    }

    if (!is_dir($rootdir)) {          // Must be a directory
        return $dirs;
    }

    if (!$dir = opendir($rootdir)) {  // Can't open it for some reason
        return $dirs;
    }

    while (false !== ($file = readdir($dir))) {
        $firstchar = substr($file, 0, 1);
        if ($firstchar == "." or $file == "CVS" or $file == $excludefile) {
            continue;
        }
        $fullfile = "$rootdir/$file";
        if (filetype($fullfile) == "dir") {
            if ($getdirs) {
                $dirs[] = $file;
            }
            if ($descend) {
                $subdirs = get_directory_list($fullfile, $excludefile, $descend, $getdirs, $getfiles);
                foreach ($subdirs as $subdir) {
                    $dirs[] = "$file/$subdir";
                }
            }
        } else if ($getfiles) {
            $dirs[] = $file;
        }
    }
    closedir($dir);

    asort($dirs);

    return $dirs;
}

function get_directory_size($rootdir, $excludefile="") {
/// Adds up all the files in a directory and works out the size

    $size = 0;

    if (!is_dir($rootdir)) {          // Must be a directory
        return $dirs;
    }

    if (!$dir = @opendir($rootdir)) {  // Can't open it for some reason
        return $dirs;
    }

    while (false !== ($file = readdir($dir))) {
        $firstchar = substr($file, 0, 1);
        if ($firstchar == "." or $file == "CVS" or $file == $excludefile) {
            continue;
        }
        $fullfile = "$rootdir/$file";
        if (filetype($fullfile) == "dir") {
            $size += get_directory_size($fullfile, $excludefile);
        } else {
            $size += filesize($fullfile);
        }
    }
    closedir($dir);

    return $size;
}

function get_real_size($size=0) {
/// Converts numbers like 10M into bytes
    if (!$size) {
        return 0;
    }
    $scan['MB'] = 1048576;
    $scan['Mb'] = 1048576;
    $scan['M'] = 1048576;
    $scan['m'] = 1048576;
    $scan['KB'] = 1024;
    $scan['Kb'] = 1024;
    $scan['K'] = 1024;
    $scan['k'] = 1024;

    while (list($key) = each($scan)) {
        if ((strlen($size)>strlen($key))&&(substr($size, strlen($size) - strlen($key))==$key)) {
            $size = substr($size, 0, strlen($size) - strlen($key)) * $scan[$key];
            break;
        }
    }
    return $size;
}

function display_size($size) {
/// Converts bytes into display form

    static $gb,$mb,$kb,$b;

    if (empty($gb)) {
        $gb = get_string('sizegb');
        $mb = get_string('sizemb');
        $kb = get_string('sizekb');
        $b  = get_string('sizeb');
    }

    if ($size >= 1073741824) {
        $size = round($size / 1073741824 * 10) / 10 . $gb;
    } else if ($size >= 1048576) {
        $size = round($size / 1048576 * 10) / 10 . $mb;
    } else if ($size >= 1024) {
        $size = round($size / 1024 * 10) / 10 . $kb;
    } else {
        $size = $size ." $b";
    }
    return $size;
}

function clean_filename($string) {
/// Cleans a given filename by removing suspicious or troublesome characters
/// Only these are allowed:
///    alphanumeric _ - .

    $string = eregi_replace("\.\.+", "", $string);
    $string = preg_replace('/[^\.a-zA-Z\d\_-]/','_', $string ); // only allowed chars
    $string = eregi_replace("_+", "_", $string);
    return    $string;
}


/// STRING TRANSLATION  ////////////////////////////////////////

function current_language() {
/// Returns the code for the current language
    global $CFG, $USER, $SESSION;

    if (!empty($CFG->courselang)) {    // Course language can override all other settings for this page
        return $CFG->courselang;

    } else if (!empty($SESSION->lang)) {    // Session language can override other settings
        return $SESSION->lang;

    } else if (!empty($USER->lang)) {    // User language can override site language
        return $USER->lang;

    } else {
        return $CFG->lang;
    }
}

function print_string($identifier, $module="", $a=NULL) {
/// Given a string to translate - prints it out.
    echo get_string($identifier, $module, $a);
}

function get_string($identifier, $module="", $a=NULL) {
/// Return the translated string specified by $identifier as
/// for $module.  Uses the same format files as STphp.
/// $a is an object, string or number that can be used
/// within translation strings
///
/// eg "hello \$a->firstname \$a->lastname"
/// or "hello \$a"

    global $CFG;

    global $course;     /// Not a nice hack, but quick
    if (empty($CFG->courselang)) {
        if (!empty($course->lang)) {
            $CFG->courselang = $course->lang;
        }
    }

    $lang = current_language();

    if ($module == "") {
        $module = "moodle";
    }

    $langpath = "$CFG->dirroot/lang";
    $langfile = "$langpath/$lang/$module.php";

    // Look for the string - if found then return it

    if (file_exists($langfile)) {
        if ($result = get_string_from_file($identifier, $langfile, "\$resultstring")) {
            eval($result);
            return $resultstring;
        }
    }

    // If it's a module, then look within the module pack itself mod/xxxx/lang/en/module.php

    if ($module != "moodle") {
        $modlangpath = "$CFG->dirroot/mod/$module/lang";
        $langfile = "$modlangpath/$lang/$module.php";
        if (file_exists($langfile)) {
            if ($result = get_string_from_file($identifier, $langfile, "\$resultstring")) {
                eval($result);
                return $resultstring;
            }
        }
    }

    // If the preferred language was English we can abort now
    if ($lang == "en") {
        return "[[$identifier]]";
    }

    // Is a parent language defined?  If so, try it.

    if ($result = get_string_from_file("parentlanguage", "$langpath/$lang/moodle.php", "\$parentlang")) {
        eval($result);
        if (!empty($parentlang)) {
            $langfile = "$langpath/$parentlang/$module.php";
            if (file_exists($langfile)) {
                if ($result = get_string_from_file($identifier, $langfile, "\$resultstring")) {
                    eval($result);
                    return $resultstring;
                }
            }
        }
    }

    // Our only remaining option is to try English

    $langfile = "$langpath/en/$module.php";
    if (!file_exists($langfile)) {
        return "ERROR: No lang file ($langpath/en/$module.php)!";
    }
    if ($result = get_string_from_file($identifier, $langfile, "\$resultstring")) {
        eval($result);
        return $resultstring;
    }

    // If it's a module, then look within the module pack itself mod/xxxx/lang/en/module.php

    if ($module != "moodle") {
        $langfile = "$modlangpath/en/$module.php";
        if (file_exists($langfile)) {
            if ($result = get_string_from_file($identifier, $langfile, "\$resultstring")) {
                eval($result);
                return $resultstring;
            }
        }
    }

    return "[[$identifier]]";  // Last resort
}


function get_string_from_file($identifier, $langfile, $destination) {
/// This function is only used from get_string().

    static $strings;    // Keep the strings cached in memory.

    if (empty($strings[$langfile])) {
        $string = array();
        include ($langfile);
        $strings[$langfile] = $string;
    } else {
        $string = &$strings[$langfile];
    }

    if (!isset ($string[$identifier])) {
        return false;
    }

    return "$destination = sprintf(\"".$string[$identifier]."\");";
}

function get_strings($array, $module='') {
/// Converts an array of strings

   $string = NULL;
   foreach ($array as $item) {
       $string->$item = get_string($item, $module);
   }
   return $string;
}

function get_list_of_languages() {
/// Returns a list of language codes and their full names
    global $CFG;

    $languages = array();

    if (!empty($CFG->langlist)) {       // use admin's list of languages
        $langlist = explode(',', $CFG->langlist);
        foreach ($langlist as $lang) {
            if (file_exists("$CFG->dirroot/lang/$lang/moodle.php")) {
                include("$CFG->dirroot/lang/$lang/moodle.php");
                $languages[$lang] = $string["thislanguage"]." ($lang)";
                unset($string);
            }
        }
    } else {
        if (!$langdirs = get_list_of_plugins("lang")) {
            return false;
        }
        foreach ($langdirs as $lang) {
            include("$CFG->dirroot/lang/$lang/moodle.php");
            $languages[$lang] = $string["thislanguage"]." ($lang)";
            unset($string);
        }
    }

    return $languages;
}

function get_list_of_countries() {
/// Returns a list of country names in the current language
    global $CFG, $USER;

    $lang = current_language();

    if (!file_exists("$CFG->dirroot/lang/$lang/countries.php")) {
        if ($parentlang = get_string("parentlanguage")) {
            if (file_exists("$CFG->dirroot/lang/$parentlang/countries.php")) {
                $lang = $parentlang;
            } else {
                $lang = "en";  // countries.php must exist in this pack
            }
        } else {
            $lang = "en";  // countries.php must exist in this pack
        }
    }

    include("$CFG->dirroot/lang/$lang/countries.php");

    if (!empty($string)) {
        asort($string);
    }

    return $string;
}

function get_list_of_pixnames() {
/// Returns a list of picture names in the current language
    global $CFG;

    $lang = current_language();

    if (!file_exists("$CFG->dirroot/lang/$lang/pix.php")) {
        if ($parentlang = get_string("parentlanguage")) {
            if (file_exists("$CFG->dirroot/lang/$parentlang/pix.php")) {
                $lang = $parentlang;
            } else {
                $lang = "en";  // countries.php must exist in this pack
            }
        } else {
            $lang = "en";  // countries.php must exist in this pack
        }
    }

    include_once("$CFG->dirroot/lang/$lang/pix.php");

    return $string;
}

function document_file($file, $include=true) {
/// Can include a given document file (depends on second
/// parameter) or just return info about it

    global $CFG;

    $file = clean_filename($file);

    if (empty($file)) {
        return false;
    }

    $langs = array(current_language(), get_string("parentlanguage"), "en");

    foreach ($langs as $lang) {
        $info->filepath = "$CFG->dirroot/lang/$lang/docs/$file";
        $info->urlpath  = "$CFG->wwwroot/lang/$lang/docs/$file";

        if (file_exists($info->filepath)) {
            if ($include) {
                include($info->filepath);
            }
            return $info;
        }
    }

    return false;
}


/// ENCRYPTION  ////////////////////////////////////////////////

function rc4encrypt($data) {
    $password = "nfgjeingjk";
    return endecrypt($password, $data, "");
}

function rc4decrypt($data) {
    $password = "nfgjeingjk";
    return endecrypt($password, $data, "de");
}

function endecrypt ($pwd, $data, $case) {
/// Based on a class by Mukul Sabharwal [mukulsabharwal@yahoo.com]

    if ($case == 'de') {
        $data = urldecode($data);
    }

    $key[] = "";
    $box[] = "";
    $temp_swap = "";
    $pwd_length = 0;

    $pwd_length = strlen($pwd);

    for ($i = 0; $i <= 255; $i++) {
        $key[$i] = ord(substr($pwd, ($i % $pwd_length), 1));
        $box[$i] = $i;
    }

    $x = 0;

    for ($i = 0; $i <= 255; $i++) {
        $x = ($x + $box[$i] + $key[$i]) % 256;
        $temp_swap = $box[$i];
        $box[$i] = $box[$x];
        $box[$x] = $temp_swap;
    }

    $temp = "";
    $k = "";

    $cipherby = "";
    $cipher = "";

    $a = 0;
    $j = 0;

    for ($i = 0; $i < strlen($data); $i++) {
        $a = ($a + 1) % 256;
        $j = ($j + $box[$a]) % 256;
        $temp = $box[$a];
        $box[$a] = $box[$j];
        $box[$j] = $temp;
        $k = $box[(($box[$a] + $box[$j]) % 256)];
        $cipherby = ord(substr($data, $i, 1)) ^ $k;
        $cipher .= chr($cipherby);
    }

    if ($case == 'de') {
        $cipher = urldecode(urlencode($cipher));
    } else {
        $cipher = urlencode($cipher);
    }

    return $cipher;
}


/// CALENDAR MANAGEMENT  ////////////////////////////////////////////////////////////////


function add_event($event) {
/// call this function to add an event to the calendar table
///  and to call any calendar plugins
/// The function returns the id number of the resulting record
/// The object event should include the following:
///     $event->name         Name for the event
///     $event->description  Description of the event (defaults to '')
///     $event->courseid     The id of the course this event belongs to (0 = all courses)
///     $event->groupid      The id of the group this event belongs to (0 = no group)
///     $event->userid       The id of the user this event belongs to (0 = no user)
///     $event->modulename   Name of the module that creates this event
///     $event->instance     Instance of the module that owns this event
///     $event->eventtype    The type info together with the module info could
///                          be used by calendar plugins to decide how to display event
///     $event->timestart    Timestamp for start of event
///     $event->timeduration Duration (defaults to zero)

    global $CFG;

    $event->timemodified = time();

    if (!$event->id = insert_record("event", $event)) {
        return false;
    }

    if (!empty($CFG->calendar)) { // call the add_event function of the selected calendar
        if (file_exists("$CFG->dirroot/calendar/$CFG->calendar/lib.php")) {
            include_once("$CFG->dirroot/calendar/$CFG->calendar/lib.php");
            $calendar_add_event = $CFG->calendar.'_add_event';
            if (function_exists($calendar_add_event)) {
                $calendar_add_event($event);
            }
        }
    }

    return $event->id;
}


function update_event($event) {
/// call this function to update an event in the calendar table
/// the event will be identified by the id field of the $event object

    global $CFG;

    $event->timemodified = time();

    if (!empty($CFG->calendar)) { // call the update_event function of the selected calendar
        if (file_exists("$CFG->dirroot/calendar/$CFG->calendar/lib.php")) {
            include_once("$CFG->dirroot/calendar/$CFG->calendar/lib.php");
            $calendar_update_event = $CFG->calendar.'_update_event';
            if (function_exists($calendar_update_event)) {
                $calendar_update_event($event);
            }
        }
    }
    return update_record("event", $event);
}


function delete_event($id) {
/// call this function to delete the event with id $id from calendar table

    global $CFG;

    if (!empty($CFG->calendar)) { // call the delete_event function of the selected calendar
        if (file_exists("$CFG->dirroot/calendar/$CFG->calendar/lib.php")) {
            include_once("$CFG->dirroot/calendar/$CFG->calendar/lib.php");
            $calendar_delete_event = $CFG->calendar.'_delete_event';
            if (function_exists($calendar_delete_event)) {
                $calendar_delete_event($id);
            }
        }
    }
    return delete_records("event", 'id', $id);
}


function hide_event($event) {
/// call this function to hide an event in the calendar table
/// the event will be identified by the id field of the $event object

    global $CFG;

    if (!empty($CFG->calendar)) { // call the update_event function of the selected calendar
        if (file_exists("$CFG->dirroot/calendar/$CFG->calendar/lib.php")) {
            include_once("$CFG->dirroot/calendar/$CFG->calendar/lib.php");
            $calendar_hide_event = $CFG->calendar.'_hide_event';
            if (function_exists($calendar_hide_event)) {
                $calendar_hide_event($event);
            }
        }
    }
    return set_field('event', 'visible', 0, 'id', $event->id);
}


function show_event($event) {
/// call this function to unhide an event in the calendar table
/// the event will be identified by the id field of the $event object

    global $CFG;

    if (!empty($CFG->calendar)) { // call the update_event function of the selected calendar
        if (file_exists("$CFG->dirroot/calendar/$CFG->calendar/lib.php")) {
            include_once("$CFG->dirroot/calendar/$CFG->calendar/lib.php");
            $calendar_show_event = $CFG->calendar.'_show_event';
            if (function_exists($calendar_show_event)) {
                $calendar_show_event($event);
            }
        }
    }
    return set_field('event', 'visible', 1, 'id', $event->id);
}


/// ENVIRONMENT CHECKING  ////////////////////////////////////////////////////////////

function get_list_of_plugins($plugin="mod", $exclude="") {
/// Lists plugin directories within some directory

    global $CFG;

    $basedir = opendir("$CFG->dirroot/$plugin");
    while ($dir = readdir($basedir)) {
        $firstchar = substr($dir, 0, 1);
        if ($firstchar == "." or $dir == "CVS" or $dir == "_vti_cnf" or $dir == $exclude) {
            continue;
        }
        if (filetype("$CFG->dirroot/$plugin/$dir") != "dir") {
            continue;
        }
        $plugins[] = $dir;
    }
    if ($plugins) {
        asort($plugins);
    }
    return $plugins;
}

function check_php_version($version="4.1.0") {
/// Returns true is the current version of PHP is greater that the specified one
    $minversion = intval(str_replace(".", "", $version));
    $curversion = intval(str_replace(".", "", phpversion()));
    return ($curversion >= $minversion);
}

function check_browser_version($brand="MSIE", $version=5.5) {
/// Checks to see if is a browser matches the specified
/// brand and is equal or better version.

    $agent = $_SERVER["HTTP_USER_AGENT"];

    if (empty($agent)) {
        return false;
    }

    switch ($brand) {

      case "Gecko":   /// Gecko based browsers

          if (substr_count($agent, "Camino")) {     // MacOS X Camino not supported.
              return false;
          }

          // the proper string - Gecko/CCYYMMDD Vendor/Version
          if (ereg("^([a-zA-Z]+)/([0-9]+\.[0-9]+) \((.*)\) (.*)$", $agent, $match)) {
              if (ereg("^([Gecko]+)/([0-9]+)",$match[4], $reldate)) {
                  if ($reldate[2] > $version) {
                      return true;
                  }
              }
          }
          break;


      case "MSIE":   /// Internet Explorer

          if (strpos($agent, 'Opera')) {     // Reject Opera
              return false;
          }
          $string = explode(";", $agent);
          if (!isset($string[1])) {
              return false;
          }
          $string = explode(" ", trim($string[1]));
          if (!isset($string[0]) and !isset($string[1])) {
              return false;
          }
          if ($string[0] == $brand and (float)$string[1] >= $version ) {
              return true;
          }
          break;

    }

    return false;
}

function ini_get_bool($ini_get_arg) {
/// This function makes the return value of ini_get consistent if you are
/// setting server directives through the .htaccess file in apache.
/// Current behavior for value set from php.ini On = 1, Off = [blank]
/// Current behavior for value set from .htaccess On = On, Off = Off
/// Contributed by jdell@unr.edu

    $temp = ini_get($ini_get_arg);

    if ($temp == "1" or strtolower($temp) == "on") {
        return true;
    }
    return false;
}

function can_use_richtext_editor() {
/// Compatibility stub to provide backward compatibility
    return can_use_html_editor();
}

function can_use_html_editor() {
/// Is the HTML editor enabled?  This depends on site and user
/// settings, as well as the current browser being used.
/// Returns false is editor is not being used, otherwise
/// returns "MSIE" or "Gecko"

    global $USER, $CFG;

    if (!empty($USER->htmleditor) and !empty($CFG->htmleditor)) {
        if (check_browser_version("MSIE", 5.5)) {
            return "MSIE";
        } else if (check_browser_version("Gecko", 20030516)) {
            return "Gecko";
        }
    }
    return false;
}


function check_gd_version() {
/// Hack to find out the GD version by parsing phpinfo output
    $gdversion = 0;

    if (function_exists('gd_info')){
        $gd_info = gd_info();
        if (substr_count($gd_info['GD Version'], "2.")) {
            $gdversion = 2;
        } else if (substr_count($gd_info['GD Version'], "1.")) {
            $gdversion = 1;
        }

    } else {
        ob_start();
        phpinfo(8);
        $phpinfo = ob_get_contents();
        ob_end_clean();

        $phpinfo = explode("\n",$phpinfo);


        foreach ($phpinfo as $text) {
            $parts = explode('</td>',$text);
            foreach ($parts as $key => $val) {
                $parts[$key] = trim(strip_tags($val));
            }
            if ($parts[0] == "GD Version") {
                if (substr_count($parts[1], "2.0")) {
                    $parts[1] = "2.0";
                }
                $gdversion = intval($parts[1]);
            }
        }
    }

    return $gdversion;   // 1, 2 or 0
}


function moodle_needs_upgrading() {
/// Checks version numbers of Main code and all modules to see
/// if there are any mismatches ... returns true or false
    global $CFG;

    include_once("$CFG->dirroot/version.php");  # defines $version and upgrades
    if ($CFG->version) {
        if ($version > $CFG->version) {
            return true;
        }
        if ($mods = get_list_of_plugins("mod")) {
            foreach ($mods as $mod) {
                $fullmod = "$CFG->dirroot/mod/$mod";
                unset($module);
                if (!is_readable("$fullmod/version.php")) {
                    notify("Module '$mod' is not readable - check permissions");
                    continue;
                }
                include_once("$fullmod/version.php");  # defines $module with version etc
                if ($currmodule = get_record("modules", "name", $mod)) {
                    if ($module->version > $currmodule->version) {
                        return true;
                    }
                }
            }
        }
    } else {
        return true;
    }
    return false;
}


/// MISCELLANEOUS ////////////////////////////////////////////////////////////////////

function notify_login_failures() {
    global $CFG, $db;

    // notify admin users or admin user of any failed logins (since last notification).
    switch ($CFG->notifyloginfailures) {
        case 'mainadmin' :
            $recip = array(get_admin());
            break;
        case 'alladmins':
            $recip = get_admins();
            break;
    }
    
    if (empty($CFG->lastnotifyfailure)) {
        $CFG->lastnotifyfailure=0;
    }
    
    // we need to deal with the threshold stuff first. 
    if (empty($CFG->notifyloginthreshold)) {
        $CFG->notifyloginthreshold = 10; // default to something sensible.
    }

    $notifyipsrs = $db->Execute("SELECT ip FROM {$CFG->prefix}log WHERE time > {$CFG->lastnotifyfailure} 
                          AND module='login' AND action='error' GROUP BY ip HAVING count(*) > $CFG->notifyloginthreshold");

    $notifyusersrs = $db->Execute("SELECT info FROM {$CFG->prefix}log WHERE time > {$CFG->lastnotifyfailure} 
                          AND module='login' AND action='error' GROUP BY info HAVING count(*) > $CFG->notifyloginthreshold");
    
    if ($notifyipsrs) {
        $ipstr = '';
        while ($row = $notifyipsrs->FetchRow()) {
            $ipstr .= "'".$row['ip']."',";
        }
        $ipstr = substr($ipstr,0,strlen($ipstr)-1);
    }
    if ($notifyusersrs) {
        $userstr = '';
        while ($row = $notifyusersrs->FetchRow()) {
            $userstr .= "'".$row['info']."',";
        }
        $userstr = substr($userstr,0,strlen($userstr)-1);
    }

    if (strlen($userstr) > 0 || strlen($ipstr) > 0) {
        $count = 0;
        $logs = get_logs("time > {$CFG->lastnotifyfailure} AND module='login' AND action='error' "
                 .((strlen($ipstr) > 0 && strlen($userstr) > 0) ? " AND ( ip IN ($ipstr) OR info IN ($userstr) ) "
                 : ((strlen($ipstr) != 0) ? " AND ip IN ($ipstr) " : " AND info IN ($userstr) ")),"l.time DESC","","",$count);
        
        // if we haven't run in the last hour and we have something useful to report and we are actually supposed to be reporting to somebody
        if (is_array($recip) and count($recip) > 0 and ((time() - (60 * 60)) > $CFG->lastnotifyfailure) 
            and is_array($logs) and count($logs) > 0) {
       
            $message = '';
            $site = get_site();
            $subject = get_string('notifyloginfailuressubject','',$site->fullname);
            $message .= get_string('notifyloginfailuresmessagestart','',$CFG->wwwroot)
                 .(($CFG->lastnotifyfailure != 0) ? '('.userdate($CFG->lastnotifyfailure).')' : '')."\n\n";
            foreach ($logs as $log) {
                $log->time = userdate($log->time);
                $message .= get_string('notifyloginfailuresmessage','',$log)."\n";
            }
            $message .= "\n\n".get_string('notifyloginfailuresmessageend','',$CFG->wwwroot)."\n\n";
            foreach ($recip as $admin) {
                mtrace("Emailing $admin->username about ".count($logs)." failed login attempts");
                email_to_user($admin,get_admin(),$subject,$message);
            }
            $conf->name = "lastnotifyfailure";
            $conf->value = time();
            if ($current = get_record("config", "name", "lastnotifyfailure")) {
                $conf->id = $current->id;
                if (! update_record("config", $conf)) {
                    mtrace("Could not update last notify time");
                }

            } else if (! insert_record("config", $conf)) {
                mtrace("Could not set last notify time");
            }
        }
    }
}

function moodle_setlocale($locale='') {

    global $SESSION, $USER, $CFG;

    if ($locale) {
        $CFG->locale = $locale;
    } else if (!empty($CFG->courselang) and ($CFG->courselang != $CFG->lang) ) {
        $CFG->locale = get_string('locale');
    } else if (!empty($SESSION->lang) and ($SESSION->lang != $CFG->lang) ) {
        $CFG->locale = get_string('locale');
    } else if (!empty($USER->lang) and ($USER->lang != $CFG->lang) ) {
        $CFG->locale = get_string('locale');
    } else if (empty($CFG->locale)) {
        $CFG->locale = get_string('locale');
        set_config('locale', $CFG->locale);   // cache it to save lookups in future
    }
    setlocale (LC_TIME, $CFG->locale);
    setlocale (LC_COLLATE, $CFG->locale);

    if ($CFG->locale != 'tr_TR') {            // To workaround a well-known PHP bug with Turkish
        setlocale (LC_CTYPE, $CFG->locale);
    }
}

function moodle_strtolower ($string, $encoding='') {
/// Converts string to lowercase using most compatible  function available
    if (function_exists('mb_strtolower')) {
        if($encoding===''){
           return mb_strtolower($string);          //use multibyte support with default encoding
        } else {
           return mb_strtolower($string,$encoding); //use given encoding
        }
    } else {
        return strtolower($string);                // use common function what rely on current locale setting
    }
}

function count_words($string) {
/// Words are defined as things between whitespace
    $string = strip_tags($string);
    return count(preg_split("/\w\b/", $string)) - 1;
}

function random_string ($length=15) {
    $pool  = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $pool .= "abcdefghijklmnopqrstuvwxyz";
    $pool .= "0123456789";
    $poollen = strlen($pool);
    mt_srand ((double) microtime() * 1000000);
    $string = "";
    for ($i = 0; $i < $length; $i++) {
        $string .= substr($pool, (mt_rand()%($poollen)), 1);
    }
    return $string;
}


function getweek ($startdate, $thedate) {
/// Given dates in seconds, how many weeks is the date from startdate
/// The first week is 1, the second 2 etc ...

    if ($thedate < $startdate) {   // error
        return 0;
    }

    return floor(($thedate - $startdate) / 604800.0) + 1;
}

function generate_password($maxlen=10) {
/// returns a randomly generated password of length $maxlen.  inspired by
/// http://www.phpbuilder.com/columns/jesus19990502.php3

    global $CFG;

    $fillers = "1234567890!$-+";
    $wordlist = file($CFG->wordlist);

    srand((double) microtime() * 1000000);
    $word1 = trim($wordlist[rand(0, count($wordlist) - 1)]);
    $word2 = trim($wordlist[rand(0, count($wordlist) - 1)]);
    $filler1 = $fillers[rand(0, strlen($fillers) - 1)];

    return substr($word1 . $filler1 . $word2, 0, $maxlen);
}

function format_float($num, $places=1) {
/// Given a float, prints it nicely
    return sprintf("%.$places"."f", $num);
}

function swapshuffle($array) {
/// Given a simple array, this shuffles it up just like shuffle()
/// Unlike PHP's shuffle() ihis function works on any machine.

    srand ((double) microtime() * 10000000);
    $last = count($array) - 1;
    for ($i=0;$i<=$last;$i++) {
        $from = rand(0,$last);
        $curr = $array[$i];
        $array[$i] = $array[$from];
        $array[$from] = $curr;
    }
    return $array;
}

function swapshuffle_assoc($array) {
/// Like swapshuffle, but works on associative arrays

    $newkeys = swapshuffle(array_keys($array));
    foreach ($newkeys as $newkey) {
        $newarray[$newkey] = $array[$newkey];
    }
    return $newarray;
}

function draw_rand_array($array, $draws) {
/// Given an arbitrary array, and a number of draws,
/// this function returns an array with that amount
/// of items.  The indexes are retained.

    srand ((double) microtime() * 10000000);

    $return = array();

    $last = count($array);

    if ($draws > $last) {
        $draws = $last;
    }

    while ($draws > 0) {
        $last--;

        $keys = array_keys($array);
        $rand = rand(0, $last);

        $return[$keys[$rand]] = $array[$keys[$rand]];
        unset($array[$keys[$rand]]);

        $draws--;
    }

    return $return;
}

function microtime_diff($a, $b) {
    list($a_dec, $a_sec) = explode(" ", $a);
    list($b_dec, $b_sec) = explode(" ", $b);
    return $b_sec - $a_sec + $b_dec - $a_dec;
}

function make_menu_from_list($list, $separator=",") {
/// Given a list (eg a,b,c,d,e) this function returns
/// an array of 1->a, 2->b, 3->c etc

    $array = array_reverse(explode($separator, $list), true);
    foreach ($array as $key => $item) {
        $outarray[$key+1] = trim($item);
    }
    return $outarray;
}

function make_grades_menu($gradingtype) {
/// Creates an array that represents all the current grades that
/// can be chosen using the given grading type.  Negative numbers
/// are scales, zero is no grade, and positive numbers are maximum
/// grades.

    $grades = array();
    if ($gradingtype < 0) {
        if ($scale = get_record("scale", "id", - $gradingtype)) {
            return make_menu_from_list($scale->scale);
        }
    } else if ($gradingtype > 0) {
        for ($i=$gradingtype; $i>=0; $i--) {
            $grades[$i] = "$i / $gradingtype";
        }
        return $grades;
    }
    return $grades;
}

function course_scale_used($courseid,$scaleid) {
////This function returns the nummber of activities
////using scaleid in a courseid

    global $CFG;

    $return = 0;

    if (!empty($scaleid)) {
        if ($cms = get_course_mods($courseid)) {
            foreach ($cms as $cm) {
                //Check cm->name/lib.php exists
                if (file_exists($CFG->dirroot.'/mod/'.$cm->modname.'/lib.php')) {
                    include_once($CFG->dirroot.'/mod/'.$cm->modname.'/lib.php');
                    $function_name = $cm->modname.'_scale_used';
                    if (function_exists($function_name)) {
                        if ($function_name($cm->instance,$scaleid)) {
                            $return++;
                        }
                    }
                }
            }
        }
    }
    return $return;
}

function site_scale_used($scaleid,&$courses) {
////This function returns the nummber of activities
////using scaleid in the entire site

    global $CFG;

    $return = 0;

    if (!is_array($courses) || count($courses) == 0) {
        $courses = get_courses("all",false,"c.id,c.shortname");
    }

    if (!empty($scaleid)) {
        if (is_array($courses) && count($courses) > 0) {
            foreach ($courses as $course) {
                $return += course_scale_used($course->id,$scaleid);
            }
        }
    }
    return $return;
}

function make_unique_id_code($extra="") {

    $hostname = "unknownhost";
    if (!empty($_SERVER["HTTP_HOST"])) {
        $hostname = $_SERVER["HTTP_HOST"];
    } else if (!empty($_ENV["HTTP_HOST"])) {
        $hostname = $_ENV["HTTP_HOST"];
    } else if (!empty($_SERVER["SERVER_NAME"])) {
        $hostname = $_SERVER["SERVER_NAME"];
    } else if (!empty($_ENV["SERVER_NAME"])) {
        $hostname = $_ENV["SERVER_NAME"];
    }

    $date = gmdate("ymdHis");

    $random =  random_string(6);

    if ($extra) {
        return "$hostname+$date+$random+$extra";
    } else {
        return "$hostname+$date+$random";
    }
}


/**
* Function to check the passed address is within the passed subnet
*
* The parameter is a comma separated string of subnet definitions.
* Subnet strings can be in one of two formats:
*   1: xxx.xxx.xxx.xxx/xx
*   2: xxx.xxx
* Return boolean
* Code for type 1 modified from user posted comments by mediator at
* http://au.php.net/manual/en/function.ip2long.php
*
* @param    addr    the address you are checking
* @param    subnetstr    the string of subnet addresses
*/

function address_in_subnet($addr, $subnetstr) {

    $subnets = explode(",", $subnetstr);
    $found = false;
    $addr = trim($addr);

    foreach ($subnets as $subnet) {
        $subnet = trim($subnet);
        if (strpos($subnet, "/") !== false) { /// type 1

            list($ip, $mask) = explode('/', $subnet);
            $mask = 0xffffffff << (32 - $mask);
            $found = ((ip2long($addr) & $mask) == (ip2long($ip) & $mask));

        } else { /// type 2
            $found = (strpos($addr, $subnet) === 0);
        }

        if ($found) {
            continue;
        }
    }

    return $found;
}

function mtrace($string, $eol="\n") {
// For outputting debugging info 

    if (defined('STDOUT')) {
        fwrite(STDOUT, $string.$eol);
    } else {
        echo "$string$eol";
    }

    flush();
}

//Replace 1 or more slashes or backslashes to 1 slash
function cleardoubleslashes ($path) {
    return preg_replace('/(\/|\\\){1,}/','/',$path);
}

function zip_files ($originalfiles, $destination) {
//Zip an array of files/dirs to a destination zip file
//Both parameters must be FULL paths to the files/dirs

    global $CFG;

    //Extract everything from destination
    $path_parts = pathinfo(cleardoubleslashes($destination));
    $destpath = $path_parts["dirname"];       //The path of the zip file
    $destfilename = $path_parts["basename"];  //The name of the zip file
    $extension = $path_parts["extension"];    //The extension of the file

    //If no file, error
    if (empty($destfilename)) {
        return false;
    }

    //If no extension, add it
    if (empty($extension)) { 
        $extension = 'zip';
        $destfilename = $destfilename.'.'.$extension;
    }

    //Check destination path exists
    if (!is_dir($destpath)) {
        return false;
    }

    //Check destination path is writable. TODO!!

    //Clean destination filename
    $destfilename = clean_filename($destfilename);

    //Now check and prepare every file
    $files = array();
    $origpath = NULL;

    foreach ($originalfiles as $file) {  //Iterate over each file
        //Check for every file
        $tempfile = cleardoubleslashes($file); // no doubleslashes!
        //Calculate the base path for all files if it isn't set
        if ($origpath === NULL) {
            $origpath = rtrim(cleardoubleslashes(dirname($tempfile)), "/");
        }
        //See if the file is readable
        if (!is_readable($tempfile)) {  //Is readable
            continue;
        }
        //See if the file/dir is in the same directory than the rest
        if (rtrim(cleardoubleslashes(dirname($tempfile)), "/") != $origpath) {
            continue;
        }
        //Add the file to the array
        $files[] = $tempfile;
    }

    //Everything is ready:
    //    -$origpath is the path where ALL the files to be compressed reside (dir).
    //    -$destpath is the destination path where the zip file will go (dir).
    //    -$files is an array of files/dirs to compress (fullpath)
    //    -$destfilename is the name of the zip file (without path)

    //print_object($files);                  //Debug

    if (empty($CFG->zip)) {    // Use built-in php-based zip function

        include_once("$CFG->libdir/pclzip/pclzip.lib.php");
        $archive = new PclZip(cleardoubleslashes("$destpath/$destfilename"));
        if (($list = $archive->create($files, PCLZIP_OPT_REMOVE_PATH,$origpath) == 0)) {
            notice($archive->errorInfo(true));
            return false;
        }

    } else {                   // Use external zip program

        $filestozip = "";
        foreach ($files as $filetozip) {
            $filestozip .= escapeshellarg(basename($filetozip));
            $filestozip .= " ";
        }
        //Construct the command
        $separator = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' ? ' &' : ' ;';
        $command = 'cd '.escapeshellarg($origpath).$separator.
                    escapeshellarg($CFG->zip).' -r '.
                    escapeshellarg(cleardoubleslashes("$destpath/$destfilename")).' '.$filestozip;
        //All converted to backslashes in WIN
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $command = str_replace('/','\\',$command);
        }
        Exec($command);
    }
    return true;
}

function unzip_file ($zipfile, $destination = '', $showstatus = true) {
//Unzip one zip file to a destination dir
//Both parameters must be FULL paths
//If destination isn't specified, it will be the
//SAME directory where the zip file resides.

    global $CFG;
    
    //Extract everything from zipfile
    $path_parts = pathinfo(cleardoubleslashes($zipfile));
    $zippath = $path_parts["dirname"];       //The path of the zip file
    $zipfilename = $path_parts["basename"];  //The name of the zip file
    $extension = $path_parts["extension"];    //The extension of the file

    //If no file, error
    if (empty($zipfilename)) {
        return false;
    }

    //If no extension, error
    if (empty($extension)) {
        return false;
    }

    //If no destination, passed let's go with the same directory
    if (empty($destination)) {
        $destination = $zippath;
    }

    //Clear $destination
    $destpath = rtrim(cleardoubleslashes($destination), "/");

    //Check destination path exists
    if (!is_dir($destpath)) {
        return false;
    }

    //Check destination path is writable. TODO!!

    //Everything is ready:
    //    -$zippath is the path where the zip file resides (dir)
    //    -$zipfilename is the name of the zip file (without path)
    //    -$destpath is the destination path where the zip file will uncompressed (dir)

    if (empty($CFG->unzip)) {    // Use built-in php-based unzip function

        include_once("$CFG->libdir/pclzip/pclzip.lib.php");
        $archive = new PclZip(cleardoubleslashes("$zippath/$zipfilename"));
        if (!$list = $archive->extract(PCLZIP_OPT_PATH, $destpath,
                                       PCLZIP_CB_PRE_EXTRACT, 'unzip_cleanfilename')) {
            notice($archive->errorInfo(true));
            return false;
        }

    } else {                     // Use external unzip program

        $separator = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' ? ' &' : ' ;';
        $redirection = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' ? '' : ' 2>&1';

        $command = 'cd '.escapeshellarg($zippath).$separator.
                    escapeshellarg($CFG->unzip).' -o '.
                    escapeshellarg(cleardoubleslashes("$zippath/$zipfilename")).' -d '.
                    escapeshellarg($destpath).$redirection;
        //All converted to backslashes in WIN
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $command = str_replace('/','\\',$command);
        }
        Exec($command,$list);
    }

    //Display some info about the unzip execution
    if ($showstatus) {
        unzip_show_status($list,$destpath);
    }
  
    return true;
}

function unzip_cleanfilename ($p_event, &$p_header) {
//This function is used as callback in unzip_file() function
//to clean illegal characters for given platform and to prevent directory traversal.
//Produces the same result as info-zip unzip.
    $p_header['filename'] = ereg_replace('[[:cntrl:]]', '', $p_header['filename']); //strip control chars first!
    $p_header['filename'] = ereg_replace('\.\.+', '', $p_header['filename']); //directory traversal protection
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        $p_header['filename'] = ereg_replace('[:*"?<>|]', '_', $p_header['filename']); //replace illegal chars
        $p_header['filename'] = ereg_replace('^([a-zA-Z])_', '\1:', $p_header['filename']); //repair drive letter
    } else {
        //Add filtering for other systems here
        // BSD: none (tested)
        // Linux: ??
        // MacosX: ??
    }    
    $p_header['filename'] = cleardoubleslashes($p_header['filename']); //normalize the slashes/backslashes
    return 1;
}

/**
* Function to raise the memory limit to a new value.
* Will respect the memory limit if it is higher, thus allowing
* settings in php.ini, apache conf or command line switches
* to override it
*
* The memory limit should be expressed with a string (eg:'64M')
* 
* Return boolean
*
* @param    value    string with the new memory limit
*/
function raise_memory_limit ($newlimit) {

    if (empty($newlimit)) { 
        return false;
    }
    
    $cur = @ini_get('memory_limit');
    if (empty($cur)) {
        // if php is compiled without --enable-memory-limits
        // apparently memory_limit is set to ''
        $cur=0;
    } else {
        if ($cur == -1){
            return true; // unlimited mem!
        }
      $cur = get_real_size($cur);
    }
    
    $new = get_real_size($newlimit);
    if ($new > $cur) {
        ini_set('memory_limit', $newlimit);
        return true;    
    }
    return false;
}

function unzip_show_status ($list,$removepath) {
//This function shows the results of the unzip execution
//depending of the value of the $CFG->zip, results will be
//text or an array of files.

    global $CFG;

    if (empty($CFG->unzip)) {    // Use built-in php-based zip function
        $strname = get_string("name");
        $strsize = get_string("size");
        $strmodified = get_string("modified");
        $strstatus = get_string("status");
        echo "<table cellpadding=\"4\" cellspacing=\"2\" border=\"0\" width=640>";
        echo "<tr><th align=left>$strname</th>";
        echo "<th align=right>$strsize</th>";
        echo "<th align=right>$strmodified</th>";
        echo "<th align=right>$strstatus</th></tr>";
        foreach ($list as $item) {
            echo "<tr>";
            $item['filename'] = str_replace(cleardoubleslashes($removepath).'/', "", $item['filename']);
            print_cell("left", $item['filename']);
            if (! $item['folder']) {
                print_cell("right", display_size($item['size']));
            } else {
                echo "<td>&nbsp;</td>";
            }
            $filedate  = userdate($item['mtime'], get_string("strftimedatetime"));
            print_cell("right", $filedate);
            print_cell("right", $item['status']);
            echo "</tr>";
        }
        echo "</table>";

    } else {                   // Use external zip program
        print_simple_box_start("center");
        echo "<PRE>";
        foreach ($list as $item) {
            echo str_replace(cleardoubleslashes($removepath.'/'), '', $item).'<br />';
        }
        echo "</PRE>";
        print_simple_box_end();
    }
}

// vim:autoindent:expandtab:shiftwidth=4:tabstop=4:tw=140:
?>
