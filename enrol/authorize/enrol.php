<?php // $Id$

require_once($CFG->dirroot.'/enrol/enrol.class.php');
require_once($CFG->dirroot.'/enrol/authorize/const.php');
require_once($CFG->dirroot.'/enrol/authorize/localfuncs.php');

/**
 * Authorize.net Payment Gateway plugin
 */
class enrolment_plugin_authorize
{
    /**
     * Credit card and Echeck error messages.
     *
     * @var array
     * @access public
     */
    var $authorizeerrors = array();

    /**
     * Cron log.
     *
     * @var string
     * @access public
     */
    var $log;


    /**
     * Presents registration forms.
     *
     * @param object $course Course info
     * @access public
     */
    function print_entry($course) {
        global $CFG, $USER, $form;

        $zerocost = zero_cost($course);
        if ($zerocost) {
            $manual = enrolment_factory::factory('manual');
            if (!empty($this->errormsg)) {
                $manual->errormsg = $this->errormsg;
            }
            $manual->print_entry($course);
            return;
        }

        httpsrequired();

        if (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == 'off') {
            if (empty($CFG->loginhttps)) {
                error(get_string('httpsrequired', 'enrol_authorize'));
            } else {
                $wwwsroot = str_replace('http:','https:', $CFG->wwwroot);
                redirect("$wwwsroot/course/enrol.php?id=$course->id");
                exit;
            }
        }

        $strcourses = get_string('courses');
        $strloginto = get_string('loginto', '', $course->shortname);

        print_header($strloginto,
                     $course->fullname,
                     "<a href=\"$CFG->wwwroot/course/\">$strcourses</a> -> $strloginto");
        print_course($course, '80%');

        if ($course->password) {
            print_heading(get_string('choosemethod', 'enrol_authorize'), 'center');
        }

        print_simple_box_start('center');
        if (has_capability('moodle/legacy:guest', get_context_instance(CONTEXT_SYSTEM, SITEID), $USER->id, false)) {
            $curcost = get_course_cost($course);
            echo '<div align="center">';
            echo '<p>'.get_string('paymentrequired').'</p>';
            echo '<p><b>'.get_string('cost').": $curcost[currency] $curcost[cost]".'</b></p>';
            echo '<p><a href="'.$CFG->httpswwwroot.'/login/">'.get_string('loginsite').'</a></p>';
            echo '</div>';
        } else {
            include($CFG->dirroot.'/enrol/authorize/enrol.html');
        }
        print_simple_box_end();

        if ($course->password) {
            $password = '';
            $teacher = get_teacher($course->id);
            include($CFG->dirroot.'/enrol/manual/enrol.html');
        }

        print_footer();
    }


    /**
     * Validates registration forms and enrols student to course.
     *
     * @param object $form Form parameters
     * @param object $course Course info
     * @access public
     */
    function check_entry($form, $course)
    {
        global $CFG;

        if (zero_cost($course) || (!empty($course->password) && !empty($form->enrol) && $form->enrol == 'manual')) {
            $manual = enrolment_factory::factory('manual');
            $manual->check_entry($form, $course);
            if (!empty($manual->errormsg)) {
                $this->errormsg = $manual->errormsg;
            }
        }
        elseif (!empty($form->paymentmethod) && in_array($form->paymentmethod, get_list_of_payment_methods())) {
            if ($form->paymentmethod == AN_METHOD_CC && validate_cc_form($form, $this->authorizeerrors)) {
                $this->cc_submit($form, $course);
            }
            elseif($form->paymentmethod == AN_METHOD_ECHECK && validate_echeck_form($form, $this->authorizeerrors)) {
                $this->echeck_submit($form, $course);
            }
        }
    }


