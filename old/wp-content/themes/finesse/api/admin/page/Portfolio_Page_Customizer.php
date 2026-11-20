<?php

class Portfolio_Page_Customizer extends Page_Customizer
{
    public function __construct()
    {
        parent::__construct();
    }

    function register_actions()
    {
    }

    function get_page_location()
    {
        return 'portfolio';
    }

    function visit(&$setting)
    {
        $page = $this->get_page_location();
        if ($setting['id'] == 'project_details') {
            array_push($setting['location'], $page);
            $setting[$page . '_slider_type'] = 'cycleSlider';
        }
    }

}
