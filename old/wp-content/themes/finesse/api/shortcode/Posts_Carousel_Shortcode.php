<?php

class Posts_Carousel_Shortcode extends Abstract_Finesse_Shortcode implements Shortcode_Designer
{

    static $PC_ATTR_IDS = 'ids';
    static $PC_ATTR_AUTO = 'auto';
    static $PC_ATTR_TYPE = 'post_type';
    static $PC_ATTR_DISPLAY = 'display';
    static $PC_ATTR_TERMS = 'terms';
    static $PC_ATTR_RELATED = 'related_with_current_post';
    static $PC_ATTR_COUNT = 'count';
    static $PC_ATTR_SCROLL_COUNT = 'scroll_count';
    static $PC_ATTR_BEFORE = 'before';
    static $PC_ATTR_AFTER = 'after';

    function render($attr, $inner_content = null, $code = '')
    {
        $attr = shortcode_atts(array(
            Posts_Carousel_Shortcode::$PC_ATTR_AUTO => '',
            Posts_Carousel_Shortcode::$PC_ATTR_DISPLAY => 'all',
            Posts_Carousel_Shortcode::$PC_ATTR_BEFORE => '',
            Posts_Carousel_Shortcode::$PC_ATTR_AFTER => '',
            Posts_Carousel_Shortcode::$PC_ATTR_IDS => '',
            Posts_Carousel_Shortcode::$PC_ATTR_TYPE => 'post',
            Posts_Carousel_Shortcode::$PC_ATTR_TERMS => '',
            Posts_Carousel_Shortcode::$PC_ATTR_RELATED => 'false',
            Posts_Carousel_Shortcode::$PC_ATTR_SCROLL_COUNT => '',
            Posts_Carousel_Shortcode::$PC_ATTR_COUNT => '8'), $attr);
        extract($attr);


        $post_ids = empty($ids) ? null : explode(',', $ids);
        if (is_array($post_ids) && count($post_ids) > 0) {
            $query_args = $this->get_selected_posts_query($post_type, $post_ids);
        } else {
            $query_args = $this->get_latests_posts_query($post_type, $terms, $related_with_current_post == 'true', $count);
        }
        $query = new wp_query($query_args);

        $content = '';
        if ($post_type == 'post') {
            $content = $this->get_blog_post_carousel($query, $attr);
        } elseif ($post_type == 'portfolio') {
            $content = $this->get_portfolio_carousel($query, $attr);
        }
        if (strlen($content) > 0) {
            $content = $before . $content . $after;
        }
        return $content;
    }

    function get_names()
    {
        return array('post_carousel');
    }

    private function get_blog_post_carousel($query, $attr)
    {
        $scroll_count = '';
        if (array_key_exists(Posts_Carousel_Shortcode::$PC_ATTR_SCROLL_COUNT, $attr)) {
            $scroll_count = $attr[Posts_Carousel_Shortcode::$PC_ATTR_SCROLL_COUNT];
        }
        $auto = '';
        if (array_key_exists(Posts_Carousel_Shortcode::$PC_ATTR_AUTO, $attr)) {
            $auto = $attr[Posts_Carousel_Shortcode::$PC_ATTR_AUTO];
        }

        if ($query->have_posts()) {
            $data_scroll_attr = $this->get_attribute('data-scroll', $scroll_count);
            $data_auto_attr = $this->get_attribute('data-auto', $auto);
            $content = '<ul class="post-carousel"' . $data_scroll_attr . $data_auto_attr . '>' . "\n";
            while ($query->have_posts()) {
                $query->the_post();
                $content .= $this->get_blog_post_carousel_item($attr);
            }
            wp_reset_postdata();
            $content .= '</ul>' . "\n";
            return $content;
        } else {
            return '';
        }
    }

    private function get_blog_post_carousel_item($attr)
    {
        $display = 'all';
        if (array_key_exists(Posts_Carousel_Shortcode::$PC_ATTR_DISPLAY, $attr)) {
            $display = $attr[Posts_Carousel_Shortcode::$PC_ATTR_DISPLAY];
        }
        $post_title = get_the_title();
        $post_url = get_permalink();
        $post_date = get_the_time('M d, Y');
        $post_format = get_post_format();
        $display_thumbnail = ($display == 'all') || ($display == '') || ($display == 'thumb');
        $display_meta = ($display == 'all') || ($display == '') || ($display == 'meta');
        if (false === $post_format) {
            $post_format = 'standard';
        }

        $content = '<li class="entry">' . "\n";
        if ($display_thumbnail) {
            $settings = apply_filters('finesse_post_carousel_blog_thumbnail_settings', array(
                'is_thumbnail' => true,
                'lightbox' => false,
                'echo' => false));
            $content .= Page_Media_Manager::render_page_media($settings);
        }
        if ($display_meta) {
            if (is_display_post_meta_enabled()) {
                $content .= '<div class="entry-meta">' . "\n";
                $content .= '<a href="' . $post_url . '" class="post-format-wrap" title="' . $post_title . '"><span class="post-format ' . $post_format . '">Permalink</span></a>' . "\n";
                $content .= '<span>' . $post_date . '</span>' . "\n";
                $content .= '</div>' . "\n";
            }
            $content .= $this->get_blog_post_entry_body() . "\n";
        }
        $content .= '</li>' . "\n";
        return $content;
    }

