<div class="profilepic" id="profilepic">
	echo '<div class="profilename" id="profilename">';
	echo '</div>';
	echo '</div>';
	echo '<div class="profileoptions" id="profileoptions">';
	echo '<ul>';
	echo '<li><a href="'.$CFG->wwwroot.'/user/editadvanced.php?id='.$USER->id.'">'.get_string('options', 'theme_cloudyday').'</a></li>';
	if ($CFG->messaging) {
		echo '<li><a href="'.$CFG->wwwroot.'/message">'.get_string('messages', 'theme_cloudyday').'</a></li>';
	}
	echo '<li><a href="'.$CFG->wwwroot.'/login/logout.php">'.get_string('logout').'</a></li>';
	echo '</ul>';
	echo '</div>';