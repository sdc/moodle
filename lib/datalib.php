<?PHP // $Id$

/// GLOBAL CONSTANTS /////////////////////////////////////////////////////////////
if ($SITE = get_site()) {
    define('SITEID', $SITE->id);
} else {
    define('SITEID', 1);
}


/// FUNCTIONS FOR DATABASE HANDLING  ////////////////////////////////
/**
* execute a given sql command string
*
* Completely general function - it just runs some SQL and reports success.
*
* @param    type description
*/
function execute_sql($command, $feedback=true) {
/// Completely general function - it just runs some SQL and reports success.

    global $db;

    $olddebug = $db->debug;

    if (!$feedback) {
        $db->debug = false;
    }

    $result = $db->Execute("$command");

    $db->debug = $olddebug;

    if ($result) {
        if ($feedback) {
            echo "<P><FONT COLOR=green><B>".get_string("success")."</B></FONT></P>";
        }
        return true;
    } else {
        if ($feedback) {
            echo "<P><FONT COLOR=red><B>".get_string("error")."</B></FONT></P>";
        }
        return false;
    }
}
/**
* on DBs that support it, switch to transaction mode and begin a transaction
* you'll need to ensure you call commit_sql() or your changes *will* be lost
* this is _very_ useful for massive updates
*/
function begin_sql() {
/// Completely general function - it just runs some SQL and reports success.

    global $CFG;
    if ($CFG->dbtype === 'postgres7') {
        return execute_sql('BEGIN', false);
    }
    return true;
}

/**
 * returns db specific uppercase function
 */
function db_uppercase() {
    global $CFG;
    switch (strtolower($CFG->dbtype)) {

    case "postgres7":
        return "upper";
        break;
    case "mysql":
    default:
        return "ucase";
        break;
    }
}

/**
 * returns db specific lowercase function
 */
function db_lowercase() {
    global $CFG;
    switch (strtolower($CFG->dbtype)) {

    case "postgres7":
        return "lower";
        break;
    case "mysql":
    default:
        return "lcase";
        break;
    }
}

/**
* on DBs that support it, commit the transaction 
*/
function commit_sql() {
/// Completely general function - it just runs some SQL and reports success.

    global $CFG;
    if ($CFG->dbtype === 'postgres7') {
        return execute_sql('COMMIT', false);
    }
    return true;
}
/**
* Run an arbitrary sequence of semicolon-delimited SQL commands
*
* Assumes that the input text (file or string) consists of
* a number of SQL statements ENDING WITH SEMICOLONS.  The
* semicolons MUST be the last character in a line.
* Lines that are blank or that start with "#" are ignored.
* Only tested with mysql dump files (mysqldump -p -d moodle)
*
* @param    type description
*/

