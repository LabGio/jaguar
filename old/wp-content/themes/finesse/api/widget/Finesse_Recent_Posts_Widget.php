<?php

class Finesse_Recent_Posts_Widget extends WP_Widget_Recent_Posts
{
    function __construct() {
        parent::__construct();
    }

    function widget($args, $instance)
    {
        $cache = wp_cache_get('widget_recent_posts', 'widget');

        if (!is_array($cache)) {
            $cache = array();
        }

        if (!isset($args['widget_id'])) {
            $args['widget_id'] = $this->id;
        }

        if (isset($cache[$args['widget_id']])) {
            echo $cache[$args['widget_id']];
            return;
        }

        ob_start();
        extract($args);

        $title = apply_filters('widget_title', empty($instance['title']) ? __('Recent Posts', 'finesse') : $instance['title'], $instance, $this->id_base);
        $post_type = empty($instance['post_type']) ? 'post' : $instance['post_type'];
        if (empty($instance['number']) || !$number = absint($instance['number'])) {
            $number = 10;
        }

        $r = new WP_Query(apply_filters('widget_posts_args', array('post_type' => $post_type, 'posts_per_page' => $number, 'no_found_rows' => true, 'post_status' => 'publish', 'ignore_sticky_posts' => true)));
        if ($r->have_posts()) {
            echo str_replace("widget", "widget latest-posts", $before_widget);
            if ($title) {
                echo $before_title . $title . $after_title;
            }
            echo '<ul>' . "\n";
            while ($r->have_posts()) {
                $r->the_post();
                $post_title = esc_attr(get_the_title() ? get_the_title() : get_the_ID());
                $post_date = get_the_time(get_option('date_format'));
                echo '<li>' . "\n";
                echo '<a href="' . get_permalink() . '" title="' . $post_title . '">' . $post_title . '</a>' . "\n";
                echo '<span>'.$post_date.'</span>' . "\n";
                echo '</li>' . "\n";
            }
            echo '</ul>' . "\n";
            echo $after_widget;
            wp_reset_postdata();
        }

        $cache[$args['widget_id']] = ob_get_flush();
        wp_cache_set('widget_recent_posts', $cache, 'widget');
    }

    function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['post_type'] = array_key_exists('post_type', $new_instance)? $new_instance['post_type'] : 'post';
        $instance['number'] = (int) $new_instance['number'];
        $instance['show_date'] = (bool) $new_instance['show_date'];
        $this->flush_widget_cache();

        $alloptions = wp_cache_get( 'alloptions', 'options' );
        if ( isset($alloptions['widget_recent_entries']) )
            delete_option('widget_recent_entries');

        return $instance;
    }

    function form( $instance ) {
        $title     = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
        $post_type = isset($instance['post_type']) ? esc_attr($instance['post_type']) : 'post';
        $number    = isset( $instance['number'] ) ? absint( $instance['number'] ) : 5;
        $show_date = isset( $instance['show_date'] ) ? (bool) $instance['show_date'] : false;
        ?>
        <p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" /></p>
        <p style="padding-top: 0.3em;">
            <label style="width: 100%; display: inline-block;" for="<?php echo $this->get_field_id("post_type"); ?>"><?php _e('Post type:', 'finesse'); ?></label>
            <select style="width: 100%;" id="<?php echo $this->get_field_id("post_type"); ?>" name="<?php echo $this->get_field_name("post_type"); ?>">
                <option value="post"<?php selected($post_type, "post" ); ?>><?php _e('Blog Post', 'finesse'); ?></option>
                <option value="portfolio"<?php selected($post_type, "portfolio" ); ?>><?php _e('Portfolio', 'finesse'); ?></option>
            </select>
        </p>
        <p><label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php _e( 'Number of posts to show:' ); ?></label>
            <input id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="text" value="<?php echo $number; ?>" size="3" /></p>

        <p><input class="checkbox" type="checkbox" <?php checked( $show_date ); ?> id="<?php echo $this->get_field_id( 'show_date' ); ?>" name="<?php echo $this->get_field_name( 'show_date' ); ?>" />
            <label for="<?php echo $this->get_field_id( 'show_date' ); ?>"><?php _e( 'Display post date?' ); ?></label></p>
    <?php
    }
}
