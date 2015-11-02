<?php

function theme_roshni_get_setting($setting, $format = false) {
    global $CFG;
    require_once($CFG->dirroot . '/lib/weblib.php');
    static $theme;
    if (empty($theme)) {
        $theme = theme_config::load('roshni');
    }
    if (empty($theme->settings->$setting)) {
        return false;
    } else if (!$format) {
        return $theme->settings->$setting;
    } else if ($format === 'format_text') {
        return format_text($theme->settings->$setting, FORMAT_PLAIN);
    } else if ($format === 'format_html') {
        return format_text($theme->settings->$setting, FORMAT_HTML, array('trusted' => true, 'noclean' => true));
    } else {
        return format_string($theme->settings->$setting);
    }
}
/**
 * Parses CSS before it is cached.
 *
 * This function can make alterations and replace patterns within the CSS.
 *
 * @param string $css The CSS
 * @param theme_config $theme The theme config object.
 * @return string The parsed CSS The parsed CSS.
 */
function theme_roshni_process_css($css, $theme) {

    // Set the background image for the logo.
    $logo = $theme->setting_file_url('logo', 'logo');
    $css = theme_roshni_set_logo($css, $logo);
    if (!empty($theme->settings->fontnamebody)) {
        $font = $theme->settings->fontnamebody;
    } else {
        $font = 'Raleway';
    }
    $headingfont = theme_roshni_get_setting('fontnameheading');
    $bodyfont = theme_roshni_get_setting('fontnamebody');

    $css = theme_roshni_set_headingfont($css, $headingfont);
    $css = theme_roshni_set_bodyfont($css, $bodyfont);
    $css = theme_roshni_set_fontfiles($css, 'heading', $headingfont);
    $css = theme_roshni_set_fontfiles($css, 'body', $bodyfont);
    // Set custom CSS.
    if (!empty($theme->settings->customcss)) {
        $customcss = $theme->settings->customcss;
    } else {
        $customcss = null;
    }
    $css = theme_roshni_set_customcss($css, $customcss);

    return $css;
}

function theme_roshni_set_headingfont($css, $headingfont) {
    $tag = '[[setting:headingfont]]';
    //$tag = '[[setting:fontnameheading]]';
    $replacement = $headingfont;
    $css = str_replace($tag, $replacement, $css);
    return $css;
}

function theme_roshni_set_bodyfont($css, $bodyfont) {
    $tag = '[[setting:bodyfont]]';
    //$tag = '[[setting:fontnamebody]]';
    $replacement = $bodyfont;
    $css = str_replace($tag, $replacement, $css);
    return $css;
}
/**
 * Adds the logo to CSS.
 *
 * @param string $css The CSS.
 * @param string $logo The URL of the logo.
 * @return string The parsed CSS
 */
function theme_roshni_set_logo($css, $logo) {
    $tag = '[[setting:logo]]';
    $replacement = $logo;
    if (is_null($replacement)) {
        $replacement = '';
    }

    $css = str_replace($tag, $replacement, $css);

    return $css;
}
/**
 * Adds the font to CSS.
 *
 * @param string $css The CSS.
 * @param string $font The font name.
 * @return string The parsed CSS
 */

