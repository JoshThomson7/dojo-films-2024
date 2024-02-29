<?php
/**
 * FL1C_CPT
 *
 * Class in charge of registering custom post types
 */

 // Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class FL1C_CPT {

	private $post_types = array(
		'team',
		'testimonial',
		//'location',
		//'development',
	);

    public function __construct() {

        foreach($this->post_types as $post_type) {
            $method = 'register_'.$post_type.'_cpt';

            if(method_exists($this, $method)) {
                $this->$method();
            }
        }

        add_action('admin_menu', array($this, 'menu_page'));
        add_action('admin_menu', array($this, 'remove_duplicate_subpage'));
        add_filter('parent_file', array($this, 'highlight_current_menu'));
        add_action('admin_head', array($this, 'column_widths'));

        add_action('acf/init', array($this, 'acf_init'));

    }

    public function menu_page() {
        add_menu_page(
            __('FL1C', FL1C_SLUG),
            FL1C_NAME,
            'edit_posts',
            FL1C_SLUG,
            '',
            'dashicons-flag',
            32
        );

        $submenu_pages = array(
			// array(
            //     'page_title'  => 'Locations',
            //     'menu_title'  => 'Locations',
            //     'capability'  => 'edit_posts',
            //     'menu_slug'   => 'edit.php?post_type=location',
            //     'function'    => null,
            // ),
			// array(
			// 	'page_title'  => 'Developments',
			// 	'menu_title'  => 'Developments',
			// 	'capability'  => 'edit_posts',
			// 	'menu_slug'   => 'edit.php?post_type=development',
			// 	'function'    => null,
			// ),
            array(
                'page_title'  => 'Team',
                'menu_title'  => 'Team',
                'capability'  => 'edit_posts',
                'menu_slug'   => 'edit.php?post_type=team',
                'function'    => null,
            ),
				array(
					'page_title'  => '',
					'menu_title'  => '&nbsp;- Categories',
					'capability'  => 'manage_options',
					'menu_slug'   => 'edit-tags.php?taxonomy=team_department&post_type=team',
					'function'    => null,
				),
            array(
                'page_title'  => 'Testimonials',
                'menu_title'  => 'Testimonials',
                'capability'  => 'edit_posts',
                'menu_slug'   => 'edit.php?post_type=testimonial',
                'function'    => null,
            )
        );

        foreach ( $submenu_pages as $submenu ) {

            add_submenu_page(
                FL1C_SLUG,
                $submenu['page_title'],
                $submenu['menu_title'],
                $submenu['capability'],
                $submenu['menu_slug'],
                $submenu['function']
            );

        }
    }

    public function highlight_current_menu( $parent_file ) {

        global $submenu_file, $current_screen, $pagenow;

        $cpts = FL1_Helpers::registered_post_types(FL1C_SLUG);

        # Set the submenu as active/current while anywhere
        if (in_array($current_screen->post_type, $cpts)) {

            if ( $pagenow == 'post.php' ) {
                $submenu_file = 'edit.php?post_type=' . $current_screen->post_type;
            }

            if ( $pagenow == 'edit-tags.php' ) {
                $submenu_file = 'edit-tags.php?taxonomy='.$current_screen->taxonomy.'&post_type=' . $current_screen->post_type;
            }

            $parent_file = FL1C_SLUG;

        }

        return $submenu_file;

    }

    /**
     * Team CPT
     */
    private function register_team_cpt() {

        // CPT
        $cpt = new FL1_CPT(
            array(
                'post_type_name' => 'team',
                'plural' => 'Team',
                'menu_name' => 'Team'
            ),
            array(
                'menu_position' => 21,
                'rewrite' => array( 'slug' => 'team', 'with_front' => false ),
                'publicly_queryable' => false,
                'generator' => FL1C_SLUG
            )
        );

		$cpt->register_taxonomy(
            array(
                'taxonomy_name' => 'team_department',
                'slug' => 'team_department',
                'singular' => 'Department',
                'plural' => 'Departments'
            )
        );

		$cpt->columns(array(
            'cb' => '<input type="checkbox" />',
	        'picture' => __('Picture'),
            'title' => __('Name'),
			'team_department' => __('Department'),
            'details' => __('Details'),
        ));

		$cpt->populate_column('picture', function($column, $post) {

            $post_id = $post->ID;
            $team = new FL1C_Team_Member($post_id);

            if(get_post_thumbnail_id($post_id)) {
				echo '<a href="'.get_admin_url().'post.php?post='.$post_id.'&action=edit"><img src="'.$team->image(200, 200)['url'].'" style="width: 80px; border-radius: 8px;" /></a>';

			} else {
				echo __( '<div class="dashicons dashicons-format-image" style="font-size:48px; height:48px; color:#e0e0e0;"></div>' );

			}
		});

		$cpt->populate_column('job_title', function($column, $post) {
            $post_id = $post->ID;
            $team = new FL1C_Team_Member($post_id);
			$job_title = $team->job_title();

            echo $job_title ? $job_title : '--';
		});

		$cpt->populate_column('details', function($column, $post) {
            $post_id = $post->ID;
            $team = new FL1C_Team_Member($post_id);
			$office_id = $team->office_id();
			$office = $team->office();
			$job_title = $team->job_title();
			$email = $team->email();

			if($office) {
				echo '<strong><a href="'.get_admin_url().'post.php?post='.$office_id.'&action=edit"">'.$office.'</strong></a><br/>';
			}

			if($job_title) {
				echo $job_title.'<br/>';
			}

			if($email) {
				echo '<small>'.$email.'</small>';
			}

		});

    }

    /**
     * Testimonials CPT
     */
    private function register_testimonial_cpt() {

        // CPT
        $cpt = new FL1_CPT(
            array(
                'post_type_name' => 'testimonial',
                'plural' => 'Testimonials',
                'menu_name' => 'Testimonials'
            ),
            array(
                'menu_position' => 21,
                'rewrite' => array( 'slug' => 'testimonial', 'with_front' => false ),
                'publicly_queryable' => false,
                'generator' => FL1C_SLUG
            )
        );

		// Taxonomies
        $cpt->register_taxonomy(
            array(
                'taxonomy_name' => 'testimonial_category',
                'slug' => 'testimonial_category',
                'singular' => 'Testimonial Category',
                'plural' => 'Testimonial Categories',
				'public' => false,
				'query_var' => false,
				'rewrite' => false,
				'publicly_queryable' => false
            )
        );

        $cpt->columns(array(
            'cb' => '<input type="checkbox" />',
            //'rating' => __('Rating'),
            'title' => __('Name'),
            'quote' => __('Quote'),
            'testimonial_category' => __('Categories'),
        ));

        $cpt->populate_column('rating', function($column, $post) {

            $post_id = $post->ID;
            $testimonial = new FL1C_Testimonial($post_id);
            
            $testimonial->rating_display();
        
        });

        $cpt->populate_column('quote', function($column, $post) {

            $post_id = $post->ID;
            $testimonial = new FL1C_Testimonial($post_id);
            
            echo $testimonial->quote(30);
        
        });

    }

    /**
     * Location
     */
    private function register_location_cpt() {

        // CPT
        $cpt = new FL1_CPT(
            array(
                'post_type_name' => 'location',
                'plural' => 'Locations',
                'menu_name' => 'Locations'
            ),
            array(
                'menu_position' => 21,
                'rewrite' => array( 'slug' => 'location', 'with_front' => false ),
                'publicly_queryable' => true,
				'hierarchical' => true,
                'generator' => FL1C_SLUG
            )
        );

    }

    /**
     * Developments
     */
    private function register_development_cpt() {

        // CPT
        $cpt = new FL1_CPT(
            array(
                'post_type_name' => 'development',
                'plural' => 'Developments',
                'menu_name' => 'Developments'
            ),
            array(
                'menu_position' => 21,
                'rewrite' => array( 'slug' => 'development', 'with_front' => false ),
                'publicly_queryable' => true,
                'generator' => FL1C_SLUG
            )
        );

    }

    public function column_widths() {
        $screen = get_current_screen();

		$post_types = array('bank', 'team');
        
        if($screen->post_type && in_array($screen->post_type, $post_types)) {
            echo '<style type="text/css">';
            echo '.column-logo, .column-picture { width: 120px !important; overflow: hidden }';
            echo '.column-title { width: 320px !important; overflow: hidden }';
            echo '</style>';
        }
    }


    /**
	 * Remove duplicate sub page
	 *
	 * @since 1.0
	 */
	public function remove_duplicate_subpage() {
        remove_submenu_page(FL1C_SLUG, FL1C_SLUG);
		
		foreach($this->post_types as $post_type) {
			$post_type = str_replace('_', '-', $post_type);
            remove_menu_page('edit.php?post_type='.$post_type);
        }
    }

    /**
	 * Remove duplicate sub page
	 *
	 * @since 1.0
	 */
	public function acf_init() {

        if(function_exists('acf_add_options_sub_page')) {
        
            acf_add_options_sub_page(array(
                'page_title'  => 'Settings',
                'menu_title'  => 'Settings',
                'menu_slug' => 'fl1c-settings',
                'parent_slug' => FL1C_SLUG,
            ));

        }

    }

}