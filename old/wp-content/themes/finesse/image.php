<?php get_header(); ?>
<?php if (have_posts()) { while (have_posts()) { the_post(); ?>
        <!-- begin content -->
        <section id="content" class="container clearfix">

            <header id="page-header">
                <h1 id="page-title"><?php the_title(); ?></h1>
            </header>

            <!-- begin main content -->
            <section id="main" class="blog-entry-single">
                <article id="post-<?php the_ID(); ?>" <?php post_class('entry clearfix') ?>>
                    <div class="entry-meta">
                        <span><span class="title"><?php _e('Posted', 'finesse'); ?>
                                :</span> <?php the_time(get_option('date_format')); ?> <?php _e('by', 'finesse'); ?> <a
                                href="<?php echo get_author_posts_url(get_the_author_meta('ID')); ?>"><?php the_author(); ?></a></span>
                        <span><span class="title"><?php _e('Tags', 'finesse'); ?>
                                :</span> <?php the_tags('', ', ', ''); ?></span>
                    </div>
                    <div class="entry-body">
                        <div class="entry-content">
                            <?php
                            $next_attachment_url = wp_get_attachment_url();
                            $post = get_post();
                            $attachment_ids = get_posts(array(
                                'post_parent' => $post->post_parent,
                                'fields' => 'ids',
                                'numberposts' => -1,
                                'post_status' => 'inherit',
                                'post_type' => 'attachment',
                                'post_mime_type' => 'image',
                                'order' => 'ASC',
                                'orderby' => 'menu_order ID'
                            ));


                            if (count($attachment_ids) > 1) {
                                $next_attachment_url = '';
                                $prev_attachment_url = '';
                                $current_index = 0;
                                foreach ($attachment_ids as $index => $attachment_id) {
                                    if ($attachment_id == $post->ID) {
                                        $current_index = $index;
                                        break;
                                    }
                                }
                                if ($current_index - 1 >= 0) {
                                    $prev_attachment_url = get_attachment_link($attachment_ids[$current_index - 1]);
                                }
                                if ($current_index + 1 < count($attachment_ids)) {
                                    $next_attachment_url = get_attachment_link($attachment_ids[$current_index + 1]);
                                }

                                echo '<div>';
                                if (!empty($prev_attachment_url)) {
                                    echo '<a href="' . $prev_attachment_url . '" class="button">'.__('Previous Image', 'finesse').'</a>';
                                }
                                if (!empty($next_attachment_url)) {
                                    echo '<a href="' . $next_attachment_url . '" class="button">'.__('Next Image', 'finesse').'</a>';
                                }
                                echo '</div>';
                            } ?>
                            <?php if (has_excerpt()) { ?>
                                <div class="caption">
                                    <div class="entry-image"><a target="_blank" href="<?php echo wp_get_attachment_url($post->ID); ?>"><?php echo wp_get_attachment_image($post->ID, 'full'); ?></a></div>
                                    <p class="caption-text"><?php the_excerpt(); ?></p>
                                </div>
                            <?php } else { ?>
                                <div class="entry-image"><a target="_blank" href="<?php echo wp_get_attachment_url($post->ID); ?>"><?php echo wp_get_attachment_image($post->ID, 'full'); ?></a></div>
                            <?php } ?>
                            <?php if (!empty($post->post_content)) { ?>
                                <div class="entry-description">
                                    <?php the_content(); ?>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </article>
            </section>
            <!-- end main content -->

        </section>
        <!-- end content -->
    <?php }} ?>
<?php get_footer(); ?>