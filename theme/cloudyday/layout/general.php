<?php
    <link href='http://fonts.googleapis.com/css?family=Droid+Sans' rel='stylesheet' type='text/css'>
    <link href='http://fonts.googleapis.com/css?family=Droid+Serif' rel='stylesheet' type='text/css'>
	<?php include('profileblock.php')?>
	<div class="clear"></div>
</div>
<div id="page_menu">
	<div id="page_right_menu">
		<div id="cal_link"><a href="<?php echo $CFG->wwwroot; ?>/calendar/view.php">Calendar</a></div>
	</div>
	<div class="clear"></div>
</div>
<div id="page_outercontent">
		<div id="ebutton"><?php if ($hasnavbar) { echo $PAGE->button; } ?></div>	
    		<?php echo $OUTPUT->navbar($PAGE->navbar);  ?>
    		
                    		<?php include('menunav.php')?>