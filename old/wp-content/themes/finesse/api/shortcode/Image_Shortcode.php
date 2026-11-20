<?php

class Image_Shortcode extends Abstract_Finesse_Shortcode implements Shortcode_Designer
{

    static $HREF_ATTR = "href";
    static $TARGET_ATTR = "target";
    static $SRC_ATTR = "src";
    static $THUMB_SRC_ATTR = "thumb_src";
    static $SIZE_ATTR = "size";
    static $LIGHTBOX_ATTR = "lightbox";
    static $TITLE_ATTR = "title";
    static $ALIGN_ATTR = "align";
    static $REL_ATTR = "rel";
    static $ALT_ATTR = "alt";
    static $CAPTION_ATTR = "caption";

    function render($attr, $inner_content = null, $code = '')
    {
        $content = '';
        extract(shortcode_atts(array(
            Image_Shortcode::$HREF_ATTR => '',
            Image_Shortcode::$TARGET_ATTR => '',
            Image_Shortcode::$REL_ATTR => '',
            Image_Shortcode::$SRC_ATTR => '',
            Image_Shortcode::$THUMB_SRC_ATTR => '',
            Image_Shortcode::$SIZE_ATTR => 'full',
            Image_Shortcode::$LIGHTBOX_ATTR => 'false',
            Image_Shortcode::$TITLE_ATTR => '',
            Image_Shortcode::$ALIGN_ATTR => '',
            Image_Shortcode::$ALT_ATTR => '',
            Image_Shortcode::$CAPTION_ATTR => ''), $attr));
        $attach_post = Multimedia_Util::get_original_post($src);
        if ($attach_post && $caption == 'inherited') {
            $caption = $attach_post->post_excerpt;
        }
        $img_content = $this->get_image_content(
            array("attach_post" => $attach_post,
                "src" => $src,
                "thumb_src" => $thumb_src,
                "title" => $title,
                "size" => $size,
                "lightbox" => ($lightbox == 'true'),
                "href" => $href,
                "rel" => $rel,
                "target" => $target,
                "alt" => $alt,
            ));

        if (strlen($caption) > 0) {
            if ($align == 'right') {
                $content .= "<div class=\"caption float-right\">";
                $content .= "<div class=\"entry-image\">$img_content</div>";
                $content .= "<p class=\"caption-text\">$caption</p>";
                $content .= "</div>";
            } elseif ($align == 'left') {
                $content .= "<div class=\"caption float-left\">";
                $content .= "<div class=\"entry-image\">$img_content</div>";
                $content .= "<p class=\"caption-text\">$caption</p>";
                $content .= "</div>";
            } else {
                $content .= "<div class=\"caption\">";
                $content .= "<div class=\"entry-image\">$img_content</div>";
                $content .= "<p class=\"caption-text\">$caption</p>";
                $content .= "</div>";
            }
        } else {
            if ($align == 'right') {
                $content = "<div class=\"entry-image float-right\">$img_content</div>";
            } elseif ($align == 'left') {
                $content = "<div class=\"entry-image float-left\">$img_content</div>";
            } else {
                $content = "<div class=\"entry-image\">$img_content</div>";
            }
        }
        return $content;
    }

    private function get_image_content($settings)
    {
        $attach_post = $settings['attach_post'];
        $src = $settings['src'];
        $thumb_src = $settings['thumb_src'];
        $title = $settings['title'];
        $size = $settings['size'];
        $lightbox = $settings['lightbox'];
        $href = $settings['href'];
        $rel = $settings['rel'];
        $target = $settings['target'];
        $alt = $settings['alt'];
        if (empty($alt)) {
            $alt = $title;
        }
        if ($attach_post) {
            $orig_size_src = Multimedia_Util::get_post_image_src($attach_post);
            switch ($size) {
                case 'thumb':
                    $src = Multimedia_Util::get_post_thumbnail_image_src($attach_post);
                    break;
                case 'post':
                    $src = Multimedia_Util::get_post_thumbnail_image_src($attach_post, 'custom-blog-post-thumb');
                    break;
                case 'portfolio':
                    $src = Multimedia_Util::get_post_thumbnail_image_src($attach_post, 'custom-portfolio-post-thumb');
                    break;
                case 'full':
                    $src = $orig_size_src;
                    break;
                default:
                    $src = Multimedia_Util::get_post_thumbnail_image_src($attach_post, $size);
                    break;
            }
        } else {
            $orig_size_src = Multimedia_Util::get_external_image_src($src);
            if (strlen($thumb_src) > 0) {
                $src = Multimedia_Util::get_external_image_src($thumb_src);
            } else {
                $src = $orig_size_src;
            }
        }


        if (isset($href) && !empty($href)) {
            $target = empty($target) ? '' : ' target="' . $target . '"';
            return "<a " . $target . " href=\"$href\" title=\"$title\"><span class=\"overlay link\"></span><img src=\"$src\" alt=\"$alt\"></a>";
        } elseif ($lightbox) {
            $rel_attr = empty($rel) ? '' : ' data-fancybox-group="' . $rel . '"';
            return "<a" . $rel_attr . " class=\"fancybox\" href=\"$orig_size_src\" title=\"$title\"><span class=\"overlay zoom\"></span><img src=\"$src\" alt=\"$alt\"></a>";
        } else {
            return "<img src=\"$src\" title=\"$title\" alt=\"$alt\">";
        }
    }

    function get_names()
    {
        return array('img');
    }

