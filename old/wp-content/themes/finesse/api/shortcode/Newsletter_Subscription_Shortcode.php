<?php

class Newsletter_Subscription_Shortcode extends Abstract_Finesse_Shortcode implements Shortcode_Designer
{

    private static $NLS_ATTR_SUCCESS_MSG = "success_msg";
    private static $NLS_ATTR_ERROR_MSG = "error_msg";
    private static $NLS_ATTR_MC_LIST_ID = "mc_list_id";

    function render($attr, $inner_content = null, $code = "")
    {
        extract(shortcode_atts(array(
            Newsletter_Subscription_Shortcode::$NLS_ATTR_SUCCESS_MSG => __('You have successfully subscribed to our newsletter.', 'finesse'),
            Newsletter_Subscription_Shortcode::$NLS_ATTR_ERROR_MSG => __('Your email address couldn\'t be subscribed (it is possible that it has already been registered).', 'finesse'),
            Newsletter_Subscription_Shortcode::$NLS_ATTR_MC_LIST_ID => '',
        ), $attr));

        $success_msg = ___($success_msg);
        $error_msg = ___($error_msg);

        $form_action = site_url('wp-admin/admin-ajax.php');
        $content = '<div id="newsletter-wrap">' . "\n";
        $content .= '<p>' . $inner_content . '</p>';
        $content .= do_shortcode('[notif id="newsletter-success-msg" type="success" display="false"]' . $success_msg . '[/notif]');
        $content .= do_shortcode('[notif id="newsletter-error-msg" type="error" display="false"]' . $error_msg . '[/notif]');
        $content .= '<form id="newsletter-form" class="content-form clearfix" action="' . $form_action . '" method="post">' . "\n";
        if(empty($mc_list_id)){
            $content .= '<input type="hidden" name="ua" value="process_newsletter_subscription">' . "\n";
        }else{
            $content .= '<input type="hidden" name="ua" value="process_mc_newsletter_subscription">' . "\n";
            $content .= '<input type="hidden" name="list-id" value="'.$mc_list_id.'">' . "\n";
        }
        $content .= '<input id="subscribe" class="button" type="submit" name="subscribe" value="' . __('Subscribe', 'finesse') . '">' . "\n";
        $content .= '<input id="newsletter" type="email" name="email" placeholder="' . __('Enter your email address here', 'finesse') . ' &hellip;" class="required">' . "\n";
        $content .= '</form>' . "\n";
        $content .= '<p class="tip"><span class="note">&#42;</span> ' . __('Check your spam folder if the mail does not arrive', 'finesse') . '.</p>' . "\n";
        $content .= '</div>';
        $content .= "<script type=\"text/javascript\">
            if(!document['formsSettings']){
                document['formsSettings'] = [];
            }
            document['formsSettings'].push({
                submitButtonId: 'subscribe',
                action: 'finesse_process_form',
                successBoxId: 'newsletter-success-msg',
                errorBoxId: 'newsletter-error-msg'
            });
		</script>";
        return $content;
    }

    function get_names()
    {
        return array('nls');
    }

    function get_visual_editor_form()
    {
        $content = '<form id="sc-nls-form" class="generic-form" method="post" action="#" data-sc="nls">';
        $content .= '<fieldset>';
        $content .= '<div>';
        $content .= '<label for="sc-nls-content">' . __('Intro', 'finesse') . ':</label>';
        $content .= '<textarea id="sc-nls-content" name="sc-nls-content" class="required" data-attr-type="content"></textarea>';
        $content .= '</div>';
        $content .= '<div>';
        $content .= '<label for="sc-nls-sm-id">' . __('Success Message', 'finesse') . ':</label>';
        $content .= '<input id="sc-nls-sm-id" name="sc-nls-sm-id" type="text" data-attr-name="' . Newsletter_Subscription_Shortcode::$NLS_ATTR_SUCCESS_MSG . '" data-attr-type="attr">';
        $content .= '</div>';
        $content .= '<div>';
        $content .= '<label for="sc-nls-em-id">' . __('Error Message', 'finesse') . ':</label>';
        $content .= '<input id="sc-nls-em-id" name="sc-nls-em-id" type="text" data-attr-name="' . Newsletter_Subscription_Shortcode::$NLS_ATTR_ERROR_MSG . '" data-attr-type="attr">';
        $content .= '</div>';
        $content .= '<div>';
        $content .= '<label for="sc-nls-list-id">' . __('MailChimp List ID (only if you want to use MailChimp)', 'finesse') . ':</label>';
        $content .= '<input id="sc-nls-list-id" name="sc-nls-list-id" type="text" data-attr-name="' . Newsletter_Subscription_Shortcode::$NLS_ATTR_MC_LIST_ID . '" data-attr-type="attr">';
        $content .= '</div>';
        $content .= '<div >';
        $content .= '<input id="sc-nls-form-submit" type="submit" name="submit" value="' . __('Insert Notification Box', 'finesse') . '" class="button-primary">';
        $content .= '</div>';
        $content .= '</fieldset>';
        $content .= '</form>';

        return $content;
    }

    function get_group_title()
    {
        return __('Others', 'finesse');
    }

    function get_title()
    {
        return __('Newsletter Subscription', 'finesse');
    }
}