<?php

class Form_Shortcode extends Abstract_Finesse_Shortcode implements Shortcode_Designer
{

    private static $FORM_ATTR_ID = "id";
    private static $FORM_ATTR_ACTION = "action";
    private static $FORM_ATTR_CAPTCHA = "captcha";
    private static $FORM_ATTR_SUCCESS = "success";
    private static $FORM_ATTR_ERROR = "error";
    private static $FORM_INPUT_ATTR_ID = 'id';
    private static $FORM_INPUT_ATTR_NAME = 'name';
    private static $FORM_INPUT_ATTR_TYPE = 'type';
    private static $FORM_INPUT_ATTR_REQUIRED = 'required';
    private static $FORM_INPUT_ATTR_LABEL = 'label';
    private static $FORM_INPUT_ATTR_VALUE = 'value';
    private static $FORM_INPUT_ATTR_CLASSES = 'classes';

    private $successBoxId;
    private $errorBoxId;
    private $submitButtonId;


    function __construct()
    {
        $this->reset();
    }

    private function reset()
    {
        $this->successBoxId = uniqid();
        $this->errorBoxId = uniqid();
        $this->submitButtonId = uniqid();
    }

    function render($attr, $inner_content = null, $code = "")
    {
        $content = '';
        switch ($code) {
            case "form":
                $inner_content = do_shortcode($this->prepare_content($inner_content));
                $content .= $this->render_form($attr, $inner_content);
                $this->reset();
                break;
            case "input":
                $inner_content = do_shortcode($this->prepare_content($inner_content));
                $content .= $this->render_form_input($attr, $inner_content);
                break;
        }
        return $content;
    }

    private function render_form($attr, $inner_content)
    {
        extract(shortcode_atts(array(
            Form_Shortcode::$FORM_ATTR_ID => '',
            Form_Shortcode::$FORM_ATTR_ACTION => '#',
            Form_Shortcode::$FORM_ATTR_CAPTCHA => 'false',
            Form_Shortcode::$FORM_ATTR_SUCCESS => '',
            Form_Shortcode::$FORM_ATTR_ERROR => ''), $attr));

        $success = strlen($success) > 0 ? ___($success) : __('The form has been successfully submitted', 'finesse');
        $error = strlen($error) > 0 ? ___($error) : __('The form couldn\'t be submitted because a server error occurred. Please try again later.', 'finesse');
        $id = strlen($id) > 0 ? ' id="' . $id . '"' : '';
        $form_action = site_url('wp-admin/admin-ajax.php');

        $content = do_shortcode('[notif id="' . $this->successBoxId . '" type="success" display="false"]' . $success . '[/notif]');
        $content .= do_shortcode('[notif id="' . $this->errorBoxId . '" type="error" display="false"]' . $error . '[/notif]');
        $content .= "<form" . $id . " class=\"content-form\" method=\"post\" action=\"$form_action\">";
        $content .= "<input name=\"ua\" type=\"hidden\" value=\"$action\">";
        $content .= $inner_content;
        $content .= "</form>";

        $content .= "<script type=\"text/javascript\">
            if(!document['formsSettings']){
                document['formsSettings'] = [];
            }
            document['formsSettings'].push({
                submitButtonId: '" . $this->submitButtonId . "',
                action: 'finesse_process_form',
                successBoxId: '" . $this->successBoxId . "',
                errorBoxId: '" . $this->errorBoxId . "'
            });
		</script>";
        $content .= "<p><span class=\"asterisk note\">*</span> " . __('Required fields', 'finesse') . "</p>";
        return $content;
    }

