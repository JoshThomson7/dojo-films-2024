<?php
/**
 * FC_CPT
 *
 * Class in charge of registering FC custom post types
 */

 // Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class FC_CPT {

    public function __construct() {

        $post_types = array(
            'fc_global',
        );

        foreach($post_types as $post_type) {
            $method = 'register_'.$post_type.'_cpt';

            if(method_exists($this, $method)) {
                $this->$method();
            }
        }

        add_action('admin_menu', array($this, 'menu_page'));
        add_action('admin_menu', array($this, 'remove_duplicate_subpage'));
        add_filter('parent_file', array($this, 'highlight_current_menu'));
        add_filter('posts_where', array($this, 'query_subfields'));

        add_filter('fl1_acf_json_save_groups', array($this, 'save_field_groups'), 10, 2);
        add_filter('fl1_acf_json_load_location', array($this, 'load_field_groups'));

    }

    public function menu_page() {
        add_menu_page(
            __('FC', FC_SLUG),
            'Flexible Content',
            'manage_options',
            FC_SLUG,
            '',
            'dashicons-table-row-after',
            30
        );

        $submenu_pages = array(
            array(
                'page_title'  => 'Global Content',
                'menu_title'  => 'Global Content',
                'capability'  => 'manage_options',
                'menu_slug'   => 'edit.php?post_type=fc-global',
                'function'    => null,
            )
        );

        foreach ( $submenu_pages as $submenu ) {

            add_submenu_page(
                FC_SLUG,
                $submenu['page_title'],
                $submenu['menu_title'],
                $submenu['capability'],
                $submenu['menu_slug'],
                $submenu['function']
            );

        }
    }

    public function highlight_current_menu( $parent_file ) {

        // global $submenu_file, $current_screen, $pagenow;

        // $cpts = FL1_Helpers::registered_post_types(FC_SLUG);

        // # Set the submenu as active/current while anywhere APM
        // if (in_array($current_screen->post_type, $cpts)) {

        //     if ( $pagenow == 'post.php' ) {
        //         $submenu_file = 'edit.php?post_type=' . $current_screen->post_type;
        //     }

        //     if ( $pagenow == 'edit-tags.php' ) {
        //         $submenu_file = 'edit-tags.php?taxonomy='.$current_screen->taxonomy.'&post_type=' . $current_screen->post_type;
        //     }

        //     $parent_file = FC_SLUG;

        // }

        return FC_SLUG;

    }

    /**
     * Global Content CPT
     */
    private function register_fc_global_cpt() {

        // CPT
        $cpt = new FL1_CPT(
            array(
                'post_type_name' => 'fc-global',
                'plural' => 'Global Content',
                'menu_name' => 'Global Content'
            ),
            array(
                'menu_position' => 21,
                'rewrite' => false,
                'show_in_menu' => false,
                'publicly_queryable' => false,
                'generator' => FC_SLUG
            )
        );

        $cpt->columns(array(
            'cb' => '<input type="checkbox" />',
	        'title' => __('Content'),
            'pages' => __('Used in'),
            'layouts' => __('Layouts'),
        ));

        $cpt->populate_column('pages', function($column, $post) {

            $post_id = $post->ID;
            
            $args = array(
                'post_type' => array('page'),
                'meta_query' => array(
                    array(
                        'key' => 'fc_content_types_$_global_content',
                        'compare' => 'LIKE',
                        'value' => $post_id,
                    )
                ),
                'fields' => 'ids'
            );

            $pages = new WP_Query($args);
            $pages = $pages->posts;

            if(!empty($pages)) {
                $used_in = array();
                foreach($pages as $page_id) {
                    $used_in[] = '<a href="'.get_edit_post_link($page_id).'">'.get_the_title($page_id).'</a>';
                }

                echo join(', ', $used_in);
            } else {
                echo '--';
            }
        
        });

        $cpt->populate_column('layouts', function($column, $post) {

            $post_id = $post->ID;
            
            if(have_rows('fc_content_types', $post_id)) {
                $layouts = array();
                while(have_rows('fc_content_types', $post_id)) {
                    the_row();
                    $layouts[] = get_row_layout();
                    
                    echo join(', ', $layouts);
                }
            } else {
                echo '--';
            }
        
        });

    }

    /**
	 * Remove duplicate sub page
	 *
	 * @since 1.0
	 */
	public function remove_duplicate_subpage() {
        remove_submenu_page(FC_SLUG, FC_SLUG);
    }

    public function query_subfields( $where ) {
        $where = str_replace("meta_key = 'fc_content_types_$", "meta_key LIKE 'fc_content_types_%", $where);
        return $where;
    } 

    public function save_field_groups($field_groups, $group) {

        if (strpos($group['title'], strtoupper(FC_SLUG)) !== false || $group['title'] === 'Flexible Content') {
            $field_groups[$group["key"]] = FC_PATH .'acf-json';
        }

        return $field_groups;

    }

    public function load_field_groups($paths) {

        $paths[] = FC_PATH .'acf-json';

        return $paths;

    }
    

}