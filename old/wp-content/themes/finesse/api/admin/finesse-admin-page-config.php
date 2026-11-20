<?php

$pages_customizer = array();
array_push($pages_customizer, new Post_Page_Customizer());
array_push($pages_customizer, new Simple_Page_Customizer());
array_push($pages_customizer, new Portfolio_Page_Customizer());

$meta_boxes = array();

$meta_boxes[] = array(
    'id' => 'meta_settings',
    'title' => __('Meta Info', 'finesse'),
    'location' => array(),
    'context' => 'advanced',
    'priority' => 'default',

    'fields' => array(
        array(
            'name' => __('Description', 'finesse'),
            'id' => 'finesse_page_description',
            'type' => 'textarea',
            'desc' => 'Enter a short description for this page or post.'
        ),
        array(
            'name' => __('Keywords', 'finesse'),
            'id' => 'finesse_page_keywords',
            'type' => 'text',
            'desc' => 'Enter some keywords separated by comma for this page or post.'
        )
    )
);

$meta_boxes[] = array(
    'id' => 'sidebar_settings',
    'title' => __('Sidebar', 'finesse'),
    'location' => array(),
    'context' => 'side',
    'priority' => 'high',
    'fields' => array(
        array('id' => 'finesse_sidebar_name')
    ),
);

$meta_boxes[] = array(
    'id' => 'project_details',
    'title' => __('Project Details', 'finesse'),
    'location' => array(),
    'context' => 'advanced',
    'priority' => 'default',

    'fields' => array(
        array(
            'name' => __('Short Description', 'finesse'),
            'id' => 'finesse_project_short_description',
            'type' => 'text',
            'desc' => 'Enter a small description of this project.'
        )
    , array(
            'name' => __('Description', 'finesse'),
            'id' => 'finesse_project_description',
            'type' => 'textarea',
            'desc' => 'Enter the fill description of this project.'
        ),
        array(
            'name' => __('Customer', 'finesse'),
            'id' => 'finesse_project_customer',
            'type' => 'text',
            'desc' => 'Enter the name of the project customer.'
        ),
        array(
            'name' => __('Year', 'finesse'),
            'id' => 'finesse_project_year',
            'type' => 'text',
            'desc' => 'Enter the year of the project creation.'
        ),
        array(
            'name' => __('Technologies Used', 'finesse'),
            'id' => 'finesse_project_technologies',
            'type' => 'textarea',
            'desc' => 'Add the technologies used in this project. Enter one per line.'
        ),
        array(
            'name' => __('Project URL', 'finesse'),
            'id' => 'finesse_project_url',
            'type' => 'text',
            'desc' => 'Enter the URL of this project.'
        )
    )
);

add_action('admin_init', 'finesse_customize_pages');
function finesse_customize_pages()
{
    global $meta_boxes;
    global $pages_customizer;
    foreach ($meta_boxes as $meta_box) {
        foreach ($pages_customizer as $page_customizer) {
            $page_customizer->visit($meta_box);
        }
        new X_Meta_Box($meta_box);
    }
}

add_action('load-post.php', 'finesse_set_page_post_formats');
add_action('load-post-new.php', 'finesse_set_page_post_formats');
function finesse_set_page_post_formats()
{
    if (isset($_GET['post'])) {
        $post = get_post($_GET['post']);
        if ($post) {
            $post_type = $post->post_type;
        } else {
            return;
        }
    } else {
        $post_type = isset($_GET['post_type']) ? $_GET['post_type'] : 'post';
    }

    if ('portfolio' == $post_type) {
        add_theme_support('post-formats', array('video'));
    } elseif ('post' == $post_type) {
        add_theme_support('post-formats', array('image', 'gallery', 'audio', 'video', 'aside', 'quote', 'link'));
    }
}
