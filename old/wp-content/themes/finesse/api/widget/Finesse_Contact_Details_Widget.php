<?php

class Finesse_Contact_Details_Widget extends Finesse_Widget
{
    function __construct()
    {
        $widget_ops = array('classname' => 'Finesse_Contact_Details_Widget', 'description' => __('A simple widget which allows you to edit your contact details.', 'finesse'));
        $this->WP_Widget(Finesse_Widget::$CONTACT_DETAILS_WIDGET, '[Finesse] ' . __('Contact Info', 'finesse'), $widget_ops);
    }

    function widget($args, $instance)
    {
        extract($args);
        $title = apply_filters('widget_title', $instance['title']);
        $address = $instance['address'];
        $phone = $instance['phone'];
        $fax = isset($instance['fax'])?$instance['fax']:'';
        $email = $instance['email'];
        $timetable = $instance['timetable'];
        $show_social_links = $instance['show_social_links'];

        echo str_replace('widget', 'widget contact-info', $before_widget);
        if ($title) {
            echo $before_title . $title . $after_title;
        }
        if(isset($address) && !empty($address)){
            echo '<p class="address"><strong>' . __('Address', 'finesse') . ':</strong> ' . $address . '</p>' . "\n";
        }
        if(isset($phone) && !empty($phone)){
            echo '<p class="phone"><strong>' . __('Phone', 'finesse') . ':</strong> ' . $phone . '</p>' . "\n";
        }
        if(isset($fax) && !empty($fax)){
            echo '<p class="fax"><strong>' . __('Fax', 'finesse') . ':</strong> ' . $fax . '</p>' . "\n";
        }
        if(isset($email) && !empty($email)){
            echo '<p class="email"><strong>' . __('Email', 'finesse') . ':</strong> <a href="mailto:' . $email . '">' . $email . '</a></p>' . "\n";
        }
        if (isset($timetable) && !empty($timetable)) {
            echo '<p class="business-hours"><strong>' . __('Business Hours', 'finesse') . ':</strong>' . ' <br>' . "\n";
            echo str_replace(array("\r\n","\r","\n","\\r","\\n","\\r\\n"),"<br/>",$timetable) . "\n";
            echo '</p>' . "\n";
        }
        if (isset($show_social_links) && $show_social_links == 'on') {
            echo '<div class="social-links">' . "\n";
            echo '<h4>' . __('Follow Us', 'finesse') . '</h4>' . "\n";
            echo '<ul>' . "\n";

            $twitter_username = get_twitter_username();
            $facebook_url = get_facebook_url();
            $gplus_url = get_gplus_url();
            $linkedin_url = get_linkedin_url();
            $youtube_url = get_youtube_url();
            $skype_url = get_skype_url();
            if (strlen($twitter_username) > 0) {
                echo '<li class="twitter"><a href="https://twitter.com/#!/' . $twitter_username . '" title="Twitter" target="_blank">Twitter</a></li>';
            }
            if (strlen($facebook_url) > 0) {
                echo '<li class="facebook"><a href="' . $facebook_url . '" title="Facebook" target="_blank">Facebook</a></li>';
            }
            if (strlen($gplus_url) > 0) {
                echo '<li class="google"><a href="' . $gplus_url . '" title="Google+" target="_blank">Google+</a></li>';
            }
            if (strlen($linkedin_url) > 0) {
                echo '<li class="linkedin"><a href="' . $linkedin_url . '" title="LinkedIn" target="_blank">LinkedIn</a></li>';
            }
            if (strlen($youtube_url) > 0) {
                echo '<li class="youtube"><a href="' . $youtube_url . '" title="YouTube" target="_blank">YouTube</a></li>';
            }
            if (strlen($skype_url) > 0) {
                echo '<li class="skype"><a href="' . $skype_url . '" title="Skype" target="_blank">Skype</a></li>';
            }
            if (is_rss_enabled()) {
                echo '<li class="rss"><a target="_blank" href="' . get_rss_url() . '" title="RSS">RSS</a></li>';
            }

            echo '</ul>' . "\n";
            echo '</div>' . "\n";
        }
        echo $after_widget; //defined by themes
    }

    function update($new_instance, $old_instance)
    {
        $instance = $old_instance;
        $instance['title'] = $new_instance['title'];
        $instance['address'] = $new_instance['address'];
        $instance['phone'] = $new_instance['phone'];
        $instance['fax'] = $new_instance['fax'];
        $instance['email'] = $new_instance['email'];
        $instance['timetable'] = $new_instance['timetable'];
        $instance['show_social_links'] = $new_instance['show_social_links'];
        return $instance;
    }

    function form($instance)
    {
        $defaults = array(
            'title' => 'Contact Info',
            'address' => get_contact_address(),
            'phone' => get_contact_phone(),
            'email' => get_contact_email(),
            'fax' => '',
            'timetable' => '',
            'show_social_links' => '',
        );
        $instance = wp_parse_args((array)$instance, $defaults);

        $this->print_text_field($instance, 'title', __('Title', 'finesse'));
        $this->print_text_field($instance, 'address', __('Address', 'finesse'));
        $this->print_text_field($instance, 'phone', __('Phone Number', 'finesse'));
        $this->print_text_field($instance, 'fax', __('Fax Number', 'finesse'));
        $this->print_text_field($instance, 'email', __('Email Address', 'finesse'));
        $this->print_textarea_field($instance, 'timetable', __('Timetable', 'finesse'));
        $show_social_links_label = __('Show Social Links', 'finesse') . ' (<a href="' . site_url() . '/wp-admin/themes.php?page=finesse-theme-options&amp;tab=social-settings" target="_blank">' . __('Configure', 'finesse') . '</a>)';
        $this->print_checkbox_field($instance, 'show_social_links', $show_social_links_label);
    }
}