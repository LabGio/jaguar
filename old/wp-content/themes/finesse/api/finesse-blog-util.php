<?php

add_filter('get_avatar', 'customize_avatar_link');
if (!function_exists('customize_avatar_link')) {
    function customize_avatar_link($avatar)
    {
        $x = strpos($avatar, 'class');
        $x1 = strpos($avatar, '\'', $x);
        $y = strpos($avatar, '\'', $x1 + 3);
        $to_replace = substr($avatar, $x, ($y - $x) + 1);
        str_replace($to_replace, '', $avatar);
        return str_replace($to_replace, '', $avatar);
    }
}

add_filter('comment_form_defaults', 'customize_comment_form_defaults');
if (!function_exists('customize_comment_form_defaults')) {
    function customize_comment_form_defaults($arg)
    {
        $arg['title_reply'] = __('Leave a Comment', 'finesse');
        $arg['comment_notes_before'] = '<p>' . __('We would be glad to get your feedback. Take a moment to comment and tell us what you think.', 'finesse') . '</p>';
        $arg['id_form'] = 'comment-form';
        $arg['comment_field'] = '<p><label for="comment">' . __('Message', 'finesse') . ':<span class="asterisk note">*</span></label><textarea id="comment" cols="45" rows="8" name="comment" class="required"></textarea></p>';
        return $arg;
    }
}

add_filter('comment_form_default_fields', 'customize_comment_form_default_fields');
if (!function_exists('customize_comment_form_default_fields')) {
    function customize_comment_form_default_fields($arg)
    {
        $commenter = wp_get_current_commenter();
        $req = get_option('require_name_email');
        $req_sign = ($req ? '<span class="asterisk note">*</span>' : '');
        $req_class = ($req ? 'class="required"' : '');
        $arg['author'] = '<p><label for="author">' . __('Name', 'finesse') . ':' . $req_sign . '</label><input id="author" type="text" name="author" value="' . esc_attr($commenter['comment_author']) . '" ' . $req_class . '></p>';
        $arg['email'] = '<p><label for="email">' . __('Email', 'finesse') . ':' . $req_sign . '</label><input id="email" type="email" name="email" value="' . esc_attr($commenter['comment_author_email']) . '" ' . $req_class . '></p>';
        $arg['url'] = '<p><label for="url">' . __('Website', 'finesse') . ':</label><input id="url" type="text" name="url" value="' . esc_attr($commenter['comment_author_url']) . '"></p>';
        return $arg;
    }
}

if (!function_exists('print_finesse_comments')) {
    function print_finesse_comments($comment, $args, $depth)
    {
        $comment_index = $GLOBALS['comment_index'];
        $article_author_id = get_the_author_meta('ID');
        $comment_id = $comment->comment_ID;
        $GLOBALS['comment'] = $comment;
        $author_name = htmlspecialchars($comment->comment_author);

        $author_url = get_comment_author_link($comment_id);
        if ($article_author_id == $comment->user_id) {
            $author_url = str_replace($author_name, $author_name . '<span class="post-author"> (' . __('Author', 'finesse') . ')</span>', $author_url);
        }
        $comment_text = get_comment_text($comment_id);
        $comment_date = get_comment_date() . ' ' . __('at', 'finesse') . ' ' . get_comment_time();

        echo "<li class=\"comment\">\n";
        echo "<div id=\"comment-$comment_index\" class=\"comment-wrap\">\n";
        echo "<div class=\"avatar-wrap\">\n";
        echo "<div class=\"avatar\">\n";
        echo get_avatar($comment, 41);
        echo "</div>\n";
        if ($comment->comment_approved != '0') {
            edit_comment_link(__('Edit', 'finesse'), ' ');
        }
        echo "</div>\n";
        echo "<div class=\"comment-details\">\n";
        echo "<div class=\"comment-author\">$author_url</div>\n";
        echo "<div class=\"comment-meta\">$comment_date</div>\n";
        echo "<div class=\"comment-content\">\n";
        echo "<p>$comment_text</p>\n";
        if ($comment->comment_approved == '0') {
            echo "<em class=\"moderation\">" . __('(Your comment is awaiting moderation.)', 'finesse') . "</em>\n";
        } else {
            comment_reply_link(array_merge($args, array('depth' => $depth, 'max_depth' => $args['max_depth'], 'reply_text' => __('Reply', 'finesse') . ' &raquo;')));
        }
        echo "</div>\n";
        echo "</div>\n";
        echo "</div>\n";
        $GLOBALS['comment_index'] = $comment_index + 1;
    }
}