function theme_roshni_set_fontfiles($css, $type, $fontname) {
    $tag = '[[setting:fontfiles' . $type . ']]';
    $replacement = '';
    if (theme_roshni_get_setting('fontselect') === '2') {
        static $theme;
        if (empty($theme)) {
            $theme = theme_config::load('roshni');  // $theme needs to be us for child themes.
        }

        $fontfiles = array();
        $fontfileeot = $theme->setting_file_url('fontfileeot' . $type, 'fontfileeot' . $type);
        if (!empty($fontfileeot)) {
            $fontfiles[] = "url('" . $fontfileeot . "?#iefix') format('embedded-opentype')";
        }
        $fontfilewoff = $theme->setting_file_url('fontfilewoff' . $type, 'fontfilewoff' . $type);
        if (!empty($fontfilewoff)) {
            $fontfiles[] = "url('" . $fontfilewoff . "') format('woff')";
        }
        $fontfilewofftwo = $theme->setting_file_url('fontfilewofftwo' . $type, 'fontfilewofftwo' . $type);
        if (!empty($fontfilewofftwo)) {
            $fontfiles[] = "url('" . $fontfilewofftwo . "') format('woff2')";
        }
        $fontfileotf = $theme->setting_file_url('fontfileotf' . $type, 'fontfileotf' . $type);
        if (!empty($fontfileotf)) {
            $fontfiles[] = "url('" . $fontfileotf . "') format('opentype')";
        }
        $fontfilettf = $theme->setting_file_url('fontfilettf' . $type, 'fontfilettf' . $type);
        if (!empty($fontfilettf)) {
            $fontfiles[] = "url('" . $fontfilettf . "') format('truetype')";
        }
        $fontfilesvg = $theme->setting_file_url('fontfilesvg' . $type, 'fontfilesvg' . $type);
        if (!empty($fontfilesvg)) {
            $fontfiles[] = "url('" . $fontfilesvg . "') format('svg')";
        }

        $replacement = '@font-face {' . PHP_EOL . 'font-family: "' . $fontname . '";' . PHP_EOL;
        $replacement .=!empty($fontfileeot) ? "src: url('" . $fontfileeot . "');" . PHP_EOL : '';
        if (!empty($fontfiles)) {
            $replacement .= "src: ";
            $replacement .= implode("," . PHP_EOL . " ", $fontfiles);
            $replacement .= ";";
        }
        $replacement .= '' . PHP_EOL . "}";
    }

    $css = str_replace($tag, $replacement, $css);
    return $css;
}
/**
 * Serves any files associated with the theme settings.
 *
 * @param stdClass $course
 * @param stdClass $cm
 * @param context $context
 * @param string $filearea
 * @param array $args
 * @param bool $forcedownload
 * @param array $options
 * @return bool
 */
function theme_roshni_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = array()) {
    static $theme;
    if (empty($theme)) {
        $theme = theme_config::load('roshni');
    }
    if ($context->contextlevel == CONTEXT_SYSTEM) {
        if ($filearea === 'logo') {
            return $theme->setting_file_serve('logo', $args, $forcedownload, $options);
        } else if ($filearea === 'style') {
            theme_roshni_serve_css($args[1]);
        } else if ($filearea === 'headerbackground') {
            return $theme->setting_file_serve('headerbackground', $args, $forcedownload, $options);
        } else if ($filearea === 'pagebackground') {
            return $theme->setting_file_serve('pagebackground', $args, $forcedownload, $options);
        } else if (preg_match("/^fontfile(eot|otf|svg|ttf|woff|woff2)(heading|body)$/", $filearea)) { // http://www.regexr.com/.
            return $theme->setting_file_serve($filearea, $args, $forcedownload, $options);
        } else if (preg_match("/^(marketing|slide)[1-9][0-9]*image$/", $filearea)) {
            return $theme->setting_file_serve($filearea, $args, $forcedownload, $options);
        } else if ($filearea === 'iphoneicon') {
            return $theme->setting_file_serve('iphoneicon', $args, $forcedownload, $options);
        } else if ($filearea === 'iphoneretinaicon') {
            return $theme->setting_file_serve('iphoneretinaicon', $args, $forcedownload, $options);
        } else if ($filearea === 'ipadicon') {
            return $theme->setting_file_serve('ipadicon', $args, $forcedownload, $options);
        } else if ($filearea === 'ipadretinaicon') {
            return $theme->setting_file_serve('ipadretinaicon', $args, $forcedownload, $options);
        } else {
            send_file_not_found();
        }
    } else {
        send_file_not_found();
    }

}
function theme_roshni_send_cached_css($path, $filename, $lastmodified, $etag) {
    global $CFG;
    require_once($CFG->dirroot . '/lib/configonlylib.php'); // For min_enable_zlib_compression().
    // 60 days only - the revision may get incremented quite often.
    $lifetime = 60 * 60 * 24 * 60;

    header('Etag: "' . $etag . '"');
    header('Content-Disposition: inline; filename="'.$filename.'"');
    if ($lastmodified) {
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $lastmodified) . ' GMT');
    }
    header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $lifetime) . ' GMT');
    header('Pragma: ');
    header('Cache-Control: public, max-age=' . $lifetime);
    header('Accept-Ranges: none');
    header('Content-Type: text/css; charset=utf-8');
    if (!min_enable_zlib_compression()) {
        header('Content-Length: ' . filesize($path . $filename));
    }

    readfile($path . $filename);
    die;
}
function theme_roshni_send_unmodified($lastmodified, $etag) {
    $lifetime = 60 * 60 * 24 * 60;
    header('HTTP/1.1 304 Not Modified');
    header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $lifetime) . ' GMT');
    header('Cache-Control: public, max-age=' . $lifetime);
    header('Content-Type: text/css; charset=utf-8');
    header('Etag: "' . $etag . '"');
    if ($lastmodified) {
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $lastmodified) . ' GMT');
    }
    die;
}


