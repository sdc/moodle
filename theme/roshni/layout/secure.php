<?php

// Get the HTML for the settings bits.
$html = theme_roshni_get_html_for_settings($OUTPUT, $PAGE);
$favicon = get_config("theme_roshni","faviconimg");
$logosettings = get_config("theme_roshni", "logostyle");
$logosetting = json_decode($logosettings, true);
if ($favicon != "") {
    $favicondetails = trim(stripcslashes($favicon),'"');
} else {
    $favicondetails = $CFG->wwwroot . '/theme/roshni/favicon.ico';
}
// Set default (LTR) layout mark-up for a three column page.
$regionmainbox = 'span9';
$regionmain = 'span8 pull-right';
$sidepre = 'span4 desktop-first-column';
$sidepost = 'span3 pull-right';
// Reset layout mark-up for RTL languages.
if (right_to_left()) {
    $regionmainbox = 'span9 pull-right';
    $regionmain = 'span8';
    $sidepre = 'span4 pull-right';
    $sidepost = 'span3 desktop-first-column';
}

echo $OUTPUT->doctype() ?>
<html <?php echo $OUTPUT->htmlattributes(); ?>>
<head>
    <title><?php echo $OUTPUT->page_title(); ?></title>
    <link type="image/x-icon" rel="shortcut icon" href="<?php echo $favicondetails;?>">
    <?php echo $OUTPUT->standard_head_html() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body <?php echo $OUTPUT->body_attributes(); ?>>

<?php echo $OUTPUT->standard_top_of_body_html() ?>

<header role="banner" class="navbar navbar-fixed-top moodle-has-zindex">
    <div class="inner-header">
        <nav role="navigation" class="navbar-inner">
            <div class="container">
                <?php if($logosetting == '"logostyle3"') { ?>
                    <a class="inner-logo" href="<?php echo $CFG->wwwroot;?>"><?php echo
                        format_string($SITE->shortname, true, array('context' => context_course::instance(SITEID)));
                    ?></a>
                <?php } else if($logosetting == '"logostyle2"') { ?>
                    <a class="inner-logo" style = "background: none;" href="<?php echo $CFG->wwwroot;?>"><?php echo
                        format_string($SITE->shortname, true, array('context' => context_course::instance(SITEID)));
                    ?></a>
                <?php } else if($logosetting == '"logostyle1"') { ?>
                    <a href="<?php echo $CFG->wwwroot;?>" class="inner-logo logo-img"></a>
                <?php } else { ?>
                    <a class="inner-logo logo-text" href="<?php echo $CFG->wwwroot;?>"></a>
                <?php } ?>
                <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </a>
                <div class="nav-collapse collapse">
                    <ul class="nav pull-right">
                        <li><?php echo $OUTPUT->page_heading_menu(); ?></li>
                    </ul>
                </div>
            </div>
        </nav>
    </div>
</header>

<div id="page" class="container-fluid">

    <header id="page-header" class="clearfix">
        <?php echo $html->heading; ?>
    </header>

    <div id="page-content" class="row-fluid">
        <div id="region-main-box" class="<?php echo $regionmainbox; ?>">
            <div class="row-fluid">
                <section id="region-main" class="<?php echo $regionmain; ?>">
                    <?php echo $OUTPUT->main_content(); ?>
                </section>
                <?php echo $OUTPUT->blocks('side-pre', $sidepre); ?>
            </div>
        </div>
        <?php echo $OUTPUT->blocks('side-post', $sidepost); ?>
    </div>

    <?php echo $OUTPUT->standard_end_of_body_html() ?>

</div>
</body>
</html>