<?php  // $Id$
/// This page prints all instances of attendance in a given course
    error_reporting(E_ALL);
    require("../../config.php");
    require("lib.php");

// if form is being submitted from generated form
if (isset($_POST["course"]))  {
  require_login();
/// -----------------------------------------------------------------------------------
/// --------------------SECTION FOR PROCESSING THE FORM ON POST -----------------------
/// -----------------------------------------------------------------------------------
  if (isset($SESSION->modform)) {   // Variables are stored in the session
      $mod = $SESSION->modform;
      unset($SESSION->modform);
  } else {
      $mod = (object)$_POST;
  }

  if (isset($cancel)) {
      if (!empty($SESSION->returnpage)) {
          $return = $SESSION->returnpage;
          unset($SESSION->returnpage);
          redirect($return);
      } else {
          redirect("view.php?id=$mod->course");
      }
  }

  if (!isteacheredit($mod->course)) {
      error("You can't modify this course!");
  }

  $modlib = "lib.php";
  if (file_exists($modlib)) {
      include_once($modlib);
  } else {
      error("This module is missing important code! ($modlib)");
  }

/* // set the information for the new instances
     $attendance->dynsection = !empty($attendance->dynsection) ? 1 : 0;
     $attendance->day = make_timestamp($attendance->theyear,
            $attendance->themonth, $attendance->theday);
     $attendance->name=userdate($attendance->day, get_string("strftimedate"));
     if ($attendance->notes) {
        $attendance->name = $attendance->name . " - " . $attendance->notes;
     }
*/
  $curdate = make_timestamp($mod->startyear, $mod->startmonth, $mod->startday);
  $stopdate = make_timestamp($mod->endyear, $mod->endmonth, $mod->endday);
  $enddate = $curdate + $mod->numsections * 604800;
  if ($curdate > $stopdate) {
    error(get_string("endbeforestart", "attendance"));
    }
  if ($enddate < $curdate) {
    error(get_string("startafterend", "attendance"));
    }
  if ($stopdate > $enddate) {
      // if stop date is after end of course, just move it to end of course
            $stopdate = $enddate;
    }
  while ($curdate <= $stopdate) {
    $mod->day = $curdate;
    $mod->name=userdate($mod->day, get_string("strftimedate"));
      if (isset($mod->notes)) {$mod->name = $mod->name . " - " . $mod->notes;}
    switch(userdate($curdate, "%u")) {
      case 1: if (!empty($mod->mon)) {attendance_add_module($mod);}break;
      case 2: if (!empty($mod->tue)) {attendance_add_module($mod);}break;
      case 3: if (!empty($mod->wed)) {attendance_add_module($mod);}break;
      case 4: if (!empty($mod->thu)) {attendance_add_module($mod);}break;
      case 5: if (!empty($mod->fri)) {attendance_add_module($mod);}break;
      case 6: if (!empty($mod->sat)) {attendance_add_module($mod);}break;
      case 7: if (!empty($mod->sun)) {attendance_add_module($mod);}break;
    } // switch
    $curdate = $curdate + 86400; // add one day to the date
  } // while for days

  if (!empty($SESSION->returnpage)) {
      $return = $SESSION->returnpage;
      unset($SESSION->returnpage);
      redirect($return);
  } else {
      redirect("index.php?id=$mod->course");
  }
  exit;

} else {
/// -----------------------------------------------------------------------------------
/// ------------------ SECTION FOR MAKING THE FORM TO BE POSTED -----------------------
/// -----------------------------------------------------------------------------------

/// @include_once("$CFG->dirroot/mod/attendance/lib.php");
/// error_reporting(E_ALL);

        require_variable($id);
        require_variable($section);

        if (! $course = get_record("course", "id", $id)) {
            error("This course doesn't exist");
        }

        if (! $module = get_record("modules", "name", "attendance")) {
            error("This module type doesn't exist");
        }

        $form->section    = $section;         // The section number itself
        $form->course     = $course->id;
        $form->module     = $module->id;
        $form->modulename = $module->name;
        $form->instance   = "";
        $form->coursemodule = "";
        $form->mode       = "add";

        $sectionname    = get_string("name$course->format");
        $fullmodulename = strtolower(get_string("modulename", $module->name));

        if ($form->section) {
            $heading->what = $fullmodulename;
            $heading->to   = "$sectionname $form->section";
            $pageheading = get_string("addingmultiple", "attendance");
        } else {
            $pageheading = get_string("addingmultiple", "attendance");
        }

    if (!isteacheredit($course->id)) {
        error("You can't modify this course!");
    }

    $streditinga = get_string("editinga", "moodle", $fullmodulename);
    $strmodulenameplural = get_string("modulenameplural", $module->name);

    if ($course->category) {
        print_header("$course->shortname: $streditinga", "$course->fullname",
                     "<a href=\"$CFG->wwwroot/course/view.php?id=$course->id\">$course->shortname</a> ->
                      <a href=\"$CFG->wwwroot/mod/$module->name/index.php?id=$course->id\">$strmodulenameplural</a> ->
                      $streditinga", "form.name", "", false);
    } else {
        print_header("$course->shortname: $streditinga", "$course->fullname",
                     "$streditinga", "form.name", "", false);
    }

    unset($SESSION->modform); // Clear any old ones that may be hanging around.


        $icon = "<img align=middle height=16 width=16 src=\"$CFG->modpixpath/$module->name/icon.gif\" alt=\"\" />&nbsp;";

        print_heading_with_help($pageheading, "mods", $module->name, $icon);
        print_simple_box_start('center');


/// Print the main part of the page

   // adaptation of mod code to view code needs this:
  @include_once("$CFG->dirroot/mod/attendance/lib.php");
    //require_once("lib.php")
// determine the end date for the course based on the number of sections and the start date
$course->enddate = $course->startdate + $course->numsections * 604800;

if (isset($CFG->attendance_dynsection) && ($CFG->attendance_dynsection == "1")) { $form->dynsection = 1; }
if (isset($CFG->attendance_autoattend) && ($CFG->attendance_autoattend == "1")) { $form->autoattend = 1; }
if (isset($CFG->attendance_grade) && ($CFG->attendance_grade == "1")) { $form->grade = 1; }
$form->maxgrade = isset($CFG->attendance_maxgrade)?$CFG->attendance_maxgrade:0;
$form->hours = isset($CFG->attendance_default_hours)?$CFG->attendance_default_hours:1;

?>
<form name="form" method="post" action="add.php">
<center>
<input type="submit" value="<?php  print_string("savechanges") ?>" />
<input type="submit" name="cancel" value="<?php  print_string("cancel") ?>" />
<table cellpadding=5>

<tr valign=top>
    <td align=right><p><b><?php print_string("startmulti", "attendance") ?>:</b></p></td>
    <td colspan="3"><?php print_date_selector("startday", "startmonth", "startyear",$course->startdate) ?></td>
</tr>
<tr valign=top>
    <td align=right><p><b><?php print_string("endmulti", "attendance") ?>:</b></p></td>
    <td colspan="3"><?php print_date_selector("endday", "endmonth", "endyear",$course->enddate) ?></td>
</tr>

<tr valign=top>
    <td align=right><p><b><?php print_string("choosedays", "attendance") ?>:</b></p></td>
    <td colspan="3">
    <?php print_string("sunday","attendance"); echo ":"; ?>
    <input type="checkbox" name="sun" />
    <?php print_string("monday","attendance"); echo ":"; ?>
    <input type="checkbox" name="mon" checked="checked" />
    <?php print_string("tuesday","attendance"); echo ":"; ?>
    <input type="checkbox" name="tue" checked="checked" />
    <?php print_string("wednesday","attendance"); echo ":"; ?>
    <input type="checkbox" name="wed" checked="checked" />
    <?php print_string("thursday","attendance"); echo ":"; ?>
    <input type="checkbox" name="thu" checked="checked" />
    <?php print_string("friday","attendance"); echo ":"; ?>
    <input type="checkbox" name="fri" checked="checked" />
    <?php print_string("saturday","attendance"); echo ":"; ?>
    <input type="checkbox" name="sat" />
<?php helpbutton("choosedays", get_string("choosedays","attendance"), "attendance");?>
    </td>
</tr>

<tr valign=top>
    <td align="right"><p><b><?php print_string("dynamicsectionmulti", "attendance") ?>:</b></p></td>
    <td align="left">
<?php
        $options = array();
        $options[0] = get_string("no");
        $options[1] = get_string("yes");
        choose_from_menu($options, "dynsection", "", "");
        helpbutton("dynsection", get_string("dynamicsectionmulti","attendance"), "attendance");
?>
<!--      <input type="checkbox" name="dynsection" <?php echo !empty($form->dynsection) ? 'checked' : '' ?> /> -->
</td>
</tr>
<tr valign=top>
    <td align="right"><p><b><?php print_string("autoattendmulti", "attendance") ?>:</b></p></td>
    <td align="left">
<?php
        $options = array();
        $options[0] = get_string("no");
        $options[1] = get_string("yes");
        choose_from_menu($options, "autoattend", "", "");
        helpbutton("autoattendmulti", get_string("autoattend","attendance"), "attendance");
?>


<!--      <input type="checkbox" name="autoattend" <?php echo !empty($form->autoattend) ? 'checked' : '' ?> /> -->
    </td>
</tr>
<?php // starting with 2 to allow for the nothing value in choose_from_menu to be the default of 1
for ($i=2;$i<=24;$i++){ $opt[$i] = $i; } ?>
<tr valign=top>
    <td align=right><p><b><?php print_string("hoursineachclass", "attendance") ?>:</b></p></td>
    <td  colspan="3" align="left"><?php choose_from_menu($opt, "hours", $form->hours, "1","","1") ?>
<?php helpbutton("hours", get_string("hoursinclass","attendance"), "attendance"); ?>
</td>
</tr>

<tr valign=top>
    <td align="right"><p><b><?php print_string("gradevaluemulti", "attendance") ?>:</b></p></td>
    <td align="left">
<?php
        $options = array();
        $options[0] = get_string("no");
        $options[1] = get_string("yes");
        choose_from_menu($options, "grade", "", "");
        helpbutton("grade", get_string("gradevalue","attendance"), "attendance");
?>

<!--      <input type="checkbox" name="grade" <?php echo !empty($form->grade) ? 'checked' : '' ?> /> -->
    </td>
</tr>
<?php // starting with 2 to allow for the nothing value in choose_from_menu to be the default of 1
for ($i=0;$i<=100;$i++){ $opt2[$i] = $i; } ?>
<tr valign=top>
    <td align=right><p><b><?php print_string("maxgradevalue", "attendance") ?>:</b></p></td>
    <td  colspan="3" align="left"><?php choose_from_menu($opt2, "maxgrade", $form->maxgrade, "0","","0");
   helpbutton("maxgrade", get_string("maxgradevalue","attendance"), "attendance");
?></td>
</tr>


</table>
<!-- These hidden variables are always the same -->
<input type="hidden" name=course        value="<?php p($form->course) ?>" />
<input type="hidden" name=coursemodule  value="<?php p($form->coursemodule) ?>" />
<input type="hidden" name=section       value="<?php p($form->section) ?>" />
<input type="hidden" name=module        value="<?php p($form->module) ?>" />
<input type="hidden" name=modulename    value="<?php p($form->modulename) ?>" />
<input type="hidden" name=instance      value="<?php p($form->instance) ?>" />
<input type="hidden" name=mode          value="<?php p($form->mode) ?>" />
<input type="hidden" name=numsections   value="<?php p($course->numsections) ?>" />
<br />
<input type="submit" value="<?php print_string("savechanges") ?>" />
<input type="submit" name="cancel" value="<?php print_string("cancel") ?>" />
</center>
</form>

<?php
    print_simple_box_end();
/// Finish the page
    print_footer($course);
    }

?>
