<?php

class Finesse_Widget extends WP_Widget
{

    static $ABOUT_SITE_WIDGET = "Finesse_About_Site_Widget";
    static $CONTACT_DETAILS_WIDGET = "Finesse_Contact_Details_Widget";
    static $LATEST_TWEETS_WIDGET = "Finesse_Latest_Tweets_Widget";
    static $SIMILAR_PROJECTS_WIDGET = "Finesse_Similar_Projects_Widget";
    static $GOOGLE_MAP_WIDGET = "Finesse_Google_Map_Widget";
    static $BLOG_MORE_CONTENT_WIDGET = "Finesse_Blog_More_Content_Widget";
    static $VIDEO_WIDGET = "Finesse_Video_Widget";
    static $FLICKR_WIDGET = "Finesse_Flickr_Widget";
    static $POST_CATEGORIES = "Finesse_Categories_Widget";
    static $POST_ARCHIVES = "Finesse_Archives_Widget";
    static $MOST_USED_TAGS = "Finesse_Most_Used_Tags_Widget";

    static $TEXT_WIDGET = "Finesse_Text_Widget";
    static $RECENT_POSTS_WIDGET = "Finesse_Recent_Posts_Widget";

    function print_info_field($label, $value)
    {
        $id = uniqid();
        echo '<p>';
        echo '<label for="' . $id . '">' . $label . '</label>';
        echo '<input id="' . $id . '" type="text" class="widefat" disabled="disabled" value="' . $value . '" />';
        echo '</p >';
    }

    function print_text_field($instance, $field, $text, $id = '', $display = true)
    {
        $field_id = strlen($id) == 0 ? $this->get_field_id($field) : $id;
        $field_name = $this->get_field_name($field);
        $field_value = esc_textarea(strip_tags($instance[$field]));
        $style = $display ? '' : ' style="display:none;"';
        echo '<p id="' . $field_id . '-p" ' . $style . '>';
        echo '<label for="' . $field_id . '" >' . __($text, 'finesse') . '</label>';
        echo '<input type="text" class="widefat" id="' . $field_id . '" name="' . $field_name . '" value="' . $field_value . '" />';
        echo '</p >';
    }

    function print_checkbox_field($instance, $field, $text, $id = '', $display = true)
    {
        $field_id = strlen($id) == 0 ? $this->get_field_id($field) : $id;
        $field_name = $this->get_field_name($field);
        $field_value = (strtolower($instance[$field]) === 'on') ? 'checked' : '';
        $style = $display ? '' : ' style="display:none;"';
        echo '<p id="' . $field_id . '-p" ' . $style . '>';
        echo '<input type="checkbox" id="' . $field_id . '" name="' . $field_name . '" ' . $field_value . ' />';
        echo '<label for="' . $field_id . '" > ' . __($text, 'finesse') . '</label>';
        echo '</p >';
    }

    function print_textarea_field($instance, $field, $text, $strip_tags = true, $id = '', $display = true)
    {
        $field_id = strlen($id) == 0 ? $this->get_field_id($field) : $id;
        $field_name = $this->get_field_name($field);
        $field_value = $strip_tags ? esc_textarea(strip_tags($instance[$field])) : $instance[$field];
        $style = $display ? '' : ' style="display:none;"';
        echo '<p id="' . $field_id . '-p" ' . $style . '>';
        echo '<label for="' . $field_id . '" >' . __($text, 'finesse') . '</label>';
        echo '<textarea class="widefat" rows="10" cols="10" id="' . $field_id . '" name="' . $field_name . '" >' . $field_value . '</textarea>';
        echo '</p >';
    }

    function print_select($instance, $field, $options, $text, $id = '', $display = true, $onchange_function = '')
    {
        $field_id = strlen($id) == 0 ? $this->get_field_id($field) : $id;
        $field_name = $this->get_field_name($field);
        $field_value = esc_textarea(strip_tags($instance[$field]));
        $style = $display ? '' : ' style="display:none;"';
        echo '<p id="' . $field_id . '-p" ' . $style . '>';
        echo '<label for="' . $field_id . '" >' . __($text, 'finesse') . '</label>';
        $onchange = strlen($onchange_function) > 0 ? ' onchange=' . $onchange_function . '(this);' : '';
        echo '<select ' . $onchange . ' class="widefat" id="' . $field_id . '" name="' . $field_name . '">';
        foreach ($options as $key => $value) {
            if ($field_value == $key) {
                $selected = ' selected="selected"';
            } else {
                $selected = '';
            }
            echo '<option value="' . $key . '" ' . $selected . '>' . $value . '</option>';
        }
        echo '</select>';
        echo '</p >';
    }
}
