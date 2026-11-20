<?php get_header(); ?>

<!-- begin content -->
<section id="content" class="container clearfix">
<!-- begin page header -->
<header id="page-header">
    <h1 id="page-title"><?php echo Post_Util::get_page_title_by_type('blog'); ?></h1>
</header>
<!-- end page header -->

<!-- begin main content -->
<section id="main" class="blog-entry-list three-fourths">
    <?php
    if (get_query_var('paged')) {
        $paged = get_query_var('paged');
    } elseif (get_query_var('page')) {
        $paged = get_query_var('page');
    } else {
        $paged = 1;
    }
    query_posts( array( 'post_type' => 'post', 'paged' => $paged ) ); ?>
    <?php if ( have_posts() ) { ?>

    <?php while ( have_posts() ) { the_post(); ?>
    <?php $page_title = Post_Util::get_page_title_by_format(); $page_format = Post_Util::get_post_format(); ?>
    <article id="post-<?php the_ID(); ?>" <?php post_class('entry clearfix') ?>>
        <?php
        $media_settings = apply_filters('finesse_blog_overview_media_settings', array('lightbox' => false));
        Page_Media_Manager::render_page_media($media_settings); ?>
        <?php if ( is_display_post_meta_enabled() ) { ?>
        <div class="entry-meta">
            <a href="<?php the_permalink(); ?>" class="post-format-wrap" title="<?php echo get_the_title(); ?>"><span class="post-format <?php echo $page_format; ?>"><?php _e('Permalink', 'finesse'); ?></span></a>
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
                <?php
                global $post;
                if (has_excerpt()) {
                    $read_more = apply_filters('finesse_post_ellipsis', '&hellip;');
                    echo do_shortcode(get_the_excerpt()).' '.$read_more;
                }elseif(preg_match('/<!--more(.*?)?-->/', $post->post_content, $matches)){
                    the_content(__('read more', 'finesse'));
                }else{
                    echo Post_Util::get_post_excerpt();
                } ?>
            </div>
        </div>
    </article>
    <?php } ?>

    <!-- begin pagination -->
    <?php do_finesse_pagination($wp_query->max_num_pages); wp_reset_query(); ?>
    <!-- end pagination -->

    <?php } else { ?>

    <article id="post-0" class="post no-results not-found entry clearfix">
        <div class="entry-body">
            <h2 class="entry-title"><?php _e('No Post Found', 'finessse'); ?></h2>
            <div class="entry-content">
                <p><?php _e('No results were found for the requested archive.', 'finesse' ); ?></p>
            </div>
        </div>
    </article>

    <?php } ?>

</section>
<!-- end main content -->

<!-- begin sidebar -->
<?php get_sidebar(); ?>
<!-- end sidebar -->
</section>
<!-- end content -->

<?php get_footer(); ?>