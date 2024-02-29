<?php
/**
 * FL1_Blog_Module Init
 *
 * Class in charge of initialising everything FW
 */

 // Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class FL1_Blog_Module {

    public function __construct() {

        $this->define_constants();

        add_filter(FL1_SLUG.'_load_dependencies', array($this, 'load_dependencies'));
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
        
        define('FL1_BLOG_VERSION', '2.0');
        define('FL1_BLOG_SLUG', 'fl1-blog');
        define('FL1_BLOG_PLUGIN_FOLDER', 'blog');
        define('FL1_BLOG_PATH', FL1_PATH.'/modules/'.FL1_BLOG_PLUGIN_FOLDER.'/');
        define('FL1_BLOG_TEMPLATE_PATH', FL1_PATH.'/modules/'.FL1_BLOG_PLUGIN_FOLDER.'/');
        define('FL1_BLOG_URL', FL1_URL.'/modules/'.FL1_BLOG_PLUGIN_FOLDER.'/');

    }

    public function load_dependencies($deps) {

        $deps[] = FL1_BLOG_PATH. 'class-fl1-blog-helpers.php';
        $deps[] = FL1_BLOG_PATH. 'class-fl1-blog-public.php';
        $deps[] = FL1_BLOG_PATH. 'class-fl1-blog.php';

        return $deps;

    }

    public function setup_theme() {

		$blogs_public = new FL1_Blog_Public();

        add_filter('fl1_acf_json_save_groups', array($this, 'save_field_groups'), 10, 2);
        add_filter('fl1_acf_json_load_location', array($this, 'load_field_groups'));

    }

    public function acf_init() {

        if(function_exists('acf_add_options_page')) {
        
            acf_add_options_sub_page(array(
                'page_title'  => 'Blog Settings',
                'menu_title'  => 'Blog Settings',
                'menu_slug' => FL1_BLOG_SLUG.'-settings',
                'parent_slug' => 'edit.php',
            ));

        }

    }

    public function save_field_groups($field_groups, $group) {

        if ($group['title'] === 'Blog Settings') {
            $field_groups[$group["key"]] = FL1_BLOG_PATH .'acf-json';
        }

        return $field_groups;

    }

    public function load_field_groups($paths) {

        $paths[] = FL1_BLOG_PATH .'acf-json';

        return $paths;

    }
    

}

// Release the Kraken!
$fl1_blog_module = new FL1_Blog_Module();