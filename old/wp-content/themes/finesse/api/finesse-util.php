<?php

if (!function_exists('get_theme_option')) {
    function get_theme_option($option_name)
    {
        global $theme_options;
        $all_options = wp_load_alloptions();
        if (!array_key_exists($option_name, $all_options)) {
            $return_value = $theme_options->get_option_value($option_name);
        } else {
            $return_value = $all_options[$option_name];
        }
        return maybe_unserialize($return_value);
    }
}

if (!function_exists('get_site_color_schema')) {
    function get_site_color_schema()
    {
        global $theme_options;
        $custom_site_color = $theme_options->get_option_value('finesse_custom_color_schema');
        if (isset($custom_site_color) && !empty($custom_site_color)) {
            $custom_site_color = str_replace('.css', '', $custom_site_color);
            return $custom_site_color;
        } else {
            $site_color = $theme_options->get_option_value('finesse_color_schema');
            if (!isset($site_color) || strlen($site_color) == 0) {
                $site_color = 'red';
            }
            return $site_color;
        }
    }
}

if (!function_exists('is_internationalization_on')) {
    function is_internationalization_on()
    {
        global $theme_options;
        $on = $theme_options->get_option_value('finesse_internationalization_enabled');
        return strtolower($on) == 'on';
    }
}

if (!function_exists('get_header_logo')) {
    function get_header_logo()
    {
        global $theme_options;
        $logo = $theme_options->get_option_value('finesse_header_logo');
        if (isset($logo) && strlen($logo) > 0) {
            return $logo;
        } else {
            return '#';
        }
    }
}

if (!function_exists('get_tagline')) {
    function get_tagline()
    {
        global $theme_options;
        $tagline = $theme_options->get_option_value('finesse_tagline');
        if (isset($tagline) && strlen($tagline) > 0) {
            return ___($tagline);
        } else {
            return '';
        }
    }
}

if (!function_exists('has_header_custom_code')) {
    function has_header_custom_code()
    {
        global $theme_options;
        $header_type = $theme_options->get_option_value('finesse_header_type');
        return (isset($header_type) && ($header_type == 'custom'));
    }
}

if (!function_exists('get_header_custom_code')) {
    function get_header_custom_code()
    {
        global $theme_options;
        return $theme_options->get_option_value('finesse_header_custom_code');
    }
}

if (!function_exists('is_custom_seo_enabled')) {
    function is_custom_seo_enabled()
    {
        global $theme_options;
        $on = $theme_options->get_option_value('finesse_custom_seo_enabled');
        return strtolower($on) == 'on';
    }
}

if (!function_exists('get_page_description')) {
    function get_page_description()
    {
        global $theme_options;
        global $post;
        if (isset($post) && isset($theme_options)) {
            $description = get_post_meta(get_the_ID(), 'finesse_page_description', true);
            if (!isset($description) || trim($description) == '') {
                $description = $theme_options->get_option_value('finesse_page_description');
            }
        } else {
            $description = get_bloginfo('description');
        }
        return $description;
    }
}

if (!function_exists('get_page_keywords')) {
    function get_page_keywords()
    {
        global $theme_options;
        global $post;
        if (isset($post) && isset($theme_options)) {
            $keywords = get_post_meta(get_the_ID(), 'finesse_page_keywords', true);
            if (!isset($keywords) || trim($keywords) == '') {
                $keywords = $theme_options->get_option_value('finesse_page_keywords');
            }
        } else {
            $keywords = '';
        }
        return $keywords;
    }
}

if (!function_exists('fin_get_page_author')) {
    function fin_get_page_author()
    {
        global $post;
        if (isset($post)) {
            $author = get_the_author_meta('display_name', $post->post_author);
        } else {
            $author = get_bloginfo('name');
        }
        return $author;
    }
}

if (!function_exists('get_fav_icon')) {
    function get_fav_icon()
    {
        global $theme_options;
        $favicon = $theme_options->get_option_value('finesse_favicon');
        if (!isset($favicon) || trim($favicon) == '') {
            $favicon = get_template_directory_uri() . '/images/favicon.ico';
        }
        return $favicon;
    }
}

