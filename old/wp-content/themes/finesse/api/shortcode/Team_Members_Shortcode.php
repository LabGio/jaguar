<?php

class Team_Members_Shortcode extends Abstract_Finesse_Shortcode implements Shortcode_Designer
{
    static $TM_ATTR_COLUMNS = "columns";
    static $TM_ATTR_PHOTO = "photo";
    static $TM_ATTR_NAME = "name";
    static $TM_ATTR_FUNCTION = "function";
    static $TM_ATTR_TWITTER = "twitter";
    static $TM_ATTR_FACEBOOK = "facebook";
    static $TM_ATTR_LINKEDIN = "linkedin";
    static $TM_ATTR_GPLUS = "googleplus";
    static $TM_ATTR_SKYPE = "skype";
    static $TM_ATTR_EMAIL = "email";
    static $TM_ATTR_ECHO = "echo";

    var $team_members = array();

    private function reset()
    {
        unset($this->team_members);
        $this->team_members = array();
    }

    function render($attr, $inner_content = null, $code = '')
    {
        $content = '';
        switch ($code) {
            case "team":
                do_shortcode($this->prepare_content($inner_content));
                $content .= $this->render_team($attr, $inner_content);
                $this->reset();
                break;
            case "member":
                $inner_content = do_shortcode($this->prepare_content($inner_content));
                $content .= $this->process_team_member($attr, $inner_content);
                break;
        }
        return $content;
    }

    private function render_team($attr, $inner_content)
    {
        extract(shortcode_atts(array(Team_Members_Shortcode::$TM_ATTR_COLUMNS => '4'), $attr));
        $no_of_columns = (isset($columns) && is_numeric($columns)) ? intval($columns) : 4;
        if ($no_of_columns < 2 || $no_of_columns > 4) {
            $no_of_columns = 4;
        }
        if ($no_of_columns == 2) {
            $class = 'one-half';
        } elseif ($no_of_columns == 3) {
            $class = 'one-third';
        } else {
            $class = 'one-fourth';
        }
        $content = '';
        foreach ($this->team_members as $i => $tm) {
            $classes = 'team-member ' . $class;
            $separator = '';
            if ((($i + 1) % $no_of_columns) == 0) {
                $classes .= ' column-last';
                $separator = '<div class="clear"></div>' . "\n";
            }
            $content .= '<div class="' . $classes . '">' . "\n";
            $content .= $tm->render();
            $content .= '</div>' . "\n";
            $content .= $separator;
        }
        return $content;
    }

    private function process_team_member($attr, $inner_content)
    {
        $content = '';
        extract(shortcode_atts(array(Team_Members_Shortcode::$TM_ATTR_ECHO => 'false'), $attr));
        $tm = new Theme_Member($attr, $inner_content);
        if ($echo == 'false') {
            array_push($this->team_members, $tm);
        } else {
            $content .= '<div class="team-member">' . "\n";
            $content .= $tm->render();
            $content .= '</div>' . "\n";
        }

        return $content;
    }

    function get_names()
    {
        return array('team', 'member');
    }

