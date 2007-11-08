<?php
/**
* Global Search Engine for Moodle
* add-on 1.8+ : Valery Fremaux [valery.fremaux@club-internet.fr] 
* 2007/08/02
*
* document handling for chat activity module
* This file contains the mapping between a chat history and it's indexable counterpart,
*
* Functions for iterating and retrieving the necessary records are now also included
* in this file, rather than mod/chat/lib.php
*
* @license http://www.gnu.org/copyleft/gpl.html GNU Public License
* @package search
* @version 2007110400
**/

require_once("$CFG->dirroot/search/documents/document.php");
require_once("$CFG->dirroot/mod/chat/lib.php");

/* 
* a class for representing searchable information
* 
**/
class ChatTrackSearchDocument extends SearchDocument {

    /**
    * constructor
    *
    */
    public function __construct(&$chatsession, $chat_module_id, $course_id, $group_id, $context_id) {
        // generic information; required
        $doc->docid         = $chat_module_id.'-'.$chatsession['sessionstart'].'-'.$chatsession['sessionend'];
        $doc->documenttype  = SEARCH_TYPE_CHAT;
        $doc->itemtype      = 'session';
        $doc->contextid     = $context_id;

        $duration           = $chatsession['sessionend'] - $chatsession['sessionstart'];
        // we cannot call userdate with relevant locale at indexing time.
        $doc->title         = get_string('chatreport', 'chat').' '.get_string('openedon', 'search').' TT_'.$chatsession['sessionstart'].'_TT ('.get_string('duration', 'search').' : '.get_string('numseconds', '', $duration).')';
        $doc->date          = $chatsession['sessionend'];
        
        //remove '(ip.ip.ip.ip)' from chat author list
        $doc->author        = preg_replace('/\(.*?\)/', '', $chatsession['authors']);
        $doc->contents      = $chatsession['content'];
        $doc->url           = chat_make_link($chat_module_id, $chatsession['sessionstart'], $chatsession['sessionend']);
        
        // module specific information; optional
        $data->chat         = $chat_module_id;
        
        // construct the parent class
        parent::__construct($doc, $data, $course_id, $group_id, 0, PATH_FOR_SEARCH_TYPE_CHAT);
    } //constructor
} //ChatTrackSearchDocument


/**
* constructs a valid link to a chat content
* @param cm_id the chat course module
* @param start the start time of the session
* @param end th end time of the session
* @return a well formed link to session display
*/
function chat_make_link($cm_id, $start, $end) {
    global $CFG;

    return $CFG->wwwroot.'/mod/chat/report.php?id='.$cm_id.'&amp;start='.$start.'&amp;end='.$end;
} //chat_make_link

/**
* fetches all the records for a given session and assemble them as a unique track
* we revamped here the code of report.php for making sessions, but without any output.
* note that we should collect sessions "by groups" if groupmode() is SEPARATEGROUPS.
* @param chat_id the database
* @return an array of objects representing the chat sessions.
*/
function chat_get_session_tracks($chat_id, $fromtime = 0, $totime = 0) {
    global $CFG;
    
    $chat = get_record('chat', 'id', $chat_id);
    $course = get_record('course', 'id', $chat->course);
    $coursemodule = get_field('modules', 'id', 'name', 'data');
    $cm = get_record('course_modules', 'course', $course->id, 'module', $coursemodule, 'instance', $chat->id);
    $groupmode = groupmode($course, $cm);

    $fromtimeclause = ($fromtime) ? "AND timestamp >= {$fromtime}" : ''; 
    $totimeclause = ($totime) ? "AND timestamp <= {$totime}" : ''; 
    $tracks = array();
    $messages = get_records_select('chat_messages', "chatid = '{$chat_id}' $fromtimeclause $totimeclause", "timestamp DESC");
    if ($messages){
        // splits discussions against groups
        $groupedMessages = array();
        if ($groupmode != SEPARATEGROUPS){
            foreach($messages as $aMessage){
                $groupedMessages[$aMessage->groupid][] = $aMessage;
            }
        }
        else{
            $groupedMessages[-1] = &$messages;
        }
        $sessiongap = 5 * 60;    // 5 minutes silence means a new session
        $sessionend = 0;
        $sessionstart = 0;
        $sessionusers = array();
        $lasttime = time();
    
        foreach ($groupedMessages as $groupId => $messages) {  // We are walking BACKWARDS through the messages
            $messagesleft = count($messages);
            foreach ($messages as $message) {  // We are walking BACKWARDS through the messages
                $messagesleft --;              // Countdown
    
                if ($message->system) {
                    continue;
                }
                // we are within a session track
                if ((($lasttime - $message->timestamp) < $sessiongap) and $messagesleft) {  // Same session
                    if (count($tracks) > 0){
                        if ($message->userid) {       // Remember user and count messages
                            $tracks[count($tracks) - 1]->sessionusers[$message->userid] = $message->userid;
                            // update last track (if exists) record appending content (remember : we go backwards)
                        }
                        $tracks[count($tracks) - 1]->content .= ' '.$message->message;
                        $tracks[count($tracks) - 1]->sessionstart = $message->timestamp;
                    }
                } 
                // we initiate a new session track (backwards)
                else {
                    $track = new Object();
                    $track->sessionend = $message->timestamp;
                    $track->sessionstart = $message->timestamp;
                    $track->content = $message->message;
                    // reset the accumulator of users
                    $track->sessionusers = array();
                    $track->sessionusers[$message->userid] = $message->userid;
                    $track->groupid = $groupId;
                    $tracks[] = $track;
                } 
                $lasttime = $message->timestamp;
            } 
        } 
    } 
    return $tracks;
} //chat_get_session_tracks

