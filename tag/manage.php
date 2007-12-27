<?php // $Id$

require_once('../config.php');
require_once($CFG->libdir.'/tablelib.php');
require_once('lib.php');

define('SHOW_ALL_PAGE_SIZE', 50000);
define('DEFAULT_PAGE_SIZE', 30);

$tagschecked = optional_param('tagschecked', array(), PARAM_INT);
$newnames    = optional_param('newname', array(), PARAM_TAG);
$tagtypes    = optional_param('tagtypes', array(), PARAM_ALPHA);
$action      = optional_param('action', '', PARAM_ALPHA);
$perpage     = optional_param('perpage', DEFAULT_PAGE_SIZE, PARAM_INT);

require_login();

if (empty($CFG->usetags)) {
    error(get_string('tagsaredisabled', 'tag'));
}

//managing tags requires moodle/tag:manage capability
$systemcontext   = get_context_instance(CONTEXT_SYSTEM);
require_capability('moodle/tag:manage', $systemcontext);

$navlinks = array();
$navlinks[] = array('name' => get_string('tags', 'tag'), 'link' => "{$CFG->wwwroot}/tag/search.php", 'type' => '');
$navlinks[] = array('name' => get_string('managetags', 'tag'), 'link' => '', 'type' => '');

$navigation = build_navigation($navlinks);
print_header_simple(get_string('managetags', 'tag'), '', $navigation);

$err_notice = '';
$notice = '';

// get all the possible tag types from db
$existing_tagtypes = array();
if ($ptypes = get_records_sql("SELECT DISTINCT(tagtype), id FROM {$CFG->prefix}tag")) {
    foreach ($ptypes as $ptype) {
        $existing_tagtypes[$ptype->tagtype] = $ptype->tagtype;
    }
}
$existing_tagtypes['official'] = get_string('tagtype_official', 'tag');
$existing_tagtypes['default'] = get_string('tagtype_default', 'tag');

switch($action) {

    case 'delete':
        if (!data_submitted() or !confirm_sesskey()) {
            break;
        }
        $str_tagschecked = tag_name_from_string(implode($tagschecked, ','));
        $str_tagschecked = str_replace(',', ', ', $str_tagschecked);

        tag_delete(implode($tagschecked, ','));

        $notice = $str_tagschecked.' --  '.get_string('deleted','tag');
        break;

    case 'reset':
        if (!data_submitted() or !confirm_sesskey()) {
            break;
        }
        $str_tagschecked = tag_name_from_string(implode($tagschecked, ','));
        $str_tagschecked = str_replace(',', ', ', $str_tagschecked);

        tag_flag_reset(implode($tagschecked, ','));

        $notice = $str_tagschecked.' -- '.get_string('reset','tag');
        break;

    case 'changetype':
        if (!data_submitted() or !confirm_sesskey()) {
            break;
        }

        $changed = array();

        foreach ($tagschecked as $tag_id) {
            if (!array_key_exists($tagtypes[$tag_id], $existing_tagtypes)) {
                //can not add new types here!!
                continue;
            }

            // update tag type;
            $tag = tag_by_id($tag_id);
            $tag->timemodified = time();
            $tag->tagtype = $tagtypes[$tag_id];

            if (update_record('tag', $tag)) {
                $changed[] = $tag_id;
            }
        }

        if ($changed) {
            $str_changed = tag_name_from_string(implode($changed, ','));
            $str_changed = str_replace(',', ', ', $str_changed);
            $notice = $str_changed.' --  '.get_string('typechanged','tag');
        }
        break;

    case 'changename':
        if (!data_submitted() or !confirm_sesskey()) {
            break;
        }

        $tags_names_changed = array();

        foreach ($tagschecked as $tag_id) {
            if ($newnames[$tag_id] != '') {
                if (tag_exists($newnames[$tag_id])) {
                    $err_notice .= $newnames[$tag_id]. '-- ' . get_string('namesalreadybeeingused','tag').'<br />';
                } else {
                    $tags_names_changed[$tag_id] = $newnames[$tag_id];
                }
            }
        }

        $tags_names_updated = tag_update_name($tags_names_changed);

        //notice to inform what tags had their names effectively updated
        if ($tags_names_updated){
            $notice = implode($tags_names_updated, ', ');
            $notice .= ' -- ' . get_string('updated','tag');
        }
        break;
}

echo '<br/>';

if ($err_notice) {
    notify($err_notice, 'red');
}
if ($notice) {
    notify($notice , 'green');
}

//setup table

$tablecolumns = array('id','name', 'fullname', 'count', 'flag', 'timemodified', 'rawname', 'tagtype', '');
$tableheaders = array(get_string('id' , 'tag'),
                      get_string('name' , 'tag'),
                      get_string('owner','tag'),
                      get_string('count','tag'),
                      get_string('flag','tag'),
                      get_string('timemodified','tag'),
                      get_string('newname', 'tag'),
                      get_string('tagtype', 'tag'),
                      get_string('select', 'tag'));

$table = new flexible_table('tag-management-list-'.$USER->id);

$baseurl = $CFG->wwwroot.'/tag/manage.php?perpage='.$perpage;

$table->define_columns($tablecolumns);
$table->define_headers($tableheaders);
$table->define_baseurl($baseurl);

$table->sortable(true, 'flag', SORT_DESC);

