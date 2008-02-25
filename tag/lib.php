<?php // $Id$

/**
 * lib.php - moodle tag library
 *
 * @version: $Id$
 * @licence http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package moodlecore
 *
 * A "tag string" is always a rawurlencode'd string. This is the same behavior 
 * as http://del.icio.us
 * @see http://www.php.net/manual/en/function.urlencode.php
 *
 * Tag strings : you can use any character in tags, except the comma (which is 
 * the separator) and the '\' (backslash).  Note that many spaces (or other 
 * blank characters) will get "compressed" into one.
 *
 * A "record" is a php array (note that an object will work too) that contains 
 * the following variables : 
 *  - type: the table containing the record that we are tagging (eg: for a
 *    blog, this is table 'post', and for a user it is 'user')
 *  - id: the id of the record 
 *
 * TODO: turn this into a full-fledged categorization system. This could start 
 * by modifying (removing, probably) the 'tag type' to use another table 
 * describing the relationship between tags (parents, sibling, etc.), which 
 * could then be merged with the 'course categorization' system...
 *
 * BASIC INSTRUCTIONS : 
 *  - to "tag a blog post" (for example): 
 *      tag_set('post', $blog_post->id, $array_of_tags);
 *
 *  - to "remove all the tags on a blog post":
 *      tag_set('post', $blog_post->id, array());
 *
 * Tag set will create tags that need to be created.  
 */

define('TAG_RETURN_ARRAY', 0);
define('TAG_RETURN_OBJECT', 1);
define('TAG_RETURN_TEXT', 2);
define('TAG_RETURN_HTML', 3);

define('TAG_CASE_LOWER', 0);
define('TAG_CASE_ORIGINAL', 1);

require_once($CFG->dirroot .'/tag/locallib.php');

///////////////////////////////////////////////////////
/////////////////// PUBLIC TAG API ////////////////////

/**
 * Delete one or more tag, and all their instances if there are any left.
 * 
 * @param mixed $tagids one tagid (int), or one array of tagids to delete
 * @return bool true on success, false otherwise 
 */
function tag_delete($tagids) {

    if (!is_array($tagids)) {
        $tagids = array($tagids);
    }

    $success = true;
    foreach( $tagids as $tagid ) {
        if (is_null($tagid)) { // can happen if tag doesn't exists
            continue;
        }
        // only delete the main entry if there were no problems deleting all the 
        // instances - that (and the fact we won't often delete lots of tags) 
        // is the reason for not using delete_records_select()
        if ( delete_records('tag_instance', 'tagid', $tagid) ) {
            $success &= (bool) delete_records('tag', 'id', $tagid);
        }
    }

    return $success;
}

/**
 * Delete one instance of a tag.  If the last instance was deleted, it will
 * also delete the tag, unless it's type is 'official'.
 *
 * @param array $record the record for which to remove the instance
 * @param int $tagid the tagid that needs to be removed
 * @return bool true on success, false otherwise
 */
function tag_delete_instance($record, $tagid) {
    global $CFG;

    if ( delete_records('tag_instance', 'tagid', $tagid, 'itemtype', $record['type'], 'itemid', $record['id']) ) {
        if ( !record_exists_sql('SELECT * FROM '. $CFG->prefix .'tag tg, '. $CFG->prefix .'tag_instance ti '.
                'WHERE (tg.id = ti.tagid AND ti.tagid = '. $tagid .') OR '.
                '(tg.id = '. $tagid .' AND tg.tagtype = "official")') ) {
            return tag_delete($tagid);
        }
    } else {
        return false;
    }
}

/**
 * Function that returns the name that should be displayed for a specific tag
 *
 * @param object $tag_object a line out of tag table, as returned by the adobd functions
 * @return string
 */
function tag_display_name($tag_object) {

    global $CFG;

    if(!isset($tag_object->name)) {
        return '';
    }

    if( empty($CFG->keeptagnamecase) ) {
        //this is the normalized tag name
        $textlib = textlib_get_instance();
        return htmlspecialchars($textlib->strtotitle($tag_object->name));
    }
    else {
        //original casing of the tag name
        return htmlspecialchars($tag_object->rawname);
    }
}

