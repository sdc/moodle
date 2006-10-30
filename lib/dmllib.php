<?php // $Id$

///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.com                                            //
//                                                                       //
// Copyright (C) 2001-3001 Martin Dougiamas        http://dougiamas.com  //
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

/// This library contains all the Data Manipulation Language (DML) functions
/// used to interact with the DB. All the dunctions in this library must be
/// generic and work against the major number of RDBMS possible. This is the
/// list of currently supported and tested DBs: mysql, postresql, mssql, oracle

/// This library is automatically included by Moodle core so you never need to
/// include it yourself.

/// For more info about the functions available in this library, please visit:
///     http://docs.moodle.org/en/DML_functions
/// (feel free to modify, improve and document such page, thanks!)

/// GLOBAL CONSTANTS /////////////////////////////////////////////////////////

$empty_rs_cache = array();   // Keeps copies of the recordsets used in one invocation
$metadata_cache = array();   // Keeps copies of the MetaColumns() for each table used in one invocations
$record_cache = array();     // Keeps copies of all simple get_record results from one invocation

/// FUNCTIONS FOR DATABASE HANDLING  ////////////////////////////////

/**
 * Execute a given sql command string
 *
 * Completely general function - it just runs some SQL and reports success.
 *
 * @uses $db
 * @param string $command The sql string you wish to be executed.
 * @param bool $feedback Set this argument to true if the results generated should be printed. Default is true.
 * @return string
 */
function execute_sql($command, $feedback=true) {
/// Completely general function - it just runs some SQL and reports success.

    global $db, $CFG;

    $olddebug = $db->debug;

    if (!$feedback) {
        $db->debug = false;
    }

    $empty_rs_cache = array();  // Clear out the cache, just in case changes were made to table structures

    if (defined('MDL_PERFDB')) { global $PERF ; $PERF->dbqueries++; };

    $result = $db->Execute($command);

    $db->debug = $olddebug;

    if ($result) {
        if ($feedback) {
            notify(get_string('success'), 'notifysuccess');
        }
        return true;
    } else {
        if ($feedback) {
            notify('<strong>' . get_string('error') . '</strong>');
        }
        if (!empty($CFG->dblogerror)) {
            $debug=array_shift(debug_backtrace());
            error_log("SQL ".$db->ErrorMsg()." in {$debug['file']} on line {$debug['line']}. STATEMENT:  $command");
        }
        return false;
    }
}

/**
* on DBs that support it, switch to transaction mode and begin a transaction
* you'll need to ensure you call commit_sql() or your changes *will* be lost.
*
* Now using ADOdb standard transactions. Some day, we should switch to
* Smart Transactions (http://phplens.com/adodb/tutorial.smart.transactions.html)
* as they autodetect errors and are nestable and easier to write
*
* this is _very_ useful for massive updates
*/
function begin_sql() {

    global $db;

    $db->BeginTrans();

    return true;
}

/**
* on DBs that support it, commit the transaction
*
* Now using ADOdb standard transactions. Some day, we should switch to
* Smart Transactions (http://phplens.com/adodb/tutorial.smart.transactions.html)
* as they autodetect errors and are nestable and easier to write
*/
function commit_sql() {

    global $db;

    $db->CommitTrans();

    return true;
}

/**
* on DBs that support it, rollback the transaction
*
* Now using ADOdb standard transactions. Some day, we should switch to
* Smart Transactions (http://phplens.com/adodb/tutorial.smart.transactions.html)
* as they autodetect errors and are nestable and easier to write
*/
function rollback_sql() {

    global $db;

    $db->RollbackTrans();

    return true;
}

/**
 * returns db specific uppercase function
 * @deprecated Moodle 1.7 because all the RDBMS use upper()
 */
function db_uppercase() {
    return "upper";
}

/**
 * returns db specific lowercase function
 * @deprecated Moodle 1.7 because all the RDBMS use lower()
 */
function db_lowercase() {
    return "lower";
}


/**
 * Run an arbitrary sequence of semicolon-delimited SQL commands
 *
 * Assumes that the input text (file or string) consists of
 * a number of SQL statements ENDING WITH SEMICOLONS.  The
 * semicolons MUST be the last character in a line.
 * Lines that are blank or that start with "#" or "--" (postgres) are ignored.
 * Only tested with mysql dump files (mysqldump -p -d moodle)
 *
 * @uses $CFG
 * @param string $sqlfile The path where a file with sql commands can be found on the server.
 * @param string $sqlstring If no path is supplied then a string with semicolon delimited sql
 * commands can be supplied in this argument.
 * @return bool Returns true if databse was modified successfully.
 */
function modify_database($sqlfile='', $sqlstring='') {

    global $CFG;

    $success = true;  // Let's be optimistic

    if (!empty($sqlfile)) {
        if (!is_readable($sqlfile)) {
            $success = false;
            echo '<p>Tried to modify database, but "'. $sqlfile .'" doesn\'t exist!</p>';
            return $success;
        } else {
            $lines = file($sqlfile);
        }
    } else {
        $sqlstring = trim($sqlstring);
        if ($sqlstring{strlen($sqlstring)-1} != ";") {
            $sqlstring .= ";"; // add it in if it's not there.
        }
        $lines[] = $sqlstring;
    }

    $command = '';

    foreach ($lines as $line) {
        $line = rtrim($line);
        $length = strlen($line);

        if ($length and $line[0] <> '#' and $line[0].$line[1] <> '--') {
            if (substr($line, $length-1, 1) == ';') {
                $line = substr($line, 0, $length-1);   // strip ;
                $command .= $line;
                $command = str_replace('prefix_', $CFG->prefix, $command); // Table prefixes
                if (! execute_sql($command)) {
                    $success = false;
                }
                $command = '';
            } else {
                $command .= $line;
            }
        }
    }

    return $success;

}

/// GENERIC FUNCTIONS TO CHECK AND COUNT RECORDS ////////////////////////////////////////

/**
 * Test whether a record exists in a table where all the given fields match the given values.
 *
 * The record to test is specified by giving up to three fields that must
 * equal the corresponding values.
 *
 * @uses $CFG
 * @param string $table The table to check.
 * @param string $field1 the first field to check (optional).
 * @param string $value1 the value field1 must have (requred if field1 is given, else optional).
 * @param string $field2 the second field to check (optional).
 * @param string $value2 the value field2 must have (requred if field2 is given, else optional).
 * @param string $field3 the third field to check (optional).
 * @param string $value3 the value field3 must have (requred if field3 is given, else optional).
 * @return bool true if a matching record exists, else false.
 */
function record_exists($table, $field1='', $value1='', $field2='', $value2='', $field3='', $value3='') {

    global $CFG;

    $select = where_clause($field1, $value1, $field2, $value2, $field3, $value3);

    return record_exists_sql('SELECT * FROM '. $CFG->prefix . $table .' '. $select);
}

/**
 * Test whether any records exists in a table which match a particular WHERE clause.
 *
 * @uses $CFG
 * @param string $table The database table to be checked against.
 * @param string $select A fragment of SQL to be used in a WHERE clause in the SQL call.
 * @return bool true if a matching record exists, else false.
 */
function record_exists_select($table, $select='') {

    global $CFG;

    if ($select) {
        $select = 'WHERE '.$select;
    }

    return record_exists_sql('SELECT * FROM '. $CFG->prefix . $table . ' ' . $select);
}

/**
 * Test whether a SQL SELECT statement returns any records.
 *
 * This function returns true if the SQL statement executes
 * without any errors and returns at least one record.
 *
 * @param string $sql The SQL statement to execute.
 * @return bool true if the SQL executes without errors and returns at least one record.
 */
function record_exists_sql($sql) {

    $limitfrom = 0; /// Number of records to skip
    $limitnum  = 1; /// Number of records to retrieve

    $rs = get_recordset_sql($sql, $limitfrom, $limitnum);

    if ($rs && $rs->RecordCount() > 0) {
        return true;
    } else {
        return false;
    }
}

/**
 * Count the records in a table where all the given fields match the given values.
 *
 * @uses $CFG
 * @param string $table The table to query.
 * @param string $field1 the first field to check (optional).
 * @param string $value1 the value field1 must have (requred if field1 is given, else optional).
 * @param string $field2 the second field to check (optional).
 * @param string $value2 the value field2 must have (requred if field2 is given, else optional).
 * @param string $field3 the third field to check (optional).
 * @param string $value3 the value field3 must have (requred if field3 is given, else optional).
 * @return int The count of records returned from the specified criteria.
 */
function count_records($table, $field1='', $value1='', $field2='', $value2='', $field3='', $value3='') {

    global $CFG;

    $select = where_clause($field1, $value1, $field2, $value2, $field3, $value3);

    return count_records_sql('SELECT COUNT(*) FROM '. $CFG->prefix . $table .' '. $select);
}

/**
 * Count the records in a table which match a particular WHERE clause.
 *
 * @uses $CFG
 * @param string $table The database table to be checked against.
 * @param string $select A fragment of SQL to be used in a WHERE clause in the SQL call.
 * @param string $countitem The count string to be used in the SQL call. Default is COUNT(*).
 * @return int The count of records returned from the specified criteria.
 */