    private function get_blog_post_entry_body()
    {
        global $post;
        $post_title = Post_Util::get_page_title_by_format();
        $read_more = apply_filters('finesse_post_carousel_ellipsis', '&hellip;');
        $excerpt_length = apply_filters('finesse_post_carousel_excerpt_length', 70);
        $post_content = shrink(get_the_excerpt(), $excerpt_length, $read_more);
        $post_format = Post_Util::get_post_format();

        $content = '<div class="entry-body">' . "\n";
        if ($post_format == 'quote') {
            $content .= '<div class="entry-content">' . "\n";
            $sc = Post_Util::get_first_shortcode_of_type('bq', $post);
            $sc = Post_Util::override_shortcode_attribute($sc, Blockquote_Shortcode::$THUMB_ATTR, 'true');
            $content .= do_shortcode($sc) . "\n";
            $content .= '</div>' . "\n";
        } else {
            if (!empty($post_title)) {
                $content .= '<h4 class="entry-title">' . $post_title . '</h4>' . "\n";
            }
            $content .= '<div class="entry-content">' . "\n";
            $content .= '<p>' . $post_content . '</p>' . "\n";
            $content .= '</div>' . "\n";
        }
        $content .= '</div>' . "\n";
        return $content;
    }

    private function get_portfolio_carousel($query, $attr)
    {
        $scroll_count = '';
        if (array_key_exists(Posts_Carousel_Shortcode::$PC_ATTR_SCROLL_COUNT, $attr)) {
            $scroll_count = $attr[Posts_Carousel_Shortcode::$PC_ATTR_SCROLL_COUNT];
        }
        $display = 'all';
        if (array_key_exists(Posts_Carousel_Shortcode::$PC_ATTR_DISPLAY, $attr)) {
            $display = $attr[Posts_Carousel_Shortcode::$PC_ATTR_DISPLAY];
        }
        $auto = '';
        if (array_key_exists(Posts_Carousel_Shortcode::$PC_ATTR_AUTO, $attr)) {
            $auto = $attr[Posts_Carousel_Shortcode::$PC_ATTR_AUTO];
        }

        if ($query->have_posts()) {
            $settings = apply_filters('finesse_post_carousel_portfolio_thumbnail_settings', array(Posts_Carousel_Shortcode::$PC_ATTR_DISPLAY => $display));
            $settings = apply_filters('finesse_post_carousel_item_settings', $settings); //for backwards compatibility. SHOULD NOT BE USED ANYMORE
            $data_scroll_attr = $this->get_attribute('data-scroll', $scroll_count);
            $data_auto_attr = $this->get_attribute('data-auto', $auto);
            $content = '<ul class="project-carousel"' . $data_scroll_attr . $data_auto_attr . '>' . "\n";
            while ($query->have_posts()) {
                $query->the_post();
                $content .= Posts_Carousel_Shortcode::get_portfolio_carousel_item('<li class="entry">', '</li>', $settings);
            }
            wp_reset_postdata();
            $content .= '</ul>' . "\n";
            return $content;
        } else {
            return '';
        }
    }

    static function get_portfolio_carousel_item($wrap_start = '<li class="entry">',
                                                $wrap_end = '</li>',
                                                $settings = array())
    {
        $merge_settings = array_merge(array('is_thumbnail' => true, 'echo' => false), $settings);
        $display = 'all';
        if (array_key_exists(Posts_Carousel_Shortcode::$PC_ATTR_DISPLAY, $merge_settings)) {
            $display = $merge_settings[Posts_Carousel_Shortcode::$PC_ATTR_DISPLAY];
        }
        $display_thumbnail = ($display == 'all') || ($display == '') || ($display == 'thumb');
        $display_meta = ($display == 'all') || ($display == '') || ($display == 'meta');
        $title = get_the_title();
        $url = get_permalink();
        $short_description = ___(get_post_meta(get_the_ID(), "finesse_project_short_description", true));
        $content = $wrap_start;
        if ($display_thumbnail) {
            $content .= Page_Media_Manager::render_page_media($merge_settings);
        }
        if ($display_meta) {
            $content .= '<h4 class="entry-title"><a href="' . $url . '">' . $title . '</a></h4>';
            $content .= '<div class="entry-content">';
            $content .= '<p>' . $short_description . '</p>';
            $content .= '</div>';
        }
        $content .= $wrap_end;
        return $content;
    }

