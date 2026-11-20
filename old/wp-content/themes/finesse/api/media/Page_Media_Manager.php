<?php

class Page_Media_Manager
{
    private static $PAGE_MEDIA_SETTINGS_META_KEY = 'finesse_page_media_settings';

    function __construct()
    {
        if (is_user_logged_in()) {
            add_action('media_buttons', array($this, 'add_edit_page_media_button_to_editor'), 30);
            add_action('wp_ajax_finesse-page-media-display', array($this, 'render_page_media_editor_form'));
            add_action('wp_ajax_finesse-page-media-save', array($this, 'save_post_media'));
            add_action('wp_ajax_finesse-page-media-add-embedded-video', array($this, 'add_embedded_video'));
            add_action('wp_ajax_finesse-page-media-add-sh-video', array($this, 'add_sh_video'));
            add_action('wp_ajax_finesse-page-media-add-audio', array($this, 'add_audio'));
            add_action('wp_ajax_finesse-page-media-add-link', array($this, 'add_link'));
            add_action('wp_ajax_finesse-page-media-remove', array($this, 'remove_post_media'));
        }
    }

    function add_edit_page_media_button_to_editor()
    {
        global $post;
        if (isset($post)) {
            $post_id = $post->ID;
            $content = '';
            $base_url = get_option('siteurl') . '/wp-admin/admin-ajax.php?action=finesse-page-media-display&post-id=' . $post_id;

            $content .= '<a id="slider-editor-button-id" title="' . __('Edit Slider', 'finesse') . '" class="button page-media-editor-button" style="display: none;" href="' . $base_url . '&media-type=slider&slider-type=none">';
            $content .= '<span class="wp-sc-buttons-icon"></span> ' . __('Edit Slider', 'finesse') . '</a>';
            $content .= '<a id="video-editor-button-id" title="' . __('Edit Video', 'finesse') . '" class="button page-media-editor-button" style="display: none;" href="' . $base_url . '&media-type=video">';
            $content .= '<span class="wp-sc-buttons-icon"></span> ' . __('Edit Video', 'finesse') . '</a>';
            $content .= '<a id="audio-editor-button-id" title="' . __('Edit Audio', 'finesse') . '" class="button page-media-editor-button" style="display: none;" href="' . $base_url . '&media-type=audio">';
            $content .= '<span class="wp-sc-buttons-icon"></span> ' . __('Edit Audio', 'finesse') . '</a>';
            $content .= '<a id="link-editor-button-id" title="' . __('Edit Link', 'finesse') . '" class="button page-media-editor-button" style="display: none;" href="' . $base_url . '&media-type=link">';
            $content .= '<span class="wp-sc-buttons-icon"></span> ' . __('Edit Link', 'finesse') . '</a>';
            echo $content;
        }
    }

    function render_page_media_editor_form()
    {
        $media_type = $_REQUEST['media-type'];
        if ($media_type == 'slider') {
            $this->render_slider_editor_form();
        } elseif ($media_type == 'video') {
            $this->render_video_editor_form();
        } elseif ($media_type == 'audio') {
            $this->render_audio_editor_form();
        } elseif ($media_type == 'link') {
            $this->render_link_editor_form();
        }
        die();
    }

    private function render_video_editor_form()
    {
        $post_id = $_REQUEST['post-id'];
        $mc = Page_Media_Manager::get_page_media_config($post_id);
        if ($mc && $mc->media_type == 'video') {
            $media_config = $mc;
        } else {
            $media_config = null;
        }

        $content = '<form method="post" action="#">';
        $content .= '<input id="page-media-post-id" name="page-media-post-id" type="hidden" value="' . $post_id . '">';
        $content .= '<input id="page-media-type" type="hidden" name="page-media-type" value="video">';
        $content .= '</form>';
        $content .= '[tabs]';
        $content .= '[tab title=\'' . __('Embedded Video', 'finesse') . '\']';
        $content .= $this->get_add_embedded_video_form($media_config);
        $content .= '[/tab]';
        $content .= '[tab title=\'' . __('Self-Hosted Video', 'finesse') . '\']';
        $content .= $this->get_add_sh_video_form($media_config);
        $content .= '[/tab]';
        $content .= '[/tabs]';

        echo do_shortcode($content);
    }

    private function render_audio_editor_form()
    {
        $post_id = $_REQUEST['post-id'];
        $mc = Page_Media_Manager::get_page_media_config($post_id);
        if ($mc && $mc->media_type == 'audio') {
            $media_config = $mc;
        } else {
            $media_config = null;
        }
        echo $this->get_add_audio_form($media_config);
    }

    private function render_link_editor_form()
    {
        $post_id = $_REQUEST['post-id'];
        $mc = Page_Media_Manager::get_page_media_config($post_id);
        if ($mc && $mc->media_type == 'link') {
            $media_config = $mc;
        } else {
            $media_config = null;
        }
        echo $this->get_add_link_form($media_config);
    }