/**
* part of search engine API
*
*/
function chat_iterator() {
    $chatrooms = get_records('chat');
    return $chatrooms;
} //chat_iterator

/**
* part of search engine API
*
*/
function chat_get_content_for_index(&$chat) {
    $documents = array();
    $course = get_record('course', 'id', $chat->course);
    $coursemodule = get_field('modules', 'id', 'name', 'chat');
    $cm = get_record('course_modules', 'course', $chat->course, 'module', $coursemodule, 'instance', $chat->id);
    if ($cm){
        $context = get_context_instance(CONTEXT_MODULE, $cm->id);
    
        // getting records for indexing
        $sessionTracks = chat_get_session_tracks($chat->id);
        if ($sessionTracks){
            foreach($sessionTracks as $aTrackId => $aTrack) {
                foreach($aTrack->sessionusers as $aUserId){
                    $user = get_record('user', 'id', $aUserId);
                    $aTrack->authors = ($user) ? $user->firstname.' '.$user->lastname : '' ;
                    $documents[] = new ChatTrackSearchDocument(get_object_vars($aTrack), $cm->id, $chat->course, $aTrack->groupid, $context->id);
                }
            }
        }
        return $documents;
    }
    return array();
} //chat_get_content_for_index

/**
* returns a single data search document based on a chat_session id
* chat session id is a text composite identifier made of :
* - the chat id
* - the timestamp when the session starts
* - the timestamp when the session ends
* @param id the multipart chat session id
* @param itemtype the type of information (session is the only type)
*/
function chat_single_document($id, $itemtype) {
    list($chat_id, $sessionstart, $sessionend) = split('-', $id);
    $chat = get_record('chat', 'id', $chat_id);
    $course = get_record('course', 'id', $chat->course);
    $coursemodule = get_field('modules', 'id', 'name', 'chat');
    $cm = get_record('course_modules', 'course', $course->id, 'module', $coursemodule, 'instance', $chat->id);
    if ($cm){
        $context = get_context_instance(CONTEXT_MODULE, $cm->id);
    
        // should be only one
        $tracks = chat_get_session_tracks($chat->id, $sessionstart, $sessionstart);
        if ($tracks){
            $aTrack = $tracks[0];
            $document = new ChatTrackSearchDocument(get_object_vars($aTrack), $cm->id, $chat->course, $aTrack->groupid, $context->id);
        }
        return $document;
    }
    return null;
} //chat_single_document

/**
* dummy delete function that packs id with itemtype.
* this was here for a reason, but I can't remember it at the moment.
*
*/
function chat_delete($info, $itemtype) {
    $object->id = $info;
    $object->itemtype = $itemtype;
    return $object;
} //chat_delete

/**
* returns the var names needed to build a sql query for addition/deletions
* // TODO chat indexable records are virtual. Should proceed in a special way 
*/
function chat_db_names() {
    //[primary id], [table name], [time created field name], [time modified field name]
    return null;
} //chat_db_names

/**
* this function handles the access policy to contents indexed as searchable documents. If this 
* function does not exist, the search engine assumes access is allowed.
* When this point is reached, we already know that : 
* - user is legitimate in the surrounding context
* - user may be guest and guest access is allowed to the module
* - the function may perform local checks within the module information logic
* @param path the access path to the module script code
* @param itemtype the information subclassing (usefull for complex modules, defaults to 'standard')
* @param this_id the item id within the information class denoted by entry_type. In chats, this id 
* points out a session history which is a close sequence of messages.
* @param user the user record denoting the user who searches
* @param group_id the current group used by the user when searching
* @return true if access is allowed, false elsewhere
*/
function chat_check_text_access($path, $itemtype, $this_id, $user, $group_id, $context_id){
    global $CFG;
    
    include_once("{$CFG->dirroot}/{$path}/lib.php");

    list($chat_id, $sessionstart, $sessionend) = split('-', $id);

    // get the chat session and all related stuff
    $chat = get_record('chat', 'id', $chat_id);
    $context = get_record('context', 'id', $context_id);
    $cm = get_record('course_modules', 'id', $context->instanceid);
    // $cm = get_coursemodule_from_instance('chat', $chat->id, $chat->course);
    // $context = get_context_instance(CONTEXT_MODULE, $cm->id);

    if (!$cm->visible and !has_capability('moodle/course:viewhiddenactivities', $context)){
        if (!empty($CFG->search_access_debug)) echo "search reject : hidden chat ";
        return false;
    }
    
    //group consistency check : checks the following situations about groups
    // trap if user is not same group and groups are separated
    $current_group = get_current_group($course->id);
    $course = get_record('course', 'id', $chat->course);
    if ((groupmode($course, $cm) == SEPARATEGROUPS) && !ismember($group_id) && !has_capability('moodle/site:accessallgroups', $context)){ 
        if (!empty($CFG->search_access_debug)) echo "search reject : chat element is in separated group ";
        return false;
    }

    //ownership check : checks the following situations about user
    // trap if user is not owner and has cannot see other's entries
    // TODO : typically may be stored into indexing cache
    if (!has_capability('mod/chat:readlog', $context)){
        if (!empty($CFG->search_access_debug)) echo "search reject : cannot read past sessions ";
        return false;
    }
        
    return true;
} //chat_check_text_access

/**
* this call back is called when displaying the link for some last post processing
*
*/
function chat_link_post_processing($title){
     setLocale(LC_TIME, substr(current_language(), 0, 2));
     $title = preg_replace('/TT_(.*)_TT/e', "userdate(\\1)", $title);
     return $title;
} //chat_link_post_processing
?>