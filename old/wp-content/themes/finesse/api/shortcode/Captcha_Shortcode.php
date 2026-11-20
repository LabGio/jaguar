<?php

class Captcha_Shortcode extends Abstract_Finesse_Shortcode
{

    function render($attr, $inner_content = null, $code = '')
    {
        $captcha_public_key = get_captcha_public_key();
        $use_ssl = is_ssl();
        return recaptcha_get_html($captcha_public_key, null, $use_ssl);
    }

    function get_names()
    {
        return array('captcha');
    }

}
