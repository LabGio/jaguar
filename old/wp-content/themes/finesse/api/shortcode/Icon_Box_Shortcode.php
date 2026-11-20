<?php

class Icon_Box_Shortcode extends Abstract_Finesse_Shortcode implements Shortcode_Designer
{
    static $ID_ATTR = "id";
    static $TITLE_ATTR = "title";
    static $HREF_ATTR = "href";
    static $TARGET_ATTR = "target";
    static $TYPE_ATTR = "type";
    static $ICON_ATTR = "icon";
    static $icons = array(
        '' => 'Custom',
        'computer' => 'Computer',
        'mouse' => 'Mouse',
        'applications' => 'Applications',
        'cog' => 'Cog',
        'globe' => 'Globe',
        'write' => 'Write',
        'address-book' => 'Address Book',
        'chemical' => 'Chemical',
    );

    function render($attr, $inner_content = null, $code = '')
    {
        $inner_content = do_shortcode($this->prepare_content($inner_content));
        extract(shortcode_atts(array(
            Icon_Box_Shortcode::$ID_ATTR => '',
            Icon_Box_Shortcode::$TITLE_ATTR => '',
            Icon_Box_Shortcode::$HREF_ATTR => '',
            Icon_Box_Shortcode::$TARGET_ATTR => '',
            Icon_Box_Shortcode::$ICON_ATTR => '',
            Icon_Box_Shortcode::$TYPE_ATTR => ''), $attr));
        $title = ___($title);
        $id = empty($id) ? '' : 'id="' . $id . '"';
        $target = empty($target) ? '' : ' target="' . $target . '"';

        if (empty($href)) {
            if (empty($icon)) {
                $content = "<div $id class=\"iconbox $type\">\n
                        <h4><span class=\"iconbox-icon\"></span>$title</h4>\n
                        <p>$inner_content</p>\n
                    </div>";
            } else {
                $icon = Multimedia_Util::get_image_src($icon);
                $content = "<div $id class=\"iconbox\">\n
                        <h4><img class=\"iconbox-icon\" src=\"$icon\" alt=\"$title\">$title</h4>\n
                        <p>$inner_content</p>\n
                    </div>";
            }
        } else {
            if (empty($icon)) {
                $content = "<div $id class=\"iconbox $type\">\n
                        <h4><a".$target." href=\"$href\"><span class=\"iconbox-icon\"></span>$title</a></h4>\n
                        <p>$inner_content</p>\n
                    </div>";
            } else {
                $icon = Multimedia_Util::get_image_src($icon);
                $content = "<div $id class=\"iconbox\">\n
                        <h4><a".$target." href=\"$href\"><img class=\"iconbox-icon\" src=\"$icon\" alt=\"$title\">$title</a></h4>\n
                        <p>$inner_content</p>\n
                    </div>";
            }
        }

        return $content;
    }

    function get_names()
    {
        return 'iconbox';
    }

    function get_visual_editor_form()
    {
        $content = '<form id="sc-iconbox-form" class="generic-form" method="post" action="#" data-sc="iconbox">';
        $content .= '<fieldset>';
        $content .= '<div>';
        $content .= '<label for="sc-iconbox-id">' . __('ID', 'finesse') . ':</label>';
        $content .= '<input id="sc-iconbox-id" name="sc-iconbox-id" type="text" data-attr-name="' . Icon_Box_Shortcode::$ID_ATTR . '" data-attr-type="attr">';
        $content .= '</div>';
        $content .= '<div>';
        $content .= '<label for="sc-iconbox-title">' . __('Title', 'finesse') . ':</label>';
        $content .= '<input id="sc-iconbox-title" name="sc-iconbox-title" type="text" class="required" data-attr-name="' . Icon_Box_Shortcode::$TITLE_ATTR . '" data-attr-type="attr">';
        $content .= '</div>';
        $content .= '<div>';
        $content .= '<label for="sc-iconbox-url">' . __('URL', 'finesse') . ':</label>';
        $content .= '<input id="sc-iconbox-url" name="sc-iconbox-url" type="text" data-attr-name="' . Icon_Box_Shortcode::$HREF_ATTR . '" data-attr-type="attr">';
        $content .= '</div>';
        $content .= '<div>';
        $content .= '<label for="sc-iconbox-type">' . __('Type', 'finesse') . ':</label>';
        $content .= '<select id="sc-iconbox-type" name="sc-iconbox-type" data-attr-name="' . Icon_Box_Shortcode::$TYPE_ATTR . '" data-attr-type="attr">';
        foreach (Icon_Box_Shortcode::$icons as $value => $name) {
            $content .= '<option value="' . $value . '">' . $name . '</option>';
        }
        $content .= '</select>';
        $content .= '</div>';
        $content .= '<div>';
        $content .= '<label for="sc-iconbox-icon">' . __('Icon URL', 'finesse') . ':</label>';
        $content .= '<input id="sc-iconbox-icon" name="sc-iconbox-icon" type="text" data-attr-name="' . Icon_Box_Shortcode::$ICON_ATTR . '" data-attr-type="attr">';
        $content .= '</div>';
        $content .= '<div>';
        $content .= '<label for="sc-iconbox-content">' . __('Content', 'finesse') . ':</label>';
        $content .= '<textarea id="sc-iconbox-content" name="sc-iconbox-content" class="required" data-attr-type="content"></textarea>';
        $content .= '</div>';
        $content .= '<div >';
        $content .= '<input id="sc-iconbox-form-submit" type="submit" name="submit" value="' . __('Insert Icon Box', 'finesse') . '" class="button-primary">';
        $content .= '</div>';
        $content .= '</fieldset>';
        $content .= '</form>';

        return $content;
    }

    function get_group_title()
    {
        return __('Elements', 'finesse');
    }

    function get_title()
    {
        return __('Icon Box', 'finesse');
    }
}