/**
 * Find all records tagged with a tag of a given type ('post', 'user', etc.)
 *
 * @param string $tag tag to look for
 * @param string $type type to restrict search to.  If null, every matching
 *     record will be returned
 * @return array of matching objects, indexed by record id, from the table containing the type requested
 */
function tag_find_records($tag, $type) {
    
    global $CFG;

    if (!$tag || !$type) {
        return array();
    }

    $tagid = tag_get_id($tag);

    $query = "SELECT it.* ".
        "FROM {$CFG->prefix}{$type} it INNER JOIN {$CFG->prefix}tag_instance tt ON it.id = tt.itemid ".
        "WHERE tt.itemtype = '{$type}' AND tt.tagid = '{$tagid}'";
    
    return get_records_sql($query); 
}

/**
 * Get the array of db record of tags associated to a record (instances).  Use 
 * tag_get_tags_csv to get the same information in a comma-separated string.
 *
 * @param array $record the record for which we want to get the tags 
 * @param string $type the tag type (either 'default' or 'official'). By default,
 *     all tags are returned.
 * @return array the array of tags
 */
function tag_get_tags($record, $type=null) {
    
    global $CFG;

    if ($type) {
        $type = "AND tg.tagtype = '$type'";
    }
    
    $tags = get_records_sql('SELECT tg.id, tg.tagtype, tg.name, tg.rawname, tg.flag, ti.ordering '.
        'FROM '. $CFG->prefix .'tag_instance ti INNER JOIN '. $CFG->prefix .'tag tg ON tg.id = ti.tagid '.
        'WHERE ti.itemtype = "'. $record['type'] .'" AND ti.itemid = "'. $record['id'] .'" '. $type .' '.
        'ORDER BY ti.ordering ASC');
    // This version of the query, reversing the ON clause, "correctly" returns 
    // a row with NULL values for instances that are still in the DB even though 
    // the tag has been deleted.  This shouldn't happen, but if it did, using 
    // this query could help "clean it up".  This causes bugs at this time.
    //$tags = get_records_sql('SELECT ti.tagid, tg.tagtype, tg.name, tg.rawname, tg.flag, ti.ordering '.
    //    'FROM '. $CFG->prefix .'tag_instance ti LEFT JOIN '. $CFG->prefix .'tag tg ON ti.tagid = tg.id '.
    //    'WHERE ti.itemtype = "'. $record['type'] .'" AND ti.itemid = "'. $record['id'] .'" '. $type .' '.
    //    'ORDER BY ti.ordering ASC');

    if (!$tags) { 
        return array();
    } else {
        return $tags;
    }
}

/**
 * Get the array of tags display names, indexed by id.
 * 
 * @param array $record the record for which we want to get the tags
 * @param string $type the tag type (either 'default' or 'official'). By default,
 *     all tags are returned.
 * @return array the array of tags (with the value returned by tag_display_name), indexed by id
 */
function tag_get_tags_array($record, $type=null) {
    $tags = array();
    foreach(tag_get_tags($record, $type) as $tag) {
        $tags[$tag->id] = tag_display_name($tag);
    }
    return $tags;
}

/**
 * Get a comma-separated string of tags associated to a record.  Use tag_get_tags
 * to get the same information in an array.
 *
 * @param array $record the record for which we want to get the tags
 * @param int $html either TAG_RETURN_HTML or TAG_RETURN_TEXT, depending
 *     on the type of output desired
 * @param string $type either 'official' or 'default', if null, all tags are
 *     returned
 * @return string the comma-separated list of tags.
 */
function tag_get_tags_csv($record, $html=TAG_RETURN_HTML, $type=null) {
    global $CFG;

    $tags_names = array();
    foreach( tag_get_tags($record, $type) as $tag ) {
        if ($html == TAG_RETURN_TEXT) {
            $tags_names[] = tag_display_name($tag);
        } else { // TAG_RETURN_HTML
            $tags_names[] = '<a href="'. $CFG->wwwroot .'/tag/index.php?tag='. rawurlencode($tag->name) .'">'. tag_display_name($tag) .'</a>';
        }
    }
    return implode(', ', $tags_names);
}

/**
 * Get an array of tag ids associated to a record.
 *
 * @param array $record the record for which we want to get the tags
 * @return array of tag ids, indexed and sorted by 'ordering'
 */
