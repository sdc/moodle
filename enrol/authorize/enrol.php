<?php  // $Id$

require_once($CFG->dirroot.'/enrol/enrol.class.php');
require_once($CFG->dirroot.'/enrol/authorize/const.php');

/**
 * get_list_of_creditcards
 *
 * @param bool $getall
 * @return array
 */
function get_list_of_creditcards($getall = false)
{
    global $CFG;

    $alltypes = array(
        'mcd' => 'Master Card',
        'vis' => 'Visa',
        'amx' => 'American Express',
        'dsc' => 'Discover',
        'dnc' => 'Diners Club',
        'jcb' => 'JCB',
        'swi' => 'Switch',
        'dlt' => 'Delta',
        'enr' => 'EnRoute'
    );

    if ($getall || empty($CFG->an_acceptccs)) {
        return $alltypes;
    }

    $ret = array();
    $ccs = explode(',', $CFG->an_acceptccs);

    foreach ($ccs as $key) {
        $ret[$key] = $alltypes[$key];
    }

    return $ret;
}

/**
 * enrolment_plugin_authorize
 *
 */
class enrolment_plugin_authorize
{
    /**
     * Credit card error messages.
     *
     * @var array
     * @access public
     */
    var $ccerrors = array();

    /**
     * Cron log.
     *
     * @var string
     * @access public
     */
    var $log;


    /**
     * Shows a credit card form for registration.
     *
     * @param object $course Course info
     * @access public
     */
    function print_entry($course)
    {
        global $CFG, $USER, $form;

        if ($this->zero_cost($course) or isguest()) {
            $manual = enrolment_factory::factory('manual');
            $manual->print_entry($course);
            return; // No money for guests ;)
        }

        $this->prevent_double_paid($course);

        if (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == 'off') {
            if (empty($CFG->loginhttps)) {
                error(get_string('httpsrequired', 'enrol_authorize'));
            } else {
                $wwwsroot = str_replace('http:','https:', $CFG->wwwroot);
                redirect("$wwwsroot/course/enrol.php?id=$course->id");
                exit;
            }
        }

        $teacher = get_teacher($course->id);
        $strcourses = get_string('courses');
        $strloginto = get_string('loginto', '', $course->shortname);

        print_header($strloginto, $course->fullname, "<a href=\"$CFG->wwwroot/course/\">$strcourses</a> -> $strloginto");
        print_course($course, '80%');

        if ($course->password) {
            print_simple_box(get_string('choosemethod', 'enrol_authorize'), 'center');
            $password = '';
            include($CFG->dirroot.'/enrol/manual/enrol.html');
        }

        print_simple_box_start('center');
        include($CFG->dirroot.'/enrol/authorize/enrol.html');
        print_simple_box_end();

        print_footer();
    }


    /**
     * Checks form params.
     *
     * @param object $form Form parameters
     * @param object $course Course info
     * @access public
     */
    function check_entry($form, $course) {
        if ((!empty($form->password)) or isguest() or $this->zero_cost($course)) {
            $manual = enrolment_factory::factory('manual');
            $manual->check_entry($form, $course);
        } elseif ((!empty($form->ccsubmit)) and $this->validate_enrol_form($form)) {
            $this->cc_submit($form, $course);
        }
    }


