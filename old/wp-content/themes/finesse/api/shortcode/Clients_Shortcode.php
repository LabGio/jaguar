<?php

class Clients_Shortcode extends Abstract_Finesse_Shortcode implements Shortcode_Designer
{
    static $CLIENT_ATTR_LOGO = "logo";
    static $CLIENT_ATTR_URL = "url";
    static $CLIENT_ATTR_TARGET = "target";
    var $clients = array();

    private function reset()
    {
        unset($this->clients);
        $this->clients = array();
    }

    function render($attr, $inner_content = null, $code = '')
    {
        $content = '';
        switch ($code) {
            case "clients":
                do_shortcode($this->prepare_content($inner_content));
                $content .= $this->render_clients();
                $this->reset();
                break;
            case "client":
                $inner_content = do_shortcode($this->prepare_content($inner_content));
                $this->process_client($attr, $inner_content);
                break;
        }
        return $content;
    }

    private function render_clients()
    {
        $content = '<ul class="clients clearfix">' . "\n";
        foreach ($this->clients as $client) {
            $content .= $client->render();
        }
        $content .= '</ul>' . "\n";
        return $content;
    }

    private function process_client($attr, $inner_content)
    {
        array_push($this->clients, new Company_Client($attr, $inner_content));
    }

    function get_names()
    {
        return array('clients', 'client');
    }

    function get_visual_editor_form()
    {
        $content = '<form id="sc-clients-form" class="generic-form" method="post" action="#" data-sc="clients">';
        $content .= '<fieldset>';
        $content .= '<div>';
        $content .= '<label for="sc-clients-content">' . __('Content', 'finesse') . ':</label>';
        $content .= '<textarea id="sc-clients-content" name="sc-clients-content" class="required" data-attr-type="content"></textarea>';
        $content .= '</div>';
        $content .= '<div >';
        $content .= '<input id="sc-clients-form-submit" type="submit" name="submit" value="' . __('Insert Clients', 'finesse') . '" class="button-primary">';
        $content .= '<input id="sc-clients-form-add" type="submit" name="submit" value="' . __('Add Team Client', 'finesse') . '" class="button-secondary">';
        $content .= '</div>';
        $content .= '</fieldset>';
        $content .= '</form>';

        $content .= '<div id="sc-add-client-dialog" title="' . __('New Client', 'finesse') . '" style="display: none">';
        $content .= '<form id="sc-add-client-form" class="generic-form" method="post" action="#" data-sc="client">';
        $content .= '<fieldset>';
        $content .= '<div>';
        $content .= '<label for="sc-add-client-logo">' . __('Logo', 'finesse') . ':</label>';
        $content .= '<input id="sc-add-client-logo" name="sc-add-client-photo-logo" type="text" data-attr-name="' . Clients_Shortcode::$CLIENT_ATTR_LOGO . '" data-attr-type="attr">';
        $content .= '</div>';
        $content .= '<div>';
        $content .= '<label for="sc-add-client-url">' . __('URL', 'finesse') . ':</label>';
        $content .= '<input id="sc-add-client-url" name="sc-add-client-url" type="text" class="required" data-attr-name="' . Clients_Shortcode::$CLIENT_ATTR_URL . '" data-attr-type="attr">';
        $content .= '</div>';
        $content .= '<div>';
        $content .= '<label for="sc-add-client-name">' . __('Name', 'finesse') . ':</label>';
        $content .= '<input id="sc-add-client-name" name="sc-add-client-name" type="text" data-attr-type="content" class="required">';
        $content .= '</div>';
        $content .= '<div >';
        $content .= '<input id="sc-add-client-form-submit" type="submit" value="' . __('Add Client', 'finesse') . '" class="button-primary">';
        $content .= '<input id="sc-add-client-form-cancel" type="submit" value="' . __('Cancel', 'finesse') . '" class="button-secondary">';
        $content .= '</div>';
        $content .= '</fieldset>';
        $content .= '</form>';
        $content .= '</div>';
        return $content;
    }

    function get_group_title()
    {
        return __('Others', 'finesse');
    }

    function get_title()
    {
        return __('Clients', 'finesse');
    }
}

class Company_Client
{

    private $inner_content;
    private $attr;

    function __construct($attr, $inner_content)
    {
        $this->attr = $attr;
        $this->inner_content = $inner_content;
    }

    function render()
    {
        extract(shortcode_atts(array(
            Clients_Shortcode::$CLIENT_ATTR_LOGO => '',
            Clients_Shortcode::$CLIENT_ATTR_URL => '',
            Clients_Shortcode::$CLIENT_ATTR_TARGET => '',
        ), $this->attr));

        $logo_src = Multimedia_Util::get_image_src($logo);

        $target = empty($target) ? '' : ' target="' . $target . '"';
        return '<li><a'.$target.' href="' . $url . '"><img src="' . $logo_src . '" alt="' . $this->inner_content . '" title="' . $this->inner_content . '"></a></li>' . "\n";
    }

}
