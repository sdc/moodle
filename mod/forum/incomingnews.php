<?php
/**
 * Automatic news-(forum post)-adding script. In lieu of a proper web service (wait for Moodle 2!).
 * Takes POST-data (to include a pre-shared key) and inserts a new discussion and post
 *     onto Moodle's front page. Could be modified to add a new discussion and post to ANY forum.
 * Note: Designed to be used by automated scripts, NOT by people.
 *
 * Author:  Paul Vaughan
 * Date:    28th September 2011
 * Version: 1.1.2
 * Notes:   Modded the script to post news as Moodle's admin user if the user doesn't exit
 *              in Moodle (this happened after starting over with Moodle 2).
 *          Also added code to remove instancesempty <p> tags.
 *
 * Author:  Paul Vaughan
 * Date:    12th September 2011
 * Version: 1.1.1
 * Notes:   Updated the 'user not known' error to report the user which wasn't found.
 *              Many won't exist in Moodle yet.
 *
 * Date:    7th September 2011
 * Version: 1.1.0
 * Notes:   Updating the script to work with Moodle 2. I am waiting on the redevelopment of the
 *              News System and then I'll change to using Web Services instead.
 *
 * Date:    18th January 2010
 * Version: 1.0.3
 * Notes:   Modified the GET script to make this POST script
 *
 * What we are really doing is adding a new discussion to an existing forum and then adding a new post to it.
 * So we need to create both the new discussion and the new post and ensure they play nicely together.
 */

/**
 * Process:
 * 1. Check remote host is an allowed host (using Moodle's getremoteaddr() function for this)
 * 2. Take in RAW POST data, removing the first 5 (unnecessary) characters
 * 3. URL-decode the data
 * 4. Parse the XML
 * 5. Check for existence of token (PSK) and a legit username/Moodle user id
 * 6. Strip tags out of the subject and possibly add slashes
 * 7. Possibly add slashes to the message
 * 8. Add in to the db tables, linking one to the other
 * 9. Add to Moodle log. Add any errors to the Apache error log too.
 */

/**
 * Need this for the db config and functions
 */
require_once('../../config.php');

/**
 * Constants
 */
// Version details
define ('VERSION', '20110928-1.1.2');
// Set to true, the script will dump messages to the screen (if run from the URL).
// Set to false, the script will fail quietly (with logging) unless it utterly screws up.
define('DEBUG', true);
// The front page of Moodle is always course 1.
define('COURSE', 1);
// The front page does not necessarily have the first forum, so check and specify here.
// Check mdl_forum for the 'id' and 'course' numbers if you want to add to a different forum
define('FORUM', 1);
// The pre-shared key needed to authenticate incoming data.
define('TOKEN', $CFG->incomingnewspresharedkey);
// Defines wether addslashes() is used or not: development and production may require different settings.
define('SLASHES', false);

/**
 * Success and failure functions.
 */
// Used when the scripts completes successfully.
function win($discussion_id) {
    global $CFG;
    // Return header
    header($_SERVER["SERVER_PROTOCOL"].' 201 Created (FULL OF WIN!)', TRUE, 201);
    header('POSTURL: '.$CFG->wwwroot.'/mod/forum/discuss.php?d='.$discussion_id);
    // Add to Moodle log
    add_to_log(1, 'forum', 'add', 'discuss.php?d='.$discussion_id, 'News System feed ('.VERSION.') - Success.', 0, USERID);
}
// Used when success != true
function fail($err) {
    // Return header on fail.
    header($_SERVER["SERVER_PROTOCOL"].' 422 Unprocessable Entity ('.$err.')', TRUE, 422);
    // Add to Moodle log
    add_to_log(1, 'forum', 'add', '', 'News System feed ('.VERSION.') - Failure ('.$err.') ('.getremoteaddr().')', 0, USERID);
    // Add to Apache error log
    error_log('[SDC_NEWS_FEED] Failure ('.$err.') ('.getremoteaddr().')');
}

/**
 * Check for allowed hosts. Could create an array and check against it but we
 * don't want more than about 4 allowed 'users'.
 */