    /**
     * Credit card number mode.
     * Send to authorize.net.
     *
     * @param object $form Form parameters
     * @param object $course Course info
     * @access private
     */
    function cc_submit($form, $course)
    {
        global $CFG, $USER, $SESSION;
        require_once('authorizenetlib.php');

        $this->prevent_double_paid($course);

        $useripno = getremoteaddr();
        $curcost = $this->get_course_cost($course);
        $exp_date = sprintf("%02d", $form->ccexpiremm) . $form->ccexpireyyyy;

        // NEW ORDER
        $timenow = time();
        $order = new stdClass();
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
            $this->email_to_admin("Error while trying to insert new data", $order);
            $this->ccerrors['header'] = "Insert record error. Admin has been notified!";
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
        $success = authorizenet_action($order, $message, $extra, $action);
        if (!$success) {
            $this->email_to_admin($message, $order);
            $this->ccerrors['header'] = $message;
            return;
        }

        $SESSION->ccpaid = 1; // security check: don't duplicate payment
        if ($order->transid == 0) { // TEST MODE
            if ($an_review) {
                redirect($CFG->wwwroot, get_string("reviewnotify", "enrol_authorize"), '30');
            }
            else {
                $timestart = $timenow;
                $timeend = $timestart + (3600 * 24); // just enrol for 1 days :)
                enrol_student($USER->id, $course->id, $timestart, $timeend, 'authorize');
                redirect("$CFG->wwwroot/course/view.php?id=$course->id");
            }
            return;
        }

        if ($an_review) { // review enabled, inform admin and redirect to main page.
            if (update_record("enrol_authorize", $order)) {
                $a = new stdClass;
                $a->url = "$CFG->wwwroot/enrol/authorize/index.php?order=$order->id";
                $a->orderid = $order->id;
                $a->transid = $order->transid;
                $a->amount = "$order->currency $order->amount";
                $a->expireon = userdate(getsettletime($timenow + (30 * 3600 * 24)));
                $a->captureon = userdate(getsettletime($timenow + (intval($CFG->an_capture_day) * 3600 * 24)));
                $a->course = $course->fullname;
                $a->user = fullname($USER);
                $a->acstatus = ($CFG->an_capture_day > 0) ? get_string('yes') : get_string('no');
                $emailmessage = get_string('adminneworder', 'enrol_authorize', $a);
                $a = new stdClass;
                $a->course = $course->shortname;
                $a->orderid = $order->id;
                $emailsubject = get_string('adminnewordersubject', 'enrol_authorize', $a);
                $admins = get_admins();
                foreach ($admins as $admin) {
                    email_to_user($admin, $USER, $emailsubject, $emailmessage);
                }
            }
            else {
                $this->email_to_admin("Error while trying to update data. Please edit manually this record: " .
                                      "ID=$order->id in enrol_authorize table.", $order);
            }
            redirect($CFG->wwwroot, get_string("reviewnotify", "enrol_authorize"), '30');
            return;
        }

        // credit card captured, ENROL student...
        if (!update_record("enrol_authorize", $order)) {
            $this->email_to_admin("Error while trying to update data. Please edit manually this record: " .
                                   "ID=$order->id in enrol_authorize table.", $order);
                                   // no error occured??? enrol student??? return??? Database busy???
        }

        if ($course->enrolperiod) {
            $timestart = $timenow;
            $timeend = $timestart + $course->enrolperiod;
        } else {
            $timestart = $timeend = 0;
        }

        if (enrol_student($USER->id, $course->id, $timestart, $timeend, 'authorize')) {
            $teacher = get_teacher($course->id);
            if (!empty($CFG->enrol_mailstudents)) {
                $a = new stdClass;
                $a->coursename = "$course->fullname";
                $a->profileurl = "$CFG->wwwroot/user/view.php?id=$USER->id";
                email_to_user($USER,
                              $teacher,
                              get_string("enrolmentnew", '', $course->shortname),
                              get_string('welcometocoursetext', '', $a));
            }
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
            $this->email_to_admin("Error while trying to enrol ".fullname($USER)." in '$course->fullname'", $order);
        }

        if ($SESSION->wantsurl) {
            $destination = $SESSION->wantsurl; unset($SESSION->wantsurl);
        } else {
            $destination = "$CFG->wwwroot/course/view.php?id=$course->id";
        }
        redirect($destination);
    }

    /**
     * validate_enrol_form
     *
     * @param object $form Form parameters
     * @access private
     */
    function validate_enrol_form($form)
    {
        global $CFG;
        require_once('ccval.php');

        if (empty($form->cc)) {
            $this->ccerrors['cc'] = get_string('missingcc', 'enrol_authorize');
        }
        if (empty($form->ccexpiremm) || empty($form->ccexpireyyyy)) {
            $this->ccerrors['ccexpire'] = get_string('missingccexpire', 'enrol_authorize');
        }
        else {
            $expdate = sprintf("%02d", intval($form->ccexpiremm)) . $form->ccexpireyyyy;
            $validcc = CCVal($form->cc, $form->cctype, $expdate);
            if (!$validcc) {
                if ($validcc === 0) {
                    $this->ccerrors['ccexpire'] = get_string('ccexpired', 'enrol_authorize');
                }
                else {
                    $this->ccerrors['cc'] = get_string('ccinvalid', 'enrol_authorize');
                }
            }
        }

        if (empty($form->ccfirstname) || empty($form->cclastname)) {
            $this->ccerrors['ccfirstlast'] = get_string('missingfullname');
        }

        if (empty($form->cvv) || !is_numeric($form->cvv)) {
            $this->ccerrors['cvv'] = get_string('missingcvv', 'enrol_authorize');
        }

        if (empty($form->cctype) || !in_array($form->cctype, array_keys(get_list_of_creditcards()))) {
            $this->ccerrors['cctype'] = get_string('missingcctype', 'enrol_authorize');
        }

        if (!empty($CFG->an_avs)) {
            if (empty($form->ccaddress)) {
                $this->ccerrors['ccaddress'] = get_string('missingaddress', 'enrol_authorize');
            }
            if (empty($form->cccity)) {
                $this->ccerrors['cccity'] = get_string('missingcity');
            }
            if (empty($form->cccountry)) {
                $this->ccerrors['cccountry'] = get_string('missingcountry');
            }
        }
        if (empty($form->cczip) || !is_numeric($form->cczip)) {
            $this->ccerrors['cczip'] = get_string('missingzip', 'enrol_authorize');
        }

        if (!empty($this->ccerrors)) {
            $this->ccerrors['header'] = get_string('someerrorswerefound');
            return false;
        }

        return true;
    }

