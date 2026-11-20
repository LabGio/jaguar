<?php

if (is_user_logged_in()) {
    add_action('wp_ajax_unsubscribe', 'finesse_process_newsletter_unsubscribe_request');
} else {
    add_action('wp_ajax_nopriv_unsubscribe', 'finesse_process_newsletter_unsubscribe_request');
}

//the subscription function is implemented in finesse-forms.php

function finesse_process_newsletter_unsubscribe_request()
{
    global $newsletter_manager;
    $GLOBALS['unsubscription_success'] = $newsletter_manager->unsubcribe($_REQUEST['uuid']);
    include FINESSE_FUNCTIONS_FILE_PATH . '/api/newsletter/unsubscribe-message.php';
    die();
}