if (!function_exists('is_search_header_displayed')) {
    function is_search_header_displayed()
    {
        global $theme_options;
        $on = $theme_options->get_option_value('finesse_display_search_header');
        return strtolower($on) == 'on';
    }
}

if (!function_exists('get_contact_address')) {
    function get_contact_address()
    {
        global $theme_options;
        $contact_email = $theme_options->get_option_value('finesse_contact_address');
        if (!isset($contact_email)) {
            $contact_email = '';
        }
        return $contact_email;
    }
}

if (!function_exists('get_contact_email')) {
    function get_contact_email()
    {
        global $theme_options;
        $contact_email = $theme_options->get_option_value('finesse_contact_email');
        if (!isset($contact_email)) {
            $contact_email = '';
        }
        return $contact_email;
    }
}

if (!function_exists('get_contact_phone')) {
    function get_contact_phone()
    {
        global $theme_options;
        $contact_phone = $theme_options->get_option_value('finesse_contact_phone');
        if (!isset($contact_phone)) {
            $contact_phone = '';
        }
        return $contact_phone;
    }
}

if (!function_exists('is_contact_details_displayed_in_header_enabled')) {
    function is_contact_details_displayed_in_header_enabled()
    {
        global $theme_options;
        $on = $theme_options->get_option_value('finesse_display_contact_details_in_header');
        return strtolower($on) == 'on';
    }
}

if (!function_exists('is_captcha_form_enabled')) {
    function is_captcha_form_enabled()
    {
        global $theme_options;
        $on = $theme_options->get_option_value('finesse_captcha_enable');
        return strtolower($on) == 'on';
    }
}

if (!function_exists('get_captcha_public_key')) {
    function get_captcha_public_key()
    {
        global $theme_options;
        $key = $theme_options->get_option_value('finesse_captcha_public_key');
        if (!isset($key)) {
            $key = '';
        }
        return $key;
    }
}

if (!function_exists('get_captcha_private_key')) {
    function get_captcha_private_key()
    {
        global $theme_options;
        $key = $theme_options->get_option_value('finesse_captcha_private_key');
        if (!isset($key)) {
            $key = '';
        }
        return $key;
    }
}

if (!function_exists('display_related_posts')) {
    function display_related_posts()
    {
        global $theme_options;
        $on = $theme_options->get_option_value('finesse_blog_display_related_posts');
        return strtolower($on) == 'on';
    }
}

if (!function_exists('is_display_post_meta_enabled')) {
    function is_display_post_meta_enabled()
    {
        global $theme_options;
        $on = $theme_options->get_option_value('finesse_blog_display_post_meta');
        return strtolower($on) == 'on';
    }
}

if (!function_exists('is_display_post_comments_enabled')) {
    function is_display_post_comments_enabled()
    {
        global $theme_options;
        $on = $theme_options->get_option_value('finesse_blog_display_post_comments');
        return strtolower($on) == 'on';
    }
}

if (!function_exists('display_portfolio_related_posts')) {
    function display_portfolio_related_posts()
    {
        global $theme_options;
        $on = $theme_options->get_option_value('finesse_portfolio_display_related_posts');
        return strtolower($on) == 'on';
    }
}

if (!function_exists('get_twitter_username')) {
    function get_twitter_username()
    {
        global $theme_options;
        $twitter_username = $theme_options->get_option_value('finesse_social_twitter_username');
        if (!isset($twitter_username) || trim($twitter_username) == '') {
            $twitter_username = '';
        }
        return $twitter_username;
    }
}

if (!function_exists('fin_get_sn_data')) {
    function fin_get_sn_data($sn)
    {
        $val = get_theme_option($sn);
        return isset($val) && !empty($val) ? $val : false;
    }
}

