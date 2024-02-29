<?php
/**
 * FC Public
 *
 * Class in charge of FC Public facing side
 */

 // Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class FC_Public {

    public function __construct() {

        add_action('wp_enqueue_scripts', array($this, 'enqueue'));
        add_action('body_class', array($this, 'body_classes'), 20);
        add_action('fc_tab_scroller', array($this, 'fc_tab_scroller'));

		$ajax_actions = array(
            'fc_team_filters',
        );

        foreach ($ajax_actions as $ajax_action) {
            add_action('wp_ajax_' . $ajax_action, array($this, $ajax_action));
            add_action('wp_ajax_nopriv_' . $ajax_action, array($this, $ajax_action));
		}

    }

    public function enqueue() {

		global $post;

        if(have_rows('fc_content_types', $post->ID)) { 
        	wp_enqueue_style(FC_SLUG, FC_URL.'assets/fc.min.css');
		}

    }

    /**
	 * Returns body CSS class names.
	 *
	 * @since 1.0
     * @param array $classes
	 */
    public function body_classes($classes) {
        global $post;
    
        if(have_rows('fc_content_types', $post->ID)) { 
            $classes[] = 'page-has-flexible-content';
        }
        
        return $classes;
    }

    /**
	 * Outputs the tab scroller.
	 */
    public function fc_tab_scroller($post_id) {
    
        if(get_field('fc_tab_scroller', $post_id)) {
            $type = get_field('fc_menu_type', $post_id);
            if($type === 'fc_sections' && have_rows('fc_content_types', $post_id)) { 
                include FC_PATH.'layouts/fc-tab-scrollbar.php';
            }

            if($type === 'child_pages' || $type === 'parent_child_pages') { 
                include FC_PATH.'layouts/fc-tab-child-pages.php';
            }
        }
        
    }

	/**
     * AJAX Filter.
     *
     * @since	1.0
     */
    public function fc_team_filters() {

        // Security check.
        wp_verify_nonce('$C.cGLu/1zxq%.KH}PjIKK|2_7WDN`x[vdhtF5GS4|+6%$wvG)2xZgJcWv3H2K_M', 'ajax_security');

        $form_data = $_POST['formData'];
        $keywords = isset($form_data['team_keywords']) && !empty($form_data['team_keywords']) ? $form_data['team_keywords'] : '';
        $team_department = isset($form_data['team_department']) && !empty($form_data['team_department']) ? $form_data['team_department'] : null;
        $team_offices = isset($form_data['team_offices']) && !empty($form_data['team_offices']) ? $form_data['team_offices'] : null;

        $wp_query_args = isset($_POST['wpQueryArgs']) && !empty($_POST['wpQueryArgs']) ? $_POST['wpQueryArgs'] : array();
        $paged = isset($wp_query_args['paged']) && !empty($wp_query_args['paged']) ? $wp_query_args['paged'] : 1;

        $args = array(
			'post_type'         => 'team',
			'post_status'       => 'publish',
			'orderby'           => 'menu_order',
			'order'             => 'asc',
			'posts_per_page'    => -1,
			'fields'			=> 'ids',
		);
        $args['tax_query'] = array();
        $args['meta_query'] = array();

		if($keywords) {
			$args['s'] = $keywords;
		}

        if($team_department) {
            $args['tax_query'][] = array(
                'taxonomy' => 'team_department',
                'terms' => $team_department,
                'field' => 'term_id',
                'operator' => 'IN'
            );
        }

		if ($team_offices) {
            $args['meta_query'][] = array(
                'key'       => 'team_office',
                'value'     => $team_offices,
                'operator' 	=> 'IN'
            );
        }

        if (!empty($wp_query_args)) {
            $args = wp_parse_args($wp_query_args, $args);
        }

		$team_query = new WP_Query($args);
		$team = $team_query->posts;
		$team_total = $team_query->post_count;

        include FC_PATH . 'layouts/fc_team-loop.php';
        wp_die();

    }

}

