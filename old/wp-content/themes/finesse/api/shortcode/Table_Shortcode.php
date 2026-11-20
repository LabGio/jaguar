<?php

class Table_Shortcode extends Abstract_Finesse_Shortcode implements Shortcode_Designer
{

    static $CAPTION_ATTR = "caption";
    static $FOOTER_ATTR = "footer";
    static $COLUMNS_SEPARATOR_ATTR = "separator";

    var $table_header;
    var $table_rows = array();

    private function reset()
    {
        unset($this->table_header);
        $this->table_rows = array();
    }

    function render($attr, $inner_content = null, $code = "")
    {
        $content = '';
        switch ($code) {
            case "table":
                $inner_content = do_shortcode($this->prepare_content($inner_content));
                $content .= $this->render_table($attr);
                $this->reset();
                break;
            case "table_header":
                $inner_content = do_shortcode($this->prepare_content($inner_content));
                $this->process_table_header($attr, $inner_content);
                break;
            case "table_row":
                $inner_content = do_shortcode($this->prepare_content($inner_content));
                $this->process_table_row($attr, $inner_content);
                break;
        }
        return $content;
    }

    private function render_table($attr)
    {
        extract(shortcode_atts(array(
            Table_Shortcode::$CAPTION_ATTR => '',
            Table_Shortcode::$FOOTER_ATTR => '',
            Table_Shortcode::$COLUMNS_SEPARATOR_ATTR => '|',), $attr));

        $content = '<table class="gen-table">' . "\n";

        //table caption
        if (strlen($caption) > 0) {
            $caption = ___($caption);
            $content .= '<caption>' . $caption . '</caption>' . "\n";
        }

        //table header
        if (isset($this->table_header)) {
            $content .= $this->table_header->render($separator);
        }

        //table footer
        $columns_count = $this->table_rows[0]->get_columns_count($separator);
        if (strlen($footer) > 0) {
            $footer = ___($footer);
            $content .= '<tfoot>';
            $content .= '<tr>';
            $content .= '<td colspan="' . $columns_count . '">' . $footer . '</td>';
            $content .= '</tr>';
            $content .= '</tfoot>';
        }

        //table body
        $content .= '<tbody>';
        $rows_total = count($this->table_rows);
        foreach ($this->table_rows as $row_count => $row) {
            if (($row_count + 1) == $rows_total) {
                $row_class = 'row-last';
            } else {
                $row_class = ($row_count % 2 != 0) ? 'odd' : '';
            }
            $content .= $row->render($separator, $row_class);
        }
        $content .= '</tbody>';

        $content .= '</table>';
        return $content;
    }

    private function process_table_header($attr, $inner_content)
    {
        if (!isset($this->table_header)) {
            $this->table_header = new Finesse_Table_Header($inner_content);
        }
    }

    private function process_table_row($attr, $inner_content)
    {
        array_push($this->table_rows, new Finesse_Table_Row($inner_content));
    }

    function get_names()
    {
        return array('table', 'table_row', 'table_header');
    }

    function get_visual_editor_form()
    {
        $content = '<form id="sc-table-form" class="generic-form" method="post" action="#">';
        $content .= '<fieldset>';
        $content .= '<div>';
        $content .= '<label for="sc-table-caption">' . __('Caption', 'finesse') . ':</label>';
        $content .= '<input id="sc-table-caption" name="sc-table-caption" type="text" data-attr-name="' . Table_Shortcode::$CAPTION_ATTR . '" data-attr-type="attr">';
        $content .= '</div>';
        $content .= '<div>';
        $content .= '<label for="sc-table-footer">' . __('Footer Text', 'finesse') . ':</label>';
        $content .= '<input id="sc-table-footer" name="sc-table-footer" type="text" data-attr-name="' . Table_Shortcode::$FOOTER_ATTR . '" data-attr-type="attr">';
        $content .= '</div>';
        $content .= '<div>';
        $content .= '<label for="sc-table-separator">' . __('Column Separator', 'finesse') . ':</label>';
        $content .= '<input id="sc-table-separator" name="sc-table-separator" type="text" class="required" value="|" data-attr-name="' . Table_Shortcode::$COLUMNS_SEPARATOR_ATTR . '" data-attr-type="attr">';
        $content .= '</div>';
        $content .= '<div>';
        $content .= '<label for="sc-table-columns">' . __('No. of Columns', 'finesse') . ':</label>';
        $content .= '<input id="sc-table-columns" name="sc-table-columns" type="text" class="required number">';
        $content .= '</div>';
        $content .= '<div>';
        $content .= '<label for="sc-table-rows">' . __('No. of Rows', 'finesse') . ':</label>';
        $content .= '<input id="sc-table-rows" name="sc-table-rows" type="text" class="required number">';
        $content .= '</div>';
        $content .= '<div >';
        $content .= '<input id="sc-table-form-submit" type="submit" value="' . __('Insert Table', 'finesse') . '" class="button-primary">';
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
        return __('Table', 'finesse');
    }

}

class Finesse_Table_Header
{

    private $content;

    function __construct($content)
    {
        $this->content = $content;
    }

    function render($separator)
    {
        $content = '<thead>';
        $content .= '<tr>';
        $columns = explode($separator, $this->content);
        foreach ($columns as $column) {
            $content .= '<th>' . $column . '</th>';
        }
        $content .= '</tr>';
        $content .= '</thead>';
        return $content;
    }

}

class Finesse_Table_Row
{
    private $content;

    function __construct($content)
    {
        $this->content = $content;
    }

    function get_columns_count($separator)
    {
        return count(explode($separator, $this->content));
    }

    function render($separator, $row_class)
    {
        $row_class = empty($row_class) ? '' : ' class="' . $row_class . '"';
        $content = '<tr' . $row_class . '>';
        $columns = explode($separator, $this->content);
        foreach ($columns as $column) {
            $content .= '<td>' . $column . '</td>';
        }
        $content .= '</tr>';
        return $content;
    }

}
