<?php

abstract class Page_Customizer
{
    public function __construct()
    {
        $this->register_actions();
    }

    abstract function register_actions();

    abstract function get_page_location();

    abstract function visit(&$setting);

    function render_sidebar_settings()
    {
        $dynamic_sidebars = get_dynamic_sidebars();
        $current_sidebar = get_current_page_sidebar();
        $content = '<p><strong>' . __('Sidebar', 'finesse') . '</strong></p>';
        $content .= '<label for="finesse-sidebar-name-id" class="screen-reader-text">' . __('Sidebar', 'finesse') . '</label>';
        $content .= '<select id="finesse-sidebar-name-id" name="finesse_sidebar_name">';
        $content .= '<option value="">Default Sidebar</option>';
        foreach ($dynamic_sidebars as $sidebar) {
            $sidebar_name = $sidebar['name'];
            $selected = ($current_sidebar == $sidebar_name) ? 'selected' : '';
            $content .= "<option $selected value=\"$sidebar_name\" class=\"level-0\">$sidebar_name</option>";
        }
        $content .= '</select>';
        echo $content;
    }
}