    function get_visual_editor_form()
    {
        $content = '<form id="sc-team-form" class="generic-form" method="post" action="#" data-sc="team">';
        $content .= '<fieldset>';
        $content .= '<div>';
        $content .= '<label for="sc-team-columns">' . __('No of Columns', 'finesse') . ':</label>';
        $content .= '<select id="sc-team-columns" name="sc-team-columns" data-attr-name="' . Team_Members_Shortcode::$TM_ATTR_COLUMNS . '" data-attr-type="attr">';
        $content .= '<option value="4">4</option>';
        $content .= '<option value="3">3</option>';
        $content .= '<option value="2">2</option>';
        $content .= '</select>';
        $content .= '</div>';
        $content .= '<div>';
        $content .= '<label for="sc-team-content">' . __('Content', 'finesse') . ':</label>';
        $content .= '<textarea id="sc-team-content" name="sc-team-content" class="required" data-attr-type="content"></textarea>';
        $content .= '</div>';
        $content .= '<div >';
        $content .= '<input id="sc-team-form-submit" type="submit" name="submit" value="' . __('Insert Team Members', 'finesse') . '" class="button-primary">';
        $content .= '<input id="sc-team-form-add" type="submit" name="submit" value="' . __('Add Team Member', 'finesse') . '" class="button-secondary">';
        $content .= '</div>';
        $content .= '</fieldset>';
        $content .= '</form>';

        $content .= '<div id="sc-add-member-dialog" title="' . __('New Team Member', 'finesse') . '" style="display: none">';
        $content .= '<form id="sc-add-member-form" class="generic-form" method="post" action="#" data-sc="member">';
        $content .= '<fieldset>';
        $content .= '<div class="image-tab-content">';

        $content .= '<div class="image-tab-content-left">';
        $content .= '<div class="radio-row">';
        $content .= '<span>';
        $content .= '<input id="sc-add-member-photo-uploaded-type-source" type="radio" name="sc-add-member-photo-type-source" value="uploaded" checked>';
        $content .= '<label for="sc-add-member-photo-uploaded-type-source">' . __('Uploaded Image', 'finesse') . '</label>';
        $content .= '</span>';
        $content .= '<span>';
        $content .= '<input id="sc-add-member-photo-new-type-source" type="radio" name="sc-add-member-photo-type-source" value="new">';
        $content .= '<label for="sc-add-member-photo-new-type-source">' . __('New Image', 'finesse') . '</label>';
        $content .= '</span>';
        $content .= '</div>';
        $content .= '<div>';
        $content .= '<label for="sc-add-member-photo-uploaded-img-src">' . __('Photo', 'finesse') . ':</label>';
        $content .= '<select id="sc-add-member-photo-uploaded-img-src" name="sc-add-member-photo-uploaded-img-src" class="image-selector" data-base-id="sc-add-member-photo-" data-attr-name="' . Team_Members_Shortcode::$TM_ATTR_PHOTO . '" data-attr-type="attr">';
        $content .= '<option value="" data-src="' . Multimedia_Util::get_external_image_src('/admin/images/team-member-preview.png') . '">' . __('No Photo', 'finesse') . '</option>';
        $images = Multimedia_Util::get_all_uploaded_images();
        foreach ($images as $img) {
            $images = wp_get_attachment_image_src($img->ID);
            $content .= '<option value="' . $img->post_title . '" data-src="' . $images[0] . '">' . $img->post_title . '</option>';
        }
        $content .= '</select>';
        $content .= '</div>';
        $content .= '<div style="display: none">';
        $content .= '<label for="sc-add-member-photo-new-img-src">' . __('Photo', 'finesse') . ':</label>';
        $content .= '<input id="sc-add-member-photo-new-img-src" name="sc-add-member-photo-new-img-src" type="text" data-attr-name="' . Team_Members_Shortcode::$TM_ATTR_PHOTO . '" data-attr-type="attr">';
        $content .= '</div>';
        $content .= '<div>';
        $content .= '<label for="sc-add-member-name">' . __('Name', 'finesse') . ':</label>';
        $content .= '<input id="sc-add-member-name" name="sc-add-member-title" type="text" class="required" data-attr-name="' . Team_Members_Shortcode::$TM_ATTR_NAME . '" data-attr-type="attr">';
        $content .= '</div>';
        $content .= '<div>';
        $content .= '<label for="sc-add-member-function">' . __('Job Title', 'finesse') . ':</label>';
        $content .= '<input id="sc-add-member-function" name="sc-add-member-function" type="text" class="required" data-attr-name="' . Team_Members_Shortcode::$TM_ATTR_FUNCTION . '" data-attr-type="attr">';
        $content .= '</div>';
        $content .= '<div>';
        $content .= '<label for="sc-add-member-content">' . __('Small Description', 'finesse') . ':</label>';
        $content .= '<textarea id="sc-add-member-content" name="sc-add-member-content" data-attr-type="content" class="required"></textarea>';
        $content .= '</div>';
        $content .= '<div>';
        $content .= '<label for="sc-add-member-twitter">' . __('Twitter URL', 'finesse') . ':</label>';
        $content .= '<input id="sc-add-member-twitter" name="sc-add-member-twitter" type="text" data-attr-name="' . Team_Members_Shortcode::$TM_ATTR_TWITTER . '" data-attr-type="attr">';
        $content .= '</div>';
        $content .= '<div>';
        $content .= '<label for="sc-add-member-facebook">' . __('Facebook URL', 'finesse') . ':</label>';
        $content .= '<input id="sc-add-member-facebook" name="sc-add-member-facebook" type="text" data-attr-name="' . Team_Members_Shortcode::$TM_ATTR_FACEBOOK . '" data-attr-type="attr">';
        $content .= '</div>';
        $content .= '<div>';
        $content .= '<label for="sc-add-member-linkedin">' . __('Linkedin URL', 'finesse') . ':</label>';
        $content .= '<input id="sc-add-member-linkedin" name="sc-add-member-linkedin" type="text" data-attr-name="' . Team_Members_Shortcode::$TM_ATTR_LINKEDIN . '" data-attr-type="attr">';
        $content .= '</div>';
        $content .= '<div>';
        $content .= '<label for="sc-add-member-googleplus">' . __('Google + URL', 'finesse') . ':</label>';
        $content .= '<input id="sc-add-member-googleplus" name="sc-add-member-googleplus" type="text" data-attr-name="' . Team_Members_Shortcode::$TM_ATTR_GPLUS . '" data-attr-type="attr">';
        $content .= '</div>';
        $content .= '<div>';
        $content .= '<label for="sc-add-member-skype">' . __('Skype', 'finesse') . ':</label>';
        $content .= '<input id="sc-add-member-skype" name="sc-add-member-skype" type="text" data-attr-name="' . Team_Members_Shortcode::$TM_ATTR_SKYPE . '" data-attr-type="attr">';
        $content .= '</div>';
        $content .= '<div>';
        $content .= '<label for="sc-add-member-email">' . __('Email', 'finesse') . ':</label>';
        $content .= '<input id="sc-add-member-email" name="sc-add-member-email" type="text" data-attr-name="' . Team_Members_Shortcode::$TM_ATTR_EMAIL . '" data-attr-type="attr">';
        $content .= '</div>';
        $content .= '<div >';
        $content .= '<input id="sc-add-member-form-submit" type="submit" value="' . __('Add Team Member', 'finesse') . '" class="button-primary">';
        $content .= '<input id="sc-add-member-form-cancel" type="submit" value="' . __('Cancel', 'finesse') . '" class="button-secondary">';
        $content .= '</div>';
        $content .= '</div>';

        $content .= '<div class="image-tab-content-right">';
        $content .= '<img id="sc-add-member-photo-preview" src="#" alt="">';
        $content .= '</div>';

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
        return __('Team Members', 'finesse');
    }
}

