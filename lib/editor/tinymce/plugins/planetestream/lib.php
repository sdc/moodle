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
 * tinymce text editor integration version file.
 *
 * @package    tinymce_planetestream
 * @copyright  Planet Enterprises Ltd
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tinymce_planetestream extends editor_tinymce_plugin {
    /** @var array list of buttons defined by this plugin */
    protected $buttons = array('planetestream');
    /**
     * Initialise Parameters
     */
    protected function update_init_params(array &$params, context $context,
    array $options = null) {
        global $PAGE, $USER, $CFG;
        if ($row = $this->find_button($params, 'moodlemedia')) {
            // Add button after 'moodlemedia'.
            $this->add_button_after($params, $row, 'planetestream', 'moodlemedia');
        } else if ($row = $this->find_button($params, 'image')) {
            // Add button after 'image'.
            $this->add_button_after($params, $row, 'planetestream', 'image');
        } else {
            // Add button in the end of the first row.
            $this->add_button_after($params, 1, 'planetestream');
        }
        // Define parameters.
        $url = rtrim(get_config('assignsubmission_estream', 'url') , '/');
        if (empty($url)) {
            $url = rtrim(get_config('planetestream', 'url') , '/');
        }
        $params['estream_url'] = $url;
        $checksum = $this->tinymce_planetestream_getchecksum();
        $delta = $this->tinymce_planetestream_obfuscate($USER->username);
        $userip = $this->tinymce_planetestream_obfuscate(getremoteaddr());
        $authticket = $this->tinymce_planetestream_getauthticket($url, $checksum, $delta, $userip, $params);
        if ($authticket == '') {
            $params['disabled'] = true;
        }
        $path = '/VLE/Moodle/Default.aspx?delta=' . $delta . '&checksum=' . $checksum
        . '&ticket=' . $authticket . '&inlinemode=moodle';
        $path .= '&mpu=' . ((string)$PAGE->pagetype == 'mod-assign-view' ? "true" : "false");
        $params['estream_path'] = $path;
        $params['base_path'] = $CFG->httpswwwroot;
        // Add JS file, which uses default name.
        $this->add_js_plugin($params);
    }
    /**
     * Returns the order in which to run this plugin.
     */
    public function get_sort_order() {
        return 140;
    }
    /**
     * Get Planet eStream checksum
     * @return string
     *
     */
    private function tinymce_planetestream_getchecksum() {
        $decchecksum = (float)(date('d') + date('m')) + (date('m') * date('d')) + (date('Y') * date('d'));
        $decchecksum += $decchecksum * (date('d') * 2.27409) * .689274;
        return md5(floor($decchecksum));
    }
    /**
     * Obfuscate Strings
     * @return string
     *
     */
    private function tinymce_planetestream_obfuscate($strx) {
        $strbase64chars = '0123456789aAbBcCDdEeFfgGHhiIJjKklLmMNnoOpPQqRrsSTtuUvVwWXxyYZz/+=';
        $strbase64string = base64_encode($strx);
        if ($strbase64string == '') {
            return '';
        }
        $strobfuscated = '';
        for ($i = 0; $i < strlen ($strbase64string); $i ++) {
            $intpos = strpos($strbase64chars, substr($strbase64string, $i, 1));
            if ($intpos == - 1) {
                return '';
            }
            $intpos += strlen($strbase64string ) + $i;
            $intpos = $intpos % strlen($strbase64chars);
            $strobfuscated .= substr($strbase64chars, $intpos, 1);
        }
        return urlencode($strobfuscated);
    }
    /**
     * Get Planet eStream Auth Ticket
     * @return string
     *
     */
    private function tinymce_planetestream_getauthticket($url, $checksum, $delta, $userip, &$params) {
        $return = '';
        try {
            $url .= '/VLE/Moodle/Auth/?source=1&checksum=' . $checksum . '&delta=' . $delta . '&u=' . $userip;
            if (!$curl = curl_init($url)) {
                return '';
            }
            curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 15);
            curl_setopt($curl, CURLOPT_TIMEOUT, 15);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($curl, CURLOPT_MAXREDIRS, 4);
            curl_setopt($curl, CURLOPT_FORBID_REUSE, true);
            $response = curl_exec($curl);
            if (strpos($response, '{"ticket":') === 0) {
                $jobj = json_decode($response);
                $return = $jobj->ticket;
                $params['estream_height'] = $jobj->height;
                $params['estream_width'] = $jobj->width;
            }
        } catch (Exception $e) {
            // ... non-fatal ...
        }
        return $return;
    }
}
