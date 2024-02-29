<?php
/**
 * APF Settings
 *
 * Class in charge of APF's default settings
 *
 * @author  Various
 * @package Advanced Property Framework
 *
*/
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class APF_Settings {

    /**
     * Returns whether the system is set to development mode
     * 
     * @return bool
     */
    public function is_dev_mode() {

        return get_field('apf_development_mode', 'option') ?? false;

    }

    /**
     * Returns the property feed provider
     * 
     * @return array
     */
    public function provider() {

        return get_field('apf_provider', 'option') ?? '';

    }

    /**
     * Returns the property feed markets
     * 
     * @return array
     */
    public function markets() {

        $markets = get_field('apf_markets', 'option') ?? array();
		$default = $this->market_default();

		// make sure the default is always first
		if($default) {
			$default_key = array_search($default, array_column($markets, 'value'));
			if($default_key !== false) {
				$default = $markets[$default_key];
				unset($markets[$default_key]);
				array_unshift($markets, $default);
			}
		}

		return $markets;

    }

	public function market_default() {

		global $post;
		$post_id = $post->ID ?? false;

		$apf_settings_default = get_field('apf_market_default', 'option') ?? false;
		$banner_defaults = get_field('avb_apf_form_defaults', $post_id);
		$market_default = $banner_defaults['market'];

		return $market_default ? $market_default : $banner_defaults;

	}

    /**
     * Returns the property feed departments
     * 
     * @return array
     */
    public function departments() {

        $departments = get_field('apf_departments', 'option') ?? array();
		$default = $this->department_default();

		// make sure the default is always first
		if($default) {
			$default_key = array_search($default, array_column($departments, 'value'));
			if($default_key !== false) {
				$default = $departments[$default_key];
				unset($departments[$default_key]);
				array_unshift($departments, $default);
			}
		}

		return $departments;

    }

	public function department_default() {

		global $post;
		$post_id = $post->ID ?? false;

		$apf_settings_default = get_field('apf_department_default', 'option') ?? false;
		$banner_defaults = get_field('avb_apf_form_defaults', $post_id);
		$department_default = $banner_defaults['department'];

		return $department_default ? $department_default : $banner_defaults;

	}

	public function area_default() {

		global $post;
		$post_id = $post->ID ?? false;

		$banner_defaults = get_field('avb_apf_form_defaults', $post_id);
		$area_default = $banner_defaults['area'];

		return $area_default ? $area_default : '';

	}

    /**
     * Returns the Google Maps API Key
     * 
     * @return string
     */
    public function google_maps_api_key() {

       return get_field('apf_google_maps_api_key', 'option');

    }

    /**
     * Returns the Google Maps API Key (Geocoding)
     * 
     * @return string
     */
    public function google_maps_api_key_geocoding() {

       return get_field('apf_google_maps_api_key_geocoding', 'option');

    }

    /**
     * Returns an array of the sorting
     * options enabled in settings
     * 
     * @return array
     */
    public function search_sorting_filters() {

        return get_field('apf_search_sorting_filter', 'option') ?? false;

    }

    /**
     * Returns whether to show the
     * show/hide sold/let properties switch
     * 
     * @return bool
     */
    public function search_hide_gone() {

        return get_field('apf_search_gone_filter', 'option') ?? false;

    }

    /**
     * Whether to show the "New Homes only" switch
     * 
     * @return bool
     */
    public function search_new_home() {

        return get_field('apf_search_new_homes', 'option') ?? false;

    }

    /**
     * Whether to show the "No. of beds" dropdown
     * 
     * @return bool
     */
    public function search_beds_dropdown() {

        return get_field('apf_search_beds', 'option') ?? false;

    }

    /**
     * Whether to show the "No. of beds" dropdown
     * 
     * @return bool
     */
    public function search_home_banners_form() {

        return get_field('apf_search_home_banners_form', 'option') ?? false;

    }

    /**
     * Default radius for the search
     * 
     * @return bool
     */
    public function default_radius() {

        return get_field('apf_search_radius_default', 'option') ?? 0.09;

    }

    /**
     * Returns search rules
     * 
     * @return Array
     */
    public function search_rules() {

        return get_field('apf_search_custom_rules', 'option') ?? array();

    }

    /**
     * Returns apf_persist_last_search
     * 
     * @return bool
     */
    public function persist_search() {

        return get_field('apf_persist_last_search', 'option');

    }

    /**
     * Returns apf_persist_delete_unload
     * 
     * @return bool
     */
    public function persist_delete_on_unload() {

        $delete = get_field('apf_persist_delete_unload', 'option');

		if($delete && $this->persist_search()) {
			return true;
		}

		return false;

    }

    /**
     * Returns search rules
     * 
     * @return String
     */
    public function do_search_rules($searched = '', $apf_radius = 0.1) {

        $do_rules = array(
            'text' => $searched,
            'radius' => $apf_radius
        );

        if(!empty($this->search_rules())) {

            foreach($this->search_rules() as $rule) {
                
                $if_search_contains = $rule['if_search_contains'] ?? '';
                $do_action = $rule['do_action'] ?? '';

                if($if_search_contains === '') { continue; }
                if($do_action === '') { continue; }

                switch ($do_action) {
                    case 'radius':
                        if($rule['action_radius'] === '') { continue 2; }

                        if(stripos($searched, $if_search_contains) !== false) {
                            $do_rules['radius'] = $rule['action_radius'];
                        }
                        break;

                    case 'replace':
                        if($rule['action_text'] === '') { continue 2; }
                        $do_rules['text'] = str_ireplace($if_search_contains, $rule['action_text'], $searched);
                        break;

                    case 'add':
                        if($rule['action_text'] === '') { continue 2; }
                        
                        if(stripos($searched, $if_search_contains) !== false) {
                            $do_rules['text'] .= ' '.$rule['action_text'];
                        }

                        break;
                    
                    default:
                        # do no'ing, innit blood?
                        break;
                }

            }

        }

        return $do_rules;

    }

    public function property_types($market = 'residential') {

        $current_types = new WP_Term_Query(array(
            'taxonomy' => 'property_type',
            'hide_empty' => true,
            'fields' => 'names'
        ));
        
        $current_types = $current_types->get_terms() ?? array();

        switch($this->provider()) {
            case 'jupix':
                $provider_types = $this->property_types_jupix($market);
                break;
            default:
                $provider_types = array();
                break;
        }

        return array_values(array_intersect($provider_types, $current_types));

    }

    private function property_types_jupix($market = 'residential') {
        if($market === 'residential') {
            return array('Houses', 'Flats / Apartments', 'Bungalows', 'Other');
        } else {
            return array(
                'Offices',
                'Serviced Offices',
                'Business Park',
                'Science / Tech / R&D',
                'A1 - High Street',
                'A1 - Centre',
                'A1 - Out Of Town',
                'A1 - Other',
                'A2 - Financial Services',
                'A3 - Restaurants / Cafes',
                'A4 - Pubs / Bars / Clubs',
                'A5 - Take Away',
                'B1 - Light Industrial',
                'B2 - Heavy Industrial',
                'B8 - Warehouse / Distribution',
                'Science / Tech / R&D',
                'Other Industrial',
                'Caravan Park',
                'Cinema',
                'Golf Property',
                'Guest House / Hotel',
                'Leisure Park',
                'Leisure Other',
                'Day Nursery / Child Care',
                'Nursing & Care Homes',
                'Surgeries',
                'Petrol Stations',
                'Show Room',
                'Garage',
                'Industrial (land)',
                'Office (land)',
                'Residential (land)',
                'Retail (land)',
                'Leisure (land)',
                'Commercial / Other (land)',
                'Refurbishment Opportunities',
                'Residential Conversions',
                'Residential',
                'Commercial',
                'Ground Leases'
            );
        }

    }

}