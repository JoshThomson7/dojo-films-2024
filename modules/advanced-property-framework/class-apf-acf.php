<?php
/**
 * APF_ACF
 *
 * Class in charge of ACF related functionality
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class APF_ACF {

    public function __construct() {

		add_action('acf/save_post', array($this, 'save_post'), 20);
		add_filter('acf/validate_value/name=property_price', array($this, 'validate_price'), 10, 4);

		add_action('pmxi_saved_post', array($this, 'wpai_saved_post'), 10, 3);

		add_filter('acf/fields/relationship/result/key=field_652406e66724c', array($this, 'acf_fc_properties_relationship'), 10, 4);

		add_filter('fl1_acf_json_save_groups', array($this, 'save_field_groups'), 10, 2);
        //add_filter('fl1_acf_json_load_location', array($this, 'load_field_groups'), 20);

    }

    /**
     * On ACF Init
     */
    public static function init() {
    
		$apf_settings = new APF_Settings();
		acf_update_setting('google_api_key', $apf_settings->google_maps_api_key());

		if(function_exists('acf_add_options_sub_page')) {
        
            acf_add_options_sub_page(array(
                'page_title'  => 'APF Settings',
                'menu_title'  => 'Settings',
                'menu_slug' => 'apf-settings',
                'parent_slug' => APF_SLUG,
            ));

        }
    
    }

	/**
	 * On ACF Save Post
	 * 
	 * @param int $post_id
	 * @return int $post_id
	 */
	public function save_post($post_id) {

		$screen = get_current_screen();

		if(get_post_type($post_id) === 'property') {

			$property = new APF_Property($post_id);
			$address = $property->get_address();
			$address_searchable = $property->get_address_searchable();
			$latitude = $property->get_latitude();
			$longitude = $property->get_longitude();
	
			if(is_array($address)) {
				if(!$address_searchable && isset($address['address'])) {
					update_field('property_address_searchable', $address['address'], $post_id);
				}

				if(!$latitude || !$longitude) {
					$property->set_latitude($address['lat']);
					$property->set_longitude($address['lng']);
				}
			}

			$this->lat_lng_to_db($post_id);

		}

		if(($post_id === 'options' && $screen->id === 'properties_page_apf-settings')) {

			$create_pages = get_field('apf_create_pages', 'options');

			if($create_pages) {
				$this->create_pages();
				update_field('apf_create_pages', false, 'options');
			}

			$create_db_tables = get_field('apf_create_db_tables', 'options');

			if($create_db_tables) {
				$this->create_db_tables();
				update_field('apf_create_db_tables', false, 'options');
			}

		}
	
		return $post_id;

	}

	/**
	 * Validate Price
	 * 
	 * @param bool $valid
	 * @param mixed $value
	 * @param array $field
	 * @param string $input
	 */
	public function validate_price( $valid, $value, $field, $input ) {

		if( !$valid ) return $valid;
	
		$price = $_POST['acf']['field_57fe5641328df'];
	
		if (strpos($price, '£') !== false) {
			$valid = 'No pound sign, por favor :)';
		}
	
		return $valid;
	
	}

	public function save_field_groups($field_groups, $group) {

        if (strpos($group['title'], strtoupper(APF_SLUG)) !== false || $group['title'] === 'Property Details' || $group['title'] === 'Branch Details') {
            $field_groups[$group["key"]] = APF_PATH .'acf-json';
        }

        return $field_groups;

    }

    public function load_field_groups($paths) {

        $paths[] = APF_PATH .'acf-json';
		pretty_print($paths);

        return $paths;

    }

	/**
	 * Do stuff after WP All Import has imported the post
	 * 
	 * @param Int $post_id
	 * @param String $xml_node
	 * @param Bool $is_update
	 */
	public function lat_lng_to_db($post_id) {    

		global $wpdb;

		$property = new APF_Property($post_id);
	
		$property_lat = $property->get_latitude();
		$property_lng = $property->get_longitude();

		if($property_lat && $property_lng) {
	
			$check_exists = $wpdb->get_row("SELECT WP_ID FROM fl1_apf_properties_geolocation WHERE WP_ID = ".$post_id);
	
			if(isset($check_exists->WP_ID) && $check_exists->WP_ID != '') {
				$wpdb->update('fl1_apf_properties_geolocation', array('lat' => $property_lat, 'lng' => $property_lng), array('WP_ID' => $post_id));
			} else {
				$wpdb->insert('fl1_apf_properties_geolocation', array('WP_ID' => $post_id, 'lat' => $property_lat, 'lng' => $property_lng));
			}

		}

	}

	/**
	 * Do stuff after WP All Import has imported the post
	 * 
	 * @param Int $post_id
	 * @param String $xml_node
	 * @param Bool $is_update
	 */
	public function wpai_saved_post( $post_id, $xml_node, $is_update ) {    

		if(get_post_type($post_id) === 'property') {
			$this->lat_lng_to_db($post_id);
		}

	}

	/**
	 * Used to create the core APF pages
	 */
	private function create_pages() {

		$pages = array(
			array(
				'name'  => 'property-search',
				'title' => 'Property Search',
				'child' => array(
					'xml' => 'XML',
					'import' => 'Import'
				)
			)
		);
	
		$template = array(
			'post_type'   => 'page',
			'post_status' => 'publish',
			'post_author' => 1
		);
	
		foreach( $pages as $page ) {
			$exists = get_page_by_title( $page['title'] );
	
			$my_page = array(
				'post_name'  => $page['name'],
				'post_title' => $page['title']
			);
	
			$my_page = array_merge( $my_page, $template );
	
			$id = ( $exists ? $exists->ID : wp_insert_post( $my_page ) );
	
			if( isset( $page['child'] ) ) {
				foreach( $page['child'] as $key => $value ) {
					$child_id = get_page_by_title( $value );
					$child_page = array(
						'post_name'   => $key,
						'post_title'  => $value,
						'post_parent' => $id
					);
					$child_page = array_merge( $child_page, $template );
					if( !isset( $child_id ) ) wp_insert_post( $child_page );
				}
			}
		}

	}

	private function create_db_tables() {

		// WP Globals
		global $wpdb;

		$geo_table = $wpdb->prefix.'apf_properties_geolocation';

		// Create Customer Table if not exist
		if( $wpdb->get_var( "show tables like '$geo_table'" ) != $geo_table ) { 

			$sql = "CREATE TABLE `fl1_apf_properties_geolocation` (";
			$sql .= " `ID` int(11) NOT NULL AUTO_INCREMENT, ";
			$sql .= " `WP_ID` int(11) NOT NULL, ";
			$sql .= " `lat` decimal(10,6) NOT NULL, ";
			$sql .= " `lng` decimal(10,6) NOT NULL, ";
			$sql .= " PRIMARY KEY (`ID`), ";
			$sql .= " KEY `ID` (`ID`), ";
			$sql .= " KEY `ID_2` (`ID`), ";
			$sql .= " KEY `WP_ID` (`WP_ID`), ";
			$sql .= " KEY `lat` (`lat`), ";
			$sql .= " KEY `lng` (`lng`) ";
			$sql .= " ) ENGINE=MyISAM DEFAULT CHARSET=latin1;";

			// Include Upgrade Script
			require_once ABSPATH . '/wp-admin/includes/upgrade.php';

			// Create Table
			dbDelta( $sql );
		}

	}

	public function acf_fc_properties_relationship( $text, $post, $field, $post_id ) {

		$property = new APF_Property($post->ID);
		$text = '<div style="display: flex; gap: 8px; align-items: center;">';

		$property_img = $property->get_main_image();
		if($property_img) {
			$text .= '<img src="'.$property_img.'" style="width: 50px;">';
		}

		$text .= $property->get_name().' ('.$property->get_feed_id().')';
		$text .= '</div>';

		return $text;

	}

}

function load_field_groups($paths) {

	$paths[] = APF_PATH .'acf-json';
	//pretty_print($paths);

	return $paths;

}
add_filter('fl1_acf_json_load_location', 'load_field_groups');