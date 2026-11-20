<?php

class Embedded_Video
{
    static function get_embedded_code($post_id)
    {
        $post = get_post($post_id);
        return do_shortcode($post->post_content);
    }

    static function is_video($post_id)
    {
        $post_title = get_the_title($post_id);
        return start_with($post_title, 'video-');
    }

    static function get_video_url($post_id)
    {
        $post_title = get_the_title($post_id);
        $tmp = explode('-', $post_title, 3);
        if ($tmp[1] == 'vimeo') {
            return 'http://vimeo.com/' . $tmp[2];
        } else if ($tmp[1] == 'youtube') {
            return 'http://www.youtube.com/watch?v=' . $tmp[2];
        } else {
            return get_permalink($post_id) . '?iframe=true&width=480&height=270';
        }
    }

    static function save($post_parent_id, $video_id, $embedded_video)
    {
        $video_id = Embedded_Video::get_video_id($video_id, $embedded_video);
        $video_source = Embedded_Video::get_video_source($embedded_video);
        $post_title = Embedded_Video::get_post_title($video_id, $video_source, $embedded_video);
        $existing_post = get_page_by_title($post_title, OBJECT, 'attachment');
        if (isset($existing_post)) {
            $post_update = array(
                'ID' => $existing_post->ID,
                'post_content' => $embedded_video,
            );
            wp_update_post($post_update);
            return $existing_post;
        } else {
            $filename = Embedded_Video::download_video_thumbnail($video_id, $video_source, $embedded_video);
            $wp_upload_dir = wp_upload_dir();

            $guid = $wp_upload_dir['baseurl'] . _wp_relative_upload_path($filename);
            $post_mime_type = wp_check_filetype(basename($filename), null);

            $video_attachment = array(
                'guid' => $guid,
                'post_mime_type' => $post_mime_type['type'],
                'post_title' => $post_title,
                'post_content' => $embedded_video,
                'post_type' => 'attachment',
                'post_status' => 'inherit',
                'post_parent' => $post_parent_id
            );
            $attach_id = wp_insert_attachment($video_attachment, $filename);
            // you must first include the image.php file
            // for the function wp_generate_attachment_metadata() to work
            require_once(ABSPATH . 'wp-admin/includes/image.php');
            $attach_data = wp_generate_attachment_metadata($attach_id, $filename);
            wp_update_attachment_metadata($attach_id, $attach_data);

            return get_page_by_title($post_title, OBJECT, 'attachment');
        }
    }

    static function update($video_id, $embedded_video)
    {
        $video_id = Embedded_Video::get_video_id($video_id, $embedded_video);
        $video_source = Embedded_Video::get_video_source($embedded_video);
        $post_title = Embedded_Video::get_post_title($video_id, $video_source, $embedded_video);
        $existing_post = get_page_by_title($post_title, OBJECT, 'attachment');
        if (isset($existing_post)) {
            $post_update = array(
                'ID' => $existing_post->ID,
                'post_content' => $embedded_video,
            );
            wp_update_post($post_update);
        } else {
            throw new Exception("The slide could not been updated because it doesn't exists.");
        }
    }

    static function delete($post_id)
    {
        wp_delete_attachment($post_id, true);
    }

    private static function download_video_thumbnail($video_id, $video_source, $embedded_video)
    {
        if (strtolower($video_source) == 'youtube') {
            return Embedded_Video::download_youtube_video_thumbnail($video_id, $embedded_video);
        } elseif (strtolower($video_source) == 'vimeo') {
            return Embedded_Video::download_vimeo_video_thumbnail($video_id, $embedded_video);
        } elseif (strtolower($video_source) == 'self-hosted') {
            return Embedded_Video::download_self_hosted_video_thumbnail($video_id, $embedded_video);
        } else {
            throw new Exception('Only YouTube, Vimeo and Self Hosted videos are supported by this feature.');
        }
    }

    private static function download_youtube_video_thumbnail($video_id, $embedded_code)
    {
        $url = "http://img.youtube.com/vi/$video_id/0.jpg";
        $data = get_url_content($url);
        if (isset($data)) {
            $video_size = Embedded_Video::get_video_size($embedded_code);
            $wp_upload_dir = wp_upload_dir();
            $video_size_as_string = $video_size['width'] . 'x' . $video_size['height'];
            $filename = $wp_upload_dir['path'] . "/youtube-$video_id-$video_size_as_string.jpg";
            Embedded_Video::save_file($filename, $data);
            return $filename;
        }
        throw new Exception('The YouTube video thumbnail could not be found.');
    }