    /**
     * The user submitted credit card form.
     *
     * @param object $form Form parameters
     * @param object $course Course info
     * @access private
     */
    function cc_submit($form, $course)
    {
        global $CFG, $USER, $SESSION;
        require_once('authorizenetlib.php');

        prevent_double_paid($course);

        $useripno = getremoteaddr();
        $curcost = get_course_cost($course);
        $exp_date = sprintf("%02d", $form->ccexpiremm) . $form->ccexpireyyyy;

        // NEW CC ORDER
        $timenow = time();
        $order = new stdClass();
        $order->paymentmethod = AN_METHOD_CC;
        $order->cclastfour = substr($form->cc, -4);
        $order->ccname = $form->ccfirstname . " " . $form->cclastname;
        $order->courseid = $course->id;
        $order->userid = $USER->id;
        $order->status = AN_STATUS_NONE; // it will be changed...
        $order->settletime = 0; // cron changes this.
        $order->transid = 0; // Transaction Id
        $order->timecreated = $timenow;
        $order->amount = $curcost['cost'];
        $order->currency = $curcost['currency'];
        $order->id = insert_record("enrol_authorize", $order);
        if (!$order->id) {
            email_to_admin("Error while trying to insert new data", $order);
            $this->authorizeerrors['header'] = "Insert record error. Admin has been notified!";
            return;
        }

        $extra = new stdClass();
        $extra->x_card_num = $form->cc;
        $extra->x_card_code = $form->cvv;
        $extra->x_exp_date = $exp_date;
        $extra->x_currency_code = $curcost['currency'];
        $extra->x_amount = $curcost['cost'];
        $extra->x_first_name = $form->ccfirstname;
        $extra->x_last_name = $form->cclastname;
        $extra->x_country = $form->cccountry;
        $extra->x_address = $form->ccaddress;
        $extra->x_state = $form->ccstate;
        $extra->x_city = $form->cccity;
        $extra->x_zip = $form->cczip;

        $extra->x_invoice_num = $order->id;
        $extra->x_description = $course->shortname;

        $extra->x_cust_id = $USER->id;
        $extra->x_email = $USER->email;
        $extra->x_customer_ip = $useripno;
        $extra->x_email_customer = empty($CFG->enrol_mailstudents) ? 'FALSE' : 'TRUE';
        $extra->x_phone = '';
        $extra->x_fax = '';

        $message = '';
        $an_review = !empty($CFG->an_review);
        $action = $an_review ? AN_ACTION_AUTH_ONLY : AN_ACTION_AUTH_CAPTURE;
        if (AN_APPROVED != authorize_action($order, $message, $extra, $action, $form->cctype)) {
            email_to_admin($message, $order);
            $this->authorizeerrors['header'] = $message;
            return;
        }

        $SESSION->ccpaid = 1; // security check: don't duplicate payment
        if ($order->transid == 0) { // TEST MODE
            if ($an_review) {
                redirect($CFG->wwwroot, get_string("reviewnotify", "enrol_authorize"), '30');
            }
            else {
                enrol_into_course($course, $USER, 'manual');
                redirect("$CFG->wwwroot/course/view.php?id=$course->id");
            }
            return;
        }

        if ($an_review) { // review enabled, inform site payment managers and redirect the user who have paid to main page.
            $a = new stdClass;
            $a->url = "$CFG->wwwroot/enrol/authorize/index.php?order=$order->id";
            $a->orderid = $order->id;
            $a->transid = $order->transid;
            $a->amount = "$order->currency $order->amount";
            $a->expireon = userdate(authorize_getsettletime($timenow + (30 * 3600 * 24)));
            $a->captureon = userdate(authorize_getsettletime($timenow + (intval($CFG->an_capture_day) * 3600 * 24)));
            $a->course = $course->fullname;
            $a->user = fullname($USER);
            $a->acstatus = ($CFG->an_capture_day > 0) ? get_string('yes') : get_string('no');
            $emailmessage = get_string('adminneworder', 'enrol_authorize', $a);
            $a = new stdClass;
            $a->course = $course->shortname;
            $a->orderid = $order->id;
            $emailsubject = get_string('adminnewordersubject', 'enrol_authorize', $a);
            $context = get_context_instance(CONTEXT_SYSTEM, SITEID);
            if ($sitepaymentmanagers = get_users_by_capability($context, 'enrol/authorize:managepayments')) {
                foreach ($sitepaymentmanagers as $sitepaymentmanager) {
                    email_to_user($sitepaymentmanager, $USER, $emailsubject, $emailmessage);
                }
            }
            redirect($CFG->wwwroot, get_string("reviewnotify", "enrol_authorize"), '30');
            return;
        }

        // Credit card captured, ENROL student now...
        if (enrol_into_course($course, $USER, 'manual')) {
            if (!empty($CFG->enrol_mailstudents)) {
                send_welcome_messages($order->id);
            }
            $teacher = get_teacher($course->id);
            if (!empty($CFG->enrol_mailteachers)) {
                $a = new stdClass;
                $a->course = "$course->fullname";
                $a->user = fullname($USER);
                email_to_user($teacher,
                              $USER,
                              get_string("enrolmentnew", '', $course->shortname),
                              get_string('enrolmentnewuser', '', $a));
            }
            if (!empty($CFG->enrol_mailadmins)) {
                $a = new stdClass;
                $a->course = "$course->fullname";
                $a->user = fullname($USER);
                $admins = get_admins();
                foreach ($admins as $admin) {
                    email_to_user($admin,
                                  $USER,
                                  get_string("enrolmentnew", '', $course->shortname),
                                  get_string('enrolmentnewuser', '', $a));
                }
            }
        } else {
            email_to_admin("Error while trying to enrol " .
            fullname($USER) . " in '$course->fullname'", $order);
        }

        if ($SESSION->wantsurl) {
            $destination = $SESSION->wantsurl; unset($SESSION->wantsurl);
        } else {
            $destination = "$CFG->wwwroot/course/view.php?id=$course->id";
        }
        redirect($destination);
    }


