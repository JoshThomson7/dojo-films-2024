<?php
/**
 * APF_CPT
 *
 * Class in charge of registering custom post types
 */

 // Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class APF_CPT {

	private $post_types = array(
		'property',
		'branch',
	);

    public function __construct() {

        foreach($this->post_types as $post_type) {
            $method = 'register_'.$post_type.'_cpt';

            if(method_exists($this, $method)) {
                $this->$method();
            }
        }

        add_action('admin_menu', array($this, 'menu_page'));
        add_filter('parent_file', array($this, 'highlight_current_menu'));
        add_action('admin_head', array($this, 'column_widths'));

		// Rewrites and endpoints
		add_filter('post_type_link', array($this, 'filter_property_link'), 10, 2);
		$this->endpoints();
		//add_filter('request', array($this, 'filter_request'));

    }

    public function menu_page() {
        add_menu_page(
            __('APF', APF_SLUG),
            'Properties',
            'edit_posts',
            APF_SLUG,
            '',
            'dashicons-admin-home',
            33
        );

        $submenu_pages = array(
			array(
                'page_title'  => 'Properties',
                'menu_title'  => 'Properties',
                'capability'  => 'edit_posts',
                'menu_slug'   => 'edit.php?post_type=property',
                'function'    => null,
            ),
				array(
					'page_title'  => '',
					'menu_title'  => '&nbsp;- Markets',
					'capability'  => 'manage_options',
					'menu_slug'   => 'edit-tags.php?taxonomy=property_market&post_type=property',
					'function'    => null,
				),
				array(
					'page_title'  => '',
					'menu_title'  => '&nbsp;- Departments',
					'capability'  => 'manage_options',
					'menu_slug'   => 'edit-tags.php?taxonomy=property_department&post_type=property',
					'function'    => null,
				),
				array(
					'page_title'  => '',
					'menu_title'  => '&nbsp;- Areas',
					'capability'  => 'manage_options',
					'menu_slug'   => 'edit-tags.php?taxonomy=property_area&post_type=property',
					'function'    => null,
				),
			array(
				'page_title'  => 'Branches',
				'menu_title'  => 'Branches',
				'capability'  => 'edit_posts',
				'menu_slug'   => 'edit.php?post_type=branch',
				'function'    => null,
			),
        );

        foreach ( $submenu_pages as $submenu ) {

            add_submenu_page(
                APF_SLUG,
                $submenu['page_title'],
                $submenu['menu_title'],
                $submenu['capability'],
                $submenu['menu_slug'],
                $submenu['function']
            );

        }

		// Remove duplicate submenu pages
		remove_submenu_page(APF_SLUG, APF_SLUG);
		
		foreach($this->post_types as $post_type) {
			$post_type = str_replace('_', '-', $post_type);
            remove_menu_page('edit.php?post_type='.$post_type);
        }

    }

    public function highlight_current_menu( $parent_file ) {

        global $submenu_file, $current_screen, $pagenow;

        $cpts = FL1_Helpers::registered_post_types(APF_SLUG);

        # Set the submenu as active/current while anywhere
        if (in_array($current_screen->post_type, $cpts)) {

            if ( $pagenow == 'post.php' ) {
                $submenu_file = 'edit.php?post_type=' . $current_screen->post_type;
            }

            if ( $pagenow == 'edit-tags.php' ) {
                $submenu_file = 'edit-tags.php?taxonomy='.$current_screen->taxonomy.'&post_type=' . $current_screen->post_type;
            }

            $parent_file = APF_SLUG;

        }

        return $parent_file;

    }

    /**
     * Testimonials CPT
     */
    private function register_property_cpt() {

        // CPT
        $cpt = new FL1_CPT(
            array(
                'post_type_name' => 'property',
                'plural' => 'Properties',
                'menu_name' => 'Properties'
            ),
            array(
                'menu_position' => 21,
                'rewrite' => array( 'slug' => 'property/%property_market%/%property_department%/%property_area%', 'with_front' => false ),
                'generator' => APF_SLUG
            )
        );

		// Taxonomies
        $cpt->register_taxonomy(
            array(
                'taxonomy_name' => 'property_market',
                'slug' => 'property_market',
                'singular' => 'Market',
                'plural' => 'Markets',
				'public' => false,
				'query_var' => true,
				'rewrite' => array( 'slug' => 'property', 'with_front' => false ),
				'publicly_queryable' => false
            )
        );

        $cpt->register_taxonomy(
            array(
                'taxonomy_name' => 'property_department',
                'slug' => 'property_department',
                'singular' => 'Department',
                'plural' => 'Departments',
				'public' => false,
				'query_var' => true,
				'rewrite' => array( 'slug' => 'property/%property_market%', 'with_front' => false ),
				'publicly_queryable' => false
            )
        );

        $cpt->register_taxonomy(
            array(
                'taxonomy_name' => 'property_area',
                'slug' => 'property_area',
                'singular' => 'Area',
                'plural' => 'Areas',
				'public' => false,
				'query_var' => true,
				'rewrite' => array( 'slug' => 'property/%property_market%/%property_department%', 'with_front' => false ),
				'publicly_queryable' => false
            )
        );

        $cpt->columns(array(
            'cb' => '<input type="checkbox" />',
            'image' => __('Image'),
            'title' => __('Name'),
            'property_market' => __('Markets'),
            'property_department' => __('Departments'),
            'property_area' => __('Areas'),
            'property_price' => __('Price'),
            'property_status' => __('Status'),
        ));

        $cpt->populate_column('image', function($column, $post) {

			$property_id = $post->ID;
			$property = new APF_Property($property_id);

            if($property->get_main_image_id()) {
				echo '<a href="'.get_admin_url().'post.php?post='.$property_id.'&action=edit"><img src="'.$property->get_main_image(200, 120).'" /></a>';

			} else {
				echo __( '<div class="dashicons dashicons-format-image" style="font-size:82px; height:82px; color:#e0e0e0;"></div>' );

			}
        
        });

        $cpt->populate_column('property_market', function($column, $post) {

            $property_id = $post->ID;
			$property = new APF_Property($property_id);
            
            echo $property->get_markets();
        
        });

        $cpt->populate_column('property_department', function($column, $post) {

            $property_id = $post->ID;
			$property = new APF_Property($property_id);
            
            echo $property->get_departments();
        
        });

        $cpt->populate_column('property_area', function($column, $post) {

            $property_id = $post->ID;
			$property = new APF_Property($property_id);
            
            echo $property->get_areas();
        
        });

        $cpt->populate_column('property_price', function($column, $post) {

            $property_id = $post->ID;
			$property = new APF_Property($property_id);
            
            echo $property->get_price_html();
        
        });

        $cpt->populate_column('property_status', function($column, $post) {

            $property_id = $post->ID;
			$property = new APF_Property($property_id);
            
            echo $property->get_status();
        
        });

    }

    /**
     * Branch CPT
     */
    private function register_branch_cpt() {

        // CPT
        $cpt = new FL1_CPT(
            array(
                'post_type_name' => 'branch',
                'plural' => 'Branches',
                'menu_name' => 'Branches'
            ),
            array(
                'menu_position' => 21,
                'rewrite' => array( 'slug' => 'branch', 'with_front' => false),
                'generator' => APF_SLUG
            )
        );

        $cpt->columns(array(
            'cb' => '<input type="checkbox" />',
            'image' => __('Image'),
            'title' => __('Name'),
			'branch_address' => __('Address'),
			'branch_email' => __('Email'),
			'branch_id' => __('ID'),
        ));

        $cpt->populate_column('image', function($column, $post) {

			$branch_id = $post->ID;
			$branch = new APF_Branch($branch_id);
			$branch_image = $branch->image();

            if(is_array($branch_image) && isset($branch_image['url'])) {

				echo '<a href="'.get_admin_url().'post.php?post='.$branch_id.'&action=edit"><img src="'.$branch_image['url'].'" /></a>';

			} else {

				echo __( '<div class="dashicons dashicons-format-image" style="font-size:82px; height:82px; color:#e0e0e0;"></div>' );

			}
        
        });

        $cpt->populate_column('branch_address', function($column, $post) {

            $branch_id = $post->ID;
			$branch = new APF_Branch($branch_id);
            
            echo $branch->get_email('address');
        
        });

        $cpt->populate_column('branch_email', function($column, $post) {

            $branch_id = $post->ID;
			$branch = new APF_Branch($branch_id);
            
            echo $branch->get_email();
        
        });

        $cpt->populate_column('branch_id', function($column, $post) {

            $branch_id = $post->ID;
			$branch = new APF_Branch($branch_id);
            
            echo $branch->get_branch_id();
        
        });

    }

    public function column_widths() {

        $screen = get_current_screen();

		$post_types = array('property', 'branch');
        
        if($screen->post_type && in_array($screen->post_type, $post_types)) {
            echo '<style type="text/css">';
            echo '.column-image { width: 140px !important; }';
            echo '.column-image img { width: 140px !important; overflow: hidden }';
            echo '</style>';
        }

    }

	/**
	 * Add custom rewrite rules
	 * 
	 * @param string $link
	 * @param object $post
	 */
	public function filter_property_link($link, $post) {

		if($post->post_type != 'property') {
			return $link;
		}
	
		if($cats = get_the_terms($post->ID, 'property_market')) {
			$link = str_replace('%property_market%', array_pop($cats)->slug, $link);
		}
	
		if($cats = get_the_terms($post->ID, 'property_department')) {
			$link = str_replace('%property_department%', array_pop($cats)->slug, $link);
		}
	
		if($cats = get_the_terms($post->ID, 'property_area')) {
		   $link = str_replace('%property_area%', array_pop($cats)->slug, $link);
		}
	
		return $link;

	}

	/**
	 * Add custom rewrite rules
	 */
	public function endpoints() {

		$apf_settings = new APF_Settings();
		if($apf_settings->provider() === 'rtfd') {
			add_rewrite_endpoint('SendProperty', EP_PAGES);
			add_rewrite_endpoint('RemoveProperty', EP_PAGES);
		}
	
	}

}