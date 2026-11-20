<?php

class Dropcap_Shortcode extends Abstract_Finesse_Shortcode implements Shortcode_Designer
{
    private static $DC_ATTR_TYPE = "type";
    private static $DC_ATTR_WRAP = "wrap";

    function render($attr, $inner_content = null, $code = "")
    {
        $content = '';
        if (isset($inner_content) && strlen($inner_content) > 0) {
            extract(shortcode_atts(array(
                Dropcap_Shortcode::$DC_ATTR_TYPE => 'simple',
                Dropcap_Shortcode::$DC_ATTR_WRAP => ''), $attr));
            $inner_content = do_shortcode($this->prepare_content($inner_content));
            $first_letter = $inner_content[0];
            $rest_of = substr($inner_content, 1);

            $content .= "<span class=\"dropcap $type\">$first_letter</span>$rest_of";
            if (strlen($wrap) > 0) {
                $content = "<$wrap>$content</$wrap>";
            }
        }
        return $content;
    }

    function get_names()
    {
        return 'dropcap';
    }

    function get_visual_editor_form()
    {
        $content = '<form id="sc-dropcap-form" class="generic-form" method="post" action="#" data-sc="dropcap">';
        $content .= '<fieldset>';
        $content .= '<div>';
        $content .= '<label for="sc-dropcap-type">' . __('Type', 'finesse') . ':</label>';
        $content .= '<select id="sc-dropcap-type" name="sc-dropcap-type" data-attr-name="'.Dropcap_Shortcode::$DC_ATTR_TYPE.'" data-attr-type="attr">';
        $content .= '<option value="simple">' . __('Simple', 'finesse') . '</option>';
        $content .= '<option value="with-bg">' . __('With Background', 'finesse') . '</option>';
        $content .= '</select>';
        $content .= '</div>';
        $content .= '<div>';
        $content .= '<label for="sc-dropcap-content">' . __('Content', 'finesse') . ':</label>';
        $content .= '<textarea id="sc-dropcap-content" name="sc-dropcap-content" class="required" data-attr-type="content"></textarea>';
        $content .= '</div>';
        $content .= '<div>';
        $content .= '<label for="sc-dropcap-wct">' . __('Wrapper', 'finesse') . ':</label>';
        $content .= '<select id="sc-dropcap-wct" name="sc-dropcap-wct" data-attr-name="'.Dropcap_Shortcode::$DC_ATTR_WRAP.'" data-attr-type="attr">';
        $content .= '<option value="">' . __('None', 'finesse') . '</option>';
        $content .= '<option value="p">' . __('Paragraph', 'finesse') . ' (&lt;p&gt;)</option>';
        $content .= '<option value="div">' . __('Div', 'finesse') . ' (&lt;div&gt;)</option>';
        $content .= '</select>';
        $content .= '</div>';
        $content .= '<div >';
        $content .= '<input id="sc-dropcap-form-submit" type="submit" name="submit" value="' . __('Insert Dropcap', 'finesse') . '" class="button-primary">';
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
        return __('Dropcap', 'finesse');
    }
}
