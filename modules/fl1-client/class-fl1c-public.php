<?php
/**
 * FL1C Public
 *
 * Class in charge of FL1C Public facing side
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class FL1C_Public {

    public function __construct() {

        add_filter('single_template', array($this, 'singles'));

    }

    /**
     * page_template filter function
     * 
     * @param string $template
     */
    public function singles($template) {
    
        global $post;

        if($post->post_type === 'development') {
            $template = FL1C_PATH . 'templates/development/single-development.php';
        }

        if($post->post_type === 'location') {
            $template = FL1C_PATH . 'templates/location/single-location.php';
        }

        return $template;
    
    }

}