    private function render_slider_editor_form()
    {
        $post_id = $_REQUEST['post-id'];
        $slider_type = $_REQUEST['slider-type'];
        $slider_configuration = Page_Media_Manager::get_page_media_config($post_id);
        if ($slider_configuration) {
            $page_slides = $slider_configuration->slides;
        } else {
            $page_slides = array();
        }
        $content = "<div class=\"slider-manager-wrap\">\n";
        $images = Multimedia_Util::get_all_uploaded_images();
        $content .= "<div class=\"slider-manager-left\">\n";
        $content .= "<form class=\"generic-form\" method=\"post\" action=\"#\">\n";
        $content .= "<div>\n";
        $content .= "<input id=\"slider-manager-search-image\" type=\"text\" placeholder=\"" . __('Search image', 'finesse') . " &hellip;\">\n";
        $content .= "</div>\n";
        $content .= "</form>\n";
        $content .= "<ul id=\"slider-manager-thumbs\" class=\"thumbs\">\n";
        foreach ($images as $img) {
            $images = wp_get_attachment_image_src($img->ID);
            $id = $img->ID;
            $title = $img->post_title;
            $img_src = $images[0];
            $type = start_with($title, 'video-') ? 'video' : 'img';
            if ($type == 'video' /*&& $slider_type == 'cycleSlider'*/) {
                continue;
            }
            $content .= "<li data-filter=\"$title\"><span class=\"type-$type\"></span><img src=\"$img_src\" title=\"$title\" data-id=\"$id\"></li>\n";
        }
        $content .= "</ul>\n";
        $content .= "</div>\n";

        $content .= "<div id=\"slider-manager-right\" class=\"slider-manager-right\">\n";
        $content .= '<form id="slider-manager-form" class="generic-form" method="post" action="#">';
        $content .= '<input id="page-media-post-id" name="page-media-post-id" type="hidden" value="' . $post_id . '">';
        $content .= '<input id="page-media-type" type="hidden" name="page-media-type" value="slider">';

//        if ($slider_type != 'cycleSlider') {
//            $content .= '<div id="slider-manager-add-video">';
//            $content .= '<a id="slider-manager-add-embedded-video" href="#">' . __('Add Embedded Video') . '</a>';
//            $content .= '<a id="slider-manager-add-sh-video" href="#">' . __('Add Self-Hosted Video', 'finesse') . '</a>';
//            $content .= '</div>';
//        }

        $content .= '[tabs]';
        $content .= '[tab title=\'' . __('Designer', 'finesse') . '\']';
        $content .= "<ul id=\"slider-manager-slides\" class=\"thumbs\">\n";
        if (count($page_slides) == 0) {
            $content .= "<li class=\"slider-manager-slide-placeholder\">" . __('Drag your images here', 'finesse') . "</li>\n";
        } else {
            $image_not_available = Multimedia_Util::get_external_image_src('/admin/images/image-not-available.png');
            foreach ($page_slides as $page_slide) {
                $images = wp_get_attachment_image_src($page_slide->id);
                if ($images) {
                    $title = '';
                    $src = $images[0];
                } else {
                    $title = __('No Image Available', 'finesse');
                    $src = $image_not_available;
                }
                $slide_id = $page_slide->id;
                $slide_type = $page_slide->type;
                $slide_image_link = property_exists($page_slide, 'image_link') ? $page_slide->image_link : '';
                $slide_image_title = property_exists($page_slide, 'image_title') ? $page_slide->image_title : '';
                $slide_caption_title = $page_slide->caption_title;
                $slide_caption_content = $page_slide->caption_content;
                $content .= '<li data-type="' . $slide_type . '" data-id="' . $slide_id . '" data-caption-title="' . $slide_caption_title . '" data-caption-content="' . $slide_caption_content . '" data-image-link="' . $slide_image_link . '" data-image-title="' . $slide_image_title . '">';
                $content .= '<img src="' . $src . '" title="' . $title . '">';
                $content .= '<span class="type-' . $page_slide->type . '"></span>';
                if ($slider_type == 'cycleSlider') {
                    $content .= '<a class="edit-meta-slider-slide-button" href="#" title="' . __('Edit Meta', 'finesse') . '">' . __('Edit Meta', 'finesse') . '</a>';
                } else {
                    $content .= '<a class="add-caption-slider-slide-button" href="#" title="' . __('Edit Caption', 'finesse') . '">' . __('Edit Caption', 'finesse') . '</a>';
                }
                $content .= '<a class="delete-slider-slide-button" href="#" title="' . __('Delete Slide', 'finesse') . '">' . __('Delete Slide', 'finesse') . '</a>';
                $content .= '</li>';
            }
        }
        $content .= "</ul>\n";
        $content .= '[/tab]';
        $content .= '[tab title=\'' . __('Settings', 'finesse') . '\']';
        $content .= $this->get_slider_settings_form($post_id, $slider_type);
        $content .= '[/tab]';
        $content .= '[/tabs]';
        $content .= "</form>\n";
        $content .= "</div>";

        $content .= '<div >';
        $content .= '<input id="slider-save-button" type="submit" name="submit" value="' . __('Save', 'finesse') . '" class="button-primary">';
        $content .= '<input id="slider-cancel-button" type="submit" name="submit" value="' . __('Cancel', 'finesse') . '" class="button-secondary">';
        $content .= '</div>';
        $content .= "</div>";

        $content .= '<div id="page-media-add-embedded-video-dialog" title="Add Embedded Video" style="display: none">';
        $content .= $this->get_add_embedded_video_form();
        $content .= "</div>";

        $content .= '<div id="page-media-add-sh-video-dialog" title="Add Self-Hosted Video" style="display: none">';
        $content .= $this->get_add_sh_video_form();
        $content .= "</div>";

        $content .= $this->get_add_slide_caption_dialog();
        $content .= $this->get_add_slide_meta_dialog();
        echo do_shortcode($content);
    }

