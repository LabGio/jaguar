<?php

class Testimonials_Carousel_Shortcode extends Abstract_Finesse_Shortcode implements Shortcode_Designer
{

    static $POST_IDENTIFIER_ATTR = 'post_identifier';
    static $AUTO_ATTR = 'auto';

    function render($attr, $inner_content = null, $code = '')
    {
        extract(shortcode_atts(array(
            Testimonials_Carousel_Shortcode::$AUTO_ATTR => '',
            Testimonials_Carousel_Shortcode::$POST_IDENTIFIER_ATTR => ''), $attr));

        $content = '';
        $post = Post_Util::find_post_by_identifier($post_identifier);
        $shortcodes = Post_Util::get_all_shortcodes_of_type('bq', $post);
        if (count($shortcodes) > 0) {
            $data_attr = $this->get_attribute('data-auto', $auto);
            $content .= '<ul class="testimonial-carousel" ' . $data_attr . '>' . "\n";
            foreach ($shortcodes as $sc) {
                $link = get_permalink($post->ID);
                $sc_id = Post_Util::get_shortcode_attribute($sc, Blockquote_Shortcode::$ID_ATTR);
                if ($sc_id) {
                    $link = $link . '#' . $sc_id;
                }

                $content .= '<li>' . "\n";
                $sc = Post_Util::override_shortcode_attribute($sc, Blockquote_Shortcode::$HREF_ATTR, $link);
                $content .= do_shortcode($sc);
                $content .= '</li>' . "\n";
            }
            $content .= '</ul>' . "\n";
        }
        return $content;
    }

    function get_names()
    {
        return array('testimonials_carousel', 'testimonials_carousel_item');
    }

    function get_visual_editor_form()
    {
        global $wpdb;
        $posts = $wpdb->get_results("SELECT ID, post_title FROM $wpdb->posts WHERE post_type='page'");
        $content = '<form id="sc-testimonials-carousel-form" class="generic-form" method="post" action="#" data-sc="testimonials_carousel">';
        $content .= '<fieldset>';
        $content .= '<p>' . __('Select the page which contains the testimonials', 'finesse') . '</p>';
        $content .= '<div>';
        $content .= '<label for="sc-testimonials-carousel-post">' . __('Post ID or Title', 'finesse') . ':</label>';
        $content .= '<select id="sc-testimonials-carousel-post" name="sc-testimonials-carousel-post" data-attr-name="' . Testimonials_Carousel_Shortcode::$POST_IDENTIFIER_ATTR . '" data-attr-type="attr">';
        foreach ($posts as $i => $post) {
            $content .= '<option value="' . $post->ID . '">' . $post->post_title . ' (' . $post->ID . ')' . '</option>';
        }
        $content .= '</select>';
        $content .= '</div>';
        $content .= '<div >';
        $content .= '<input id="sc-testimonials-carousel-form-submit" type="submit" name="submit" value="' . __('Insert Testimonials Carousel', 'finesse') . '" class="button-primary">';
        $content .= '</div>';
        $content .= '</fieldset>';
        $content .= '</form>';

        return $content;
    }

    function get_group_title()
    {
        return __('Dynamic Elements', 'finesse');
    }

    function get_title()
    {
        return __('Testimonials Carousel', 'finesse');
    }
}