function count_records_select($table, $select='', $countitem='COUNT(*)') {

    global $CFG;

    if ($select) {
        $select = 'WHERE '.$select;
    }

    return count_records_sql('SELECT '. $countitem .' FROM '. $CFG->prefix . $table .' '. $select);
}

/**
 * Get the result of a SQL SELECT COUNT(...) query.
 *
 * Given a query that counts rows, return that count. (In fact,
 * given any query, return the first field of the first record
 * returned. However, this method should only be used for the
 * intended purpose.) If an error occurrs, 0 is returned.
 *
 * @uses $CFG
 * @uses $db
 * @param string $sql The SQL string you wish to be executed.
 * @return int the count. If an error occurrs, 0 is returned.
 */
function count_records_sql($sql) {
    $rs = get_recordset_sql($sql);

    if ($rs) {
        return reset($rs->fields);
    } else {
        return 0;
    }
}

/// GENERIC FUNCTIONS TO GET, INSERT, OR UPDATE DATA  ///////////////////////////////////


/**
 * Get a single record as an object
 *
 * @uses $CFG
 * @param string $table The table to select from.
 * @param string $field1 the first field to check (optional).
 * @param string $value1 the value field1 must have (requred if field1 is given, else optional).
 * @param string $field2 the second field to check (optional).
 * @param string $value2 the value field2 must have (requred if field2 is given, else optional).
 * @param string $field3 the third field to check (optional).
 * @param string $value3 the value field3 must have (requred if field3 is given, else optional).
 * @return mixed a fieldset object containing the first mathcing record, or false if none found.
 */
function get_record($table, $field1, $value1, $field2='', $value2='', $field3='', $value3='', $fields='*') {
    
    global $CFG, $record_cache;
    
    // Check to see whether this record is eligible for caching (fields=*, only condition is id)
    $docache = false;
    if (!empty($CFG->enablerecordcache) && $field1=='id' && !$field2 && !$field3 && $fields=='*') {
        $docache = true;
        // If it's in the cache, return it
        if (!empty($record_cache[$table][$value1])) {
            return $record_cache[$table][$value1];
        }
    }
    
    $select = where_clause($field1, $value1, $field2, $value2, $field3, $value3);

    $record = get_record_sql('SELECT '.$fields.' FROM '. $CFG->prefix . $table .' '. $select);
    
    // If we're caching records, store this one (supposing we got something - we don't cache failures)
    if (!empty($CFG->enablerecordcache) && $record && $docache) {
        $record_cache[$table][$value1] = $record;
    }

    return $record;
}

/**
 * Get a single record as an object using an SQL statement
 *
 * The SQL statement should normally only return one record. In debug mode
 * you will get a warning if more record is returned (unless you
 * set $expectmultiple to true). In non-debug mode, it just returns
 * the first record.
 *
 * @uses $CFG
 * @uses $db
 * @param string $sql The SQL string you wish to be executed, should normally only return one record.
 * @param bool $expectmultiple If the SQL cannot be written to conviniently return just one record,
 *      set this to true to hide the debug message.
 * @param bool $nolimit sometimes appending ' LIMIT 1' to the SQL causes an error. Set this to true
 *      to stop your SQL being modified. This argument should probably be deprecated.
 * @return Found record as object. False if not found or error
 */
function get_record_sql($sql, $expectmultiple=false, $nolimit=false) {

    global $CFG;

/// Default situation
    $limitfrom = 0; /// Number of records to skip
    $limitnum  = 1; /// Number of records to retrieve

/// Only a few uses of the 2nd and 3rd parameter have been found
/// I think that we should avoid to use them completely, one
/// record is one record, and everything else should return error.
/// So the proposal is to change all the uses, (4-5 inside Moodle
/// Core), drop them from the definition and delete the next two
/// "if" sentences. (eloy, 2006-08-19)

    if ($nolimit) {
        $limitfrom = 0;
        $limitnum  = 0;
    } else if ($expectmultiple) {
        $limitfrom = 0;
        $limitnum  = 1;
    } else if (debugging()) {
        // Debugging mode - don't use a limit of 1, but do change the SQL, because sometimes that
        // causes errors, and in non-debug mode you don't see the error message and it is
        // impossible to know what's wrong.
        $limitfrom = 0;
        $limitnum  = 100;
    }

    if (!$rs = get_recordset_sql($sql, $limitfrom, $limitnum)) {
        return false;
    }

    $recordcount = $rs->RecordCount();

    if ($recordcount == 0) {          // Found no records
        return false;

    } else if ($recordcount == 1) {    // Found one record
    /// DIRTY HACK to retrieve all the ' ' (1 space) fields converted back
    /// to '' (empty string) for Oracle. It's the only way to work with
    /// all those NOT NULL DEFAULT '' fields until we definetively delete them
        if ($CFG->dbtype == 'oci8po') {
            array_walk($rs->fields, 'onespace2empty');
        }
    /// End od DIRTY HACK
        return (object)$rs->fields;

    } else {                          // Error: found more than one record
        notify('Error:  Turn off debugging to hide this error.');
        notify($sql . '(with limits ' . $limitfrom . ', ' . $limitnum . ')');
        if ($records = $rs->GetAssoc(true)) {
            notify('Found more than one record in get_record_sql !');
            print_object($records);
        } else {
            notify('Very strange error in get_record_sql !');
            print_object($rs);
        }
        print_continue("$CFG->wwwroot/$CFG->admin/config.php");
    }
}

/**
 * Gets one record from a table, as an object
 *
 * @uses $CFG
 * @param string $table The database table to be checked against.
 * @param string $select A fragment of SQL to be used in a where clause in the SQL call.
 * @param string $fields A comma separated list of fields to be returned from the chosen table.
 * @return object|false Returns an array of found records (as objects) or false if no records or error occured.
 */
function get_record_select($table, $select='', $fields='*') {

    global $CFG;

    if ($select) {
        $select = 'WHERE '. $select;
    }

    return get_record_sql('SELECT '. $fields .' FROM '. $CFG->prefix . $table .' '. $select);
}

/**
 * Get a number of records as an ADODB RecordSet.
 *
 * Selects records from the table $table.
 *
 * If specified, only records where the field $field has value $value are retured.
 *
 * If specified, the results will be sorted as specified by $sort. This
 * is added to the SQL as "ORDER BY $sort". Example values of $sort
 * mightbe "time ASC" or "time DESC".
 *
 * If $fields is specified, only those fields are returned.
 *
 * This function is internal to datalib, and should NEVER should be called directly
 * from general Moodle scripts.  Use get_record, get_records etc.
 *
 * If you only want some of the records, specify $limitfrom and $limitnum.
 * The query will skip the first $limitfrom records (according to the sort
 * order) and then return the next $limitnum records. If either of $limitfrom
 * or $limitnum is specified, both must be present.
 *
 * The return value is an ADODB RecordSet object
 * @link http://phplens.com/adodb/reference.functions.adorecordset.html
 * if the query succeeds. If an error occurrs, false is returned.
 *
 * @param string $table the table to query.
 * @param string $field a field to check (optional).
 * @param string $value the value the field must have (requred if field1 is given, else optional).
 * @param string $sort an order to sort the results in (optional, a valid SQL ORDER BY parameter).
 * @param string $fields a comma separated list of fields to return (optional, by default all fields are returned).
 * @param int $limitfrom return a subset of records, starting at this point (optional, required if $limitnum is set).
 * @param int $limitnum return a subset comprising this many records (optional, required if $limitfrom is set).
 * @return mixed an ADODB RecordSet object, or false if an error occured.
 */
function get_recordset($table, $field='', $value='', $sort='', $fields='*', $limitfrom='', $limitnum='') {

    if ($field) {
        $select = "$field = '$value'";
    } else {
        $select = '';
    }

    return get_recordset_select($table, $select, $sort, $fields, $limitfrom, $limitnum);
}

/**
 * Get a number of records as an ADODB RecordSet.
 *
 * If given, $select is used as the SELECT parameter in the SQL query,
 * otherwise all records from the table are returned.
 *
 * Other arguments and the return type as for @see function get_recordset.
 *
 * @uses $CFG
 * @param string $table the table to query.
 * @param string $select A fragment of SQL to be used in a where clause in the SQL call.
 * @param string $sort an order to sort the results in (optional, a valid SQL ORDER BY parameter).
 * @param string $fields a comma separated list of fields to return (optional, by default all fields are returned).
 * @param int $limitfrom return a subset of records, starting at this point (optional, required if $limitnum is set).
 * @param int $limitnum return a subset comprising this many records (optional, required if $limitfrom is set).
 * @return mixed an ADODB RecordSet object, or false if an error occured.
 */
function get_recordset_select($table, $select='', $sort='', $fields='*', $limitfrom='', $limitnum='') {

    global $CFG;

    if ($select) {
        $select = ' WHERE '. $select;
    }

    if ($sort) {
        $sort = ' ORDER BY '. $sort;
    }

    return get_recordset_sql('SELECT '. $fields .' FROM '. $CFG->prefix . $table . $select . $sort, $limitfrom, $limitnum);
}

