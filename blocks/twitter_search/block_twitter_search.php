<?php
// This file is part of Twitter Search block for Moodle
//
// Twitter Search Block for Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Twitter Search Block for Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
//
// Copyright Kevin Hughes 2011.

class block_twitter_search extends block_base {

    public function init() {
        $this->title = get_string('twitter_search', 'block_twitter_search');
    }

    public function has_config() {
        return true;
    }

    public function get_content() {

        //function cmp($a, $b) {
        //    return strlen($b)-strlen($a);
        //}

        global $PAGE, $CFG;
        if ($this->content != null) {
            return $this->content;
        }

        require_once('twitteroauth.php');

        $twitterautherror = false;
        if (!isset($CFG->block_twitter_search_consumer_key) || empty($CFG->block_twitter_search_consumer_key)) {
            $twitterautherror = true;
        }
        if (!isset($CFG->block_twitter_search_consumer_secret) || empty($CFG->block_twitter_search_consumer_secret)) {
            $twitterautherror = true;
        }
        if (!isset($CFG->block_twitter_search_access_token) || empty($CFG->block_twitter_search_access_token)) {
            $twitterautherror = true;
        }
        if (!isset($CFG->block_twitter_search_access_token_secret) || empty($CFG->block_twitter_search_access_token_secret)) {
            $twitterautherror = true;
        }
        if ($twitterautherror) {

            $this->content = new stdClass;
            $this->content->text = '<p>'.get_string('error-twitterauth', 'block_twitter_search').'</p>';

        } else {

            $connection = new TwitterOAuth(
                $CFG->block_twitter_search_consumer_key,
                $CFG->block_twitter_search_consumer_secret,
                $CFG->block_twitter_search_access_token,
                $CFG->block_twitter_search_access_token_secret
            );

            $output = '';

            $searchterm = (isset($this->config->search_term)) ? $this->config->search_term : '#moodle';
            $searchtermenc = urlencode($searchterm);

            $numtweets      = (isset($this->config->numtweets)) ? $this->config->numtweets : 5;
            $username       = (isset($this->config->show_usernames)) ? $this->config->show_usernames : true;
            $realname       = (isset($this->config->show_names)) ? $this->config->show_names : false;
            $image          = (isset($this->config->show_images)) ? $this->config->show_images : true;
            $update         = (isset($this->config->show_update)) ? $this->config->show_update : true;
            $title          = (isset($this->config->title_block)) ? $this->config->title_block : false;
            $type           = (isset($this->config->tweettype)) ? $this->config->tweettype : 'recent';
            $expandimglinks = (isset($this->config->expand_img_links)) ? $this->config->expand_img_links : true;

            $data = $connection->get('search/tweets', array('q' => $searchtermenc, 'count' => $numtweets));

            if (isset($data->statuses)) {

                $output .= '<ul class="block_twitter_search_tweets">';
                foreach ($data->statuses as $status) {

                    // Beginnings of a blacklist.
                    if ($status->user->screen_name == 'e1doom') {
                        break;
                    }

                    // Get all the nice information from the returned data.
                    $author     = $status->user->screen_name;
                    $authorname = $status->user->name;
                    $authorimg  = $status->user->profile_image_url;
                    $authorlink = 'http://twitter.com/'.$author;
                    $tweet      = ' '.$status->text;

                    $output .= '<li class="tweet">';

                    // Formatting the user's username and/or name.
                    if ($username == false && $realname == false) {
                        $authortext = '';
                    } else if ($username == true && $realname == true) {
                        $authortext = '@'.$author.' ('.$authorname.')';
                    } else if ($username == true) {
                        $authortext = '@'.$author.'';
                    } else if ($realname == true) {
                        $authortext = $authorname.'';
                    }

                    // Adding in the image.
                    if ($image == true) {
                        $output .= '<img src="'.$authorimg.'">';
                    }

                    // Replacing #hashtags with linked #hashtags. It's not perfect.
                    $allhashtags = array();
                    foreach ($status->entities->hashtags as $hashtag) {
                        $allhashtags[] = $hashtag->text;
                    }

                    // Sorting the hashtags into length order.
                    //usort($allhashtags, "cmp");

                    // Replacing #hashtags with linked #hashtags. It's not perfect.
                    foreach ($allhashtags as $hashtag) {
                        $tweet = preg_replace('/ #'.$hashtag.'/i',
                            ' <a href="https://twitter.com/search?q='.urlencode('#'.$hashtag).'">#'.$hashtag.'</a>', $tweet);
                    }

                    // Replacing @usernames with linked @usernames. It's not perfect.
                    foreach ($status->entities->user_mentions as $usermention) {
                        $tweet = preg_replace('/@'.$usermention->screen_name.'/i',
                            '<a href="https://twitter.com/'.$usermention->screen_name.'">'.'@'.$usermention->screen_name.'</a>', $tweet);
                    }

                    // Replacing links with linked links. Again, it's not perfect.
                    foreach ($status->entities->urls as $url) {
                        $tweet = preg_replace('#'.$url->url.'#i', '<a href="'.$url->url.'">'.$url->display_url.'</a>', $tweet);
                    }

                    // Replacing media links with linked media links. Again, it's not perfect.
                    if (isset($status->entities->media)) {
                        foreach ($status->entities->media as $url) {
                            if ($expandimglinks) {
                                $tweet = preg_replace('#'.$url->url.'#i', '<a href="'.$url->url.'">'.$url->display_url.'<img class="ts_embedded_img" src="'.$url->media_url.':thumb"></a>', $tweet);
                            } else {
                                $tweet = preg_replace('#'.$url->url.'#i', '<a href="'.$url->url.'">'.$url->display_url.'</a>', $tweet);
                            }
                        }
                    }

                    $output .= '<a href="'.$authorlink.'">'.$authortext.'</a> ';
                    $output .= format_text($tweet, FORMAT_HTML);
                    $output .= '</li>';
                }

//                if ($CFG->enableajax && $update) {
//                    $PAGE->requires->js_init_call('M.block_twitter_search.init', array($search_term_enc, $numtweets));
//                    $output .= "</ul><a class='block_twitter_search_refresh' href='". $CFG->wwwroot ."/blocks/twitter_search/tweets.php?q=".
//                        $search_term_enc."&n=".$numtweets."' onclick='return false'>".get_string('update', 'block_twitter_search')."</a>";
//                } else {
                    $output .= '</ul>';
//                }

                $this->content = new stdClass;
                if ($title == false) {
                    $this->title = $searchterm . get_string('on_twitter', 'block_twitter_search');
                } else {
                    $this->title = $title;
                }
                $this->content->text = $output;
            } else {
                // No statuses returned.
                $this->content = new stdClass;
                $this->content->text = '<p>'.get_string('not_found', 'block_twitter_search').'</p>';
            }

        } // END Twitter auth check.

    }

    public function instance_allow_config() {
        return true;
    }

    public function instance_allow_multiple() {
        return true;
    }
}
