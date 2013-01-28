<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Shibboleth login block.
 *
 * @package     block
 * @subpackage  shibboleth_login
 * @copyright   2011 onwards Paul Vaughan, Kevin Hughes
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

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

            // Had to hard-code the URL as it uses https:// and that's not available anywhere, AFAIK...
            $url = str_replace('http://', 'https://', $CFG->wwwroot.'/auth/shibboleth/index.php');
            $this->content->text  = '<div><a href="'.$url.'">';
            $this->content->text .= '  <img style="display: block; margin: 0 auto;" src="'.$CFG->wwwroot.'/blocks/shibboleth_login/login.png">';
            $this->content->text .= '</a></div>';

        }

        $this->content->footer = '';

        return $this->content;
    }
}

?>
