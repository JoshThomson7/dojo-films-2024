<?php
/**
 * APF_Search
 *
 * Class in charge of APF_Search related functionality
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class APF_Search {

    public function __construct() {

        add_action('wp_ajax_nopriv_apf_property_search', array($this, 'property_search'));
		add_action('wp_ajax_apf_property_search', array($this, 'property_search'));

		add_action('wp_ajax_nopriv_apf_map', array($this, 'map'));
		add_action('wp_ajax_apf_map', array($this, 'map'));

		add_action('wp_ajax_nopriv_fc_properties', array($this, 'fc_properties'));
		add_action('wp_ajax_fc_properties', array($this, 'fc_properties'));

    }

	public function property_search() {

		wp_verify_nonce('$C.cGLu/1zxq%.KH}PjIKK|2_7WDN`x[vdhtF5GS4|+6%$wvG)2xZgJcWv3H2K_M', 'apf_security');
		
		$search_params = isset($_POST['search_data']) && !empty($_POST['search_data']) ? $_POST['search_data'] : '';
		$properties = $this->fetchProperties($search_params);
		require_once APF_PATH . 'templates/loop.php';
	
		wp_die();

	}

	public function map() {

		wp_verify_nonce('$C.cGLu/1zxq%.KH}PjIKK|2_7WDN`x[vdhtF5GS4|+6%$wvG)2xZgJcWv3H2K_M', 'ajax_security');
	
		$search_params = isset($_POST['search_data']) && !empty($_POST['search_data']) ? $_POST['search_data'] : '';
		$properties = $this->fetchProperties($search_params);
		wp_send_json($properties->posts);
		wp_die();
	
	}

    private function fetchProperties($data) {

		$search_params = $data;
	
		$apf_market = isset($search_params['apf_market']) && !empty($search_params['apf_market']) ? htmlspecialchars(trim($search_params['apf_market'])) : 'residential';
		$apf_dept = isset($search_params['apf_dept']) && !empty($search_params['apf_dept']) ? htmlspecialchars(trim($search_params['apf_dept'])) : 'for-sale';
		$apf_dept = $apf_market === 'student' ? 'to-let' : $apf_dept;
		$apf_location = isset($search_params['apf_location']) && !empty($search_params['apf_location']) ? str_replace(',', ' ', trim($search_params['apf_location'])) : '';
		
		$apf_radius = isset($search_params['apf_radius']) && !empty($search_params['apf_radius']) ? $search_params['apf_radius'] : 0;
		$apf_minprice = isset($search_params['apf_minprice']) && !empty($search_params['apf_minprice']) ? $search_params['apf_minprice'] : 0;
		$apf_maxprice = isset($search_params['apf_maxprice']) && !empty($search_params['apf_maxprice']) ? $search_params['apf_maxprice'] : 9999999999;
		$apf_minbeds = isset($search_params['apf_minbeds']) && !empty($search_params['apf_minbeds']) ? $search_params['apf_minbeds'] : '';
		$apf_maxbeds = isset($search_params['apf_maxbeds']) && !empty($search_params['apf_maxbeds']) ? $search_params['apf_maxbeds'] : '';
		$apf_view = isset($search_params['apf_view']) && !empty($search_params['apf_view']) ? $search_params['apf_view'] : 'grid';
		$apf_order = isset($search_params['apf_order']) && !empty($search_params['apf_order']) ? $search_params['apf_order'] : 'price_desc';
		$apf_status = isset($search_params['apf_status']) && !empty($search_params['apf_status']) ? $search_params['apf_status'] : '';
		$apf_property_type = isset($search_params['apf_property_type']) && !empty($search_params['apf_property_type']) ? $search_params['apf_property_type'] : 'Spaceship';
		$apf_new_homes = isset($search_params['apf_new_homes']) && !empty($search_params['apf_new_homes']) ? $search_params['apf_new_homes'] : '';
		$apf_branch = isset($search_params['apf_branch']) && !empty($search_params['apf_branch']) ? $search_params['apf_branch'] : '';
		$posts_per_page = isset($search_params['posts_per_page']) && !empty($search_params['posts_per_page']) ? $search_params['posts_per_page'] : 30;
		$apf_page = isset($search_params['apf_page']) && !empty($search_params['apf_page']) ? $search_params['apf_page'] : 1;
		$posts_in = isset($search_params['posts_in']) && !empty($search_params['posts_in']) ? $search_params['posts_in'] : array();
		$pagination = !isset($search_params['pagination']) ? true : ($search_params['pagination'] === 'true' ? true : false);
		$search_params['pagination'] = $pagination;
		
		$acf_price_field = 'property_price';
	
		$property_ids = $posts_in;
	
		if($apf_location) {
	
			if($apf_radius < 0.1) {
				$apf_settings = new APF_Settings();
				$apf_rules = $apf_settings->do_search_rules($apf_location, $apf_radius);
	
				if(is_array($apf_rules)) {
					$apf_location = $apf_rules['text'] !== '' ? $apf_rules['text'] : $apf_location;
					$apf_radius = $apf_rules['radius'] !== '' ? $apf_rules['radius'] : $apf_radius;
				}
			}
			
			$geolocate = $this->geolocate($apf_location, $apf_radius);
			$property_ids = !empty($geolocate['ids']) ? wp_list_pluck($geolocate['ids'], 'ID') : array(0);
	
		}
		
		$args = array(
			'post_type'         => 'property',
			'post_status'       => 'publish',
			'post__in'          => $property_ids,
			'tax_query' => array(
				'relation' => 'AND',
				array(
					'taxonomy'  => 'property_market',
					'field'     => 'slug',
					'terms'     => $apf_market,
					'operator'  => 'IN'
				),
				array(
					'taxonomy'  => 'property_department',
					'field'     => 'slug',
					'terms'     => $apf_dept,
					'operator'  => 'IN'
				)
			),
			'meta_query' => array(
				array(
					'key'       => $acf_price_field,
					'value'     => array($apf_minprice, $apf_maxprice),
					'compare'   => 'BETWEEN',
					'type'      => 'numeric'
				),
				array(
					'key'       => 'property_bedrooms',
					'value'     => array($apf_minbeds, $apf_maxbeds),
					'compare'   => 'BETWEEN',
					'type'      => 'numeric'
				),
				array(
					'key'       => 'property_status',
					'value'     => APF_Helpers::exclude_statuses($apf_status),
					'compare'   => 'NOT IN',
				)
			),
			'posts__in'         => $posts_in,
			'posts_per_page'    => $posts_per_page,
			'paged'             => $apf_page,
			'fields'            => 'ids'
		);
	
		// If no property IDs have been return form geocoding,
		// search within the address field
		if(count($property_ids) == 1) {
			$property_id = $property_ids[0];
			if($property_id == 0) {
				$args['meta_query'][] = array(
					'key'       => 'property_address_searchable',
					'value'     => $apf_location,
					'compare'   => 'LIKE',
				);
				unset($args['post__in']);
			}
		}
	
		if($apf_new_homes === 'true') {
			array_push($args['meta_query'], array(
				'key'       => 'property_new_home',
				'value'     => 1,
				'compare'   => '=',
				'type'      => 'numeric'
			));
		}
	
		switch ($apf_order) {
			case 'price_asc':
				$args['meta_key'] = $acf_price_field;
				$args['orderby'] = 'meta_value_num';
				$args['order'] = 'ASC';
				break;
	
			case 'date_desc':
				$args['orderby'] = 'date';
				$args['order'] = 'DESC';
				break;
	
			case 'date_asc':
				$args['orderby'] = 'date';
				$args['order'] = 'ASC';
				break;
			
			default:
				$args['meta_key'] = $acf_price_field;
				$args['orderby'] = 'meta_value_num';
				$args['order'] = 'DESC';
				break;
		}
	
		if($apf_branch) {
			$args['meta_query'][] = array(
				'key'       => 'property_branch_id',
				'value'     => $apf_branch,
				'compare'   => '=',
				'type'      => 'numeric'
			);
		}

		if(!empty($posts_in)) {
			$args['posts_per_page'] = -1;
			unset($args['tax_query']);
			unset($args['meta_query']);
		}
	
		// Return
		$data = new stdClass();
		$data->geolocate = $geolocate;
		$data->search_params = $search_params;

		$query = new WP_Query($args);
		$data->posts = $query->posts;
		$data->max_num_pages = $query->max_num_pages;
	
		return $data;
	
	}


	/**
	 * FC Properties
	 * AJAX callback for fetching properties for FC
	 */
	public function fc_properties() {

		wp_verify_nonce('$C.cGLu/1zxq%.KH}PjIKK|2_7WDN`x[vdhtF5GS4|+6%$wvG)2xZgJcWv3H2K_M', 'apf_security');
		
		$search_params = isset($_POST['search_data']) && !empty($_POST['search_data']) ? $_POST['search_data'] : '';

		if(
			isset($search_params['apf_market']) &&
			$search_params['apf_market'] === 'student' &&
			isset($search_params['apf_dept']) &&
			$search_params['apf_dept'] === ''
		) {
			$search_params['apf_dept'] = 'to-let';
		}

		$properties = $this->fetchProperties($search_params);
		require_once APF_PATH . 'templates/loop.php';
	
		wp_die();

	}

	/**
	 * Geolocate
	 * 
	 * @param string $location
	 * @param float $radius
	 */
	private function geolocate($location, $radius = 0.5) {

		// Collect array of IDs
		$geolocate = array(
			'lat' => '',
			'lng' => '',
			'is_nearest' => false,
			'ids' => array()
		);

		// Geocode location.
		$geo_data = $this->geocode($location);
		
		if(!empty($geo_data) && is_array($geo_data)) {
		
			$lat = $geo_data[0];
			$lng = $geo_data[1];

			if(!empty($lat) && !empty($lng)) {

				global $wpdb;

				$geoSQL = $wpdb->prepare("SELECT properties.*, {$wpdb->prefix}posts.post_title FROM (SELECT WP_ID, SQRT(POW(69.1 * (lat - %s), 2) + POW(69.1 * (%s - lng) * COS(lat / 57.3), 2)) AS distance FROM fl1_apf_properties_geolocation HAVING distance < %s) as properties INNER JOIN {$wpdb->prefix}posts ON {$wpdb->prefix}posts.ID = properties.WP_ID ORDER BY distance", $lat, $lng, $radius);
				$geoLocations = $wpdb->get_results($geoSQL);
				
				// If no properties are found, find nearest one.
				if(empty($geoLocations)) {
					
					$nearestSQL = $wpdb->prepare("SELECT properties.*, {$wpdb->prefix}posts.post_title FROM (SELECT WP_ID, SQRT(POW(69.1 * (lat - %s), 2) + POW(69.1 * (%s - lng) * COS(lat / 57.3), 2)) AS distance FROM fl1_apf_properties_geolocation HAVING distance < %s) as properties INNER JOIN {$wpdb->prefix}posts ON {$wpdb->prefix}posts.ID = properties.WP_ID ORDER BY distance", $lat, $lng, 5);
					$geoLocations = $wpdb->get_results($nearestSQL);
					if(!empty($geoLocations)) { $geolocate['is_nearest'] = true; }
				}
				
				// Collect array WP_ID => distance
				if(!empty($geoLocations)) {
					foreach($geoLocations as $location) {
						array_push($geolocate['ids'], array(
							'ID' => $location->WP_ID,
							'distance' => $location->distance,
						));
					}
				}
			
			}

		}

		return $geolocate;

	}

	/**
	 * Geocode
	 * 
	 * @param string $full_address
	 * @param string $action
	 */
	private function geocode($full_address, $action = null) {

		// bail early if no address passed
		if(empty($full_address)) {
			return false;
		}

		// hacky hack
		if($action != 'geolocate') {
			$uk = '+United+Kingdom';
		}

		// url encode the address
		$full_address = str_replace(" ", "+", urlencode($full_address)).$uk;

		if($full_address) {
			$apf_settings = new APF_Settings();
			$url = 'https://maps.google.com/maps/api/geocode/json?&address='.$full_address.'&key='.$apf_settings->google_maps_api_key_geocoding();

			// get the json response
			$resp_json = file_get_contents($url);

			// decode the json
			$resp = json_decode($resp_json, true);

			// response status will be 'OK', if able to geocode given address
			if($resp['status'] === 'OK') {

				// get the important data
				$lati = $resp['results'][0]['geometry']['location']['lat'];
				$longi = $resp['results'][0]['geometry']['location']['lng'];
				$formatted_address = $resp['results'][0]['formatted_address'];

				// verify if data is complete
				if($lati && $longi && $formatted_address){

					// put the data in the array
					$data_arr = array();

					array_push(
						$data_arr,
						$lati,
						$longi,
						$formatted_address
					);

					return $data_arr;

				} else {
					return false;
				}

			} else {
				return false;
			}
		} else {
			return false;
		}

	}

}

