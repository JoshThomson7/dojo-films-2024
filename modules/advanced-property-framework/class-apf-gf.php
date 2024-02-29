<?php
/**
 * APF_GF
 *
 * Class in charge of Gravity Forms integration
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class APF_GF {

    public function __construct() {

		add_filter('gform_field_value_apf_property_id', array($this, 'property_id'));
		add_filter('gform_field_value_apf_property_title', array($this, 'property_title'));
		add_filter('gform_field_value_apf_property_url', array($this, 'property_url'));
		add_filter('gform_field_value_apf_branch_email', array($this, 'branch_email'));

    }

    /**
	 * Property ID
	 * 
	 * @param mixed $value
	 */
	public function property_id($value) {

		$property_id = FL1_Helpers::get_top_parent_page_id();
		$value = $property_id;
		return $value;

	}

	/**
	 * Property Title
	 * 
	 * @param mixed $value
	 */
	public function property_title($value) {

		$post_id = FL1_Helpers::get_top_parent_page_id();

		$property = new APF_Property($post_id);

		$property_title = $property->get_name();
		$property_price = $property->get_price();
		$value = $property_title.$property_price;

		return $value;
	}

	/**
	 * Property URL
	 * 
	 * @param mixed $value
	 */
	public function property_url($value) {

		$post_id = FL1_Helpers::get_top_parent_page_id();
		$property = new APF_Property($post_id);
		$property_url = $property->get_permalink();

		$value = $property_url;

		return $value;
	}
	
	/**
	 * Branch Email
	 * 
	 * @param mixed $value
	 */
	public function branch_email($value) {

		$post_id = FL1_Helpers::get_top_parent_page_id();

		$property = new APF_Property($post_id);
		$branch_id = $property->get_branch_id();

		// Get branch
		$branch_query = new WP_Query(array(
			'post_type'         => 'branch',
			'post_status'       => 'publish',
			'posts_per_page'    => 1,
			'meta_query' => array(
				array(
					'key'       => 'branch_id',
					'value'     => $branch_id,
					'compare'   => 'LIKE'
				)
			),
			'fields'            => 'ids'
		));
		$branch = $branch_query->posts;
		
		if(count($branch) > 0) {
			$branch = new APF_Branch($branch[0]);
			return $branch->get_email(false);
		}

		return $value;
	}

}

