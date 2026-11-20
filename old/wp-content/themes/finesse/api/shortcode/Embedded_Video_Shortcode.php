<?php

class Embedded_Video_Shortcode extends Abstract_Finesse_Shortcode implements Shortcode_Designer
{
    function render($attr, $inner_content = '', $code = '')
    {
        $inner_content = $this->prepare_content($inner_content);
        $content = "<div class=\"entry-video\">\n
                		$inner_content\n
                    </div>";
        return $content;
    }

    function get_names()
    {
        return 'evideo';
    }

    function get_visual_editor_form()
    {
        $content = '<form id="sc-evideo-form" class="generic-form" method="post" action="#" data-sc="evideo">';
        $content .= '<fieldset>';
        $content .= '<div>';
        $content .= '<label for="sc-evideo-content">' . __('Embedded Code', 'finesse') . ':</label>';
        $content .= '<textarea id="sc-evideo-content" name="sc-evideo-content" class="required" data-attr-type="content"></textarea>';
        $content .= '</div>';
        $content .= '<div >';
        $content .= '<input id="sc-evideo-form-submit" type="submit" name="submit" value="' . __('Insert Embedded Video', 'finesse') . '" class="button-primary">';
        $content .= '</div>';
        $content .= '</fieldset>';
        $content .= '</form>';

        return $content;
    }

    function get_group_title()
    {
        return __('Multimedia', 'finesse');
    }

    function get_title()
    {
        return __('Embedded Video', 'finesse');
    }
}