<?php
$hasheading = ($PAGE->heading);
$hasnavbar = (empty($PAGE->layout_options['nonavbar']) && $PAGE->has_navbar());
$hasfooter = (empty($PAGE->layout_options['nofooter']));
$hassidepre = $PAGE->blocks->region_has_content('side-pre', $OUTPUT);
$hassidepost = $PAGE->blocks->region_has_content('side-post', $OUTPUT);
$showsidepre = $hassidepre && !$PAGE->blocks->region_completely_docked('side-pre', $OUTPUT);
$showsidepost = $hassidepost && !$PAGE->blocks->region_completely_docked('side-post', $OUTPUT);

$custommenu = $OUTPUT->custom_menu();
$hascustommenu = (empty($PAGE->layout_options['nocustommenu']) && !empty($custommenu));
$haslogo = (!empty($PAGE->theme->settings->logo));

$bodyclasses = array();
if ($showsidepre && !$showsidepost) {
    $bodyclasses[] = 'side-pre-only';
} else if ($showsidepost && !$showsidepre) {
    $bodyclasses[] = 'side-post-only';
} else if (!$showsidepost && !$showsidepre) {
    $bodyclasses[] = 'content-only';
}
if ($hascustommenu) {
    $bodyclasses[] = 'has_custom_menu';
}

echo $OUTPUT->doctype() ?>
<html <?php echo $OUTPUT->htmlattributes() ?>>
<head>
    <title><?php echo $PAGE->title ?></title>
    <link rel="shortcut icon" href="<?php echo $OUTPUT->pix_url('favicon', 'theme')?>" />
    <?php echo $OUTPUT->standard_head_html() ?>

<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-1183265-27']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>

</head>
<body id="<?php p($PAGE->bodyid) ?>" class="<?php p($PAGE->bodyclasses.' '.join(' ', $bodyclasses)) ?>">
<?php echo $OUTPUT->standard_top_of_body_html() ?>

<div id="page">
<?php
/* Code to choose a random background image (actually a random class) */
$bg_themes = array('beauty','graphics','hair','hair2','happy','happy2','meerkats',
    'pawsbeauty','pawsbeauty2','pawsstudents','phone','pony','science','service','studio');
$bg_theme = $bg_themes[rand(0, count($bg_themes)-1)];
?>
<div id="graphicwrap" class="<?php echo $bg_theme; ?>">

<div id="headerwrap">
    <div id="logowrap">

    <?php if ($haslogo) {
        echo html_writer::link(new moodle_url('/'), "<img src='".$PAGE->theme->settings->logo."' alt='logo' id='logo' />");
    } else { ?>
        <img src="<?php echo $OUTPUT->pix_url('logo', 'theme')?>" id="logo">

    <?php } ?>

        </div>
        <?php include('profileblock.php')?>

<div id="menuwrap">
<div id="homeicon">
<a href="<?php echo $CFG->wwwroot; ?>"><img src="<?php echo $OUTPUT->pix_url('menu/home_icon', 'theme')?>"></a>
</div>
    <?php
    if ($hascustommenu) { ?>
                    <div id="menuitemswrap"><div id="custommenu"><?php echo $custommenu; ?></div></div>
                <?php } ?>
</div>
<div id="jcontrols_button">
                <div class="jcontrolsleft">
                        <?php if ($hasnavbar) { ?>
                            <div class="navbar clearfix">
                                <div class="breadcrumb"> <?php echo $OUTPUT->navbar();  ?></div>

                            </div>
                        <?php } ?>
                        </div>
                        <div id="ebutton">
    <?php if ($hasnavbar) { echo $PAGE->button; } ?>
    </div>


</div>

<div id="contentwrapper">
    <!-- start OF moodle CONTENT -->
                <div id="page-content">
                    <div id="region-main-box">
                        <div id="region-post-box">

                                <div id="region-main-wrap">
                                    <div id="region-main">
                                        <div class="region-content">
                                        <div id="mainpadder">
                                        <?php echo core_renderer::MAIN_CONTENT_TOKEN ?>
                                        </div>
                                        </div>
                                    </div>
                                </div>

                    <?php if ($hassidepre) { ?>
                    <div id="region-pre" class="block-region">
                        <div class="region-content">


                            <?php echo $OUTPUT->blocks_for_region('side-pre') ?>
                        </div>
                    </div>
                    <?php } ?>

                    <?php if ($hassidepost) { ?>
                    <div id="region-post" class="block-region">
                        <div class="region-content">

                            <?php echo $OUTPUT->blocks_for_region('side-post') ?>
                        </div>
                    </div>
                    <?php } ?>

                        </div>
                    </div>
                 </div>
    <!-- END OF CONTENT -->
</div>

<br style="clear: both;">
 <?php if ($hasfooter) { ?>
<div id="footerwrapper">
<div id="footerinner">
            <?php
            echo $OUTPUT->login_info();
            echo $OUTPUT->standard_footer_html();
            ?> <div>Original theme created by Shaun Daubney</div>
                                <?php echo $PAGE->theme->settings->footnote; ?>


</div>
</div>

 <?php } ?>
 </div>
 </div>

<?php echo $OUTPUT->standard_end_of_body_html() ?>
</body>
</html>