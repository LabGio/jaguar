<?php

class Pricing_Boxes_Shortcodes extends Abstract_Finesse_Shortcode implements Shortcode_Designer
{

    static $PB_ATTR_COLUMNS = "columns";
    static $PB_ATTR_HIGHLIGHTED_COLUMNS = "highlighted_column";
    static $PB_ATTR_TITLE = "title";
    static $PB_ATTR_PRICE = "price";
    static $PB_ATTR_UNIT = "unit";
    static $PB_ATTR_ORDER_URL = "order_url";
    static $PB_ATTR_ORDER_TEXT = "order_text";
    static $PB_ROW_SEPARATOR = "separator";

    var $boxes = array();

    private function reset()
    {
        unset($this->boxes);
        $this->boxes = array();
    }

    function render($attr, $inner_content = null, $code = "")
    {
        $content = '';
        switch ($code) {
            case "pricing_boxes":
                do_shortcode($this->prepare_content($inner_content));
                $content .= $this->render_table($attr);
                $this->reset();
                break;
            case "pricing_box_column":
                $inner_content = do_shortcode($this->prepare_content($inner_content));
                $this->process_column($attr, $inner_content);
                break;
        }
        return $content;
    }

    private function render_table($attr)
    {
        extract(shortcode_atts(array(
            Pricing_Boxes_Shortcodes::$PB_ATTR_COLUMNS => '4',
            Pricing_Boxes_Shortcodes::$PB_ATTR_HIGHLIGHTED_COLUMNS => '',
            Pricing_Boxes_Shortcodes::$PB_ROW_SEPARATOR => '|',), $attr));

        if ($columns == '2') {
            $base_layout_class = 'one-half';
        } elseif ($columns == '3') {
            $base_layout_class = 'one-third';
        } else {
            $base_layout_class = 'one-fourth';
        }

        $content = '';
        foreach ($this->boxes as $i => $box) {
            $layout_class = $base_layout_class;
            $is_highlighted = (isset($highlighted_column) && !empty($highlighted_column) && (($i + 1) == intval($highlighted_column)));
            $is_last = ($i + 1) == intval($columns);
            if ($is_highlighted) {
                $layout_class .= ' featured';
            }
            if ($is_last) {
                $layout_class .= ' column-last';
            }
            $content .= $box->render($separator, $layout_class, $is_highlighted);
        }
        return $content;
    }

    private function process_column($attr, $inner_content)
    {
        array_push($this->boxes, new Finesse_Pricing_Table_Box($attr, $inner_content));
    }

    function get_names()
    {
        return array('pricing_boxes', 'pricing_box_column');
    }

    function get_visual_editor_form()
    {
        $content = '<form id="sc-pb-form" class="generic-form" method="post" action="#">';
        $content .= '<fieldset>';
        $content .= '<div>';
        $content .= '<label for="sc-pb-columns">' . __('No. of Columns', 'finesse') . ':</label>';
        $content .= '<select id="sc-pb-columns" name="sc-pb-columns">';
        $content .= '<option value="2">' . __('Two', 'finesse') . '</option>';
        $content .= '<option value="3">' . __('Three', 'finesse') . '</option>';
        $content .= '<option value="4">' . __('Four', 'finesse') . '</option>';
        $content .= '</select>';
        $content .= '</div>';
        $content .= '<div>';
        $content .= '<label for="sc-pb-rows">' . __('No. of Rows', 'finesse') . ':</label>';
        $content .= '<input id="sc-pb-rows" name="sc-pb-rows" type="text" class="required number">';
        $content .= '</div>';
        $content .= '<div>';
        $content .= '<label for="sc-pb-hc">' . __('Highlighted Column', 'finesse') . ':</label>';
        $content .= '<input id="sc-pb-hc" name="sc-pb-hc" type="text" class="number">';
        $content .= '</div>';
        $content .= '<div>';
        $content .= '<label for="sc-pb-separator">' . __('Rows Separator', 'finesse') . ':</label>';
        $content .= '<input id="sc-pb-separator" name="sc-pb-separator" type="text" class="required" value="|">';
        $content .= '</div>';
        $content .= '<div >';
        $content .= '<input id="sc-pb-form-submit" type="submit" value="' . __('Insert Pricing Boxes', 'finesse') . '" class="button-primary">';
        $content .= '</div>';
        $content .= '</fieldset>';
        $content .= '</form>';

        return $content;
    }

    function get_group_title()
    {
        return __('Pricing Tables', 'finesse');
    }

    function get_title()
    {
        return __('Pricing Boxes', 'finesse');
    }

}

class Finesse_Pricing_Table_Box
{
    private $attr;
    private $content;

    function __construct($attr, $content)
    {
        $this->attr = $attr;
        $this->content = $content;
    }

    function render($separator, $layout_class, $is_highlighted)
    {
        extract(shortcode_atts(array(
            Pricing_Boxes_Shortcodes::$PB_ATTR_TITLE => '',
            Pricing_Boxes_Shortcodes::$PB_ATTR_PRICE => '',
            Pricing_Boxes_Shortcodes::$PB_ATTR_UNIT => '',
            Pricing_Boxes_Shortcodes::$PB_ATTR_ORDER_TEXT => '',
            Pricing_Boxes_Shortcodes::$PB_ATTR_ORDER_URL => ''), $this->attr));

        $title = ___($title);
        $unit = ___($unit);

        $content = '<div class="pricing-box ' . $layout_class . '">';
        $content .= '<header class="header">' . "\n";
        $content .= '<h2 class="title">' . $title . '</h2>' . "\n";
        $content .= '<h3 class="price">' . "\n";
        $content .= '<span>' . $price . '</span>' . "\n";
        $content .= '<sup>/' . $unit . '</sup>' . "\n";
        $content .= '</h3>' . "\n";
        $content .= '</header>' . "\n";

        $content .= '<ul class="features">' . "\n";
        $columns = explode($separator, $this->content);
        foreach ($columns as $column) {
            $content .= '<li>' . $column . '</li>' . "\n";
        }
        $content .= '</ul>' . "\n";

        if (strlen($order_url) > 0 && strlen($order_text) > 0) {
            $content .= '<footer class="footer">' . "\n";
            $button_color = $is_highlighted ? '' : ' color="black"';
            $content .= do_shortcode('[button href="' . $order_url . '"' . $button_color . ']' . $order_text . '[/button]') . "\n";
            $content .= '</footer>' . "\n";
        }
        $content .= '</div>' . "\n";
        return $content;
    }

}