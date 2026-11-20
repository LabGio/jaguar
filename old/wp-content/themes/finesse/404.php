<?php get_header(); ?>

<!-- begin content -->
<section id="content" class="container clearfix">
    <!-- begin page header -->
    <header id="page-header">
        <h1 id="page-title"><?php _e('404 Error Page', 'finesse'); ?></h1>
    </header>
    <!-- end page header -->

    <!-- begin main content -->
    <p><?php _e('It seems that the page you were looking for doesn\'t exist.', 'finesse'); ?></p>
    <p><?php _e('Perhaps one of the links below can help.', 'finesse'); ?></p>
    <?php global $simple_menu_walker;
    wp_nav_menu( array(
        'theme_location' => 'primary',
        'container' => '',
        'items_wrap' => '<ul class="arrow">%3$s</ul>',
        'depth' => '1',
        'walker' => $simple_menu_walker));
    ?>
    <!-- end main content -->
</section>
<!-- end content -->

<?php get_footer(); ?>