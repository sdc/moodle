<?PHP // $Id$
    //This function provides automatic linking to
    //wiki pages when its page title is found inside every Moodle text
    //It's based in the glosssary filter by Williams Castillo
    //Modifications by mchurch. Enjoy! :-)

    require_once($CFG->dirroot.'/mod/wiki/lib.php');

    function wiki_filter($courseid, $text) {

        global $CFG;

        if (empty($courseid)) {
            if ($site = get_site()) {
                $courseid = $site->id;
            }
        }

        if (!($course = get_record('course', 'id', $courseid))) {
            return $text;
        }

//      Get all wikis for this course.
        $wikis = wiki_get_course_wikis($courseid);
        if (empty($wikis)) {
            return $text;
        }

//      Walk through each wiki, and get entries.
        foreach ($wikis as $wiki) {
            if ($wiki_entries = wiki_get_entries($wiki)) {

//              Walk through each entry and get the pages.
                foreach ($wiki_entries as $wiki_entry) {
                    if ($wiki_pages = get_records('wiki_pages', 'wiki', $wiki_entry->id)) {

//                      Walk through each page and filter.
                        foreach ($wiki_pages as $wiki_page) {
                            $startlink = '<a class="autolink" title="Wiki" href="'
                                        .$CFG->wwwroot.'/mod/wiki/view.php?wid='.$wiki->id
                                        .'&userid='.$wiki_entry->userid
                                        .'&groupid='.$wiki_entry->groupid
                                        .'&wikipage='.$wiki_page->pagename.'">';
                            $text = wiki_link_names($text, $wiki_page->pagename, $startlink, '</a>');
                        }
                    }
                }
            }
        }

        return $text;
    }
    
    function wiki_link_names($text,$name,$href_tag_begin,$href_tag_end = "</a>") {

        $list_of_words_cp = strip_tags($name);

        $list_of_words_cp = trim($list_of_words_cp,'|');

        $list_of_words_cp = trim($list_of_words_cp);

        $list_of_words_cp = preg_quote($list_of_words_cp,'/');

        $invalidprefixs = "([a-zA-Z0-9])";
        $invalidsufixs  = "([a-zA-Z0-9])";

        //Avoid seaching in the string if it's inside invalidprefixs and invalidsufixs
        $words = array();
        $regexp = '/'.$invalidprefixs.'('.$list_of_words_cp.')|('.$list_of_words_cp.')'.$invalidsufixs.'/is';
        preg_match_all($regexp,$text,$list_of_words);

        foreach (array_unique($list_of_words[0]) as $key=>$value) {
            $words['<*'.$key.'*>'] = $value;
        }
        if (!empty($words)) {
            $text = str_replace($words,array_keys($words),$text);
        }

        //Now avoid searching inside the <nolink>tag
        $excludes = array();
        preg_match_all('/<nolink>(.+?)<\/nolink>/is',$text,$list_of_excludes);
        foreach (array_unique($list_of_excludes[0]) as $key=>$value) {
            $excludes['<+'.$key.'+>'] = $value;
        }
        if (!empty($excludes)) {
            $text = str_replace($excludes,array_keys($excludes),$text);
        }

        //Now avoid searching inside links
        $links = array();
        preg_match_all('/<A[\s](.+?)>(.+?)<\/A>/is',$text,$list_of_links);
        foreach (array_unique($list_of_links[0]) as $key=>$value) {
            $links['<@'.$key.'@>'] = $value;
        }
        if (!empty($links)) {
            $text = str_replace($links,array_keys($links),$text);
        }

        //Now avoid searching inside every tag
        $final = array();
        preg_match_all('/<(.+?)>/is',$text,$list_of_tags);
        foreach (array_unique($list_of_tags[0]) as $key=>$value) {
            $final['<|'.$key.'|>'] = $value;
        }
        if (!empty($final)) {
            $text = str_replace($final,array_keys($final),$text);
        }

        $text = preg_replace('/('.$list_of_words_cp.')/is', $href_tag_begin.'$1'.$href_tag_end,$text);

        //Now rebuild excluded areas
        if (!empty($final)) {
            $text = str_replace(array_keys($final),$final,$text);
        }
        if (!empty($links)) {
            $text = str_replace(array_keys($links),$links,$text);
        }
        if (!empty($excludes)) {
            $text = str_replace(array_keys($excludes),$excludes,$text);
        }
        if (!empty($words)) {
            $text = str_replace(array_keys($words),$words,$text);
        }
        return $text;
    }
?>
