<?php
/**
 * Template Name: Portfolio Overview
 */
get_header(); ?>
<?php
add_filter('the_title', 'finesse_portfolio_category_title');
if (!function_exists('finesse_portfolio_category_title')) {
    function finesse_portfolio_category_title()
    {
        return sprintf(__('Portfolio Category: %s', 'finesse'), single_term_title( '', false ) );
    }
}
?>

<!-- begin content -->
<section id="content" class="container clearfix">
    <!-- begin page header -->
    <header id="page-header">
        <h1 id="page-title"><?php the_title(); remove_filter('the_title', 'finesse_portfolio_category_title'); ?></h1>
    </header>
    <!-- end page header -->

    <!-- begin main content -->

    <!-- begin gallery -->
    <ul id="gallery" class="portfolio-grid clearfix">
        <?php $i = 1;
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
