<?php

$paged = 1;
$prev_page = -1;
$next_page = -1;

if (!function_exists('do_finesse_pagination')) {
    function do_finesse_pagination($max_num_pages = '')
    {
        global $paged;
        global $wp_query;

        $first_text = __('First', 'finesse');
        $last_text = __('Last', 'finesse');
        $page_text = __('Page', 'finesse');
        $of_text = __('of', 'finesse');
        $big = 999999999; // need an unlikely integer

        $paginate_links = paginate_links(array(
            'type' => 'array',
            'base' => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
            'format' => '?paged=%#%',
            'current' => max(1, get_query_var('paged')),
            'total' => $wp_query->max_num_pages,
            'prev_text' => __('&laquo; Previous', 'finesse'),
            'next_text' => __('Next &raquo;', 'finesse'),
        ));

        if ($max_num_pages == '') {
            global $wp_query;
            $max_num_pages = $wp_query->max_num_pages;
            if (!$max_num_pages) {
                $max_num_pages = 1;
            }
        }
        if (empty($paged)) {
            $paged = 1;
        }
        $prev_page = ($paged - 1 > 0) ? $paged - 1 : -1;
        $next_page = ($paged + 1 <= $max_num_pages) ? $paged + 1 : -1;

        if ($prev_page >= 0 || $next_page >= 0) {
            echo "<nav class=\"page-nav\">\n";
            echo "<span>$page_text $paged $of_text $max_num_pages</span>\n";
            echo "<ul>\n";
            echo "<li><a href=\"" . get_pagenum_link(1) . "\">&laquo; $first_text</a></li>\n";
            foreach ($paginate_links as $link) {
                $link = str_replace('<span', '<li', $link);
                $link = str_replace('</span>', '</li>', $link);
                $link = str_replace('<a', '<li><a', $link);
                $link = str_replace('</a>', '</a></li>', $link);
                echo $link;
            }
            echo "<li><a href=\"" . get_pagenum_link($max_num_pages) . "\">$last_text &raquo;</a></li>\n";
            echo "</ul>\n";
            echo "</nav>\n";
        }

    }
}