    /**
     * zero_cost
     *
     * @param unknown_type $course
     * @return number
     * @access private
     */
    function zero_cost($course) {
        $curcost = $this->get_course_cost($course);
        return (abs($curcost['cost']) < 0.01);
    }


    /**
     * get_course_cost
     *
     * @param object $course
     * @return array
     * @access private
     */
    function get_course_cost($course)
    {
        global $CFG;

        $cost = (float)0;
        $currency = (!empty($course->currency))
                     ? $course->currency :( empty($CFG->enrol_currency)
                                            ? 'USD' : $CFG->enrol_currency );

        if (!empty($course->cost)) {
            $cost = (float)(((float)$course->cost) < 0) ? $CFG->enrol_cost : $course->cost;
        }

        $cost = format_float($cost, 2);
        $ret = array('cost' => $cost, 'currency' => $currency);

        return $ret;
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
        $curcost = $this->get_course_cost($course);

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

        if (!$this->check_openssl_loaded()) {
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
        set_config('enrol_cost', optional_param('enrol_cost', 5, PARAM_INT));
        set_config('enrol_currency', optional_param('enrol_currency', 'USD', PARAM_ALPHA));
        set_config('enrol_mailstudents', optional_param('enrol_mailstudents', 0, PARAM_BOOL));
        set_config('enrol_mailteachers', optional_param('enrol_mailteachers', 0, PARAM_BOOL));
        set_config('enrol_mailadmins', optional_param('enrol_mailadmins', 0, PARAM_BOOL));

        // optional authorize.net settings
        set_config('an_avs', optional_param('an_avs', 0, PARAM_BOOL));
        set_config('an_test', optional_param('an_test', 0, PARAM_BOOL));
        set_config('an_teachermanagepay', optional_param('an_teachermanagepay', 0, PARAM_BOOL));
        set_config('an_referer', optional_param('an_referer', 'http://', PARAM_URL));

        $acceptccs = optional_param('acceptccs', array_keys(get_list_of_creditcards()), PARAM_ALPHA);
        set_config('an_acceptccs', implode(',', $acceptccs));

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
        $mconfig = get_config('enrol/authorize');

        if ((!empty($reviewval)) &&
            ($captureday > 0 || $emailexpired > 0) &&
            (time() - intval($mconfig->an_lastcron) > 3600 * 24)) {
            return false;
        }

        set_config('an_review', $reviewval);
        set_config('an_capture_day', $captureday);
        set_config('an_emailexpired', $emailexpired);
        set_config('an_emailexpiredteacher', $emailexpiredteacher);
        set_config('an_sorttype', $sorttype);

        // required fields
        $loginval = optional_param('an_login', '');
        $tranval = optional_param('an_tran_key', '');
        $passwordval = optional_param('an_password', '');

        if ((empty($CFG->loginhttps) and substr($CFG->wwwroot, 0, 5) !== 'https') ||
            !$this->check_openssl_loaded() ||
            empty($loginval) ||
            (empty($tranval) and empty($passwordval))) {
            return false;
        }

        set_config('an_login', $loginval);
        set_config('an_password', $passwordval);
        set_config('an_tran_key', $tranval);

        return true;
    }


    /**
     * email_to_admin
     *
     * @param string $subject
     * @param mixed $data
     * @access private
     */
    function email_to_admin($subject, $data)
    {
        $site = get_site();
        $admin = get_admin();
        $data = (array)$data;

        $message = "$site->fullname:  Transaction failed.\n\n$subject\n\n";
        foreach ($data as $key => $value) {
            $message .= "$key => $value\n";
        }
        email_to_user($admin, $admin, "Authorize.net ERROR: ".$subject, $message);
    }


    /**
     * prevent_double_paid
     *
     * @param object $course
     * @access private
     */
    function prevent_double_paid($course)
    {
        global $CFG, $SESSION, $USER;

        $status = empty($CFG->an_test) ? AN_STATUS_AUTH : AN_STATUS_NONE;

        if ($rec=get_record('enrol_authorize','userid',$USER->id,'courseid',$course->id,'status',$status,'id')) {
            $a = new stdClass;
            $a->orderid = $rec->id;
            $a->url = "$CFG->wwwroot/enrol/authorize/index.php?order=$a->orderid";
            redirect($a->url, get_string("paymentpending", "enrol_authorize", $a), '10');
            return;
        }
        if (isset($SESSION->ccpaid)) {
            unset($SESSION->ccpaid);
            redirect($CFG->wwwroot . '/login/logout.php');
            return;
        }
    }


    /**
     * check_openssl_loaded
     *
     * @return bool
     * @access private
     */
    function check_openssl_loaded() {
        return extension_loaded('openssl');
    }


    /**
     * cron
     * @access public
     */
    function cron()
    {
        global $CFG, $SITE;
        require_once($CFG->dirroot.'/enrol/authorize/authorizenetlib.php');

        $oneday = 86400;
        $timenow = time();
        $settlementtime = getsettletime($timenow);
        $timediff30 = $settlementtime - (30 * $oneday);
        $mconfig = get_config('enrol/authorize');
        set_config('an_lastcron', $timenow, 'enrol/authorize');

        if (intval($mconfig->an_dailysettlement) < $settlementtime) {
            set_config('an_dailysettlement', $settlementtime, 'enrol/authorize');
            // Some clean-up and update
            $select = "(status='".AN_STATUS_NONE."') AND (timecreated<'$timediff30')";
            delete_records_select('enrol_authorize', $select);
            $select = "(status='".AN_STATUS_AUTH."') AND (timecreated<'$timediff30')";
            execute_sql("UPDATE {$CFG->prefix}enrol_authorize SET status='".AN_STATUS_EXPIRE."' WHERE $select", false);
            $timediff60 = $settlementtime - (60 * $oneday);
            $select = "(status='".AN_STATUS_EXPIRE."') AND (timecreated<'$timediff60')";
            delete_records_select('enrol_authorize', $select);
            // Daily warning email for expiring pending orders.
            if (!empty($CFG->an_emailexpired)) {
                $timediffem = $settlementtime - ((30 - intval($CFG->an_emailexpired)) * $oneday);
                $select = "(status='". AN_STATUS_AUTH ."') AND (timecreated<'$timediffem') AND (timecreated>'$timediff30')";
                if ($count = count_records_select('enrol_authorize', $select)) {
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
                    $a->url = $CFG->wwwroot."/enrol/authorize/index.php?status=".AN_STATUS_AUTH;
                    $message = get_string('pendingordersemail', 'enrol_authorize', $a);
                    $adminuser = get_admin();
                    email_to_user($adminuser, $adminuser, $subject, $message);
                    if (!empty($CFG->an_teachermanagepay) and !empty($CFG->an_emailexpiredteacher)) {
                        $sorttype = empty($CFG->an_sorttype) ? 'ttl' : $CFG->an_sorttype;
                        $sql = "SELECT E.courseid, E.currency, C.fullname, C.shortname, " .
                               "COUNT(E.courseid) AS cnt, SUM(E.amount) as ttl " .
                               "FROM {$CFG->prefix}enrol_authorize E " .
                               "INNER JOIN {$CFG->prefix}course C ON C.id = E.courseid " .
                               "WHERE $select GROUP BY E.courseid ORDER BY $sorttype DESC";
                        $message = ''; $subject = ''; $lastcourse = 0;
                        $coursesandcounts = get_records_sql($sql);
                        foreach($coursesandcounts as $courseandcount) {
                            $lastcourse = $courseandcount->courseid;
                            if ($teachers = get_course_teachers($lastcourse)) {
                                $a = new stdClass;
                                $a->course = $courseandcount->shortname;
                                $a->pending = $courseandcount->cnt;
                                $a->days = $CFG->an_emailexpired;
                                $subject = get_string('pendingorderssubject', 'enrol_authorize', $a);
                                $a = new stdClass;
                                $a->course = $courseandcount->fullname;
                                $a->pending = $courseandcount->cnt;
                                $a->currency = $courseandcount->currency;
                                $a->sumcost = $courseandcount->ttl;
                                $a->days = $CFG->an_emailexpired;
                                $a->url = $CFG->wwwroot.'/enrol/authorize/index.php?course='.
                                $lastcourse.'&amp;status='.AN_STATUS_AUTH;
                                $message = get_string('pendingordersemailteacher', 'enrol_authorize', $a);
                                foreach ($teachers as $teacher) {
                                    email_to_user($teacher, $adminuser, $subject, $message);
                                }
                            }
                        }
                    }
                }
            }
        }

        if (empty($CFG->an_review) || (!empty($CFG->an_test)) ||
            (intval($CFG->an_capture_day) < 1) || (!$this->check_openssl_loaded())) {
            return;
        }

        $timediffcnf = $settlementtime - (intval($CFG->an_capture_day) * $oneday);
        $sql = "SELECT E.*, C.fullname, C.enrolperiod " .
               "FROM {$CFG->prefix}enrol_authorize E " .
               "INNER JOIN {$CFG->prefix}course C ON C.id = E.courseid " .
               "WHERE (status = '" .AN_STATUS_AUTH. "') " .
               "  AND (E.timecreated < '$timediffcnf') AND (E.timecreated > '$timediff30')";

        if (!$orders = get_records_sql($sql)) {
            return;
        }

        $eachconn = intval($mconfig->an_eachconnsecs);
        if (empty($eachconn)) $eachconn = 3;
        elseif ($eachconn > 60) $eachconn = 60;

        $ordercount = count((array)$orders);
        if (($ordercount * $eachconn) + intval($mconfig->an_lastcron) > $timenow) {
            return;
        }

        $faults = '';
        $sendem = array();
        $elapsed = time();
        @set_time_limit(0);
        $this->log = "AUTHORIZE.NET AUTOCAPTURE CRON: " . userdate($timenow) . "\n";

        foreach ($orders as $order) {
            $message = '';
            $extra = NULL;
            $oldstatus = $order->status;
            $success = authorizenet_action($order, $message, $extra, AN_ACTION_PRIOR_AUTH_CAPTURE);
            if ($success) {
                if (!update_record("enrol_authorize", $order)) {
                    $this->email_to_admin("Error while trying to update data. Please edit manually this record: " .
                    "ID=$order->id in enrol_authorize table.", $order);
                }
                $timestart = $timeend = 0;
                if ($order->enrolperiod) {
                    $timestart = $timenow;
                    $timeend = $order->settletime + $order->enrolperiod;
                }
                if (enrol_student($order->userid, $order->courseid, $timestart, $timeend, 'authorize')) {
                    $this->log .= "User($order->userid) has been enrolled to course($order->courseid).\n";
                    if (!empty($CFG->enrol_mailstudents)) {
                        $sendem[] = $order->id;
                    }
                }
                else {
                    $user = get_record('user', 'id', $order->userid);
                    $faults .= "Error while trying to enrol ".fullname($user)." in '$order->fullname' \n";
                    foreach ($order as $okey => $ovalue) {
                        $faults .= "   $okey = $ovalue\n";
                    }
                }
            }
            else {
                $this->log .= "Order $order->id: " . $message . "\n";
                if ($order->status != $oldstatus) { //expired
                    update_record("enrol_authorize", $order);
                }
            }
        }

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
        if (empty($sendem)) {
            return;
        }

        $lastcourse = 0;
        $select = "SELECT E.id, E.courseid, E.userid, C.fullname " .
                  "FROM {$CFG->prefix}enrol_authorize E " .
                  "INNER JOIN {$CFG->prefix}course C ON C.id = E.courseid " .
                  "WHERE E.id IN(" . implode(',', $sendem) . ") " .
                  "ORDER BY E.courseid";
        $orders = get_records_sql($select);
        foreach ($orders as $order)
        {
            if ($lastcourse != $order->courseid) {
                $lastcourse = $order->courseid;
                $teacher = get_teacher($lastcourse);
            }
            $user = get_record('user', 'id', $order->userid);
            $a = new stdClass;
            $a->coursename = $order->fullname;
            $a->profileurl = "$CFG->wwwroot/user/view.php?id=$user->id";
            email_to_user($user, $teacher,
                          get_string("enrolmentnew", '', $order->fullname),
                          get_string('welcometocoursetext', '', $a));
        }
    }
}
?>
