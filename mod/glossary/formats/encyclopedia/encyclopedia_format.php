<?PHP  // $Id$

function glossary_show_entry_encyclopedia($course, $cm, $glossary, $entry, $mode="",$hook="",$printicons=1,$ratings=NULL, $aliases=true) {
    global $THEME, $CFG, $USER;

    $colour = $THEME->cellheading2;

    $user = get_record("user", "id", $entry->userid);
    $strby = get_string("writtenby", "glossary");

    echo "\n<br /><table border=0 width=95% cellspacing=0 valign=top cellpadding=3 class=forumpost align=center>";

    echo "\n<tr>";
    echo "\n<td bgcolor=\"$colour\" width=35 valign=top class=\"forumpostpicture\">";
    $return = false;
    if ($entry) {
        print_user_picture($user->id, $course->id, $user->picture);
    
        echo "</td>";
        echo "<td valign=\"top\" width=100% bgcolor=\"$THEME->cellheading\" class=\"forumpostheader\">";
        echo "<b>";
        glossary_print_entry_concept($entry);
        echo "</b><br />";
    
        echo "<font size=\"2\">$strby " . fullname($user, isteacher($course->id)) . "</font>";
        echo "&nbsp;&nbsp;<font size=1>(".get_string("lastedited").": ".
             userdate($entry->timemodified).")</font>";
        echo "</td>";
        echo "\n<td bgcolor=\"$THEME->cellheading\" width=35 valign=top class=\"forumpostheader\">";

        glossary_print_entry_approval($cm, $entry, $mode);
        echo "</td>";
        
        echo "</tr>";

        echo "\n<tr>";
        echo "\n<td bgcolor=\"$colour\" width=35 valign=top class=\"forumpostside\">&nbsp;</td>";
        echo "\n<td width=100% colspan=\"2\" bgcolor=\"$THEME->cellcontent\" class=\"forumpostmessage\">";

        if ($entry->attachment) {
            $entry->course = $course->id;
            if (strlen($entry->definition)%2) {
                $align = "right";
            } else {
                $align = "left";
            }
            glossary_print_entry_attachment($entry,"",$align,false);
        }
        glossary_print_entry_definition($entry);
        if ($printicons or $ratings or $aliases) {
            echo "</td></tr>";
            echo "\n<td bgcolor=\"$colour\" width=35 valign=top class=\"forumpostside\">&nbsp;</td>";
            echo "\n<td width=100% colspan=\"2\" bgcolor=\"$THEME->cellcontent\" class=\"forumpostmessage\">";
    
            $return = glossary_print_entry_lower_section($course, $cm, $glossary, $entry,$mode,$hook,$printicons,$ratings, $aliases);
            echo ' ';
        }
    } else {
        echo "<center>";
        print_string("noentry", "glossary");
        echo "</center>";
    }
    echo "</td></tr>";

    echo "</table>\n";
    
    return $return;
}

function glossary_print_entry_encyclopedia($course, $cm, $glossary, $entry, $mode="", $hook="", $printicons=1, $ratings=NULL) {

    //The print view for this format is exactly the normal view, so we use it

    //Take out autolinking in definitions un print view
    $entry->definition = '<nolink>'.$entry->definition.'</nolink> ';

    //Call to view function (without icons, ratings and aliases) and return its result
    return glossary_show_entry_encyclopedia($course, $cm, $glossary, $entry, $mode, $hook, false, false, false);

}

?>
