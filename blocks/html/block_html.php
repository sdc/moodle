<?php //$Id$

class block_html extends block_base {

    function init() {
        $this->title = get_string('html', 'block_html');
        $this->version = 2007101509;
    }

    function applicable_formats() {
        return array('all' => true);
    }

    function specialization() {
        $this->title = isset($this->config->title) ? $this->config->title : get_string('newhtmlblock', 'block_html');
    }

    function instance_allow_multiple() {
        return true;
    }

    function get_content() {
        if ($this->content !== NULL) {
            return $this->content;
        }

        $filteropt = new stdClass;
        $filteropt->noclean = true;

        $this->content = new stdClass;
        $this->content->text = isset($this->config->text) ? format_text($this->config->text, FORMAT_HTML, $filteropt) : '';
        $this->content->footer = '';

        unset($filteropt); // memory footprint

        return $this->content;
    }

    function backup_encode_absolute_links_in_config(&$config) {
        $config->text = backup_encode_absolute_links($config->text);
    }

    function restore_decode_absolute_links_in_config(&$config) {
        $oldtext = $config->text;
        $config->text = restore_decode_absolute_links($oldtext);
        return $config->text != $oldtext;
    }
}
?>
