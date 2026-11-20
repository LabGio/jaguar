<?php
/**
 * Template Name: Contact Page
 */
get_header(); ?>

<!-- begin content -->
<section id="content" class="container clearfix">
    <!-- begin page header -->
    <header id="page-header">
        <h1 id="page-title"><?php _e('Contact', 'finesse'); ?></h1>
    </header>
    <!-- end page header -->

    <!-- begin main content -->

    <!-- begin google map -->
    <?php Contact_Map_Manager::render_contact_map() ?>
    <!-- end google map -->

    <?php if (have_posts()) { while (have_posts()) { the_post(); ?>
    <!-- begin main -->
    <section id="main" class="three-fourths">
        <?php global $post; if (empty($post->post_content)) { ?>
        <!-- begin contact form -->
        <h2><?php _e('Contact Us', 'finesse'); ?></h2>
        <p><?php _e('We would be glad to have feedback from you. Drop us a line, whether it is a comment, a question, a work proposition or just a hello. You can use either the form below or the contact details on the right.', 'finesse'); ?></p>
        <div id="contact-form-success-msg" class="notification-box notification-box-success" style="display: none;">
            <p id="contact-form-success-msg-p"><?php _e('Your message has been successfully sent. We will get back to you as soon as possible.', 'finesse'); ?></p>
            <a href="#" class="notification-close notification-close-success">x</a>
        </div>

        <div id="contact-form-error-msg" class="notification-box notification-box-error " style="display: none;">
            <p id="contact-form-error-msg-p"><?php _e('Your message couldn\'t be sent because a server error occurred. Please try again.', 'finesse'); ?></p>
            <a href="#" class="notification-close notification-close-error">x</a>
        </div>
        <form id="contact-form" class="content-form" method="post" action="<?php echo site_url('wp-admin/admin-ajax.php'); ?>">
            <p>
                <label for="name"><?php _e('Name', 'finesse'); ?>:<span class="note">*</span></label>
                <input id="name" type="text" name="name" class="required">
            </p>
            <p>
                <label for="email"><?php _e('Email', 'finesse'); ?>:<span class="note">*</span></label>
                <input id="email" type="email" name="email" class="required">
            </p>
            <p>
                <label for="url"><?php _e('Website', 'finesse'); ?>:</label>
                <input id="url" type="url" name="url">
            </p>
            <p>
                <label for="subject"><?php _e('Subject', 'finesse'); ?>:<span class="note">*</span></label>
                <input id="subject" type="text" name="subject" class="required">
            </p>
            <p>
                <label for="message"><?php _e('Message', 'finesse'); ?>:<span class="note">*</span></label>
                <textarea id="message" cols="68" rows="8" name="message" class="required"></textarea>
            </p>
            <?php if(is_captcha_form_enabled()) { ?>
            <p>
                <?php echo do_shortcode('[captcha][/captcha]'); ?>
            </p>
            <? } ?>
            <p>
                <input type="hidden" name="ua" value="process_contact_form">
                <input id="submit-contact" class="button" type="submit" name="submit" value="<?php _e('Send Message', 'finesse'); ?>">
            </p>
        </form>
        <p><span class="note">*</span> <?php _e('Required fields', 'finesse'); ?></p>
        <script type="text/javascript">
            if(!document['formsSettings']){
                document['formsSettings'] = [];
            }
            document['formsSettings'].push({
                submitButtonId: 'submit-contact',
                action: 'finesse_process_form',
                successBoxId: 'contact-form-success-msg',
                errorBoxId: 'contact-form-error-msg'
            });
		</script>
        <!-- end contact form -->
        <?php } else{ ?>
        <?php the_content(); ?>
        <?php } ?>
    </section>
    <!-- end main -->
    <?php }} ?>

    <!-- begin sidebar -->
    <?php get_sidebar(); ?>
    <!-- end sidebar -->

    <!-- end main content -->
</section>
<!-- end content -->

<?php get_footer(); ?>