    /**
     * The user submitted echeck form.
     *
     * @param object $form Form parameters
     * @param object $course Course info
     * @access private
     */
    function echeck_submit($form, $course)
    {
        global $CFG, $USER, $SESSION;
        require_once('authorizenetlib.php');

        prevent_double_paid($course);

        $useripno = getremoteaddr();
        $curcost = get_course_cost($course);

        // NEW ECHECK ORDER
        $timenow = time();
        $order = new stdClass();
        $order->paymentmethod = AN_METHOD_ECHECK;
        $order->cclastfour = 0;
        $order->ccname = $form->firstname . ' ' . $form->lastname;
        $order->courseid = $course->id;
        $order->userid = $USER->id;
        $order->status = AN_STATUS_NONE; // it will be changed...
        $order->settletime = 0; // cron changes this.
        $order->transid = 0; // Transaction Id
        $order->timecreated = $timenow;
        $order->amount = $curcost['cost'];
        $order->currency = $curcost['currency'];
        $order->id = insert_record("enrol_authorize", $order);
        if (!$order->id) {
            email_to_admin("Error while trying to insert new data", $order);
            $this->authorizeerrors['header'] = "Insert record error. Admin has been notified!";
            return;
        }

        $extra = new stdClass();
        $extra->x_bank_aba_code = $form->abacode;
        $extra->x_bank_acct_num = $form->accnum;
        $extra->x_bank_acct_type = $form->acctype;
        $extra->x_echeck_type = ($form->acctype == 'BUSINESSCHECKING') ? 'CCD' : 'WEB';
        $extra->x_bank_name = $form->bankname;
        $extra->x_currency_code = $curcost['currency'];
        $extra->x_amount = $curcost['cost'];
        $extra->x_first_name = $form->firstname;
        $extra->x_last_name = $form->lastname;
        $extra->x_country = $USER->country;
        $extra->x_address = $USER->address;
        $extra->x_city = $USER->city;
        $extra->x_state = '';
        $extra->x_zip = '';

        $extra->x_invoice_num = $order->id;
        $extra->x_description = $course->shortname;

        $extra->x_cust_id = $USER->id;
        $extra->x_email = $USER->email;
        $extra->x_customer_ip = $useripno;
        $extra->x_email_customer = empty($CFG->enrol_mailstudents) ? 'FALSE' : 'TRUE';
        $extra->x_phone = '';
        $extra->x_fax = '';

        $message = '';
        if (AN_REVIEW != authorize_action($order, $message, $extra, AN_ACTION_AUTH_CAPTURE)) {
            email_to_admin($message, $order);
            $this->authorizeerrors['header'] = $message;
            return;
        }

        $SESSION->ccpaid = 1; // security check: don't duplicate payment
        redirect($CFG->wwwroot, get_string("reviewnotify", "enrol_authorize"), '30');
    }


