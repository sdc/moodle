<?php  // $Id$
defined('MOODLE_INTERNAL') or die('Direct access to this script is forbidden.');

$window = (!empty($quiz->popup)) ? 'quizpopup' : '_self';
$windowoptions = ($window == '_self') ? '' : "left=0, top=0, height='+window.screen.height+', width='+window.screen.width+', channelmode=yes, fullscreen=yes, scrollbars=yes, resizeable=no, directories=no, toolbar=no, titlebar=no, location=no, status=no, menubar=no";
$buttontext = ($numattempts) ? get_string('reattemptquiz', 'quiz') : get_string('attemptquiznow', 'quiz');
$buttontext = ($unfinished) ? get_string('continueattemptquiz', 'quiz') : $buttontext;
$buttontext = htmlspecialchars($buttontext,ENT_QUOTES);
if (!empty($CFG->usesid) && !isset($_COOKIE[session_name()])) {
    $attempturl=sid_process_url("attempt.php?id=$cm->id");
} else {
    $attempturl="attempt.php?id=$cm->id";
};
 ?>

<script language="javascript" type="text/javascript">
<!--
document.write('<input type="button" value="<?php echo $buttontext ?>" '+
               'onclick="javascript: <?php if ($quiz->timelimit || $quiz->attempts) echo "if (confirm(\\'".addslashes($strconfirmstartattempt)."\\'))"; ?> '+
               'window.open(\'<?php echo $attempturl ?>\', \'<?php echo $window ?>\', \'<?php echo $windowoptions ?>\'); " />');
// -->
</script>
<noscript>
    <strong><?php print_string('noscript', 'quiz'); ?></strong>
</noscript>