if (!function_exists('fin_get_twitter_config')) {
    function fin_get_twitter_config()
    {
        $twitter_config = array();
        $api_url = fin_get_sn_data('finesse_social_twitter_api_url');
        if ($api_url) {
            $twitter_config['api_url'] = $api_url;
        } else {
            return false;
        }
        $consumer_key = fin_get_sn_data('finesse_social_twitter_consumer_key');
        if ($consumer_key) {
            $twitter_config['consumer_key'] = $consumer_key;
        } else {
            return false;
        }
        $consumer_secret = fin_get_sn_data('finesse_social_twitter_consumer_secret');
        if ($consumer_secret) {
            $twitter_config['consumer_secret'] = $consumer_secret;
        } else {
            return false;
        }
        $access_token = fin_get_sn_data('finesse_social_twitter_access_token');
        if ($access_token) {
            $twitter_config['access_token'] = $access_token;
        } else {
            return false;
        }
        $access_token_secret = fin_get_sn_data('finesse_social_twitter_access_token_secret');
        if ($access_token_secret) {
            $twitter_config['access_token_secret'] = $access_token_secret;
        } else {
            return false;
        }

        return $twitter_config;
    }
}

if (!function_exists('get_facebook_url')) {
    function get_facebook_url()
    {
        global $theme_options;
        $facebook_url = $theme_options->get_option_value('finesse_social_facebook_url');
        if (!isset($facebook_url) || trim($facebook_url) == '') {
            $facebook_url = '';
        }
        return $facebook_url;
    }
}

if (!function_exists('get_gplus_url')) {
    function get_gplus_url()
    {
        global $theme_options;
        $gplus_url = $theme_options->get_option_value('finesse_social_gplus_url');
        if (!isset($gplus_url) || trim($gplus_url) == '') {
            $gplus_url = '';
        }
        return $gplus_url;
    }
}

if (!function_exists('get_linkedin_url')) {
    function get_linkedin_url()
    {
        global $theme_options;
        $linkedin_url = $theme_options->get_option_value('finesse_social_linkedin_url');
        if (!isset($linkedin_url) || trim($linkedin_url) == '') {
            $linkedin_url = '';
        }
        return $linkedin_url;
    }
}

if (!function_exists('get_youtube_url')) {
    function get_youtube_url()
    {
        global $theme_options;
        $youtube_url = $theme_options->get_option_value('finesse_social_youtube_url');
        if (!isset($youtube_url) || trim($youtube_url) == '') {
            $youtube_url = '';
        }
        return $youtube_url;
    }
}

if (!function_exists('get_skype_url')) {
    function get_skype_url()
    {
        global $theme_options;
        $skype_url = $theme_options->get_option_value('finesse_social_skype_url');
        if (!isset($skype_url) || trim($skype_url) == '') {
            $skype_url = '';
        }
        return $skype_url;
    }
}

if (!function_exists('get_flicker_id')) {
    function get_flicker_id()
    {
        global $theme_options;
        $flickr_id = $theme_options->get_option_value('finesse_social_flickr_id');
        if (!isset($flickr_id) || trim($flickr_id) == '') {
            $flickr_id = '';
        }
        return $flickr_id;
    }
}

if (!function_exists('is_rss_enabled')) {
    function is_rss_enabled()
    {
        global $theme_options;
        $on = $theme_options->get_option_value('finesse_rss_enabled');
        return isset($on) && (strtolower($on) == 'on');
    }
}

if (!function_exists('get_rss_url')) {
    function get_rss_url()
    {
        global $theme_options;
        $rss_type = $theme_options->get_option_value('finesse_rss_type');
        return get_bloginfo($rss_type, 'display');
    }
}

if (!function_exists('is_responsive_enabled')) {
    function is_responsive_enabled()
    {
        global $theme_options;
        $on = $theme_options->get_option_value('finesse_responsive_enabled');
        return isset($on) && (strtolower($on) == 'on');
    }
}

if (!function_exists('get_supported_languages')) {
    function get_supported_languages()
    {
        $languages = get_option('finesse_languages');
        if (isset($languages) && is_array($languages) && count($languages) > 0) {
            return $languages;
        } else {
            return array();
        }
    }
}

if (!function_exists('save_supported_languages')) {
    function save_supported_languages($languages)
    {
        update_option('finesse_languages', $languages);
    }
}

if (!function_exists('get_footer_text')) {
    function get_footer_text()
    {
        global $theme_options;
        $value = $theme_options->get_option_value('finesse_footer_text');
        if (isset($value) && strlen($value) > 0) {
            return html_entity_decode(apply_filters('widget_title', $value));
        } else {
            return '';
        }
    }
}

