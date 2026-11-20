<?php

class Button_Shortcode extends Abstract_Finesse_Shortcode implements Shortcode_Designer
{

    private static $BTN_ATTR_HREF = "href";
    private static $BTN_ATTR_COLOR = "color";
    private static $BTN_ATTR_TYPE = "type";
    private static $BTN_ATTR_TARGET = "target";
    private static $BTN_ATTR_REL = "rel";
    private static $BTN_ATTR_CLASS = "class";
    static $predefined_colors = array(
        'Use the Theme Color' => '',
        'Orange' => 'orange',
        'Green' => 'green',
        'Blue' => 'blue',
        'Light Orange' => 'light-orange',
        'Red' => 'red',
        'Purple' => 'purple',
        'Pink' => 'pink',
        'Teal' => 'teal',
        'Black' => 'black');

    function render($attr, $inner_content = null, $code = '')
    {
        extract(shortcode_atts(array(
            Button_Shortcode::$BTN_ATTR_HREF => '#',
            Button_Shortcode::$BTN_ATTR_COLOR => '',
            Button_Shortcode::$BTN_ATTR_TARGET => '',
            Button_Shortcode::$BTN_ATTR_REL => '',
            Button_Shortcode::$BTN_ATTR_CLASS => '',
            Button_Shortcode::$BTN_ATTR_TYPE => 'basic'), $attr));
        $target = empty($target) ? '' : 'target="' . $target . '"';
        $rel = empty($rel) ? '' : 'rel="' . $rel . '"';
        $class_value = 'button';
        if (!empty($class)) {
            $class_value = $class_value . ' ' . $class;
        }
        if ($type == 'large') {
            return "<a class=\"$class_value large $color\" href=\"$href\" $target $rel>$inner_content</a>";
        } else {
            return "<a class=\"$class_value $color\" href=\"$href\" $target $rel>$inner_content</a>";
        }
    }

    function get_names()
    {
        return array('button', 'btn');
    }

    function get_visual_editor_form()
    {
        $content = '<form id="sc-button-form" class="generic-form" method="post" action="#" data-sc="btn">';
        $content .= '<fieldset>';
        $content .= '<div>';
        $content .= '<label for="sc-button-href">' . __('URL', 'finesse') . ':</label>';
        $content .= '<input id="sc-button-href" name="sc-button-href" type="text" class="required" data-attr-name="' . Button_Shortcode::$BTN_ATTR_HREF . '" data-attr-type="attr">';
        $content .= '</div>';
        $content .= '<div>';
        $content .= '<label for="sc-button-text">' . __('Text', 'finesse') . ':</label>';
        $content .= '<input id="sc-button-text" name="sc-button-text" type="text" class="required" data-attr-type="content">';
        $content .= '</div>';
        $content .= '<div>';
        $content .= '<label>' . __('Bg Color', 'finesse') . ':</label>';
        $content .= '<ul data-ref="sc-bg-color" class="color-thumbs">';
        foreach (Button_Shortcode::$predefined_colors as $key => $value) {
            $content .= "<li><a class=\"$value\" title=\"$key\" href=\"#\" data-color=\"$value\"></a></li>";
        }
        $content .= '</ul>';
        $content .= '<input id="sc-bg-color" name="sc-bg-color" type="hidden" data-attr-name="' . Button_Shortcode::$BTN_ATTR_COLOR . '" data-attr-type="attr">';
        $content .= '</div>';
        $content .= '<div>';
        $content .= '<label for="sc-button-type">' . __('Type', 'finesse') . ':</label>';
        $content .= '<select id="sc-button-type" name="sc-button-type" data-attr-name="' . Button_Shortcode::$BTN_ATTR_TYPE . '" data-attr-type="attr">';
        $content .= '<option value="">' . __('Normal Button', 'finesse') . '</option>';
        $content .= '<option value="large">' . __('Large Button', 'finesse') . '</option>';
        $content .= '</select>';
        $content .= '</div>';
        $content .= '<div >';
        $content .= '<input id="sc-button-form-submit" type="submit" name="submit" value="' . __('Insert Button', 'finesse') . '" class="button-primary">';
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
        return __('Button', 'finesse');
    }
}
