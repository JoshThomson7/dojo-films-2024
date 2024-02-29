<?php
/**
 * Agency Pilot Import Class
 * 
 * @package APF
 * @version 2.0
 */

class APFI_RTFD {

    private $xml;
    private $json;
    private $property;

    public function xml() {

        global $wp_query;

        $payload = file_get_contents('php://input');

        if(!$payload) { return; }

        $this->xml = simplexml_load_string($payload, "SimpleXMLElement", LIBXML_NOCDATA);
        $this->json = json_encode($this->xml);
        $this->property = json_decode($this->json, TRUE);

        // if(isset($wp_query->query_vars['SendProperty'])) {
        //     $this->add_property();
        // }

        echo '<pre>';
        print_r($this->restructure_data());
        echo '</pre>';

    }

    private function add_property() {

        $this->restructure_data();

        $args = array(
            'post_type' => 'property',
            'post_status' => 'publish',
            'post_title' => wp_strip_all_tags($this->property['address']['display_address']),
        );

        $property_exists = $this->property_exists();
        if($property_exists) {
            $args['ID'] = $property_exists;
        }

        $id = wp_insert_post($args);

        if($id) {
            update_field('property_feed_id', $this->property['agent_ref'], $id);
            update_field('property_branch', $this->property['branch_id'], $id);
            update_field('property_price', $this->property['price_information']['price'], $id);
            update_field('property_price_qualifier', $this->property['price_information']['price_qualifier'], $id);
            update_field('property_receptions', $this->property['details']['reception_rooms'], $id);
            update_field('property_receptions', $this->property['details']['reception_rooms'], $id);
            update_field('property_bedrooms', $this->property['details']['bedrooms'], $id);
            update_field('property_bathrooms', $this->property['details']['bathrooms'], $id);
            update_field('property_address', $this->property['address']['address_flat'], $id); // To do
            update_field('property_address_searchable', $this->property['address']['address_flat'], $id);
            update_field('property_summary', apply_filters('the_content', $this->property['details']['summary']), $id);
            update_field('property_about', apply_filters('the_content', $this->property['details']['description']), $id);
            update_field('property_features', $this->property['details']['property_features'], $id);
            update_field('property_image', $this->property['property_image'], $id);
            update_field('property_gallery', $this->property['property_gallery'], $id);
            update_field('property_floorplans', $this->property['property_floorplans'], $id);
            update_field('property_epc', $this->property['epc'], $id);
            update_field('property_brochure', '', $id);
            update_field('property_video', '', $id);
            update_field('property_type', $this->property['property_type'], $id);
            update_field('property_status', $this->property['status'], $id);
            update_field('property_featured', 0, $id);
            update_field('property_new_home', 0, $id);
        }

    }

    private function restructure_data() {

        $this->branch_id();
        $this->type();
        $this->status();
        $this->address_flat();
        $this->media();
        $this->features();

        $this->property = $this->property['property'];
        
    }

    private function branch_id() {

        $branch_id = $this->property['branch']['branch_id'] ?? '';
        $this->property['property']['branch_id'] = $branch_id;

    }

    private function address_flat() {

        $address = $this->property['property']['address'] ?? array();
        $address_flat = '';
        if(!empty($address)) {
            foreach($address as $address_idx => $address_data) {
                if($address_idx !== 'display_address' && $address_data) {
                    $address_flat .= ' '.$address_data;
                }
            }
        }

        $this->property['property']['address']['address_flat'] = trim($address_flat);

    }

