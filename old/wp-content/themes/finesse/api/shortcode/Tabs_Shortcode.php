<?php

class Tabs_Shortcode extends Abstract_Finesse_Shortcode implements Shortcode_Designer
{
    private static $TAB_ATTR_TITLE = "title";
    private static $TAB_ATTR_ACTIVE = "active";
    private static $TAB_ATTR_DISABLED = "disabled";
    private static $TAB_ATTR_HSTYLE = "height_style";
    var $tabs = array();

    private function reset()
    {
        unset($this->tabs);
        $this->tabs = array();
    }

    function render($attr, $inner_content = null, $code = '')
    {
        $content = '';
        switch ($code) {
            case "tabs":
                $inner_content = do_shortcode($this->prepare_content($inner_content));
                $content .= $this->render_tabs($attr, $inner_content);
                $this->reset();
                break;
            case "tab":
                $inner_content = do_shortcode($this->prepare_content($inner_content));
                $content .= $this->render_tab($attr, $inner_content);
                break;
        }
        return $content;
    }

    private function render_tabs($attr, $inner_content = null)
    {
        extract(shortcode_atts(array(
            Tabs_Shortcode::$TAB_ATTR_ACTIVE => '',
            Tabs_Shortcode::$TAB_ATTR_DISABLED => '',
            Tabs_Shortcode::$TAB_ATTR_HSTYLE => ''), $attr));

        $data_active_attr = $this->get_attribute('data-active', $active);
        $data_disabled_attr = $this->get_attribute('data-disabled', $disabled);
        $data_hstyle_attr = $this->get_attribute('data-height-style', $height_style);

        $content = '<div class="tabs"' . $data_active_attr . $data_disabled_attr . $data_hstyle_attr . '>';
        $content .= '<ul class="nav clearfix">';
        foreach ($this->tabs as $k => $v) {
            $content .= $k;
        }
        $content .= '</ul>';
        foreach ($this->tabs as $k => $v) {
            $content .= $v;
        }
        $content .= '</div>';
        return $content;
    }

    private function render_tab($attr, $inner_content = null)
    {
        extract(shortcode_atts(array(Tabs_Shortcode::$TAB_ATTR_TITLE => ''), $attr));
        $title = ___($title);
        $i = count($this->tabs) + 1;
        $key = "<li><a href=\"#tab-$i\">$title</a></li>";
        $value = "<div id=\"tab-$i\" class=\"tab\">$inner_content</div>";
        $this->tabs[$key] = $value;
        return '';
    }

    function get_names()
    {
        return array('tabs', 'tab');
    }

    function get_visual_editor_form()
    {
        $ex1 = '[tabs active="2"]';
        $ex1 .= '[tab title="Tab 1"]Content 1[/tab]';
        $ex1 .= '[tab title="Tab 2"]Content 2[/tab]';
        $ex1 .= '[tab title="Tab 3"]Content 3[/tab]';
        $ex1 .= '[tab title="Tab 4"]Content 4[/tab]';
        $ex1 .= '[/tabs]';
        $ex1 = str_replace(array('[', ']'), array('&#91;', '&#93;'), $ex1);

        $ex2 = '[tabs active="1" disabled="3,4"]';
        $ex2 .= '[tab title="Tab 1"]Content 1[/tab]';
        $ex2 .= '[tab title="Tab 2"]Content 2[/tab]';
        $ex2 .= '[tab title="Tab 3"]Content 3[/tab]';
        $ex2 .= '[tab title="Tab 4"]Content 4[/tab]';
        $ex2 .= '[/tabs]';
        $ex2 = str_replace(array('[', ']'), array('&#91;', '&#93;'), $ex2);

        $content = '<form id="sc-tabs-form" class="generic-form" method="post" action="#">';
        $content .= '<fieldset>';
        $content .= '<div>';
        $content .= '<label for="sc-tabs-ex">' . __('Example Type', 'finesse') . ':</label>';
        $content .= '<select id="sc-tabs-ex" name="sc-tabs-ex" data-ex1="'.esc_textarea($ex1).'" data-ex2="'.esc_textarea($ex2).'">';
        $content .= '<option value="">' . __('Choose Example ...', 'finesse') . '</option>';
        $content .= '<option value="ex1">' . __('Default Example', 'finesse') . '</option>';
        $content .= '<option value="ex2">' . __('With Disabled Tabs', 'finesse') . '</option>';
        $content .= '</select>';
        $content .= '</div>';
        $content .= '<div>';
        $content .= '<label for="sc-tabs-content">' . __('Content', 'finesse') . ':</label>';
        $content .= '<textarea id="sc-tabs-content" name="sc-tabs-content" class="required"></textarea>';
        $content .= '</div>';
        $content .= '<div >';
        $content .= '<input id="sc-tabs-form-submit" type="submit" name="submit" value="' . __('Insert Tabs', 'finesse') . '" class="button-primary">';
        $content .= '</div>';
        $content .= '</fieldset>';
        $content .= '</form>';
        return $content;
    }

    function get_group_title()
    {
        return __('Dynamic Elements', 'finesse');
    }

    function get_title()
    {
        return __('Tabs', 'finesse');
    }
}

?>