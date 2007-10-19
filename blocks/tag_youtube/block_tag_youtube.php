<?php // $id$

require_once($CFG->dirroot.'/tag/lib.php');
require_once($CFG->libdir . '/filelib.php');
require_once($CFG->libdir . '/magpie/rss_cache.inc');
require_once($CFG->libdir . '/phpxml/xml.php');

define('YOUTUBE_DEV_KEY', 'Dlp6qqRbI28');
define('DEFAULT_NUMBER_OF_VIDEOS', 5);
define('YOUTUBE_CACHE_EXPIRATION', 1800);

class block_tag_youtube extends block_base {

    function init() {
        $this->title = get_string('blockname','block_tag_youtube');
        $this->version = 2007080800;
    }

    function applicable_formats() {
        return array('tag' => true);
    }

    function specialization() {
        $this->title = !empty($this->config->title) ? $this->config->title : get_string('blockname', 'block_tag_youtube');
    }

    function instance_allow_multiple() {
        return true;
    }

    function get_content() {

        if ($this->content !== NULL) {
            return $this->content;
        }

        if(!empty($this->config->playlist)){
            //videos from a playlist
            $text = $this->get_videos_by_playlist();
        }
        else{
            if(!empty($this->config->category)){
                //videos from category with tag
                $text = $this->get_videos_by_tag_and_category();
            }
            else {
                //videos with tag
                $text = $this->get_videos_by_tag();
            }
        }

        $this->content = new stdClass;
        $this->content->text = $text;
        $this->content->footer = '';

        return $this->content;
    }

    function get_videos_by_playlist(){

        $numberofvideos = DEFAULT_NUMBER_OF_VIDEOS;
        if( !empty($this->config->numberofvideos)) {
            $numberofvideos = $this->config->numberofvideos;
        }

        $request = 'http://www.youtube.com/api2_rest?method=youtube.videos.list_by_playlist';
        $request .= '&dev_id=' . YOUTUBE_DEV_KEY;
        $request .= "&id={$this->config->playlist}";
        $request .= "&page=1";
        $request .= "&per_page={$numberofvideos}";

        return $this->fetch_request($request);
    }

    function get_videos_by_tag(){

        $tagid       = optional_param('id',     0,      PARAM_INT);   // tag id

        $query_tag = tag_display_name(tag_by_id($tagid));
        $query_tag = urlencode($query_tag);

        $numberofvideos = DEFAULT_NUMBER_OF_VIDEOS;
        if( !empty($this->config->numberofvideos)) {
            $numberofvideos = $this->config->numberofvideos;
        }

        $request = 'http://www.youtube.com/api2_rest?method=youtube.videos.list_by_tag';
        $request .= '&dev_id=' . YOUTUBE_DEV_KEY;
        $request .= "&tag={$query_tag}";
        $request .= "&page=1";
        $request .= "&per_page={$numberofvideos}";

        return $this->fetch_request($request);
    }

    function get_videos_by_tag_and_category(){

        $tagid       = optional_param('id',     0,      PARAM_INT);   // tag id

        $query_tag = tag_display_name(tag_by_id($tagid));
        $query_tag = urlencode($query_tag);

        $numberofvideos = DEFAULT_NUMBER_OF_VIDEOS;
        if( !empty($this->config->numberofvideos)) {
            $numberofvideos = $this->config->numberofvideos;
        }

        $request = 'http://www.youtube.com/api2_rest?method=youtube.videos.list_by_category_and_tag';
        $request .= '&category_id='.$this->config->category;
        $request .= '&dev_id=' . YOUTUBE_DEV_KEY;
        $request .= "&tag={$query_tag}";
        $request .= "&page=1";
        $request .= "&per_page={$numberofvideos}";

        return $this->fetch_request($request);
    }

    function fetch_request($request){

        global $CFG;

        make_upload_directory('/cache/youtube');

        $cache = new RSSCache($CFG->dataroot . '/cache/youtube',YOUTUBE_CACHE_EXPIRATION);
        $cache_status = $cache->check_cache( $request);

        if ( $cache_status == 'HIT' ) {
            $cached_response = $cache->get( $request );

            $xmlobj = XML_unserialize($cached_response);
            return $this->render_video_list($xmlobj);
        }

        if ( $cache_status == 'STALE' ) {
            $cached_response = $cache->get( $request );
        }

        $response = download_file_content($request);

        if(empty($response)){
            $response = $cached_response;
        }
        else{
            $cache->set($request, $response);
        }

        $xmlobj = XML_unserialize($response);
        return $this->render_video_list($xmlobj);
    }

    function render_video_list($xmlobj){

        $text = '';
        $text .= '<ul class="yt-video-entry unlist img-text">';
        $videos = $xmlobj['ut_response']['video_list']['video'];

        foreach($videos as $video){
            $text .= '<li>';
            $text .= '<a href="'. s($video['url']) . '">';
            $text .= '<img alt="" class="youtube-thumb" src="'. $video['thumbnail_url'] .'" /> <span>';
            $text .= s($video['title']). '</span></a>';
            $text .= '<div>';
            $text .= format_time($video['length_seconds']);
            $text .= "</div></li>\n";
        }
        $text .= "</ul><div class=\"clearer\"></div>\n";

        return $text;
    }
}
?>
