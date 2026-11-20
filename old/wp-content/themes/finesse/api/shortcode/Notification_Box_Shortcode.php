<?php

class Notification_Box_Shortcode extends Abstract_Finesse_Shortcode implements Shortcode_Designer
{

    private static $NB_ATTR_ID = "id";
    private static $NB_ATTR_CLOSEABLE = "closeable";
    private static $NB_ATTR_TYPE = "type";
    private static $NB_ATTR_DISPLAY = "display";

    function render($attr, $inner_content = null, $code = "")
    {
        $uuid = uniqid();
        extract(shortcode_atts(array(
            Notification_Box_Shortcode::$NB_ATTR_ID => $uuid,
            Notification_Box_Shortcode::$NB_ATTR_CLOSEABLE => 'true',
            Notification_Box_Shortcode::$NB_ATTR_TYPE => 'info',
            Notification_Box_Shortcode::$NB_ATTR_DISPLAY => 'true'
        ), $attr));
        switch ($type) {
            case 'info':
            case 'success':
            case 'warning':
            case 'error':
                return $this->generate_notification_box($id, $inner_content, $type, $closeable === 'true', $display === 'true');
            default:
                return $this->generate_notification_box($id, $inner_content, 'info', $closeable === 'true', $display === 'true');
        }
    }

    private function generate_notification_box($id, $text, $type, $closeable, $display)
    {
        $display_style = $display ? '' : 'style="display: none;"';
        $content = "<div id=\"" . $id . "\" class=\"notification-box notification-box-$type\" $display_style>\n";
        $content .= "<p id=\"" . $id . "-p\">$text</p>\n";
        if ($closeable) {
            $content .= "<a href=\"#\" class=\"notification-close notification-close-$type\">x</a>\n";
        }
        $content .= "</div>";
        return $content;
    }

    function get_names()
    {
        return array('notification', 'notif');
    }

    function get_visual_editor_form()
    {
        $content = '<form id="sc-notifbox-form" class="generic-form" method="post" action="#" data-sc="notif">';
        $content .= '<fieldset>';
        $content .= '<div>';
        $content .= '<label for="sc-notifbox-id">' . __('ID', 'finesse') . ':</label>';
        $content .= '<input id="sc-notifbox-id" name="sc-notifbox-id" type="text" data-attr-name="'.Notification_Box_Shortcode::$NB_ATTR_ID.'" data-attr-type="attr">';
        $content .= '</div>';
        $content .= '<div>';
        $content .= '<label for="sc-notifbox-content">' . __('Content', 'finesse') . ':</label>';
        $content .= '<textarea id="sc-notifbox-content" name="sc-notifbox-content" class="required" data-attr-type="content"></textarea>';
        $content .= '</div>';
        $content .= '<div>';
        $content .= '<label for="sc-notifbox-type">' . __('Type', 'finesse') . ':</label>';
        $content .= '<select id="sc-notifbox-type" name="sc-notifbox-type" data-attr-name="'.Notification_Box_Shortcode::$NB_ATTR_TYPE.'" data-attr-type="attr">';
        $content .= '<option value="info">' . __('Info', 'finesse') . '</option>';
        $content .= '<option value="success">' . __('Success', 'finesse') . '</option>';
        $content .= '<option value="warning">' . __('Warning', 'finesse') . '</option>';
        $content .= '<option value="error">' . __('Error', 'finesse') . '</option>';
        $content .= '</select>';
        $content .= '</div>';
        $content .= '<div>';
        $content .= '<input id="sc-notifbox-closeable" name="sc-notifbox-closeable" type="checkbox" checked data-attr-name="'.Notification_Box_Shortcode::$NB_ATTR_CLOSEABLE.'" data-attr-type="attr">';
        $content .= '<label for="sc-notifbox-closeable">' . __('Display the close button', 'finesse') . '</label>';
        $content .= '</div>';
        $content .= '<div>';
        $content .= '<input id="sc-notifbox-display" name="sc-notifbox-display" type="checkbox" checked data-attr-name="'.Notification_Box_Shortcode::$NB_ATTR_DISPLAY.'" data-attr-type="attr">';
        $content .= '<label for="sc-notifbox-display">' . __('Display after the page is loaded', 'finesse') . '</label>';
        $content .= '</div>';
        $content .= '<div >';
        $content .= '<input id="sc-notifbox-form-submit" type="submit" name="submit" value="' . __('Insert Notification Box', 'finesse') . '" class="button-primary">';
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
        return __('Notification Box', 'finesse');
    }
}