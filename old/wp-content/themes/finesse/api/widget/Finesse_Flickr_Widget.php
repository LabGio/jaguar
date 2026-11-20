<?php

class Finesse_Flickr_Widget extends Finesse_Widget
{
    function __construct()
    {
        $widget_ops = array('classname' => 'Finesse_Flickr_Widget', 'description' => __('Displays the latest pictures from a Flickr account.', 'finesse'));
        $this->WP_Widget(Finesse_Widget::$FLICKR_WIDGET, '[Finesse] ' . __('Flickr Photostream', 'finesse'), $widget_ops);
    }

    function widget($args, $instance)
    {
        extract($args);
        $title = apply_filters('widget_title', $instance['title']);
        $pictures_count = $instance['pictures_count'];
        $flickr_id = get_flicker_id();

        echo str_replace('widget', 'widget flickr-widget', $before_widget);
        if ($title) {
            echo $before_title . $title . $after_title;
        }
        echo '<ul class="flickr-feed clearfix"></ul>';
        echo "<script type=\"text/javascript\">
            if(!document['flickrSettings']){
                document['flickrSettings'] = {id: '$flickr_id', limit: $pictures_count};
            }
		</script>";
        echo $after_widget; //defined by themes
    }

    function update($new_instance, $old_instance)
    {
        $instance = $old_instance;
        $instance['title'] = $new_instance['title'];
        $instance['pictures_count'] = intval($new_instance['pictures_count']);
        return $instance;
    }

    function form($instance)
    {
        $defaults = array(
            'title' => '',
            'pictures_count' => '6'
        );
        $instance = wp_parse_args((array)$instance, $defaults);
        $id_label = 'ID (<a href="' . site_url() . '/wp-admin/themes.php?page=finesse-theme-options&amp;tab=social-settings&amp;expand=flickr-id" target="_blank">Change</a>)';
        $this->print_info_field($id_label, get_flicker_id());
        $this->print_text_field($instance, 'title', __('Title', 'finesse'));
        $this->print_text_field($instance, 'pictures_count', __('The number of pictures', 'finesse'));
    }
}

?>