/**
 * Get a number of records as an ADODB RecordSet.
 *
 * Only records where $field takes one of the values $values are returned.
 * $values should be a comma-separated list of values, for example "4,5,6,10"
 * or "'foo','bar','baz'".
 *
 * Other arguments and the return type as for @see function get_recordset.
 *
 * @param string $table the table to query.
 * @param string $field a field to check (optional).
 * @param string $values comma separated list of values the field must have (requred if field is given, else optional).
 * @param string $sort an order to sort the results in (optional, a valid SQL ORDER BY parameter).
 * @param string $fields a comma separated list of fields to return (optional, by default all fields are returned).
 * @param int $limitfrom return a subset of records, starting at this point (optional, required if $limitnum is set).
 * @param int $limitnum return a subset comprising this many records (optional, required if $limitfrom is set).
 * @return mixed an ADODB RecordSet object, or false if an error occured.
 */
function get_recordset_list($table, $field='', $values='', $sort='', $fields='*', $limitfrom='', $limitnum='') {

    if ($field) {
        $select = "$field IN ($values)";
    } else {
        $select = '';
    }

    return get_recordset_select($table, $select, $sort, $fields, $limitfrom, $limitnum);
}

/**
 * Get a number of records as an ADODB RecordSet.  $sql must be a complete SQL query.
 * This function is internal to datalib, and should NEVER should be called directly
 * from general Moodle scripts.  Use get_record, get_records etc.
 *
 * The return type is as for @see function get_recordset.
 *
 * @uses $CFG
 * @uses $db
 * @param string $sql the SQL select query to execute.
 * @param int $limitfrom return a subset of records, starting at this point (optional, required if $limitnum is set).
 * @param int $limitnum return a subset comprising this many records (optional, required if $limitfrom is set).
 * @return mixed an ADODB RecordSet object, or false if an error occured.
 */
function get_recordset_sql($sql, $limitfrom=null, $limitnum=null) {
    global $CFG, $db;

    if (empty($db)) {
        return false;
    }

/// Temporary hack as part of phasing out all access to obsolete user tables  XXX
    if (!empty($CFG->rolesactive)) {
        if (strpos($sql, $CFG->prefix.'user_students') || 
            strpos($sql, $CFG->prefix.'user_teachers') ||
            strpos($sql, $CFG->prefix.'user_coursecreators') ||
            strpos($sql, $CFG->prefix.'user_admins')) {
            if (debugging()) { var_dump(debug_backtrace()); }
            error('This SQL relies on obsolete tables!  Your code must be fixed by a developer.');
        }
    }


    if (defined('MDL_PERFDB')) { global $PERF ; $PERF->dbqueries++; };

    if ($limitfrom || $limitnum) {
        ///Special case, 0 must be -1 for ADOdb
        $limitfrom = empty($limitfrom) ? -1 : $limitfrom;
        $limitnum  = empty($limitnum) ? -1 : $limitnum;
        $rs = $db->SelectLimit($sql, $limitnum, $limitfrom);
    } else {
        $rs = $db->Execute($sql);
    }
    if (!$rs) {
        debugging($db->ErrorMsg() .'<br /><br />'. $sql);
        if (!empty($CFG->dblogerror)) {
            $debug=array_shift(debug_backtrace());
            error_log("SQL ".$db->ErrorMsg()." in {$debug['file']} on line {$debug['line']}. STATEMENT:  $sql with limits ($limitfrom, $limitnum)");
        }
        return false;
    }

    return $rs;
}

/**
 * Utility function used by the following 4 methods.
 *
 * @param object an ADODB RecordSet object.
 * @return mixed mixed an array of objects, or false if an error occured or the RecordSet was empty.
 */
function recordset_to_array($rs) {

    global $CFG;

    if ($rs && $rs->RecordCount() > 0) {
    /// First of all, we are going to get the name of the first column
    /// to introduce it back after transforming the recordset to assoc array
    /// See http://docs.moodle.org/en/XMLDB_Problems, fetch mode problem.
        $firstcolumn = $rs->FetchField(0);
    /// Get the whole associative array
        if ($records = $rs->GetAssoc(true)) {
            foreach ($records as $key => $record) {
            /// Really DIRTY HACK for Oracle, but it's the only way to make it work
            /// until we got all those NOT NULL DEFAULT '' out from Moodle
                if ($CFG->dbtype == 'oci8po') {
                    array_walk($record, 'onespace2empty');
                }
            /// End of DIRTY HACK
                $record[$firstcolumn->name] = $key;/// Re-add the assoc field
                $objects[$key] = (object) $record; /// To object
            }
            return $objects;
        } else {
            return false;
        }
    } else {
        return false;
    }
}

/**
 * This function is used to convert all the Oracle 1-space defaults to the empty string
 * like a really DIRTY HACK to allow it to work better until all those NOT NULL DEFAULT ''
 * fields will be out from Moodle.
 * @param string the string to be converted to '' (empty string) if it's ' ' (one space)
 * @param mixed the key of the array in case we are using this function from array_walk,
 *              defaults to null for other (direct) uses
 * @return boolean always true (the converted variable is returned by reference)
 */
function onespace2empty(&$item, $key=null) {
    $item = $item == ' ' ? '' : $item;
    return true;
}
///End DIRTY HACK


/**
 * Get a number of records as an array of objects.
 *
 * If the query succeeds and returns at least one record, the
 * return value is an array of objects, one object for each
 * record found. The array key is the value from the first
 * column of the result set. The object associated with that key
 * has a member variable for each column of the results.
 *
 * @param string $table the table to query.
 * @param string $field a field to check (optional).
 * @param string $value the value the field must have (requred if field1 is given, else optional).
 * @param string $sort an order to sort the results in (optional, a valid SQL ORDER BY parameter).
 * @param string $fields a comma separated list of fields to return (optional, by default all fields are returned).
 * @param int $limitfrom return a subset of records, starting at this point (optional, required if $limitnum is set).
 * @param int $limitnum return a subset comprising this many records (optional, required if $limitfrom is set).
 * @return mixed an array of objects, or false if no records were found or an error occured.
 */
function get_records($table, $field='', $value='', $sort='', $fields='*', $limitfrom='', $limitnum='') {
    $rs = get_recordset($table, $field, $value, $sort, $fields, $limitfrom, $limitnum);
    return recordset_to_array($rs);
}

/**
 * Get a number of records as an array of objects.
 *
 * Return value as for @see function get_records.
 *
 * @param string $table the table to query.
 * @param string $select A fragment of SQL to be used in a where clause in the SQL call.
 * @param string $sort an order to sort the results in (optional, a valid SQL ORDER BY parameter).
 * @param string $fields a comma separated list of fields to return (optional, by default all fields are returned).
 * @param int $limitfrom return a subset of records, starting at this point (optional, required if $limitnum is set).
 * @param int $limitnum return a subset comprising this many records (optional, required if $limitfrom is set).
 * @return mixed an array of objects, or false if no records were found or an error occured.
 */
function get_records_select($table, $select='', $sort='', $fields='*', $limitfrom='', $limitnum='') {
    $rs = get_recordset_select($table, $select, $sort, $fields, $limitfrom, $limitnum);
    return recordset_to_array($rs);
}

/**
 * Get a number of records as an array of objects.
 *
 * Return value as for @see function get_records.
 *
 * @param string $table The database table to be checked against.
 * @param string $field The field to search
 * @param string $values Comma separated list of possible value
 * @param string $sort Sort order (as valid SQL sort parameter)
 * @param string $fields A comma separated list of fields to be returned from the chosen table.
 * @return mixed an array of objects, or false if no records were found or an error occured.
 */
function get_records_list($table, $field='', $values='', $sort='', $fields='*', $limitfrom='', $limitnum='') {
    $rs = get_recordset_list($table, $field, $values, $sort, $fields, $limitfrom, $limitnum);
    return recordset_to_array($rs);
}

/**
 * Get a number of records as an array of objects.
 *
 * Return value as for @see function get_records.
 *
 * @param string $sql the SQL select query to execute.
 * @param int $limitfrom return a subset of records, starting at this point (optional, required if $limitnum is set).
 * @param int $limitnum return a subset comprising this many records (optional, required if $limitfrom is set).
 * @return mixed an array of objects, or false if no records were found or an error occured.
 */
function get_records_sql($sql, $limitfrom='', $limitnum='') {
    $rs = get_recordset_sql($sql, $limitfrom, $limitnum);
    return recordset_to_array($rs);
}

/**
 * Utility function used by the following 3 methods.
 *
 * @param object an ADODB RecordSet object with two columns.
 * @return mixed an associative array, or false if an error occured or the RecordSet was empty.
 */
function recordset_to_menu($rs) {
    global $CFG;

    if ($rs && $rs->RecordCount() > 0) {
        $keys = array_keys($rs->fields);
        $key0=$keys[0];
        $key1=$keys[1];
        while (!$rs->EOF) {
            $menu[$rs->fields[$key0]] = $rs->fields[$key1];
            $rs->MoveNext();
        }
        /// Really DIRTY HACK for Oracle, but it's the only way to make it work
        /// until we got all those NOT NULL DEFAULT '' out from Moodle
        if ($CFG->dbtype == 'oci8po') {
            array_walk($menu, 'onespace2empty');
        }
        /// End of DIRTY HACK
        return $menu;
    } else {
        return false;
    }
}

