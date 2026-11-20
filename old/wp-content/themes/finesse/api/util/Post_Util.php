<?php

class Post_Util
{

    static function find_post_by_identifier($post_identifier)
    {
        if (strlen($post_identifier) > 0 && is_numeric($post_identifier)) {
            return get_post($post_identifier);
        } elseif (strlen($post_identifier) > 0) {
            global $wpdb;
            $post_id = $wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE post_title = '" . $post_identifier . "'");
            if (is_numeric($post_id)) {
                return get_post($post_id);
            }
        }
        return null;
    }

    static function get_post_format($post = null)
    {
        $post = isset($post) ? $post : $GLOBALS['post'];
        if (isset($post)) {
            $post_format = get_post_format($post);
            if (false == $post_format) {
                return 'standard';
            } else {
                return $post_format;
            }
        } else {
            return 'standard';
        }
    }

    static function get_page_title_by_type($page_type)
    {
        $title = '';
        if ($page_type == 'blog') {
            $title = __('Blog', 'finesse');
        } elseif ($page_type == 'tag') {
            $title = sprintf(__('Posts Filtered by Tag: %s', 'finesse'), '' . single_cat_title('', false) . '');
        } elseif ($page_type == 'category') {
            $title = sprintf(__('Posts Filtered by Category: %s', 'finesse'), '' . single_cat_title('', false) . '');
        } elseif ($page_type == 'author') {
            $title = sprintf(__('Posts Filtered by Author: %s', 'finesse'), '' . get_the_author() . '');
        } elseif ($page_type == 'archive') {
            if (is_day()) {
                $title = sprintf(__('Daily Archives: %s', 'finesse'), get_the_time());
            } elseif (is_month()) {
                $title = sprintf(__('Monthly Archives: %s', 'finesse'), get_the_time('F Y'));
            } elseif (is_year()) {
                $title = sprintf(__('Yearly Archives: %s', 'finesse'), get_the_time('Y'));
            } else {
                $title = __('Blog Archives', 'finesse');
            }
        }
        return $title;
    }

    static function get_page_title_by_format($post = null, $as_link = true)
    {
        $content = '';
        $post = isset($post) ? $post : $GLOBALS['post'];
        if (isset($post)) {
            $post_format = Post_Util::get_post_format($post);
            $post_type = $post->post_type;
            if ($post_type == 'post') {
                switch ($post_format) {
                    case 'aside':
                    case 'quote':
                        $content = '';
                        break;
                    case 'link':
                        $media_config = Page_Media_Manager::get_page_media_config($post->ID);
                        if ($media_config && $media_config->media_type == 'link') {
                            $url = $media_config->link_url;
                        } else {
                            $url = '#';
                        }
                        $content = '<a href="' . $url . '" target="_blank" >[Link] ' . get_the_title($post->ID) . '</a>';
                        break;
                    default:
                        if ($as_link) {
                            $post_url = get_permalink($post->ID);
                            $content = '<a href="' . $post_url . '" >' . get_the_title($post->ID) . '</a>';
                        } else {
                            $content = get_the_title($post->ID);
                        }
                        break;
                }
            }
        }

        return $content;
    }

    static function get_post_excerpt()
    {
        $post_format = Post_Util::get_post_format();
        if ($post_format == 'quote') {
            $sc = Post_Util::get_first_shortcode_of_type('bq');
            if ($sc) {
                $link = get_permalink();
                $sc_id = Post_Util::get_shortcode_attribute($sc, Blockquote_Shortcode::$ID_ATTR);
                if ($sc_id) {
                    $link = $link . '#' . $sc_id;
                }
                $sc = Post_Util::override_shortcode_attribute($sc, Blockquote_Shortcode::$TYPE_ATTR, 'simple');
                $sc = Post_Util::override_shortcode_attribute($sc, Blockquote_Shortcode::$HREF_ATTR, $link);
                return do_shortcode($sc);
            }
        }
        $read_more = apply_filters('finesse_post_ellipsis', '&hellip;');
        $excerpt_length = apply_filters('excerpt_length', 350);
        return '<p>' . shrink(get_the_excerpt(), $excerpt_length, $read_more) . '</p>';
    }

    static function get_first_shortcode_of_type($shortcode_name, $post = null)
    {
        $post = isset($post) ? $post : $GLOBALS['post'];
        if (isset($post) && $post) {
            $sc_pattern = '/\[(\[?)('.$shortcode_name.')(?![\w-])([^\]\/]*(?:\/(?!\])[^\]\/]*)*?)(?:(\/)\]|\](?:([^\[]*+(?:\[(?!\/\2\])[^\[]*+)*+)\[\/\2\])?)(\]?)/s';
            $post_content = $post->post_content;
            if(function_exists('qtrans_useCurrentLanguageIfNotFoundShowAvailable')){
                $post_content = qtrans_useCurrentLanguageIfNotFoundShowAvailable($post_content);
            }
            preg_match_all($sc_pattern, $post_content, $matches);

            if (array_key_exists(2, $matches) && in_array($shortcode_name, $matches[2])) {
                foreach ($matches[0] as $sc) {
                    if (start_with($sc, '[' . $shortcode_name)) {
                        return $sc;
                    }
                }
            }
        }
        return false;
    }

    static function get_all_shortcodes_of_type($shortcode_name, $post = null)
    {
        $shortcodes = array();
        $post = isset($post) ? $post : $GLOBALS['post'];
        if (isset($post) && $post) {
            $sc_pattern = '/\[(\[?)('.$shortcode_name.')(?![\w-])([^\]\/]*(?:\/(?!\])[^\]\/]*)*?)(?:(\/)\]|\](?:([^\[]*+(?:\[(?!\/\2\])[^\[]*+)*+)\[\/\2\])?)(\]?)/s';
            $post_content = $post->post_content;
            if(function_exists('qtrans_useCurrentLanguageIfNotFoundShowAvailable')){
                $post_content = qtrans_useCurrentLanguageIfNotFoundShowAvailable($post_content);
            }
            preg_match_all($sc_pattern, $post_content, $matches);

            if (array_key_exists(2, $matches) && in_array($shortcode_name, $matches[2])) {
                foreach ($matches[0] as $sc) {
                    if (start_with($sc, '[' . $shortcode_name)) {
                        array_push($shortcodes, $sc);
                    }
                }
            }
        }
        return $shortcodes;
    }

    static function override_shortcode_attribute($sc, $attr_name, $attr_value)
    {
        $sc = str_replace($attr_name . ' =', $attr_name . '_old=', $sc);
        $sc = str_replace($attr_name . '=', $attr_name . '_old=', $sc);

        $a = explode(' ', $sc);
        $sc_begin = $a[0] . ' ';
        $sc = str_replace($sc_begin, $sc_begin . $attr_name . '="' . $attr_value . '" ', $sc);

        return $sc;
    }

    static function get_shortcode_attribute($shortcode, $attr_name)
    {
        $re = '/' . preg_quote($attr_name) . '=([\'"])?((?(1).+?|[^\s>]+))(?(1)\1)/is';
        if (preg_match($re, $shortcode, $match)) {
            return urldecode($match[2]);
        }
        return false;
    }
}
