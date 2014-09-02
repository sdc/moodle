<?php include 'partials/header.php'; ?>
<body id="<?php p($PAGE->bodyid) ?>" class="<?php p($PAGE->bodyclasses.' '.join(' ', $bodyclasses)) ?>">
<?php echo $OUTPUT->standard_top_of_body_html() ?>
<?php include 'partials/page_header.php'; ?>
<?php include 'partials/page_content.php' ?>
<?php include 'partials/footer.php' ?>
<script type = "text/javascript">
    //<![CDATA[
    <?php if (!empty($PAGE->theme->settings->customjs)) {
        echo $PAGE->theme->settings->customjs;
    } ?>
    //]]>
</script>

<script type="text/javascript" src="http://moodle.southdevon.ac.uk/clickheat/js/clickheat.js"></script>
<script type="text/javascript">
<!--
clickHeatSite = 'mainmoodle';
clickHeatGroup = (document.title == '' ? '-none-' : encodeURIComponent(document.title));
clickHeatServer = 'http://moodle.southdevon.ac.uk/clickheat/click.php';
initClickHeat();
//-->
</script>

</body>
</html>