$remote = getremoteaddr(); // Moodle's most reliable way of getting the remote host's IP address.
if ($remote != '172.21.4.85' && $remote != '172.21.11.5' && $remote != '172.20.1.12' && $remote != '172.20.1.50') { // PV, KH, Dev server, webapp0.
    /**
     * Produce a 'fail' header + Moodle log + Apache log with a specific number so
     * the failure can be traced. It gives enough detail that errors can be
     * traced but no information which could easily be used to compromise the server.
     */
    fail('HOST_NOT_APPROVED');
    if(DEBUG == true) {
        die('Not an approved host. Stopped.');
    }
    exit;
}

/**
 * Get and check the (now raw) POST data - if it doesn't exist, don't do anything.
 */
$the_xml= file_get_contents("php://input");
if(empty($the_xml)) {
    fail('NO_RAW_POST_DATA');
    if(DEBUG == true) {
        die('POST data empty. Stopped.');
    }
    exit;
} else {

    // URL-decode it
    $incoming_xml = urldecode($the_xml);
    if(DEBUG == true) {
        echo 'POST data URL-decoded: '.$incoming_xml.'<br />';
    }
}

/**
 * Parse the XML.
 */
$xmlDoc = new DOMDocument();
$xmlDoc->loadXML($incoming_xml);
// Load from some file or another (debugging).
//$xmlDoc->load("sample.xml");

// 'Select' and use the 'item' tag
$item = $xmlDoc->getElementsByTagName('item');
// Check for existence of news items
if ($item->length == 0) {
    fail('NO_NEWS_ITEMS');
    if(DEBUG == true) {
        die('No items. Stopped.');
    }
    exit;
}

/**
 * Cycle through $item and process each set of nodes found
 */
