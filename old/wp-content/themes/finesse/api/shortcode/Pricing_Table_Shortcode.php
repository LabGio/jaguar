<?php

class Pricing_Table_Shortcode extends Abstract_Finesse_Shortcode implements Shortcode_Designer
{

    static $TABLE_ATTR_HEADER_PRICE = "price";
    static $TABLE_ATTR_HEADER_UNIT = "unit";
    static $TABLE_ATTR_FOOTER_ORDER_TEXT = "order_text";
    static $TABLE_ATTR_FOOTER_ORDER_HREF = "order_url";
    static $TABLE_ATTR_ROW_TITLE = "title";
    static $TABLE_ATTR_COL_SEPARATOR = "separator";
    static $TABLE_ATTR_HIGHLIGHTED_COLUMNS = "highlighted_column";

    var $table_header_columns = array();
    var $table_footer_columns = array();
    var $table_rows = array();

    private function reset()
    {
        unset($this->table_header_columns);
        $this->table_header_columns = array();
        unset($this->table_footer_columns);
        $this->table_footer_columns = array();
        unset($this->table_rows);
        $this->table_rows = array();
    }

    function render($attr, $inner_content = null, $code = "")
    {
        $content = '';
        switch ($code) {
            case "pricing_table":
                do_shortcode($this->prepare_content($inner_content));
                $content .= $this->render_table($attr);
                $this->reset();
                break;
            case "pricing_table_header":
                $inner_content = do_shortcode($this->prepare_content($inner_content));
                $this->process_table_header($attr, $inner_content);
                break;
            case "pricing_table_row":
                $inner_content = do_shortcode($this->prepare_content($inner_content));
                $this->process_table_row($attr, $inner_content);
                break;
            case "pricing_table_footer":
                $inner_content = do_shortcode($this->prepare_content($inner_content));
                $this->process_table_footer($attr, $inner_content);
                break;
        }
        return $content;
    }

    private function render_table($attr)
    {
        extract(shortcode_atts(array(
            Pricing_Table_Shortcode::$TABLE_ATTR_COL_SEPARATOR => '|',
            Pricing_Table_Shortcode::$TABLE_ATTR_HIGHLIGHTED_COLUMNS => ''), $attr));

        $content = '<table class="gen-table pricing-table responsive">' . "\n";

        //table header
        $content .= '<thead>' . "\n";
        $content .= '<tr>' . "\n";
        $content .= '<th class="empty-left-top">&nbsp;</th>' . "\n";
        foreach ($this->table_header_columns as $i => $footer) {
            $is_featured = (($i + 1) == intval($highlighted_column)) ? true : false;
            $content .= $footer->render($is_featured);
        }
        $content .= '</tr>' . "\n";
        $content .= '</thead>' . "\n";

        //table footer
        $content .= '<tfoot>' . "\n";
        $content .= '<tr>' . "\n";
        $content .= '<td class="empty-left-bottom">&nbsp;</td>' . "\n";
        foreach ($this->table_footer_columns as $i => $footer) {
            $is_featured = (($i + 1) == intval($highlighted_column)) ? true : false;
            $content .= $footer->render($is_featured);
        }
        $content .= '</tr>' . "\n";
        $content .= '</tfoot>' . "\n";

        //table body
        $content .= '<tbody>';
        foreach ($this->table_rows as $i => $row) {
            $is_last = (($i + 1) == count($this->table_rows));

            if ($i % 2 != 0) {
                $class_name = $is_last ? 'odd row-last' : 'odd';
            } else {
                $class_name = $is_last ? 'row-last' : '';
            }

            $content .= $row->render($separator, $class_name);
        }
        $content .= '</tbody>';

        $content .= '</table>';
        return $content;
    }

    private function process_table_header($attr, $inner_content)
    {
        array_push($this->table_header_columns, new Finesse_Pricing_Table_Header($attr, $inner_content));
    }

    private function process_table_footer($attr, $inner_content)
    {
        array_push($this->table_footer_columns, new Finesse_Pricing_Table_Footer($attr, $inner_content));
    }

    private function process_table_row($attr, $inner_content)
    {
        array_push($this->table_rows, new Finesse_Pricing_Table_Row($attr, $inner_content));
    }

