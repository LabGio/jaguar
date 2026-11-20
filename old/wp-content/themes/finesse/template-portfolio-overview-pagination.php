<?php
/**
 * Template Name: Portfolio Overview - Pagination
 */
get_header(); ?>

<!-- begin content -->
<section id="content" class="container clearfix">
    <!-- begin page header -->
    <header id="page-header">
        <h1 id="page-title"><?php the_title(); ?></h1>
    </header>
    <!-- end page header -->

    <!-- begin main content -->

    <!-- begin gallery -->
    <ul id="gallery" class="portfolio-grid clearfix">
        <?php
        if ( get_query_var('paged') ) {
            $paged = get_query_var('paged');
        } elseif ( get_query_var('page') ) {
            $paged = get_query_var('page');
        } else {
            $paged = 1;
        }
        wp_reset_query();
        $default_posts_per_page = get_option('posts_per_page');
        $posts_per_page = 4 * $default_posts_per_page;
        $query_args = apply_filters('finesse_portfolio_overview_query_args',
            array('post_type' => 'portfolio',
            'posts_per_page' => $posts_per_page,
            'order' => 'DESC',
            'paged' => $paged));
        query_posts($query_args);
        $i = 1;
        if (have_posts()) {
            while (have_posts()) {
                the_post();
                $data_id = 'id-'+$i;

                $terms = get_the_terms($post->ID, 'filter');
                $data_type = '';
                if($terms){
                    foreach ($terms as $term) {
                        $data_type .= ' ' . $term->slug;
                    }
                }
                $data_type = trim($data_type);

                $wrap_start = '<li data-id="'.$data_id.'" data-type="'.$data_type.'" class="entry one-fourth">';
                $wrap_end = '</li>';

                $settings = apply_filters('finesse_portfolio_overview_item_settings', array());
                echo Posts_Carousel_Shortcode::get_portfolio_carousel_item($wrap_start, $wrap_end, $settings);
                $i++;
            }
        }
        ?>
    </ul>
    <!-- end gallery -->

    <!-- begin pagination -->
    <?php do_finesse_pagination($wp_query->max_num_pages); wp_reset_query(); ?>
    <!-- end pagination -->

    <!-- end main content -->
</section>
<!-- end content -->

<?php get_footer(); ?>