/**
 * Get the first two columns from a number of records as an associative array.
 *
 * Arguments as for @see function get_recordset.
 *
 * If no errors occur, and at least one records is found, the return value
 * is an associative whose keys come from the first field of each record,
 * and whose values are the corresponding second fields. If no records are found,
 * or an error occurs, false is returned.
 *
 * @param string $table the table to query.
 * @param string $field a field to check (optional).
 * @param string $value the value the field must have (requred if field1 is given, else optional).
 * @param string $sort an order to sort the results in (optional, a valid SQL ORDER BY parameter).
 * @param string $fields a comma separated list of fields to return (optional, by default all fields are returned).
 * @param int $limitfrom return a subset of records, starting at this point (optional, required if $limitnum is set).
 * @param int $limitnum return a subset comprising this many records (optional, required if $limitfrom is set).
 * @return mixed an associative array, or false if no records were found or an error occured.
 */
function get_records_menu($table, $field='', $value='', $sort='', $fields='*', $limitfrom='', $limitnum='') {
    $rs = get_recordset($table, $field, $value, $sort, $fields, $limitfrom, $limitnum);
    return recordset_to_menu($rs);
}

/**
 * Get the first two columns from a number of records as an associative array.
 *
 * Arguments as for @see function get_recordset_select.
 * Return value as for @see function get_records_menu.
 *
 * @param string $table The database table to be checked against.
 * @param string $select A fragment of SQL to be used in a where clause in the SQL call.
 * @param string $sort Sort order (optional) - a valid SQL order parameter
 * @param string $fields A comma separated list of fields to be returned from the chosen table.
 * @param int $limitfrom return a subset of records, starting at this point (optional, required if $limitnum is set).
 * @param int $limitnum return a subset comprising this many records (optional, required if $limitfrom is set).
 * @return mixed an associative array, or false if no records were found or an error occured.
 */
function get_records_select_menu($table, $select='', $sort='', $fields='*', $limitfrom='', $limitnum='') {
    $rs = get_recordset_select($table, $select, $sort, $fields, $limitfrom, $limitnum);
    return recordset_to_menu($rs);
}

/**
 * Get the first two columns from a number of records as an associative array.
 *
 * Arguments as for @see function get_recordset_sql.
 * Return value as for @see function get_records_menu.
 *
 * @param string $sql The SQL string you wish to be executed.
 * @param int $limitfrom return a subset of records, starting at this point (optional, required if $limitnum is set).
 * @param int $limitnum return a subset comprising this many records (optional, required if $limitfrom is set).
 * @return mixed an associative array, or false if no records were found or an error occured.
 */
function get_records_sql_menu($sql, $limitfrom='', $limitnum='') {
    $rs = get_recordset_sql($sql, $limitfrom, $limitnum);
    return recordset_to_menu($rs);
}

/**
 * Get a single value from a table row where all the given fields match the given values.
 *
 * @param string $table the table to query.
 * @param string $return the field to return the value of.
 * @param string $field1 the first field to check (optional).
 * @param string $value1 the value field1 must have (requred if field1 is given, else optional).
 * @param string $field2 the second field to check (optional).
 * @param string $value2 the value field2 must have (requred if field2 is given, else optional).
 * @param string $field3 the third field to check (optional).
 * @param string $value3 the value field3 must have (requred if field3 is given, else optional).
 * @return mixed the specified value, or false if an error occured.
 */
function get_field($table, $return, $field1, $value1, $field2='', $value2='', $field3='', $value3='') {
    global $CFG;
    $select = where_clause($field1, $value1, $field2, $value2, $field3, $value3);
    return get_field_sql('SELECT ' . $return . ' FROM ' . $CFG->prefix . $table . ' ' . $select);
}

/**
 * Get a single value from a table row where a particular select clause is true.
 *
 * @uses $CFG
 * @param string $table the table to query.
 * @param string $return the field to return the value of.
 * @param string $select A fragment of SQL to be used in a where clause in the SQL call.
 * @return mixed the specified value, or false if an error occured.
 */
function get_field_select($table, $return, $select) {
    global $CFG;
    if ($select) {
        $select = 'WHERE '. $select;
    }
    return get_field_sql('SELECT ' . $return . ' FROM ' . $CFG->prefix . $table . ' ' . $select);
}

/**
 * Get a single value from a table.
 *
 * @param string $sql an SQL statement expected to return a single value.
 * @return mixed the specified value, or false if an error occured.
 */
function get_field_sql($sql) {
    global $CFG;

/// Strip potential LIMIT uses arriving here, debugging them (MDL-7173)
    $newsql = preg_replace('/ LIMIT [0-9, ]+$/is', '', $sql);
    if ($newsql != $sql) {
        debugging('Incorrect use of LIMIT clause (not cross-db) in call to get_field_sql(): ' . $sql, DEBUG_DEVELOPER);
        $sql = $newsql;
    }

    $rs = get_recordset_sql($sql, 0, 1);

    if ($rs && $rs->RecordCount() == 1) {
        /// DIRTY HACK to retrieve all the ' ' (1 space) fields converted back
        /// to '' (empty string) for Oracle. It's the only way to work with
        /// all those NOT NULL DEFAULT '' fields until we definetively delete them
        if ($CFG->dbtype == 'oci8po') {
            $value = reset($rs->fields);
            onespace2empty($value);
            return $value;
        }
        /// End of DIRTY HACK
        return reset($rs->fields);
    } else {
        return false;
    }
}

/**
 * Get an array of data from one or more fields from a database
 * use to get a column, or a series of distinct values
 *
 * @uses $CFG
 * @uses $db
 * @param string $sql The SQL string you wish to be executed.
 * @return mixed|false Returns the value return from the SQL statment or false if an error occured.
 * @todo Finish documenting this function
 */
function get_fieldset_sql($sql) {

    global $db, $CFG;

    if (defined('MDL_PERFDB')) { global $PERF ; $PERF->dbqueries++; };

    $rs = $db->Execute($sql);
    if (!$rs) {
        debugging($db->ErrorMsg() .'<br /><br />'. $sql);
        if (!empty($CFG->dblogerror)) {
            $debug=array_shift(debug_backtrace());
            error_log("SQL ".$db->ErrorMsg()." in {$debug['file']} on line {$debug['line']}. STATEMENT:  $sql");
        }
        return false;
    }

    if ( $rs->RecordCount() > 0 ) {
        $keys = array_keys($rs->fields);
        $key0 = $keys[0];
        $results = array();
        while (!$rs->EOF) {
            array_push($results, $rs->fields[$key0]);
            $rs->MoveNext();
        }
        /// DIRTY HACK to retrieve all the ' ' (1 space) fields converted back
        /// to '' (empty string) for Oracle. It's the only way to work with
        /// all those NOT NULL DEFAULT '' fields until we definetively delete them
        if ($CFG->dbtype == 'oci8po') {
            array_walk($results, 'onespace2empty');
        }
        /// End of DIRTY HACK
        return $results;
    } else {
        return false;
    }
}

/**
 * Set a single field in every table row where all the given fields match the given values.
 *
 * @uses $CFG
 * @uses $db
 * @param string $table The database table to be checked against.
 * @param string $newfield the field to set.
 * @param string $newvalue the value to set the field to.
 * @param string $field1 the first field to check (optional).
 * @param string $value1 the value field1 must have (requred if field1 is given, else optional).
 * @param string $field2 the second field to check (optional).
 * @param string $value2 the value field2 must have (requred if field2 is given, else optional).
 * @param string $field3 the third field to check (optional).
 * @param string $value3 the value field3 must have (requred if field3 is given, else optional).
 * @return mixed An ADODB RecordSet object with the results from the SQL call or false.
 */
function set_field($table, $newfield, $newvalue, $field1, $value1, $field2='', $value2='', $field3='', $value3='') {

    global $db, $CFG, $record_cache;

    if (defined('MDL_PERFDB')) { global $PERF ; $PERF->dbqueries++; };

    // Clear record_cache based on the parameters passed (individual record or whole table)
    if (!empty($CFG->enablerecordcache)) {
        if ($field1 == 'id') {
            if (isset($record_cache[$table][$value1])) {
                unset($record_cache[$table][$value1]);
            }
        } else if ($field2 == 'id') {
            if (isset($record_cache[$table][$value2])) {
                unset($record_cache[$table][$value2]);
            }
        } else if ($field3 == 'id') {
            if (isset($record_cache[$table][$value3])) {
                unset($record_cache[$table][$value3]);
            }
        } else {
            if (isset($record_cache[$table])) {
                unset($record_cache[$table]);
            }
        }
    }

    $select = where_clause($field1, $value1, $field2, $value2, $field3, $value3);

    $dataobject = new StdClass;
    $dataobject->{$newfield} = $newvalue;
    // Oracle DIRTY HACK - 
    if ($CFG->dbtype == 'oci8po') {
        oracle_dirty_hack($table, $dataobject); // Convert object to the correct "empty" values for Oracle DB
        $newvalue = $dataobject->{$newfield};
    }
    // End DIRTY HACK

/// Under Oracle and MSSQL we have our own set field process
/// If the field being updated is clob/blob, we use our alternate update here
/// They will be updated later
    if (($CFG->dbtype == 'oci8po' || $CFG->dbtype == 'mssql' || $CFG->dbtype == 'odbc_mssql' || $CFG->dbtype == 'mssql_n')
      && !empty($select)) {
    /// Detect lobs
        $foundclobs = array();
        $foundblobs = array();
        db_detect_lobs($table, $dataobject, $foundclobs, $foundblobs);
    }

/// Under Oracle and MSSQL, finally, Update all the Clobs and Blobs present in the record
/// if we know we have some of them in the query
    if (($CFG->dbtype == 'oci8po' || $CFG->dbtype == 'mssql' || $CFG->dbtype == 'odbc_mssql' || $CFG->dbtype == 'mssql_n')
      && !empty($select) &&
      (!empty($foundclobs) || !empty($foundblobs))) {
        if (!db_update_lobs($table, $select, $foundclobs, $foundblobs)) {
            return false; //Some error happened while updating LOBs
        } else {
            return true; //Everrything was ok
        }
    }

/// Arriving here, standard update 
    return $db->Execute('UPDATE '. $CFG->prefix . $table .' SET '. $newfield  .' = \''. $newvalue .'\' '. $select);
}

