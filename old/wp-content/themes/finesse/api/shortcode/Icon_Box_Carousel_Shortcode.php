<?php

class Icon_Box_Carousel_Shortcode extends Abstract_Finesse_Shortcode implements Shortcode_Designer
{

    static $POST_IDENTIFIER_ATTR = 'post_identifier';
    static $AUTO_ATTR = 'auto';
    static $SCROLL_COUNT = 'scroll_count';
    static $TITLE_ATTR = "title";
    static $HREF_ATTR = "href";
    static $TARGET_ATTR = "target";
    static $TYPE_ATTR = "type";
    static $ICON_ATTR = "icon";

    var $items = array();

    private function reset()
    {
        unset($this->items);
        $this->items = array();
    }

    function render($attr, $inner_content = null, $code = '')
    {
        $content = '';
        switch ($code) {
            case "iconbox_carousel":
                do_shortcode($this->prepare_content($inner_content));
                $content .= $this->render_carousel($attr);
                $this->reset();
                break;
            case "iconbox_carousel_item":
                $inner_content = do_shortcode($this->prepare_content($inner_content));
                $this->render_carousel_item($attr, $inner_content);
                break;
        }
        return $content;
    }

    private function render_carousel($attr)
    {
        extract(shortcode_atts(array(
            Icon_Box_Carousel_Shortcode::$POST_IDENTIFIER_ATTR => '',
            Icon_Box_Carousel_Shortcode::$SCROLL_COUNT => '',
            Icon_Box_Carousel_Shortcode::$AUTO_ATTR => ''), $attr));

        $data_scroll_attr = $this->get_attribute('data-scroll', $scroll_count);
        $data_auto_attr = $this->get_attribute('data-auto', $auto);

        $content = '<ul class="iconbox-carousel"' . $data_scroll_attr . $data_auto_attr . '>' . "\n";
        if (empty($post_identifier)) {
            foreach ($this->items as $item) {
                $content .= '<li>' . "\n";
                $content .= $item->render() . "\n";
                $content .= '</li>' . "\n";
            }
        } else {
            $post = Post_Util::find_post_by_identifier($post_identifier);
            $shortcodes = Post_Util::get_all_shortcodes_of_type('iconbox', $post);
            foreach ($shortcodes as $sc) {
                $content .= '<li>' . "\n";
                $link = Post_Util::get_shortcode_attribute('iconbox', Icon_Box_Shortcode::$HREF_ATTR);
                if (!isset($link) || empty($link) || $link == '#') {
                    $link = get_permalink($post->ID);
                    $sc_id = Post_Util::get_shortcode_attribute($sc, Icon_Box_Shortcode::$ID_ATTR);
                    if ($sc_id) {
                        $link = $link . '#' . $sc_id;
                    }
                    $sc = Post_Util::override_shortcode_attribute($sc, Icon_Box_Shortcode::$HREF_ATTR, $link);
                }
                $content .= do_shortcode($sc);
                $content .= '</li>' . "\n";
            }
        }
        $content .= '</ul>';

        return $content;
    }

    private function render_carousel_item($attr, $inner_content)
    {
        array_push($this->items, new Finesse_Icon_Box_Carousel_Item($attr, $inner_content));
    }

    function get_names()
    {
        return array('iconbox_carousel', 'iconbox_carousel_item');
    }

