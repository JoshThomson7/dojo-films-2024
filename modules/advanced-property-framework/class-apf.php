<?php
/**
 * APF: Advanced Property Framework
 *
 * Class in charge of initialising everything APF
 */

 // Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class APF {

	private $code;
	private $name;
	private $folder;
	private $version;

    public function __construct($code, $name, $folder, $version = '1.0') {

		$this->code = $code;
		$this->name = $name;
		$this->folder = $folder;
		$this->version = $version;

        $this->define_constants();

        add_filter(FL1_SLUG.'_load_dependencies', array($this, 'load_dependencies'));
        add_action(FL1_SLUG.'_setup_theme',	array($this, 'setup_theme'));
        add_action(FL1_SLUG.'_init', array($this, 'init'));
        add_action(FL1_SLUG.'_enqueue', array($this, 'enqueue'));

    }

    /**
     * Setup constants.
     *
     * @access private
     * @since 1.0
     * @return void
     */
    private function define_constants() {

        define('APF_VERSION', $this->version);
        define('APF_SLUG', $this->code);
        define('APF_NAME', $this->name);
        define('APF_PLUGIN_FOLDER', $this->folder);
        define('APF_PATH', FL1_PATH.'/modules/'.APF_PLUGIN_FOLDER.'/');
        define('APF_URL', FL1_URL.'/modules/'.APF_PLUGIN_FOLDER.'/');

    }
    
    /**
     * Loads all dependencies.
     *
     * @access public
     * @since 1.0
     * @return void
     */
    public function load_dependencies($deps) {

        $deps[] = APF_PATH. 'class-apf-helpers.php';
        $deps[] = APF_PATH. 'class-apf-cpt.php';
        $deps[] = APF_PATH. 'class-apf-settings.php';
        $deps[] = APF_PATH. 'class-apf-public.php';
        $deps[] = APF_PATH. 'class-apf-acf.php';
        $deps[] = APF_PATH. 'class-apf-search.php';
        $deps[] = APF_PATH. 'class-apf-property.php';
        $deps[] = APF_PATH. 'class-apf-branch.php';
        $deps[] = APF_PATH. 'class-apf-gf.php';

        return $deps;

    }

    public function setup_theme() {

		new APF_CPT();
		new APF_ACF();

    }

    public function init() {

       	new APF_Public();
		new APF_Search();
		new APF_GF();
		
		APF_ACF::init();
        
    }

    public function enqueue() {

		$apf_settings = new APF_Settings();

		// Ajax
		wp_localize_script(APF_SLUG.'-global', APF_SLUG.'_ajax_object', array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'ajax_nonce' => wp_create_nonce('$C.cGLu/1zxq%.KH}PjIKK|2_7WDN`x[vdhtF5GS4|+6%$wvG)2xZgJcWv3H2K_M'),
			'apf_path' => APF_URL,
			'apf_page' => APF_Helpers::property_search_url(),
			'apf_properties_map_url' => APF_Helpers::property_search_url().'xml/',
			'apf_branches_map_url' => get_permalink(get_page_by_path('find-a-branch/xml')),
			'apf_google_api_key' => $apf_settings->google_maps_api_key(),
			'apf_google_api_key_geocoding' => $apf_settings->google_maps_api_key_geocoding(),
			'apf_persist_search' => $apf_settings->persist_search(),
			'apf_persist_delete_unload' => $apf_settings->persist_delete_on_unload(),
		));

		// Google Maps
		wp_enqueue_script('google-maps', 'https://maps.googleapis.com/maps/api/js?v=3&key='.$apf_settings->google_maps_api_key());
		wp_enqueue_script(APF_SLUG.'-global', APF_URL.'assets/js/apf.min.js', array('jquery'), '', false);

		// style
		wp_enqueue_style(APF_SLUG.'-global', APF_URL.'assets/css/apf.min.css' );

    }

}

// Release the Kraken!
new APF('apf', 'APF', 'advanced-property-framework');