/**
 * Delete the records from a table where all the given fields match the given values.
 *
 * @uses $CFG
 * @uses $db
 * @param string $table the table to delete from.
 * @param string $field1 the first field to check (optional).
 * @param string $value1 the value field1 must have (requred if field1 is given, else optional).
 * @param string $field2 the second field to check (optional).
 * @param string $value2 the value field2 must have (requred if field2 is given, else optional).
 * @param string $field3 the third field to check (optional).
 * @param string $value3 the value field3 must have (requred if field3 is given, else optional).
 * @return mixed An ADODB RecordSet object with the results from the SQL call or false.
 */
function delete_records($table, $field1='', $value1='', $field2='', $value2='', $field3='', $value3='') {

    global $db, $CFG, $record_cache;

    // Clear record_cache based on the parameters passed (individual record or whole table)
    if (!empty($CFG->enablerecordcache)) {
        if ($field1 == 'id') {
            if (isset($record_cache[$table][$value1])) {
                unset($record_cache[$table][$value1]);
            }
        } else if ($field2 == 'id') {
            if (isset($record_cache[$table][$value2])) {
                unset($record_cache[$table][$value2]);
            }
        } else if ($field3 == 'id') {
            if (isset($record_cache[$table][$value3])) {
                unset($record_cache[$table][$value3]);
            }
        } else {
            if (isset($record_cache[$table])) {
                unset($record_cache[$table]);
            }
        }
    }

    if (defined('MDL_PERFDB')) { global $PERF ; $PERF->dbqueries++; };

    $select = where_clause($field1, $value1, $field2, $value2, $field3, $value3);

    return $db->Execute('DELETE FROM '. $CFG->prefix . $table .' '. $select);
}

/**
 * Delete one or more records from a table
 *
 * @uses $CFG
 * @uses $db
 * @param string $table The database table to be checked against.
 * @param string $select A fragment of SQL to be used in a where clause in the SQL call (used to define the selection criteria).
 * @return object A PHP standard object with the results from the SQL call.
 * @todo Verify return type.
 */
function delete_records_select($table, $select='') {

    global $CFG, $db, $record_cache;

    // Clear record_cache (whole table)
    if (!empty($CFG->enablerecordcache) && isset($record_cache[$table])) {
        unset($record_cache[$table]);
    }

    if (defined('MDL_PERFDB')) { global $PERF ; $PERF->dbqueries++; };

    if ($select) {
        $select = 'WHERE '.$select;
    }

    return $db->Execute('DELETE FROM '. $CFG->prefix . $table .' '. $select);
}

/**
 * Insert a record into a table and return the "id" field if required
 *
 * If the return ID isn't required, then this just reports success as true/false.
 * $dataobject is an object containing needed data
 *
 * @uses $db
 * @uses $CFG
 * @param string $table The database table to be checked against.
 * @param array $dataobject A data object with values for one or more fields in the record
 * @param bool $returnid Should the id of the newly created record entry be returned? If this option is not requested then true/false is returned.
 * @param string $primarykey The primary key of the table we are inserting into (almost always "id")
 */
function insert_record($table, $dataobject, $returnid=true, $primarykey='id') {

    global $db, $CFG, $empty_rs_cache;

    if (empty($db)) {
        return false;
    }

/// Temporary hack as part of phasing out all access to obsolete user tables  XXX
    if (!empty($CFG->rolesactive)) {
        if (in_array($table, array('user_students', 'user_teachers', 'user_coursecreators', 'user_admins'))) {
            if (debugging()) { var_dump(debug_backtrace()); }
            error('This SQL relies on obsolete tables ('.$table.')!  Your code must be fixed by a developer.');
        }
    }

    if (defined('MDL_PERFDB')) { global $PERF ; $PERF->dbqueries++; };

/// In Moodle we always use auto-numbering fields for the primary key
/// so let's unset it now before it causes any trouble later
    unset($dataobject->{$primarykey});

/// Get an empty recordset. Cache for multiple inserts.
    if (empty($empty_rs_cache[$table])) {
        /// Execute a dummy query to get an empty recordset
        if (!$empty_rs_cache[$table] = $db->Execute('SELECT * FROM '. $CFG->prefix . $table .' WHERE '. $primarykey  .' = \'-1\'')) {
            return false;
        }
    }

    $rs = $empty_rs_cache[$table];

/// Postgres doesn't have the concept of primary key built in
/// and will return the OID which isn't what we want.
/// The efficient and transaction-safe strategy is to
/// move the sequence forward first, and make the insert
/// with an explicit id.
    if ( $CFG->dbtype === 'postgres7' && $returnid == true ) {
        if ($nextval = (int)get_field_sql("SELECT NEXTVAL('{$CFG->prefix}{$table}_{$primarykey}_seq')")) {
            $dataobject->{$primarykey} = $nextval;
        }
    }

/// First basic support of insert for Oracle. As it doesn't
/// support autogenerated fields, we rely on the corresponding
/// sequence. It will work automatically, unless we need to
/// return the primary from the function, in this case we
/// get the next sequence value here and insert it manually.
    if ( $CFG->dbtype === 'oci8po' && $returnid == true) {
    /// We need this here (move this function to dmlib?)
        include_once($CFG->libdir . '/ddllib.php');
        $xmldb_table = new XMLDBTable($table);
        $seqname = find_sequence_name($xmldb_table);
        if (!$seqname) {
        /// Fallback, seqname not found, something is wrong. Inform and use the alternative getNameForObject() method
            debugging('Sequence name for table ' . $table->getName() . ' not found', DEBUG_DEVELOPER);
            $generator = new XMLDBoci8po();
            $generator->setPrefix($CFG->prefix);
            $seqname = $generator->getNameForObject($table, $primarykey, 'seq');
        }
        if ($nextval = (int)$db->GenID($seqname)) {
            $dataobject->{$primarykey} = $nextval;
        }
    }

/// Begin DIRTY HACK
    if ($CFG->dbtype == 'oci8po') {
        oracle_dirty_hack($table, $dataobject); // Convert object to the correct "empty" values for Oracle DB
    }
/// End DIRTY HACK

/// Under Oracle we have our own insert record process
/// detect all the clob/blob fields and change their contents to @#CLOB#@ and @#BLOB#@
/// saving them into $foundclobs and $foundblobs [$fieldname]->contents
/// Same for mssql (only processing blobs - image fields)
    if (($CFG->dbtype == 'oci8po' || $CFG->dbtype == 'mssql' || $CFG->dbtype == 'odbc_mssql' || $CFG->dbtype == 'mssql_n')) {
        $foundclobs = array();
        $foundblobs = array();
        db_detect_lobs($table, $dataobject, $foundclobs, $foundblobs);
    }
    
/// Get the correct SQL from adoDB
    if (!$insertSQL = $db->GetInsertSQL($rs, (array)$dataobject, true)) {
        return false;
    }

/// Under Oracle and MSSQL, replace all the '@#CLOB#@' and '@#BLOB#@' ocurrences to proper default values
/// if we know we have some of them in the query
    if (($CFG->dbtype == 'oci8po' || $CFG->dbtype == 'mssql' || $CFG->dbtype == 'odbc_mssql' || $CFG->dbtype == 'mssql_n') &&
      (!empty($foundclobs) || !empty($foundblobs))) {
    /// Initial configuration, based on DB
        switch ($CFG->dbtype) {
            case 'oci8po':
                $clobdefault = 'empty_clob()'; //Value of empty default clobs for this DB
                $blobdefault = 'empty_blob()'; //Value of empty default blobs for this DB
                break;
            case 'mssql':
            case 'odbc_mssql': 
            case 'mssql_n':
                $clobdefault = 'null'; //Value of empty default clobs for this DB (under mssql this won't be executed
                $blobdefault = 'null'; //Value of empty default blobs for this DB
                break;  
        }
        $insertSQL = str_replace("'@#CLOB#@'", $clobdefault, $insertSQL);
        $insertSQL = str_replace("'@#BLOB#@'", $blobdefault, $insertSQL);
    }

/// Run the SQL statement
    if (!$rs = $db->Execute($insertSQL)) {
        debugging($db->ErrorMsg() .'<br /><br />'.$insertSQL);
        if (!empty($CFG->dblogerror)) {
            $debug=array_shift(debug_backtrace());
            error_log("SQL ".$db->ErrorMsg()." in {$debug['file']} on line {$debug['line']}. STATEMENT:  $insertSQL");
        }
        return false;
    }

/// Under Oracle, finally, Update all the Clobs and Blobs present in the record
/// if we know we have some of them in the query
    if ($CFG->dbtype == 'oci8po' &&
      !empty($dataobject->{$primarykey}) && 
      (!empty($foundclobs) || !empty($foundblobs))) {
        if (!db_update_lobs($table, $dataobject->{$primarykey}, $foundclobs, $foundblobs)) {
            return false; //Some error happened while updating LOBs
        }
    }

/// If a return ID is not needed then just return true now (but not in MSSQL DBs, where we may have some pending tasks)
    if (!$returnid && !($CFG->dbtype == 'mssql' || $CFG->dbtype == 'odbc_mssql' || $CFG->dbtype == 'mssql_n')) {
        return true;
    }

/// We already know the record PK if it's been passed explicitly,
/// or if we've retrieved it from a sequence (Postgres and Oracle).
    if (!empty($dataobject->{$primarykey})) {
        return $dataobject->{$primarykey};
    }

/// This only gets triggered with MySQL and MSQL databases
/// however we have some postgres fallback in case we failed
/// to find the sequence.
    $id = $db->Insert_ID();

/// Under MSSQL all the Blobs (IMAGE) present in the record
/// if we know we have some of them in the query
    if (($CFG->dbtype == 'mssql' || $CFG->dbtype == 'odbc_mssql' || $CFG->dbtype == 'mssql_n') &&
      !empty($id) && 
      (!empty($foundclobs) || !empty($foundblobs))) {
        if (!db_update_lobs($table, $id, $foundclobs, $foundblobs)) {
            return false; //Some error happened while updating LOBs
        }
    }

    if ($CFG->dbtype === 'postgres7') {
        // try to get the primary key based on id
        if ( ($rs = $db->Execute('SELECT '. $primarykey .' FROM '. $CFG->prefix . $table .' WHERE oid = '. $id))
             && ($rs->RecordCount() == 1) ) {
            trigger_error("Retrieved $primarykey from oid on table $table because we could not find the sequence.");
            return (integer)reset($rs->fields);
        }
        trigger_error('Failed to retrieve primary key after insert: SELECT '. $primarykey .
                      ' FROM '. $CFG->prefix . $table .' WHERE oid = '. $id);
        return false;
    }

    return (integer)$id;
}

