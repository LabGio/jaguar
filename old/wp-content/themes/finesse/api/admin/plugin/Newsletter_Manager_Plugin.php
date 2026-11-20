<?php

class Newsletter_Manager_Plugin extends Finesse_Plugin
{

    public function __construct()
    {
        parent::__construct();
    }

    function register_actions()
    {
        add_action('wp_ajax_newsletter-manager-send', array($this, 'send_newsletter'));
        add_action('wp_ajax_newsletter-manager-remove-email', array($this, 'remove_email'));
    }

    private function get_table_name()
    {
        global $wpdb;
        return $table_name = $wpdb->base_prefix . "finesse_nl_subscription";
    }

    protected function activate()
    {
        global $wpdb;
        $table_name = $this->get_table_name();
        $wpdb->query("CREATE TABLE if not exists $table_name (
          id int(6) NOT NULL PRIMARY KEY AUTO_INCREMENT,
          uuid varchar(128) NOT NULL,
          email varchar(128) NOT NULL,
          subscription_date datetime NOT NULL,
          UNIQUE (uuid),
          UNIQUE (email)
        ) DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;");
    }

    function render()
    {
        echo '<div class="wrap">';
        echo '<div class="icon32" id="icon-themes"></div>';
        echo '<h2>Finesse - ' . __('Newsletter Manager', 'finesse') . '</h2>';
        if (isset($_REQUEST['newsletter-sent'])) {
            if ($_REQUEST['newsletter-sent'] == 'true') {
                echo '<div id="message" class="updated below-h2"><p><strong>' . __('The newsletter has been sent', 'finesse') . '.</strong></p></div>';
            } elseif ($_REQUEST['newsletter-sent'] == 'false') {
                $error_array = $_REQUEST['newsletter-errors'];
                $error_array = unserialize(base64_decode($error_array));
                echo '<div id="message" class="updated settings-error"><p><strong>' . __('Failing to send the newsletter to the following addresses', 'finesse') . ':</strong></p>';
                echo '<ul>';
                foreach ($error_array as $error) {
                    echo '<li>' . $error . '</li>';
                }
                echo '</ul>';
                echo '</div>';
            }
        }
        echo '<div>';
        echo '<form id="newsletter-send-form" method="post" enctype="multipart/form-data" action="admin-ajax.php">';
        echo wp_referer_field(false);
        echo $this->get_form();
        echo '</br>';
        echo '<p>' . __('Select the email addresses to which you wish to send letter', 'finesse') . '.</p>';
        echo '<div id="newsletter-emails-list">';
        echo $this->get_list();
        echo '</div>';

        echo '<p class="submit"><input type="submit" value="' . __('Send Newsletter', 'finesse') . '" class="button-primary" id="newsletter-manager-form-submit"></p>';
        echo '</form>';
        echo '</div>';
        echo '</div>';
    }

    private function get_form()
    {
        $content = '<table class="form-table"><tbody>';
        $content .= '<tr class="form-field form-required">';
        $content .= '<th scope="row"><label for="newsletter-subject">' . __('Newsletter Subject', 'finesse') . ' <span class="description">(required)</span></label></th>';
        $content .= '<td><input id="newsletter-subject" type="text" aria-required="true" value="" name="newsletter-subject" style="width: 25em"></td>';
        $content .= '</tr>';

        $content .= '<tr class="form-field form-required">';
        $content .= '<th scope="row"><label for="newsletter-template">' . __('Newsletter Template', 'finesse') . ' <span class="description">(required)</span></label></th>';
        $content .= '<td><input id="newsletter-template" type="file" aria-required="true" value="" name="newsletter-template"></td>';
        $content .= '</tr>';

        $content .= '</tbody></table>';

        $content .= '<input id="newsletter-action" type="hidden" name="action" value="newsletter-manager-send" >';
        $content .= '<input id="newsletter-nonce" type="hidden" name="nonce" value="' . wp_create_nonce('newsletter-manager-send') . '" />';
        return $content;
    }

    private function get_list($error_msg = null)
    {
        $content = '';
        if (isset($error_msg)) {
            $content .= '<div class="error"><p>' . $error_msg . '</p></div>';
        }
        $content .= '<table cellspacing="0" class="wp-list-table widefat fixed media">';
        $content .= '<thead>';
        $content .= '<tr>';
        $content .= '<th style="" class="manage-column column-cb check-column" id="cb" scope="col"><input type="checkbox"></th>';
        $content .= '<th style="" class="manage-column column-response" scope="col"><span>' . __('ID', 'finesse') . '</span></a></th>';
        $content .= '<th style="" class="manage-column column-title" scope="col"><span>' . __('UUID', 'finesse') . '</span></a></th>';
        $content .= '<th style="" class="manage-column column-title" scope="col"><span>' . __('Email', 'finesse') . '</span></a></th>';
        $content .= '<th style="" class="manage-column column-response" scope="col"><span>' . __('Subscription Date', 'finesse') . '</span></a></th>';
        $content .= '</tr>';
        $content .= '</thead>';

        $content .= '<tfoot>';
        $content .= '<tr>';
        $content .= '<th style="" class="manage-column column-cb check-column" scope="col"><input type="checkbox"></th>';
        $content .= '<th style="" class="manage-column column-response" scope="col"><span>' . __('ID', 'finesse') . '</span></a></th>';
        $content .= '<th style="" class="manage-column column-title" scope="col"><span>' . __('UUID', 'finesse') . '</span></a></th>';
        $content .= '<th style="" class="manage-column column-title" scope="col"><span>' . __('Email', 'finesse') . '</span></a></th>';
        $content .= '<th style="" class="manage-column column-response" scope="col"><span>' . __('Subscription Date', 'finesse') . '</span></a></th>';
        $content .= '</tr>';
        $content .= '</tfoot>';
        $content .= '<tbody id="the-list">';

        $subscribers = $this->get_all_subscribers();
        foreach ($subscribers as $i => $subscribe) {
            $alternate_class = ($i % 2 == 0) ? 'alternate' : '';
            $content .= "<tr valign=\"top\" class=\"$alternate_class author-self status-inherit\">";
            $content .= '<th class="check-column" scope="row"><input type="checkbox" value="' . $subscribe->email . '" name="emails[]"></th>';
            $content .= '<td class="title column-title"><strong>' . $subscribe->id . '</strong>';
            $content .= '<div class="row-actions"><span class="delete"><a href="' . $subscribe->uuid . '" class="newsletter-manager-remove-email">' . __('Delete', 'finesse') . '</a></span></div>';
            $content .= '</td>';
            $content .= '<td class="title column-title"><strong>' . $subscribe->uuid . '</strong></td>';
            $content .= '<td class="title column-title"><strong>' . $subscribe->email . '</strong></td>';
            $content .= '<td class="title column-title"><strong>' . $subscribe->subscription_date . '</strong></td>';
        }

        $content .= '</tbody>';
        $content .= '</table>';
        return $content;
    }