    private function get_slider_settings_form($post_id, $slider_type)
    {
        $slider_configuration = Page_Media_Manager::get_page_media_config($post_id);
        if ($slider_type == 'flexSlider') {
            if ($slider_configuration) {
                $slider_settings = $slider_configuration->settings;

                $animation_fade = $slider_settings->animation == 'fade' ? ' selected' : '';
                $animation_slide = $slider_settings->animation == 'slide' ? ' selected' : '';
                $animation_speed = $slider_settings->animation_speed;
                $easing_swing = $slider_settings->easing == 'swing' ? ' selected' : '';
                $easing_linear = $slider_settings->easing == 'linear' ? ' selected' : '';
                $animation_loop = $slider_settings->animation_loop == 'true' ? ' checked' : '';
                $slide_show = $slider_settings->slide_show == 'true' ? ' checked' : '';
                $slide_show_speed = $slider_settings->slide_show_speed;
                $randomize = $slider_settings->randomize == 'true' ? ' checked' : '';
                $pause_on_hover = $slider_settings->pause_on_hover == 'true' ? ' checked' : '';
            } else {
                $animation_fade = '';
                $animation_slide = '';
                $animation_speed = '600';
                $easing_swing = '';
                $easing_linear = '';
                $animation_loop = ' checked';
                $slide_show = ' checked';
                $slide_show_speed = '7000';
                $randomize = '';
                $pause_on_hover = ' checked';
            }
            $content = '<fieldset>';
            $content .= '<input id="slider-manager-slider-type" name="slider-manager-slider-type" type="hidden" value="' . $slider_type . '">';
            $content .= '<div>';
            $content .= '<label for="slider-manager-slider-settings-animation">' . __('Animation', 'finesse') . ':</label>';
            $content .= '<select id="slider-manager-slider-settings-animation" name="slider-manager-slider-settings-animation" class="required">';
            $content .= '<option value="fade"' . $animation_fade . '>' . __('Fade', 'finesse') . '</option>';
            $content .= '<option value="slide"' . $animation_slide . '>' . __('Slide', 'finesse') . '</option>';
            $content .= '</select>';
            $content .= '</div>';
            $content .= '<div>';
            $content .= '<label for="slider-manager-slider-settings-animspeed">' . __('Animation speed', 'finesse') . ':</label>';
            $content .= '<input id="slider-manager-slider-settings-animspeed" name="slider-manager-slider-settings-animspeed" type="text" value="' . $animation_speed . '" class="required number">';
            $content .= '</div>';
            $content .= '<div>';
            $content .= '<label for="slider-manager-slider-settings-easing">' . __('Easing', 'finesse') . ':</label>';
            $content .= '<select id="slider-manager-slider-settings-easing" name="slider-manager-slider-settings-easing" class="required">';
            $content .= '<option value="swing"' . $easing_swing . '>' . __('Swing', 'finesse') . '</option>';
            $content .= '<option value="linear"' . $easing_linear . '>' . __('Linear', 'finesse') . '</option>';
            $content .= '</select>';
            $content .= '</div>';
            $content .= '<div>';
            $content .= '<input id="slider-manager-slider-settings-animloop" name="slider-manager-slider-settings-animloop" type="checkbox"' . $animation_loop . '>';
            $content .= '<label for="slider-manager-slider-settings-animloop">' . __('Animation loop', 'finesse') . '</label>';
            $content .= '</div>';
            $content .= '<div>';
            $content .= '<input id="slider-manager-slider-settings-slideshow" name="slider-manager-slider-settings-slideshow" type="checkbox"' . $slide_show . '>';
            $content .= '<label for="slider-manager-slider-settings-slideshow">' . __('Animate slider automatically', 'finesse') . '</label>';
            $content .= '</div>';
            $content .= '<div>';
            $content .= '<label for="slider-manager-slider-settings-slideshowspeed">' . __('Slide show speed', 'finesse') . ':</label>';
            $content .= '<input id="slider-manager-slider-settings-slideshowspeed" name="slider-manager-slider-settings-slideshowspeed" type="text" value="' . $slide_show_speed . '" class="required number">';
            $content .= '</div>';
            $content .= '<div>';
            $content .= '<input id="slider-manager-slider-settings-random" name="slider-manager-slider-settings-random" type="checkbox"' . $randomize . '>';
            $content .= '<label for="slider-manager-slider-settings-random">' . __('Randomize slide order', 'finesse') . '</label>';
            $content .= '</div>';
            $content .= '<div>';
            $content .= '<input id="slider-manager-slider-settings-pausehover" name="slider-manager-slider-settings-pausehover" type="checkbox"' . $pause_on_hover . '>';
            $content .= '<label for="slider-manager-slider-settings-pausehover">' . __('Pause the slideshow when hovering over slider', 'finesse') . '</label>';
            $content .= '</div>';
            $content .= '</fieldset>';
            return $content;
        } elseif ($slider_type == 'cycleSlider') {
            if ($slider_configuration) {
                $slider_settings = $slider_configuration->settings;

                if (property_exists($slider_settings, 'enabled')) {
                    $enabled_checked = $slider_settings->enabled == 'true' ? ' checked' : '';
                    $settings_styles = $slider_settings->enabled == 'true' ? '' : ' style="display:none;"';
                } else {
                    $enabled_checked = ' checked';
                    $settings_styles = '';
                }

                $pause_on_pager_hover = $slider_settings->pause_on_pager_hover == 'true' ? ' checked' : '';
                $nowrap = $slider_settings->nowrap == 'false' ? ' checked' : '';
                $fx_fade = $slider_settings->fx == 'fade' ? ' selected' : '';
                $fx_sh = $slider_settings->fx == 'scrollHorizontal' ? ' selected' : '';
                $fx_sr = $slider_settings->fx == 'scrollRight' ? ' selected' : '';
                $animation_speed = $slider_settings->speed;
                $transition_timeout = $slider_settings->timeout;
                $slide_show = $slider_settings->slide_show == 'true' ? ' checked' : '';
                $pause_on_hover = $slider_settings->pause == 'true' ? ' checked' : '';
            } else {
                $enabled_checked = ' checked';
                $pause_on_pager_hover = ' checked';
                $nowrap = ' checked';
                $fx_sh = ' selected';
                $fx_fade = '';
                $fx_sr = '';
                $animation_speed = '600';
                $transition_timeout = '2000';
                $slide_show = '';
                $pause_on_hover = ' checked';
                $settings_styles = '';
            }
            $content = '<fieldset>';
            $content .= '<input id="slider-manager-slider-type" name="slider-manager-slider-type" type="hidden" value="' . $slider_type . '">';
            $content .= '<div>';
            $content .= '<input id="slider-manager-slider-enabled" name="slider-manager-slider-enabled" type="checkbox"' . $enabled_checked . '>';
            $content .= '<label for="slider-manager-slider-enabled">' . __('Sliding Enabled', 'finesse') . '</label>';
            $content .= '</div>';
            $content .= '<div class="cycle-slider-settings"' . $settings_styles . '>';
            $content .= '<label for="slider-manager-slider-settings-animation">' . __('Animation', 'finesse') . ':</label>';
            $content .= '<select id="slider-manager-slider-settings-animation" name="slider-manager-slider-settings-animation" class="required">';
            $content .= '<option value="scrollHorizontal"' . $fx_sh . '>' . __('Horizontal Scrolling', 'finesse') . '</option>';
            $content .= '<option value="fade"' . $fx_fade . '>' . __('Fade', 'finesse') . '</option>';
            $content .= '<option value="scrollRight"' . $fx_sr . '>' . __('Scroll Right', 'finesse') . '</option>';
            $content .= '</select>';
            $content .= '</div>';
            $content .= '<div class="cycle-slider-settings"' . $settings_styles . '>';
            $content .= '<label for="slider-manager-slider-settings-animspeed">' . __('Animation speed', 'finesse') . ':</label>';
            $content .= '<input id="slider-manager-slider-settings-animspeed" name="slider-manager-slider-settings-animspeed" type="text" value="' . $animation_speed . '" class="required number">';
            $content .= '</div>';
            $content .= '<div class="cycle-slider-settings"' . $settings_styles . '>';
            $content .= '<input id="slider-manager-slider-settings-circular" name="slider-manager-slider-settings-circular" type="checkbox"' . $nowrap . '>';
            $content .= '<label for="slider-manager-slider-settings-circular">' . __('Circular slider', 'finesse') . '</label>';
            $content .= '</div>';
            $content .= '<div class="cycle-slider-settings"' . $settings_styles . '>';
            $content .= '<input id="slider-manager-slider-settings-pph" name="slider-manager-slider-settings-pph" type="checkbox"' . $pause_on_pager_hover . '>';
            $content .= '<label for="slider-manager-slider-settings-pph">' . __('Pause when hovering over pager link', 'finesse') . '</label>';
            $content .= '</div>';
            $content .= '<div class="cycle-slider-settings"' . $settings_styles . '>';
            $content .= '<input id="slider-manager-slider-settings-slideshow" name="slider-manager-slider-settings-slideshow" type="checkbox"' . $slide_show . '>';
            $content .= '<label for="slider-manager-slider-settings-slideshow">' . __('Slide show', 'finesse') . '</label>';
            $content .= '</div>';
            $content .= '<div class="cycle-slider-settings"' . $settings_styles . '>';
            $content .= '<label for="slider-manager-slider-settings-slideshowspeed">' . __('Slide show speed', 'finesse') . ':</label>';
            $content .= '<input id="slider-manager-slider-settings-slideshowspeed" name="slider-manager-slider-settings-slideshowspeed" type="text" value="' . $transition_timeout . '" class="required number">';
            $content .= '</div>';
            $content .= '<div class="cycle-slider-settings"' . $settings_styles . '>';
            $content .= '<input id="slider-manager-slider-settings-pausehover" name="slider-manager-slider-settings-pausehover" type="checkbox"' . $pause_on_hover . '>';
            $content .= '<label for="slider-manager-slider-settings-pausehover">' . __('Pause on hover (active only when the slide-show is on)', 'finesse') . '</label>';
            $content .= '</div>';
            $content .= '</fieldset>';
            return $content;
        } else {
            return '';
        }
    }