    private function media() {

        $images = array();
        $floorplans = array();
        $epc = '';

        $media = $this->property['property']['media'] ?? array();

        if(!empty($media)) {

            foreach($media as $media_idx => $media_data) {
                
                switch ($media_data['media_type']) {
                    case 2: // Floorplan
                        array_push($floorplans, array('property_floorplan_image_url' => $media_data['media_url']));
                        break;

                    case 7: // EPC
                        $epc = $media_data['media_url'];
                        break;
                    
                    default: // Images
                        if($media_idx < 1) {
                            $this->property['property']['property_image'] = $media_data['media_url'];
                        }
                        array_push($images, array('property_gallery_img_url' => $media_data['media_url']));
                        break;
                }

            }

        }

        $this->property['property']['property_gallery'] = $images;
        $this->property['property']['property_floorplans'] = $floorplans;
        $this->property['property']['epc'] = $epc;

        unset($this->property['property']['media']);

    }

    private function features() {

        $get_features = $this->property['property']['details']['features'] ?? array();
        $features = array();
        if(!empty($get_features)) {
            foreach($get_features as $feature) {
                $features[]['property_feature'] = trim($feature);
            }
        }

        $this->property['property']['details']['property_features'] = $features;
        unset($this->property['property']['details']['features']);

    }

    private function type() {

        $get_type = $this->property['property']['property_type'] ?? null;
        $type = '';

        if($get_type) {
            switch($get_type){
                case 0:
                    $type = 'Not Specified';
                    break;
                case 1:
                    $type = 'Terraced';
                    break;
                case 2:
                    $type = 'End of Terrace';
                    break;
                case 3:
                    $type = 'Semi-Detached';
                    break;
                case 4:
                    $type = 'Detached';
                    break;
                case 5:
                    $type = 'Mews';
                    break;
                case 6:
                    $type = 'Cluster House';
                    break;
                case 7:
                    $type = 'Ground Flat';
                    break;
                case 8:
                    $type = 'Flat';
                    break;
                case 9:
                    $type = 'Studio';
                    break;
                case 10:
                    $type = 'Ground Maisonette';
                    break;
                case 11:
                    $type = 'Maisonette';
                    break;
                case 12:
                    $type = 'Bungalow';
                    break;
                case 13:
                    $type = 'Terraced Bungalow';
                    break;
                case 14:
                    $type = 'Semi-Detached Bungalow';
                    break;
                case 15:
                    $type = 'Detached Bungalow';
                    break;
                case 16:
                    $type = 'Mobile Home';
                    break;
                case 17:
                    $type = 'Hotel';
                    break;
                case 18:
                    $type = 'Guest House';
                    break;
                case 19:
                    $type = 'Commercial Property';
                    break;
                case 20:
                    $type = 'Land';
                    break;
                case 21:
                    $type = 'Link Detached House';
                    break;
                case 22:
                    $type = 'Town House';
                    break;
                case 23:
                    $type = 'Cottage';
                    break;
                case 24:
                    $type = 'Chalet';
                    break;
                case 27:
                    $type = 'Villa';
                    break;
                case 28:
                    $type = 'Apartment';
                    break;
                case 29:
                    $type = 'Penthouse';
                    break;
                case 30:
                    $type = 'Finca';
                    break;
                case 43:
                    $type = 'Barn Conversion';
                    break;
                case 44:
                    $type = 'Serviced Apartments';
                    break;
                case 45:
                    $type = 'Parking';
                    break;
                case 46:
                    $type = 'Sheltered Housing';
                    break;
                case 47:
                    $type = 'Retirement Property';
                    break;
                case 48:
                    $type = 'House Share';
                    break;
                case 49:
                    $type = 'Flat Share';
                    break;
                case 50:
                    $type = 'Park Home';
                    break;
                case 51:
                    $type = 'Garages';
                    break;
                case 52:
                    $type = 'Farm House';
                    break;
                case 53:
                    $type = 'Equestrian';
                    break;
                case 56:
                    $type = 'Duplex';
                    break;
                case 59:
                    $type = 'Triplex';
                    break;
                case 62:
                    $type = 'Longere';
                    break;
                case 65:
                    $type = 'Gite';
                    break;
                case 68:
                    $type = 'Barn';
                    break;
                case 71:
                    $type = 'Trulli';
                    break;
                case 74:
                    $type = 'Mill';
                    break;
                case 77:
                    $type = 'Ruins';
                    break;
                case 80:
                    $type = 'Restaurant';
                    break;
                case 83:
                    $type = 'Cafe';
                    break;
                case 86:
                    $type = 'Mill';
                    break;
                case 89:
                    $type = 'Trulli';
                    break;
                case 92:
                    $type = 'Castle';
                    break;
                case 95:
                    $type = 'Village House';
                    break;
                case 101:
                    $type = 'Cave House';
                    break;
                case 104:
                    $type = 'Cortijo';
                    break;
                case 107:
                    $type = 'Farm Land';
                    break;
                case 110:
                    $type = 'Plot';
                    break;
                case 113:
                    $type = 'Country House';
                    break;
                case 116:
                    $type = 'Stone House';
                    break;
                case 117:
                    $type = 'Caravan';
                    break;
                case 118:
                    $type = 'Lodge';
                    break;
                case 119:
                    $type = 'Log Cabin';
                    break;
                case 120:
                    $type = 'Manor House';
                    break;
                case 121:
                    $type = 'Stately Home';
                    break;
                case 125:
                    $type = 'Off-Plan';
                    break;
                case 128:
                    $type = 'Semi-detached Villa';
                    break;
                case 131:
                    $type = 'Detached Villa';
                    break;
                case 134:
                    $type = 'Bar';
                    break;
                case 137:
                    $type = 'Shop';
                    break;
                case 140:
                    $type = 'Riad';
                    break;
                case 141:
                    $type = 'House Boat';
                    break;
                case 142:
                    $type = 'Hotel Room';
                    break;
            }

            $this->property['property']['property_type'] = $type;

        }

    }

