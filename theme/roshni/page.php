<?php
    include("../../config.php");

    global $OUTPUT,$CFG;
    
    $PAGE->set_context(context_system::instance());
    $PAGE->set_pagelayout('course');
    
    
    $PAGE->set_title(str_replace("_", " ", $_GET["page"]));
    $PAGE->set_url($CFG->wwwroot . "/theme/roshni/page.php");
    $PAGE->navbar->add($_GET["page"]);
    $favicon = get_config("theme_roshni","faviconimg");
    if ($favicon != '""') {
        $favicondetails = $favicon;
    } else {
        $favicondetails = $CFG->wwwroot . '/theme/' . $CFG->theme . '/favicon.ico';
    }
    ?>
    <?php
    echo $OUTPUT->header();
    $pluginname = 'theme_roshni';
    $dynamicpage = 'dynamic_page';
    $dynamicpages = $DB->get_record_sql('select config.value from {config_plugins} config where config.plugin="'.$pluginname.'" and config.name="'.$dynamicpage.'"');
    if($dynamicpages) {
        $dynamicpagedetails = json_decode($dynamicpages->value,true);
    } else {
        $dynamicpagedetails = '';
    }
    
    if(!empty($dynamicpagedetails)) {
       
        $dynamicpageArray = $dynamicpageArray1 = $dynamicpageArray2 = array();
        $dynamicpageArray1 = $dynamicpagedetails['text'];
        $dynamicpageArray2 = $dynamicpagedetails['textarea'];
        for ($i=0; $i<count($dynamicpageArray1); $i++) {
            $dynamicpageArray[$dynamicpageArray1[$i]] = $dynamicpageArray2[$i];
        }
    }
    foreach ($dynamicpageArray as $key => $dynamicpageArrayvalue) {

        if(str_replace(" ", "_", $key) == $_GET["page"]) { ?>
            <div class="custom-article">
            <?php echo $dynamicpageArrayvalue; ?>
            </div>
        <?php
        }
    }
echo $OUTPUT->footer();