    private static function download_vimeo_video_thumbnail($video_id, $embedded_code)
    {
        $xml = get_url_content("http://vimeo.com/api/v2/video/$video_id.xml");
        if (isset($xml)) {
            $video_size = Embedded_Video::get_video_size($embedded_code);
            $x = strpos($xml, '<thumbnail_large>') + 17;
            $y = strpos($xml, '</thumbnail_large>', $x);
            $url = substr($xml, $x, $y - $x);
            $data = get_url_content($url);
            if (isset($data)) {
                $wp_upload_dir = wp_upload_dir();
                $video_size_as_string = $video_size['width'] . 'x' . $video_size['height'];
                $filename = $wp_upload_dir['path'] . "/vimeo-$video_id-$video_size_as_string.jpg";
                Embedded_Video::save_file($filename, $data);
                return $filename;
            }
        }
        throw new Exception('The Vimeo video thumbnail could not be found.');
    }

    private static function download_self_hosted_video_thumbnail($video_id, $embedded_code)
    {
        $video_size = Embedded_Video::get_video_size($embedded_code);
        $video_size_as_string = $video_size['width'] . 'x' . $video_size['height'];
        $poster_url = Embedded_Video::get_attribute_value('poster', $embedded_code);
        $data = get_url_content(Multimedia_Util::get_image_src($poster_url));
        if (isset($data)) {
            $extension = 'jpg';
            $path_parts = pathinfo($poster_url);
            if ($path_parts && $path_parts['extension'] != NULL) {
                $extension = $path_parts['extension'];
            }
            $wp_upload_dir = wp_upload_dir();
            $filename = $wp_upload_dir['path'] . "/self-hosted-$video_id-$video_size_as_string.$extension";
            Embedded_Video::save_file($filename, $data);
            return $filename;
        } else {
            throw new Exception('The Self Hosted video thumbnail could not be found.');
        }
    }

    private static function get_video_id($video_id, $embedded_code)
    {
        if ($video_id == 'self-hosted') {
            return md5($embedded_code);
        } else {
            if (strlen($video_id) == 0 || strpos($embedded_code, $video_id) === false) {
                throw new Exception('The video ID is wrong.');
            }
            return $video_id;
        }
    }

    static function get_video_size($embedded_code)
    {
        $embedded_code = strtolower($embedded_code);
        $embedded_code = str_replace(array('= "', '= \\"', '=\\"'), '="', $embedded_code);
        $embedded_code = str_replace('\\"', '"', $embedded_code);
        $embedded_code = str_replace(array('= \'', '= \\\'', '=\\\''), '=\'', $embedded_code);
        $embedded_code = str_replace('\\\'', '\'', $embedded_code);
        $embedded_code = str_replace('height =', 'height=', $embedded_code);
        $embedded_code = str_replace('width =', 'width=', $embedded_code);

        $width = Embedded_Video::get_attribute_value('width', $embedded_code);
        $height = Embedded_Video::get_attribute_value('height', $embedded_code);
        if ($width && $height) {
            return array("width" => $width, "height" => $height);
        } else {
            throw new Exception("The video size (width, height) are not correctly specified.");
        }
    }

    private static function get_post_title($video_id, $video_source, $embedded_code)
    {
        $video_size = Embedded_Video::get_video_size($embedded_code);
        return 'video-' . $video_source . '-' . $video_id . '-' . $video_size['width'] . 'x' . $video_size['height'];
    }

    static function get_attribute_value($attrib, $tag)
    {
        //get attribute from html tag
        $re = '/' . preg_quote($attrib) . '=([\'"])?((?(1).+?|[^\s>]+))(?(1)\1)/is';
        if (preg_match($re, $tag, $match)) {
            return urldecode($match[2]);
        }
        return false;
    }

    private static function get_video_source($embedded_code)
    {
        $embedded_code = strtolower($embedded_code);

        if (start_with($embedded_code, '[video')) {
            return 'self-hosted';
        } elseif (strpos($embedded_code, 'vimeo')) {
            return 'vimeo';
        } elseif (strpos($embedded_code, 'youtube')) {
            return 'youtube';
        }
        throw new Exception('Only YouTube, Vimeo and Self Hosted videos are supported by this feature.');
    }

    private static function save_file($filename, $data){
        include_once ABSPATH . 'wp-admin/includes/file.php';
        WP_Filesystem();
        global $wp_filesystem;
        return $wp_filesystem->put_contents($filename, $data);
    }

}
