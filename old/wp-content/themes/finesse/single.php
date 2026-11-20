<?php get_header(); ?>

<!-- begin content -->
<section id="content" class="container clearfix">
<!-- begin page header -->
<header id="page-header">
    <h1 id="page-title"><?php _e('Blog', 'finesse'); ?></h1>
</header>
<!-- end page header -->

<!-- begin main content -->
<section id="main" class="blog-entry-single three-fourths">
<?php if ( have_posts() ) { while ( have_posts() ) { the_post(); ?>
<?php $page_title = Post_Util::get_page_title_by_format(null, false); $page_format = Post_Util::get_post_format(); ?>
<article id="post-<?php the_ID(); ?>" <?php post_class('entry clearfix') ?>>
    <?php
    $media_settings = apply_filters('finesse_blog_media_settings', array());
    Page_Media_Manager::render_page_media($media_settings); ?>

    <?php if ( is_display_post_meta_enabled() ) { ?>
    <div class="entry-meta">
        <span class="post-format <?php echo $page_format; ?>"><?php _e('Permalink', 'finesse'); ?></span>
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
            <?php the_content(); ?>
        </div>
    </div>
</article>

<!-- begin wp_link_pages -->
<?php $wp_link_pages = wp_link_pages(array(
    'echo' => '0',
    'before' => '<nav class="page-nav"><ul>',
    'after' => '</ul></nav>',
    'link_before' => '++',
    'link_after' => '--',
));
if(strlen($wp_link_pages) > 0){
    $wp_link_pages = str_replace('<a', '<li><a', $wp_link_pages);
    $wp_link_pages = str_replace('>++', '>', $wp_link_pages);
    $wp_link_pages = str_replace('--</a>', '</a></li>', $wp_link_pages);

    $wp_link_pages = str_replace('++', '<li class="current">', $wp_link_pages);
    $wp_link_pages = str_replace('--', '</li>', $wp_link_pages);
    echo $wp_link_pages;
}
?>
<!-- end wp_link_pages -->

<?php if (display_related_posts()) { ?>
<!-- begin related posts -->
<?php $before = '<section class="related-posts"><h3>'.__('Related Posts', 'finesse').'</h3>';
    $after = '</section>';
    $post_carousel_settings = apply_filters('finesse_blog_related_posts', array(
        'related_with_current_post' => 'true',
        'display' => 'all',
        'terms' => '',
        'count' => '',
        'scroll_count' => '3',
    ));
    $post_carousel_sc = do_shortcode('[post_carousel post_type="post" related_with_current_post="'.$post_carousel_settings['related_with_current_post'].'" display="'.$post_carousel_settings['display'].'" terms="'.$post_carousel_settings['terms'].'" count="'.$post_carousel_settings['count'].'" scroll_count="'.$post_carousel_settings['scroll_count'].'"][/post_carousel]');
    if(strlen($post_carousel_sc) > 0){
        echo $before.$post_carousel_sc.$after;
    } ?>
<!-- end related posts -->
<?php } ?>

<?php if ( is_display_post_comments_enabled() ) { ?>
<!-- begin comments -->
<?php comments_template('', true); ?>
<!-- end comments -->
<?php } ?>
<?php }} ?>
</section>
<!-- end main content -->

<!-- begin sidebar -->
<?php get_sidebar(); ?>
<!-- end sidebar -->
</section>
<!-- end content -->

<?php get_footer(); ?>