<?PHP // $Id$
//
// setup.php
// 
// Sets up sessions, connects to databases and so on
//
// Normally this is only called by the main config.php file 
// 
// Normally this file does not need to be edited.
//
//////////////////////////////////////////////////////////////

    if (!isset($CFG->wwwroot)) {
        die;
    }
    
/// If there are any errors in the standard libraries we want to know!
    error_reporting(E_ALL);

/// Just say no to link prefetching (Moz prefetching, Google Web Accelerator, others)
/// http://www.google.com/webmasters/faq.html#prefetchblock
    if (!empty($_SERVER['HTTP_X_moz']) && $_SERVER['HTTP_X_moz'] === 'prefetch'){
        header($_SERVER['SERVER_PROTOCOL'] . ' 404 Prefetch Forbidden'); 
        trigger_error('Prefetch request forbidden.');
        exit;
    }

/// Connect to the database using adodb

    $CFG->libdir   = "$CFG->dirroot/lib";

    require_once("$CFG->libdir/adodb/adodb.inc.php"); // Database access functions

    $db = &ADONewConnection($CFG->dbtype);         

    error_reporting(0);  // Hide errors 

    if (!isset($CFG->dbpersist) or !empty($CFG->dbpersist)) {    // Use persistent connection (default)
        $dbconnected = $db->PConnect($CFG->dbhost,$CFG->dbuser,$CFG->dbpass,$CFG->dbname);
    } else {                                                     // Use single connection
        $dbconnected = $db->Connect($CFG->dbhost,$CFG->dbuser,$CFG->dbpass,$CFG->dbname);
    }
    if (! $dbconnected) {
        // In the name of protocol correctness, monitoring and performance
        // profiling, set the appropriate error headers for machine comsumption
        if (isset($_SERVER['SERVER_PROTOCOL'])) { 
            // Avoid it with cron.php. Note that we assume it's HTTP/1.x
            header($_SERVER['SERVER_PROTOCOL'] . ' 503 Service Unavailable');        
        }
        // and then for human consumption...
        echo '<html><body>';
        echo '<table align="center"><tr>';
        echo '<td style="color:#990000; text-align:center; font-size:large; border-width:1px; '.
             '    border-color:#000000; border-style:solid; border-radius: 20px; border-collapse: collapse; '.
             '    -moz-border-radius: 20px; padding: 15px">';
        echo '<p>Error: Database connection failed.</p>';
        echo '<p>It is possible that the database is overloaded or otherwise not running properly.</p>';
        echo '<p>The site administrator should also check that the database details have been correctly specified in config.php</p>';
        echo '</td></tr></table>';
        echo '</body></html>';
        die;
    }

    error_reporting(E_ALL);       // Show errors from now on.

    if (!isset($CFG->prefix)) {   // Just in case it isn't defined in config.php
        $CFG->prefix = "";
    }


/// Define admin directory

    if (!isset($CFG->admin)) {   // Just in case it isn't defined in config.php
        $CFG->admin = 'admin';   // This is relative to the wwwroot and dirroot
    }


/// Load up standard libraries 

    require_once("$CFG->libdir/weblib.php");          // Functions for producing HTML
    require_once("$CFG->libdir/datalib.php");         // Functions for accessing databases
    require_once("$CFG->libdir/moodlelib.php");       // Other general-purpose functions


/// Increase memory limits if possible

    raise_memory_limit('64M');    // We should never NEED this much but just in case...        


/// Load up any configuration from the config table
    
    if ($configs = get_records('config')) {
        $CFG = (array)$CFG;
        foreach ($configs as $config) {
            if (!isset($CFG[$config->name])) {
                $CFG[$config->name] = $config->value;
            } else {
                error_log("\$CFG->$config->name in config.php overrides database setting");
            }
        }
        $CFG = (object)$CFG;
        unset($configs);
        unset($config);
    }


/// Set error reporting back to normal
    if (empty($CFG->debug)) {
        $CFG->debug = 7;
    }
    error_reporting($CFG->debug);


/// Set a default enrolment configuration (see bug 1598)
    if (!isset($CFG->enrol)) {
        $CFG->enrol = 'internal';
    }

/// File permissions on created directories in the $CFG->dataroot

    if (empty($CFG->directorypermissions)) {
        $CFG->directorypermissions = 0777;      // Must be octal (that's why it's here)
    }

/// Set up smarty template system
    require_once("$CFG->libdir/smarty/Smarty.class.php");  
    $smarty = new Smarty;
    $smarty->template_dir = "$CFG->dirroot/templates/$CFG->template";
    if (!file_exists("$CFG->dataroot/cache")) {
        make_upload_directory('cache');
    }
    $smarty->compile_dir = "$CFG->dataroot/cache";

    if(empty($CFG->respectsessionsettings)) {
     
        // Some distros disable GC by setting probability to 0
        // overriding the PHP default of 1 
        // (gc_probability is divided by gc_divisor, which defaults to 1000)
        if (ini_get('session.gc_probability') == 0) {
            ini_set('session.gc_probability', 1);
        }
        
        /// Set session timeouts
        if (!empty($CFG->sessiontimeout)) {
            ini_set('session.gc_maxlifetime', $CFG->sessiontimeout);
        }

        /// Set custom session path
        if (!file_exists("$CFG->dataroot/sessions")) {
            make_upload_directory('sessions');
        }
        ini_set('session.save_path', "$CFG->dataroot/sessions");

    } // end of PHP session settings override
    
