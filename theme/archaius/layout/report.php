<?php include 'partials/header.php'; ?>
<body id="<?php p($PAGE->bodyid) ?>" class="<?php p($PAGE->bodyclasses.' '.join(' ', $bodyclasses)) ?>">
<?php echo $OUTPUT->standard_top_of_body_html() ?>
<?php include 'partials/page_header.php'; ?>
<div id="regions-control"></div>
<div id="page" class="main-content clearfix">
    <?php if ($hasnavbar) { ?>
        <div class="navbar clearfix">
            <nav class="breadcrumb"><?php echo $OUTPUT->navbar(); ?></nav>
            <div class="navbutton"><?php echo $PAGE->button; ?></div>
        </div>
    <?php }?>
    <div class="page-content report-page">
        <div class="main-report-content">
            <?php echo $OUTPUT->main_content() ?>
        </div>
        <?php if ($hassidepre) { ?>
            <div id="report-region-pre" class="block-region">
                <div class="region-content">
                    <?php echo $OUTPUT->blocks_for_region('side-pre') ?>
                </div>
            </div>
        <?php } ?>
    </div>
</div>
<?php include 'partials/footer.php' ?>
<script type = "text/javascript">
    //<![CDATA[   
    <?php if (!empty($PAGE->theme->settings->customjs)) {
        echo $PAGE->theme->settings->customjs;
    } ?>
    //]]>
</script>

<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-1183265-2', 'auto');
  ga('send', 'pageview');
</script>

</body>
</html>
