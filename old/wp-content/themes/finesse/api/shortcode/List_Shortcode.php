<?php

class List_Shortcode extends Abstract_Finesse_Shortcode implements Shortcode_Designer
{

    private static $LI_ATTR_TYPE = "type";
    private static $LI_ATTR_INDENT = "indent";
    private static $LI_ATTR_SEP = "separator";

    function render($attr, $inner_content = null, $code = "")
    {
        $content = '';
        if (isset($inner_content) && strlen($inner_content) > 0) {
            $inner_content = do_shortcode($this->prepare_content($inner_content));
            switch ($code) {
                case "ul":
                    $content .= $this->render_unorder_list($attr, $inner_content);
                    break;
                case "ol":
                    $content .= $this->render_order_list($attr, $inner_content);
                    break;
            }
        }
        return $content;
    }

    private function render_unorder_list($attr, $inner_content)
    {
        extract(shortcode_atts(array(
            List_Shortcode::$LI_ATTR_TYPE => 'circle',
            List_Shortcode::$LI_ATTR_INDENT => '',
            List_Shortcode::$LI_ATTR_SEP => '|'), $attr));
        switch ($type) {
            case "arrow":
                $class = "arrow";
                break;
            case "square":
                $class = "square";
                break;
            case "check":
                $class = "check";
                break;
            default :
                $class = "circle";
                break;
        }
        if($indent == 'true'){
            $class .= ' indent';
        }
        $lines = explode($separator, $inner_content);
        $content = "<ul class=\"$class\">\n";
        foreach ($lines as $line) {
            if(strlen(trim($line)) > 0){
                $content .= "<li>$line</li>";
            }
        }
        $content .= "</ul>";
        return $content;
    }

    private function render_order_list($attr, $inner_content)
    {
        extract(shortcode_atts(array(
            List_Shortcode::$LI_ATTR_TYPE => 'decimal',
            List_Shortcode::$LI_ATTR_SEP => '|'), $attr));
        switch ($type) {
            case "upper-roman":
                $class = "upper-roman";
                break;
            case "lower-alpha":
                $class = "lower-alpha";
                break;
            case "upper-alpha":
                $class = "upper-alpha";
                break;
            default :
                $class = "decimal";
                break;
        }
        $lines = explode($separator, $inner_content);
        $content = "<ol class=\"$class\">\n";
        foreach ($lines as $line) {
            if(strlen(trim($line)) > 0){
                $content .= "<li>$line</li>\n";
            }
        }
        $content .= "</ol>";
        return $content;
    }

    function get_names()
    {
        return array('ul', 'ol');
    }

    function get_visual_editor_form()
    {
        $content = '<form id="sc-list-form" class="generic-form" method="post" action="#">';
        $content .= '<fieldset>';
        $content .= '<div>';
        $content .= '<label for="sc-li-content">' . __('Content (one item per line)', 'finesse') . ':</label>';
        $content .= '<textarea id="sc-li-content" name="sc-li-content" class="required"></textarea>';
        $content .= '</div>';
        $content .= '<div>';
        $content .= '<input id="sc-li-indent" name="sc-li-indent" type="checkbox">';
        $content .= '<label for="sc-li-indent">' . __('Indent the list', 'finesse') . '</label>';
        $content .= '</div>';
        $content .= '<div class="radio-row">';
        $content .= '<span>';
        $content .= '<input id="sc-li-ol-type" type="radio" name="sc-li-type" value="ol" checked class="sc-li-type-selector">';
        $content .= '<label for="sc-li-ol-type">' . __('Ordered List', 'finesse') . '</label>';
        $content .= '</span>';
        $content .= '<span>';
        $content .= '<input id="sc-li-ul-type" type="radio" name="sc-li-type" value="ul" class="sc-li-type-selector">';
        $content .= '<label for="sc-li-ul-type">' . __('Unordered List', 'finesse') . '</label>';
        $content .= '</span>';
        $content .= '</div>';
        $content .= '<div>';
        $content .= '<label for="sc-li-ol-icon">' . __('Icon', 'finesse') . ':</label>';
        $content .= '<select id="sc-li-ol-icon">';
        $content .= '<option value="decimal" selected>' . __('Decimal', 'finesse') . '</option>';
        $content .= '<option value="upper-alpha">' . __('Upper Latin', 'finesse') . '</option>';
        $content .= '<option value="lower-alpha">' . __('Lower Latin', 'finesse') . '</option>';
        $content .= '<option value="upper-roman">' . __('Upper Roman', 'finesse') . '</option>';
        $content .= '</select>';
        $content .= '<select id="sc-li-ul-icon" name="sc-li-ul-icon" style="display: none">';
        $content .= '<option value="circle" selected>' . __('Circle', 'finesse') . '</option>';
        $content .= '<option value="arrow">' . __('Arrow', 'finesse') . '</option>';
        $content .= '<option value="square">' . __('Square', 'finesse') . '</option>';
        $content .= '<option value="check">' . __('Check', 'finesse') . '</option>';
        $content .= '</select>';
        $content .= '</div>';
        $content .= '<div >';
        $content .= '<input id="sc-list-form-submit" type="submit" value="' . __('Insert List', 'finesse') . '" class="button-primary">';
        $content .= '<input id="sc-list-form-add" type="submit" value="' . __('Add Item', 'finesse') . '" class="button-secondary">';
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
        return __('List', 'finesse');
    }
}