    /**
     * Gets access icons.
     *
     * @param object $course
     * @return string
     * @access public
     */
    function get_access_icons($course) {

        $manual = enrolment_factory::factory('manual');
        $str = $manual->get_access_icons($course);
        $curcost = get_course_cost($course);

        if (abs($curcost['cost']) > 0.00) {
            $strrequirespayment = get_string("requirespayment");
            $strcost = get_string("cost");
            $currency = $curcost['currency'];

            switch ($currency) {
                case 'USD': $currency = 'US$'; break;
                case 'CAD': $currency = 'C$'; break;
                case 'EUR': $currency = '&euro;'; break;
                case 'GBP': $currency = '&pound;'; break;
                case 'JPY': $currency = '&yen;'; break;
            }

            $str .= '<div class="cost" title="'.$strrequirespayment.'">'.$strcost.': ';
            $str .= $currency . ' ' . $curcost['cost'].'</div>';
        }

        return $str;
    }


    /**
     * Shows config form & errors
     *
     * @param object $frm
     * @access public
     */
    function config_form($frm)
    {
        global $CFG;

        if (! check_openssl_loaded()) {
            notify('PHP must be compiled with SSL support (--with-openssl)');
        }

        if (empty($CFG->loginhttps) and substr($CFG->wwwroot, 0, 5) !== 'https') {
            notify('loginhttps must be ON');
        }

        if (!empty($frm->an_review)) {
            $captureday = intval($frm->an_capture_day);
            $emailexpired = intval($frm->an_emailexpired);
            if ($captureday > 0 || $emailexpired > 0) {
                $mconfig = get_config('enrol/authorize');
                if ((time() - intval($mconfig->an_lastcron) > 3600 * 24)) {
                    notify(get_string('admincronsetup', 'enrol_authorize'));
                }
            }
        }

        if ($count = count_records('enrol_authorize', 'status', AN_STATUS_AUTH)) {
            $a = new stdClass;
            $a->count = $count;
            $a->url = $CFG->wwwroot."/enrol/authorize/index.php?status=".AN_STATUS_AUTH;
            notify(get_string('adminpendingorders', 'enrol_authorize', $a));
        }

        if (data_submitted()) {
            if (empty($frm->an_login)) {
                notify("an_login required");
            }
            if (empty($frm->an_tran_key) && empty($frm->an_password)) {
                notify("an_tran_key or an_password required");
            }
        }

        include($CFG->dirroot.'/enrol/authorize/config.html');
    }