function tag_get_tags_ids($record) {
    
    $tag_ids = array();
    foreach( tag_get_tags($record) as $tag ) {
        $tag_ids[$tag->ordering] = $tag->id;
    }
    ksort($tag_ids);
    return $tag_ids;
}

/** 
 * Returns the database ID of a set of tags.
 * 
 * @param mixed $tags one tag, or array of tags, to look for.
 * @param bool $return_value specify the type of the returned value. Either 
 *     TAG_RETURN_OBJECT, or TAG_RETURN_ARRAY (default). If TAG_RETURN_ARRAY 
 *     is specified, an array will be returned even if only one tag was 
 *     passed in $tags.
 * @return mixed tag-indexed array of ids (or objects, if second parameter is 
 *     TAG_RETURN_OBJECT), or only an int, if only one tag is given *and* the 
 *     second parameter is null. No value for a key means the tag wasn't found.
 */
function tag_get_id($tags, $return_value=null) {
    global $CFG;
    static $tag_id_cache = array();

    $return_an_int = false;
    if (!is_array($tags)) {
        if(is_null($return_value) || $return_value == TAG_RETURN_OBJECT) {
            $return_an_int = true; 
        }
        $tags = array($tags);
    }
   
    $result = array();
    
    //TODO: test this and see if it helps performance without breaking anything
    //foreach($tags as $key => $tag) {
    //    $clean_tag = moodle_strtolower($tag);
    //    if ( array_key_exists($clean_tag), $tag_id_cache) ) {
    //        $result[$clean_tag] = $tag_id_cache[$clean_tag];
    //        $tags[$key] = ''; // prevent further processing for this one.
    //    }
    //}

    $tags = array_values(tag_normalize($tags));
    foreach($tags as $key => $tag) {
        $tags[$key] = addslashes(moodle_strtolower($tag)); 
        $result[moodle_strtolower($tag)] = null; // key must exists : no value for a key means the tag wasn't found.
    }
    $tag_string = "'". implode("', '", $tags) ."'";

    if ($rs = get_recordset_sql('SELECT * FROM '. $CFG->prefix .'tag WHERE name in ('. $tag_string .') order by name')) {
        while ($record = rs_fetch_next_record($rs)) {
            if ($return_value == TAG_RETURN_OBJECT) {
                $result[$record->name] = $record;
            } else { // TAG_RETURN_ARRAY
                $result[$record->name] = $record->id;
            }
        }
    }

    if ($return_an_int) {
        return array_pop($result);
    }

    return $result;
}

/**
 * Get a tag as an object (line) returned by get_recordset_sql 
 *
 * @param int $tagid a tag id
 * @return object a line returned from get_recordset_sql, or false
 */
function tag_get_tag_by_id($tagid) {
    global $CFG;
    $rs = get_recordset_sql('SELECT * FROM '. $CFG->prefix .'tag WHERE id = '. $tagid);
    return rs_fetch_next_record($rs);
}

/**
 * Returns tags related to a tag
 *
 * Related tags of a tag come from two sources:
 *   - manually added related tags, which are tag_instance entries for that tag
 *   - correlated tags, which are a calculated
 *
 * @param string $tag_name_or_id is a single **normalized** tag name or the id of a tag
 * @param int $limitnum return a subset comprising this many records (optional, default is 10)
 * @return array an array of tag objects
 */
function tag_get_related_tags($tagid, $limitnum=10) {

    //gets the manually added related tags
    if (!$related_tags = tag_get_tags(array('type'=>'tag', 'id'=>$tagid))) {
        $related_tags = array();
    }

    //gets the correlated tags
    $automatic_related_tags = tag_get_correlated($tagid, $limitnum);
    if (is_array($automatic_related_tags)) {
        $related_tags = array_merge($related_tags, $automatic_related_tags);
    }

    return array_slice(object_array_unique($related_tags), 0 , $limitnum);
}

/** 
 * Get a comma-separated list of tags related to another tag.
 *
 * @param array $related_tags the array returned by tag_get_related_tags
 * @param int $html either TAG_RETURN_HTML (default) or TAG_RETURN_TEXT : return html links, or just text.
 * @return string comma-separated list
 */