/**
 * Update a record in a table
 *
 * $dataobject is an object containing needed data
 * Relies on $dataobject having a variable "id" to
 * specify the record to update
 *
 * @uses $CFG
 * @uses $db
 * @param string $table The database table to be checked against.
 * @param array $dataobject An object with contents equal to fieldname=>fieldvalue. Must have an entry for 'id' to map to the table specified.
 * @return bool
 */
function update_record($table, $dataobject) {

    global $db, $CFG, $record_cache;

    if (! isset($dataobject->id) ) {
        return false;
    }

    // Remove this record from record cache since it will change
    if (!empty($CFG->enablerecordcache) && isset($record_cache[$table][$dataobject->id])) {
        unset($record_cache[$table][$dataobject->id]);
    }
    
/// Temporary hack as part of phasing out all access to obsolete user tables  XXX
    if (!empty($CFG->rolesactive)) {
        if (in_array($table, array('user_students', 'user_teachers', 'user_coursecreators', 'user_admins'))) {
            if (debugging()) { var_dump(debug_backtrace()); }
            error('This SQL relies on obsolete tables ('.$table.')!  Your code must be fixed by a developer.');
        }
    }

/// Begin DIRTY HACK
    if ($CFG->dbtype == 'oci8po') {
        oracle_dirty_hack($table, $dataobject); // Convert object to the correct "empty" values for Oracle DB
    }
/// End DIRTY HACK

/// Under Oracle and MSSQL we have our own update record process
/// detect all the clob/blob fields and delete them from the record being updated
/// saving them into $foundclobs and $foundblobs [$fieldname]->contents
/// They will be updated later
    if (($CFG->dbtype == 'oci8po' || $CFG->dbtype == 'mssql' || $CFG->dbtype == 'odbc_mssql' || $CFG->dbtype == 'mssql_n')
      && !empty($dataobject->id)) {
    /// Detect lobs
        $foundclobs = array();
        $foundblobs = array();
        db_detect_lobs($table, $dataobject, $foundclobs, $foundblobs, true);
    }

    // Determine all the fields in the table
    if (!$columns = $db->MetaColumns($CFG->prefix . $table)) {
        return false;
    }
    $data = (array)$dataobject;

    if (defined('MDL_PERFDB')) { global $PERF ; $PERF->dbqueries++; };

    // Pull out data matching these fields
    $ddd = array();
    foreach ($columns as $column) {
        if ($column->name <> 'id' and isset($data[$column->name]) ) {
            $ddd[$column->name] = $data[$column->name];
            // PostgreSQL bytea support
            if ($CFG->dbtype == 'postgres7' && $column->type == 'bytea') {
                $ddd[$column->name] = $db->BlobEncode($ddd[$column->name]);
            }
        }
    }

    // Construct SQL queries
    $numddd = count($ddd);
    $count = 0;
    $update = '';

/// Only if we have fields to be updated (this will prevent both wrong updates + 
/// updates of only LOBs in Oracle
    if ($numddd) {
        foreach ($ddd as $key => $value) {
            $count++;
            $update .= $key .' = \''. $value .'\'';   // All incoming data is already quoted
            if ($count < $numddd) {
                $update .= ', ';
            }
        }

        if (!$rs = $db->Execute('UPDATE '. $CFG->prefix . $table .' SET '. $update .' WHERE id = \''. $dataobject->id .'\'')) {
            debugging($db->ErrorMsg() .'<br /><br />UPDATE '. $CFG->prefix . $table .' SET '. $update .' WHERE id = \''. $dataobject->id .'\'');
            if (!empty($CFG->dblogerror)) {
                $debug=array_shift(debug_backtrace());
                error_log("SQL ".$db->ErrorMsg()." in {$debug['file']} on line {$debug['line']}. STATEMENT:  UPDATE $CFG->prefix$table SET $update WHERE id = '$dataobject->id'");
            }
            return false;
        }
    }

/// Under Oracle AND MSSQL, finally, Update all the Clobs and Blobs present in the record
/// if we know we have some of them in the query
    if (($CFG->dbtype == 'oci8po' || $CFG->dbtype == 'mssql' || $CFG->dbtype == 'odbc_mssql' || $CFG->dbtype == 'mssql_n')
      && !empty($dataobject->id) && 
      (!empty($foundclobs) || !empty($foundblobs))) {
        if (!db_update_lobs($table, $dataobject->id, $foundclobs, $foundblobs)) {
            return false; //Some error happened while updating LOBs
        }
    }

    return true;
}



/**
 * Returns the proper SQL to do paging
 *
 * @uses $CFG
 * @param string $page Offset page number
 * @param string $recordsperpage Number of records per page
 * @deprecated Moodle 1.7 use the new $limitfrom, $limitnum available in all
 *             the get_recordXXX() funcions.
 * @return string
 */
function sql_paging_limit($page, $recordsperpage) {
    global $CFG;

    debugging('Function sql_paging_limit() is deprecated. Replace it with the correct use of limitfrom, limitnum parameters', DEBUG_DEVELOPER);

    switch ($CFG->dbtype) {
        case 'postgres7':
             return 'LIMIT '. $recordsperpage .' OFFSET '. $page;
        default:
             return 'LIMIT '. $page .','. $recordsperpage;
    }
}

/**
 * Returns the proper SQL to do LIKE in a case-insensitive way
 *
 * Note the LIKE are case sensitive for Oracle. Oracle 10g is required to use 
 * the caseinsensitive search using regexp_like() or NLS_COMP=LINGUISTIC :-(
 * See http://docs.moodle.org/en/XMLDB_Problems#Case-insensitive_searches
 * 
 * @uses $CFG
 * @return string
 */
function sql_ilike() {
    global $CFG;

    switch ($CFG->dbtype) {
        case 'postgres7':
             return 'ILIKE';
        default:
             return 'LIKE';
    }
}


/**
 * Returns the proper SQL (for the dbms in use) to concatenate $firstname and $lastname
 *
 * @uses $CFG
 * @param string $firstname User's first name
 * @param string $lastname User's last name
 * @return string
 */
function sql_fullname($firstname='firstname', $lastname='lastname') {
    return sql_concat($firstname, "' '", $lastname);
}

/**
 * Returns the proper SQL to do CONCAT between the elements passed
 * Can take many parameters - just a passthrough to $db->Concat()
 *
 * @uses $db
 * @param string $element
 * @return string
 */
function sql_concat() {
    global $db;

    $args = func_get_args();
    return call_user_func_array(array($db, 'Concat'), $args);
}

/**
 * Returns the proper SQL to do CONCAT between the elements passed
 * with a given separator
 *
 * @uses $db
 * @param string $separator
 * @param array  $elements
 * @return string
 */
function sql_concat_join($separator="' '", $elements=array()) {
    global $db;
 
    // copy to ensure pass by value
    $elem = $elements;

    // Intersperse $elements in the array.
    // Add items to the array on the fly, walking it
    // _backwards_ splicing the elements in. The loop definition
    // should skip first and last positions.
    for ($n=count($elem)-1; $n > 0 ; $n--) {
        array_splice($elem, $n, 0, $separator);
    }
    return call_user_func_array(array($db, 'Concat'), $elem);
}

