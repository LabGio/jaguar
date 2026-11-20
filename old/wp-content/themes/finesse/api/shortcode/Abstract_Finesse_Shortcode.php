<?php

abstract class Abstract_Finesse_Shortcode
{

    function register_shortcode()
    {
        $names = $this->get_names();
        if (is_array($names)) {
            foreach ($names as $name) {
                $register = apply_filters('fin_register_shortcode', true, $name);
                if ($register) {
                    add_shortcode($name, array($this, 'render_shortcode'));
                }
            }
        } else {
            $register = apply_filters('fin_register_shortcode', true, $names);
            if ($register) {
                add_shortcode($names, array($this, 'render_shortcode'));
            }
        }
    }

    final function render_shortcode($attr, $inner_content = null, $code = "")
    {
        $inner_content = ___($inner_content);
        return $this->render($attr, $inner_content, $code);
    }

    abstract function render($attr, $inner_content = null, $code = "");

    abstract function get_names();

    protected function prepare_content($inner_content)
    {
        $inner_content = shortcode_unautop($inner_content);
        $inner_content = trim($inner_content, "\x00..\x1F");
        return $inner_content;
    }

    protected function get_error($error_msg)
    {
        $sc_names = $this->get_names();
        if (is_array($sc_names)) {
            $sc_names = $sc_names[0];
        }
        return "<p style=\"color:red\">SHORTCODE '.$sc_names.' ERROR: $error_msg</p>";
    }

    protected function get_attribute($attr_name, $attr_value, $attr_default_value = '')
    {
        if ($attr_value === '') {
            $attr_value = $attr_default_value;
        }
        return ($attr_value === '') ? '' : " $attr_name=\"$attr_value\"";
    }

}