    private function status() {

        $get_status = $this->property['property']['status'] ?? null;
        $status = '';

        if($get_status) {
            switch($get_status){
                case 0:
                    $status = 'Available';
                    break;
                case 1:
                    $status = 'Sold STC';
                    break;
                case 2:
                    $status = 'Sold STCM';
                    break;
                case 3:
                    $status = 'Under Offer';
                    break;
                case 4:
                    $status = 'Reserved';
                    break;
                case 5:
                    $status = 'Let Agreed';
                    break;
                case 6:
                    $status = 'Sold';
                    break;
                case 7:
                    $status = 'Let';
                    break;
            }

            $this->property['property']['status'] = $status;

        }

    }

    private function property_exists() {

        $agent_ref = $this->property['property']['agent_ref'] ?? null;

        $args = array(
            'post_type' => 'property',
            'post_status' => 'publish',
            'meta_query' => array(
                array(
                    'key' => 'agent_ref',
                    'value' => $agent_ref,
                    'compare' => '='
                )
            ),
            'posts_per_page' => 1,
            'fields' => 'ids'
        );

        $query = new WP_Query($args);
        $posts = $query->posts;

        return !empty($posts) ? reset($posts) : 0;

    }

    /**
     * Check if in debug mode
     */
    private function isDebug() {
        if(isset($_GET['debug']) && !empty($_GET['debug']) && $_GET['debug'] === 'true') {
            return true;
        }

        return false;
    }

    /**
	 * Register core endpoints.
	 *
	 * @since 1.0
	 */
	public function register_enpoints() {

        add_rewrite_endpoint('SendProperty', EP_PAGES);
        add_filter( 'request', array($this, 'filter_var_request'));

    }

    /**
	 * Filter endpoint request.
	 *
	 * If nothing follows the endpoint, your query var will be empty (but set), so it will always evaluate as false when you try to catch it.
     * You can get around this by filtering 'request' and changing the value of your endpoint variables to true if they are set.
     *
     * @since 1.0
	 */
	public function filter_var_request($vars) {

        if( isset( $vars['SendProperty'] ) ) $vars['SendProperty'] = true;
        return $vars;

    }

}