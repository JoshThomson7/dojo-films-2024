<?php
/**
 * APF_Property
 *
 * Class in charge of property
 */

 // Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class APF_Property {

    /**
	 * The post ID.
	 *
	 * @since 1.0
	 * @access   private
	 * @var      string
	 */
    protected $id;
    
    /**
	 * Initialize the class and set its properties.
	 *
	 * @since 1.0
	 * @access public
	 * @param int $id
	 */
    public function __construct($id = null) {

        $this->id = $id;

    }

    /**
     * Gets post ID.
     * If not set, use global $post
     */
    public function id() {

        if($this->id) {

            return $this->id;

        } else {

            global $post;
            
            if(isset($post->ID)) {
                return $post->ID;
            }

        }

        return null;

    }

    /**
     * Returns post title
     */
    public function get_name($ref = false) {

        $name = get_the_title($this->id);

		if($ref) {
			$name .= ' <span class="apf-property-ref">'.$this->get_feed_id().')</span>';
		}

		return $name;


    }

    /**
     * Returns permalink
     */
    public function get_permalink() {

        return get_permalink($this->id);

    }

    /**
     * Returns date
     * 
     * @param string $format
     */
    public function date($format = 'M jS Y') {

        return get_the_time($format, $this->id);

    }

	/**
	 * Returns the feed ID
	 */
    public function get_feed_id() {
		
		return get_field('property_feed_id', $this->id);
		
	}

	/**
	 * Returns the branch ID
	 */
    public function get_branch_id() {
		
		return get_field('property_branch', $this->id);
		
	}

	/**
	 * Returns the number of receptions
	 */
    public function get_receptions() {
		
		return get_field('property_receptions', $this->id);
		
	}

	/**
	 * Returns the number of bedrooms
	 */
    public function get_bedrooms() {
		
		return get_field('property_bedrooms', $this->id);
		
	}

	/**
	 * Returns whether property is a studio
	 */
    public function is_studio() {
		
		return get_field('property_is_studio', $this->id);
		
	}

	/**
	 * Returns the number of bathrooms
	 */
    public function get_bathrooms() {
		
		return get_field('property_bathrooms', $this->id);
		
	}

	/**
	 * Returns property_edit_mode
	 * 
	 * @return string manual | feed
	 */
    public function get_price() {
		
		return get_field('property_price', $this->id);
		
	}

	/**
	 * Returns the feed ID
	 */
    public function get_edit_mode() {
		
		return get_field('property_edit_mode', $this->id);
		
	}

	/**
	 * Returns the property currency
	 */
    public function get_currency() {
		
		$currency = get_field('property_currency', $this->id);

		switch ($currency) {
			case 'GBP':
				$currency = '&pound;';
				break;

			case 'USD':
				$currency = '$';
				break;
			
			case 'EUR':
				$currency = '€';
				break;
			
			default:
				$currency = '&pound;';
				break;
		}

		return $currency;
		
	}

	/**
	 * Returns the text before the price
	 */
    public function get_price_before() {

		return get_field('property_price_before', $this->id);
		
	}

	/**
	 * Returns the property price frequency
	 */
    public function get_price_frequency() {

		if(has_term('to-let', 'property_department', $this->id)) {

			return get_field('property_price_frequency', $this->id);

		}

		return '';
		
	}

	/**
	 * Returns the property price qualifier
	 */
    public function get_price_qualifier() {
		
		$get_qualifier = get_field('property_price_qualifier', $this->id);

		switch ($get_qualifier) {
			case 'PA':
				$qualifier = 'POA';
				break;

			case 'GP':
				$qualifier = 'Guide price';
				break;
			
			case 'OE':
				$qualifier = 'Offers in excess of';
				break;
			
			default:
				$qualifier = $get_qualifier;
				break;
		}

		return $qualifier;
		
	}

	/**
	 * Returns the property price
	 * 
	 * @param bool $show_currency
	 * @param bool $show_period
	 * @param bool $html
	 * @param bool $echo
	 */
    public function get_price_html() {

		// Qualifier
		$get_qualifier = $this->get_price_qualifier();
		$price = $get_qualifier ? ' <small>'.$get_qualifier.'</small>' : '';

		$price .= '<div class="apf__price">';

		// Price before
		$get_price_before = $this->get_price_before();
		$price .= $get_price_before ? '<span class="apf__price__before">'.$get_price_before.'</span>' : '';

		// Currency
		$get_currency = $this->get_currency();
		$price .= $get_currency ? '<span class="apf__currency">'.$get_currency.'</span>' : '';

		// Price
		$get_price = (float)$this->get_price();
		$get_price = FL1_Helpers::has_decimals($get_price) ? number_format($get_price, 2) : number_format($get_price);
		$price .= $get_price ? '<span class="apf__digits">'.$get_price.'</span>' : '';
		
		// Period
		$get_period = $this->get_price_frequency();
		$price .= $get_period ? ' <span class="apf__period">'.$get_period.'</span>' : '';
		$price .= '</div>';
	
		return $price;

	}

	/**
	 * Returns property latitude
	 * 
	 * @return array
	 */
    public function get_latitude() {
		
		return get_field('property_latitude', $this->id);
		
	}

	/**
	 * Returns property longitude
	 * 
	 * @return array
	 */
    public function get_longitude() {
		
		return get_field('property_longitude', $this->id);
		
	}

	/**
	 * Returns property address
	 * 
	 * @return array
	 */
    public function get_address() {
		
		return get_field('property_address', $this->id) ?? array();
		
	}

	/**
	 * Returns searchable property address 
	 * 
	 * @return array
	 */
    public function get_address_searchable() {
		
		return get_field('property_address_searchable', $this->id);
		
	}

	/**
	 * Returns whether property has map
	 * 
	 * @return array
	 */
    public function has_map() {
		
		$lat = $this->get_latitude();
		$lng = $this->get_longitude();
		$address = $this->get_address();

		if(($lat && $lng) || !empty($address)) {
			return true;
		}

		return false;
		
	}

	/**
	 * Returns property summary
	 */
    public function get_summary() {
		
		return get_field('property_summary', $this->id);
		
	}

	/**
	 * Returns property description
	 */
    public function get_about() {
		
		return get_field('property_about', $this->id);
		
	}

	/**
	 * Returns property features
	 */
    public function get_features() {
		
		return get_field('property_features', $this->id) ?? array();
		
	}

	/**
	 * Returns attachment ID of main image
	 */
    public function get_main_image_id() {

		$field_name = $this->get_edit_mode() === 'manual' ? 'property_image_manual' : 'property_image';
		return get_field($field_name, $this->id);
		
	}

	/**
	 * Returns property gallery array
	 */
    public function get_gallery() {

		$field_name = $this->get_edit_mode() === 'manual' ? 'property_gallery_manual' : 'property_gallery';
		$get_gallery = get_field($field_name, $this->id) ?? array();

		$gallery = $get_gallery;
		if($this->get_edit_mode() === 'manual' && !empty($get_gallery)) {
			$gallery = array();			
			foreach($get_gallery as $image_id) {
				$image = vt_resize($image_id, null, 1200, null, true);
				if(!is_wp_error($image) && is_array($image) && isset($image['url'])) {
					$gallery[]['image_url'] = $image['url'];
				}
			}
		}

		return $gallery;
		
	}

    /**
     * Returns featured image.
     * 
     * @param int $width
     * @param int $height
     * @param bool $crop
	 * @param bool $echo
     * @see vt_resize() in modules/wp-image-resize.php
     */
    public function get_main_image($width = 900, $height = null, $crop = true) {
	
		if(get_post_thumbnail_id()) {

			$attachment_id = get_post_thumbnail_id();
			$image = vt_resize($attachment_id, null, $width, $height, $crop);
	
		} elseif($this->get_main_image_id()) {
			
			$image = $this->get_main_image_id();
			
			if($this->get_edit_mode() === 'manual') {
				$image = vt_resize($image, null, $width, $height, $crop);
				$image = is_array($image) && isset($image['url']) ? $image['url'] : false;
			}
	
		} elseif(!empty($this->get_gallery())) {
	
			$gallery = $this->get_gallery();
	
			if(!empty($gallery) && count($gallery) > 0) {
				$image = $gallery[0]['image_url'];
			}
	
		}

		if($image && !is_wp_error($image)) {
			return $image;
		}

		return false;
		
	}

	/**
	 * Returns property gallery array
	 */
    public function get_floorplans() {
		
		$field_name = $this->get_edit_mode() === 'manual' ? 'property_floorplans_manual' : 'property_floorplans';
		$get_floorplans = get_field($field_name, $this->id);

		$floorplans = $get_floorplans;
		if($this->get_edit_mode() === 'manual' && !empty($get_floorplans)) {
			$floorplans = array();			
			foreach($get_floorplans as $floorplan) {
				$image = vt_resize($floorplan['image_url'], null, 1200, null, true);
				if(!is_wp_error($image) && is_array($image) && isset($image['url'])) {
					$floorplans[]['image_url'] = $image['url'];
				}
			}
		}

		return $floorplans;
		
	}

	/**
	 * Returns property EPC
	 */
    public function get_epc() {
		
		$field_name = $this->get_edit_mode() === 'manual' ? 'property_epc_manual' : 'property_epc';
		$epc = get_field($field_name, $this->id);

		if(is_numeric($epc)) {
			$epc = wp_get_attachment_url($epc);
		}

		return $epc;
		
	}

	/**
	 * Returns property EPC
	 */
    public function get_brochure() {
		
		$field_name = $this->get_edit_mode() === 'manual' ? 'property_brochure_manual' : 'property_brochure';
		return get_field($field_name, $this->id);
		
	}

	/**
	 * Returns property video
	 */
    public function get_video() {
		
		return get_field('property_video', $this->id);
		
	}

	/**
	 * Returns property type
	 */
    public function get_type() {
		
		return get_field('property_type', $this->id);
		
	}

	/**
	 * Returns property style
	 */
    public function get_style() {
		
		return get_field('property_style', $this->id);
		
	}

	/**
	 * Returns property status
	 */
    public function get_status() {
		
		return get_field('property_status', $this->id);
		
	}

	/**
	 * Returns property status HTML
	 */
    public function get_status_html() {

		$status = $this->get_status();
		
		switch($status) {

            case 'Available':
                $color = '';
                $property_status = '';
                break;

            case 'Let':
				$color = ' apf__status__red';
                $property_status = 'Let';
                break;

            case 'Let Agreed':
            case 'Let agreed':
                $color = ' apf__status__red';
                $property_status = 'Let Agreed';
                break;

            case 'Sold':
                $color = ' apf__status__red';
                $property_status = 'Sold';
                break;
			
			case 'Sold STC':
            case 'Sold Subject to Contract':
                $color = ' apf__status__red';
                $property_status = 'Sold STC';
                break;

            case 'Under Offer':
            case 'Under offer':
            case 'under offer':
                $color = ' apf__status__amber';
                $property_status = 'Under Offer';
                break;

            case 'Under Development':
                $color = ' apf__status__amber';
                $property_status = 'Under Development';
                break;

			default:
				$color = ' apf__status__grey';
				$property_status = $status;
				break;

        }

		return $property_status ? '<span class="apf__property__status'.$color.'">'.$property_status.'</span>' : '';
		
	}

	/**
	 * Returns whether property is featured
	 */
    public function is_featured() {
		
		return get_field('property_featured', $this->id);
		
	}

	/**
	 * Returns whether property is a new home
	 */
    public function is_new_home() {
		
		return get_field('property_new_home', $this->id);
		
	}

	/**
	 * Returns if property is for sale
	 */
	public function is_for_sale() {
		return has_term('for-sale', 'property_department', $this->id);
	}

	/**
	 * Returns if property is to let
	 */
	public function is_to_let() {
		return has_term('to-let', 'property_department', $this->id);
	}

	/**
	 * Returns a formatted 
	 */
	public function get_seo_title() {

		$title = '';

		$property_beds = $this->get_bedrooms();
		$title .= $property_beds ? $property_beds.'-bed ' : '';

		$property_type = $this->get_type();
		$title .= $property_type ? strtolower($property_type).' ' : 'property ';

		if($this->is_studio()) {
			$title = 'Studio ';
		}

		$property_branch = 'for sale';
		
		if($this->is_to_let()) {
			$property_branch = 'to let';
		}

		$title .= $property_branch ? $property_branch.' ' : '';

		return $title;

	}

	/**
	 * Returns property markets
	 */
	public function get_markets($return = null) {
		
		return FL1_Helpers::get_terms($this->id, 'property_market', $return);

	}

	/**
	 * Returns property departments
	 */
	public function get_departments($return = null) {
		
		return FL1_Helpers::get_terms($this->id, 'property_department', $return);

	}

	/**
	 * Returns property areas
	 */
	public function get_areas($return = null) {
		
		return FL1_Helpers::get_terms($this->id, 'property_area', $return);

	}

	/**
	 * Is student property
	 */
	public function is_student() {

		return has_term('student', 'property_market', $this->id);

	}

	/*--------------------------------------*
	 * 
	 * Setters
	 * 
	 *-------------------------------------*/

	/**
	 * Sets the property address
	 * @param string $address
	 */
	public function set_address_searchable($address) {
		update_field('property_address_searchable', $address, $this->id);
	}

	/**
	 * Sets the latitude
	 * @param string $lat
	 */
	public function set_latitude($lat) {
		update_field('property_latitude', $lat, $this->id);
	}

	/**
	 * Sets the longitude
	 * @param string $lng
	 */
	public function set_longitude($lng) {
		update_field('property_longitude', $lng, $this->id);
	}

}

