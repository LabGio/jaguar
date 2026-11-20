<?php if ( post_password_required() ) { ?>
<section id="comments">
    <p><?php _e('This post is password protected. Enter the password to view any comments.', 'finesse'); ?></p>
</section>
<?php return;/* Stop the rest of comments.php from being processed, but don't kill the script entirely.*/ ?>
<?php } ?>

<?php if ( have_comments() ) { ?>
<!-- begin comments -->
<section id="comments">
    <!-- begin comments header -->
    <h3><?php printf( _n('1 Comment', '%1$s Comments', get_comments_number(), 'finesse'), number_format_i18n( get_comments_number() )); ?></h3>
    <!-- end comments header -->

    <!-- begin comment list -->
    <ol class="comment-list">
        <?php $GLOBALS['comment_index']=1; wp_list_comments( array('callback' => 'print_finesse_comments', 'style' => 'ol') ); ?>
    </ol>

    <?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) { // are there comments to navigate through ?>
    <nav id="comment-nav-below" class="navigation page-nav" role="navigation">
        <ul>
            <li class="nav-previous"><?php previous_comments_link( __( '&larr; Older Comments', 'finesse' ) ); ?></li>
            <li class="nav-next"><?php next_comments_link( __( 'Newer Comments &rarr;', 'finesse' ) ); ?></li>
        </ul>
    </nav>
    <?php } // check for comment navigation ?>
    <!-- end comment list -->
</section>
<!-- end comments -->
<?php } ?>

<!-- begin leave comment -->
<?php if (comments_open(get_the_ID())) { comment_form(array(), get_the_ID()); } ?>
<!-- end leave comment -->