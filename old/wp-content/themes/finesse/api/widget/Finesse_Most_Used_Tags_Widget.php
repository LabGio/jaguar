<?php

class Finesse_Most_Used_Tags_Widget extends Finesse_Widget
{
    function __construct()
    {
        $widget_ops = array('classname' => 'Finesse_Most_Used_Tags_Widget', 'description' => __('Your most used tags', 'finesse'));
        $this->WP_Widget(Finesse_Widget::$MOST_USED_TAGS, '[Finesse] ' . __('Most Used Tags', 'finesse'), $widget_ops);
    }

    function widget($args, $instance)
    {
        extract($args);
        $title = apply_filters('widget_title', $instance['title']);
        $count = $instance['count'];
        if ($count == '0') {
            $count = '';
        }

        echo $before_widget;
        if ($title) {
            echo $before_title . $title . $after_title;
        }
        $tags = get_terms('post_tag', array('orderby' => 'count', 'order' => 'DESC', 'number' => $count));

        echo '<ul class="tags clearfix">' . "\n";
        foreach ($tags as $tag) {
            $tag_link = get_term_link(intval($tag->term_id), $tag->taxonomy);
            $tag_link = '#' != $tag_link ? esc_url($tag_link) : '#';
            echo '<li><a href="' . $tag_link . '">' . $tag->name . '</a></li>';
        }
        echo '</ul>' . "\n";

        echo $after_widget; //defined by themes
    }

    function update($new_instance, $old_instance)
    {
        $instance = $old_instance;
        $instance['title'] = $new_instance['title'];
        $instance['count'] = $new_instance['count'];
        return $instance;
    }

    function form($instance)
    {
        $defaults = array(
            'title' => '',
            'count' => '10'
        );
        $instance = wp_parse_args((array)$instance, $defaults);

        $this->print_text_field($instance, 'title', __('Title', 'finesse'));
        $this->print_text_field($instance, 'count', __('Number of Tags', 'finesse'));
    }
}