<!-- begin footer -->
<footer id="footer">
    <div class="container">
        <!-- begin footer top -->
        <?php if ( function_exists('dynamic_sidebar') && is_active_sidebar(FINESSE_SIDEBAR_FOOTER) ) {?>
        <div id="footer-top">
            <?php dynamic_sidebar(FINESSE_SIDEBAR_FOOTER); ?>
        </div>
        <?php } ?>
        <!-- end footer top -->

        <!-- begin footer bottom -->
        <div id="footer-bottom">
            <div class="one-half">
                <p><?php echo get_footer_text(); ?></p>
            </div>

            <div class="one-half column-last">
                <?php global $footer_menu_walker;
                    wp_nav_menu( array(
                        'theme_location' => 'footer',
                        'container' => 'nav',
                        'container_id' => 'footer-nav',
                        'container_class' => ' ',
                        'items_wrap' => '<ul>%3$s</ul>',
                        'depth' => '1',
                        'walker' => $footer_menu_walker ) );
                ?>
            </div>
        </div>
        <!-- end footer bottom -->
    </div>
</footer>
<!-- end footer -->
</div>
<!-- end container -->
<?php wp_footer(); ?>
<?php $tracking_code = get_tracking_code(); if ( $tracking_code ) { echo $tracking_code; } ?>
</body>
</html>