    private function get_add_embedded_video_form($media_config = null)
    {
        $display_remove_button = false;
        if (isset($media_config) && $media_config->video_type == 'embedded') {
            $display_remove_button = true;
            $embedded_code = base64_decode($media_config->embedded_code_base64);
        } else {
            $embedded_code = '';
        }
        $content = '<form id="page-media-add-embedded-video-form" class="generic-form" method="post" action="#">';
        $content .= '<fieldset>';
        $content .= '<div>';
        $content .= '<label for="page-media-add-embedded-video-id">' . __('Video ID', 'finesse') . ':</label>';
        $content .= '<input id="page-media-add-embedded-video-id" name="page-media-add-embedded-video-id" type="text" class="required">';
        $content .= '</div>';
        $content .= '<div>';
        $content .= '<label for="page-media-add-embedded-embed-code">' . __('Embed Code', 'finesse') . ':</label>';
        $content .= '<textarea id="page-media-add-embedded-embed-code" name="page-media-add-embedded-embed-code" class="required">' . $embedded_code . '</textarea>';
        $content .= '</div>';
        $content .= '<div >';
        $content .= '<input id="page-media-add-embedded-video-form-submit" type="button" value="' . __('Save', 'finesse') . '" class="button-primary">';
        $content .= '<input id="page-media-add-embedded-video-form-cancel" type="button" value="' . __('Cancel', 'finesse') . '" class="button-secondary">';
        if (isset($media_config)) {
            if ($display_remove_button) {
                $content .= '<a id="page-media-add-embedded-video-form-remove" class="deletion" href="#">' . __('Remove Video', 'finesse') . '</a>';
            }
        }
        $content .= '</div>';
        $content .= '</fieldset>';
        $content .= '</form>';
        return $content;
    }

    private function get_add_sh_video_form($media_config = null)
    {
        $display_remove_button = false;
        if (isset($media_config) && $media_config->video_type == 'sh') {
            $display_remove_button = true;
            $poster = $media_config->sh_poster;
            $m4v = $media_config->sh_m4v;
            $webm = $media_config->sh_webm;
            $ogg = $media_config->sh_ogg;
            $subtitle = $media_config->sh_subtitle;
            $chapter = $media_config->sh_chapter;
        } else {
            $poster = '';
            $m4v = '';
            $webm = '';
            $ogg = '';
            $subtitle = '';
            $chapter = '';
        }
        $content = '<form id="page-media-add-sh-video-form" class="generic-form" method="post" action="#">';
        $content .= '<fieldset>';
        $content .= '<div>';
        $content .= '<label for="page-media-add-sh-video-poster">' . __('Poster URL', 'finesse') . ':</label>';
        $content .= '<input id="page-media-add-sh-video-poster" name="page-media-add-sh-video-poster" type="text" value="' . $poster . '" class="required">';
        $content .= '</div>';
        $content .= '<div>';
        $content .= '<label for="page-media-add-sh-video-m4v">' . __('M4V URL', 'finesse') . ':</label>';
        $content .= '<input id="page-media-add-sh-video-m4v" name="page-media-add-sh-video-m4v" type="text" value="' . $m4v . '" >';
        $content .= '</div>';
        $content .= '<div>';
        $content .= '<label for="page-media-add-sh-video-webm">' . __('WEBM URL', 'finesse') . ':</label>';
        $content .= '<input id="page-media-add-sh-video-webm" name="page-media-add-sh-video-webm" type="text" value="' . $webm . '" >';
        $content .= '</div>';
        $content .= '<div>';
        $content .= '<label for="page-media-add-sh-video-ogg">' . __('OGG URL', 'finesse') . ':</label>';
        $content .= '<input id="page-media-add-sh-video-ogg" name="page-media-add-sh-video-ogg" type="text" value="' . $ogg . '" >';
        $content .= '</div>';
        $content .= '<div>';
        $content .= '<label for="page-media-add-sh-video-subtitle">' . __('Subtitle', 'finesse') . ':</label>';
        $content .= '<input id="page-media-add-sh-video-subtitle" name="page-media-add-sh-video-subtitle" value="' . $subtitle . '" type="text">';
        $content .= '</div>';
        $content .= '<div>';
        $content .= '<label for="page-media-add-sh-video-chapter">' . __('Chapter', 'finesse') . ':</label>';
        $content .= '<input id="page-media-add-sh-video-chapter" name="page-media-add-sh-video-chapter" value="' . $chapter . '" type="text">';
        $content .= '</div>';
        $content .= '<div>';
        $content .= '<input id="page-media-add-sh-video-form-submit" type="button" value="' . __('Save', 'finesse') . '" class="button-primary">';
        $content .= '<input id="page-media-add-sh-video-form-cancel" type="button" value="' . __('Cancel', 'finesse') . '" class="button-secondary">';
        if (isset($media_config)) {
            if ($display_remove_button) {
                $content .= '<a id="page-media-add-sh-video-form-remove" class="deletion" href="#">' . __('Remove Video', 'finesse') . '</a>';
            }
        }
        $content .= '</div>';
        $content .= '</fieldset>';
        $content .= '</form>';
        return $content;
    }

    private function get_add_audio_form($media_config = null)
    {
        $post_id = $_REQUEST['post-id'];
        if (isset($media_config)) {
            $mp3 = $media_config->audio_mp3;
            $wav = $media_config->audio_wav;
            $ogg = $media_config->audio_ogg;
        } else {
            $mp3 = '';
            $wav = '';
            $ogg = '';
        }
        $content = '<form id="page-media-add-audio-form" class="generic-form" method="post" action="#">';
        $content .= '<fieldset>';
        $content .= '<div>';
        $content .= '<label for="page-media-add-audio-mp3">' . __('MP3 URL', 'finesse') . ':</label>';
        $content .= '<input id="page-media-add-audio-mp3" name="page-media-add-audio-mp3" type="text" value="' . $mp3 . '" >';
        $content .= '</div>';
        $content .= '<div>';
        $content .= '<label for="page-media-add-audio-wav">' . __('WAV URL', 'finesse') . ':</label>';
        $content .= '<input id="page-media-add-audio-wav" name="page-media-add-audio-wav" type="text" value="' . $wav . '" >';
        $content .= '</div>';
        $content .= '<div>';
        $content .= '<label for="page-media-add-audio-ogg">' . __('OGG URL', 'finesse') . ':</label>';
        $content .= '<input id="page-media-add-audio-ogg" name="page-media-add-audio-ogg" type="text" value="' . $ogg . '" >';
        $content .= '</div>';
        $content .= '<div>';
        $content .= '<input id="page-media-add-audio-form-submit" type="button" value="' . __('Save', 'finesse') . '" class="button-primary">';
        $content .= '<input id="page-media-add-audio-form-cancel" type="button" value="' . __('Cancel', 'finesse') . '" class="button-secondary">';
        $content .= '<a id="page-media-add-audio-form-remove" class="deletion" href="#">' . __('Remove Audio', 'finesse') . '</a>';
        $content .= '<input id="page-media-type" type="hidden" name="page-media-type" value="audio">';
        $content .= '<input id="page-media-post-id" name="page-media-post-id" type="hidden" value="' . $post_id . '">';
        $content .= '</div>';
        $content .= '</fieldset>';
        $content .= '</form>';
        return $content;
    }