    function get_visual_editor_form()
    {
        $content = '<form id="sc-img-form" class="generic-form" method="post" action="#" data-sc="img">';
        $content .= '<fieldset>';
        $content .= '<div class="image-tab-content">';

        $content .= '<div class="image-tab-content-left">';
        $content .= '<div class="radio-row">';
        $content .= '<span>';
        $content .= '<input id="sc-img-src-uploaded-type-source" type="radio" name="sc-img-src-type-source" value="uploaded" checked>';
        $content .= '<label for="sc-img-src-uploaded-type-source">' . __('Uploaded Image', 'finesse') . '</label>';
        $content .= '</span>';
        $content .= '<span>';
        $content .= '<input id="sc-img-src-new-type-source" type="radio" name="sc-img-src-type-source" value="new">';
        $content .= '<label for="sc-img-src-new-type-source">' . __('New Image', 'finesse') . '</label>';
        $content .= '</span>';
        $content .= '</div>';
        $content .= '<div>';
        $content .= '<label for="sc-img-src-uploaded-img-src">' . __('Image Name', 'finesse') . ':</label>';
        $content .= '<select id="sc-img-src-uploaded-img-src" name="sc-img-src-uploaded-img-src" class="required image-selector" data-base-id="sc-img-src-" data-attr-name="' . Image_Shortcode::$SRC_ATTR . '" data-attr-type="attr">';
        $content .= '<option value="">' . __('Select Image ...', 'finesse') . '</option>';
        $images = Multimedia_Util::get_all_uploaded_images();
        foreach ($images as $img) {
            $images = wp_get_attachment_image_src($img->ID);
            $content .= '<option value="' . $img->post_title . '" data-src="' . $images[0] . '">' . $img->post_title . '</option>';
        }
        $content .= '</select>';
        $content .= '</div>';
        $content .= '<div>';
        $content .= '<label for="sc-img-src-uploaded-size-src">' . __('Image Size', 'finesse') . '</label>';
        $content .= '<select id="sc-img-src-uploaded-size-src" name="sc-img-src-uploaded-size-src" data-attr-name="' . Image_Shortcode::$SIZE_ATTR . '" data-attr-type="attr">';
        $content .= '<option value="full">' . __('Original Size', 'finesse') . '</option>';
        $content .= '<option value="thumb">' . __('Custom Thumbnail Size (220 x 130)', 'finesse') . '</option>';
        $content .= '<option value="thumbnail">' . __('Default Thumbnail Size (150 x 150)', 'finesse') . '</option>';
        $content .= '<option value="medium">' . __('Medium Size (300 x 300)', 'finesse') . '</option>';
        $content .= '<option value="large">' . __('Large Size (640 x 640)', 'finesse') . '</option>';
        $content .= '</select>';
        $content .= '</div>';
        $content .= '<div>';
        $content .= '<input id="sc-img-lightbox" name="sc-img-lightbox" type="checkbox" data-attr-name="' . Image_Shortcode::$LIGHTBOX_ATTR . '" data-attr-type="attr">';
        $content .= '<label for="sc-img-lightbox">' . __('Use Lightbox', 'finesse') . '</label>';
        $content .= '</div>';
        $content .= '<div style="display: none">';
        $content .= '<label for="sc-img-src-new-img-src">' . __('Image Src', 'finesse') . ':</label>';
        $content .= '<input id="sc-img-src-new-img-src" name="sc-img-src-new-img-src" type="text" class="required" data-attr-name="' . Image_Shortcode::$SRC_ATTR . '" data-attr-type="attr">';
        $content .= '</div>';
        $content .= '<div style="display: none">';
        $content .= '<label for="sc-img-src-new-thumb-src">' . __('Thumbnail Src', 'finesse') . ':</label>';
        $content .= '<input id="sc-img-src-new-thumb-src" name="sc-img-src-new-thumb-src" type="text" data-attr-name="' . Image_Shortcode::$THUMB_SRC_ATTR . '" data-attr-type="attr">';
        $content .= '</div>';

        $content .= '<div>';
        $content .= '<label for="sc-img-title">' . __('Title', 'finesse') . ':</label>';
        $content .= '<input id="sc-img-title" name="sc-img-title" type="text" data-attr-name="' . Image_Shortcode::$TITLE_ATTR . '" data-attr-type="attr">';
        $content .= '</div>';
        $content .= '<div>';
        $content .= '<label for="sc-img-caption">' . __('Caption', 'finesse') . ':</label>';
        $content .= '<input id="sc-img-caption" name="sc-img-caption" type="text" data-attr-name="' . Image_Shortcode::$CAPTION_ATTR . '" data-attr-type="attr">';
        $content .= '</div>';
        $content .= '<div>';
        $content .= '<label for="sc-img-align">' . __('Align', 'finesse') . ':</label>';
        $content .= '<select id="sc-img-align" name="sc-img-align" data-attr-name="' . Image_Shortcode::$ALIGN_ATTR . '" data-attr-type="attr">';
        $content .= '<option value="" >' . __('None', 'finesse') . '</option>';
        $content .= '<option value="left">' . __('Left', 'finesse') . '</option>';
        $content .= '<option value="right">' . __('Right', 'finesse') . '</option>';
        $content .= '</select>';
        $content .= '</div>';
        $content .= '<div >';
        $content .= '<input id="sc-img-form-submit" type="submit" name="submit" value="' . __('Insert Image', 'finesse') . '" class="button-primary">';
        $content .= '</div>';
        $content .= '</div>';

        $content .= '<div class="image-tab-content-right">';
        $content .= '<img id="sc-img-src-preview" src="#" alt="">';
        $content .= '</div>';

        $content .= '</div>';
        $content .= '</fieldset>';
        $content .= '</form>';

        return $content;
    }

    function get_group_title()
    {
        return __('Multimedia', 'finesse');
    }

    function get_title()
    {
        return __('Image', 'finesse');
    }
}
