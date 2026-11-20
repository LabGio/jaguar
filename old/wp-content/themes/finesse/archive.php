<?php get_header(); ?>

<!-- begin content -->
<section id="content" class="container clearfix">
    <!-- begin page header -->
    <header id="page-header">
        <h1 id="page-title"><?php echo Post_Util::get_page_title_by_type('archive'); ?></h1>
    </header>
    <!-- end page header -->

    <!-- begin main content -->
    <section id="main" class="blog-entry-list three-fourths">
        <?php if ( have_posts() ) { while ( have_posts() ) { the_post(); ?>
        <?php $page_title = Post_Util::get_page_title_by_format(); $page_format = Post_Util::get_post_format(); ?>
        <article class="entry clearfix">
            <?php
            $media_settings = apply_filters('finesse_blog_overview_media_settings', array('lightbox' => false));
            $media_settings = apply_filters('finesse_blog_archive_media_settings', $media_settings);
            Page_Media_Manager::render_page_media($media_settings); ?>
            <?php if ( is_display_post_meta_enabled() ) { ?>
            <div class="entry-meta">
                <a href="<?php the_permalink(); ?>" class="post-format-wrap" title="<?php the_title(); ?>"><span class="post-format <?php echo $page_format; ?>"><?php _e('Permalink', 'finesse'); ?></span></a>
                <span><span class="title"><?php _e('Posted', 'finesse'); ?>:</span> <?php the_time(get_option('date_format')); ?> <?php _e('by', 'finesse'); ?> <a href="<?php echo get_author_posts_url(get_the_author_meta('ID')); ?>"><?php the_author(); ?></a></span>
                <span><span class="title"><?php _e('Tags', 'finesse'); ?>:</span> <?php the_tags('', ', ', '' ); ?></span>
                <span><span class="title"><?php _e('Comments', 'finesse'); ?>:</span> <?php comments_popup_link('0', '1', '%'); ?></span>
            </div>
            <?php } ?>
            <div class="entry-body">
                <?php if (!empty($page_title)) { ?>
                <h2 class="entry-title"><?php echo $page_title; ?></h2>
                <?php } ?>
                <div class="entry-content">
                    <?php echo Post_Util::get_post_excerpt(); ?>
                </div>
            </div>
        </article>
        <?php }} ?>

        <!-- begin pagination -->
        <?php do_finesse_pagination($wp_query->max_num_pages); ?>
        <!-- end pagination -->
    </section>
    <!-- end main content -->

    <!-- begin sidebar -->
    <?php get_sidebar(); ?>
    <!-- end sidebar -->
</section>
<!-- end content -->

<?php get_footer(); ?>