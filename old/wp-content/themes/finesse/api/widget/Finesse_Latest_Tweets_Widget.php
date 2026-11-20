<?php

class Finesse_Latest_Tweets_Widget extends Finesse_Widget
{

    function Finesse_Latest_Tweets_Widget()
    {
        $widget_ops = array('classname' => 'Finesse_Latest_Tweets_Widget', 'description' => __('A simple widget which displays your latest tweets.', 'finesse'));
        $this->WP_Widget(Finesse_Widget::$LATEST_TWEETS_WIDGET, '[Finesse] ' . __('Display Latest Tweets', 'finesse'), $widget_ops);
    }

    function widget($args, $instance)
    {
        extract($args);
        $title = apply_filters('widget_title', $instance['title']);
        $username = get_twitter_username();
        $tweets_count = min(intval($instance['tweets_count']), 20);

        echo str_replace('widget', 'widget twitter-widget', $before_widget);
        if ($title) {
            echo $before_title . $title . $after_title;
        }
        $proxy_url = site_url('wp-admin/admin-ajax.php?action=fin-twitter-proxy');
        echo '<div class="tweet"></div>';
        echo "<script type=\"text/javascript\">
            if(!document['twitterSettings']){
                document['twitterSettings'] = {username: '$username', count: $tweets_count, proxyURL: '$proxy_url'};
            }
		</script>";
        echo $after_widget; //defined by themes
    }

    function update($new_instance, $old_instance)
    {
        $instance = $old_instance;
        $instance['title'] = $new_instance['title'];
        $instance['username'] = $new_instance['username'];
        $instance['tweets_count'] = $new_instance['tweets_count'];
        return $instance;
    }

    function form($instance)
    {
        /* Default widget values. */
        $defaults = array(
            'title' => __('Latest Tweets', 'finesse'),
            'tweets_count' => '2',
        );
        $instance = wp_parse_args((array)$instance, $defaults);
        $username_label = __('Username', 'finesse');
        $change_label = __('Change', 'finesse');
        $id_label = $username_label . ' (<a href="' . site_url() . '/wp-admin/themes.php?page=finesse-theme-options&amp;tab=social-settings&amp;expand=twitter-username" target="_blank">' . $change_label . '</a>)';
        $this->print_info_field($id_label, get_twitter_username());
        $this->print_text_field($instance, 'title', __('Title', 'finesse'));
        $this->print_text_field($instance, 'tweets_count', __('Number of tweets(max 20)', 'finesse'));
    }

}