    /**
     * process_config
     *
     * @param object $config
     * @return bool true if it will be saved.
     * @access public
     */
    function process_config($config)
    {
        global $CFG;

        // site settings
        if (($cost = optional_param('enrol_cost', 5, PARAM_INT)) > 0) {
            set_config('enrol_cost', $cost);
        }
        set_config('enrol_currency', optional_param('enrol_currency', 'USD', PARAM_ALPHA));
        set_config('enrol_mailstudents', optional_param('enrol_mailstudents', 0, PARAM_BOOL));
        set_config('enrol_mailteachers', optional_param('enrol_mailteachers', 0, PARAM_BOOL));
        set_config('enrol_mailadmins', optional_param('enrol_mailadmins', 0, PARAM_BOOL));

        // optional authorize.net settings
        set_config('an_avs', optional_param('an_avs', 0, PARAM_BOOL));
        set_config('an_test', optional_param('an_test', 0, PARAM_BOOL));
        set_config('an_referer', optional_param('an_referer', 'http://', PARAM_URL));

        $acceptmethods = optional_param('acceptmethods', get_list_of_payment_methods(), PARAM_ALPHA);
        set_config('an_acceptmethods', implode(',', $acceptmethods));
        $acceptccs = optional_param('acceptccs', array_keys(get_list_of_creditcards()), PARAM_ALPHA);
        set_config('an_acceptccs', implode(',', $acceptccs));
        $acceptechecktypes = optional_param('acceptechecktypes', get_list_of_bank_account_types(), PARAM_ALPHA);
        set_config('an_acceptechecktypes', implode(',', $acceptechecktypes));

        $cutoff_hour = optional_param('an_cutoff_hour', 0, PARAM_INT);
        $cutoff_min = optional_param('an_cutoff_min', 5, PARAM_INT);
        set_config('an_cutoff', $cutoff_hour * 60 + $cutoff_min);

        // cron depencies
        $reviewval = optional_param('an_review', 0, PARAM_BOOL);
        $captureday = optional_param('an_capture_day', 5, PARAM_INT);
        $emailexpired = optional_param('an_emailexpired', 2, PARAM_INT);
        $emailexpiredteacher = optional_param('an_emailexpiredteacher', 0, PARAM_BOOL);
        $sorttype = optional_param('an_sorttype', 'ttl', PARAM_ALPHA);

        $captureday = ($captureday > 29) ? 29 : (($captureday < 0) ? 0 : $captureday);
        $emailexpired = ($emailexpired > 5) ? 5 : (($emailexpired < 0) ? 0 : $emailexpired);

        if (!empty($reviewval) && ($captureday > 0 || $emailexpired > 0)) {
            $mconfig = get_config('enrol/authorize');
            if (time() - intval($mconfig->an_lastcron) > 3600 * 24) {
                return false;
            }
        }

        set_config('an_review', $reviewval);
        set_config('an_capture_day', $captureday);
        set_config('an_emailexpired', $emailexpired);
        set_config('an_emailexpiredteacher', $emailexpiredteacher);
        set_config('an_sorttype', $sorttype);

        // https and openssl library is required
        if ((substr($CFG->wwwroot, 0, 5) !== 'https' and empty($CFG->loginhttps)) or
            !check_openssl_loaded()) {
            return false;
        }

        // required fields
        $loginval = optional_param('an_login', '');
        if (empty($loginval)) {
            return false;
        }
        set_config('an_login', $loginval);

        $tranval = optional_param('an_tran_key', '');
        $passwordval = optional_param('an_password', '');
        $deletecurrent = optional_param('delete_current', '');

        if (!empty($passwordval)) { // password is changing
            set_config('an_password', $passwordval);
        }
        elseif (!empty($deletecurrent) and !empty($tranval)) {
            set_config('an_password', '');
            $CFG->an_password = '';
        }

        if (empty($tranval) and empty($CFG->an_password)) {
            return false;
        }

        set_config('an_tran_key', $tranval);
        return true;
    }

