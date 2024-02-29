<?php
/**
 * FL1_Gravity_Forms Init
 *
 * Class in charge of initialising everything FL1_Gravity_Forms
 */

 // Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class FL1_Gravity_Forms {

    public function __construct() {

        $this->define_constants();

        add_filter(FL1_SLUG.'_load_dependencies', array($this, 'load_dependencies'));
        add_action(FL1_SLUG.'_init', array($this, 'init'));

    }

    /**
     * Setup constants.
     *
     * @access private
     * @since 1.0
     * @return void
     */
    private function define_constants() {

        define('GF_NAME', 'FL1 Gravity Forms');
        define('GF_VERSION', '2.0');
        define('GF_SLUG', 'fl1_gf');
        define('GF_PLUGIN_FOLDER', 'gravity-forms');
        define('GF_PATH', FL1_PATH.'/modules/'.GF_PLUGIN_FOLDER.'/');
        define('GF_URL', FL1_URL.'/modules/'.GF_PLUGIN_FOLDER.'/');

    }
    
    /**
     * Loads all dependencies.
     *
     * @access public
     * @since 1.0
     * @return void
     */
    public function load_dependencies($deps) {

        $deps[] = GF_PATH. 'class-gf-public.php';

        return $deps;

    }

    public function init() {
        
        new FL1_GF_Public();

    }

}

// Release the Kraken!
new FL1_Gravity_Forms();