<?php

class Finesse_Categories_Widget extends WP_Widget_Categories
{
    function __construct() {
        parent::__construct();
    }

    function widget($args, $instance)
    {
        extract($args);

        $title = apply_filters('widget_title', empty($instance['title']) ? __('Categories', 'finesse') : $instance['title'], $instance, $this->id_base);
        $post_type = array_key_exists('post_type', $instance)? $instance['post_type'] : 'post';
        $c = !empty($instance['count']) ? '1' : '0';
        $h = !empty($instance['hierarchical']) ? '1' : '0';
        $d = !empty($instance['dropdown']) ? '1' : '0';
        $taxonomy = ($post_type == 'portfolio')? 'filter' : 'category';

        echo $before_widget;
        if ($title) {
            echo $before_title . $title . $after_title;
        }

        $cat_args = array('post_type' => $post_type, 'taxonomy' => $taxonomy, 'orderby' => 'name', 'show_count' => $c, 'hierarchical' => $h);

        if ($d) {
            $home_url = home_url();
            $cat_args['show_option_none'] = __('Select Category', 'finesse');
            wp_dropdown_categories(apply_filters('widget_categories_dropdown_args', $cat_args));
            echo "<script type=\"text/javascript\">
            var dropdown = document.getElementById(\"cat\");
            function onCatChange() {
                if ( dropdown.options[dropdown.selectedIndex].value > 0 ) {
                    location.href = \"$home_url/?cat=\"+dropdown.options[dropdown.selectedIndex].value;
                }
            }
            dropdown.onchange = onCatChange;
        </script>";

        } else {
            echo '<ul class="menu">' . "\n";
            $cat_args['title_li'] = '';
            wp_list_categories(apply_filters('widget_categories_args', $cat_args));
            echo '</ul>' . "\n";
        }
        echo $after_widget;
    }

    function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['post_type'] = array_key_exists('post_type', $new_instance)? $new_instance['post_type'] : 'post';
        $instance['count'] = !empty($new_instance['count']) ? 1 : 0;
        $instance['hierarchical'] = !empty($new_instance['hierarchical']) ? 1 : 0;
        $instance['dropdown'] = !empty($new_instance['dropdown']) ? 1 : 0;

        return $instance;
    }

    function form( $instance ) {
        //Defaults
        $instance = wp_parse_args( (array) $instance, array( 'title' => '') );
        $post_type = array_key_exists('post_type', $instance)? $instance['post_type'] : 'post';
        $title = esc_attr( $instance['title'] );
        $count = isset($instance['count']) ? (bool) $instance['count'] :false;
        $hierarchical = isset( $instance['hierarchical'] ) ? (bool) $instance['hierarchical'] : false;
        $dropdown = isset( $instance['dropdown'] ) ? (bool) $instance['dropdown'] : false;
        ?>
        <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e( 'Title:' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></p>
        <p style="padding-top: 0.3em;">
            <label style="width: 100%; display: inline-block;" for="<?php echo $this->get_field_id("post_type"); ?>"><?php _e('Post type:', 'finesse'); ?></label>
            <select style="width: 100%;" id="<?php echo $this->get_field_id("post_type"); ?>" name="<?php echo $this->get_field_name("post_type"); ?>">
                <option value="post"<?php selected($post_type, "post" ); ?>><?php _e('Blog Post', 'finesse'); ?></option>
                <option value="portfolio"<?php selected($post_type, "portfolio" ); ?>><?php _e('Portfolio', 'finesse'); ?></option>
            </select>
        </p>
        <p><input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('dropdown'); ?>" name="<?php echo $this->get_field_name('dropdown'); ?>"<?php checked( $dropdown ); ?> />
            <label for="<?php echo $this->get_field_id('dropdown'); ?>"><?php _e('Display as dropdown', 'finesse'); ?></label><br />

            <input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('count'); ?>" name="<?php echo $this->get_field_name('count'); ?>"<?php checked( $count ); ?> />
            <label for="<?php echo $this->get_field_id('count'); ?>"><?php _e('Show post counts', 'finesse'); ?></label><br />

            <input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('hierarchical'); ?>" name="<?php echo $this->get_field_name('hierarchical'); ?>"<?php checked( $hierarchical ); ?> />
            <label for="<?php echo $this->get_field_id('hierarchical'); ?>"><?php _e('Show hierarchy', 'finesse'); ?></label></p>
    <?php
    }

}