    /**
     * This function is run by admin/cron.php every time if admin has enabled this plugin.
     *
     * Everyday at settlement time (default is 00:05), it cleans up some tables
     * and sends email to admin/teachers about pending orders expiring if manual-capture has enabled.
     *
     * If admin set up 'Order review' and 'Capture day', it captures credits cards and enrols students.
     *
     * @access public
     */
    function cron()
    {
        global $CFG;
        require_once($CFG->dirroot.'/enrol/authorize/authorizenetlib.php');

        $oneday = 86400;
        $timenow = time();
        $settlementtime = authorize_getsettletime($timenow);
        $timediff30 = $settlementtime - (30 * $oneday);
        $mconfig = get_config('enrol/authorize');
        set_config('an_lastcron', $timenow, 'enrol/authorize');

        mtrace("Processing authorize cron...");

        if (intval($mconfig->an_dailysettlement) < $settlementtime) {
            set_config('an_dailysettlement', $settlementtime, 'enrol/authorize');
            mtrace("    daily cron; some cleanups and sending email to admins the count of pending orders expiring", ": ");
            $this->cron_daily();
            mtrace("done");
        }

        mtrace("    scheduled capture", ": ");
        if (empty($CFG->an_review) or
           (!empty($CFG->an_test)) or
           (intval($CFG->an_capture_day) < 1) or
           (!check_openssl_loaded())) {
            mtrace("disabled");
            return; // order review disabled or test mode or manual capture or openssl wasn't loaded.
        }

        $timediffcnf = $settlementtime - (intval($CFG->an_capture_day) * $oneday);
        $sql = "SELECT * FROM {$CFG->prefix}enrol_authorize
                WHERE (status = '" .AN_STATUS_AUTH. "')
                  AND (timecreated < '$timediffcnf')
                  AND (timecreated > '$timediff30')
                ORDER BY courseid";

        if (!$orders = get_records_sql($sql)) {
            mtrace("no pending orders");
            return;
        }

        $eachconn = intval($mconfig->an_eachconnsecs);
        if (empty($eachconn)) $eachconn = 3;
        elseif ($eachconn > 60) $eachconn = 60;

        $ordercount = count((array)$orders);
        if (($ordercount * $eachconn) + intval($mconfig->an_lastcron) > $timenow) {
            mtrace("blocked");
            return;
        }

        mtrace("    $ordercount orders are being processed now", ": ");

        $faults = '';
        $sendem = array();
        $elapsed = time();
        @set_time_limit(0);
        $this->log = "AUTHORIZE.NET AUTOCAPTURE CRON: " . userdate($timenow) . "\n";

        $lastcourseid = 0;
        foreach ($orders as $order) {
            $message = '';
            $extra = NULL;
            if (AN_APPROVED == authorize_action($order, $message, $extra, AN_ACTION_PRIOR_AUTH_CAPTURE)) {
                if ($lastcourseid != $order->courseid) {
                    $lastcourseid = $order->courseid;
                    $course = get_record('course', 'id', $lastcourseid);
                    $role = get_default_course_role($course);
                    $context = get_context_instance(CONTEXT_COURSE, $lastcourseid);
                }
                $timestart = $timeend = 0;
                if ($course->enrolperiod) {
                    $timestart = $timenow;
                    $timeend = $order->settletime + $course->enrolperiod;
                }
                $user = get_record('user', 'id', $order->userid);
                if (role_assign($role->id, $user->id, 0, $context->id, $timestart, $timeend, 0, 'manual')) {
                    $this->log .= "User($user->id) has been enrolled to course($course->id).\n";
                    if (!empty($CFG->enrol_mailstudents)) {
                        $sendem[] = $order->id;
                    }
                }
                else {
                    $faults .= "Error while trying to enrol ".fullname($user)." in '$course->fullname' \n";
                    foreach ($order as $okey => $ovalue) {
                        $faults .= "   $okey = $ovalue\n";
                    }
                }
            }
            else {
                $this->log .= "Error, Order# $order->id: " . $message . "\n";
            }
        }

        mtrace("processed");

        $timenow = time();
        $elapsed = $timenow - $elapsed;
        $eachconn = ceil($elapsed / $ordercount);
        set_config('an_eachconnsecs', $eachconn, 'enrol/authorize');

        $this->log .= "AUTHORIZE.NET CRON FINISHED: " . userdate($timenow);

        $adminuser = get_admin();
        if (!empty($faults)) {
            email_to_user($adminuser, $adminuser, "AUTHORIZE.NET CRON FAULTS", $faults);
        }
        if (!empty($CFG->enrol_mailadmins)) {
            email_to_user($adminuser, $adminuser, "AUTHORIZE.NET CRON LOG", $this->log);
        }

        // Send emails to students about which courses have enrolled.
        if (!empty($sendem)) {
            mtrace("    sending welcome messages to students", ": ");
            send_welcome_messages($sendem);
            mtrace("sent");
        }
    }

