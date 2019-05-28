<?php

add_action('wp_enqueue__scripts', 'success_child_styles');

function success_child_styles()
{
    wp_enqueue_style('parent-style', get_template_directory_uri() . 'style.css');

}
function add_success_cpt_query($query)
{
    if (is_home() && $query->is_main_query()) {
        $query->set('post_type', array('post', 'success', 'book'));
        return $query;
    }

}
add_action('pre_get_posts', 'add_success_cpt_query');