    private function get_add_link_form($media_config = null)
    {
        $post_id = $_REQUEST['post-id'];
        if (isset($media_config)) {
            $url = $media_config->link_url;
        } else {
            $url = '#';
        }
        $content = '<form id="page-media-add-link-form" class="generic-form" method="post" action="#">';
        $content .= '<fieldset>';
        $content .= '<div>';
        $content .= '<label for="page-media-add-link-url">' . __('URL', 'finesse') . ':</label>';
        $content .= '<input id="page-media-add-link-url" name="page-media-add-link-url" type="text" value="' . $url . '" >';
        $content .= '</div>';
        $content .= '<input id="page-media-add-link-form-submit" type="button" value="' . __('Save', 'finesse') . '" class="button-primary">';
        $content .= '<input id="page-media-add-link-form-cancel" type="button" value="' . __('Cancel', 'finesse') . '" class="button-secondary">';
        $content .= '<input id="page-media-type" type="hidden" name="page-media-type" value="link">';
        $content .= '<input id="page-media-post-id" name="page-media-post-id" type="hidden" value="' . $post_id . '">';
        $content .= '</div>';
        $content .= '</fieldset>';
        $content .= '</form>';
        return $content;
    }

    private function get_add_slide_caption_dialog()
    {
        $content = '<div id="page-media-add-slide-caption-dialog" title="Edit Slide Caption" style="display: none">';
        $content .= '<form id="page-media-add-slide-caption-form" class="generic-form" method="post" action="#">';
        $content .= '<fieldset>';
        $content .= '<div>';
        $content .= '<label for="page-media-add-slide-caption-link">' . __('Image Link', 'finesse') . ':</label>';
        $content .= '<input id="page-media-add-slide-caption-link" name="page-media-add-slide-caption-link" type="text">';
        $content .= '</div>';
        $content .= '<div>';
        $content .= '<label for="page-media-add-slide-caption-title">' . __('Caption Title', 'finesse') . ':</label>';
        $content .= '<input id="page-media-add-slide-caption-title" name="page-media-add-slide-caption-title" type="text">';
        $content .= '</div>';
        $content .= '<div>';
        $content .= '<label for="page-media-add-slide-caption-content">' . __('Caption Content', 'finesse') . ':</label>';
        $content .= '<textarea id="page-media-add-slide-caption-content" name="page-media-add-slide-caption-content"></textarea>';
        $content .= '</div>';
        $content .= '<div >';
        $content .= '<input id="page-media-add-slide-caption-form-submit" type="button" value="' . __('Save', 'finesse') . '" class="button-primary">';
        $content .= '<input id="page-media-add-slide-caption-form-cancel" type="button" value="' . __('Cancel', 'finesse') . '" class="button-secondary">';
        $content .= '</div>';
        $content .= '</fieldset>';
        $content .= '</form>';
        $content .= '</div>';
        return $content;
    }

    private function get_add_slide_meta_dialog()
    {
        $content = '<div id="page-media-add-slide-meta-dialog" title="Edit Meta" style="display: none">';
        $content .= '<form id="page-media-add-slide-meta-form" class="generic-form" method="post" action="#">';
        $content .= '<fieldset>';
        $content .= '<div>';
        $content .= '<label for="page-media-add-slide-meta-title">' . __('Image Title', 'finesse') . ':</label>';
        $content .= '<input id="page-media-add-slide-meta-title" name="page-media-add-slide-meta-title" type="text">';
        $content .= '</div>';
        $content .= '<div >';
        $content .= '<input id="page-media-add-slide-meta-form-submit" type="button" value="' . __('Save', 'finesse') . '" class="button-primary">';
        $content .= '<input id="page-media-add-slide-meta-form-cancel" type="button" value="' . __('Cancel', 'finesse') . '" class="button-secondary">';
        $content .= '</div>';
        $content .= '</fieldset>';
        $content .= '</form>';
        $content .= '</div>';
        return $content;
    }

    function save_post_media()
    {
        $post_id = $_REQUEST['post-id'];
        $slider_settings = $_REQUEST['settings'];

        $old_value = get_post_meta($post_id, Page_Media_Manager::$PAGE_MEDIA_SETTINGS_META_KEY, true);
        update_post_meta($post_id, Page_Media_Manager::$PAGE_MEDIA_SETTINGS_META_KEY, $slider_settings, $old_value);
    }

    static function get_page_media_config($post_id)
    {
        $slider_settings = get_post_meta($post_id, Page_Media_Manager::$PAGE_MEDIA_SETTINGS_META_KEY, true);
        if (isset($slider_settings) && !empty($slider_settings)) {
            return json_decode($slider_settings, false);
        } else {
            return false;
        }
    }

    function add_embedded_video()
    {
        try {
            $content = '';
            $post_id = $_POST['post-id'];
            $media_type = $_POST['media-type'];
            $video_id = $_POST['video-id'];
            $embed_code_base64 = $_POST['embed-code-base64'];

            if ($media_type == 'slider') {
                $embed_code = base64_decode($embed_code_base64);
                $sc = '[evideo]' . $embed_code . '[/evideo]';
                $post = Embedded_Video::save($post_id, $video_id, $sc);
                $images = wp_get_attachment_image_src($post->ID);

                $post_id = $post->ID;
                $post_title = $post->post_title;
                $content = "<li data-filter=\"" . $post_title . "\"><span class=\"type-video\"></span><img src=\"" . $images[0] . "\" title=\"" . $post_title . "\" data-id=\"" . $post_id . "\"></li>\n";
            } elseif ($media_type == 'video') {
                $media_settings = '{"media_type": "video", "video_type": "embedded", "embedded_code_base64": "' . $embed_code_base64 . '"}';
                $old_value = get_post_meta($post_id, Page_Media_Manager::$PAGE_MEDIA_SETTINGS_META_KEY, true);
                update_post_meta($post_id, Page_Media_Manager::$PAGE_MEDIA_SETTINGS_META_KEY, $media_settings, $old_value);
                $content = $media_settings;
            }

            echo $content;
            die();
        } catch (Exception $e) {
            header($_SERVER['SERVER_PROTOCOL'] . " 500 Internal Server Error");
            echo $e->getMessage();
            die();
        }
    }

