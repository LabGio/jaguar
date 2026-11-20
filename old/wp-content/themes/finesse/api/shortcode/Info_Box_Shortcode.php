<?php

class Info_Box_Shortcode extends Abstract_Finesse_Shortcode implements Shortcode_Designer
{

    private static $IB_ATTR_TITLE = "title";
    private static $IB_ATTR_BTN_HREF = "button_href";
    private static $IB_ATTR_BTN_TARGET = "target";
    private static $IB_ATTR_BTN_TEXT = "button_text";
    private static $IB_ATTR_BTN_COLOR = "button_color";

    function render($attr, $inner_content = null, $code = "")
    {
        $inner_content = do_shortcode($this->prepare_content($inner_content));
        extract(shortcode_atts(array(
            Info_Box_Shortcode::$IB_ATTR_TITLE => '',
            Info_Box_Shortcode::$IB_ATTR_BTN_HREF => '#',
            Info_Box_Shortcode::$IB_ATTR_BTN_TARGET => '',
            Info_Box_Shortcode::$IB_ATTR_BTN_TEXT => '',
            Info_Box_Shortcode::$IB_ATTR_BTN_COLOR => ''), $attr));

        $title = ___($title);
        $content = "<div class=\"infobox\">\n";
        $content .= "<div class=\"infobox-inner\">\n";
        if (strlen($button_text) > 0) {
            $target = strlen($target) > 0 ? ' target="' . $target . '"' : '';
            $href = strlen($button_href) > 0 ? ' href="' . $button_href . '"' : ' href="#"';
            $color = strlen($button_color) > 0 ? ' color="' . $button_color . '"' : '';
            $button = do_shortcode('[button' . $target . $href . $color . ' type="large"]' . $button_text . '[/button]');
            $content .= $button;
        }
        $content .= "\n<div class=\"with-button\">\n";
        if (strlen($title) > 0) {
            $content .= "<h2>$title</h2>\n";
        }
        $content .= "<p>$inner_content</p>\n";
        $content .= "</div>\n";
        if (isset($button)) {
            $content .= str_replace('button large', 'button large mobile-button', $button) . "\n";
        }

        $content .= "</div>\n";
        $content .= "</div>\n";

        return $content;
    }

    function get_names()
    {
        return 'infobox';
    }

    function get_visual_editor_form()
    {
        $content = '<form id="sc-infobox-form" class="generic-form" method="post" action="#" data-sc="infobox">';
        $content .= '<fieldset>';
        $content .= '<div>';
        $content .= '<label for="sc-infobox-title">' . __('Title', 'finesse') . ':</label>';
        $content .= '<input id="sc-infobox-title" name="sc-infobox-title" type="text" class="required" data-attr-name="' . Info_Box_Shortcode::$IB_ATTR_TITLE . '" data-attr-type="attr">';
        $content .= '</div>';
        $content .= '<div>';
        $content .= '<label for="sc-infobox-content">' . __('Content', 'finesse') . ':</label>';
        $content .= '<textarea id="sc-infobox-content" name="sc-infobox-content" class="required" data-attr-type="content"></textarea>';
        $content .= '</div>';
        $content .= '<div>';
        $content .= '<label for="sc-infobox-button-href">' . __('Button URL', 'finesse') . ':</label>';
        $content .= '<input id="sc-infobox-button-href" name="sc-infobox-button-href" type="text" data-attr-name="' . Info_Box_Shortcode::$IB_ATTR_BTN_HREF . '" data-attr-type="attr">';
        $content .= '</div>';
        $content .= '<div>';
        $content .= '<label for="sc-infobox-button-text">' . __('Button Text', 'finesse') . ':</label>';
        $content .= '<input id="sc-infobox-button-text" name="sc-infobox-button-text" type="text" data-attr-name="' . Info_Box_Shortcode::$IB_ATTR_BTN_TEXT . '" data-attr-type="attr">';
        $content .= '</div>';
        $content .= '<div>';
        $content .= '<label>' . __('Button Color', 'finesse') . ':</label>';
        $content .= '<ul data-ref="sc-infobox-button-color" class="color-thumbs">';
        foreach (Button_Shortcode::$predefined_colors as $key => $value) {
            $content .= "<li><a class=\"$value\" title=\"$key\" href=\"#\" data-color=\"$value\"></a></li>";
        }
        $content .= '</ul>';
        $content .= '<input id="sc-infobox-button-color" name="sc-infobox-button-color" type="hidden" data-attr-name="' . Info_Box_Shortcode::$IB_ATTR_BTN_COLOR . '" data-attr-type="attr">';
        $content .= '</div>';
        $content .= '<div >';
        $content .= '<input id="sc-infobox-form-submit" type="submit" name="submit" value="' . __('Insert Info Box', 'finesse') . '" class="button-primary">';
        $content .= '</div>';
        $content .= '</fieldset>';
        $content .= '</form>';

        return $content;
    }

    function get_group_title()
    {
        return __('Elements', 'finesse');
    }

    function get_title()
    {
        return __('Info Box', 'finesse');
    }
}