function modify_database($sqlfile="", $sqlstring="") {

    global $CFG;

    $success = true;  // Let's be optimistic

    if (!empty($sqlfile)) {
        if (!is_readable($sqlfile)) {
            $success = false;
            echo "<P>Tried to modify database, but \"$sqlfile\" doesn't exist!</P>";
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

    $command = "";

    foreach ($lines as $line) {
        $line = rtrim($line);
        $length = strlen($line);

        if ($length and $line[0] <> "#") {
            if (substr($line, $length-1, 1) == ";") {
                $line = substr($line, 0, $length-1);   // strip ;
                $command .= $line;
                $command = str_replace("prefix_", $CFG->prefix, $command); // Table prefixes
                if (! execute_sql($command)) {
                    $success = false;
                }
                $command = "";
            } else {
                $command .= $line;
            }
        }
    }

    return $success;

}

/// FUNCTIONS TO MODIFY TABLES ////////////////////////////////////////////

/**
* Add a new field to a table, or modify an existing one (if oldfield is defined).
*
* Add a new field to a table, or modify an existing one (if oldfield is defined).
*
* @param    type description
*/

function table_column($table, $oldfield, $field, $type="integer", $size="10",
                      $signed="unsigned", $default="0", $null="not null", $after="") {
    global $CFG, $db;

    switch (strtolower($CFG->dbtype)) {

        case "mysql":
        case "mysqlt":

            switch (strtolower($type)) {
                case "text":
                    $type = "TEXT";
                    $signed = "";
                    break;
                case "integer":
                    $type = "INTEGER($size)";
                    break;
                case "varchar":
                    $type = "VARCHAR($size)";
                    $signed = "";
                    break;
            }

            if (!empty($oldfield)) {
                $operation = "CHANGE $oldfield $field";
            } else {
                $operation = "ADD $field";
            }

            $default = "DEFAULT '$default'";

            if (!empty($after)) {
                $after = "AFTER `$after`";
            }

            return execute_sql("ALTER TABLE {$CFG->prefix}$table $operation $type $signed $default $null $after");
            break;

        case "postgres7":        // From Petri Asikainen
            //Check db-version
            $dbinfo = $db->ServerInfo();
            $dbver = substr($dbinfo['version'],0,3);

            //to prevent conflicts with reserved words
            $realfield = "\"$field\"";
            $field = "\"${field}_alter_column_tmp\"";
            $oldfield = "\"$oldfield\"";

            switch (strtolower($type)) {
                case "integer":
                    if ($size <= 4) {
                        $type = "INT2";
                    }
                    if ($size <= 10) {
                        $type = "INT";
                    }
                    if  ($size > 10) {
                        $type = "INT8";
                    }
                    break;
                case "varchar":
                    $type = "VARCHAR($size)";
                    break;
            }

            $default = "'$default'";

            //After is not implemented in postgesql
            //if (!empty($after)) {
            //    $after = "AFTER '$after'";
            //}

            //Use transactions
            execute_sql("BEGIN");

            //Allways use temporaly column
            execute_sql("ALTER TABLE {$CFG->prefix}$table ADD COLUMN $field $type");
            //Add default values
            execute_sql("UPDATE {$CFG->prefix}$table SET $field=$default");


            if ($dbver >= "7.3") {
                // modifying 'not null' is posible before 7.3
                //update default values to table
                if ($null == "NOT NULL") {
                    execute_sql("UPDATE {$CFG->prefix}$table SET $field=$default where $field IS NULL");
                    execute_sql("ALTER TABLE {$CFG->prefix}$table ALTER COLUMN $field SET $null");
                } else {
                    execute_sql("ALTER TABLE {$CFG->prefix}$table ALTER COLUMN $field DROP NOT NULL");
                }
            }

            execute_sql("ALTER TABLE {$CFG->prefix}$table ALTER COLUMN $field SET DEFAULT $default");

            if ( $oldfield != "\"\"" ) {
                execute_sql("UPDATE {$CFG->prefix}$table SET $field = $oldfield");
                execute_sql("ALTER TABLE  {$CFG->prefix}$table drop column $oldfield");
            }

            execute_sql("ALTER TABLE {$CFG->prefix}$table RENAME COLUMN $field TO $realfield");

            return execute_sql("COMMIT");
            break;

        default:
            switch (strtolower($type)) {
                case "integer":
                    $type = "INTEGER";
                    break;
                case "varchar":
                    $type = "VARCHAR";
                    break;
            }

            $default = "DEFAULT '$default'";

            if (!empty($after)) {
                $after = "AFTER $after";
            }

            if (!empty($oldfield)) {
                execute_sql("ALTER TABLE {$CFG->prefix}$table RENAME COLUMN $oldfield $field");
            } else {
                execute_sql("ALTER TABLE {$CFG->prefix}$table ADD COLUMN $field $type");
            }

            execute_sql("ALTER TABLE {$CFG->prefix}$table ALTER COLUMN $field SET $null");
            return execute_sql("ALTER TABLE {$CFG->prefix}$table ALTER COLUMN $field SET $default");
            break;

    }
}



/// GENERIC FUNCTIONS TO CHECK AND COUNT RECORDS ////////////////////////////////////////

/**
* Returns true or false depending on whether the specified record exists
*
* Returns true or false depending on whether the specified record exists
*
* @param    type description
*/
function record_exists($table, $field1="", $value1="", $field2="", $value2="", $field3="", $value3="") {

    global $CFG;

    if ($field1) {
        $select = "WHERE $field1 = '$value1'";
        if ($field2) {
            $select .= " AND $field2 = '$value2'";
            if ($field3) {
                $select .= " AND $field3 = '$value3'";
            }
        }
    } else {
        $select = "";
    }

    return record_exists_sql("SELECT * FROM $CFG->prefix$table $select LIMIT 1");
}


/**
* Returns true or false depending on whether the specified record exists
*
* The sql statement is provided as a string.
*
* @param    type description
*/
function record_exists_sql($sql) {

    global $CFG, $db;

    if (!$rs = $db->Execute($sql)) {
        if (isset($CFG->debug) and $CFG->debug > 7) {
            notify($db->ErrorMsg()."<br /><br />$sql");
        }
        return false;
    }

    if ( $rs->RecordCount() ) {
        return true;
    } else {
        return false;
    }
}


/**
* Get all the records and count them
*
* Get all the records and count them
*
* @param    type description
*/
function count_records($table, $field1="", $value1="", $field2="", $value2="", $field3="", $value3="") {

    global $CFG;

    if ($field1) {
        $select = "WHERE $field1 = '$value1'";
        if ($field2) {
            $select .= " AND $field2 = '$value2'";
            if ($field3) {
                $select .= " AND $field3 = '$value3'";
            }
        }
    } else {
        $select = "";
    }

    return count_records_sql("SELECT COUNT(*) FROM $CFG->prefix$table $select");
}

/**
* Get all the records and count them
*
* Get all the records and count them
*
* @param    type description
*
*/
function count_records_select($table, $select="", $countitem="COUNT(*)") {

    global $CFG;

    if ($select) {
        $select = "WHERE $select";
    }

    return count_records_sql("SELECT $countitem FROM $CFG->prefix$table $select");
}


/**
* Get all the records and count them
*
* The sql statement is provided as a string.
*
* @param    type description
*/
function count_records_sql($sql) {

    global $CFG, $db;

    $rs = $db->Execute("$sql");
    if (!$rs) {
        if (isset($CFG->debug) and $CFG->debug > 7) {
            notify($db->ErrorMsg()."<br /><br />$sql");
        }
        return 0;
    }

    return $rs->fields[0];
}




/// GENERIC FUNCTIONS TO GET, INSERT, OR UPDATE DATA  ///////////////////////////////////

/**
* Get a single record as an object
*
* Get a single record as an object
*
* @param    string  $table the name of the table to select from
* @param    string  $field1 the name of the field for the first criteria
* @param    string  $value1 the value of the field for the first criteria
* @param    string  $field2 the name of the field for the second criteria
* @param    string  $value2 the value of the field for the second criteria
* @param    string  $field3 the name of the field for the third criteria
* @param    string  $value3 the value of the field for the third criteria
* @return   object(fieldset)    a fieldset object containing the first record selected
*/
function get_record($table, $field1, $value1, $field2="", $value2="", $field3="", $value3="") {

    global $CFG;

    $select = "WHERE $field1 = '$value1'";

    if ($field2) {
        $select .= " AND $field2 = '$value2'";
        if ($field3) {
            $select .= " AND $field3 = '$value3'";
        }
    }

    return get_record_sql("SELECT * FROM $CFG->prefix$table $select");
}

/**
* Get a single record as an object
*
* The sql statement is provided as a string.
* A LIMIT is normally added to only look for 1 record
*
* @param    type description
*/
function get_record_sql($sql) {

    global $db, $CFG;

    if (isset($CFG->debug) and $CFG->debug > 7) {    // Debugging mode - don't use limit
       $limit = "";
    } else {
       $limit = " LIMIT 1";    // Workaround - limit to one record
    }

    if (!$rs = $db->Execute("$sql$limit")) {
        if (isset($CFG->debug) and $CFG->debug > 7) {    // Debugging mode - print checks
            notify( $db->ErrorMsg() . "<br /><br />$sql$limit" );
        }
        return false;
    }

    if (!$recordcount = $rs->RecordCount()) {
        return false;                 // Found no records
    }

    if ($recordcount == 1) {          // Found one record
        return (object)$rs->fields;

    } else {                          // Error: found more than one record
        notify("Error:  Turn off debugging to hide this error.");
        notify("$sql$limit");
        if ($records = $rs->GetAssoc(true)) {
            notify("Found more than one record in get_record_sql !");
            print_object($records);
        } else {
            notify("Very strange error in get_record_sql !");
            print_object($rs);
        }
        print_continue("$CFG->wwwroot/admin/config.php");
    }
}

/**
* Gets one record from a table, as an object
*
* "select" is a fragment of SQL to define the selection criteria
*
* @param    type description
*/
function get_record_select($table, $select="", $fields="*") {

    global $CFG;

    if ($select) {
        $select = "WHERE $select";
    }

    return get_record_sql("SELECT $fields FROM $CFG->prefix$table $select");
}


/**
* Get a number of records as an array of objects
*
* Can optionally be sorted eg "time ASC" or "time DESC"
* If "fields" is specified, only those fields are returned
* The "key" is the first column returned, eg usually "id"
* limitfrom and limitnum must both be specified or not at all
*
* @param    type description
*/
function get_records($table, $field="", $value="", $sort="", $fields="*", $limitfrom="", $limitnum="") {

    global $CFG;

    if ($field) {
        $select = "WHERE $field = '$value'";
    } else {
        $select = "";
    }

    if ($limitfrom !== "") {
        switch ($CFG->dbtype) {
            case "mysql":
                 $limit = "LIMIT $limitfrom,$limitnum";
                 break;
            case "postgres7":
                 $limit = "LIMIT $limitnum OFFSET $limitfrom";
                 break;
            default:
                 $limit = "LIMIT $limitnum,$limitfrom";
        }
    } else {
        $limit = "";
    }

    if ($sort) {
        $sort = "ORDER BY $sort";
    }

    return get_records_sql("SELECT $fields FROM $CFG->prefix$table $select $sort $limit");
}

/**
* Get a number of records as an array of objects
*
* Can optionally be sorted eg "time ASC" or "time DESC"
* "select" is a fragment of SQL to define the selection criteria
* The "key" is the first column returned, eg usually "id"
* limitfrom and limitnum must both be specified or not at all
*
* @param    type description
*/
function get_records_select($table, $select="", $sort="", $fields="*", $limitfrom="", $limitnum="") {

    global $CFG;

    if ($select) {
        $select = "WHERE $select";
    }

    if ($limitfrom !== "") {
        switch ($CFG->dbtype) {
            case "mysql":
                 $limit = "LIMIT $limitfrom,$limitnum";
                 break;
            case "postgres7":
                 $limit = "LIMIT $limitnum OFFSET $limitfrom";
                 break;
            default:
                 $limit = "LIMIT $limitnum,$limitfrom";
        }
    } else {
        $limit = "";
    }

    if ($sort) {
        $sort = "ORDER BY $sort";
    }

    return get_records_sql("SELECT $fields FROM $CFG->prefix$table $select $sort $limit");
}


/**
* Get a number of records as an array of objects
*
* Differs from get_records() in that the values variable
* can be a comma-separated list of values eg  "4,5,6,10"
* Can optionally be sorted eg "time ASC" or "time DESC"
* The "key" is the first column returned, eg usually "id"
*
* @param    type description
*/
function get_records_list($table, $field="", $values="", $sort="", $fields="*") {

    global $CFG;

    if ($field) {
        $select = "WHERE $field in ($values)";
    } else {
        $select = "";
    }

    if ($sort) {
        $sort = "ORDER BY $sort";
    }

    return get_records_sql("SELECT $fields FROM $CFG->prefix$table $select $sort");
}



/**
* Get a number of records as an array of objects
*
* The "key" is the first column returned, eg usually "id"
* The sql statement is provided as a string.
*
* @param    type description
*/
function get_records_sql($sql) {

    global $CFG,$db;

    if (!$rs = $db->Execute($sql)) {
        if (isset($CFG->debug) and $CFG->debug > 7) {
            notify($db->ErrorMsg()."<br /><br />$sql");
        }
        return false;
    }

    if ( $rs->RecordCount() > 0 ) {
        if ($records = $rs->GetAssoc(true)) {
            foreach ($records as $key => $record) {
                $objects[$key] = (object) $record;
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
* Get a number of records as an array of objects
*
* Can optionally be sorted eg "time ASC" or "time DESC"
* If "fields" is specified, only those fields are returned
* The "key" is the first column returned, eg usually "id"
*
* @param    type description
*/
function get_records_menu($table, $field="", $value="", $sort="", $fields="*") {

    global $CFG;

    if ($field) {
        $select = "WHERE $field = '$value'";
    } else {
        $select = "";
    }

    if ($sort) {
        $sort = "ORDER BY $sort";
    }

    return get_records_sql_menu("SELECT $fields FROM $CFG->prefix$table $select $sort");
}

/**
* Get a number of records as an array of objects
*
* Can optionally be sorted eg "time ASC" or "time DESC"
* "select" is a fragment of SQL to define the selection criteria
* Returns associative array of first two fields
*
* @param    type description
*/
function get_records_select_menu($table, $select="", $sort="", $fields="*") {

    global $CFG;

    if ($select) {
        $select = "WHERE $select";
    }

    if ($sort) {
        $sort = "ORDER BY $sort";
    }

    return get_records_sql_menu("SELECT $fields FROM $CFG->prefix$table $select $sort");
}


/**
* Given an SQL select, this function returns an associative
*
* array of the first two columns.  This is most useful in
* combination with the choose_from_menu function to create
* a form menu.
*
* @param    type description
*/
function get_records_sql_menu($sql) {

    global $CFG, $db;

    if (!$rs = $db->Execute($sql)) {
        if (isset($CFG->debug) and $CFG->debug > 7) {
            notify($db->ErrorMsg()."<br /><br />$sql");
        }
        return false;
    }

    if ( $rs->RecordCount() > 0 ) {
        while (!$rs->EOF) {
            $menu[$rs->fields[0]] = $rs->fields[1];
            $rs->MoveNext();
        }
        return $menu;

    } else {
        return false;
    }
}

/**
* Get a single field from a database record
*
* longdesc
*
* @param    type description
*/
function get_field($table, $return, $field1, $value1, $field2="", $value2="", $field3="", $value3="") {

    global $db, $CFG;

    $select = "WHERE $field1 = '$value1'";

    if ($field2) {
        $select .= " AND $field2 = '$value2'";
        if ($field3) {
            $select .= " AND $field3 = '$value3'";
        }
    }

    $rs = $db->Execute("SELECT $return FROM $CFG->prefix$table $select");
    if (!$rs) {
        if (isset($CFG->debug) and $CFG->debug > 7) {
            notify($db->ErrorMsg()."<br /><br />SELECT $return FROM $CFG->prefix$table $select");
        }
        return false;
    }

    if ( $rs->RecordCount() == 1 ) {
        return $rs->fields["$return"];
    } else {
        return false;
    }
}


/**
* Get a single field from a database record
*
* longdesc
*
* @param    type description
*/
function get_field_sql($sql) {

    global $db, $CFG;

    $rs = $db->Execute($sql);
    if (!$rs) {
        if (isset($CFG->debug) and $CFG->debug > 7) {
            notify($db->ErrorMsg()."<br /><br />$sql");
        }
        return false;
    }

    if ( $rs->RecordCount() == 1 ) {
        return $rs->fields[0];
    } else {
        return false;
    }
}

/**
* Set a single field in a database record
*
* longdesc
*
* @param    type description
*/
function set_field($table, $newfield, $newvalue, $field1, $value1, $field2="", $value2="", $field3="", $value3="") {

    global $db, $CFG;

    $select = "WHERE $field1 = '$value1'";

    if ($field2) {
        $select .= " AND $field2 = '$value2'";
        if ($field3) {
            $select .= " AND $field3 = '$value3'";
        }
    }

    return $db->Execute("UPDATE $CFG->prefix$table SET $newfield = '$newvalue' $select");
}


/**
* Delete one or more records from a table
*
* Delete one or more records from a table
*
* @param    type description
*/
function delete_records($table, $field1="", $value1="", $field2="", $value2="", $field3="", $value3="") {

    global $db, $CFG;

    if ($field1) {
        $select = "WHERE $field1 = '$value1'";
        if ($field2) {
            $select .= " AND $field2 = '$value2'";
            if ($field3) {
                $select .= " AND $field3 = '$value3'";
            }
        }
    } else {
        $select = "";
    }

    return $db->Execute("DELETE FROM $CFG->prefix$table $select");
}

/**
* Delete one or more records from a table
*
* "select" is a fragment of SQL to define the selection criteria
*
* @param    type description
*/
function delete_records_select($table, $select="") {

    global $CFG, $db;

    if ($select) {
        $select = "WHERE $select";
    }

    return $db->Execute("DELETE FROM $CFG->prefix$table $select");
}


/**
* Insert a record into a table and return the "id" field if required
*
* If the return ID isn't required, then this just reports success as true/false.
* $dataobject is an object containing needed data
*
* @param    type description
*/
function insert_record($table, $dataobject, $returnid=true, $primarykey='id') {

    global $db, $CFG;

/// Execute a dummy query to get an empty recordset
    if (!$rs = $db->Execute("SELECT * FROM $CFG->prefix$table WHERE $primarykey ='-1'")) {
        return false;
    }

/// Get the correct SQL from adoDB
    if (!$insertSQL = $db->GetInsertSQL($rs, (array)$dataobject, true)) {
        return false;
    }

/// Run the SQL statement
    if (!$rs = $db->Execute($insertSQL)) {
        if (isset($CFG->debug) and $CFG->debug > 7) {
            notify($db->ErrorMsg()."<br /><br />$insertSQL");
        }
        return false;
    }

/// If a return ID is not needed then just return true now
    if (!$returnid) {
        return true;
    }

/// Find the return ID of the newly inserted record
    switch ($CFG->dbtype) {
        case "postgres7":             // Just loves to be special
            $oid = $db->Insert_ID();
            if ($rs = $db->Execute("SELECT $primarykey FROM $CFG->prefix$table WHERE oid = $oid")) {
                if ($rs->RecordCount() == 1) {
                    return (integer) $rs->fields[0];
                }
            }
            return false;

        default:
            return $db->Insert_ID();  // Should work on most databases, but not all!
    }
}


/**
* Update a record in a table
*
* $dataobject is an object containing needed data
* Relies on $dataobject having a variable "id" to
* specify the record to update
*
* @param    type description
*/
function update_record($table, $dataobject) {

    global $db, $CFG;

    if (! isset($dataobject->id) ) {
        return false;
    }

    // Determine all the fields in the table
    if (!$columns = $db->MetaColumns("$CFG->prefix$table")) {
        return false;
    }
    $data = (array)$dataobject;

    // Pull out data matching these fields
    foreach ($columns as $column) {
        if ($column->name <> "id" and isset($data[$column->name]) ) {
            $ddd[$column->name] = $data[$column->name];
        }
    }

    // Construct SQL queries
    $numddd = count($ddd);
    $count = 0;
    $update = "";

    foreach ($ddd as $key => $value) {
        $count++;
        $update .= "$key = '$value'";
        if ($count < $numddd) {
            $update .= ", ";
        }
    }

    if ($rs = $db->Execute("UPDATE $CFG->prefix$table SET $update WHERE id = '$dataobject->id'")) {
        return true;
    } else {
        if (isset($CFG->debug) and $CFG->debug > 7) {
            notify($db->ErrorMsg()."<br /><br />UPDATE $CFG->prefix$table SET $update WHERE id = '$dataobject->id'");
        }
        return false;
    }
}




/// USER DATABASE ////////////////////////////////////////////////

/**
* Get a complete user record, which includes all the info
*
* in the user record, as well as membership information
* Suitable for setting as $USER session cookie.
*
* @param    type description
*/
function get_user_info_from_db($field, $value) {

    global $CFG;

    if (!$field or !$value) {
        return false;
    }

/// Get all the basic user data

    if (! $user = get_record_select("user", "$field = '$value' AND deleted <> '1'")) {
        return false;
    }

/// Add membership information

    if ($admins = get_records("user_admins", "userid", $user->id)) {
        $user->admin = true;
    }

    if ($site = get_site()) {
        $user->student[$site->id] = isstudent($site->id, $user->id);
    }

/// Determine enrolments based on current enrolment module

    require_once("$CFG->dirroot/enrol/$CFG->enrol/enrol.php");
    $enrol = new enrolment_plugin();
    $enrol->get_student_courses($user);
    $enrol->get_teacher_courses($user);

/// Get various settings and preferences

    if ($displays = get_records("course_display", "userid", $user->id)) {
        foreach ($displays as $display) {
            $user->display[$display->course] = $display->display;
        }
    }

    if ($preferences = get_records('user_preferences', 'userid', $user->id)) {
        foreach ($preferences as $preference) {
            $user->preference[$preference->name] = $preference->value;
        }
    }

    if ($groups = get_records("groups_members", "userid", $user->id)) {
        foreach ($groups as $groupmember) {
            $courseid = get_field("groups", "courseid", "id", $groupmember->groupid);
            $user->groupmember[$courseid] = $groupmember->groupid;
        }
    }


    return $user;
}

/**
* Updates user record to record their last access
*
* longdesc
*
*/
function update_user_in_db() {

   global $db, $USER, $REMOTE_ADDR, $CFG;

   if (!isset($USER->id))
       return false;

   $timenow = time();
   if ($db->Execute("UPDATE {$CFG->prefix}user SET lastIP='$REMOTE_ADDR', lastaccess='$timenow'
                     WHERE id = '$USER->id' ")) {
       return true;
   } else {
       return false;
   }
}


/**
* Does this username and password specify a valid admin user?
*
* longdesc
*
* @param    type description
*/
function adminlogin($username, $md5password) {

    global $CFG;

    return record_exists_sql("SELECT u.id
                                FROM {$CFG->prefix}user u,
                                     {$CFG->prefix}user_admins a
                              WHERE u.id = a.userid
                                AND u.username = '$username'
                                AND u.password = '$md5password'");
}


/**
* Get the guest user information from the database
*
* longdesc
*
* @param    type description
*/
function get_guest() {
    return get_user_info_from_db("username", "guest");
}


/**
* Returns $user object of the main admin user
*
* longdesc
*
* @param    type description
*/
function get_admin () {

    global $CFG;

    if ( $admins = get_admins() ) {
        foreach ($admins as $admin) {
            return $admin;   // ie the first one
        }
    } else {
        return false;
    }
}

/**
* Returns list of all admins
*
* longdesc
*
* @param    type description
*/
function get_admins() {

    global $CFG;

    return get_records_sql("SELECT u.*, a.id as adminid
                              FROM {$CFG->prefix}user u,
                                   {$CFG->prefix}user_admins a
                             WHERE a.userid = u.id
                             ORDER BY a.id ASC");
}

/**
* Returns list of all creators
*
* longdesc
*
* @param    type description
*/
function get_creators() {

    global $CFG;

    return get_records_sql("SELECT u.*
                              FROM {$CFG->prefix}user u,
                                   {$CFG->prefix}user_coursecreators a
                             WHERE a.userid = u.id
                             ORDER BY u.id ASC");
}

/**
* Returns $user object of the main teacher for a course
*
* longdesc
*
* @param    type description
*/
function get_teacher($courseid) {

    global $CFG;

    if ( $teachers = get_course_teachers($courseid, "t.authority ASC")) {
        foreach ($teachers as $teacher) {
            if ($teacher->authority) {
                return $teacher;   // the highest authority teacher
            }
        }
    } else {
        return false;
    }
}

/**
* Searches logs to find all enrolments since a certain date
* 
* used to print recent activity
*
* @param    type description
*/
function get_recent_enrolments($courseid, $timestart) {

    global $CFG;

    return get_records_sql("SELECT DISTINCT u.id, u.firstname, u.lastname, l.time
                            FROM {$CFG->prefix}user u,
                                 {$CFG->prefix}user_students s,
                                 {$CFG->prefix}log l
                            WHERE l.time > '$timestart'
                              AND l.course = '$courseid'
                              AND l.module = 'course'
                              AND l.action = 'enrol'
                              AND l.info = u.id
                              AND u.id = s.userid
                              AND s.course = '$courseid'
                              ORDER BY l.time ASC");
}

/**
* Returns array of userinfo of all students in this course
* or on this site if courseid is id of site
*
* @param    type description
*/
function get_course_students($courseid, $sort="s.timeaccess", $dir="", $page=0, $recordsperpage=99999,
                             $firstinitial="", $lastinitial="", $group=NULL, $search="", $fields='', $exceptions='') {

    global $CFG;

    if ($courseid == SITEID and $CFG->allusersaresitestudents) {
        // return users with confirmed, undeleted accounts who are not site teachers
        // the following is a mess because of different conventions in the different user functions
        $sort = str_replace('s.timeaccess', 'lastaccess', $sort); // site users can't be sorted by timeaccess
        $sort = str_replace('timeaccess', 'lastaccess', $sort); // site users can't be sorted by timeaccess
        $sort = str_replace('u.', '', $sort); // the get_user function doesn't use the u. prefix to fields
        $fields = str_replace('u.', '', $fields);
        if ($sort) {
            $sort = "$sort $dir";
        }
        // Now we have to make sure site teachers are excluded
        if ($teachers = get_records('user_teachers', 'course', SITEID)) {
            foreach ($teachers as $teacher) {
                $exceptions .= ",$teacher->userid";
            }
            $exceptions = ltrim($exceptions, ',');
        }
        return get_users(true, $search, true, $exceptions, $sort, $firstinitial, $lastinitial,
                          $page, $recordsperpage, $fields ? $fields : '*');
    }

    switch ($CFG->dbtype) {
        case "mysql":
             $fullname = " CONCAT(firstname,\" \",lastname) ";
             $limit = "LIMIT $page,$recordsperpage";
             $LIKE = "LIKE";
             break;
        case "postgres7":
             $fullname = " firstname||' '||lastname ";
             $limit = "LIMIT $recordsperpage OFFSET ".($page);
             $LIKE = "ILIKE";
             break;
        default:
             $fullname = " firstname||\" \"||lastname ";
             $limit = "LIMIT $recordsperpage,$page";
             $LIKE = "ILIKE";
    }

    $groupmembers = '';

    // make sure it works on the site course
    $select = "s.course = '$courseid' AND ";
    if ($courseid == SITEID) {
        $select = '';
    }

    $select .= "s.userid = u.id AND u.deleted = '0' ";

    if (!$fields) {
        $fields = 'u.id, u.confirmed, u.username, u.firstname, u.lastname, '.
                  'u.maildisplay, u.mailformat, u.maildigest, u.email, u.city, '.
                  'u.country, u.picture, u.idnumber, u.department, u.institution, '.
                  'u.emailstop, u.lang, u.timezone, s.timeaccess as lastaccess';
    }

    if ($search) {
        $search = " AND ($fullname $LIKE '%$search%' OR email $LIKE '%$search%') ";
    }

    if ($firstinitial) {
        $select .= " AND u.firstname $LIKE '$firstinitial%' ";
    }

    if ($lastinitial) {
        $select .= " AND u.lastname $LIKE '$lastinitial%' ";
    }

    if ($group === 0) {   /// Need something here to get all students not in a group
        return array();

    } else if ($group !== NULL) {
        $groupmembers = ", {$CFG->prefix}groups_members gm ";
        $select .= " AND u.id = gm.userid AND gm.groupid = '$group'";
    }

    if (!empty($exceptions)) {
        $select .= " AND u.id NOT IN ($exceptions)";
    }

    if ($sort) {
        $sort = " ORDER BY $sort ";
    }

    $students = get_records_sql("SELECT $fields
                            FROM {$CFG->prefix}user u,
                                 {$CFG->prefix}user_students s
                                 $groupmembers
                            WHERE $select $search $sort $dir $limit");

    if ($courseid != SITEID) {
        return $students;
    }

    // We are here because we need the students for the site.
    // These also include teachers on real courses minus those on the site
    if ($teachers = get_records('user_teachers', 'course', SITEID)) {
        foreach ($teachers as $teacher) {
            $exceptions .= ",$teacher->userid";
        }
        $exceptions = ltrim($exceptions, ',');
        $select .= " AND u.id NOT IN ($exceptions)";
    }
    if (!$teachers = get_records_sql("SELECT $fields
                            FROM {$CFG->prefix}user u,
                                 {$CFG->prefix}user_teachers s
                                 $groupmembers
                            WHERE $select $search $sort $dir $limit")) {
        return $students;
    }
    if (!$students) {
        return $teachers;
    }
    return $teachers + $students;
}

/**
* Counts the students in a given course (or site), or a subset of them
*
* @param    type description
*/
function count_course_students($course, $search="", $firstinitial="", $lastinitial="", $group=NULL, $exceptions='') {

    if ($students = get_course_students($course->id, '', '', 0, 999999, $firstinitial, $lastinitial, $group, $search, '', $exceptions)) {
        return count($students);
    }
    return 0;
}


/**
* Returns list of all teachers in this course
* (also works for site)
*
* @param    type description
*/
function get_course_teachers($courseid, $sort="t.authority ASC", $exceptions='') {

    global $CFG;

    if (!empty($exceptions)) {
        $exceptions = " AND u.id NOT IN ($exceptions) ";
    }

    if (!empty($sort)) {
        $sort = " ORDER by $sort";
    }

    return get_records_sql("SELECT u.id, u.username, u.firstname, u.lastname, u.maildisplay, u.mailformat, u.maildigest,
                                   u.email, u.city, u.country, u.lastlogin, u.picture, u.lang, u.timezone,
                                   u.emailstop, t.authority,t.role,t.editall,t.timeaccess as lastaccess
                            FROM {$CFG->prefix}user u,
                                 {$CFG->prefix}user_teachers t
                            WHERE t.course = '$courseid' AND t.userid = u.id
                              AND u.deleted = '0' AND u.confirmed = '1' $exceptions $sort");
}

/**
* Returns all the users of a course: students and teachers
*
* @param    type description
*/
function get_course_users($courseid, $sort="timeaccess DESC", $exceptions='', $fields='') {

    /// Using this method because the single SQL is too inefficient
    // Note that this has the effect that teachers and students are
    // sorted individually. Returns first all teachers, then all students

    if (!$teachers = get_course_teachers($courseid, $sort, $exceptions)) {
        $teachers = array();
    }
    if (!$students = get_course_students($courseid, $sort, '', 0, 99999, '', '', NULL, '', $fields, $exceptions)) {
        $students = array();
    }

    return $teachers + $students;

}


/**
* Search through course users
* If used for the site course searches through all undeleted, confirmed users
*
* @param    type description
*/
function search_users($courseid, $groupid, $searchtext, $sort='', $exceptions='') {
    global $CFG;

    switch ($CFG->dbtype) {
        case "mysql":
             $fullname = " CONCAT(u.firstname,\" \",u.lastname) ";
             $LIKE = "LIKE";
             break;
        case "postgres7":
             $fullname = " u.firstname||' '||u.lastname ";
             $LIKE = "ILIKE";
             break;
        default:
             $fullname = " u.firstname||\" \"||u.lastname ";
             $LIKE = "ILIKE";
    }

    if (!empty($exceptions)) {
        $except = " AND u.id NOT IN ($exceptions) ";
    } else {
        $except = '';
    }

    if (!empty($sort)) {
        $order = " ORDER by $sort";
    } else {
        $order = '';
    }

    $select = "u.deleted = '0' AND u.confirmed = '1'";

    if (!$courseid or $courseid == SITEID) {
        return get_records_sql("SELECT u.id, u.firstname, u.lastname, u.email
                      FROM {$CFG->prefix}user u
                      WHERE $select
                          AND ($fullname $LIKE '%$searchtext%' OR u.email $LIKE '%$searchtext%')
                          $except $order");
    } else {

        if ($groupid) {
            return get_records_sql("SELECT u.id, u.firstname, u.lastname, u.email
                          FROM {$CFG->prefix}user u,
                               {$CFG->prefix}groups_members g
                          WHERE $select AND g.groupid = '$groupid' AND g.userid = u.id
                              AND ($fullname $LIKE '%$searchtext%' OR u.email $LIKE '%$searchtext%')
                              $except $order");
        } else {
            if (!$teachers = get_records_sql("SELECT u.id, u.firstname, u.lastname, u.email
                          FROM {$CFG->prefix}user u,
                               {$CFG->prefix}user_teachers s
                          WHERE $select AND s.course = '$courseid' AND s.userid = u.id
                              AND ($fullname $LIKE '%$searchtext%' OR u.email $LIKE '%$searchtext%')
                              $except $order")) {
                $teachers = array();
            }
            if (!$students = get_records_sql("SELECT u.id, u.firstname, u.lastname, u.email
                          FROM {$CFG->prefix}user u,
                               {$CFG->prefix}user_students s
                          WHERE $select AND s.course = '$courseid' AND s.userid = u.id
                              AND ($fullname $LIKE '%$searchtext%' OR u.email $LIKE '%$searchtext%')
                              $except $order")) {
                $students = array();
            }
            return $teachers + $students;
        }
    }
}


/**
* Returns a list of all site users
* Obsolete, just calls get_course_users(SITEID)
*
* @param    type description
*/
function get_site_users($sort="u.lastaccess DESC", $fields='*', $exceptions='') {

    return get_course_users(SITEID, $sort, $exceptions, $fields);
}


/**
* Returns a subset of users
*
* longdesc
*
* @param    bookean $get    if false then only a count of the records is returned
* @param    string  $search a simple string to search for
* @param    boolean $confirmed  a switch to allow/disallow unconfirmed users
* @param    array(int)  $exceptions a list of IDs to ignore, eg 2,4,5,8,9,10
* @param    string  $sort   a SQL snippet for the sorting criteria to use
* @param    string  $firstinitial
* @param    string  $lastinitial
* @param    string  $page
* @param    string  $recordsperpage
* @param    string  $fields a comma separated list of fields
*/
function get_users($get=true, $search="", $confirmed=false, $exceptions="", $sort="firstname ASC",
                   $firstinitial="", $lastinitial="", $page=0, $recordsperpage=99999, $fields="*") {

    global $CFG;

    switch ($CFG->dbtype) {
        case "mysql":
             $limit = "LIMIT $page,$recordsperpage";
             $fullname = " CONCAT(firstname,\" \",lastname) ";
             $LIKE = "LIKE";
             break;
        case "postgres7":
             $limit = "LIMIT $recordsperpage OFFSET ".($page);
             $fullname = " firstname||' '||lastname ";
             $LIKE = "ILIKE";
             break;
        default:
             $limit = "LIMIT $recordsperpage,$page";
             $fullname = " firstname||\" \"||lastname ";
             $LIKE = "ILIKE";
    }

    $select = "username <> 'guest' AND deleted = 0";

    if ($search) {
        $select .= " AND ($fullname $LIKE '%$search%' OR email $LIKE '%$search%') ";
    }

    if ($confirmed) {
        $select .= " AND confirmed = '1' ";
    }

    if ($exceptions) {
        $select .= " AND id NOT IN ($exceptions) ";
    }

    if ($firstinitial) {
        $select .= " AND firstname $LIKE '$firstinitial%'";
    }
    if ($lastinitial) {
        $select .= " AND lastname $LIKE '$lastinitial%'";
    }

    if ($sort and $get) {
        $sort = " ORDER BY $sort ";
    } else {
        $sort = "";
    }

    if ($get) {
        return get_records_select("user", "$select $sort $limit", '', $fields);
    } else {
        return count_records_select("user", "$select $sort $limit");
    }
}


/**
* shortdesc
*
* longdesc
*
* @param    type description
*/
function get_users_listing($sort="lastaccess", $dir="ASC", $page=0, $recordsperpage=99999,
                           $search="", $firstinitial="", $lastinitial="") {

    global $CFG;

    switch ($CFG->dbtype) {
        case "mysql":
             $limit = "LIMIT $page,$recordsperpage";
             $fullname = " CONCAT(firstname,\" \",lastname) ";
             $LIKE = "LIKE";
             break;
        case "postgres7":
             $limit = "LIMIT $recordsperpage OFFSET ".($page);
             $fullname = " firstname||' '||lastname ";
             $LIKE = "ILIKE";
             break;
        default:
             $limit = "LIMIT $recordsperpage,$page";
             $fullname = " firstname||' '||lastname ";
             $LIKE = "LIKE";
    }

    $select = 'deleted <> 1';

    if ($search) {
        $select .= " AND ($fullname $LIKE '%$search%' OR email $LIKE '%$search%') ";
    }

    if ($firstinitial) {
        $select .= " AND firstname $LIKE '$firstinitial%' ";
    }

    if ($lastinitial) {
        $select .= " AND lastname $LIKE '$lastinitial%' ";
    }

    if ($sort) {
        $sort = " ORDER BY $sort $dir";
    }

/// warning: will return UNCONFIRMED USERS
    return get_records_sql("SELECT id, username, email, firstname, lastname, city, country, lastaccess, confirmed
                              FROM {$CFG->prefix}user
                             WHERE $select $sort $limit ");

}


/**
* shortdesc
*
* longdesc
*
* @param    type description
*/
function get_users_confirmed() {
    global $CFG;
    return get_records_sql("SELECT *
                              FROM {$CFG->prefix}user
                             WHERE confirmed = 1
                               AND deleted = 0
                               AND username <> 'guest'
                               AND username <> 'changeme'");
}


/**
* shortdesc
*
* longdesc
*
* @param    type description
*/
function get_users_unconfirmed($cutofftime=2000000000) {
    global $CFG;
    return get_records_sql("SELECT *
                             FROM {$CFG->prefix}user
                            WHERE confirmed = 0
                              AND firstaccess > 0
                              AND firstaccess < '$cutofftime'");
}


/**
* shortdesc
*
* longdesc
*
* @param    type description
*/
function get_users_longtimenosee($cutofftime) {
    global $CFG;
    return get_records_sql("SELECT DISTINCT *
                              FROM {$CFG->prefix}user_students
                             WHERE timeaccess > '0'
                               AND timeaccess < '$cutofftime' ");
}

/**
* Returns an array of group objects that the user is a member of
* in the given course.  If userid isn't specified, then return a
* list of all groups in the course.
*
* @param    type description
*/
function get_groups($courseid, $userid=0) {
    global $CFG;

    if ($userid) {
        $dbselect = ", {$CFG->prefix}groups_members m";
        $userselect = "AND m.groupid = g.id AND m.userid = '$userid'";
    } else {
        $dbselect = '';
        $userselect = '';
    }

    return get_records_sql("SELECT DISTINCT g.*
                              FROM {$CFG->prefix}groups g $dbselect
                             WHERE g.courseid = '$courseid' $userselect ");
}


/**
* Returns an array of user objects
*
* @param    type description
*/
function get_group_users($groupid, $sort="u.lastaccess DESC", $exceptions='') {
    global $CFG;
    if (!empty($exceptions)) {
        $except = " AND u.id NOT IN ($exceptions) ";
    } else {
        $except = '';
    }
    return get_records_sql("SELECT DISTINCT u.*
                              FROM {$CFG->prefix}user u,
                                   {$CFG->prefix}groups_members m
                             WHERE m.groupid = '$groupid'
                               AND m.userid = u.id $except
                          ORDER BY $sort");
}

/**
* An efficient way of finding all the users who aren't in groups yet
*
* @param    type description
*/
function get_users_not_in_group($courseid) {
    global $CFG;

    return array();     /// XXX TO BE DONE
}


/**
* Returns an array of user objects
*
* @param    type description
*/
function get_group_students($groupid, $sort="u.lastaccess DESC") {
    global $CFG;
    return get_records_sql("SELECT DISTINCT u.*
                              FROM {$CFG->prefix}user u,
                                   {$CFG->prefix}groups_members m,
                                   {$CFG->prefix}groups g,
                                   {$CFG->prefix}user_students s
                             WHERE m.groupid = '$groupid'
                               AND m.userid = u.id
                               AND m.groupid = g.id
                               AND g.courseid = s.course
                               AND s.userid = u.id
                          ORDER BY $sort");
}


/**
* Returns the user's group in a particular course
*
* @param    type description
*/
function user_group($courseid, $userid) {
    global $CFG;

    return get_record_sql("SELECT g.*
                             FROM {$CFG->prefix}groups g,
                                  {$CFG->prefix}groups_members m
                             WHERE g.courseid = '$courseid'
                               AND g.id = m.groupid
                               AND m.userid = '$userid'");
}




/// OTHER SITE AND COURSE FUNCTIONS /////////////////////////////////////////////


/**
* Returns $course object of the top-level site.
*
* Returns $course object of the top-level site.
*
* @param    type description
*/
function get_site () {

    if ( $course = get_record("course", "category", 0)) {
        return $course;
    } else {
        return false;
    }
}


/**
* Returns list of courses, for whole site, or category
*
* Returns list of courses, for whole site, or category
*
* @param    type description
*
* Important: Using c.* for fields is extremely expensive because 
*            we are using distinct. You almost _NEVER_ need all the fields
*            in such a large SELECT
*/
function get_courses($categoryid="all", $sort="c.sortorder ASC", $fields="c.*") {

    global $USER, $CFG;
    
    $categoryselect = "";
    if ($categoryid != "all" && is_numeric($categoryid)) {
        $categoryselect = "c.category = '$categoryid'";
    }

    $teachertable = "";
    $visiblecourses = "";
    $sqland = "";
    if (!empty($categoryselect)) {
        $sqland = "AND ";
    }
    if (!empty($USER->id)) {  // May need to check they are a teacher
        if (!iscreator()) {
            $visiblecourses = "$sqland ((c.visible > 0) OR t.userid = '$USER->id')";
            $teachertable = "LEFT JOIN {$CFG->prefix}user_teachers t ON t.course = c.id";
        }
    } else {
        $visiblecourses = "$sqland c.visible > 0";
    }

    if ($categoryselect or $visiblecourses) {
        $selectsql = "{$CFG->prefix}course c $teachertable WHERE $categoryselect $visiblecourses";
    } else {
        $selectsql = "{$CFG->prefix}course c $teachertable";
    }

    return get_records_sql("SELECT ".((!empty($teachertable)) ? " DISTINCT " : "")." $fields FROM $selectsql ".((!empty($sort)) ? "ORDER BY $sort" : ""));
}




/**
* Returns list of courses, for whole site, or category
*
* Similar to get_courses, but allows paging
*
* @param    type description
*
* Important: Using c.* for fields is extremely expensive because 
*            we are using distinct. You almost _NEVER_ need all the fields
*            in such a large SELECT
*/
function get_courses_page($categoryid="all", $sort="c.sortorder ASC", $fields="c.*",
                          &$totalcount, $limitfrom="", $limitnum="") {

    global $USER, $CFG;

    $categoryselect = "";
    if ($categoryid != "all" && is_numeric($categoryid)) {
        $categoryselect = "c.category = '$categoryid'";
    }

    $teachertable = "";
    $visiblecourses = "";
    $sqland = "";
    if (!empty($categoryselect)) {
        $sqland = "AND ";
    }
    if (!empty($USER)) {  // May need to check they are a teacher
        if (!iscreator()) {
            $visiblecourses = "$sqland ((c.visible > 0) OR t.userid = '$USER->id')";
            $teachertable = "LEFT JOIN {$CFG->prefix}user_teachers t ON t.course=c.id";
        }
    } else {
        $visiblecourses = "$sqland c.visible > 0";
    }

    if ($limitfrom !== "") {
        switch ($CFG->dbtype) {
            case "mysql":
                 $limit = "LIMIT $limitfrom,$limitnum";
                 break;
            case "postgres7":
                 $limit = "LIMIT $limitnum OFFSET $limitfrom";
                 break;
            default:
                 $limit = "LIMIT $limitnum,$limitfrom";
        }
    } else {
        $limit = "";
    }

    $selectsql = "{$CFG->prefix}course c $teachertable WHERE $categoryselect $visiblecourses";

    $totalcount = count_records_sql("SELECT COUNT(DISTINCT c.id) FROM $selectsql");

    return get_records_sql("SELECT DISTINCT $fields FROM $selectsql ".((!empty($sort)) ? "ORDER BY $sort" : "")." $limit");
}



/**
* shortdesc
*
* longdesc
*
* @param    type description
*/
function get_my_courses($userid, $sort="visible DESC,sortorder ASC") {

    global $CFG;

    $course = array();

    if ($students = get_records("user_students", "userid", $userid, "", "id, course")) {
        foreach ($students as $student) {
            $course[$student->course] = $student->course;
        }
    }
    if ($teachers = get_records("user_teachers", "userid", $userid, "", "id, course")) {
        foreach ($teachers as $teacher) {
            $course[$teacher->course] = $teacher->course;
        }
    }
    if (empty($course)) {
        return $course;
    }

    $courseids = implode(',', $course);

    return get_records_list("course", "id", $courseids, $sort);

//  The following is correct but VERY slow with large datasets
//
//    return get_records_sql("SELECT c.*
//                              FROM {$CFG->prefix}course c,
//                                   {$CFG->prefix}user_students s,
//                                   {$CFG->prefix}user_teachers t
//                             WHERE (s.userid = '$userid' AND s.course = c.id)
//                                OR (t.userid = '$userid' AND t.course = c.id)
//                             GROUP BY c.id
//                             ORDER BY $sort");
}


/**
* Returns a list of courses that match a search
*
* Returns a list of courses that match a search
*
* @param    type description
*/
function get_courses_search($searchterms, $sort="fullname ASC", $page=0, $recordsperpage=50, &$totalcount) {

    global $CFG;

    switch ($CFG->dbtype) {
        case "mysql":
             $limit = "LIMIT $page,$recordsperpage";
             break;
        case "postgres7":
             $limit = "LIMIT $recordsperpage OFFSET ".$page;
             break;
        default:
             $limit = "LIMIT $recordsperpage,$page";
    }

    //to allow case-insensitive search for postgesql
    if ($CFG->dbtype == "postgres7") {
        $LIKE = "ILIKE";
        $NOTLIKE = "NOT ILIKE";   // case-insensitive
        $REGEXP = "~*";
        $NOTREGEXP = "!~*";
    } else {
        $LIKE = "LIKE";
        $NOTLIKE = "NOT LIKE";
        $REGEXP = "REGEXP";
        $NOTREGEXP = "NOT REGEXP";
    }

    $fullnamesearch = "";
    $summarysearch = "";

    foreach ($searchterms as $searchterm) {
        if ($fullnamesearch) {
            $fullnamesearch .= " AND ";
        }
        if ($summarysearch) {
            $summarysearch .= " AND ";
        }

        if (substr($searchterm,0,1) == "+") {
            $searchterm = substr($searchterm,1);
            $summarysearch .= " summary $REGEXP '(^|[^a-zA-Z0-9])$searchterm([^a-zA-Z0-9]|$)' ";
            $fullnamesearch .= " fullname $REGEXP '(^|[^a-zA-Z0-9])$searchterm([^a-zA-Z0-9]|$)' ";
        } else if (substr($searchterm,0,1) == "-") {
            $searchterm = substr($searchterm,1);
            $summarysearch .= " summary $NOTREGEXP '(^|[^a-zA-Z0-9])$searchterm([^a-zA-Z0-9]|$)' ";
            $fullnamesearch .= " fullname $NOTREGEXP '(^|[^a-zA-Z0-9])$searchterm([^a-zA-Z0-9]|$)' ";
        } else {
            $summarysearch .= " summary $LIKE '%$searchterm%' ";
            $fullnamesearch .= " fullname $LIKE '%$searchterm%' ";
        }

    }

    $selectsql = "{$CFG->prefix}course WHERE ($fullnamesearch OR $summarysearch) AND category > '0'";

    $totalcount = count_records_sql("SELECT COUNT(*) FROM $selectsql");

    $courses = get_records_sql("SELECT * FROM $selectsql ".((!empty($sort)) ? "ORDER BY $sort" : "")." $limit");

    if ($courses) {  /// Remove unavailable courses from the list
        foreach ($courses as $key => $course) {
            if (!$course->visible) {
                if (!isteacher($course->id)) {
                    unset($courses[$key]);
                    $totalcount--;
                }
            }
        }
    }

    return $courses;
}


/**
* Returns a sorted list of categories
*
* Returns a sorted list of categories
*
* @param    type description
*/
function get_categories($parent="none", $sort="sortorder ASC") {

    if ($parent == "none") {
        $categories = get_records("course_categories", "", "", $sort);
    } else {
        $categories = get_records("course_categories", "parent", $parent, $sort);
    }
    if ($categories) {  /// Remove unavailable categories from the list
        $creator = iscreator();
        foreach ($categories as $key => $category) {
            if (!$category->visible) {
                if (!$creator) {
                    unset($categories[$key]);
                }
            }
        }
    }
    return $categories;
}


/**
* This recursive function makes sure that the courseorder is consecutive
*
* @param    type description
*
* $n is the starting point, offered only for compatilibity -- will be ignored!
* $safe (bool) prevents it from assuming category-sortorder is unique, used to upgrade
*       safely from 1.4 to 1.5
*/
function fix_course_sortorder($categoryid=0, $n=0, $safe=0) {

    global $CFG;

    $count = 0;
    
    $n=100;

    // get some basic info
    $info = get_record_sql('SELECT MIN(sortorder) AS min, 
                                   MAX(sortorder) AS max,
                                   COUNT(sortorder)  AS count
                            FROM ' . $CFG->prefix . 'course 
                            WHERE category=' . $categoryid);
    if (is_object($info)) { // no courses?
        $max   = $info->max;
        $count = $info->count;
        $min   = $info->min;
        unset($info);
    }

    // actually sort only if there are courses,
    // and we meet one ofthe triggers:
    //  - safe flag
    //  - they are not in a continuos block
    //  - they are too close to the 'bottom'
    if ($count && (    $safe 
                    || ($max-$min+1!=$count)
                    || $min < 10 ) ) {
        if ($courses = get_courses($categoryid, 'c.sortorder ASC', 'c.id,c.sortorder')) {
            begin_sql();

            // find the ideal starting point
            if ( ($min<$n&&$n<$max) || ($n+$count>=$min) || ($min<10) ) { 

                $n = $max+100; // this is usually the ideal solution
                
                // if we are aiming way too high, try to bring it back to earth
                if ($n > 100+3*$count) {
                    if ($min > 100+$count){
                        $n = 100;
                    }
                }
            }

            foreach ($courses as $course) { 
                if ($course->sortorder != $n ) { // save db traffic
                    set_field('course', 'sortorder', $n, 'id', $course->id);
                }
                $n++;
            }
            commit_sql();
        }    
    }
    set_field("course_categories", "coursecount", $count, "id", $categoryid);

    $n=0;
    if ($categories = get_categories($categoryid)) {
        foreach ($categories as $category) {
            $n = fix_course_sortorder($category->id, $n);
        }
    }

    return $n;
}

/**
* This function creates a default separated/connected scale
*
* This function creates a default separated/connected scale
* so there's something in the database.  The locations of
* strings and files is a bit odd, but this is because we
* need to maintain backward compatibility with many different
* existing language translations and older sites.
*
* @param    type description
*/
function make_default_scale() {

    global $CFG;

    $defaultscale = NULL;
    $defaultscale->courseid = 0;
    $defaultscale->userid = 0;
    $defaultscale->name  = get_string("separateandconnected");
    $defaultscale->scale = get_string("postrating1", "forum").",".
                           get_string("postrating2", "forum").",".
                           get_string("postrating3", "forum");
    $defaultscale->timemodified = time();

    /// Read in the big description from the file.  Note this is not
    /// HTML (despite the file extension) but Moodle format text.
    $parentlang = get_string("parentlang");
    if (is_readable("$CFG->dirroot/lang/$CFG->lang/help/forum/ratings.html")) {
        $file = file("$CFG->dirroot/lang/$CFG->lang/help/forum/ratings.html");
    } else if ($parentlang and is_readable("$CFG->dirroot/lang/$parentlang/help/forum/ratings.html")) {
        $file = file("$CFG->dirroot/lang/$parentlang/help/forum/ratings.html");
    } else if (is_readable("$CFG->dirroot/lang/en/help/forum/ratings.html")) {
        $file = file("$CFG->dirroot/lang/en/help/forum/ratings.html");
    } else {
        $file = "";
    }

    $defaultscale->description = addslashes(implode("", $file));

    if ($defaultscale->id = insert_record("scale", $defaultscale)) {
        execute_sql("UPDATE {$CFG->prefix}forum SET scale = '$defaultscale->id'", false);
    }
}

/**
* Returns a menu of all available scales from the site as well as the given course
*
* Returns a menu of all available scales from the site as well as the given course
*
* @param    type description
*/
function get_scales_menu($courseid=0) {

    global $CFG;

    $sql = "SELECT id, name FROM {$CFG->prefix}scale
             WHERE courseid = '0' or courseid = '$courseid'
          ORDER BY courseid ASC, name ASC";

    if ($scales = get_records_sql_menu("$sql")) {
        return $scales;
    }

    make_default_scale();

    return get_records_sql_menu("$sql");
}

/// MODULE FUNCTIONS /////////////////////////////////////////////////

/**
* Just gets a raw list of all modules in a course
*
* Just gets a raw list of all modules in a course
*
* @param    type description
*/
function get_course_mods($courseid) {
    global $CFG;

    return get_records_sql("SELECT cm.*, m.name as modname
                            FROM {$CFG->prefix}modules m,
                                 {$CFG->prefix}course_modules cm
                            WHERE cm.course = '$courseid'
                            AND cm.deleted = '0'
                            AND cm.module = m.id ");
}

/**
* Given an instance of a module, finds the coursemodule description
*
* Given an instance of a module, finds the coursemodule description
*
* @param    type description
*/
function get_coursemodule_from_instance($modulename, $instance, $courseid) {

    global $CFG;

    return get_record_sql("SELECT cm.*, m.name
                           FROM {$CFG->prefix}course_modules cm,
                                {$CFG->prefix}modules md,
                                {$CFG->prefix}$modulename m
                           WHERE cm.course = '$courseid' AND
                                 cm.deleted = '0' AND
                                 cm.instance = m.id AND
                                 md.name = '$modulename' AND
                                 md.id = cm.module AND
                                 m.id = '$instance'");

}

/**
* Returns an array of all the active instances of a particular module in a given course, sorted in the order they are defined
*
* Returns an array of all the active instances of a particular
* module in a given course, sorted in the order they are defined
* in the course.   Returns false on any errors.
*
* @param    string  $modulename the name of the module to get instances for
* @param        object(course)  $course this depends on an accurate $course->modinfo
*/
function get_all_instances_in_course($modulename, $course) {

    global $CFG;

    if (!$modinfo = unserialize($course->modinfo)) {
        return array();
    }

    if (!$rawmods = get_records_sql("SELECT cm.id as coursemodule, m.*,cw.section,cm.visible as visible,cm.groupmode
                            FROM {$CFG->prefix}course_modules cm,
                                 {$CFG->prefix}course_sections cw,
                                 {$CFG->prefix}modules md,
                                 {$CFG->prefix}$modulename m
                            WHERE cm.course = '$course->id' AND
                                  cm.instance = m.id AND
                                  cm.deleted = '0' AND
                                  cm.section = cw.id AND
                                  md.name = '$modulename' AND
                                  md.id = cm.module")) {
        return array();
    }

    // Hide non-visible instances from students
    if (isteacher($course->id)) {
        $invisible = -1;
    } else {
        $invisible = 0;
    }

    foreach ($modinfo as $mod) {
        if ($mod->mod == $modulename and $mod->visible > $invisible) {
            $instance = $rawmods[$mod->cm];
            if (!empty($mod->extra)) {
                $instance->extra = $mod->extra;
            }
            $outputarray[] = $instance;
        }
    }

    return $outputarray;

}


/**
* determine whether a module instance is visible within a course
*
* Given a valid module object with info about the id and course,
* and the module's type (eg "forum") returns whether the object
* is visible or not
*
* @param    type description
*/
function instance_is_visible($moduletype, $module) {

    global $CFG;

    if (!empty($module->id)) {
        if ($records = get_records_sql("SELECT cm.instance, cm.visible
                                        FROM {$CFG->prefix}course_modules cm,
                                             {$CFG->prefix}modules m
                                       WHERE cm.course = '$module->course' AND
                                             cm.module = m.id AND
                                             m.name = '$moduletype' AND
                                             cm.instance = '$module->id'")) {
    
            foreach ($records as $record) { // there should only be one - use the first one
                return $record->visible;
            }
        }
    }
    return true;  // visible by default!
}




/// LOG FUNCTIONS /////////////////////////////////////////////////////


/**
* Add an entry to the log table.
*
* Add an entry to the log table.  These are "action" focussed rather
* than web server hits, and provide a way to easily reconstruct what
* any particular student has been doing.
*
* @param    int     $course  the course id
* @param    string  $module  the module name - e.g. forum, journal, resource, course, user etc
* @param    string  $action  view, edit, post (often but not always the same as the file.php)
* @param    string  $url     the file and parameters used to see the results of the action
* @param    string  $info    additional description information
* @param    string  $cm      the course_module->id if there is one
* @param    string  $user    if log regards $user other than $USER
*/
function add_to_log($courseid, $module, $action, $url="", $info="", $cm=0, $user=0) {

    global $db, $CFG, $USER, $REMOTE_ADDR;

    if ($user) {
        $userid = $user;
    } else {
        if (isset($USER->realuser)) {  // Don't log
            return;
        }
        $userid = empty($USER->id) ? "0" : $USER->id;
    }

    $timenow = time();
    $info = addslashes($info);

    $result = $db->Execute("INSERT INTO {$CFG->prefix}log (time, userid, course, ip, module, cmid, action, url, info)
        VALUES ('$timenow', '$userid', '$courseid', '$REMOTE_ADDR', '$module', '$cm', '$action', '$url', '$info')");

    if (!$result and ($CFG->debug > 7)) {
        echo "<P>Error: Could not insert a new entry to the Moodle log</P>";  // Don't throw an error
    }
    if (!$user and isset($USER->id)) {
        if ($courseid == 1) {
            update_user_in_db();
        } else if (isstudent($courseid)) {
            $db->Execute("UPDATE {$CFG->prefix}user_students SET timeaccess = '$timenow' ".
                         "WHERE course = '$courseid' AND userid = '$userid'");
        } else if (isteacher($courseid, false, false)) {
            $db->Execute("UPDATE {$CFG->prefix}user_teachers SET timeaccess = '$timenow' ".
                         "WHERE course = '$courseid' AND userid = '$userid'");
        }
    }
}


/**
* select all log records based on SQL criteria
*
* select all log records based on SQL criteria
*
* @param    string  $select SQL select criteria
* @param    string  $order  SQL order by clause to sort the records returned
*/
function get_logs($select, $order="l.time DESC", $limitfrom="", $limitnum="", &$totalcount) {
    global $CFG;

    if ($limitfrom !== "") {
        switch ($CFG->dbtype) {
            case "mysql":
                 $limit = "LIMIT $limitfrom,$limitnum";
                 break;
            case "postgres7":
                 $limit = "LIMIT $limitnum OFFSET $limitfrom";
                 break;
            default:
                 $limit = "LIMIT $limitnum,$limitfrom";
        }
    } else {
        $limit = "";
    }

    if ($order) {
        $order = "ORDER BY $order";
    }

    $selectsql = "{$CFG->prefix}log l LEFT JOIN {$CFG->prefix}user u ON l.userid = u.id ".((strlen($select) > 0) ? "WHERE $select" : "");
    $totalcount = count_records_sql("SELECT COUNT(*) FROM $selectsql");

    return get_records_sql("SELECT l.*, u.firstname, u.lastname, u.picture
                                FROM $selectsql $order $limit");
}


/**
* select all log records for a given course and user
*
* select all log records for a given course and user
*
* @param    type description
*/
function get_logs_usercourse($userid, $courseid, $coursestart) {
    global $CFG;

    if ($courseid) {
        $courseselect = " AND course = '$courseid' ";
    } else {
        $courseselect = '';
    }

    return get_records_sql("SELECT floor((time - $coursestart)/86400) as day, count(*) as num
                            FROM {$CFG->prefix}log
                           WHERE userid = '$userid'
                             AND time > '$coursestart' $courseselect
                        GROUP BY day ");
}

/**
* select all log records for a given course, user, and day
*
* select all log records for a given course, user, and day
*
* @param    type description
*/
function get_logs_userday($userid, $courseid, $daystart) {
    global $CFG;

    if ($courseid) {
        $courseselect = " AND course = '$courseid' ";
    } else {
        $courseselect = '';
    }

    return get_records_sql("SELECT floor((time - $daystart)/3600) as hour, count(*) as num
                            FROM {$CFG->prefix}log
                           WHERE userid = '$userid'
                             AND time > '$daystart' $courseselect
                        GROUP BY hour ");
}

/**
 * Returns an object with counts of failed login attempts
 *
 * Returns information about failed login attempts.  If the current user is
 * an admin, then two numbers are returned:  the number of attempts and the
 * number of accounts.  For non-admins, only the attempts on the given user
 * are shown.
 *
 * @param mode      - admin, teacher or everybody
 * @param username  - the username we are searching for
 * @param lastlogin - the date from which we are searching
 */

function count_login_failures($mode, $username, $lastlogin) {

    $select = "module='login' AND action='error' AND time > $lastlogin";

    if (isadmin()) {    // Return information about all accounts
        if ($count->attempts = count_records_select('log', $select)) {
            $count->accounts = count_records_select('log', $select, 'COUNT(DISTINCT info)');
            return $count;
        }
    } else if ($mode == 'everybody' or ($mode == 'teacher' and isteacher())) {
        if ($count->attempts = count_records_select('log', "$select AND info = '$username'")) {
            return $count;
        }
    }
    return NULL;
}


/// GENERAL HELPFUL THINGS  ///////////////////////////////////

/**
* dump a given object's information in a PRE block
*
* dump a given object's information in a PRE block
* Mostly just for debugging
*
* @param    type description
*/
function print_object($object) {

    echo "<PRE>";
    print_r($object);
    echo "</PRE>";
}



// vim:autoindent:expandtab:shiftwidth=4:tabstop=4:tw=140:
?>
