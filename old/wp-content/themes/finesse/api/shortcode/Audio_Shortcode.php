<?php

class Audio_Shortcode extends Abstract_Finesse_Shortcode implements Shortcode_Designer
{
    static $WIDTH_ATTR = "width";
    static $THUMB_ATTR = "thumb";
    static $FORMAT_ATTR = "format";
    static $SRC_ATTR = "src";
    private $sources = array();

    private function reset()
    {
        unset($this->sources);
        $this->sources = array();
    }

    function render($attr, $inner_content = '', $code = '')
    {
        $content = '';
        switch ($code) {
            case "audio":
                $inner_content = do_shortcode($this->prepare_content($inner_content));
                $content .= $this->render_audio($attr, $inner_content);
                $this->reset();
                break;
            case "audio_source":
                $this->process_audio_source($attr);
                break;
        }
        return $content;
    }

    private function render_audio($attr, $inner_content)
    {
        extract(shortcode_atts(array(Audio_Shortcode::$WIDTH_ATTR => '700',
            Audio_Shortcode::$THUMB_ATTR => 'false'), $attr));

        $div_class = ($thumb == 'false') ? ' large-audio' : '';
        $audio_size = ($thumb == 'false') ? ' width="' . $width . '"' : ' width="220"';

        $content = "<div class=\"entry-audio" . $div_class . "\">\n";
        if(function_exists('wp_audio_shortcode') && empty($this->sources)){
            $content .= wp_audio_shortcode($attr);
        }else{
            if (count($this->sources) == 1) {
                foreach ($this->sources as $format => $source) {
                    $src = $source;
                }
                $content .= '<audio' . $audio_size . ' style="width: 100%;" src="' . $src . '" controls preload="none"></audio>' . "\n";
            } else {
                $content .= '<audio' . $audio_size . ' style="width: 100%;" controls preload="none">' . "\n";
                foreach ($this->sources as $format => $source) {
                    $content .= '<source src="' . $source . '" type="audio/' . $format . '">' . "\n";
                }
                $content .= '</audio>' . "\n";
            }
        }

        $content .= "</div>";
        return $content;
    }

    private function process_audio_source($attr)
    {
        extract(shortcode_atts(array(Audio_Shortcode::$FORMAT_ATTR => '',
            Audio_Shortcode::$SRC_ATTR => ''), $attr));
        $src = Multimedia_Util::get_media_url($src);
        $this->sources[$format] = $src;
    }

    function get_names()
    {
        return array('audio', 'audio_source');
    }

    function get_visual_editor_form()
    {
        $content = '<form id="sc-audio-form" class="generic-form" method="post" action="#" data-sc="audio">';
        $content .= '<fieldset>';
        $content .= '<div>';
        $content .= '<label for="sc-audio-content">' . __('Content', 'finesse') . ':</label>';
        $content .= '<textarea id="sc-audio-content" name="sc-audio-content" class="required" data-attr-type="content"></textarea>';
        $content .= '</div>';
        $content .= '<div>';
        $content .= '<label for="sc-audio-width">' . __('Width', 'finesse') . ':</label>';
        $content .= '<input id="sc-audio-width" name="sc-audio-width" type="text" class="required number" value="700" data-attr-name="' . Audio_Shortcode::$WIDTH_ATTR . '" data-attr-type="attr">';
        $content .= '</div>';
        $content .= '<div>';
        $content .= '<input id="sc-audio-thumb" name="sc-audio-thumb" type="checkbox" data-attr-name="' . Audio_Shortcode::$THUMB_ATTR . '" data-attr-type="attr">';
        $content .= '<label for="sc-audio-thumb">' . __('This is a thumbnail audio', 'finesse') . '</label>';
        $content .= '</div>';
        $content .= '<div >';
        $content .= '<input id="sc-audio-form-submit" type="submit" name="submit" value="' . __('Insert Audio', 'finesse') . '" class="button-primary">';
        $content .= '<input id="sc-audio-form-add-src" type="submit" name="submit" value="' . __('Add Audio Source', 'finesse') . '" class="button-secondary">';
        $content .= '</div>';
        $content .= '</fieldset>';
        $content .= '</form>';

        $content .= '<div id="sc-audio-source-dialog" title="' . __('New Audio Source', 'finesse') . '" style="display: none">';
        $content .= '<form id="sc-audio-source-form" class="generic-form" method="post" action="#" data-sc="audio_source">';
        $content .= '<fieldset>';
        $content .= '<div>';
        $content .= '<label for="sc-audio-source-format">' . __('Audio Format', 'finesse') . ':</label>';
        $content .= '<select id="sc-audio-source-format" name="sc-audio-source-format" data-attr-name="' . Audio_Shortcode::$FORMAT_ATTR . '" data-attr-type="attr">';
        $content .= '<option value="mpeg">MP3</option>';
        $content .= '<option value="wav">Wav</option>';
        $content .= '<option value="ogg">Ogg</option>';
        $content .= '</select>';
        $content .= '</div>';
        $content .= '<div>';
        $content .= '<label for="sc-audio-source-src">' . __('Source (URL)', 'finesse') . ':</label>';
        $content .= '<input id="sc-audio-source-src" name="sc-audio-source-src" type="text" class="required" data-attr-name="' . Audio_Shortcode::$SRC_ATTR . '" data-attr-type="attr">';
        $content .= '</div>';
        $content .= '<div >';
        $content .= '<input id="sc-audio-source-form-submit" type="submit" name="submit" value="' . __('Add Audio Source', 'finesse') . '" class="button-primary">';
        $content .= '<input id="sc-audio-source-form-cancel" type="submit" name="submit" value="' . __('Cancel', 'finesse') . '" class="button-secondary">';
        $content .= '</div>';
        $content .= '</fieldset>';
        $content .= '</form>';
        $content .= '</div>';

        return $content;
    }

    function get_group_title()
    {
        return __('Multimedia', 'finesse');
    }

    function get_title()
    {
        return __('Audio', 'finesse');
    }

    function register_shortcode()
    {
        parent::register_shortcode();
        add_filter('wp_audio_shortcode_class', array($this, 'wp_audio_shortcode_class'));
    }

    function wp_audio_shortcode_class(){
        return '';
    }

}