function tag_get_related_tags_csv($related_tags, $html=TAG_RETURN_HTML) {
    global $CFG;

    $tags_names = array();
    foreach($related_tags as $tag) {
        if ( $html == TAG_RETURN_TEXT) {
            $tags_names[] = rawurlencode(tag_display_name($tag));
        } else {
            // TAG_RETURN_HTML
            $tags_names[] = '<a href="'. $CFG->wwwroot .'/tag/index.php?tag='. rawurlencode($tag->name) .'">'. tag_display_name($tag) .'</a>';
        }
    }
    return implode(', ', $tags_names);
}

/**
 * Change the "value" of a tag, and update the associated 'name'.
 *
 * @param int $tagid the id of the tag to modify
 * @param string $newtag the new name
 * @return bool true on success, false otherwise
 */
function tag_rename($tagid, $newtag) {

    if (! $newtag_clean = array_shift(tag_normalize($newtag, TAG_CASE_ORIGINAL)) ) {
        return false;
    }

    if ( tag_get_id($newtag_clean) ) {
        // 'newtag' already exists and merging tags is not yet supported.
        return false; 
    }

    if ($tag = get_record('tag', 'id', $tagid)) {
        $tag->rawname = addslashes($newtag_clean); 
        $tag->name = addslashes(moodle_strtolower($newtag_clean)); 
        $tag->timemodified = time();
        return update_record('tag', $tag);
    }
    return false;
}

/**
 * Set the tags assigned to a record.  This overwrites the current tags.
 * 
 * This function is meant to be fed the string coming up from the user 
 * interface, which contains all tags assigned to a record.
 *
 * @param string $record_type the type of record to tag ('post' for blogs, 
 *     'user' for users, 'tag' for tags, etc.
 * @param int $record_id the id of the record to tag
 * @param array $tags the array of tags to set on the record. If 
 *     given an empty array, all tags will be removed.
 * @return void 
 */
function tag_set($record_type, $record_id, $tags) {
    global $db;

    $record = array('type' => $record_type, 'id' => $record_id);

    $tags_ids = tag_get_id($tags, TAG_RETURN_ARRAY); // force an array, even if we only have one tag.
    $cleaned_tags = tag_normalize($tags);
    //echo 'tags-in-tag_set'; var_dump($tags); var_dump($tags_ids); var_dump($cleaned_tags);

    $current_ids = tag_get_tags_ids($record);
    //var_dump($current_ids);
    $tags_to_assign = array();

    // for data coherence reasons, it's better to remove deleted tags
    // before adding new data: ordering could be duplicated.
    foreach($current_ids as $current_id) {
        if (!in_array($current_id, $tags_ids)) {
            tag_delete_instance($record, $current_id);
        }
    }

    foreach($tags as $ordering => $tag) {
        $tag = trim($tag);
        if (!$tag) {
            continue;
        }

        $clean_tag = $cleaned_tags[$tag];
        $tag_current_id = $tags_ids[$clean_tag];
        
        if ( is_null($tag_current_id) ) {
            // create new tags
            //echo "call to add tag $tag\n";
            $new_tag = tag_add($tag);
            tag_assign($record, $new_tag[$clean_tag], $ordering);
        } 
        elseif ( empty($current_ids) || !in_array($tag_current_id, $current_ids) ) {
            // assign existing tags
            tag_assign($record, $tag_current_id, $ordering);
        } 
        elseif ( isset($current_ids[$ordering]) && $current_ids[$ordering] != $tag_current_id ) { 
            // this actually checks if the ordering number points to the same tag
            //recompute ordering, if necessary
            //echo 'ordering changed for ', $tag, ':', $ordering, "\n";
            tag_assign($record, $tag_current_id, $ordering);
        }
    }
}

/**
 * Adds a tag to a record, without overwriting the current tags.
 * 
 * @param string $record_type the type of record to tag ('post' for blogs, 
 *     'user' for users, etc.
 * @param int $record_id the id of the record to tag
 * @param string $tag the tag to add
 * @return void
 */
function tag_set_add($record_type, $record_id, $tag) {

    $record = array('type' => $record_type, 'id' => $record_id);
    
    $new_tags = array();
    foreach( tag_get_tags($record) as $current_tag ) {
        $new_tags[] = $current_tag->rawname;
    }
    $new_tags[] = $tag;
    
    return tag_set($record_type, $record_id, $new_tags);
}

