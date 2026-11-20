<?php

class Self_Hosted_Video_Shortcode extends Abstract_Finesse_Shortcode implements Shortcode_Designer
{
    static $THUMB_ATTR = "is_thumb";
    static $WIDTH_ATTR = "width";
    static $HEIGHT_ATTR = "height";
    static $POSTER_ATTR = "poster";
    static $FORMAT_ATTR = "format";
    static $SRC_ATTR = "src";
    static $TYPE_ATTR = "type";
    static $LANG_ATTR = "lang";

    private $sources = array();
    private $tracks = array();

    private function init()
    {
        unset($this->sources);
        unset($this->tracks);
        $this->sources = array();
        $this->tracks = array();
    }

    function render($attr, $inner_content = '', $code = '')
    {
        $content = '';
        switch ($code) {
            case "video":
                $inner_content = do_shortcode($this->prepare_content($inner_content));
                $content .= $this->render_video($attr, $inner_content);
                $this->init();
                break;
            case "video_source":
                $this->process_video_source($attr);
                break;
            case "video_track":
                $this->process_video_track($attr);
                break;
        }
        return $content;
    }

    private function render_video($attr, $inner_content)
    {
        extract(shortcode_atts(array(Self_Hosted_Video_Shortcode::$WIDTH_ATTR => '700',
            Self_Hosted_Video_Shortcode::$HEIGHT_ATTR => '393',
            Self_Hosted_Video_Shortcode::$THUMB_ATTR => 'false',
            Self_Hosted_Video_Shortcode::$POSTER_ATTR => ''), $attr));

        $div_class = ($is_thumb == 'false') ? ' large-video' : '';
        $content = "<div class=\"entry-video" . $div_class . "\">\n";
        if (function_exists('wp_video_shortcode') && empty($this->sources)) {
            if ($is_thumb == 'true') {
                $attr = array_merge($attr, array('width' => 220, 'height' => 130));
            }
            $content .= wp_video_shortcode($attr);
        } else {
            $template_uri = get_template_directory_uri();
            $video_size = ($is_thumb == 'false') ? 'width="' . $width . '" height="' . $height . '"' : 'width="220" height="130"';
            $poster_size = ($is_thumb == 'true') ? 'custom-medium-post-thumb' : 'full';
            $poster = Multimedia_Util::get_image_src($poster, $poster_size);
            $content .= "<video " . $video_size . " style=\"width: 100%; height: 100%;\" poster=\"$poster\" controls preload=\"none\">\n";
            foreach ($this->sources as $format => $source) {
                $content .= "<source type=\"video/$format\" src=\"$source\" />\n";
            }
            foreach ($this->tracks as $track) {
                $content .= $track . "\n";
            }

            if (array_key_exists('mp4', $this->sources)) {
                $fallback_format = $this->sources['mp4'];
            } elseif (array_key_exists('webm', $this->sources)) {
                $fallback_format = $this->sources['webm'];
            } else {
                $fallback_format = $this->sources['ogg'];
            }
            $content .= '<object type="application/x-shockwave-flash" data="' . $template_uri . '/js/flashmediaelement.swf">' . "\n";
            $content .= '<param name="movie" value="' . $template_uri . '/js/flashmediaelement.swf" />' . "\n";
            $content .= '<param name="flashvars" value="controls=true&amp;file=' . $fallback_format . '" />' . "\n";
            $content .= '<img src="' . $poster . '" title="' . __('No video playback capabilities', 'finesse') . '" alt="' . __('No video playback capabilities', 'finesse') . '" />' . "\n";
            $content .= "</object>\n";
            $content .= "</video>\n";
        }
        $content .= "</div>";
        return $content;
    }

    private function process_video_source($attr)
    {
        extract(shortcode_atts(array(Self_Hosted_Video_Shortcode::$FORMAT_ATTR => '',
            Self_Hosted_Video_Shortcode::$SRC_ATTR => ''), $attr));
        $src = Multimedia_Util::get_media_url($src);
        $this->sources[$format] = $src;
    }

    private function process_video_track($attr)
    {
        extract(shortcode_atts(array(Self_Hosted_Video_Shortcode::$TYPE_ATTR => '',
            Self_Hosted_Video_Shortcode::$SRC_ATTR => '',
            Self_Hosted_Video_Shortcode::$LANG_ATTR => ''), $attr));
        $src = Multimedia_Util::get_media_url($src);
        $content = "<track kind=\"$type\" src=\"$src\" srclang=\"$lang\" />";
        array_push($this->tracks, $content);
    }

    function get_names()
    {
        return array('video', 'video_source', 'video_track');
    }