    private function render_form_input($attr, $inner_content = null)
    {
        $content = '';
        extract(shortcode_atts(array(
            Form_Shortcode::$FORM_INPUT_ATTR_ID => uniqid(),
            Form_Shortcode::$FORM_INPUT_ATTR_NAME => '',
            Form_Shortcode::$FORM_INPUT_ATTR_TYPE => 'text',
            Form_Shortcode::$FORM_INPUT_ATTR_REQUIRED => 'false',
            Form_Shortcode::$FORM_INPUT_ATTR_LABEL => '',
            Form_Shortcode::$FORM_INPUT_ATTR_VALUE => '',
            Form_Shortcode::$FORM_INPUT_ATTR_CLASSES => ''
        ), $attr));
        $label = ___($label);
        $required_span = '';
        if ($required == 'true') {
            $required_span = "<span class=\"asterisk note\">*</span>";
            if (empty($classes)) {
                $classes = "required";
            } else {
                $classes = $classes . " required";
            }
        }

        if ($type == 'submit') {
            if (is_captcha_form_enabled()) {
                $content .= "<p>";
                $content .= do_shortcode('[captcha][/captcha]');
                $content .= "</p>";
            }
            $content .= "<p>";
            $content .= "<button id=\"$id\" class=\"button\" type=\"submit\" name=\"submit\" value=\"$label\"><span class=\"inner\"><span class=\"text\">$label</span></span></button>";
            $this->submitButtonId = $id;
            $content .= "</p>";
        } else {
            $content .= "<p>";
            if ($type == 'hidden') {
                $content .= "<input id=\"$id\" type=\"$type\" name=\"$name\" value=\"$value\">";
            } elseif ($type == 'textarea') {
                $content .= "<label for=\"$id\">" . $label . ":$required_span</label>";
                $content .= "<textarea id=\"$id\" cols=\"68\" rows=\"8\" name=\"$name\" class=\"$classes\">$value</textarea>";
            } elseif ($type == 'custom') {
                $content .= "<label for=\"$id\">" . $label . ":$required_span</label>";
                $content .= $inner_content;
            } elseif ($type == 'checkbox') {
                $checked = ($value == 'on' || $value == 'checked') ? 'checked' : '';
                $content .= "<input id=\"$id\" name=\"$name\" type=\"checkbox\" class=\"$classes\" $checked>";
                $content .= " <label for=\"$id\">" . $label . "$required_span</label>";
            } elseif ($type == 'number') {
                $content .= "<label for=\"$id\">" . $label . ":$required_span</label>";
                $content .= "<input id=\"$id\" type=\"text\" name=\"$name\" value=\"$value\" class=\"number $classes\">";
            } elseif ($type == 'datepicker') {
                $content .= "<label for=\"$id\">" . $label . ":$required_span</label>";
                $content .= "<input id=\"$id\" type=\"text\" name=\"$name\" value=\"$value\" class=\"datepicker $classes\">";
            } else {
                $content .= "<label for=\"$id\">" . $label . ":$required_span</label>";
                $content .= "<input id=\"$id\" type=\"$type\" name=\"$name\" value=\"$value\" class=\"$classes\">";
            }
            $content .= "</p>";
        }
        return $content;
    }

    function get_names()
    {
        return array('form', 'input');
    }

