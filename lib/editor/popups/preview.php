<?php // $Id$ preview for insert image dialog
    
    include("../../../config.php");
    require("../../../files/mimetypes.php");
    
    $id       = required_param('id', PARAM_INT);
    $imageurl = required_param('imageurl', PARAM_URL);

    if (! $course = get_record("course", "id", $id) ) {
        error("That's an invalid course id");
    }

    require_login($course->id);

    if (!isteacher($course->id)) {
        error("Only teachers can use this functionality");
    }

    $imageurl = rawurldecode($imageurl);   /// Full URL starts with $CFG->wwwroot/file.php
    $imagepath = str_replace("$CFG->wwwroot/file.php", '', $imageurl);
    $imagepath = str_replace("?file=", '', $imagepath); // if we're using second option of file path.

    $size = null;
    if ($imagepath != $imageurl) {         /// This is an internal image
        $size = getimagesize($CFG->dataroot.$imagepath);
    }
    
    $width = $size[0];
    $height = $size[1];
    settype($width, "integer");
    settype($height, "integer");
    
    if ($height >= 200) {
        $division = ($height / 190);
        $width = round($width / $division);
        $height = 190;
    }
    echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\"\n";
    echo "\t\"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n";
    echo "<html>\n";
    echo "<head>\n";
    echo "<title>Preview</title>\n";
    echo "<style type=\"text/css\">\n";
    echo " body { margin: 2px; }\n";
    echo "</style>\n";
    echo "</head>\n";
    echo "<body bgcolor=\"#ffffff\">\n";
    print "<img src=\"$imageurl\" width=\"$width\" height=\"$height\" alt=\"\">";
    echo "</body>\n</html>\n";
    
?>
