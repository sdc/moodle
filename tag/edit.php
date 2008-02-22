<?php // $Id$

require_once('../config.php');
require_once('lib.php');
require_once('edit_form.php');

require_js(array('yui_dom-event', 'yui_connection', 'yui_animation', 'yui_autocomplete'));

require_login();

if (empty($CFG->usetags)) {
    error(get_string('tagsaredisabled', 'tag'));
}

$tagid   = required_param('id', PARAM_INT);   // user id

$tag     = tag_by_id($tagid);
$tagname = tag_display_name($tag);

//Editing a tag requires moodle/tag:edit capability
$systemcontext   = get_context_instance(CONTEXT_SYSTEM);
require_capability('moodle/tag:edit', $systemcontext);

// set the relatedtags field of the $tag object that will be passed to the form
$tag->relatedtags = tag_names_csv(get_item_tags('tag',$tagid));

if (can_use_html_editor()) {
    $options = new object();
    $options->smiley = false;
    $options->filter = false;

    // convert and remove any XSS
    $tag->description       = format_text($tag->description, $tag->descriptionformat, $options);
    $tag->descriptionformat = FORMAT_HTML;
}

$tagform = new tag_edit_form();
$tagform->set_data($tag);

// if new data has been sent, update the tag record
if ($tagnew = $tagform->get_data()) {

    $tagnew->timemodified = time();

    if (!update_record('tag', $tagnew)) {
        error('Error updating tag record');
    }

    //updated related tags
    update_item_tags('tag', $tagnew->id, $tagnew->relatedtags);

    redirect($CFG->wwwroot.'/tag/index.php?id='.$tagnew->id);
}


$navlinks = array();
$navlinks[] = array('name' => get_string('tags', 'tag'), 'link' => "{$CFG->wwwroot}/tag/search.php", 'type' => '');
$navlinks[] = array('name' => $tagname, 'link' => '', 'type' => '');

$navigation = build_navigation($navlinks);
print_header_simple(get_string('tag', 'tag') . ' - '. $tagname, '', $navigation);

print_heading($tagname, '', 2);

$tagform->display();

if (ajaxenabled()) {
?>

<script type="text/javascript">

// An XHR DataSource
var myServer = "./tag_autocomplete.php";
var myDataSource = new YAHOO.widget.DS_XHR(myServer, ["\n", "\t"]);
myDataSource.responseType = YAHOO.widget.DS_XHR.TYPE_FLAT;
myDataSource.maxCacheEntries = 60;
myDataSource.queryMatchSubset = true;

var myAutoComp = new YAHOO.widget.AutoComplete("id_relatedtags","relatedtags-autocomplete", myDataSource);
myAutoComp.delimChar = ",";
myAutoComp.maxResultsDisplayed = 20;
myAutoComp.minQueryLength = 2;
myAutoComp.allowBrowserAutocomplete = false;
</script>

<?php
}
print_footer();

?>