    private function get_latests_posts_query($post_type, $terms, $related_with_current_post, $count)
    {
        $query_args = array(
            'showposts' => $count,
            'post_type' => $post_type,
        );

        if (strlen($terms) > 0) {
            $taxonomy = ($post_type == 'post') ? 'category' : 'filter';
            $terms_array = explode(',', $terms);
            $query_terms = array();
            foreach ($terms_array as $term) {
                array_push($query_terms, trim($term));
            }
            $query_args['tax_query'] = array(
                array(
                    'taxonomy' => $taxonomy,
                    'field' => 'slug',
                    'terms' => $query_terms,
                    'operator' => 'IN'
                )
            );
        } elseif ($related_with_current_post) {
            global $post;
            $query_args['post__not_in'] = array($post->ID);

            $taxonomy = ($post_type == 'post') ? 'category' : 'filter';
            $terms_array = get_the_terms($post->ID, $taxonomy);
            if ($terms_array && count($terms_array) > 0) {
                $query_terms = array();
                foreach ($terms_array as $term) {
                    array_push($query_terms, $term->slug);
                }
                $query_args['tax_query'] = array(
                    array(
                        'taxonomy' => $taxonomy,
                        'field' => 'slug',
                        'terms' => $query_terms,
                        'operator' => 'IN'
                    )
                );
            }

        }
        return $query_args;
    }

    private function get_selected_posts_query($post_type, $posts_id)
    {
        $selected_posts_id = array();
        foreach ($posts_id as $id) {
            $id = trim($id);
            if (strlen($id) > 0 && is_numeric($id)) {
                array_push($selected_posts_id, intval($id));
            }
        }
        $query_args = array(
            'post_type' => $post_type,
            'post__in' => $selected_posts_id,
        );

        return $query_args;
    }

    function get_visual_editor_form()
    {
        $content = '<form id="sc-post-carousel-form" class="generic-form" method="post" action="#" data-sc="post_carousel">';
        $content .= '<fieldset>';
        $content .= '<div>';
        $content .= '<label for="sc-post-carousel-type">' . __('Type', 'finesse') . ':</label>';
        $content .= '<select id="sc-post-carousel-type" name="sc-post-carousel-type" data-attr-name="' . Posts_Carousel_Shortcode::$PC_ATTR_TYPE . '" data-attr-type="attr">';
        $content .= '<option value="post">' . __('Post', 'finesse') . '</option>';
        $content .= '<option value="portfolio">' . __('Portfolio', 'finesse') . '</option>';
        $content .= '</select>';
        $content .= '</div>';
        $content .= '<div>';
        $content .= '<input id="sc-post-carousel-related" name="sc-post-carousel-related" type="checkbox" data-attr-name="' . Posts_Carousel_Shortcode::$PC_ATTR_RELATED . '" data-attr-type="attr">';
        $content .= '<label for="sc-post-carousel-related">' . __('Display only the posts related with the current one', 'finesse') . '</label>';
        $content .= '</div>';
        $content .= '<div>';
        $content .= '<label for="sc-post-carousel-display">' . __('Display', 'finesse') . ':</label>';
        $content .= '<select id="sc-post-carousel-display" name="sc-post-carousel-display" data-attr-name="' . Posts_Carousel_Shortcode::$PC_ATTR_DISPLAY . '" data-attr-type="attr">';
        $content .= '<option value="all">' . __('Thumbnail and Post Meta', 'finesse') . '</option>';
        $content .= '<option value="thumb">' . __('Only the Thumbnail', 'finesse') . '</option>';
        $content .= '<option value="meta">' . __('Only the Post Meta', 'finesse') . '</option>';
        $content .= '</select>';
        $content .= '</div>';
        $content .= '<div>';
        $content .= '<label for="sc-post-carousel-scroll-count">' . __('Scroll Count', 'finesse') . ':</label>';
        $content .= '<input id="sc-post-carousel-scroll-count" name="sc-post-carousel-scroll-count" type="text" value="" class="number" data-attr-name="' . Posts_Carousel_Shortcode::$PC_ATTR_SCROLL_COUNT . '" data-attr-type="attr">';
        $content .= '</div>';
        $content .= '<div>';
        $content .= '<label for="sc-post-carousel-count">' . __('Items Count', 'finesse') . ':</label>';
        $content .= '<input id="sc-post-carousel-count" name="sc-post-carousel-count" type="text" value="10" class="required number" data-attr-name="' . Posts_Carousel_Shortcode::$PC_ATTR_COUNT . '" data-attr-type="attr">';
        $content .= '</div>';
        $content .= '<div >';
        $content .= '<input id="sc-post-carousel-form-submit" type="submit" name="submit" value="' . __('Insert Post Carousel', 'finesse') . '" class="button-primary">';
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
        return __('Post Carousel', 'finesse');
    }
}