if (!function_exists('get_tracking_code')) {
    function get_tracking_code()
    {
        global $theme_options;
        $tracking_code = $theme_options->get_option_value('finesse_tracking_code');
        if (isset($tracking_code) && trim($tracking_code) != '') {
            if (contains_string($tracking_code, '<script')) {
                return $tracking_code;
            } else {
                return '<script type="text/javascript">' . $tracking_code . '</script>';
            }
        } else {
            return false;
        }
    }
}

if (!function_exists('get_dynamic_sidebars')) {
    function get_dynamic_sidebars()
    {
        $dynamic_sidebars = get_option('finesse_dynamic_sidebars');
        if (!$dynamic_sidebars) {
            return array();
        } else {
            return $dynamic_sidebars;
        }
    }
}

if (!function_exists('save_dynamic_sidebars')) {
    function save_dynamic_sidebars($dynamic_sidebars)
    {
        update_option('finesse_dynamic_sidebars', $dynamic_sidebars);
    }
}

if (!function_exists('get_current_page_sidebar')) {
    function get_current_page_sidebar()
    {
        global $wp_registered_sidebars;
        global $post;
        if (isset($post) && isset($wp_registered_sidebars)) {
            $sidebar_name = get_post_meta(get_the_ID(), 'finesse_sidebar_name', true);
            if (is_string($sidebar_name) && array_key_exists(sanitize_title($sidebar_name), $wp_registered_sidebars)) {
                return $sidebar_name;
            }
        }
        return 'default-sidebar';
    }
}

if (!function_exists('get_current_layout')) {
    function get_current_layout()
    {
        global $theme_options;
        return $theme_options->get_option_value('finesse_layout_type');
    }
}

//-------------------------------------------- UTIL FUNCTIONS ----------------------------------------------------------

if (!function_exists('print_language_switcher')) {
    function print_language_switcher()
    {
        if (is_internationalization_on()) {
            if (!print_qtranslate_language_switcher()) {
                print_transposh_language_switcher();
            }
        }
    }
}

if (!function_exists('print_qtranslate_language_switcher')) {
    function print_qtranslate_language_switcher()
    {
        if (function_exists('qtrans_getSortedLanguages')) {
            global $q_config;
            $supported_languages = qtrans_getSortedLanguages();
            echo '<div id="polyglotLanguageSwitcher">';
            echo '<form action="#">';
            echo '<select id="polyglot-language-options">';
            foreach ($supported_languages as $language) {
                $language_name = $q_config['language_name'][$language];
                $current_language = $q_config['language'];
                $selected_attr = ($language == $current_language) ? 'selected' : '';
                $id = $language;
                echo '<option id="' . $id . '" value="' . $language . '" ' . $selected_attr . '>' . $language_name . '</option>';
            }
            echo '</select >';
            echo '</form >';
            echo '</div>';
            return true;
        }
        return false;
    }
}

if (!function_exists('print_transposh_language_switcher')) {
    function print_transposh_language_switcher()
    {

        if (isset($GLOBALS['my_transposh_plugin'])) {
            $transposh = $GLOBALS['my_transposh_plugin'];
            $supported_languages = $transposh->options->get_sorted_langs();
            echo '<div id="polyglotLanguageSwitcher">';
            echo '<form action="#">';
            echo '<select id="polyglot-language-options">';
            foreach ($supported_languages as $language => $value) {
                if ($transposh->options->is_active_language($language) ||
                    $transposh->options->is_default_language($language)
                ) {
                    $language_name = explode(',', $value);
                    $language_name = $language_name[0];
                    $current_language = $transposh->target_language;
                    if (!isset($current_language) || empty($current_language)) {
                        $current_language = $transposh->tgl;
                    }
                    $selected_attr = ($language == $current_language) ? 'selected' : '';
                    $id = $language;
                    echo '<option id="' . $id . '" value="' . $language . '" ' . $selected_attr . '>' . $language_name . '</option>';
                }
            }
            echo '</select >';
            echo '</form >';
            echo '</div>';
            return true;
        }
        return false;
    }
}

