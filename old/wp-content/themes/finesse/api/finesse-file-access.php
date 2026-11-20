<?php

include_once ABSPATH . 'wp-admin/includes/file.php';

$ftp_on = get_option('finesse_file_access_ftp_enabled');
if ($ftp_on && $ftp_on == 'on') {
    $hostname = get_option('finesse_file_access_ftp_hostname');
    $theme_path = get_option('finesse_file_access_ftp_theme_path');
    $username = get_option('finesse_file_access_ftp_username');
    $password = get_option('finesse_file_access_ftp_password');

    $ftp_credentials = array(
        'hostname' => $hostname,
        'username' => $username,
        'password' => $password,
    );
    define('FINESSE_THEME_PATH', $theme_path);
    define('FS_METHOD', 'ftpext');
    WP_Filesystem($ftp_credentials);
} else {
    define('FINESSE_THEME_PATH', FINESSE_FUNCTIONS_FILE_PATH);
    WP_Filesystem();
}