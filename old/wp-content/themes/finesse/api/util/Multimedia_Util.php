<?php

class Multimedia_Util
{
    static function get_all_uploaded_images()
    {
        global $wpdb;
        return $wpdb->get_results("SELECT * FROM $wpdb->posts WHERE post_type = 'attachment' and post_mime_type like '%image/%'");
    }

    static function get_original_post($id)
    {
        if(is_numeric($id)){
            $post = get_post($id);
        }else{
            $post = get_page_by_title($id, OBJECT, 'attachment');
        }
        if (!$post) {
            $post = Multimedia_Util::get_page_by_guid($id, OBJECT, 'attachment');
        }
        return $post;
    }

    private static function get_page_by_guid($page_title, $output = OBJECT, $post_type = 'page')
    {
        global $wpdb;
        $page = $wpdb->get_var($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE guid = %s AND post_type= %s", $page_title, $post_type));
        if ($page)
            return get_post($page, $output);

        return null;
    }

    static function get_post_thumbnail_image_src($post, $thumbnail_size = 'custom-medium-post-thumb')
    {
        $image = wp_get_attachment_image_src($post->ID, $thumbnail_size, false);
        if ($image) {
            return $image[0];
        } else {
            return Multimedia_Util::get_post_guid($post);
        }
    }

    static function get_post_image_src($post)
    {
        return Multimedia_Util::get_post_guid($post);
    }

    static function get_external_image_src($src)
    {
        if (start_with($src, '/')) {
            $template_uri = get_template_directory_uri();
            return $template_uri . $src;
        } else {
            return $src;
        }
    }

    static function get_image_src($src, $thumbnail_size = 'full')
    {
        if (is_a($src, 'WP_Post')) {
            $post = $src;
        } else {
            $post = Multimedia_Util::get_original_post($src);
        }
        if ($post) {
            if ($thumbnail_size == 'full') {
                return Multimedia_Util::get_post_guid($post);
            } else {
                return Multimedia_Util::get_post_thumbnail_image_src($post, $thumbnail_size);
            }
        } else {
            return Multimedia_Util::get_external_image_src($src);
        }
    }

    static function get_media_url($url)
    {
        if (start_with($url, 'http://') || start_with($url, 'https://')) {
            return $url;
        } else {
            $post = Multimedia_Util::get_original_post($url);
            if ($post) {
                return Multimedia_Util::get_post_guid($post);
            } elseif (start_with($url, '/')) {
                return get_template_directory_uri() . '/' . $url;
            } else {
                return $url;
            }
        }
    }

    private static function get_post_guid($post)
    {
        $guid = $post->guid;
        if (is_ssl()) {
            $guid = str_replace('http://', 'https://', $guid);
        }
        return $guid;
    }

}
