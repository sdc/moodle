<?PHP  //$Id$

// PostgreSQL commands for upgrading this enrolment module

function authorize_upgrade($oldversion=0) {
    global $CFG, $THEME, $db;

    $result = true;

    if ($oldversion == 0) {
        modify_database("$CFG->dirroot/enrol/authorize/db/postgres7.sql");
    }

    if ($oldversion < 2005080200) {
        // Be sure, only last 4 digit is inserted.
        table_column('enrol_authorize', 'cclastfour', 'cclastfour', 'integer', '4', 'unsigned', '0', 'not null');
        table_column('enrol_authorize', 'courseid', 'courseid', 'integer', '10', 'unsigned', '0', 'not null');
        table_column('enrol_authorize', 'userid', 'userid', 'integer', '10', 'unsigned', '0', 'not null');
        // Add some indexes for speed.
        modify_database('',"CREATE INDEX prefix_enrol_authorize_courseid_idx ON prefix_enrol_authorize (courseid);");
        modify_database('',"CREATE INDEX prefix_enrol_authorize_userid_idx ON prefix_enrol_authorize (userid);");
    }
    
    if ($oldversion < 2005112100) {
        include_once("$CFG->dirroot/enrol/authorize/enrol.php");

        table_column('enrol_authorize', '', 'authcode', 'varchar', '6', '', '', '', 'avscode'); // CAPTURE_ONLY
        table_column('enrol_authorize', '', 'status', 'integer', '10', 'unsigned', '0', 'not null', 'transid');
        table_column('enrol_authorize', '', 'timecreated', 'integer', '10', 'unsigned', '0', 'not null', 'status');
        table_column('enrol_authorize', '', 'timeupdated', 'integer', '10', 'unsigned', '0', 'not null', 'timecreated');
        // status index for speed.
        modify_database('',"CREATE INDEX prefix_enrol_authorize_status_idx ON prefix_enrol_authorize (status);");
        // defaults.
        $timenow = time();
        $status = AN_STATUS_AUTH | AN_STATUS_CAPTURE;
        execute_sql(" UPDATE {$CFG->prefix}enrol_authorize SET timecreated='$timenow', timeupdated='$timenow', status='$status' ", false);
    }
    
    return $result;
}

?>