/**
 * Returns the proper SQL to do IS NULL
 * @uses $CFG
 * @param string $fieldname The field to add IS NULL to
 * @return string
 */
function sql_isnull($fieldname) {
    global $CFG;

    switch ($CFG->dbtype) {
        case 'mysql':
             return $fieldname.' IS NULL';
        default:
             return $fieldname.' IS NULL';
    }
}

/**
 * Returns the proper AS keyword to be used to aliase columns
 * SQL defines the keyword as optional and nobody but PG
 * seems to require it. This function should be used inside all
 * the statements using column aliases.
 * Note than the use of table aliases doesn't require the
 * AS keyword at all, only columns for postgres.
 * @uses $CFG
 * @ return string the keyword
 * @deprecated Moodle 1.7 because coding guidelines now enforce to use AS in column aliases
 */
function sql_as() {
    global $CFG, $db;

    switch ($CFG->dbtype) {
        case 'postgres7':
            return 'AS';
        default:
            return '';
    }
}

/**
 * Returns the SQL text to be used to order by one TEXT (clob) column, because
 * some RDBMS doesn't support direct ordering of such fields.
 * Note that the use or queries being ordered by TEXT columns must be minimised,
 * because it's really slooooooow.
 * @param string fieldname the name of the TEXT field we need to order by
 * @param string number of chars to use for the ordering (defaults to 32)
 * @return string the piece of SQL code to be used in your statement.
 */
function sql_order_by_text($fieldname, $numchars=32) {

    global $CFG;

    switch ($CFG->dbtype) {
        case 'mssql':
        case 'mssql_n':
        case 'odbc_mssql':
            return 'CONVERT(varchar, ' . $fieldname . ', ' . $numchars . ')';
            break;
        case 'oci8po':
            return 'dbms_lob.substr(' . $fieldname . ', ' . $numchars . ',1)';
            break;
        default:
            return $fieldname;
    }
}


/**
 * Returns SQL to be used as a subselect to find the primary role of users.  
 * Geoff Cant <geoff@catalyst.net.nz> (the author) is very keen for this to
 * be implemented as a view in future versions. 
 *
 * eg if this function returns a string called $primaryroles, then you could:
 * $sql = 'SELECT COUNT(DISTINCT prs.userid) FROM ('.$primary_roles.') prs 
 *          WHERE prs.primary_roleid='.$role->id.' AND prs.courseid='.$course->id.
 *          ' AND prs.contextlevel = '.CONTEXT_COURSE;
 *
 * @return string the piece of SQL code to be used in your FROM( ) statement.
 */
function sql_primary_role_subselect() {
    global $CFG;
    return 'SELECT ra.userid,
                ra.roleid AS primary_roleid,
                ra.contextid,
                r.sortorder,
                r.name,
                r.description,
                r.shortname,
                c.instanceid AS courseid,
                c.contextlevel
            FROM '.$CFG->prefix.'role_assignments ra
            INNER JOIN '.$CFG->prefix.'role r ON ra.roleid = r.id
            INNER JOIN '.$CFG->prefix.'context c ON ra.contextid = c.id
            WHERE NOT EXISTS ( 
                              SELECT 1
                              FROM '.$CFG->prefix.'role_assignments i_ra
                              INNER JOIN '.$CFG->prefix.'role i_r ON i_ra.roleid = i_r.id
                              WHERE ra.userid = i_ra.userid AND 
                                     ra.contextid = i_ra.contextid AND 
                                     i_r.sortorder < r.sortorder
                              ) ';
}

/**
 * Prepare a SQL WHERE clause to select records where the given fields match the given values.
 *
 * Prepares a where clause of the form
 *     WHERE field1 = value1 AND field2 = value2 AND field3 = value3
 * except that you need only specify as many arguments (zero to three) as you need.
 *
 * @param string $field1 the first field to check (optional).
 * @param string $value1 the value field1 must have (requred if field1 is given, else optional).
 * @param string $field2 the second field to check (optional).
 * @param string $value2 the value field2 must have (requred if field2 is given, else optional).
 * @param string $field3 the third field to check (optional).
 * @param string $value3 the value field3 must have (requred if field3 is given, else optional).
 */
function where_clause($field1='', $value1='', $field2='', $value2='', $field3='', $value3='') {
    if ($field1) {
        $select = "WHERE $field1 = '$value1'";
        if ($field2) {
            $select .= " AND $field2 = '$value2'";
            if ($field3) {
                $select .= " AND $field3 = '$value3'";
            }
        }
    } else {
        $select = '';
    }
    return $select;
}

/**
 * Get the data type of a table column, using an ADOdb MetaType() call.
 *
 * @uses $CFG
 * @uses $db
 * @param string $table The name of the database table
 * @param string $column The name of the field in the table
 * @return string Field type or false if error
 */

function column_type($table, $column) {
    global $CFG, $db;

    if (defined('MDL_PERFDB')) { global $PERF ; $PERF->dbqueries++; };

    if(!$rs = $db->Execute('SELECT '.$column.' FROM '.$CFG->prefix.$table.' WHERE 1=2')) {
        return false;
    }

    $field = $rs->FetchField(0);
    return $rs->MetaType($field->type);
}

/**
 * This function will execute an array of SQL commands, returning
 * true/false if any error is found and stopping/continue as desired.
 * It's widely used by all the ddllib.php functions
 *
 * @param array sqlarr array of sql statements to execute
 * @param boolean continue to specify if must continue on error (true) or stop (false)
 * @param boolean feedback to specify to show status info (true) or not (false)
 * @param boolean true if everything was ok, false if some error was found
 */
function execute_sql_arr($sqlarr, $continue=true, $feedback=true) {

    if (!is_array($sqlarr)) {
        return false;
    }

    $status = true;
    foreach($sqlarr as $sql) {
        if (!execute_sql($sql, $feedback)) {
            $status = false;
            if (!$continue) {
                break;
            }
        }
    }
    return $status;
}

/**
 * This function, called from setup.php includes all the configuration
 * needed to properly work agains any DB. It setups connection encoding
 * and some other variables.
 */
function configure_dbconnection() {

    global $CFG, $db;

    switch ($CFG->dbtype) {
        case 'mysql':
        /// Set names if needed
            if ($CFG->unicodedb) {
                $db->Execute("SET NAMES 'utf8'");
            }
            break;
        case 'postgres7':
        /// Set names if needed
            if ($CFG->unicodedb) {
                $db->Execute("SET NAMES 'utf8'");
            }
            break;
        case 'mssql':
        case 'mssql_n':
        case 'odbc_mssql':
        /// No need to set charset. It must be specified in the driver conf
        /// Allow quoted identifiers
            $db->Execute('SET QUOTED_IDENTIFIER ON');
        /// Force ANSI nulls so the NULL check was done by IS NULL and NOT IS NULL
        /// instead of equal(=) and distinct(<>) simbols
            $db->Execute('SET ANSI_NULLS ON');
        /// Enable sybase quotes, so addslashes and stripslashes will use "'"
            ini_set('magic_quotes_sybase', '1');
        /// NOTE: Not 100% useful because GPC has been addslashed with the setting off
        ///       so IT'S MANDATORY TO CHANGE THIS UNDER php.ini or .htaccess for this DB
        ///       or to turn off magic_quotes to allow Moodle to do it properly
            break;
        case 'oci8po':
        /// No need to set charset. It must be specified by the NLS_LANG env. variable
        /// Enable sybase quotes, so addslashes and stripslashes will use "'"
            ini_set('magic_quotes_sybase', '1');
        /// NOTE: Not 100% useful because GPC has been addslashed with the setting off
        ///       so IT'S MANDATORY TO ENABLE THIS UNDER php.ini or .htaccess for this DB
        ///       or to turn off magic_quotes to allow Moodle to do it properly
            break;
    }
}

/**
 * This function will handle all the records before being inserted/updated to DB for Oracle
 * installations. This is because the "special feature" of Oracle where the empty string is
 * equal to NULL and this presents a problem with all our currently NOT NULL default '' fields.
 *
 * Once Moodle DB will be free of this sort of false NOT NULLS, this hack could be removed safely
 *
 * Note that this function is 100% private and should be used, exclusively by DML functions
 * in this file. Also, this is considered a DIRTY HACK to be removed when possible. (stronk7)
 *
 * This function is private and must not be used outside dmllib at all
 *
 * @param $table string the table where the record is going to be inserted/updated (without prefix)
 * @param $dataobject object the object to be inserted/updated
 * @param $usecache boolean flag to determinate if we must use the per request cache of metadata
 *        true to use it, false to ignore and delete it
 */