    function get_names()
    {
        return array('pricing_table', 'pricing_table_row', 'pricing_table_header', 'pricing_table_footer');
    }

    function get_visual_editor_form()
    {
        $content = '<form id="sc-pt-form" class="generic-form" method="post" action="#">';
        $content .= '<fieldset>';
        $content .= '<div>';
        $content .= '<label for="sc-pt-separator">' . __('Column Separator', 'finesse') . ':</label>';
        $content .= '<input id="sc-pt-separator" name="sc-pt-separator" type="text" class="required" value="|">';
        $content .= '</div>';
        $content .= '<div>';
        $content .= '<label for="sc-pt-columns">' . __('No. of Columns', 'finesse') . ':</label>';
        $content .= '<input id="sc-pt-columns" name="sc-pt-columns" type="text" class="required number">';
        $content .= '</div>';
        $content .= '<div>';
        $content .= '<label for="sc-pt-rows">' . __('No. of Rows', 'finesse') . ':</label>';
        $content .= '<input id="sc-pt-rows" name="sc-pt-rows" type="text" class="required number">';
        $content .= '</div>';
        $content .= '<div>';
        $content .= '<label for="sc-pt-hc">' . __('Highlighted Column', 'finesse') . ':</label>';
        $content .= '<input id="sc-pt-hc" name="sc-pt-hc" type="text" class="required number">';
        $content .= '</div>';
        $content .= '<div >';
        $content .= '<input id="sc-pt-form-submit" type="submit" value="' . __('Insert Pricing Table', 'finesse') . '" class="button-primary">';
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
        return __('Pricing Table', 'finesse');
    }

}

class Finesse_Pricing_Table_Header
{

    private $attr;
    private $content;

    function __construct($attr, $content)
    {
        $this->attr = $attr;
        $this->content = $content;
    }

    function render($is_featured)
    {
        extract(shortcode_atts(array(
            Pricing_Table_Shortcode::$TABLE_ATTR_HEADER_PRICE => '',
            Pricing_Table_Shortcode::$TABLE_ATTR_HEADER_UNIT => ''), $this->attr));

        $th_class = $is_featured ? ' class="featured"' : '';
        $content = '<th' . $th_class . '>' . "\n";
        $content .= '<span class="title">' . $this->content . "</span>\n";
        $content .= '<span class="price">' . "\n";
        $content .= '<span>' . $price . '</span>' . "\n";
        $content .= '<sup>/' . $unit . '</sup>' . "\n";
        $content .= '</span>' . "\n";
        $content .= '</th>' . "\n";
        return $content;
    }

}

class Finesse_Pricing_Table_Footer
{

    private $attr;
    private $content;

    function __construct($attr, $content)
    {
        $this->attr = $attr;
        $this->content = $content;
    }

    function render($is_featured)
    {
        extract(shortcode_atts(array(
            Pricing_Table_Shortcode::$TABLE_ATTR_FOOTER_ORDER_TEXT => '',
            Pricing_Table_Shortcode::$TABLE_ATTR_FOOTER_ORDER_HREF => ''), $this->attr));

        $content = '<td>' . "\n";
        $button_color = $is_featured ? '' : ' color="black"';
        $content .= do_shortcode('[button href="' . $order_url . '"' . $button_color . ']' . $order_text . '[/button]') . "\n";
        $content .= '</td>' . "\n";

        return $content;
    }

}

class Finesse_Pricing_Table_Row
{
    private $attr;
    private $content;

    function __construct($attr, $content)
    {
        $this->attr = $attr;
        $this->content = $content;
    }

    function render($separator, $row_class)
    {
        extract(shortcode_atts(array(
            Pricing_Table_Shortcode::$TABLE_ATTR_ROW_TITLE => ''), $this->attr));

        $class_name = strlen($row_class) > 0 ? ' class="' . $row_class . '"' : '';
        $content = '<tr' . $class_name . '>';
        $content .= '<th>' . $title . '</th>';
        $columns = explode($separator, $this->content);
        foreach ($columns as $column) {
            $content .= '<td>' . $column . '</td>';
        }
        $content .= '</tr>';
        return $content;
    }

}
