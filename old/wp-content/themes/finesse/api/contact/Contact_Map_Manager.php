<?php

class Contact_Map_Manager
{
    private static $CONTACT_MAP_SETTINGS_META_KEY = 'finesse_contact_map_settings';

    function __construct()
    {
        if (is_user_logged_in()) {
            add_action('media_buttons', array($this, 'add_edit_contact_button_to_editor'), 30);
            add_action('wp_ajax_finesse-contact-map-settings-display', array($this, 'render_edit_contact_form'));
            add_action('wp_ajax_finesse-contact-map-settings-save', array($this, 'save_contact_map_settings'));
        }
    }

    function add_edit_contact_button_to_editor()
    {
        global $post;
        if (isset($post)) {
            $post_id = $post->ID;
            $title = __('Edit Contact', 'finesse');
            $url = get_option('siteurl') . '/wp-admin/admin-ajax.php?action=finesse-contact-map-settings-display&post-id=' . $post_id;
            echo '<a id="contact-editor-button-id" title="' . $title . '" class="button page-contact-editor-button" style="display: none;" href="' . $url . '">';
            echo '<span class="wp-sc-buttons-icon"></span> ' . $title . '</a>';
            echo '</a>';
        }
    }

    function render_edit_contact_form()
    {
        $post_id = $_REQUEST['post-id'];
        $address = get_contact_address();
        $change_address_link = site_url() . '/wp-admin/themes.php?page=finesse-theme-options&amp;tab=general-settings&amp;expand=contact-details';
        $map_settings_json = get_post_meta($post_id, Contact_Map_Manager::$CONTACT_MAP_SETTINGS_META_KEY, true);
        if (isset($map_settings_json) && !empty($map_settings_json)) {
            $map_settings = json_decode($map_settings_json);
            $localization_type = $map_settings->localization_type;
            $address_localization = $localization_type == 'address' ? ' selected' : '';
            $coordinates_localization = $localization_type == 'coordinates' ? ' selected' : '';
            $zoom = $map_settings->zoom;
            $map_height = $map_settings->height;
            $latitude = $map_settings->latitude;
            $longitude = $map_settings->longitude;
        } else {
            $address_localization = ' selected';
            $coordinates_localization = '';
            $zoom = '17';
            $map_height = '400';
            $latitude = '';
            $longitude = '';
        }
        $content = '<form id="contact-editor-form" class="generic-form" method="post" action="#">';
        $content .= '<fieldset>';
        $content .= '<div>';
        $content .= '<label for="contact-editor-loc-type">' . __('Localization Type', 'finesse') . ':</label>';
        $content .= '<select id="contact-editor-loc-type" name="contact-editor-loc-type">';
        $content .= '<option value="address"' . $address_localization . '>' . __('By Address', 'finesse') . '</option>';
        $content .= '<option value="coordinates"' . $coordinates_localization . '>' . __('By Coordinates', 'finesse') . '</option>';
        $content .= '</select>';
        $content .= '</div>';

        $content .= '<div>';
        $content .= '<label for="contact-editor-address">' . __('Address', 'finesse') . ':</label>';
        $content .= '<input id="contact-editor-address" name="contact-editor-address" type="text" value="' . $address . '" disabled>';
        $content .= '<a href="' . $change_address_link . '">' . __('Change Address', 'finesse') . '</a>';
        $content .= '</div>';
        $content .= '<div>';
        $content .= '<label for="contact-editor-lat">' . __('Latitude', 'finesse') . '</label>';
        $content .= '<input id="contact-editor-lat" name="contact-editor-lat" type="text" class="required number" value="' . $latitude . '">';
        $content .= '</div>';
        $content .= '<div>';
        $content .= '<label for="contact-editor-long">' . __('Longitude', 'finesse') . '</label>';
        $content .= '<input id="contact-editor-long" name="contact-editor-long" type="text" class="required number" value="' . $longitude . '">';
        $content .= '</div>';

        $content .= '<div>';
        $content .= '<label for="contact-editor-zoom">' . __('Map zoom', 'finesse') . ':</label>';
        $content .= '<input id="contact-editor-zoom" name="contact-editor-zoom" type="text" class="required number" value="' . $zoom . '">';
        $content .= '</div>';
        $content .= '<div>';
        $content .= '<label for="contact-editor-height">' . __('Map height (px)', 'finesse') . '</label>';
        $content .= '<input id="contact-editor-height" name="contact-editor-height" type="text" class="required number" value="' . $map_height . '">';
        $content .= '</div>';
        $content .= '<div >';
        $content .= '<input id="contact-editor-post-id" name="contact-editor-post-id" type="hidden" value="' . $post_id . '">';
        $content .= '<input id="contact-editor-form-save" type="submit" name="submit" value="' . __('Save', 'finesse') . '" class="button-primary">';
        $content .= '<input id="contact-editor-form-cancel" type="submit" name="submit" value="' . __('Cancel', 'finesse') . '" class="button-secondary">';
        $content .= '</div>';
        $content .= '</fieldset>';
        $content .= '</form>';
        echo $content;
        die();
    }

    function save_contact_map_settings()
    {
        $post_id = $_REQUEST['post-id'];
        $settings = $_REQUEST['settings'];

        $old_value = get_post_meta($post_id, Contact_Map_Manager::$CONTACT_MAP_SETTINGS_META_KEY, true);
        update_post_meta($post_id, Contact_Map_Manager::$CONTACT_MAP_SETTINGS_META_KEY, $settings, $old_value);
    }

    static function render_contact_map()
    {
        global $post;
        $map_settings_json = get_post_meta($post->ID, Contact_Map_Manager::$CONTACT_MAP_SETTINGS_META_KEY, true);
        if (isset($map_settings_json) && !empty($map_settings_json)) {
            $map_settings = json_decode($map_settings_json, false);
            $localization_type = $map_settings->localization_type;
            $zoom = $map_settings->zoom;
            $map_height = $map_settings->height;
            $latitude = $map_settings->latitude;
            $longitude = $map_settings->longitude;
        } else {
            $localization_type = 'address';
            $zoom = '17';
            $map_height = '400';
        }

        if ($localization_type == 'coordinates') {
            echo do_shortcode("[gmap loc_type=\"$localization_type\" lat=\"$latitude\" lng=\"$longitude\" zoom=\"$zoom\" height=\"$map_height\"][/gmap]");
        } else {
            $address = get_contact_address();
            echo do_shortcode("[gmap loc_type=\"$localization_type\" address=\"$address\" zoom=\"$zoom\" height=\"$map_height\"][/gmap]");
        }
    }

}