    /**
     * Daily cron. It executes at settlement time (default is 00:05).
     *
     * @access private
     */
    function cron_daily()
    {
        global $CFG, $SITE;
        require_once($CFG->dirroot.'/enrol/authorize/authorizenetlib.php');

        $oneday = 86400;
        $timenow = time();
        $settlementtime = authorize_getsettletime($timenow);
        $timediff30 = $settlementtime - (30 * $oneday);

        // Delete orders that no transaction was made.
        $select = "(status='".AN_STATUS_NONE."') AND (timecreated<'$timediff30')";
        delete_records_select('enrol_authorize', $select);

        // Pending orders are expired with in 30 days.
        $select = "(status='".AN_STATUS_AUTH."') AND (timecreated<'$timediff30')";
        execute_sql("UPDATE {$CFG->prefix}enrol_authorize SET status='".AN_STATUS_EXPIRE."' WHERE $select", false);

        // Delete expired orders 60 days later.
        $timediff60 = $settlementtime - (60 * $oneday);
        $select = "(status='".AN_STATUS_EXPIRE."') AND (timecreated<'$timediff60')";
        delete_records_select('enrol_authorize', $select);

        // XXX TODO SEND EMAIL to uploadcsv user

        // Daily warning email for pending orders expiring.
        if (empty($CFG->an_emailexpired)) {
            return; // not enabled
        }

        // Pending orders count will be expired.
        $timediffem = $settlementtime - ((30 - intval($CFG->an_emailexpired)) * $oneday);
        $select = "(status='". AN_STATUS_AUTH ."') AND (timecreated<'$timediffem') AND (timecreated>'$timediff30')";
        $count = count_records_select('enrol_authorize', $select);
        if (!$count) {
            return;
        }

        // Email to admin
        $a = new stdClass;
        $a->pending = $count;
        $a->days = $CFG->an_emailexpired;
        $a->course = $SITE->shortname;
        $subject = get_string('pendingorderssubject', 'enrol_authorize', $a);
        $a = new stdClass;
        $a->pending = $count;
        $a->days = $CFG->an_emailexpired;
        $a->course = $SITE->fullname;
        $a->enrolurl = "$CFG->wwwroot/$CFG->admin/users.php";
        $a->url = $CFG->wwwroot.'/enrol/authorize/index.php?status='.AN_STATUS_AUTH;
        $message = get_string('pendingordersemail', 'enrol_authorize', $a);
        $adminuser = get_admin();
        email_to_user($adminuser, $adminuser, $subject, $message);

        // Email to teachers
        if (empty($CFG->an_emailexpiredteacher)) {
            return; // email feature disabled for teachers.
        }

        $sorttype = empty($CFG->an_sorttype) ? 'ttl' : $CFG->an_sorttype;
        $sql = "SELECT e.courseid, e.currency, c.fullname, c.shortname,
                  COUNT(e.courseid) AS cnt, SUM(e.amount) as ttl
                FROM {$CFG->prefix}enrol_authorize e
                  INNER JOIN {$CFG->prefix}course c ON c.id = e.courseid
                WHERE (e.status = ". AN_STATUS_AUTH .")
                  AND (e.timecreated < $timediffem)
                  AND (e.timecreated > $timediff30)
                GROUP BY e.courseid
                ORDER BY $sorttype DESC";

        $courseinfos = get_records_sql($sql);
        foreach($courseinfos as $courseinfo) {
            $lastcourse = $courseinfo->courseid;
            $context = get_context_instance(CONTEXT_COURSE, $lastcourse);
            if ($paymentmanagers = get_users_by_capability($context, 'enrol/authorize:managepayments')) {
                $a = new stdClass;
                $a->course = $courseinfo->shortname;
                $a->pending = $courseinfo->cnt;
                $a->days = $CFG->an_emailexpired;
                $subject = get_string('pendingorderssubject', 'enrol_authorize', $a);
                $a = new stdClass;
                $a->course = $courseinfo->fullname;
                $a->pending = $courseinfo->cnt;
                $a->currency = $courseinfo->currency;
                $a->sumcost = $courseinfo->ttl;
                $a->days = $CFG->an_emailexpired;
                $a->url = $CFG->wwwroot.'/enrol/authorize/index.php?course='.$lastcourse.'&amp;status='.AN_STATUS_AUTH;
                $message = get_string('pendingordersemailteacher', 'enrol_authorize', $a);
                foreach ($paymentmanagers as $paymentmanager) {
                    email_to_user($paymentmanager, $adminuser, $subject, $message);
                }
            }
        }
    }
}
?>
