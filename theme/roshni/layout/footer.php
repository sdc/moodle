<footer>
	<div class="container">
		<?php if(isloggedin()) { ?>
		<div id="course-footer"><?php echo $OUTPUT->course_footer(); ?></div>
		<?php
		    echo $html->footnote;
		    echo $OUTPUT->login_info();
		}
		$footer = get_config("theme_roshni","footer");
		$footers = json_decode($footer, true);
		if (empty($footers)) {
		?>
		<p>&copy; 2015 ROSHNI. ALL RIGHTS RESERVED.</p>
		<?php } else { ?>
		<p><?php echo $footers; ?></p>
		<?php } ?>
	</div><!-- END of .container -->
</footer><!-- END of footer -->