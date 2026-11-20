<?php
/**
 * Template Name: Front Page
 */
get_header(); ?>

    <!-- begin content -->
    <section id="content" class="container clearfix">
        <!-- begin slider -->
        <section id="slider-home">
        <?php
            $media_settings = apply_filters('finesse_front_page_media_settings', array());
            if(is_array($media_settings)){
                Page_Media_Manager::render_page_media($media_settings);
            }else{
                do_action('finesse_front_page_slider');
            } ?>
        </section>
        <!-- end slider -->

        <?php if ( have_posts() ) { while ( have_posts() ) { the_post(); ?>
        <?php the_content(); ?>
        <?php }}?>
    </section>
    <!-- end content -->

<?php get_footer(); ?>