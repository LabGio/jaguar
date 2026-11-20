<?php
/**
 * Template Name: Portfolio Overview
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

    <!-- begin filter -->
    <div id="filter">
        <span><?php _e('Filter', 'finesse'); ?>:</span>
        <ul>
            <li class="active"><a href="#" class="all"><?php _e("All", "finesse"); ?></a></li>
            <?php
            $filter_args = apply_filters('finesse_portfolio_overview_filter_args',
                array('taxonomy' => 'filter', 'orderby' => 'name', 'order' => 'desc'));
            $categories = get_categories($filter_args);
            foreach ($categories as $cat) {
                echo '<li><a href="#' . $cat->slug . '" class="' . $cat->category_nicename . '">'.___($cat->cat_name).'</a></li>';
            } ?>
        </ul>
    </div>
    <!-- end filter -->

    <!-- begin gallery -->
    <ul id="gallery" class="portfolio-grid clearfix">
        <?php
            $query_args = apply_filters('finesse_portfolio_overview_query_args',
                array('post_type' => 'portfolio', 'posts_per_page' => -1, 'order' => 'DESC'));
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
            wp_reset_query();
        ?>
    </ul>
    <!-- end gallery -->

    <!-- end main content -->
</section>
<!-- end content -->

<?php get_footer(); ?>
