<?php

abstract class Finesse_Plugin
{

    public function __construct()
    {
        if (is_user_logged_in()) {
            global $pagenow;
            if (isset($_GET['activated']) && $pagenow == 'themes.php') {
                $this->activate();
            }
        }
        $this->register_actions();
    }

    abstract function register_actions();

    protected abstract function activate();

    abstract function render();

    abstract function get_title();

    abstract function get_slug();
}