/**
 * Set the type of a tag.  At this time (version 1.9) the possible values
 * are 'default' or 'official'.  Official tags will be displayed separately "at
 * tagging time" (while selecting the tags to apply to a record).
 *
 * @param string $tagid tagid to modify
 * @param string $type either 'default' or 'official'
 * @return true on success, false otherwise
 */
function tag_type_set($tagid, $type) {
    if ($tag = get_record('tag', 'id', $tagid)) {
        $tag->tagtype = $type;
        $tag->timemodified = time();
        return update_record('tag', $tag);
    }
    return false;
}

///////////////////////////////////////////////////////
/////////////////// PRIVATE TAG API ///////////////////

/**
 * A * @param array $record the record that will be tagged
 * @param string $tags the comma-separated tags to set on the record. If 
 *     given an empty array, all tags will be removed.
dds one or more tag in the database.  This function should not be called 
 * directly : you should use tag_set.
 *
 * @param mixed $tags one tag, or an array of tags, to be created
 * @param string $tag_type type of tag to be created ("default" is the default 
 *     value and "official" is the only other supported value at this time). An
 *     official tag is kept even if there are no records tagged with it.
 * @return an array of tags ids, indexed by their lowercase normalized names. 
 *     Any boolean false in the array indicates an error while adding the tag.
 */
function tag_add($tags, $type="default") {
    global $USER;

    require_capability('moodle/tag:create', get_context_instance(CONTEXT_SYSTEM)); 

    if (!is_array($tags)) {
        $tags = array($tags);
    }

    $tag_object = new StdClass;
    $tag_object->tagtype = $type;
    $tag_object->userid = $USER->id;
    $tag_object->timemodified   = time();

    $clean_tags = tag_normalize($tags, TAG_CASE_ORIGINAL);

    $tags_ids = array();
    foreach($clean_tags as $tag) {
        $tag = trim($tag);
        if (!$tag) {
            $tags_ids[$tag] = false;
        } else {
            // note that the difference between rawname and name is only 
            // capitalization : the rawname is NOT the same at the rawtag. 
            $tag_object->rawname = addslashes($tag); 
            $tag_name_lc = moodle_strtolower($tag);
            $tag_object->name = addslashes($tag_name_lc); 
            //var_dump($tag_object);
            $tags_ids[$tag_name_lc] = insert_record('tag', $tag_object);
        }
    }

    return $tags_ids;
}

/**
 * Assigns a tag to a record: if the record already exists, the time and
 * ordering will be updated.
 * 
 * @param array $record the record that will be tagged
 * @param string $tagid the tag id to set on the record. 
 * @param int $ordering the order of the instance for this record
 * @return bool true on success, false otherwise
 */
function tag_assign($record, $tagid, $ordering) {

    require_capability('moodle/tag:create', get_context_instance(CONTEXT_SYSTEM));

    if ( $tag_instance_object = get_record('tag_instance', 'tagid', $tagid, 'itemtype', $record['type'], 'itemid', $record['id']) ) {
        $tag_instance_object->ordering = $ordering;
        $tag_instance_object->timemodified = time();
        return update_record('tag_instance', $tag_instance_object);
    } else { 
        $tag_instance_object = new StdClass;
        $tag_instance_object->tagid = $tagid;
        $tag_instance_object->itemid = $record['id'];
        $tag_instance_object->itemtype = $record['type'];
        $tag_instance_object->ordering = $ordering;
        $tag_instance_object->timemodified = time();
        return insert_record('tag_instance', $tag_instance_object);
    }
}

/**
 * Function that returns tags that start with some text, for use by the autocomplete feature
 *
 * @param string $text string that the tag names will be matched against
 * @return mixed an array of objects, or false if no records were found or an error occured.
 */
function tag_autocomplete($text) {
    global $CFG;
    return get_records_sql('SELECT tg.id, tg.name, tg.rawname FROM '. $CFG->prefix .'tag tg WHERE tg.name LIKE "'. moodle_strtolower($text) .'%"');
}

