<?php

class Preformatted_Shortcode extends Abstract_Finesse_Shortcode implements Shortcode_Designer
{
    private static $PRE_ATTR_TYPE = "type";
    private static $PRE_ATTR_WRAP = "wrap";

    function render($attr, $inner_content = null, $code = "")
    {
        $content = '';
        if (isset($inner_content) && strlen($inner_content) > 0) {
            extract(shortcode_atts(array(
                Preformatted_Shortcode::$PRE_ATTR_TYPE => 'pre',
                Preformatted_Shortcode::$PRE_ATTR_WRAP => ''), $attr));
            $inner_content = do_shortcode($this->prepare_content($inner_content));

            switch ($type) {
                case "code":
                    $start_tag = "<code>";
                    $end_tag = "</code>";
                    break;
                case "pre-code":
                    $start_tag = "<pre><code>";
                    $end_tag = "</code></pre>";
                    break;
                default :
                    $start_tag = "<pre>";
                    $end_tag = "</pre>";
                    break;
            }
            if (strlen($wrap) > 0) {
                $inner_content = "<$wrap>$inner_content</$wrap>";
            }

            $content = $start_tag.$inner_content.$end_tag;
        }
        return $content;
    }

    function get_names()
    {
        return 'pre';
    }

    function get_visual_editor_form()
    {
        $content = '<form id="sc-pre-form" class="generic-form" method="post" action="#" data-sc="pre">';
        $content .= '<fieldset>';
        $content .= '<div>';
        $content .= '<label for="sc-pre-tag-type">' . __('Tag Type', 'finesse') . ':</label>';
        $content .= '<select id="sc-pre-tag-type" name="sc-pre-tag-type" data-attr-name="'.Preformatted_Shortcode::$PRE_ATTR_TYPE.'" data-attr-type="attr">';
        $content .= '<option value="pre">' . __('Use', 'finesse') . ' &lt;pre&gt; tag</option>';
        $content .= '<option value="code">' . __('Use', 'finesse') . ' &lt;code&gt; tag</option>';
        $content .= '<option value="pre-code">' . __('Use', 'finesse') . ' &lt;pre&gt;&lt;code&gt; tags</option>';
        $content .= '</select>';
        $content .= '</div>';
        $content .= '<div>';
        $content .= '<label for="sc-pre-content">' . __('Content', 'finesse') . ':</label>';
        $content .= '<textarea id="sc-pre-content" name="sc-pre-content" class="required" data-attr-type="content"></textarea>';
        $content .= '</div>';
        $content .= '<div>';
        $content .= '<label for="sc-pre-wct">' . __('Content Wrapper', 'finesse') . ':</label>';
        $content .= '<select id="sc-pre-wct" name="sc-pre-wct" data-attr-name="'.Preformatted_Shortcode::$PRE_ATTR_WRAP.'" data-attr-type="attr">';
        $content .= '<option value="">' . __('None', 'finesse') . '</option>';
        $content .= '<option value="p">' . __('Paragraph', 'finesse') . ' (&lt;p&gt;)</option>';
        $content .= '<option value="div">' . __('Div', 'finesse') . ' (&lt;div&gt;)</option>';
        $content .= '</select>';
        $content .= '</div>';
        $content .= '<div >';
        $content .= '<input id="sc-pre-form-submit" type="submit" name="submit" value="' . __('Insert Preformatted Text', 'finesse') . '" class="button-primary">';
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
        return __('Preformatted Text', 'finesse');
    }
}