    function add_sh_video()
    {
        try {
            $content = '';
            $post_id = $_POST['post-id'];
            $media_type = $_POST['media-type'];
            $poster = $_POST['poster'];
            $m4v = $_POST['m4v'];
            $webm = $_POST['webm'];
            $ogg = $_POST['ogg'];
            $subtitle = $_POST['subtitle'];
            $chapter = $_POST['chapter'];

            if ($media_type == 'slider') {
                $sc = '[video poster="' . $poster . '"]';
                if (strlen($m4v) > 0) {
                    $sc .= '[video_source format="mp4" src="' . $m4v . '"][/video_source]';
                }
                if (strlen($webm) > 0) {
                    $sc .= '[video_source format="webm" src="' . $webm . '"][/video_source]';
                }
                if (strlen($ogg) > 0) {
                    $sc .= '[video_source format="ogg" src="' . $ogg . '"][/video_source]';
                }
                if (strlen($subtitle) > 0) {
                    $sc .= '[video_track type="subtitles" src="' . $subtitle . '" lang="en"][/video_track]';
                }
                if (strlen($chapter) > 0) {
                    $sc .= '[video_track type="chapters" src="' . $chapter . '" lang="en"][/video_track]';
                }
                $sc .= '[/video]';
                $post = Embedded_Video::save($post_id, 'self-hosted', $sc);
                $images = wp_get_attachment_image_src($post->ID);

                $post_id = $post->ID;
                $post_title = $post->post_title;
                $content = "<li data-filter=\"" . $post_title . "\"><span class=\"type-video\"></span><img src=\"" . $images[0] . "\" title=\"" . $post_title . "\" data-id=\"" . $post_id . "\"></li>\n";
            } elseif ($media_type == 'video') {
                $media_settings = '{"media_type": "video"';
                $media_settings .= ', "video_type": "sh"';
                $media_settings .= ', "sh_poster": "' . $poster . '"';
                $media_settings .= ', "sh_m4v": "' . $m4v . '"';
                $media_settings .= ', "sh_webm": "' . $webm . '"';
                $media_settings .= ', "sh_ogg": "' . $ogg . '"';
                $media_settings .= ', "sh_subtitle": "' . $subtitle . '"';
                $media_settings .= ', "sh_chapter": "' . $chapter . '"}';
                $old_value = get_post_meta($post_id, Page_Media_Manager::$PAGE_MEDIA_SETTINGS_META_KEY, true);
                update_post_meta($post_id, Page_Media_Manager::$PAGE_MEDIA_SETTINGS_META_KEY, $media_settings, $old_value);

                $content = $media_settings;
            }

            echo $content;
        } catch (Exception $e) {
            header($_SERVER['SERVER_PROTOCOL'] . " 500 Internal Server Error");
            echo $e->getMessage();
        }
        die();
    }

    function add_audio()
    {
        try {
            $post_id = $_POST['post-id'];
            $mp3 = $_POST['mp3'];
            $wav = $_POST['wav'];
            $ogg = $_POST['ogg'];

            $media_settings = '{"media_type": "audio"';
            $media_settings .= ', "audio_mp3": "' . $mp3 . '"';
            $media_settings .= ', "audio_wav": "' . $wav . '"';
            $media_settings .= ', "audio_ogg": "' . $ogg . '"}';
            $old_value = get_post_meta($post_id, Page_Media_Manager::$PAGE_MEDIA_SETTINGS_META_KEY, true);
            update_post_meta($post_id, Page_Media_Manager::$PAGE_MEDIA_SETTINGS_META_KEY, $media_settings, $old_value);

            echo $media_settings;
        } catch (Exception $e) {
            header($_SERVER['SERVER_PROTOCOL'] . " 500 Internal Server Error");
            echo $e->getMessage();
        }
        die();
    }

    function add_link()
    {
        try {
            $post_id = $_POST['post-id'];
            $link = $_POST['link'];

            $media_settings = '{"media_type": "link"';
            $media_settings .= ' ,"link_url": "' . $link . '"}';
            $old_value = get_post_meta($post_id, Page_Media_Manager::$PAGE_MEDIA_SETTINGS_META_KEY, true);
            update_post_meta($post_id, Page_Media_Manager::$PAGE_MEDIA_SETTINGS_META_KEY, $media_settings, $old_value);

            echo $media_settings;
        } catch (Exception $e) {
            header($_SERVER['SERVER_PROTOCOL'] . " 500 Internal Server Error");
            echo $e->getMessage();
        }
        die();
    }

    function remove_post_media()
    {
        try {
            $post_id = $_POST['post-id'];

            $old_value = get_post_meta($post_id, Page_Media_Manager::$PAGE_MEDIA_SETTINGS_META_KEY, true);
            update_post_meta($post_id, Page_Media_Manager::$PAGE_MEDIA_SETTINGS_META_KEY, '', $old_value);
            echo '';
        } catch (Exception $e) {
            header($_SERVER['SERVER_PROTOCOL'] . " 500 Internal Server Error");
            echo $e->getMessage();
        }
        die();
    }

