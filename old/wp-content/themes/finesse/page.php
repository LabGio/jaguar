<?php get_header(); ?>

<!-- begin content -->
<section id="content" class="container clearfix">
    <?php if ( have_posts() ) { while ( have_posts() ) { the_post(); ?>
    <!-- begin page header -->
    <header id="page-header">
        <h1 id="page-title"><?php the_title(); ?></h1>
    </header>
    <!-- end page header -->

    <!-- begin main content -->
    <section id="main" class="three-fourths">
        <?php the_content(); ?>
    </section>
    <!-- end main content -->
    <?php }} ?>
    <!-- begin sidebar -->
    <?php get_sidebar(); ?>
    <!-- end sidebar -->
</section>
<!-- end content -->

<?php get_footer(); ?>