    function get_visual_editor_form()
    {
        $content = '<form id="sc-video-form" class="generic-form" method="post" action="#">';
        $content .= '<fieldset>';
        $content .= '<div>';
        $content .= '<label for="sc-video-content">' . __('Content', 'finesse') . ':</label>';
        $content .= '<textarea id="sc-video-content" name="sc-video-content" class="required"></textarea>';
        $content .= '</div>';
        $content .= '<div>';
        $content .= '<label for="sc-video-poster">' . __('Poster Source', 'finesse') . ':</label>';
        $content .= '<input id="sc-video-poster" name="sc-video-poster" type="text" class="required">';
        $content .= '</div>';
        $content .= '<div>';
        $content .= '<label for="sc-video-width">' . __('Width', 'finesse') . ':</label>';
        $content .= '<input id="sc-video-width" name="sc-video-width" type="text" class="required number" value="700">';
        $content .= '</div>';
        $content .= '<div>';
        $content .= '<label for="sc-video-height">' . __('Height', 'finesse') . ':</label>';
        $content .= '<input id="sc-video-height" name="sc-video-height" type="text" class="required number" value="393">';
        $content .= '</div>';
        $content .= '<div>';
        $content .= '<input id="sc-video-thumb" name="sc-video-thumb" type="checkbox">';
        $content .= '<label for="sc-video-thumb">' . __('This is a thumbnail video', 'finesse') . '</label>';
        $content .= '</div>';
        $content .= '<div >';
        $content .= '<input id="sc-video-form-submit" type="submit" name="submit" value="' . __('Insert Self-Hosted Video', 'finesse') . '" class="button-primary">';
        $content .= '<input id="sc-video-form-add-src" type="submit" name="submit" value="' . __('Add Video Source', 'finesse') . '" class="button-secondary">';
        $content .= '<input id="sc-video-form-add-track" type="submit" name="submit" value="' . __('Add Video Track', 'finesse') . '" class="button-secondary">';
        $content .= '</div>';
        $content .= '</fieldset>';
        $content .= '</form>';

        $content .= '<div id="sc-video-source-dialog" title="' . __('New Video Source', 'finesse') . '" style="display: none">';
        $content .= '<form id="sc-video-source-form" class="generic-form" method="post" action="#">';
        $content .= '<fieldset>';
        $content .= '<div>';
        $content .= '<label for="sc-video-source-format">' . __('Video Format', 'finesse') . ':</label>';
        $content .= '<select id="sc-video-source-format" name="sc-video-source-format">';
        $content .= '<option value="mp4">MP4</option>';
        $content .= '<option value="webm">WEBM</option>';
        $content .= '<option value="ogg">Ogg</option>';
        $content .= '</select>';
        $content .= '</div>';
        $content .= '<div>';
        $content .= '<label for="sc-video-source-src">' . __('Source (URL)', 'finesse') . ':</label>';
        $content .= '<input id="sc-video-source-src" name="sc-video-source-src" type="text" class="required">';
        $content .= '</div>';
        $content .= '<div >';
        $content .= '<input id="sc-video-source-form-submit" type="submit" name="submit" value="' . __('Add Video Source', 'finesse') . '" class="button-primary">';
        $content .= '<input id="sc-video-source-form-cancel" type="submit" name="submit" value="' . __('Cancel', 'finesse') . '" class="button-secondary">';
        $content .= '</div>';
        $content .= '</fieldset>';
        $content .= '</form>';
        $content .= '</div>';

        $content .= '<div id="sc-video-track-dialog" title="' . __('New Video Track', 'finesse') . '" style="display: none">';
        $content .= '<form id="sc-video-track-form" class="generic-form" method="post" action="#">';
        $content .= '<fieldset>';
        $content .= '<div>';
        $content .= '<label for="sc-video-track-type">' . __('Track Type', 'finesse') . ':</label>';
        $content .= '<select id="sc-video-track-type" name="sc-video-track-type">';
        $content .= '<option value="subtitles">' . __('Subtitles', 'finesse') . '</option>';
        $content .= '<option value="chapters">' . __('Chapters', 'finesse') . '</option>';
        $content .= '<option value="captions">' . __('Captions', 'finesse') . '</option>';
        $content .= '<option value="descriptions">' . __('Descriptions', 'finesse') . '</option>';
        $content .= '<option value="metadata">' . __('Metadata', 'finesse') . '</option>';
        $content .= '</select>';
        $content .= '</div>';
        $content .= '<div>';
        $content .= '<label for="sc-video-track-src">' . __('Source (URL)', 'finesse') . ':</label>';
        $content .= '<input id="sc-video-track-src" name="sc-video-track-src" type="text" class="required">';
        $content .= '</div>';
        $content .= '<div>';
        $content .= '<label for="sc-video-track-lang">' . __('The Language of the Timed Track Data', 'finesse') . ':</label>';
        $content .= '<input id="sc-video-track-lang" name="sc-video-track-lang" type="text" class="required" value="en">';
        $content .= '</div>';
        $content .= '<div >';
        $content .= '<input id="sc-video-track-form-submit" type="submit" name="submit" value="' . __('Add Video Track', 'finesse') . '" class="button-primary">';
        $content .= '<input id="sc-video-track-form-cancel" type="submit" name="submit" value="' . __('Cancel', 'finesse') . '" class="button-secondary">';
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
        return __('Self-Hosted Video', 'finesse');
    }

    function register_shortcode()
    {
        parent::register_shortcode();
        add_filter('wp_video_shortcode_class', array($this, 'wp_video_shortcode_class'));
    }

    function wp_video_shortcode_class()
    {
        return '';
    }

}
