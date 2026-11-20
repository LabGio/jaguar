<?php

class Horizontal_Space_Shortocode extends Abstract_Finesse_Shortcode
{

    function render($attr, $inner_content = null, $code = '')
    {
        extract(shortcode_atts(array('size' => '20'), $attr));
        return '<div class="space' . $size . '"></div>';
    }

    function get_names()
    {
        return array('hs');
    }

}