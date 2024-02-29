<?php
/**
 * FL1 ACF
 *
 * ACF hooks and filters
 *
 * @author  fl1
 * @link    http://fl1.digital
 * @version 1.0
 */

 // Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class FL1_ACF {

    /**
     * Initialize hooks on load.
     *
     * @since 1.0
     * @return void
     */
    public function __construct() {

        $this->apf_acf_init();

    }

    /**
     * ACF init.
     */
    public function apf_acf_init() {
    
        if( function_exists('acf_add_options_page') ) {
            acf_add_options_sub_page(array(
                'page_title'  => 'Header Options',
                'menu_title'  => 'Header',
                'parent_slug' => 'themes.php'
            ));
    
            acf_add_options_sub_page(array(
                'page_title'  => 'Footer Options',
                'menu_title'  => 'Footer',
                'parent_slug' => 'themes.php',
            ));

			acf_add_options_sub_page(array(
                'page_title'  => 'Theme Settings',
                'menu_title'  => 'Theme Settings',
                'parent_slug' => 'themes.php',
            ));
        }
    
    }

}