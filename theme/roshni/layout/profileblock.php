<?php
function get_content () {
  global $USER, $CFG, $SESSION, $COURSE;
  $wwwroot = '';
  $signup = '';
}

if (empty($CFG->loginhttps)) {
  $wwwroot = $CFG->wwwroot;
} else {
  $wwwroot = str_replace("http://", "https://", $CFG->wwwroot);
}

if (!isloggedin() or isguestuser()) {
  echo '<form class="navbar-form pull-right span8" method="post" action="'.$wwwroot. '/login/index.php?authldap_skipntlmsso=1">
          <input class="span3 inp1 mp3" type="text" placeholder="Email">
          <input class="span3 inp1 mp3" type="password" placeholder="Password">
          <button type="submit" class="btn btn2">Sign in</button>
        </form>';
  } 
?>
