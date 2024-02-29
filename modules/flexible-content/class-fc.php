<?php
/**
 * Flexible_Content Init
 *
 * Class in charge of initialising everything Flexible_Content
 */

 // Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class Flexible_Content {

    public function __construct() {

        $this->define_constants();

        add_filter(FL1_SLUG.'_load_dependencies', array($this, 'load_dependencies'));
        add_action(FL1_SLUG.'_init', array($this, 'init'));
        add_action(FL1_SLUG.'_setup_theme',	array($this, 'setup_theme'));
        add_action(FL1_SLUG.'_acf_init', array($this, 'acf_init'));

    }

    /**
     * Setup constants.
     *
     * @access private
     * @since 1.0
     * @return void
     */
    private function define_constants() {

        define('FC_NAME', 'Flexible Content');
        define('FC_VERSION', '2.0');
        define('FC_SLUG', 'fc');
        define('FC_PLUGIN_FOLDER', 'flexible-content');
        define('FC_PATH', FL1_PATH.'/modules/'.FC_PLUGIN_FOLDER.'/');
        define('FC_URL', FL1_URL.'/modules/'.FC_PLUGIN_FOLDER.'/');

    }
    
    /**
     * Loads all dependencies.
     *
     * @access public
     * @since 1.0
     * @return void
     */
    public function load_dependencies($deps) {

        $deps[] = FC_PATH. 'class-fc-cpt.php';
        $deps[] = FC_PATH. 'class-fc-public.php';
        $deps[] = FC_PATH. 'class-fc-helpers.php';

        return $deps;

    }

    public function init() {
        
        new FC_Public();

    }

    public function setup_theme() {

        new FC_CPT();

    }

	public function acf_init() {

        if(function_exists('acf_add_options_sub_page')) {
        
            acf_add_options_sub_page(array(
                'page_title'  => 'FC Settings',
                'menu_title'  => 'FC Settings',
                'menu_slug' => 'fc-settings',
                'parent_slug' => FC_SLUG,
            ));

        }

    }

}

// Release the Kraken!
new Flexible_Content();