/// Set sessioncookie variable if it isn't already
    if (!isset($CFG->sessioncookie)) {
        $CFG->sessioncookie = '';
    }

/// Location of standard files

    $CFG->wordlist    = "$CFG->libdir/wordlist.txt";
    $CFG->javascript  = "$CFG->libdir/javascript.php";
    $CFG->moddata     = 'moddata';


/// Load up theme variables (colours etc)

    if (!isset($CFG->theme)) {
        $CFG->theme = 'standard';
    }
    include("$CFG->dirroot/theme/$CFG->theme/config.php");

    $CFG->stylesheet  = "$CFG->wwwroot/theme/$CFG->theme/styles.php";
    $CFG->header      = "$CFG->dirroot/theme/$CFG->theme/header.html";
    $CFG->footer      = "$CFG->dirroot/theme/$CFG->theme/footer.html";

    if (empty($THEME->custompix)) {
        $CFG->pixpath = "$CFG->wwwroot/pix";
        $CFG->modpixpath = "$CFG->wwwroot/mod";
    } else {
        $CFG->pixpath = "$CFG->wwwroot/theme/$CFG->theme/pix";
        $CFG->modpixpath = "$CFG->wwwroot/theme/$CFG->theme/pix/mod";
    }


/// A hack to get around magic_quotes_gpc being turned off

    if (!ini_get_bool('magic_quotes_gpc') ) {
        foreach ($_GET as $key => $var) {
            if (!is_array($var)) {
                $_GET[$key] = addslashes($var);
            } else {
                foreach ($var as $arrkey => $arrvar) {
                    $var[$arrkey] = addslashes($arrvar);
                }
                $_GET[$key] = $var;
            }
        }
        foreach ($_POST as $key => $var) {
            if (!is_array($var)) {
                $_POST[$key] = addslashes($var);
            } else {
                foreach ($var as $arrkey => $arrvar) {
                    $var[$arrkey] = addslashes($arrvar);
                }
                $_POST[$key] = $var;
            }
        }
        foreach ($_COOKIE as $key => $var) {
            if (!is_array($var)) {
                $_COOKIE[$key] = addslashes($var);
            } else {
                foreach ($var as $arrkey => $arrvar) {
                    $var[$arrkey] = addslashes($arrvar);
                }
                $_COOKIE[$key] = $var;
            }
        }
    }


/// The following is a hack to get around the problem of PHP installations
/// that have "register_globals" turned off (default since PHP 4.1.0).
/// Eventually I'll go through and upgrade all the code to make this unnecessary

    if (isset($_GET)) {
        extract($_GET, EXTR_SKIP);    // Skip existing variables, ie CFG
    }
    if (isset($_POST)) {
        extract($_POST, EXTR_SKIP);   // Skip existing variables, ie CFG
    }
    if (isset($_SERVER)) { 
        extract($_SERVER);
    }

    
/// Load up global environment variables

    class object {};

    unset(${'MoodleSession'.$CFG->sessioncookie});
    unset($_GET['MoodleSession'.$CFG->sessioncookie]);
    unset($_POST['MoodleSession'.$CFG->sessioncookie]);

    if (!isset($nomoodlecookie)) {
        session_name('MoodleSession'.$CFG->sessioncookie);
        @session_start();
        if (! isset($_SESSION['SESSION'])) { 
            $_SESSION['SESSION'] = new object; 
        }
        if (! isset($_SESSION['USER']))    { 
            $_SESSION['USER']    = new object; 
        }
    
        $SESSION = &$_SESSION['SESSION'];   // Makes them easier to reference
        $USER    = &$_SESSION['USER'];
    }
    else {
        $SESSION = NULL;
        $USER    = NULL;
    }

    if (defined('FULLME')) {     // Usually in command-line scripts like admin/cron.php
        $FULLME = FULLME;
        $ME = FULLME;
    } else {
        $FULLME = qualified_me();
        $ME = strip_querystring($FULLME);
    }

/// In VERY rare cases old PHP server bugs (it has been found on PHP 4.1.2 running 
/// as a CGI under IIS on Windows) may require that you uncomment the following:
//  session_register("USER");
//  session_register("SESSION");


/// Set language/locale of printed times.  If user has chosen a language that 
/// that is different from the site language, then use the locale specified 
/// in the language file.  Otherwise, if the admin hasn't specified a locale
/// then use the one from the default language.  Otherwise (and this is the 
/// majority of cases), use the stored locale specified by admin.

    if (isset($_GET['lang'])) {
        if (!detect_munged_arguments($lang, 0) and file_exists("$CFG->dirroot/lang/$lang")) {
            $SESSION->lang = $lang;
            $SESSION->encoding = get_string('thischarset');
        }
    }
    if (empty($CFG->lang)) {
        $CFG->lang = "en";
    }

    moodle_setlocale();

    if (!empty($CFG->opentogoogle)) {
        if (empty($_SESSION['USER'])) {
            if (!empty($_SERVER['HTTP_USER_AGENT'])) {
                if (strpos($_SERVER['HTTP_USER_AGENT'], 'Googlebot') !== false ) {
                    $USER = guest_user();
                }
                if (strpos($_SERVER['HTTP_USER_AGENT'], 'google.com') !== false ) {
                    $USER = guest_user();
                }
            }
            if (empty($_SESSION['USER']) and !empty($_SERVER['HTTP_REFERER'])) {
                if (strpos($_SERVER['HTTP_REFERER'], 'google') !== false ) {
                    $USER = guest_user();
                }
            }
        }
    }

?>