    function get_visual_editor_form()
    {
        $content = '<form id="sc-form-form" class="generic-form" method="post" action="#" data-sc="form">';
        $content .= '<fieldset>';
        $content .= '<div>';
        $content .= '<label for="sc-form-id">' . __('ID', 'finesse') . ':</label>';
        $content .= '<input id="sc-form-id" name="sc-form-action" type="text" data-attr-name="' . Form_Shortcode::$FORM_ATTR_ID . '" data-attr-type="attr">';
        $content .= '</div>';
        $content .= '<div>';
        $content .= '<label for="sc-form-action">' . __('Action', 'finesse') . ':</label>';
        $content .= '<input id="sc-form-action" name="sc-form-action" type="text" data-attr-name="' . Form_Shortcode::$FORM_ATTR_ACTION . '" data-attr-type="attr" class="required">';
        $content .= '</div>';
        $content .= '<div>';
        $content .= '<label for="sc-form-captcha">' . __('Add Captcha', 'finesse') . ':</label>';
        $content .= '<select id="sc-form-captcha" name="sc-form-captcha" data-attr-name="' . Form_Shortcode::$FORM_ATTR_CAPTCHA . '" data-attr-type="attr" class="required">';
        $content .= '<option value="false">' . __('No', 'finesse') . '</option>';
        $content .= '<option value="true">' . __('Yes', 'finesse') . '</option>';
        $content .= '</select>';
        $content .= '</div>';
        $content .= '<div>';
        $content .= '<label for="sc-form-success">' . __('Success Message', 'finesse') . ':</label>';
        $content .= '<input id="sc-form-success" name="sc-form-success" type="text" data-attr-name="' . Form_Shortcode::$FORM_ATTR_SUCCESS . '" data-attr-type="attr">';
        $content .= '</div>';
        $content .= '<div>';
        $content .= '<label for="sc-form-error">' . __('Error Message', 'finesse') . ':</label>';
        $content .= '<input id="sc-form-error" name="sc-form-error" type="text" data-attr-name="' . Form_Shortcode::$FORM_ATTR_ERROR . '" data-attr-type="attr">';
        $content .= '</div>';
        $content .= '<div>';
        $content .= '<label for="sc-form-content">' . __('Content', 'finesse') . ':</label>';
        $content .= '<textarea id="sc-form-content" name="sc-form-content" class="required" data-attr-type="content"></textarea>';
        $content .= '</div>';
        $content .= '<div >';
        $content .= '<input id="sc-form-form-submit" type="submit" name="submit" value="' . __('Insert Form', 'finesse') . '" class="button-primary">';
        $content .= '<input id="sc-form-form-add" type="submit" name="submit" value="' . __('Add Input Field', 'finesse') . '" class="button-secondary">';
        $content .= '</div>';
        $content .= '</fieldset>';
        $content .= '</form>';

        $content .= '<div id="sc-form-input-dialog" title="' . __('New Input Field', 'finesse') . '" style="display: none">';
        $content .= '<form id="sc-form-input-form" class="generic-form" method="post" action="#" data-sc="input">';
        $content .= '<fieldset>';
        $content .= '<div>';
        $content .= '<label for="sc-form-input-id">' . __('ID', 'finesse') . ':</label>';
        $content .= '<input id="sc-form-input-id" name="sc-form-input-id" data-attr-name="' . Form_Shortcode::$FORM_INPUT_ATTR_ID . '" data-attr-type="attr" type="text">';
        $content .= '</div>';
        $content .= '<div>';
        $content .= '<label for="sc-form-input-label">' . __('Label', 'finesse') . ':</label>';
        $content .= '<input id="sc-form-input-label" name="sc-form-input-label" data-attr-name="' . Form_Shortcode::$FORM_INPUT_ATTR_LABEL . '" data-attr-type="attr" type="text" class="required">';
        $content .= '</div>';
        $content .= '<div>';
        $content .= '<label for="sc-form-input-name">' . __('Name', 'finesse') . ':</label>';
        $content .= '<input id="sc-form-input-name" name="sc-form-input-name" data-attr-name="' . Form_Shortcode::$FORM_INPUT_ATTR_NAME . '" data-attr-type="attr" type="text" class="required">';
        $content .= '</div>';
        $content .= '<div>';
        $content .= '<label for="sc-form-input-value">' . __('Default Value', 'finesse') . ':</label>';
        $content .= '<input id="sc-form-input-value" name="sc-form-input-value" data-attr-name="' . Form_Shortcode::$FORM_INPUT_ATTR_VALUE . '" data-attr-type="attr" type="text">';
        $content .= '</div>';
        $content .= '<div>';
        $content .= '<label for="sc-form-input-type">' . __('Type', 'finesse') . ':</label>';
        $content .= '<select id="sc-form-input-type" data-attr-name="' . Form_Shortcode::$FORM_INPUT_ATTR_TYPE . '" data-attr-type="attr">';
        $content .= '<option value="text">' . __('Text Field', 'finesse') . '</option>';
        $content .= '<option value="number">' . __('Number Field', 'finesse') . '</option>';
        $content .= '<option value="datepicker">' . __('Date Picker Field', 'finesse') . '</option>';
        $content .= '<option value="textarea">' . __('Text Area', 'finesse') . '</option>';
        $content .= '<option value="hidden">' . __('Hidden Field', 'finesse') . '</option>';
        $content .= '<option value="checkbox">' . __('Checkbox Field', 'finesse') . '</option>';
        $content .= '<option value="submit">' . __('Submit Button', 'finesse') . '</option>';
        $content .= '</select>';
        $content .= '</div>';
        $content .= '<div>';
        $content .= '<input id="sc-form-input-required" name="sc-form-input-required" type="checkbox" data-attr-name="' . Form_Shortcode::$FORM_INPUT_ATTR_REQUIRED . '" data-attr-type="attr">';
        $content .= '<label for="sc-form-input-required">' . __('Is required', 'finesse') . '</label>';
        $content .= '</div>';
        $content .= '<div >';
        $content .= '<input id="sc-form-input-form-submit" type="submit" name="submit" value="' . __('Add Input Field', 'finesse') . '" class="button-primary">';
        $content .= '<input id="sc-form-input-form-cancel" type="submit" name="submit" value="' . __('Cancel', 'finesse') . '" class="button-secondary">';
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
        return __('Form', 'finesse');
    }
}