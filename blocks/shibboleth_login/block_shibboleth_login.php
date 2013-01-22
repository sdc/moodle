<?php

class block_shibboleth_login extends block_base {

    function init() {
        $this->title = get_string('pluginname', 'block_shibboleth_login');
    }

    function applicable_formats() {
        return array('site' => true);
    }

    function instance_allow_multiple() {
        return false;
    }

    function get_content () {
        global $CFG;

        if ($this->content !== NULL) {
            return $this->content;
        }

        $this->content = new stdClass;

        if (!isloggedin() or isguestuser()) {

            // had to hard-code the URL as it uses https:// and that's not available anywhere, AFAIK...
            $url = str_replace('http://', 'https://', $CFG->wwwroot.'/auth/shibboleth/index.php');
            $this->content->text = '<div><a href="'.$url.'">';
            $this->content->text .= '<img style="display: block; margin: 0 auto;" src="'.$CFG->wwwroot.'/blocks/shibboleth_login/login.png" /></a></div>';

        }

        $this->content->footer = '';

        return $this->content;
    }
}

?>
