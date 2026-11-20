<?php

class Email_Util
{
    static function send_email_to_me($from_email, $from_name, $subject, $message)
    {
        Email_Util::validate_email_address($from_email);
        Email_Util::validate_required_field($subject);
        Email_Util::validate_required_field($message);
        $to = get_bloginfo('admin_email');

        $headers = "Content-type: text/html; charset=UTF-8\r\n";
        $headers .= "From: $from_name <$from_email>\r\n";
        $headers .= "Reply-To: $from_email\r\n";
        if (!wp_mail($to, $subject, $message, $headers)) {
            throw new Exception("Error sending the email.");
        }
    }

    static function send_email_to($to, $subject, $message)
    {
        Email_Util::validate_email_address($to);
        Email_Util::validate_required_field($subject);
        Email_Util::validate_required_field($message);
        $from_name = get_bloginfo('name');
        $from_email = get_bloginfo('admin_email');

        $headers = "Content-type: text/html; charset=UTF-8\r\n";
        $headers .= "From: $from_name <$from_email>\r\n";
        $headers .= "Reply-To: $from_email\r\n";
        if (!wp_mail($to, $subject, $message, $headers)) {
            throw new Exception("Error sending the email. Please check the server log for more details.");
        }
    }

    private static function validate_required_field($field_value)
    {
        if (!isset($field_value) || strlen(trim($field_value)) == 0) {
            throw new Exception("The field " . $field_value . " is empty.");
        }
    }

    private static function validate_email_address($email)
    {
        if (!is_email($email)) {
            throw new Exception("Invalid email address");
        }
    }
}
