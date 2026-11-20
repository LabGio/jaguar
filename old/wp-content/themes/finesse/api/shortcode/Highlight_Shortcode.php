<?php

class Highlight_Shortcode extends Abstract_Finesse_Shortcode implements Shortcode_Designer
{

    private static $HL_ATTR_COLOR = "color";
    private static $HL_ATTR_WRAP = "wrap";

    function render($attr, $inner_content = null, $code = "")
    {
        $content = '';
        if (isset($inner_content) && strlen($inner_content) > 0) {
            extract(shortcode_atts(array(
                Highlight_Shortcode::$HL_ATTR_COLOR => 'colored',
                Highlight_Shortcode::$HL_ATTR_WRAP => ''), $attr));
            $inner_content = do_shortcode($this->prepare_content($inner_content));
            $class = 'colored';
            if (strtolower($color) == 'black') {
                $class = 'black';
            }

            $content .= "<mark class=\"highlight $class\">$inner_content</mark>";
            if (strlen($wrap) > 0) {
                $content = "<$wrap>$content</$wrap>";
            }
        }
        return $content;
    }

    function get_names()
    {
        return array('highlight', 'hl');
    }

    function get_visual_editor_form()
    {
        $content = '<form id="sc-highlight-form" class="generic-form" method="post" action="#" data-sc="hl">';
        $content .= '<fieldset>';
        $content .= '<div>';
        $content .= '<label>' . __('Color', 'finesse') . ':</label>';
        $content .= '<ul data-ref="sc-highlight-color" class="color-thumbs">';
        $content .= '<li><a class="colored" title="Default Color" href="#" data-color="colored"></a></li>';
        $content .= '<li><a class="black" title="Black" href="#" data-color="black"></a></li>';
        $content .= '</ul>';
        $content .= '<input id="sc-highlight-color" name="sc-highlight-color" value="colored" type="hidden" data-attr-name="'.Highlight_Shortcode::$HL_ATTR_COLOR.'" data-attr-type="attr">';
        $content .= '</div>';
        $content .= '<div>';
        $content .= '<label for="sc-highlight-content">' . __('Content', 'finesse') . ':</label>';
        $content .= '<textarea id="sc-highlight-content" name="sc-highlight-content" class="required" data-attr-type="content"></textarea>';
        $content .= '</div>';
        $content .= '<div>';
        $content .= '<label for="sc-highlight-wct">' . __('Wrapper', 'finesse') . ':</label>';
        $content .= '<select id="sc-highlight-wct" name="sc-highlight-wct" data-attr-name="'.Highlight_Shortcode::$HL_ATTR_WRAP.'" data-attr-type="attr">';
        $content .= '<option value="">' . __('None', 'finesse') . '</option>';
        $content .= '<option value="p">' . __('Paragraph', 'finesse') . ' (&lt;p&gt;)</option>';
        $content .= '<option value="div">' . __('Div', 'finesse') . ' (&lt;div&gt;)</option>';
        $content .= '</select>';
        $content .= '</div>';
        $content .= '<div >';
        $content .= '<input id="sc-highlight-form-submit" type="submit" name="submit" value="' . __('Insert Highlight Text', 'finesse') . '" class="button-primary">';
        $content .= '</div>';
        $content .= '</fieldset>';
        $content .= '</form>';
        return $content;
    }

    function get_group_title()
    {
        return __('Typography', 'finesse');
    }

    function get_title()
    {
        return __('Highlight', 'finesse');
    }
}