if (!function_exists('contains_string')) {
    function contains_string($haystack, $needle)
    {
        $pos = strpos($haystack, $needle);
        if ($pos === false) {
            return false;
        } else {
            return true;
        }
    }
}

if (!function_exists('start_with')) {
    function start_with($haystack, $needle)
    {
        $length = strlen($needle);
        return (substr($haystack, 0, $length) === $needle);
    }
}

if (!function_exists('end_with')) {
    function end_with($haystack, $needle)
    {
        $length = strlen($needle);
        $start = $length * -1; //negative
        return (substr($haystack, $start) === $needle);
    }
}

if (!function_exists('emphasize')) {
    function emphasize($haystack, $needle)
    {
        return preg_replace("/$needle/i", '<strong>$0</strong>', $haystack);
    }
}

if (!function_exists('shrink_without_strip_tags')) {
    function shrink_without_strip_tags($content, $limit = 350, $read_more_symbol = '&hellip;')
    {
        if (strlen($content) > $limit) {
            if ($read_more_symbol == '') {
                $read_more_symbol = '.';
            } else {
                $read_more_symbol = ' ' . $read_more_symbol;
            }
            $content = substr($content, 0, strpos($content, " ", $limit)) . $read_more_symbol;
        }
        return $content;
    }
}

if (!function_exists('shrink')) {
    function shrink($content, $limit = 350, $read_more_symbol = '&hellip;')
    {
        $content = strip_tags($content);
        if (strlen($content) > $limit) {
            if ($read_more_symbol == '') {
                $read_more_symbol = '.';
            } else {
                $read_more_symbol = ' ' . $read_more_symbol;
            }
            $content = substr($content, 0, strpos($content, " ", $limit)) . $read_more_symbol;
        }
        return $content;
    }
}

if (!function_exists('shrink_starting_from')) {
    function shrink_starting_from($haystack, $needle, $limit = 350, $read_more_symbol = '&hellip;')
    {
        $haystack = strip_tags($haystack);
        $tmp_haystack = stristr($haystack, $needle);
        if ($tmp_haystack) {
            $haystack = $read_more_symbol . ' ' . $tmp_haystack;
        }
        if (strlen($haystack) > $limit) {
            if ($read_more_symbol == '') {
                $read_more_symbol = '.';
            } else {
                $read_more_symbol = ' ' . $read_more_symbol;
            }
            $haystack = substr($haystack, 0, strpos($haystack, " ", $limit)) . $read_more_symbol;
        }
        return $haystack;
    }
}

if (!function_exists('get_url_content')) {
    function get_url_content($url, $method = 'GET')
    {
        $headers = get_headers($url);
        if (strrpos($headers[0], '200')) {
            $opts = array(
                'http' => array(
                    'method' => $method,
                    'header' => "Accept: text/html\r\n"
                )
            );
            $context = stream_context_create($opts);
            return file_get_contents($url, false, $context);
        } else {
            return null;
        }
    }
}

if (!function_exists('escape_json_value')) {
    function escape_json_value($value)
    {
        $value = preg_replace("/\r\n|\r|\n/", ' ', stripslashes($value));
        return addslashes($value);
    }
}

if (!function_exists('is_ie_version')) {
    function is_ie_version($ie_version)
    {
        preg_match('/MSIE (.*?);/', $_SERVER['HTTP_USER_AGENT'], $matches);
        if (count($matches) > 1) {
            //Then we're using IE
            $version = $matches[1];
            return intval($ie_version) == intval($version);
        }
        return false;
    }
}

if (!function_exists('is_captcha_code_valid')) {
    function is_captcha_code_valid()
    {
        if (isset($_POST["recaptcha_challenge_field"]) && isset($_POST["recaptcha_response_field"])) {
            $captcha_private_key = get_captcha_private_key();
            $resp = recaptcha_check_answer($captcha_private_key, $_SERVER["REMOTE_ADDR"],
                $_POST["recaptcha_challenge_field"], $_POST["recaptcha_response_field"]);
            return $resp->is_valid;
        } else {
            return false;
        }
    }
}