function oracle_dirty_hack ($table, &$dataobject, $usecache = true) {

    global $CFG, $db, $metadata_cache;

/// Init and delete metadata cache
    if (!isset($metadata_cache) || !$usecache) {
        $metadata_cache = array();
    }

/// For Oracle DB, empty strings are converted to NULLs in DB
/// and this breaks a lot of NOT NULL columns currenty Moodle. In the future it's
/// planned to move some of them to NULL, if they must accept empty values and this
/// piece of code will become less and less used. But, for now, we need it.
/// What we are going to do is to examine all the data being inserted and if it's
/// an empty string (NULL for Oracle) and the field is defined as NOT NULL, we'll modify
/// such data in the best form possible ("0" for booleans and numbers and " " for the
/// rest of strings. It isn't optimal, but the only way to do so.
/// In the oppsite, when retrieving records from Oracle, we'll decode " " back to
/// empty strings to allow everything to work properly. DIRTY HACK.

/// If the db isn't Oracle, return without modif
    if ( $CFG->dbtype != 'oci8po') {
        return;
    }

/// Get Meta info to know what to change, using the cached meta if exists
    if (!isset($metadata_cache[$table])) {
        $metadata_cache[$table] = array_change_key_case($db->MetaColumns($CFG->prefix . $table), CASE_LOWER);
    }
    $columns = $metadata_cache[$table];
/// Iterate over all the fields in the insert, transforming values
/// in the best possible form
    foreach ($dataobject as $fieldname => $fieldvalue) {
    /// If the field doesn't exist in metadata, skip
        if (!isset($columns[strtolower($fieldname)])) {
            continue;
        }
    /// If the field ins't VARCHAR or CLOB, skip
        if ($columns[strtolower($fieldname)]->type != 'VARCHAR2' && $columns[strtolower($fieldname)]->type != 'CLOB') {
            continue;
        }
    /// If the field isn't NOT NULL, skip (it's nullable, so accept empty values)
        if (!$columns[strtolower($fieldname)]->not_null) {
            continue;
        }
    /// If the value isn't empty, skip
        if (!empty($fieldvalue)) {
            continue;
        }
    /// Now, we have one empty value, going to be inserted to one NOT NULL, VARCHAR2 or CLOB field
    /// Try to get the best value to be inserted
        if (gettype($fieldvalue) == 'boolean') {
            $dataobject->$fieldname = '0'; /// Transform false to '0' that evaluates the same for PHP
        } else if (gettype($fieldvalue) == 'integer') {
            $dataobject->$fieldname = '0'; /// Transform 0 to '0' that evaluates the same for PHP
        } else if (gettype($fieldvalue) == 'NULL') {
            $dataobject->$fieldname = '0'; /// Transform NULL to '0' that evaluates the same for PHP
        } else {
            $dataobject->$fieldname = ' '; /// Transform '' to ' ' that DONT'T EVALUATE THE SAME
                                           /// (we'll transform back again on get_records_XXX functions and others)!!
        }
    }
}
/// End of DIRTY HACK

/**
 * This function will search for all the CLOBs and BLOBs fields passed in the dataobject, replacing
 * their contents by the fixed strings '@#CLOB#@' and '@#BLOB#@' and returning one array for all the
 * found CLOBS and another for all the found BLOBS
 * Used by Oracle drivers to perform the two-step insertion/update of LOBs and
 * by MSSQL to perform the same exclusively for BLOBs (IMAGE fields)
 *
 * This function is private and must not be used outside dmllib at all
 *
 * @param $table string the table where the record is going to be inserted/updated (without prefix)
 * @param $dataobject object the object to be inserted/updated
 * @param $clobs array of clobs detected
 * @param $dataobject array of blobs detected
 * @param $unset boolean to specify if we must unset found LOBs from the original object (true) or
 *        just return them modified to @#CLOB#@ and @#BLOB#@ (false)
 * @param $usecache boolean flag to determinate if we must use the per request cache of metadata
 *        true to use it, false to ignore and delete it
 */
function db_detect_lobs ($table, &$dataobject, &$clobs, &$blobs, $unset = false, $usecache = true) {

    global $CFG, $db, $metadata_cache;

    $dataarray = (array)$dataobject; //Convert to array. It's supposed that PHP 4.3 doesn't iterate over objects

/// Initial configuration, based on DB
    switch ($CFG->dbtype) {
        case 'oci8po':
            $clobdbtype = 'CLOB'; //Name of clobs for this DB
            $blobdbtype = 'BLOB'; //Name of blobs for this DB
            break;
        case 'mssql':
        case 'odbc_mssql':
        case 'mssql_n':
            $clobdbtype = 'NOTPROCESSES'; //Name of clobs for this DB (under mssql flavours we don't process CLOBS)
            $blobdbtype = 'IMAGE'; //Name of blobs for this DB
            break;
        default:
            return; //Other DB doesn't need this two step to happen, prevent continue
    }

/// Init and delete metadata cache
    if (!isset($metadata_cache) || !$usecache) {
        $metadata_cache = array();
    }

/// Get Meta info to know what to change, using the cached meta if exists
    if (!isset($metadata_cache[$table])) {
        $metadata_cache[$table] = array_change_key_case($db->MetaColumns($CFG->prefix . $table), CASE_LOWER);
    }
    $columns = $metadata_cache[$table];

    foreach ($dataarray as $fieldname => $fieldvalue) {
    /// If the field doesn't exist in metadata, skip
        if (!isset($columns[strtolower($fieldname)])) {
            continue;
        }
    /// If the field is CLOB, update its value to '@#CLOB#@' and store it in the $clobs array
        if (strtoupper($columns[strtolower($fieldname)]->type) == $clobdbtype) { // && strlen($dataobject->$fieldname) > 3999
            $clobs[$fieldname] = $dataobject->$fieldname;
            if ($unset) {
                unset($dataobject->$fieldname);
            } else {
                $dataobject->$fieldname = '@#CLOB#@';
            }
            continue;
        }

    /// If the field is BLOB OR IMAGE, update its value to '@#BLOB#@' and store it in the $blobs array
        if (strtoupper($columns[strtolower($fieldname)]->type) == $blobdbtype) { // && strlen($dataobject->$fieldname) > 3999
            $blobs[$fieldname] = $dataobject->$fieldname;
            if ($unset) {
                unset($dataobject->$fieldname);
            } else {
                $dataobject->$fieldname = '@#BLOB#@';
            }
            continue;
        }
    }
}

/**
 * This function will iterate over $clobs and $blobs array, executing the needed
 * UpdateClob() and UpdateBlob() ADOdb function calls to store LOBs contents properly
 * Records to be updated are always searched by PK (id always!)
 *
 * Used by Orace CLOBS and BLOBS and MSSQL IMAGES
 *
 * This function is private and must not be used outside dmllib at all
 *
 * @param $table string the table where the record is going to be inserted/updated (without prefix)
 * @param $sqlcondition mixed value defining the records to be LOB-updated. It it's a number, must point
 *        to the PK og the table (id field), else it's processed as one harcoded SQL condition (WHERE clause)
 * @param $clobs array of clobs to be updated
 * @param $blobs array of blobs to be updated
 */
function db_update_lobs ($table, $sqlcondition, &$clobs, &$blobs) {

    global $CFG, $db;

    $status = true;

/// Initial configuration, based on DB
    switch ($CFG->dbtype) {
        case 'oci8po':
            $clobdbtype = 'CLOB'; //Name of clobs for this DB
            $blobdbtype = 'BLOB'; //Name of blobs for this DB
            break;
        case 'mssql':
        case 'odbc_mssql':
        case 'mssql_n':
            $clobdbtype = 'NOTPROCESSES'; //Name of clobs for this DB (under mssql flavours we don't process CLOBS)
            $blobdbtype = 'IMAGE'; //Name of blobs for this DB
            break;
        default:
            return; //Other DB doesn't need this two step to happen, prevent continue
    }

/// Calculate the update sql condition
    if (is_numeric($sqlcondition)) { /// If passing a number, it's the PK of the table (id)
        $sqlcondition = 'id=' . $sqlcondition;
    } else { /// Else, it's a formal standard SQL condition, we try to delete the WHERE in case it exists
        $sqlcondition = trim(preg_replace('/^WHERE/is', '', trim($sqlcondition)));
    }

/// Update all the clobs
    if ($clobs) {
        foreach ($clobs as $key => $value) {
        
            if (defined('MDL_PERFDB')) { global $PERF ; $PERF->dbqueries++; }; /// Count the extra updates in PERF

            if (!$db->UpdateClob($CFG->prefix.$table, $key, $value, $sqlcondition)) {
                $status = false;
                $statement = "UpdateClob('$CFG->prefix$table', '$key', '" . substr($value, 0, 100) . "...', '$sqlcondition')";
                debugging($db->ErrorMsg() ."<br /><br />$statement");
                if (!empty($CFG->dblogerror)) {
                    $debug=array_shift(debug_backtrace());
                    error_log("SQL ".$db->ErrorMsg()." in {$debug['file']} on line {$debug['line']}. STATEMENT:  $statement");
                }
            }
        }
    }
/// Update all the blobs
    if ($blobs) {
        foreach ($blobs as $key => $value) {
        
            if (defined('MDL_PERFDB')) { global $PERF ; $PERF->dbqueries++; }; /// Count the extra updates in PERF

            if(!$db->UpdateBlob($CFG->prefix.$table, $key, $value, $sqlcondition)) {
                $status = false;
                $statement = "UpdateBlob('$CFG->prefix$table', '$key', '" . substr($value, 0, 100) . "...', '$sqlcondition')";
                debugging($db->ErrorMsg() ."<br /><br />$statement");
                if (!empty($CFG->dblogerror)) {
                    $debug=array_shift(debug_backtrace());
                    error_log("SQL ".$db->ErrorMsg()." in {$debug['file']} on line {$debug['line']}. STATEMENT:  $statement");
                }
            }
        }
    }
    return $status;
}

?>