/**
 * Calculates and stores the correlated tags of all tags.
 * The correlations are stored in the 'tag_correlation' table.
 *
 * Two tags are correlated if they appear together a lot.
 * Ex.: Users tagged with "computers" will probably also be tagged with "algorithms".
 *
 * The rationale for the 'tag_correlation' table is performance.
 * It works as a cache for a potentially heavy load query done at the 'tag_instance' table.
 * So, the 'tag_correlation' table stores redundant information derived from the 'tag_instance' table.
 *
 * @param number $min_correlation cutoff percentage (optional, default is 2)
 */
function tag_compute_correlations($min_correlation=2) {

    global $CFG;

    $all_tags = get_records_list('tag');
    

    $tag_correlation_obj = new object();
    foreach($all_tags as $tag) {

        // query that counts how many times any tag appears together in items
        // with the tag passed as argument ($tag_id)
        $query = "SELECT tb.tagid , COUNT(*) AS nr ".
            "FROM {$CFG->prefix}tag_instance ta INNER JOIN {$CFG->prefix}tag_instance tb ON ta.itemid = tb.itemid ".
            "WHERE ta.tagid = {$tag->id} AND tb.tagid != {$tag->id} ".
            "GROUP BY tb.tagid ".
            "HAVING nr > $min_correlation ".
            "ORDER BY nr DESC";  // todo: find out if it's necessary to order.

        $correlated = array();

        // Correlated tags happen when they appear together in more occasions 
        // than $min_correlation.
        if ($tag_correlations = get_records_sql($query)) {
            foreach($tag_correlations as $correlation) {
        //        if($correlation->nr >= $min_correlation){
                    $correlated[] = $correlation->tagid;
        //        }
            }
        }

        $correlated = implode(',', $correlated);
        //var_dump($correlated);

        //saves correlation info in the caching table
        if ($tag_correlation_obj = get_record('tag_correlation', 'tagid', $tag->id)) {
            $tag_correlation_obj->correlatedtags = $correlated;
            update_record('tag_correlation', $tag_correlation_obj);
        } else {
            $tag_correlation_obj->tagid          = $tag->id;
            $tag_correlation_obj->correlatedtags = $correlated;
            insert_record('tag_correlation', $tag_correlation_obj);
        }
    }
}

/**
 * Tasks that should be performed at cron time
 */
function tag_cron() {
    tag_compute_correlations();
}

/** 
 * Get the name of a tag
 * 
 * @param mixed $tagids the id of the tag, or an array of ids
 * @return mixed string name of one tag, or id-indexed array of strings
 */
function tag_get_name($tagids) {

    $return_a_string = false;
    if ( !is_array($tagids) ) {
        $return_a_string = true;
        $tagids = array($tagids);
    }

    $tag_names = array();
    foreach(get_records_list('tag', 'id', implode(',', $tagids)) as $tag) { 
        $tag_names[$tag->id] = $tag->name;
    }

    if ($return_a_string) {
        return array_pop($tag_names);
    }

    return $tag_names;
}

/**
 * Returns the correlated tags of a tag, retrieved from the tag_correlation
 * table.  Make sure cron runs, otherwise the table will be empty and this 
 * function won't return anything.
 *
 * @param int $tag_id is a single tag id
 * @return array an array of tag objects, empty if no correlated tags are found
 */
function tag_get_correlated($tag_id, $limitnum=null) {

    $tag_correlation = get_record('tag_correlation', 'tagid', $tag_id);

    if (!$tag_correlation || empty($tag_correlation->correlatedtags)) {
        return array();
    }
    
    if (!$result = get_records_select('tag', "id IN ({$tag_correlation->correlatedtags})", '', '*', 0, $limitnum)) {
        return array();
    }

    return $result;
}

/**
 * Function that normalizes a list of tag names.
 *
 * @param mixed $tags array of tags, or a single tag.
 * @param int $case case to use for returned value (default: lower case). Either CASE_LOWER or CASE_UPPER
 * @return array of lowercased normalized tags, indexed by the normalized tag. (Eg: 'Banana' => 'banana')
 */