    function get_visual_editor_form()
    {
        global $wpdb;
        $posts = $wpdb->get_results("SELECT ID, post_title FROM $wpdb->posts WHERE post_type='page'");
        $content = '<form id="sc-ib-carousel-form" class="generic-form" method="post" action="#" data-sc="iconbox_carousel">';
        $content .= '<fieldset>';

        $content .= '<div>';
        $content .= '<label for="sc-ib-carousel-genmode">' . __('Generation Mode', 'finesse') . ':</label>';
        $content .= '<select id="sc-ib-carousel-genmode" name="sc-ib-carousel-genmode">';
        $content .= '<option value="m">' . __('Add one by one manually', 'finesse') . '</option>';
        $content .= '<option value="a">' . __('Automatically extracts from page', 'finesse') . '</option>';
        $content .= '</select>';
        $content .= '</div>';
        $content .= '<div style="display: none">';
        $content .= '<label for="sc-ib-carousel-postid">' . __('Select Page', 'finesse') . ':</label>';
        $content .= '<select id="sc-ib-carousel-postid" name="sc-ib-carousel-postid" data-attr-name="' . Icon_Box_Carousel_Shortcode::$POST_IDENTIFIER_ATTR . '" data-attr-type="attr">';
        foreach ($posts as $i => $post) {
            $content .= '<option value="' . $post->ID . '">' . $post->post_title . ' (' . $post->ID . ')' . '</option>';
        }
        $content .= '</select>';
        $content .= '</div>';
        $content .= '<div>';
        $content .= '<label for="sc-ib-carousel-content">' . __('Content', 'finesse') . ':</label>';
        $content .= '<textarea id="sc-ib-carousel-content" name="sc-ib-carousel-content" class="required" data-attr-type="content"></textarea>';
        $content .= '</div>';
        $content .= '<div >';
        $content .= '<input id="sc-ib-carousel-form-submit" type="submit" name="submit" value="' . __('Insert Icon-Box Carousel', 'finesse') . '" class="button-primary">';
        $content .= '<input id="sc-ib-carousel-form-add" type="submit" name="submit" value="' . __('Add Carousel Item', 'finesse') . '" class="button-secondary">';
        $content .= '</div>';
        $content .= '</fieldset>';
        $content .= '</form>';

        $content .= '<div id="sc-add-ib-carousel-item-dialog" title="' . __('New Carousel Item', 'finesse') . '" style="display: none">';
        $content .= '<form id="sc-add-ib-carousel-item-form" class="generic-form" method="post" action="#" data-sc="iconbox_carousel_item">';
        $content .= '<fieldset>';
        $content .= '<div>';
        $content .= '<label for="sc-add-ib-carousel-item-title">' . __('Title', 'finesse') . ':</label>';
        $content .= '<input id="sc-add-ib-carousel-item-title" name="sc-add-ib-carousel-item-title" type="text" class="required" data-attr-name="' . Icon_Box_Carousel_Shortcode::$TITLE_ATTR . '" data-attr-type="attr">';
        $content .= '</div>';
        $content .= '<div>';
        $content .= '<label for="sc-add-ib-carousel-item-url">' . __('URL', 'finesse') . ':</label>';
        $content .= '<input id="sc-add-ib-carousel-item-url" name="sc-add-ib-carousel-item-url" type="text" data-attr-name="' . Icon_Box_Carousel_Shortcode::$HREF_ATTR . '" data-attr-type="attr">';
        $content .= '</div>';
        $content .= '<div>';
        $content .= '<label for="sc-add-ib-carousel-item-type">' . __('Type', 'finesse') . ':</label>';
        $content .= '<select id="sc-add-ib-carousel-item-type" name="sc-add-ib-carousel-item-type" data-attr-name="' . Icon_Box_Carousel_Shortcode::$TYPE_ATTR . '" data-attr-type="attr">';
        foreach (Icon_Box_Shortcode::$icons as $value => $name) {
            $content .= '<option value="' . $value . '">' . $name . '</option>';
        }
        $content .= '</select>';
        $content .= '</div>';
        $content .= '<div>';
        $content .= '<label for="sc-add-ib-carousel-item-icon">' . __('Icon URL', 'finesse') . ':</label>';
        $content .= '<input id="sc-add-ib-carousel-item-icon" name="sc-add-ib-carousel-item-icon" type="text" data-attr-name="' . Icon_Box_Carousel_Shortcode::$ICON_ATTR . '" data-attr-type="attr">';
        $content .= '</div>';
        $content .= '<div>';
        $content .= '<label for="sc-add-ib-carousel-item-content">' . __('Content', 'finesse') . ':</label>';
        $content .= '<textarea id="sc-add-ib-carousel-item-content" name="sc-add-ib-carousel-item-content" class="required" data-attr-type="content"></textarea>';
        $content .= '</div>';
        $content .= '<div >';
        $content .= '<input id="sc-add-ib-carousel-item-form-submit" type="submit" name="submit" value="' . __('Insert Carousel Item', 'finesse') . '" class="button-primary">';
        $content .= '<input id="sc-add-ib-carousel-item-form-cancel" type="submit" name="submit" value="' . __('Cancel', 'finesse') . '" class="button-secondary">';
        $content .= '</div>';
        $content .= '</fieldset>';
        $content .= '</form>';
        $content .= '</div>';
        return $content;
    }

    function get_group_title()
    {
        return __('Dynamic Elements', 'finesse');
    }

    function get_title()
    {
        return __('Icon-Box Carousel', 'finesse');
    }
}

class Finesse_Icon_Box_Carousel_Item
{
    private $attr;
    private $content;

    function __construct($attr, $content)
    {
        $this->attr = $attr;
        $this->content = $content;
    }

    function get_columns_count($separator)
    {
        return count(explode($separator, $this->content));
    }

    function render()
    {
        extract(shortcode_atts(array(
            Icon_Box_Carousel_Shortcode::$TITLE_ATTR => '',
            Icon_Box_Carousel_Shortcode::$HREF_ATTR => '',
            Icon_Box_Carousel_Shortcode::$TARGET_ATTR => '',
            Icon_Box_Carousel_Shortcode::$ICON_ATTR => '',
            Icon_Box_Carousel_Shortcode::$TYPE_ATTR => ''), $this->attr));

        return do_shortcode('[iconbox target="' . $target . '" icon="' . $icon . '" title="' . $title . '" href="' . $href . '" type="' . $type . '"]' . $this->content . '[/iconbox]');
    }

}