    static function render_page_media($settings = array())
    {
        $post = isset($GLOBALS['post']) ? $GLOBALS['post'] : null;
        $settings = array_merge(array(
            'post' => $post,
            'lightbox' => true,
            'is_thumbnail' => false,
            'echo' => true), $settings);
        $content = '';
        $post = $settings['post'];
        $use_lightbox = $settings['lightbox'];
        $is_thumbnail = $settings['is_thumbnail'];
        $echo = $settings['echo'];
        $post_type = $post->post_type;

        $media_config = Page_Media_Manager::get_page_media_config($post->ID);
        if ($media_config) {
            $media_type = $media_config->media_type;
            if ($media_type == 'slider') {
                $slider_type = $media_config->slider_type;
                if ($slider_type === 'flexSlider') {
                    $content .= Page_Media_Manager::render_flex_slider($media_config);
                } elseif ($slider_type === 'cycleSlider') {
                    if ($is_thumbnail) {
                        $content .= Page_Media_Manager::render_cycle_slider($media_config, 'custom-medium-post-thumb', $use_lightbox ? null : $post);
                    } else {
                        if ($post_type == 'portfolio') {
                            $content .= Page_Media_Manager::render_cycle_slider($media_config, 'custom-portfolio-post-thumb', $use_lightbox ? null : $post);
                        } elseif ($post_type == 'post') {
                            $content .= Page_Media_Manager::render_cycle_slider($media_config, 'custom-blog-post-thumb', $use_lightbox ? null : $post);
                        }
                    }
                }
            } elseif ($media_type == 'video') {
                $video_type = $media_config->video_type;
                if ($video_type == 'embedded') {
                    $embedded_code = base64_decode($media_config->embedded_code_base64);
                    $sc = '[evideo]' . $embedded_code . '[/evideo]';
                    $content = do_shortcode($sc);
                } elseif ($video_type == 'sh') {
                    $poster = $media_config->sh_poster;
                    $m4v = $media_config->sh_m4v;
                    $webm = $media_config->sh_webm;
                    $ogg = $media_config->sh_ogg;
                    $subtitle = $media_config->sh_subtitle;
                    $chapter = $media_config->sh_chapter;

                    $sc = '[video ';
                    $sc .= Self_Hosted_Video_Shortcode::$POSTER_ATTR . '="' . $poster . '" ';
                    if ($is_thumbnail) {
                        $sc .= Self_Hosted_Video_Shortcode::$THUMB_ATTR . '="true" ';
                    }
                    $sc .= ']';
                    if (!empty($m4v)) {
                        $sc .= '[video_source ';
                        $sc .= Self_Hosted_Video_Shortcode::$FORMAT_ATTR . '="mp4" ';
                        $sc .= Self_Hosted_Video_Shortcode::$SRC_ATTR . '="' . $m4v . '" ';
                        $sc .= '][/video_source]';
                    }
                    if (!empty($webm)) {
                        $sc .= '[video_source ';
                        $sc .= Self_Hosted_Video_Shortcode::$FORMAT_ATTR . '="webm" ';
                        $sc .= Self_Hosted_Video_Shortcode::$SRC_ATTR . '="' . $webm . '" ';
                        $sc .= '][/video_source]';
                    }
                    if (!empty($ogg)) {
                        $sc .= '[video_source ';
                        $sc .= Self_Hosted_Video_Shortcode::$FORMAT_ATTR . '="ogg" ';
                        $sc .= Self_Hosted_Video_Shortcode::$SRC_ATTR . '="' . $ogg . '" ';
                        $sc .= '][/video_source]';
                    }
                    if (!empty($subtitle)) {
                        $sc .= '[video_track ';
                        $sc .= Self_Hosted_Video_Shortcode::$TYPE_ATTR . '="subtitles" ';
                        $sc .= Self_Hosted_Video_Shortcode::$LANG_ATTR . '="en" ';
                        $sc .= Self_Hosted_Video_Shortcode::$SRC_ATTR . '="' . $subtitle . '" ';
                        $sc .= '][/video_track]';
                    }
                    if (!empty($chapter)) {
                        $sc .= '[video_track ';
                        $sc .= Self_Hosted_Video_Shortcode::$TYPE_ATTR . '="chapters" ';
                        $sc .= Self_Hosted_Video_Shortcode::$LANG_ATTR . '="en" ';
                        $sc .= Self_Hosted_Video_Shortcode::$SRC_ATTR . '="' . $chapter . '" ';
                        $sc .= '][/video_track]';
                    }
                    $sc .= '[/video] ';
                    $content = do_shortcode($sc);
                }
            } elseif ($media_type == 'audio') {
                $mp3 = $media_config->audio_mp3;
                $wav = $media_config->audio_wav;
                $ogg = $media_config->audio_ogg;

                $sc = '[audio ';
                if ($is_thumbnail) {
                    $sc .= Audio_Shortcode::$THUMB_ATTR . '="true" ';
                }
                $sc .= ']';
                if (!empty($mp3)) {
                    $sc .= '[audio_source ';
                    $sc .= Audio_Shortcode::$FORMAT_ATTR . '="mp3" ';
                    $sc .= Audio_Shortcode::$SRC_ATTR . '="' . $mp3 . '" ';
                    $sc .= '][/audio_source]';
                }
                if (!empty($wav)) {
                    $sc .= '[audio_source ';
                    $sc .= Audio_Shortcode::$FORMAT_ATTR . '="wav" ';
                    $sc .= Audio_Shortcode::$SRC_ATTR . '="' . $wav . '" ';
                    $sc .= '][/audio_source]';
                }
                if (!empty($ogg)) {
                    $sc .= '[audio_source ';
                    $sc .= Audio_Shortcode::$FORMAT_ATTR . '="ogg" ';
                    $sc .= Audio_Shortcode::$SRC_ATTR . '="' . $ogg . '" ';
                    $sc .= '][/audio_source]';
                }
                $sc .= '[/audio] ';
                $content = do_shortcode($sc);
            }
        }

        if (empty($content) && $post_type == 'post' && has_post_thumbnail($post->ID)) {
            $post_url = get_permalink($post->ID);
            $title = get_the_title($post->ID);
            $img_title = get_the_title(get_post_thumbnail_id($post->ID));
            $sc = '[img ';
            $sc .= Image_Shortcode::$SRC_ATTR . '="' . $img_title . '" ';
            $sc .= Image_Shortcode::$TITLE_ATTR . '="' . $title . '" ';
            if ($is_thumbnail) {
                $sc .= Image_Shortcode::$SIZE_ATTR . '="thumb" ';
            } else {
                $sc .= Image_Shortcode::$SIZE_ATTR . '="post" ';
            }
            if ($use_lightbox) {
                $sc .= Image_Shortcode::$LIGHTBOX_ATTR . '="true" ';
            } else {
                $sc .= Image_Shortcode::$HREF_ATTR . '="' . $post_url . '" ';
            }
            $sc .= '][/img] ';
            $content = do_shortcode($sc);
        }

        if ($echo) {
            echo $content;
        } else {
            return $content;
        }
    }

    private static function render_flex_slider($slider_settings)
    {
        $slides = $slider_settings->slides;
        $settings = $slider_settings->settings;
        $flex_slider_id = uniqid();
        $content = '<div class="flex-container">' . "\n";
        $content .= '<div id="' . $flex_slider_id . '" class="flexslider">' . "\n";
        $content .= '<ul class="slides">' . "\n";

        foreach ($slides as $slide) {
            $slide_id = $slide->id;
            $slide_type = $slide->type;
            $slide_image_link = property_exists($slide, 'image_link') ? $slide->image_link : '';
            $slide_caption_title = $slide->caption_title;
            $slide_caption_content = $slide->caption_content;
            $image_full = wp_get_attachment_image_src($slide_id, 'custom-slider-thumb', false);
            $alt_text = get_post_meta($slide_id , '_wp_attachment_image_alt', true);

            if ($image_full) {
                $content .= '<li>' . "\n";
                if (strlen($slide_image_link) > 0) {
                    $slide_image_link = base64_decode($slide_image_link);
                    if (is_numeric($slide_image_link)) {
                        $slide_image_link = get_permalink(intval($slide_image_link));
                    }
                    $content .= '<a href="' . $slide_image_link . '"><img src="' . $image_full[0] . '" alt="'.$alt_text.'">' . "</a>\n";
                } else {
                    $content .= '<img src="' . $image_full[0] . '" alt="'.$alt_text.'">' . "\n";
                }
                if (strlen($slide_caption_title) > 0 || strlen($slide_caption_content) > 0) {
                    $content .= '<div class="flex-caption">' . "\n";
                    if (strlen($slide_caption_title) > 0) {
                        $slide_caption_title = do_shortcode(base64_decode($slide_caption_title));
                        $slide_caption_title = ___($slide_caption_title);
                        $content .= '<h2>' . $slide_caption_title . '</h2>' . "\n";
                    }
                    if (strlen($slide_caption_content) > 0) {
                        $slide_caption_content = base64_decode($slide_caption_content);
                        if (start_with(trim($slide_caption_content), '[') ||
                            start_with(trim($slide_caption_content), '<form')
                        ) {
                            $slide_caption_content = do_shortcode($slide_caption_content);
                            $content .= $slide_caption_content . "\n";
                        } else {
                            $slide_caption_content = ___($slide_caption_content);
                            $content .= '<p>' . $slide_caption_content . '</p>' . "\n";
                        }
                    }
                    $content .= '</div>' . "\n";
                }
                $content .= '</li>' . "\n";
            }
        }

        $content .= '</ul>' . "\n";
        $content .= '</div>' . "\n";
        $content .= '</div>' . "\n";

        $slider_js_config = '{';
        $slider_js_config .= 'pauseOnHover:' . $settings->pause_on_hover;
        $slider_js_config .= ', animation: "' . $settings->animation . '"';
        $slider_js_config .= ', animationSpeed: ' . $settings->animation_speed;
        $slider_js_config .= ', easing: "' . $settings->easing . '"';
        $slider_js_config .= ', animationLoop: ' . $settings->animation_loop;
        $slider_js_config .= ', slideshow: ' . $settings->slide_show;
        $slider_js_config .= ', slideshowSpeed: ' . $settings->slide_show_speed;
        $slider_js_config .= ', randomize: ' . $settings->randomize;
        $slider_js_config .= '}';
        $content .= "<script type=\"text/javascript\">
            if(!document['flexSliderSettings']){
                document['flexSliderSettings'] = [];
            }
            document['flexSliderSettings'].push({
                sliderId: '" . $flex_slider_id . "',
                config: $slider_js_config
            });
		</script>";
        return $content;
    }