class Theme_Member
{

    private $inner_content;
    private $attr;

    function __construct($attr, $inner_content)
    {
        $this->attr = $attr;
        $this->inner_content = $inner_content;
    }

    function render()
    {
        extract(shortcode_atts(array(
            Team_Members_Shortcode::$TM_ATTR_PHOTO => '/images/demo/team-member.png',
            Team_Members_Shortcode::$TM_ATTR_NAME => '',
            Team_Members_Shortcode::$TM_ATTR_FUNCTION => '',
            Team_Members_Shortcode::$TM_ATTR_EMAIL => '',
            Team_Members_Shortcode::$TM_ATTR_FACEBOOK => '',
            Team_Members_Shortcode::$TM_ATTR_TWITTER => '',
            Team_Members_Shortcode::$TM_ATTR_GPLUS => '',
            Team_Members_Shortcode::$TM_ATTR_LINKEDIN => '',
            Team_Members_Shortcode::$TM_ATTR_SKYPE => '',
        ), $this->attr));

        $photo_src = Multimedia_Util::get_image_src($photo, 'custom-team-member-thumb');

        $social_links = '';
        if (strlen($twitter) > 0) {
            $social_links .= '<li class="twitter"><a href="' . $twitter . '" title="Twitter" target="_blank">Twitter</a></li>' . "\n";
        }
        if (strlen($facebook) > 0) {
            $social_links .= '<li class="facebook"><a href="' . $facebook . '" title="Facebook" target="_blank">Facebook</a></li>' . "\n";
        }
        if (strlen($linkedin) > 0) {
            $social_links .= '<li class="linkedin"><a href="' . $linkedin . '" title="LinkedIn" target="_blank">LinkedIn</a></li>' . "\n";
        }
        if (strlen($googleplus) > 0) {
            $social_links .= '<li class="googleplus"><a href="' . $googleplus . '" title="Google+" target="_blank">Google+</a></li>' . "\n";
        }
        if (strlen($skype) > 0) {
            $social_links .= '<li class="skype"><a href="' . $skype . '" title="Skype" target="_blank">Skype</a></li>' . "\n";
        }
        if (strlen($email) > 0) {
            $social_links .= '<li class="email"><a href="' . $email . '" title="Email" target="_blank">Email</a></li>' . "\n";
        }

        $content = '<img class="photo" src="' . $photo_src . '" alt="' . $name . '">' . "\n";
        $content .= '<div class="content">' . "\n";
        $content .= '<h3 class="name">' . $name . '</h3>' . "\n";
        $content .= '<span class="job-title">' . $function . '</span>' . "\n";
        $content .= '<p>' . $this->inner_content . '</p>' . "\n";
        if (strlen($social_links) > 0) {
            $content .= '<ul class="social-links">' . "\n";
            $content .= $social_links;
            $content .= '</ul>' . "\n";
        }
        $content .= '</div>' . "\n";
        return $content;
    }

}