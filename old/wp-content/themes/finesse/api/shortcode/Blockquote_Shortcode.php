<?php

class Blockquote_Shortcode extends Abstract_Finesse_Shortcode implements Shortcode_Designer
{
    static $ID_ATTR = "id";
    static $THUMB_ATTR = "thumb";
    static $HREF_ATTR = "href";
    static $TARGET_ATTR = "target";
    static $TYPE_ATTR = "type";
    static $PARAGRAPH_SEPARATOR_ATTR = "separator";
    static $AUTHOR_ATTR = "author";
    static $PROFESSION_ATTR = "profession";
    static $COMPANY_ATTR = "company";

    function render($attr, $inner_content = null, $code = '')
    {
        $inner_content = do_shortcode($this->prepare_content($inner_content));
        extract(shortcode_atts(array(
            Blockquote_Shortcode::$ID_ATTR => '',
            Blockquote_Shortcode::$HREF_ATTR => '',
            Blockquote_Shortcode::$TARGET_ATTR => '',
            Blockquote_Shortcode::$TYPE_ATTR => 'bubble',
            Blockquote_Shortcode::$THUMB_ATTR => 'false',
            Blockquote_Shortcode::$PARAGRAPH_SEPARATOR_ATTR => '|',
            Blockquote_Shortcode::$AUTHOR_ATTR => '',
            Blockquote_Shortcode::$PROFESSION_ATTR => '',
            Blockquote_Shortcode::$COMPANY_ATTR => ''), $attr));
        $lines = explode($separator, $inner_content);

        $is_simple = ($type == 'simple');
        $id = empty($id) ? '' : ' id="' . $id . '"';
        $target = empty($target) ? '' : ' target="' . $target . '"';
        $class_name = $is_simple ? ' class="simple"' : ' class="speech-bubble"';
        $read_more = empty($href) ? '&hellip;' : '<a'.$target.' href="' . $href . '">' . __('Read More', 'finesse') . ' &hellip;</a>';

        $content = '<blockquote' . $id . $class_name . '>';
        $content .= '<div class="quote-content">';
        if ($thumb == 'true') {
            $read_more = apply_filters('finesse_bq_ellipsis', $read_more);
            $excerpt_length = apply_filters('finesse_bq_excerpt_length', 60);
            $content .= '<p>' . shrink($lines[0], $excerpt_length, $read_more) . '</p>';
        } else {
            if (empty($href)) {
                for ($i = 0; $i < count($lines); $i++) {
                    $content .= '<p>' . $lines[$i] . '</p>';
                }
            } else {
                $content .= '<p>' . $lines[0] . ' ' . $read_more . '</p>';
            }
        }
        if (!$is_simple) {
            $content .= '<span class="quote-arrow"></span>';
        }
        $content .= '</div>';
        if (strlen($author) > 0) {
            if ($is_simple) {
                $profession = strlen($profession) > 0 ? ', ' . $profession : '';
                $company = strlen($company) > 0 ? ', ' . $company : '';
                $content .= '<div class="quote-meta">&mdash; ' . $author . $profession . $company . '</div>';
            } else {
                $profession = strlen($profession) > 0 ? ', ' . $profession . "\n" : '';
                $company = strlen($company) > 0 ? "<br>\n<span class=\"grey-text\">" . $company . "</span>\n" : '';
                $content .= '<div class="quote-meta">' . $author . $profession . $company . '</div>';
            }
        }
        $content .= '</blockquote>';

        return $content;
    }

    function get_names()
    {
        return array('bq');
    }

    function get_visual_editor_form()
    {
        $content = '<form id="sc-bq-form" class="generic-form" method="post" action="#" data-sc="bq">';
        $content .= '<fieldset>';
        $content .= '<div>';
        $content .= '<label for="sc-bq-type">' . __('Type', 'finesse') . ':</label>';
        $content .= '<select id="sc-bq-type" name="sc-bq-type" data-attr-name="' . Blockquote_Shortcode::$TYPE_ATTR . '" data-attr-type="attr">';
        $content .= '<option value="bubble">' . __('Speech Bubble', 'finesse') . '</option>';
        $content .= '<option value="simple">' . __('Simple', 'finesse') . '</option>';
        $content .= '</select>';
        $content .= '</div>';
        $content .= '<div>';
        $content .= '<label for="sc-bq-id">' . __('ID', 'finesse') . ':</label>';
        $content .= '<input id="sc-bq-id" name="sc-bq-id" type="text" data-attr-name="' . Blockquote_Shortcode::$ID_ATTR . '" data-attr-type="attr">';
        $content .= '</div>';
        $content .= '<div>';
        $content .= '<label for="sc-bq-content">' . __('Quote Content', 'finesse') . ':</label>';
        $content .= '<textarea id="sc-bq-content" name="sc-bq-content" class="required" data-attr-type="content"></textarea>';
        $content .= '</div>';
        $content .= '<div>';
        $content .= '<label for="sc-bq-author">' . __('Author', 'finesse') . ':</label>';
        $content .= '<input id="sc-bq-author" name="sc-bq-author" type="text" data-attr-name="' . Blockquote_Shortcode::$AUTHOR_ATTR . '" data-attr-type="attr">';
        $content .= '</div>';
        $content .= '<div>';
        $content .= '<label for="sc-bq-profession">' . __('Profession', 'finesse') . ':</label>';
        $content .= '<input id="sc-bq-profession" name="sc-bq-profession" type="text" data-attr-name="' . Blockquote_Shortcode::$PROFESSION_ATTR . '" data-attr-type="attr">';
        $content .= '</div>';
        $content .= '<div>';
        $content .= '<label for="sc-bq-company">' . __('Company', 'finesse') . ':</label>';
        $content .= '<input id="sc-bq-company" name="sc-bq-company" type="text" data-attr-name="' . Blockquote_Shortcode::$COMPANY_ATTR . '" data-attr-type="attr">';
        $content .= '</div>';
        $content .= '<div>';
        $content .= '<label for="sc-bq-separator">' . __('Paragraph Separator', 'finesse') . ':</label>';
        $content .= '<input id="sc-bq-separator" name="sc-bq-separator" type="text" value="|" class="required" data-attr-name="' . Blockquote_Shortcode::$PARAGRAPH_SEPARATOR_ATTR . '" data-attr-type="attr">';
        $content .= '</div>';
        $content .= '<div >';
        $content .= '<input id="sc-bq-form-submit" type="submit" name="submit" value="' . __('Insert Blockquote', 'finesse') . '" class="button-primary">';
        $content .= '</div>';
        $content .= '</fieldset>';
        $content .= '</form>';
        return $content;
    }

    function get_group_title()
    {
        return __('Typography', 'finesse');
    }

    function get_title()
    {
        return __('Blockquote', 'finesse');
    }
}