/**
 * Adds any custom CSS to the CSS before it is cached.
 *
 * @param string $css The original CSS.
 * @param string $customcss The custom CSS to add.
 * @return string The CSS which now contains our custom CSS.
 */
function theme_roshni_set_customcss($css, $customcss) {
    $tag = '[[setting:customcss]]';
    $replacement = $customcss;
    if (is_null($replacement)) {
        $replacement = '';
    }

    $css = str_replace($tag, $replacement, $css);

    return $css;
}

/**
 * Returns an object containing HTML for the areas affected by settings.
 *
 * Do not add roshni specific logic in here, child themes should be able to
 * rely on that function just by declaring settings with similar names.
 *
 * @param renderer_base $output Pass in $OUTPUT.
 * @param moodle_page $page Pass in $PAGE.
 * @return stdClass An object with the following properties:
 *      - navbarclass A CSS class to use on the navbar. By default ''.
 *      - heading HTML to use for the heading. A logo if one is selected or the default heading.
 *      - footnote HTML to use as a footnote. By default ''.
 */
function theme_roshni_get_html_for_settings(renderer_base $output, moodle_page $page) {
    global $CFG;
    $return = new stdClass;

    $return->navbarclass = '';
    if (!empty($page->theme->settings->invert)) {
        $return->navbarclass .= ' navbar-inverse';
    }

    if (!empty($page->theme->settings->logo)) {
        $return->heading = html_writer::tag('div', '', array('class' => 'logo'));
    } else {
        $return->heading = $output->page_heading();
    }

    $return->footnote = '';
    if (!empty($page->theme->settings->footnote)) {
        $return->footnote = '<div class="footnote text-center">'.format_text($page->theme->settings->footnote).'</div>';
    }

    return $return;
}

/**
 * All theme functions should start with theme_roshni_
 * @deprecated since 2.5.1
 */
function roshni_process_css() {
    throw new coding_exception('Please call theme_'.__FUNCTION__.' instead of '.__FUNCTION__);
}

/**
 * All theme functions should start with theme_roshni_
 * @deprecated since 2.5.1
 */
function roshni_set_logo() {
    throw new coding_exception('Please call theme_'.__FUNCTION__.' instead of '.__FUNCTION__);
}

/**
 * All theme functions should start with theme_roshni_
 * @deprecated since 2.5.1
 */
function roshni_set_customcss() {
    throw new coding_exception('Please call theme_'.__FUNCTION__.' instead of '.__FUNCTION__);
}