foreach($item as $value) {
    /**
     * Get values out of the XML.
     */
    $tokens     = $value->getElementsByTagName('token');
    $token      = $tokens->item(0)->nodeValue;

    $users      = $value->getElementsByTagName('user');
    $user       = $users->item(0)->nodeValue;

    $subjects   = $value->getElementsByTagName('title');
    $subject    = $subjects->item(0)->nodeValue;

    $messages   = $value->getElementsByTagName('body');
    $message    = $messages->item(0)->nodeValue;

    $date_ls    = $value->getElementsByTagName('launch-date');
    $date_l     = $date_ls->item(0)->nodeValue;

    $date_rs    = $value->getElementsByTagName('removal-date');
    $date_r     = $date_rs->item(0)->nodeValue;

    /**
     * Check the secure token and bail out if not identical.
     */
    if ($token != TOKEN) {
        fail('SECURE_TOKEN_MISMATCH');
        if(DEBUG == true) {
            die('Tokens do not match, cannot validate incoming data. Stopped.');
        }
        exit;
    }

    // Check the user and get the Moodle id
    if(empty($user)) {
        fail('NO_USER_FOUND');
        if(DEBUG == true) {
            die('User empty. Stopped.');
        }
        exit;
    } else {
        // Pass the userid to the Moodle db and get back the Moodle ID.
        $moodleuser = $DB->get_field('user', 'id', array('username' => $user));
        if(!$moodleuser) {
            fail('USER_NOT_FOUND_IN_MOODLE['.$user.']');
            if(DEBUG == true) {
                echo 'Could not get Moodle user from database. Using a default user instead.';
                // instead of an utter fail, use the Admin Istrator account...
                $moodleuser = 2;
            }
        } else {
            DEFINE('USERID', $moodleuser);
            if(DEBUG == true) {
                echo('Moodle user ID gleaned from the Moodle database. Continuing...');
            }
        }
    }

    /**
     * Check the $subject var
     */
    if(empty($subject)) {
        fail('NO_SUBJECT');
        if(DEBUG == true) {
            die('Subject empty. Stopped.');
        }
        exit;
    } else {
        // Strip out the HTML entities, but there shouldn't be any there in the first place
        $subject = strip_tags($subject);
        if(DEBUG == true) {
            echo '$subject HTML tags stripped: '.$subject.'<br />';
        }

        // Add slashes.
        if(SLASHES == true) {
            $subject = addslashes($subject);
            if(DEBUG == true) {
                echo '$subject has had slashes added: '.$subject.'<br />';
            }
        }
    }

    /**
     * Check the $message var
     */
    if(empty($message)) {
        fail('NO_BODY');
        if(DEBUG == true) {
            die('Message empty. Stopped.');
        }
        exit;
    } else {
        // Remove empty <p> tags.
        $message = str_replace(array('<p></p>', '<p> </p>', '<p>&nbsp;</p>'), '', $message);
        if(DEBUG == true) {
            echo '$message has had empty <p> tags removed: '.$message.'<br />';
        }
        // Add slashes.
        if(SLASHES == true) {
            $message = addslashes($message);
            if(DEBUG == true) {
                echo '$message has had slashes added: '.$message.'<br />';
            }
//            $message = htmlspecialchars($message, ENT_QUOTES);
        }
    }

    /**
     * Check the $date_l var
     */
    if(empty($date_l)) {
        // Having an empty date is okay, we just pass 0 to Moodle.
        $timestart = 0;
    } else {
        // Process the 'datetime' format into Unix Epoch format.
        $timestart = strtotime($date_l);
        if ($timestart == false) {
            fail('TIMESTART_CONVERSION_FAILURE');
            if(DEBUG == true) {
                die('Timestart conversion failed.');
            }
        } else {
            echo '$timestart has been converted to Unix epoch: '.$timestart.'<br />';
        }
    }

    /**
     * Check the $date_r var
     */
    if(empty($date_r)) {
        $timeend = 0;
    } else {
        $timeend = strtotime($date_r);
        if ($timeend == false) {
            fail('TIMESTART_CONVERSION_FAILURE');
            if(DEBUG == true) {
                die('Timeend conversion failed.');
            }
        } else {
            echo '$timeend has been converted to Unix epoch: '.$timeend.'<br />';
        }
    }

    /**
     * Everything checks out, so go on and get it processed
     */

    // Use this var for timestamps in both tables.
    $now = time();

    /**
     * Create and populate the new DISCUSSION object. Code borrowed from mod/forum/post.php
     */
    $newdiscussion = new stdClass();
    $newdiscussion->course          = COURSE;       // Constant defined at the top of this script.
    $newdiscussion->forum           = FORUM;        // Constant defined at the top of this script.
    $newdiscussion->name            = $subject;
    $newdiscussion->userid          = USERID;
    $newdiscussion->groupid         = -1;
    $newdiscussion->assessed        = 1;
    $newdiscussion->timemodified    = $now;
    $newdiscussion->usermodified    = USERID;
    $newdiscussion->timestart       = $timestart;
    $newdiscussion->timeend         = $timeend;

    // Insert $newdiscussion object into the database, getting the id of the new row.
    $newdiscussion_id = $DB->insert_record('forum_discussions', $newdiscussion);
    if (!$newdiscussion_id) {
        fail('NEW_DISCUSSION_INSERT_FAILURE');
        if(DEBUG == true) {
            die('Could not insert new discussion. Stopped.');
        }
        exit;
    }

    /**
     * Create and populate the new POST object.
     */
    $newpost = new stdClass();
    $newpost->discussion    = $newdiscussion_id;    // We get this from the result of the insert_record function, above.
    $newpost->parent        = 0;
    $newpost->userid        = USERID;
    $newpost->created       = $now;
    $newpost->modified      = $now;
    $newpost->subject       = $subject;
    /**
     * If the user is trusted (which it should be) add this in which // bypasses text cleaning. Defined within Moodle.
     * Unsure if needed in Moodle 2.
     */
    //$newpost->message       = TRUSTTEXT.$message;
    $newpost->message       = $message;
    $newpost->format        = 1;
    $newpost->mailnow       = 0; // 0 is for no email sent, 1 is to send out an email.

    // Insert $newpost into the database, getting the id of the new row.
    $newpost_id = $DB->insert_record('forum_posts', $newpost);
    if (!$newpost_id) {
        fail('NEW_POST_INSERT_FAILURE');
        if(DEBUG == true) {
            die('Could not insert new post. Stopped.');
        }
        exit;
    }

    /**
     * Create and populate the update discussion object
     */
    $updatediscussion = new stdClass();
    $updatediscussion->id           = $newdiscussion_id;
    $updatediscussion->firstpost    = $newpost_id;

    // Update the new discussion row, getting the id of the new row.
    if (!$DB->update_record('forum_discussions', $updatediscussion)) {
        fail('DISCUSSION_UPDATE_FAILURE');
        if(DEBUG == true) {
            die('Could not update new discussion with first post id. Stopped.');
        }
        exit;
    }

    /**
     * Send headers out for each news item written.
     */
    win($newdiscussion_id);

} // End looping through all the XML items

exit;
?>
