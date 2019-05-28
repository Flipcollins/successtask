<?php

/**
 * @package MetaBoxClass
 */
/*
Plugin Name: Custom Meta Box and Rewrite
Plugin URI: https://successagency.com
Description: A Custom Meta box and a Rewrite EndPoint
Version: 1.0.0
Author: Farai Mrape
Author URI: https://www.linkedin.com/in/farai-mrape/
License: GPL2
 */

require ('inc/WriteEndpointFah.php');
if (!defined('ABSPATH')) {
    die;
}

//Create Class Custom Metabox
class MetaBoxClass extends WriteEndpointFah
{

    //Hook actions that require construct
    public function __construct()
    {

        add_action('add_meta_boxes', array($this, 'success_add_meta_box'));
        add_action('save_post', array($this, 'save'));
        add_action('the_content', array($this, 'custom_message'));
        $this->success_construct();
    }

    //Add your metabox field
    public function success_add_meta_box($post_type)
    {
        $post_types = array('post', 'page');

        //limit meta box to certain post types
        if (in_array($post_type, $post_types)) {
            add_meta_box('cs-meta',
                'Add Custom Message',
                array($this, 'success_meta_box_function'),
                $post_type,
                'normal',
                'high');
        }
    }

    //Display metabox
    public function success_meta_box_function($post)
    {

        // Add an nonce field so we can check for it later.
        wp_nonce_field('cs_nonce_check', 'cs_nonce_check_value');

        // Use get_post_meta to retrieve an existing value from the database.
        $custom_message = get_post_meta($post->ID, '_cs_custom_message', true);

        // Display the form, using the current value.
        echo '<div style="margin: 10px 100px; text-align: center">';
        echo '<label for="custom_message">';
        echo '<strong><p>Add custom message</p></strong>';
        echo '</label>';
        echo '<textarea rows="3" cols="50" name="cs_custom_message">';
        echo esc_attr($custom_message);
        echo '</textarea>';
        echo '</div>';
    }

    public function save($post_id)
    {


        // Check if our nonce is set.
        if (!isset($_POST['cs_nonce_check_value']))
            return $post_id;

        $nonce = $_POST['cs_nonce_check_value'];

        // Verify nonce validity
        if (!wp_verify_nonce($nonce, 'cs_nonce_check'))
            return $post_id;

       //handle autosave
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
            return $post_id;

        // Verify user permissions
        if ('page' == $_POST['post_type']) {

            if (!current_user_can('edit_page', $post_id))
                return $post_id;

        } else {

            if (!current_user_can('edit_post', $post_id))
                return $post_id;
        }


        // Sanitize the user input and save
        $data = sanitize_text_field($_POST['cs_custom_message']);

        // Update the meta field.
        update_post_meta($post_id, '_cs_custom_message', $data);
    }

    public function custom_message($content)
    {
        global $post;
        //display metadata
        $data = get_post_meta($post->ID, '_cs_custom_message', true);
        if (!empty($data)) {
            $custom_message = "<div style='background-color: #FFEBE8;border-color: #C00;padding: 2px;margin:2px;font-weight:bold;text-align:center'>";
            $custom_message .= $data;
            $custom_message .= "</div>";
            $content = $custom_message . $content;
        }

        return $content;
    }

}

//instantiate custom meta box class
if (class_exists('MetaBoxClass')) {

    new MetaBoxClass;

}
