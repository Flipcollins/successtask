<?php
class WriteEndpointFah
{
    public function success_construct()
    {
        add_action('init', array($this, 'success_endpoints_add_endpoint'));
        add_action('template_redirect', array($this, 'success_endpoints_template_redirect'));

    }

    public function success_endpoints_add_endpoint()
    {
        // register a rewrite endpoint
        add_rewrite_endpoint('arbitrary', EP_PERMALINK | EP_PAGES);
    }

    public function success_endpoints_template_redirect()
    {
        global $wp_query;
        // exit if not JSON request
        if (!isset($wp_query->query_vars['arbitrary']) || !is_singular())
            return;
        // print output
        $this->success_endpoints_do_json();


        exit;
    }

    public function success_endpoints_do_json()
    {
        header('Content-Type: application/json');
        $post = get_queried_object();
        echo json_encode($post);
    }


    public function success_endpoints_activate()
    {
        //flushing rewrite rules after adding endpoint
        $this->success_endpoints_add_endpoint();

        // flush rewrite rules
        flush_rewrite_rules();
    }

    public function success_endpoints_deactivate()
    {
        // flush rules on deactivate
        flush_rewrite_rules();
    }

}

//Instantiate object of class if it does not exist
if (class_exists('WriteEndpointFah')) {
    $writeendpointfah = new WriteEndpointFah();
}

//Register activation hook
register_activation_hook(__FILE__, array($writeendpointfah, 'success_endpoints_activate'));

//Deactivate hook
register_deactivation_hook(__FILE__, array($writeendpointfah, 'success_endpoints_deactivate'));