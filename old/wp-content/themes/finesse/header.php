<?php include_once 'api/finesse-util.php'; ?>
<!DOCTYPE HTML>
<!--[if IE 8]> <html class="ie8 no-js" <?php language_attributes(); ?>> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--> <html class="no-js" <?php language_attributes(); ?>> <!--<![endif]-->
<head>
    <!-- begin meta -->
    <meta charset="<?php bloginfo( 'charset' ); ?>" />
    <meta http-equiv="X-UA-Compatible" content="IE=8, IE=9, IE=10" >
    <?php if (is_custom_seo_enabled()) { ?>
    <meta name="description" content="<?php echo get_page_description();?>">
    <meta name="keywords" content="<?php echo get_page_keywords();?>">
    <meta name="author" content="<?php echo fin_get_page_author();?>">
    <?php } ?>
    <?php if (is_responsive_enabled()) { ?>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <?php } ?>
    <!-- end meta -->

    <link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
    <link href="<?php echo get_fav_icon(); ?>" type="image/x-icon" rel="shortcut icon">
    <title><?php bloginfo( 'name' ); wp_title('-', true, 'left');
        $site_description = get_bloginfo( 'description', 'display' );
        if ( $site_description && ( is_home() || is_front_page() ) )
            echo " - $site_description";?></title>

    <!-- begin JS & CSS -->
    <?php wp_head(); ?>
    <!-- end JS & CSS -->

    <?php if (is_user_logged_in()) { ?>
    <style type="text/css" media="screen">
        html { margin-top: 0 !important; }
        * html body { margin-top: 0 !important; }
    </style>
    <?php } ?>
</head>

<body <?php body_class(); ?>>
<!-- begin container -->
<div id="wrap">
    <!-- begin header -->
    <header id="header" class="container">
        <!-- begin header top -->
        <section id="header-top" class="clearfix">
            <!-- begin header left -->
            <div class="one-half">
                <h1 id="logo"><a href="<?php echo get_home_url(); ?>"><img src="<?php echo get_header_logo(); ?>" alt="<?php echo get_bloginfo('name');?>"></a></h1>
                <p id="tagline"><?php echo get_tagline(); ?></p>
            </div>
            <!-- end header left -->

            <!-- begin header right -->
            <div class="one-half column-last">
                <?php if(has_header_custom_code()) { ?>
                    <?php echo get_header_custom_code();?>
                <?php } else { ?>
                    <!-- begin language switcher -->
                    <?php print_language_switcher();?>
                    <!-- end language switcher -->

                    <?php if(is_contact_details_displayed_in_header_enabled()) { ?>
                    <!-- begin contact info -->
                    <div class="contact-info">
                        <?php $contact_phone = get_contact_phone(); if(strlen($contact_phone)>0) { ?>
                        <p class="phone"><?php echo $contact_phone; ?></p>
                        <?php }?>
                        <?php $contact_email = get_contact_email(); if(strlen($contact_email)>0) { ?>
                        <p class="email"><a href="mailto:<?php echo $contact_email; ?>"><?php echo $contact_email; ?></a></p>
                        <?php } ?>
                    </div>
                    <!-- end contact info -->
                    <?php }?>
                <?php } ?>
            </div>
            <!-- end header right -->
        </section>
        <!-- end header top -->

        <!-- begin navigation bar -->
        <section id="navbar" class="clearfix">
            <!-- begin navigation -->
            <?php global $header_menu_walker;
                    wp_nav_menu( array(
                        'theme_location' => 'primary',
                        'container' => 'nav',
                        'container_id' => 'nav',
                        'container_class' => ' ',
                        'items_wrap' => '<ul id="navlist" class="clearfix">%3$s</ul>',
                        'walker' => $header_menu_walker ) );
            ?>
            <!-- end navigation -->

            <?php if ( is_search_header_displayed() ) { ?>
            <!-- begin search form -->
            <form id="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>" method="get">
                <input id="fin-s" type="text" name="s" placeholder="<?php _e('Search', 'finesse') ?> &hellip;" style="display: none;">
                <input id="search-submit" type="submit" name="search-submit" value="<?php _e('Search', 'finesse') ?>">
            </form>
            <!-- end search form -->
            <?php } ?>
        </section>
        <!-- end navigation bar -->

    </header>
    <!-- end header -->
