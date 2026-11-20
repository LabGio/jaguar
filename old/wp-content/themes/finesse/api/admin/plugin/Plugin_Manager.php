<?php

class Plugin_Manager
{
    var $plugins = array();

    function register_plugin($plugin)
    {
        array_push($this->plugins, $plugin);
    }

    function load_plugins()
    {
        add_action('admin_menu', array($this, 'register_plugins_left_menu'));
    }

    function register_plugins_left_menu()
    {
        foreach ($this->plugins as $plugin) {
            $plugin_title = $plugin->get_title();
            $plugin_slug = $plugin->get_slug();
            add_management_page($plugin_title, $plugin_title, 'manage_options', $plugin_slug,
                array($plugin, 'render'));
        }
    }

}