function tag_normalize($rawtags, $case = TAG_CASE_LOWER) {

    // cache normalized tags, to prevent (in some cases) costly (repeated) calls to clean_param
    static $cleaned_tags_lc = array(); // lower case - use for comparison
    static $cleaned_tags_mc = array(); // mixed case - use for saving to database

    if ( !is_array($rawtags) ) {
        $rawtags = array($rawtags);
    }

    $result = array();
    foreach($rawtags as $rawtag) {
        $rawtag = trim($rawtag);
        if (!$rawtag) {
            continue;
        }
        if ( !array_key_exists($rawtag, $cleaned_tags_lc) ) {
            $cleaned_tags_lc[$rawtag] = moodle_strtolower( clean_param($rawtag, PARAM_TAG) );
            $cleaned_tags_mc[$rawtag] = clean_param($rawtag, PARAM_TAG);
        }
        if ( $case == TAG_CASE_LOWER ) { 
            $result[$rawtag] = $cleaned_tags_lc[$rawtag];
        } else { // TAG_CASE_ORIGINAL
            $result[$rawtag] = $cleaned_tags_mc[$rawtag];
        }
    }
    
    return $result;
}


/**
 * Search for tags with names that match some text
 *
 * @param string $text escaped string that the tag names will be matched against
 * @param boolean $ordered If true, tags are ordered by their popularity. If false, no ordering.
 * @param int $limitfrom return a subset of records, starting at this point (optional, required if $limitnum is set).
 * @param int $limitnum return a subset comprising this many records (optional, required if $limitfrom is set).
 * @return mixed an array of objects, or false if no records were found or an error occured.
 */
function tag_find_tags($text, $ordered=true, $limitfrom='', $limitnum='') {

    global $CFG;

    $text = array_shift(tag_normalize($text, TAG_CASE_LOWER));

    if ($ordered) {
        $query = "SELECT tg.id, tg.name, tg.rawname, COUNT(ti.id) AS count ".
            "FROM {$CFG->prefix}tag tg LEFT JOIN {$CFG->prefix}tag_instance ti ON tg.id = ti.tagid ".
            "WHERE tg.name LIKE '%{$text}%' ".
            "GROUP BY tg.id, tg.name, tg.rawname ".
            "ORDER BY count DESC";
    } else {
        $query = "SELECT tg.id, tg.name, tg.rawname ".
            "FROM {$CFG->prefix}tag tg ".
            "WHERE tg.name LIKE '%{$text}%'";
    }
    return get_records_sql($query, $limitfrom , $limitnum);
}

///////////////////////////////////////////////////////////
////// functions copied over from the first version //////

/**
 * Flag a tag as inapropriate
 * 
 * @param mixed $tagids one (int) tagid, or an array of tagids
 * @return void
 */
function tag_set_flag($tagids) {
    if ( !is_array($tagids) ) {
        $tagids = array($tagids);
    }
    foreach ($tagids as $tagid) {
        $tag = get_record('tag', 'id', $tagid);
        $tag->flag++;
        $tag->timemodified = time();
        update_record('tag', $tag);
    }
}

/** 
 * Remove the inapropriate flag on a tag
 * 
 * @param mixed $tagids one (int) tagid, or an array of tagids
 * @return bool true if function succeeds, false otherwise
 */
function tag_unset_flag($tagids) {
    global $CFG;

    require_capability('moodle/tag:manage', get_context_instance(CONTEXT_SYSTEM));

    if ( is_array($tagids) ) {
        $tagids = implode(',', $tagids);
    }
    $timemodified = time();
    return execute_sql('UPDATE '. $CFG->prefix .'tag tg SET tg.flag = 0, tg.timemodified = '. $timemodified .' WHERE tg.id IN ('. $tagids .')', false);
}

/**
 * Count how many records are tagged with a specific tag,
 *
 * @param string $record record to look for ('post', 'user', etc.)
 * @param int $tag is a single tag id
 * @return int number of mathing tags.
 */
function tag_record_count($record_type, $tagid) {
    return count_records('tag_instance', 'itemtype', $record_type, 'tagid', $tagid);
}

/**
 * Determine if a record is tagged with a specific tag  
 *
 * @param array $record the record to look for
 * @param string $tag a tag name
 * @return bool true if it is tagged, false otherwise
 */
function tag_record_tagged_with($record, $tag) {
    if ($tagid = tag_get_id($tag)) {
        return count_records('tag_instance', 'itemtype', $record['type'], 'itemid', $record['id'], 'tagid', $tagid);
    } else {
        return 0; // tag doesn't exist
    }
}

?>
