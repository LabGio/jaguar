<?php

class Toggles_Shortcode extends Abstract_Finesse_Shortcode implements Shortcode_Designer
{
    private static $TOGGLES_ATTR_TYPE = "type";
    private static $TOGGLES_ATTR_TITLE = "title";
    private static $TOGGLES_ATTR_STATE = "state";
    private static $TOGGLES_ATTR_DISABLED = "disabled";
    private static $TOGGLES_ATTR_HSTYLE = "height_style";
    var $toggles = array();

    private function reset()
    {
        unset($this->toggles);
        $this->toggles = array();
    }

    function render($attr, $inner_content = null, $code = '')
    {
        $content = '';
        switch ($code) {
            case "toggles":
                do_shortcode($this->prepare_content($inner_content));
                $content .= $this->render_toggles($attr);
                $this->reset();
                break;
            case "toggle":
                $inner_content = do_shortcode($this->prepare_content($inner_content));
                $this->render_toggle($attr, $inner_content);
                break;
        }
        return $content;
    }

    private function render_toggles($attr)
    {
        extract(shortcode_atts(array(
            Toggles_Shortcode::$TOGGLES_ATTR_TYPE => 'default',
            Toggles_Shortcode::$TOGGLES_ATTR_DISABLED => '',
            Toggles_Shortcode::$TOGGLES_ATTR_HSTYLE => ''), $attr));
        if ($type == 'accordion') {
            $data_disabled_attr = $this->get_attribute('data-disabled', $disabled);
            $data_hstyle_attr = $this->get_attribute('data-height-style', $height_style);
            $active = '';
            foreach ($this->toggles as $index => $v) {
                if (empty($active) && $v['active'] == 'true') {
                    $active = $index + 1;
                }
            }
            $data_active_attr = $this->get_attribute('data-active', $active);
            $content = '<div class="accordion"' . $data_active_attr . $data_disabled_attr . $data_hstyle_attr . '>';
            foreach ($this->toggles as $v) {
                $content .= '<div>';
                $content .= "<span class=\"accordion-title\">" . $v['title'] . "</span>";
                $content .= '<div class="accordion-inner">';
                $content .= $v['value'];
                $content .= '</div></div>';
            }
            $content .= '</div>';
        } else {
            $content = '';
            foreach ($this->toggles as $v) {
                $data_active_attr = $this->get_attribute('data-active', $v['active']);
                $data_disabled_attr = $this->get_attribute('data-disabled', $v['disabled']);
                $data_hstyle_attr = $this->get_attribute('data-height-style', $v['height_style']);
                $content .= '<div class="toggle"' . $data_active_attr . $data_disabled_attr . $data_hstyle_attr . '>';
                $content .= "<span class=\"toggle-title\">" . $v['title'] . "</span>";
                $content .= '<div class="toggle-inner">';
                $content .= $v['value'];
                $content .= '</div></div>';
            }
        }
        return $content;
    }

    private function render_toggle($attr, $inner_content = null)
    {
        extract(shortcode_atts(array(
            Toggles_Shortcode::$TOGGLES_ATTR_TITLE => '',
            Toggles_Shortcode::$TOGGLES_ATTR_DISABLED => '',
            Toggles_Shortcode::$TOGGLES_ATTR_HSTYLE => '',
            Toggles_Shortcode::$TOGGLES_ATTR_STATE => 'closed'), $attr));
        $title = ___($title);
        array_push($this->toggles, array('title' => $title,
            'value' => $inner_content,
            'active' => ($state == 'closed') ? 'false' : 'true',
            'disabled' => $disabled,
            'height_style' => $height_style));
    }

    function get_names()
    {
        return array('toggles', 'toggle');
    }

    function get_visual_editor_form()
    {
        $toogle_example = '[toggles]';
        $toogle_example .= '[toggle title="Title 1" state="opened"]Content 1[/toggle]';
        $toogle_example .= '[toggle title="Title 2"]Content 2[/toggle]';
        $toogle_example .= '[toggle title="Title 3" state="opened"]Content 3[/toggle]';
        $toogle_example .= '[/toggles]';
        $toogle_example = str_replace(array('[', ']'), array('&#91;', '&#93;'), $toogle_example);

        $accordion_example = '[toggles type="accordion"]';
        $accordion_example .= '[toggle title="Title 1" state="opened"]Content 1[/toggle]';
        $accordion_example .= '[toggle title="Title 2"]Content 2[/toggle]';
        $accordion_example .= '[toggle title="Title 3"]Content 3[/toggle]';
        $accordion_example .= '[/toggles]';
        $accordion_example = str_replace(array('[', ']'), array('&#91;', '&#93;'), $accordion_example);

        $content = '<form id="sc-toggle-form" class="generic-form" method="post" action="#" data-sc="toggles">';
        $content .= '<fieldset>';
        $content .= '<div>';
        $content .= '<label for="sc-toggles-type">' . __('Example Type', 'finesse') . ':</label>';
        $content .= '<select id="sc-toggles-type" name="sc-toggles-type" data-default="' . esc_textarea($toogle_example) . '" data-accordion="' . esc_textarea($accordion_example) . '">';
        $content .= '<option value="">' . __('Choose Example ...', 'finesse') . '</option>';
        $content .= '<option value="default">' . __('Default', 'finesse') . '</option>';
        $content .= '<option value="accordion">' . __('Accordion', 'finesse') . '</option>';
        $content .= '</select>';
        $content .= '</div>';
        $content .= '<div>';
        $content .= '<label for="sc-toggles-content">' . __('Content', 'finesse') . ':</label>';
        $content .= '<textarea id="sc-toggles-content" name="sc-toggles-content"></textarea>';
        $content .= '</div>';

        $content .= '<div >';
        $content .= '<input id="sc-toggle-form-submit" type="submit" name="submit" value="' . __('Insert Toggles', 'finesse') . '" class="button-primary">';
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
        return __('Toggles & Accordions', 'finesse');
    }
}