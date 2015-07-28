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
 * Planet eStream Repository Plugin
 *
 * @since 2.0
 * @package    repository_planetestream
 * @copyright  2012 Planet eStream
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */
require_once($CFG->dirroot . '/config.php');
require_once($CFG->dirroot . '/repository/lib.php');
defined('MOODLE_INTERNAL') || die ();
class repository_planetestream extends repository {
    public function __construct($repositoryid, $context = SYSCONTEXTID, $options = array()) {
        parent::__construct($repositoryid, $context, $options);
    }
    public function check_login() {
        return !empty($this->keyword);
    }
    public function search($searchtext, $page = 0) {
        global $SESSION;
        $sesskeyword = 'planetestream_' . $this->id . '_keyword';
        $sesssort = 'planetestream_' . $this->id . '_sort';
        $sesscat = 'planetestream_' . $this->id . '_category';
        $sessshow = 'planetestream_' . $this->id . '_mediatype';
        $sesschapters = 'planetestream_' . $this->id . '_chapters';
        $sort = optional_param('planetestream_sort', null, PARAM_TEXT);
        $cat = optional_param('planetestream_cat', null, PARAM_TEXT);
        $show = optional_param('planetestream_show', null, PARAM_TEXT);
        $chapters = '';
        if (isset ( $_POST ['planetestream_chapters'] )) {
            $chapters = 'on';
        }
        if ($page && !$searchtext && isset ($SESSION->{$sesskeyword})) {
            $searchtext = $SESSION->{$sesskeyword};
        }
        if ($page && !$sort && isset ($SESSION->{$sesssort})) {
            $sort = $SESSION->{$sesssort};
        }
        if (! $sort) {
            $sort = 'relevance'; // ... default ...
        }
        if ($page && !$cat && isset ( $SESSION->{$sesscat})) {
            $cat = $SESSION->{$sesscat};
        }
        if ($page && !$show && isset ( $SESSION->{$sessshow})) {
            $show = $SESSION->{$sessshow};
        }
        if ($page && !$chapters && isset ( $SESSION->{$sesschapters})) {
            $chapters = $SESSION->{$sesschapters};
        }
        $SESSION->{$sesskeyword} = $searchtext;
        $SESSION->{$sesssort} = $sort;
        $SESSION->{$sesscat} = (string) $cat;
        $SESSION->{$sessshow} = (string) $show;
        $SESSION->{$sesschapters} = (string) $chapters;
        $ret = array ();
        $ret['nologin'] = true;
        $ret['page'] = ( int ) $page;
        if ($ret['page'] < 1) {
            $ret['page'] = 1;
        }
        if ($searchtext == '') {
            $searchtext = '*';
        }
        $ret ['list'] = $this->funcgetlist($searchtext, $ret['page'] - 1, $sort, $cat, $show, $chapters);
        $ret['norefresh'] = true;
        $ret['nosearch'] = true;
        $ret['pages'] = - 1;
        if (count ($ret ['list']) == 1) { // ... !(<10) ...
            $ret['pages'] = 0;
            $ret['page'] = 0;
        }
        return $ret;
    }
    private function funcgetlist($keyword, $pageindex, $sort, $cat, $show, $chapters) {
        global $USER;
        global $SESSION;
        $list = array();
        $this->feed_url = $this->get_url () . '/VLE/Moodle/Default.aspx?search=' . urlencode ($keyword)
        . '&format=5&pageindex=' . $pageindex . '&orderby=' . $sort . '&cat=' . $cat . '&show=' . $show
        . '&delta=' . $this->funcobfuscate ($USER->username) . '&checksum=' . $this->funcgetchecksum()
        . '&mc=' . $chapters;
        $c = new curl(array (
            'cache' => false,
            'module_cache' => false
        ));
        $content = $c->get($this->feed_url);
        $xml = simplexml_load_string($content);
        foreach ($xml->item as $item) {
            $title = (string) $item->title;
            $description = (string) $item->description;
            $description = str_replace ('[ No Description ]', '', $description);
            $source = (string) $this->get_url () . '/VLE/Moodle/Video/' . $item->file . '.swf';
            $recordtype = (string) 'Recording';
            $tumbnailurl = (string) $this->get_url () . '/GetImage.aspx';
            if ($item->recordtype == '2') {
                $recordtype = 'Playlist';
            } else if ($item->recordtype == '4') {
                $recordtype = 'Photoset';
            } else if ($item->recordtype == '-99') {
                $recordtype = 'Chapter';
            }
            $shorttitle = (string) '';
            $thumbnailcontainerheight = (string) '190';
            $idparts = explode ( '~', $item->file );
            if (count($idparts) == 4) {
                $thumbnailcontainerheight = (string) '90';
                $tumbnailurl .= '?type=chap&width=120&height=90&id=' . $idparts [3];
                $shorttitle = $title . ' (Chapter) ' . $description;
            } else {
                $tumbnailurl .= '?type=cd&width=354&height=190&forceoverlay=true&source=moodle&id='
                .$idparts [0] . '~' . $idparts [1];
                $shorttitle = $recordtype . ' ' . get_string ('addedon', 'repository_planetestream')
                . ' ' . date('d/m/Y', (integer) $item->addedat) . ' '
                . get_string ('addedby', 'repository_planetestream')
                . ' ' . $item->addedby . ' ' . $title . ' ' . $description;
            }
            $dimensions = (string) $SESSION->{'planetestream_' . $this->id . '_dimensions'};
            $list [] = array (
                'shorttitle' => $shorttitle,
                'thumbnail_title' => $title,
                'title' => $title . '.m4v',
                'thumbnail' => $tumbnailurl,
                'thumbnail_width' => '600',
                'thumbnail_height' => $thumbnailcontainerheight,
                'license' => 'Other',
                'size' => '',
                'date' => '',
                'lastmodified' => '',
                'datecreated' => $item->addedat,
                'author' => $item->addedby,
                'dimensions' => str_replace ('?d=', '', $dimensions),
                'source' => $source . $dimensions
            );
        }
        return $list;
    }
    /**
     * the planetestream plugin does not support global search
     */
    public function global_search() {
        return false;
    }
    private function funcgetchecksum() {
        $decchecksum = (float) (date('d') + date('m')) + (date('m') * date('d')) + (date('Y') * date('d'));
        $decchecksum += $decchecksum * (date('d') * 2.27409) * .689274;
        return md5(floor($decchecksum));
    }
    private function funcobfuscate($strx) {
        $strbase64chars = '0123456789aAbBcCDdEeFfgGHhiIJjKklLmMNnoOpPQqRrsSTtuUvVwWXxyYZz/+=';
        $strbase64string = base64_encode ( $strx );
        if ($strbase64string == '') {
            return '';
        }
        $strobfuscated = '';
        for ($i = 0; $i < strlen($strbase64string); $i++) {
            $intpos = strpos($strbase64chars, substr ($strbase64string, $i, 1));
            if ($intpos == - 1) {
                return '';
            }
            $intpos += strlen($strbase64string) + $i;
            $intpos = $intpos % strlen($strbase64chars);
            $strobfuscated .= substr($strbase64chars, $intpos, 1);
        }
        return $strobfuscated;
    }
    /**
     * get_listing..
     *
     * @param string $path        	
     * @param int $page        	
     * @return array
     */
    public function get_listing($path = '', $page = '') {
        return array ();
    }
    /**
     * Generate search form
     */
    public function print_login($ajax = true) {
        global $USER;
        global $SESSION;
        $ret = array ();
        $help = new stdClass();
        $help->type = 'hidden';
        $help->label = '<div style="position: relative; min-width: 100%; height: 24px; '
        .'font-size: 12px; white-space: nowrap;"><div style="float: left; padding-right: 7px;">'
        .'[&nbsp;<a href="#" id="planetestream_addandembed" title="add (upload) and embed a new'
        .'media file" style="text-decoration: underline" onclick="alert(\'An upload window will'
        .'now open.\n\nAfter the upload has completed, click the Search button to find, select,'
        .'and insert the uploaded item.\'); var dtX=new Date(); document.getElementById(\'plane'
        .'testream_search\').value=\'mi\' + dtX.getTime(); window.open(\'' . $this->get_url()
        . '/UploadContentVLE.aspx?sourceID=11&uid=mi\' + dtX.getTime(), \'add\', \'width=720,he'
        .'ight=680,left=100,top=100,scrollbars=yes\'); document.getElementById(\'planetestream_'
        .'search\').setAttribute(\'disabled\', \'true\');document.getElementById(\'planetestrea'
        .'m_show\').selectedIndex=0;document.getElementById(\'planetestream_show\').setAttribut'
        .'e(\'disabled\', \'true\');document.getElementById(\'planetestream_sort\').selectedInd'
        .'ex=0;document.getElementById(\'planetestream_sort\').setAttribute(\'disabled\', \'tru'
        .'e\');document.getElementById(\'planetestream_cat\').selectedIndex=0;document.getEleme'
        .'ntById(\'planetestream_cat\').setAttribute(\'disabled\', \'true\');document.getElemen'
        .'tById(\'planetestream_chapters\').checked=false;document.getElementById(\'planetestre'
        .'am_chapters\').setAttribute(\'disabled\', \'\'); return false;">add &amp; embed</a>&n'
        .'bsp;]</div><div style="float: left; padding-right: 7px;">[&nbsp;<a href="#" title="ad'
        .'d (upload) a new media file" style="text-decoration: underline" onclick="window.open(\''
        . $this->get_url () . '/UploadContentVLE.aspx?sourceID=11\', \'add\', \'width=720,heigh'
        .'t=680,left=100,top=100,scrollbars=yes\'); return false;">add</a>&nbsp;]</div><div sty'
        .'le="float: left;">    [&nbsp;<a href="#" title="view help" style="text-decoration: un'
        .'derline" onclick="window.open(\'' . $this->get_url () . '/VLE/Moodle/Help.aspx\'); re'
        .'turn false;">help</a>&nbsp;]</div></div>';
        $search = new stdClass ();
        $search->type = 'text';
        $search->id = 'planetestream_search';
        $search->name = 's'; // ... tic ...
        $search->label = get_string('search', 'repository_planetestream') . ': ';
        $show = new stdClass();
        $show->type = 'select';
        $show->options = array (
            (object) array (
                'value' => '0',
                'label' => get_string('show_all', 'repository_planetestream')
            ), (object) array (
                'value' => '7',
                'label' => get_string('show_video', 'repository_planetestream')
            ), (object) array (
                'value' => '2',
                'label' => get_string('show_playlist', 'repository_planetestream')
            ), (object) array (
                'value' => '4',
                'label' => get_string('show_photoset', 'repository_planetestream')
            )
        );
        $show->id = 'planetestream_show';
        $show->name = 'planetestream_show';
        $show->label = get_string('show', 'repository_planetestream') . ': ';
        $sort = new stdClass ();
        $sort->type = 'select';
        $sort->options = array (
            (object) array (
                'value' => '0',
                'label' => get_string('sort_orderby_relevance', 'repository_planetestream')
             ), (object) array (
                'value' => '8',
                'label' => get_string('sort_orderby_date', 'repository_planetestream')
            ), (object) array (
                'value' => '7',
                'label' => get_string('sort_orderby_rating', 'repository_planetestream')
            ), (object) array (
                'value' => '3',
                'label' => get_string('sort_orderby_popularity', 'repository_planetestream')
            )
        );
        $sort->id = 'planetestream_sort';
        $sort->name = 'planetestream_sort';
        $sort->label = get_string('sort_orderby', 'repository_planetestream' ) . ': ';
        $category = new stdClass();
        $category->type = 'select';
        $url = (string)$this->get_url () . '/VLE/Moodle/Default.aspx?show=info&delta='
        . $this->funcobfuscate($USER->username) . '&checksum=' . $this->funcgetchecksum();
        $c = new curl(array(
            'cache' => false,
            'module_cache' => false
        ));
        curl_setopt($c, CURLOPT_CONNECTTIMEOUT, 6);
        curl_setopt($c, CURLOPT_TIMEOUT, 12);
        $content = $c->get ($url);
        $xml = simplexml_load_string($content);
        $cats = array();
        $cats [] = array (
            'value' => '0',
            'label' => 'All'
        );
        foreach ($xml->cats->cat as $catitem) {
            $cats [] = array (
                'value' => (string) $catitem->id,
                'label' => (string) $catitem->name
            );
        }
        if ($xml->catname [0] != '') {
            $category->label = $xml->catname [0] . ':';
        } else {
            $category->label = 'Category:';
        }
        $category->id = 'planetestream_cat';
        $category->name = 'planetestream_cat';
        $category->options = $cats;
        $chapters = new stdClass ();
        $chapters->type = 'checkbox';
        $chapters->id = 'planetestream_chapters';
        $chapters->name = 'planetestream_chapters';
        $chapters->label = get_string('sort_includechapters', 'repository_planetestream' ) . ': ';
        $ret ['login'] = array (
            $help,
            $search,
            $show,
            $sort,
            $category,
            $chapters
        );
        $ret ['login_btn_label'] = get_string('search');
        $ret ['login_btn_action'] = 'search';
        $ret ['allowcaching'] = false;
        $strdimensions = $xml->dimensions[0];
        if ($strdimensions != '') {
            $SESSION->{'planetestream_' . $this->id . '_dimensions'} = (string) '?d=' . $strdimensions;
        }
        return $ret;
    }
    /**
     * file types supported by planetestream plugin
     *
     * @return array
     */
    public function supported_filetypes() {
        return array (
            'video'
        );
    }
    /**
     * Gets the names of the repository config options as an array
     *
     * @return array The array of config option names
     */
    public static function get_type_option_names() {
        return array (
            'url',
            'pluginname'
        );
    }
    /**
     * Edit/Create Admin Settings Moodle form
     *
     * @param moodleform $mform
     * Moodle form (passed by reference)
     * @param string $classname
     * repository class name
     */
    public static function type_config_form($mform, $classname = 'repository') {
        parent::type_config_form ( $mform, $classname );
        $mform->addElement ('text', 'url', get_string ('settingsurl', 'repository_planetestream'));
        $mform->setType('url', PARAM_RAW);
        $mform->addRule('url', get_string('required'), 'required', null, 'client');
        $mform->addElement ('static', null, '', get_string('settingsurl_text', 'repository_planetestream'));
        $mform->addElement ('static', null, '', '<p>&nbsp;</p><p>Please note: The remainder of the configuration'
        .'options can be found on your Planet eStream Website Administration Console, within the <span style="fon'
        .'t-style: italic">VLE Integration section.</span></p>');
    }
    /**
     * planetestream plugin only return external links
     *
     * @return int
     */
    public function supported_returntypes() {
        return FILE_EXTERNAL;
    }
    private function get_url() {
        $url = (string) get_config('planetestream', 'url');
        $intpos = (int) strpos($url, '://');
        if ($intpos != 0) {
            $intpos = strpos($url, '/', $intpos + 3);
            if ($intpos != 0) {
                $url = substr($url, 0, $intpos);
            }
        }
        return $url;
    }
}
