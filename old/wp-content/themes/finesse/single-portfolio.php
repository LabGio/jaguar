<?php get_header(); ?>

<!-- begin content -->
<section id="content" class="container clearfix">
    <?php if ( have_posts() ) { while ( have_posts() ) { the_post(); ?>
    <!-- begin page header -->
    <header id="page-header">
        <h1 id="page-title"><?php echo get_the_title(); ?></h1>
    </header>
    <!-- end page header -->

    <!-- begin main content -->

    <!-- begin project -->
    <section>
        <!-- begin project media -->
        <div class="three-fourths">
            <?php
            $media_settings = apply_filters('finesse_portfolio_media_settings', array());
            Page_Media_Manager::render_page_media($media_settings); ?>
            <?php the_content(); ?>
        </div>
        <!-- end project media -->

        <!-- begin project description -->
        <div class="one-fourth column-last">
            <?php $post_id = get_the_ID();
            $project_description = get_post_meta($post_id, "finesse_project_description", true);
            $project_customer = get_post_meta($post_id, "finesse_project_customer", true);
            $project_year = get_post_meta($post_id, "finesse_project_year", true);
            $project_technologies = get_post_meta($post_id, "finesse_project_technologies", true);
            $project_url = get_post_meta($post_id, "finesse_project_url", true);
            ?>

            <?php if (isset($project_description) && strlen($project_description) > 0) { ?>
            <h3><?php _e('Overview', 'finesse'); ?></h3>
            <?php foreach (explode("\n", $project_description ) as $line) { ?>
                 <p><?php echo ___($line); ?></p>
            <?php }} ?>

            <?php if (isset($project_customer) && strlen($project_customer) > 0) { ?>
            <h3><?php _e('Customer', 'finesse'); ?></h3>
            <?php if (start_with($project_customer, 'http://') || start_with($project_customer, 'https://')) { ?>
            <p><img class="client-logo" src="<?php echo ___($project_customer); ?>"></p>
            <?php } else { ?>
            <p>&mdash; <?php echo ___($project_customer); ?></p>
            <?php }}?>

            <?php if (isset($project_year) && strlen($project_year) > 0) { ?>
            <h3><?php _e('Year', 'finesse'); ?></h3>
            <p>&mdash; <?php echo ___($project_year); ?></p>
            <?php }?>

            <?php if (isset($project_technologies) && strlen($project_technologies) > 0) { ?>
            <h3><?php _e('Technology', 'finesse'); ?></h3>
            <ul class="check">
                <?php foreach (explode("\n", $project_technologies) as $technology) { ?>
                <li><?php echo ___($technology); ?></li>
                <?php } ?>
            </ul>
            <?php }?>
            <?php if (isset($project_url) && strlen($project_url) > 0) {
            echo do_shortcode('[btn href="' . $project_url . '"]' . __('Visit Website', 'finesse') . '[/btn]');
        } ?>
        </div>
        <!-- end project description -->
        <div class="clear"></div>
    </section>
    <!-- end project -->

    <?php if(display_portfolio_related_posts()){ ?>
    <!-- begin related projects -->
    <h2><?php _e('Related Projects', 'finesse'); ?></h2>
    <?php $post_carousel_settings = apply_filters('finesse_portfolio_related_projects', array(
                'related_with_current_post' => 'true',
                'display' => 'all',
                'terms' => '',
                'count' => '',
                'scroll_count' => '',
            ));
            echo do_shortcode('[post_carousel post_type="portfolio" related_with_current_post="'.$post_carousel_settings['related_with_current_post'].'" display="'.$post_carousel_settings['display'].'" terms="'.$post_carousel_settings['terms'].'" count="'.$post_carousel_settings['count'].'" scroll_count="'.$post_carousel_settings['scroll_count'].'"][/post_carousel]'); ?>
    <!-- end related projects -->
    <?php } ?>

    <?php }} ?>
    <!-- end main content -->
</section>
<!-- end content -->


<?php get_footer(); ?>