    private static function render_cycle_slider($slider_config, $thumb_size, $post = null)
    {
        $use_lightbox = ($post == null);
        $content = '';

        $slides = $slider_config->slides;
        $settings = $slider_config->settings;
        $slides_count = count($slides);
        if ($slides_count == 1) {
            foreach ($slides as $slide) {
                $slide_id = $slide->id;
                $slide_type = $slide->type;
                if ($slide_type == 'img') {
                    $slide_image_title = property_exists($slide, 'image_title') ? ___(base64_decode($slide->image_title)) : '';
                    $image_thumb = wp_get_attachment_image_src($slide_id, $thumb_size, false);
                    $image_full = wp_get_attachment_image_src($slide_id, 'full', false);
                    if ($image_thumb && $image_full) {
                        $image_title = empty($slide_image_title) ? get_the_title($slide_id) : $slide_image_title;
                        $content .= '<div class="entry-image">' . "\n";
                        if ($use_lightbox) {
                            $content .= '<a class="fancybox" href="' . $image_full[0] . '" title="' . $image_title . '"><span class="overlay zoom"></span><img src="' . $image_thumb[0] . '" alt="' . $image_title . '"></a>' . "\n";
                        } else {
                            $link = get_permalink($post->ID);
                            $title = empty($slide_image_title) ? get_the_title($post->ID) : $slide_image_title;
                            $content .= '<a href="' . $link . '" title="' . $title . '"><span class="overlay link"></span><img src="' . $image_thumb[0] . '" alt="' . $title . '"></a>';
                        }
                        $content .= '</div>' . "\n";
                    }
                } elseif ($slide_type == 'video') {
                    $content .= Embedded_Video::get_embedded_code($slide_id) . "\n";
                }
            }
        } elseif ($slides_count > 1) {
            if (property_exists($settings, 'enabled')) {
                $slider_enabled = $settings->enabled == 'true';
            } else {
                $slider_enabled = true;
            }
            $cycle_slider_id = uniqid();
            $div_class = $slider_enabled ? 'entry-slider' : 'entry-image';
            $content .= '<div id="' . $cycle_slider_id . '" class="' . $div_class . '">' . "\n";
            if ($slider_enabled) {
                $content .= '<ul>' . "\n";
            }
            $gallery_name = uniqid();
            foreach ($slides as $i => $slide) {
                $media_content = '';

                $slide_id = $slide->id;
                $slide_type = $slide->type;
                if ($slide_type == 'img') {
                    $slide_image_title = property_exists($slide, 'image_title') ? ___(base64_decode($slide->image_title)) : '';
                    $image_thumb = wp_get_attachment_image_src($slide_id, $thumb_size, false);
                    $image_full = wp_get_attachment_image_src($slide_id, 'full', false);
                    if ($image_thumb && $image_full) {
                        if ($use_lightbox) {
                            if ($slider_enabled) {
                                $a_class = 'fancybox';
                            } else {
                                $a_class = $i > 0 ? 'fancybox invisible' : 'fancybox';
                            }
                            $image_title = empty($slide_image_title) ? get_the_title($slide_id) : $slide_image_title;
                            $media_content .= '<a class="' . $a_class . '" data-fancybox-group="' . $gallery_name . '" href="' . $image_full[0] . '" title="' . $image_title . '"><span class="overlay zoom"></span><img src="' . $image_thumb[0] . '" alt="' . $image_title . '"></a>';
                        } else {
                            $class_attr = (!$slider_enabled && $i > 0) ? ' class="invisible"' : '';
                            $link = get_permalink($post->ID);
                            $title = empty($slide_image_title) ? get_the_title($post->ID) : $slide_image_title;
                            $media_content .= '<a' . $class_attr . ' href="' . $link . '" title="' . $title . '"><span class="overlay link"></span><img src="' . $image_thumb[0] . '" alt="' . $title . '"></a>';
                        }
                    }
                } elseif ($slide_type == 'video') {
                    $media_content .= Embedded_Video::get_embedded_code($slide_id);
                }

                if ($slider_enabled) {
                    $li_style = $i > 0 ? ' style="display: none;"' : '';
                    $content .= '<li' . $li_style . '>' . "\n";
                    $content .= $media_content . "\n";
                    $content .= '</li>' . "\n";
                } else {
                    $content .= $media_content . "\n";
                }
            }
            if ($slider_enabled) {
                $content .= '</ul>' . "\n";
            }
            $content .= '</div>' . "\n";

            if ($slider_enabled) {
                $slider_js_config = '{';
                $slider_js_config .= 'fx: "' . $settings->fx . '"';
                $slider_js_config .= ', speed: ' . $settings->speed;
                $slider_js_config .= ', nowrap: ' . $settings->nowrap;
                $slider_js_config .= ', pauseOnPagerHover: ' . $settings->pause_on_pager_hover;
                $slider_js_config .= ', timeout: ' . (($settings->slide_show == 'false') ? '0' : $settings->timeout);
                $slider_js_config .= ', pause: ' . $settings->pause;
                $slider_js_config .= '}';
                $content .= "<script type=\"text/javascript\">
                    if(!document['cycleSliderSettings']){
                        document['cycleSliderSettings'] = [];
                    }
                    document['cycleSliderSettings'].push({
                        sliderId: '" . $cycle_slider_id . "',
                        config: $slider_js_config
                    });
                </script>";
            }
        }
        return $content;
    }

}