$table->set_attribute('cellspacing', '0');
$table->set_attribute('id', 'tag-management-list');
$table->set_attribute('class', 'generaltable generalbox');

$table->set_control_variables(array(
TABLE_VAR_SORT    => 'ssort',
TABLE_VAR_HIDE    => 'shide',
TABLE_VAR_SHOW    => 'sshow',
TABLE_VAR_IFIRST  => 'sifirst',
TABLE_VAR_ILAST   => 'silast',
TABLE_VAR_PAGE    => 'spage'
));

$table->setup();

if ($table->get_sql_sort()) {
    $sort = ' ORDER BY '.$table->get_sql_sort();
} else {
    $sort = '';
}

if ($table->get_sql_where()) {
    $where = 'WHERE '.$table->get_sql_where();
} else {
    $where = '';
}

$query = "
    SELECT tg.id, tg.name, tg.rawname, tg.tagtype, COUNT(ti.id) AS count, u.id AS owner, tg.flag, tg.timemodified, u.firstname, u.lastname
      FROM {$CFG->prefix}tag_instance ti
           RIGHT JOIN {$CFG->prefix}tag tg ON tg.id = ti.tagid
           LEFT JOIN {$CFG->prefix}user u ON tg.userid = u.id
  {$where}
  GROUP BY tg.id, tg.name, tg.rawname, tg.tagtype, u.id, tg.flag, tg.timemodified, u.firstname, u.lastname
   {$sort}";


$totalcount = count_records_sql("SELECT COUNT(DISTINCT(tg.id))
                                   FROM {$CFG->prefix}tag tg
                                        LEFT JOIN {$CFG->prefix}user u ON u.id = tg.userid
                                 $where");

$table->initialbars(true); // always initial bars
$table->pagesize($perpage, $totalcount);

echo '<form id="tag-management-form" method="post" action="'.$CFG->wwwroot.'/tag/manage.php"><div>';

//retrieve tags from DB
if ($tagrecords = get_records_sql($query, $table->get_page_start(),  $table->get_page_size())) {

    $taglist = array_values($tagrecords);

    //print_tag_cloud(array_values(get_records_sql($query)), false);

    //populate table with data
    foreach ($taglist as $tag ){

        $id             =   $tag->id;
        $name           =   '<a href="'.$CFG->wwwroot.'/tag/index.php?id='.$tag->id.'">'. tag_display_name($tag) .'</a>';
        $owner          =   '<a href="'.$CFG->wwwroot.'/user/view.php?id='.$tag->owner.'">' . fullname($tag) . '</a>';
        $count          =   $tag->count;
        $flag           =   $tag->flag;
        $timemodified   =   format_time(time() - $tag->timemodified);
        $checkbox       =   '<input type="checkbox" name="tagschecked[]" value="'.$tag->id.'" />';
        $text           =   '<input type="text" name="newname['.$tag->id.']" />';

        $tagtype        =   choose_from_menu($existing_tagtypes, 'tagtypes['.$tag->id.']', $tag->tagtype, '', '', '0', true);

        //if the tag if flagged, highlight it
        if ($tag->flag > 0) {
            $id = '<span class="flagged-tag">' . $id . '</span>';
            $name = '<span class="flagged-tag">' . $name . '</span>';
            $owner = '<span class="flagged-tag">' . $owner . '</span>';
            $count = '<span class="flagged-tag">' . $count . '</span>';
            $flag = '<span class="flagged-tag">' . $flag . '</span>';
            $timemodified = '<span class="flagged-tag">' . $timemodified . '</span>';
            $tagtype = '<span class="flagged-tag">'. $tagtype. '</span>';
        }

        $data = array($id, $name, $owner, $count, $flag, $timemodified, $text, $tagtype, $checkbox);

        $table->add_data($data);
    }


    echo '<input type="button" onclick="checkall()" value="'.get_string('selectall').'" /> ';
    echo '<input type="button" onclick="checknone()" value="'.get_string('deselectall').'" /> ';
    echo '<input type="hidden" name="sesskey" value="'.sesskey().'" /> ';
    echo '<br/><br/>';
    echo '<select id="menuformaction" name="action">
                <option value="" selected="selected">'. get_string('withselectedtags', 'tag') .'</option>
                <option value="reset">'. get_string('resetflag', 'tag') .'</option>
                <option value="delete">'. get_string('delete', 'tag') .'</option>
                <option value="changetype">'. get_string('changetype', 'tag') .'</option>
                <option value="changename">'. get_string('changename', 'tag') .'</option>
            </select>';

    echo '<button id="tag-management-submit" type="submit">'. get_string('ok') .'</button>';

}

$table->print_html();

echo '</div></form>';

if ($perpage == SHOW_ALL_PAGE_SIZE) {
    echo '<div id="showall"><a href="'.$baseurl.'&amp;perpage='.DEFAULT_PAGE_SIZE.'">'.get_string('showperpage', '', DEFAULT_PAGE_SIZE).'</a></div>';

} else if ($totalcount > 0 and $perpage < $totalcount) {
    echo '<div id="showall"><a href="'.$baseurl.'&amp;perpage='.SHOW_ALL_PAGE_SIZE.'">'.get_string('showall', '', $totalcount).'</a></div>';
}

echo '<br/>';

print_footer();

?>