    function get_title()
    {
        return __('Newsletter Manager', 'finesse');
    }

    function get_slug()
    {
        return 'finesse-newsletter-manager';
    }

    function send_newsletter()
    {
        global $wp_filesystem;
        $errors_array = array();

        $referer = $_POST['_wp_http_referer'];
        $redirect_location = 'http://' . $_SERVER['HTTP_HOST'] . $referer;

        $newsletter_subject = $_POST['newsletter-subject'];
        $to_list = $_POST['emails'];

        $overrides = array('test_form' => false, 'test_type' => false);
        $file = wp_handle_upload($_FILES['newsletter-template'], $overrides);

        if($file){
            $template_response = wp_remote_get($file['url']);
            $template_content = $template_response['body'];
            $template_content = str_replace('</body>', '', $template_content);
            $template_content = str_replace('</BODY>', '', $template_content);
            $template_content = str_replace('</html>', '', $template_content);
            $template_content = str_replace('</HTML>', '', $template_content);


            $base_unsubscribe_url = site_url('wp-admin/admin-ajax.php');
            $subscribers = $this->get_all_subscribers_grouped_by_email();
            foreach ($subscribers as $email => $subscribe) {
                if (in_array($email, $to_list)) {
                    $unsubscribe_url = $base_unsubscribe_url . '?action=unsubscribe&amp;uuid=' . $subscribe->uuid;
                    $message = $template_content . '<p>To stop receiving emails from ' . site_url() . ', unsubscribe <a href="' . $unsubscribe_url . '">here</a>.</p>';
                    $message .= '</body></html>';
                    try {
                        Email_Util::send_email_to($email, $newsletter_subject, $message);
                    } catch (Exception $e) {
                        array_push($errors_array, $email);
                    }
                }
            }
            if (count($errors_array) > 0) {
                $newsletter_errors = base64_encode(serialize($errors_array));
                $redirect_location .= '&newsletter-sent=false&newsletter-errors=' . $newsletter_errors;
            } else {
                $redirect_location .= '&newsletter-sent=true';
            }
        }else{
            $redirect_location .= '&newsletter-sent=false';
        }
        header('Location: ' . $redirect_location);
    }

    function remove_email()
    {
        try {
            $uuid = isset($_POST['uuid']) ? $_POST['uuid'] : '';
            if (empty($uuid)) {
                throw new Exception('The UUID is not valid');
            }
            global $wpdb;
            $table_name = $this->get_table_name();
            $rows_affected = $wpdb->query($wpdb->prepare("DELETE FROM $table_name WHERE uuid = '%s'", $uuid));
            if ($rows_affected <= 0) {
                throw new Exception('The UUID: \'' . $uuid . '\' could not be found in the database.');
            }
            echo $this->get_list();
        } catch (Exception $e) {
            echo $this->get_list($e->getMessage());
        }
        die();
    }

    private function get_all_subscribers()
    {
        global $wpdb;
        $table_name = $this->get_table_name();
        return $wpdb->get_results("SELECT * FROM $table_name");
    }

    private function get_all_subscribers_grouped_by_email()
    {
        $subscribers_grouped_by_email = array();
        $subscribers = $this->get_all_subscribers();
        foreach ($subscribers as $subscribe) {
            $subscribers_grouped_by_email[$subscribe->email] = $subscribe;
        }
        return $subscribers_grouped_by_email;
    }

    private function is_email_subscribed($email)    {
        global $wpdb;
        $table_name = $this->get_table_name();
        $count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE email = '%s'", $email));
        return intval($count) > 0;
    }

    function subscribe($email)
    {
        if (is_email($email)) {
            if($this->is_email_subscribed($email)){
                throw new Exception('This email address is already subscribed.');
            }
            global $wpdb;
            $table_name = $this->get_table_name();
            $rows_affected = $wpdb->insert($table_name, array(
                'uuid' => uniqid(),
                'email' => $email,
                'subscription_date' => current_time('mysql')));
            if ($rows_affected <= 0) {
                throw new Exception('Internal Server Error');
            }
        } else {
            throw new Exception('Invalid email address.');
        }
    }

    function unsubcribe($uuid)
    {
        global $wpdb;
        $table_name = $this->get_table_name();
        $rows_affected = $wpdb->query($wpdb->prepare("DELETE FROM $table_name WHERE uuid = '%s'", $uuid));
        return $rows_affected > 0;
    }

}
