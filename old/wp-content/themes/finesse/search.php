<?php
/*
Template Name: Search Page
*/
get_header(); ?>

<!-- begin content -->
<section id="content" class="container clearfix">
    <!-- begin page header -->
    <header id="page-header">
        <h1 id="page-title"><?php _e('Search Results', 'finesse'); ?></h1>
    </header>
    <!-- end page header -->

    <!-- begin main content -->
    <?php if ( have_posts() ) { ?>
    <p><?php global $wp_query; printf('%d ' . __('results found for', 'finesse') . ' &lsquo;%s&rsquo;.', $wp_query->found_posts, get_search_query()); ?></p>

    <!-- begin search results -->
    <ul id="search-results">
        <?php while ( have_posts() ) { the_post(); ?>
        <li>
            <h2><a href="<?php the_permalink(); ?>"><?php echo emphasize(get_the_title(), get_search_query());?></a></h2>
            <p><?php $read_more = apply_filters('finesse_search_result_ellipsis', '&hellip;');
                $excerpt_length = apply_filters('finesse_search_result_excerpt_length', 350);
                echo emphasize(shrink_starting_from(get_the_excerpt(), get_search_query(), $excerpt_length, $read_more), get_search_query());?></p>
            <p><a href="<?php the_permalink(); ?>"><?php the_permalink(); ?></a></p>
        </li>
        <?php } ?>
    </ul>
    <!-- end search results -->

    <!-- begin pagination -->
    <?php do_finesse_pagination($wp_query->max_num_pages); ?>
    <!-- end pagination -->
    <?php } else { ?>
    <p><?php _e('Sorry, no posts matched your criteria. Please try and search again.', 'finesse'); ?></p>
    <?php } ?>

    <!-- end main content -->
</section>
<!-- end content -->

<?php get_footer(); ?>