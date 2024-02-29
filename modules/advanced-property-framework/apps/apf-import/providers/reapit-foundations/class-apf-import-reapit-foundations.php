<?php
/**
 * Reapit Import Class
 * 
 * @package APF
 * @version 2.0
 */

class APFI_Reapit_Foundations {

    private $client_id;
    private $secret;
    private $reapit_customer;
    private $office_ids;
    private $marketing_mode;
    private $selling;
	private $letting;
    private $token_endpoint = 'https://connect.reapit.cloud/token';
    private $properties_endpoint = 'https://platform.reapit.cloud/properties/';
    private $token;
    private $token_expiry;

    public function __construct() {
        
        $this->client_id = get_field('apf_provider_reapit_foundations_client_id', 'option');
        $this->secret = get_field('apf_provider_reapit_foundations_secret', 'option');
        $this->reapit_customer = get_field('apf_provider_reapit_foundations_reapit_customer', 'option') ?? 'SBOX';
        $this->marketing_mode = get_field('apf_provider_reapit_foundations_marketing_mode', 'option') ?? array('selling');
        $this->selling = get_field('apf_provider_reapit_foundations_selling', 'option') ?? array();
		$this->letting = get_field('apf_provider_reapit_foundations_letting', 'option') ?? array();

    }

    public function xml() {

        //sg_cachepress_purge_everything();

        $this->token = $this->getToken();
        $properties = $this->getPropertyCollection();

        header("Content-Type: application/xml; charset=utf-8");
        header("Expires: on, 01 Jan 1970 00:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");

        $xml_data = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><properties></properties>');

        APFI_Utils::array_to_xml($properties, $xml_data, '', 'reapit-foundations');

        $dom = dom_import_simplexml($xml_data)->ownerDocument;
        $dom = new DOMDocument('1.0');
        $dom->encoding = 'UTF-8';
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom->loadXML($xml_data->asXML());
        echo $dom->saveXML();

    }

    /**
     * Get the token from the Reapit API
     * 
     * @param array $custom_args
     * @return string
     */
    private function getToken() {

        $this->token = get_field('apf_provider_reapit_foundations_token', 'option') ?? '';
        $this->token_expiry = get_field('apf_provider_reapit_foundations_token_expiry', 'option') ?? '';

        if($this->token) {
            $now = new DateTime('now', wp_timezone());
            $token_expiry = new DateTime($this->token_expiry, wp_timezone());

            if($now <= $token_expiry) {
                return $this->token;
            }
        }

        $args = array(
            'method' => 'POST',
            'headers' => array(
                'Content-type: application/x-www-form-urlencoded',
                'Authorization' => 'Basic '.base64_encode($this->client_id.':'.$this->secret)
            ),
            'body' => array(
                'client_id' => $this->client_id,
                'grant_type' => 'client_credentials'
            )
        );
    
        $request = wp_remote_post($this->token_endpoint, $args);
        if(is_wp_error($request)) { return false; }
    
        $body = wp_remote_retrieve_body($request);
        $body = json_decode($body);
    
        if(property_exists($body, 'error')) {
            return $body->error;
        }
    
        if(!property_exists($body, 'access_token')) { 
            return 'No token returned';
        }

        $now = new DateTime('now', wp_timezone());
        $token_expiry = $body->expires_in;
        $token_expiry = $now->add(new DateInterval('PT'.$token_expiry.'S'));
        $token_expiry = $token_expiry->format('Y-m-d H:i:s');
        update_field('apf_provider_reapit_foundations_token_expiry', $token_expiry, 'option');
        update_field('apf_provider_reapit_foundations_token', $body->access_token, 'option');
    
        return $body->access_token;

    }

    /**
     * Get the properties from the Reapit API
     */
    private function getPropertyCollection($custom_args = array()) {

        $property_collection = array();

		if(empty($this->marketing_mode)) return $property_collection;

		foreach($this->marketing_mode as $marketing_mode) {

			$department = $marketing_mode === 'selling' ? $this->selling : $this->letting;
			$internet_advertising = $department['internet_advertising'] ? 'true' : 'false';
			$statuses = $department['statuses'] ? APFI_Utils::commasep_to_array($department['statuses']) : array();
			$office_ids = $department['office_ids'] ? APFI_Utils::commasep_to_array($department['office_ids']) : array();

			$args = array(
				'marketingMode' => $marketing_mode,
				'pageSize' => '100',
				'pageNumber' => '1',
				'embed' => 'images',
				'internetAdvertising' => $internet_advertising,
				'isExternal' => 'false'
			);

			if($marketing_mode === 'selling' && !empty($statuses)) {
				$args['sellingStatus'] = $statuses;
			}

			if($marketing_mode === 'letting' && !empty($statuses)) {
				$args['lettingStatus'] = $statuses;
			}

			if(!empty($office_ids)) {
				$args['officeId'] = $office_ids;
			}

			$properties = $this->getProperties($args);
			
			if(property_exists($properties, 'totalPageCount')) {

				$property_pages = $properties->totalPageCount;
				$properties = array_values($properties->_embedded);
				$properties = $this->cleanUpProperties($properties) ?? array();
				array_push($property_collection, ...$properties);
				
				if($property_pages > 1) {
					for ($i = 2; $i <= $property_pages; $i++) {

						$args2 = $args;
						$args2['pageNumber'] = $i;

						// if($marketing_mode === 'selling' && $this->statuses) {
						// 	$args2['sellingStatus'] = APFI_Utils::commasep_to_array($this->statuses);
						// }

						// if($marketing_mode === 'letting' && $this->statuses_lettings) {
						// 	$args2['lettingStatus'] = APFI_Utils::commasep_to_array($this->statuses_lettings);
						// }
				
						// if($this->office_ids) {
						// 	$args2['officeId'] = APFI_Utils::commasep_to_array($this->office_ids);
						// }

						$more_properties = $this->getProperties($args2);
						$more_properties = $more_properties->_embedded;
						$more_properties = $this->cleanUpProperties($more_properties);

						array_push($property_collection, ...$more_properties);
					
					}
				}

			}

		}

		// pretty_print($property_collection);
		// die();
        
        return $property_collection;
    
    }

    /**
     * Get the properties from the Reapit API
     * 
     * @param array $custom_args
     * @return array
     */
    private function getProperties($custom_args = array(), $pluck = '') {

		$query_string = APFI_Utils::query_string($custom_args);
        $url = $this->properties_endpoint.'?'.$query_string;

		//echo $url.'<br/>';

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'accept: application/json',
                'api-version: 2020-01-31',
                'reapit-customer: '.$this->reapit_customer,
                'Authorization: Bearer '.$this->token
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        $response = json_decode($response);

		// pretty_print($response);
		// die();

        if(!property_exists($response, '_embedded')) {
            echo 'No properties found';
            die();
        }

        if($pluck) {
            return property_exists($response, $pluck) ? $response->$pluck : $response;
        }

        return $response;
    
    }

	private function cleanUpProperties($properties) {

		if(empty($properties)) return false;

		$property_collection = array();

		foreach($properties as $property) {

			$the_property = new stdClass();

			$mode = $property->marketingMode === 'selling' ? $property->selling : $property->letting;

			$the_property->title = $this->propertyTitle($property->address);
			$the_property->market = $this->market($property->specialFeatures);
			$the_property->feed_provider = 'reapit-foundations';
			$the_property->feed_property_id = $property->id;
			$the_property->branch_id = $this->branchID($property->officeIds);
			$the_property->department = $this->department($property->marketingMode);
			$the_property->area = $this->area($property->address);
;
			$the_property->price = $this->price($property->marketingMode, $mode);
			$the_property->price_qualifier = $this->price_qualifier($mode->qualifier);
			$the_property->price_frequency = $property->marketingMode === 'letting' ? $this->price_frequency($mode->rentFrequency) : '';

			$the_property->receptions = $property->receptions;
			$the_property->bedrooms = $property->bedrooms;
			$the_property->is_studio = $this->is_studio($property->bedrooms);
			$the_property->bathrooms = $property->bathrooms;
			$the_property->type = $this->type($property->type);
			$the_property->status = $this->status($mode->status);
			$the_property->address_searchable = $this->address_searchable($property->address);
			$the_property->latitude = $this->latitude($property->address);
			$the_property->longitude = $this->longitude($property->address);
			$the_property->summary = $property->description;
			$the_property->features = $this->features($property->summary);
			$the_property->about = nl2br($property->longDescription);
			$the_property->edit_mode = 'feed';
			$the_property->mainImage = $property->featuredImageUrl;

			$media = $this->media($property->_embedded->images);

			$the_property->gallery = isset($media['images']) ? $media['images'] : array();
			$the_property->floorplans = isset($media['floorplans']) ? $media['floorplans'] : array();
			$the_property->epcs = isset($media['epcs']) ? $media['epcs'] : array();
			$the_property->brochure = $mode->publicBrochureUrl;
			$the_property->video = $property->videoUrl;
			
			array_push($property_collection, $the_property);

		}

		return $property_collection;

	}

	private function propertyTitle($address) {

		if(empty($address)) return false;

		$components = [];

		if (!empty($address->line1)) {
			$components[] = $address->line1;
		}

		if (!empty($address->postcode)) {
			// get first part of postcode
			$postcode = explode(' ', $address->postcode);
			$postcode = is_array($postcode) ? $postcode[0] : $postcode;
			$components[] = $postcode;
		}

		$components = implode(', ', $components);
		$components = str_replace(',,', ',', $components);

		return $components;

	}

	private function market($features) {

		$market = 'Residential';

		if(in_array('Students', $features)) {
			$market = 'Student';
		}

		return $market;

	}

	private function branchID($branch_ids) {

		if(empty($branch_ids)) return false;
		return is_array($branch_ids) ? $branch_ids[0] : null;

	}

	private function department($marketingMode) {

		if($marketingMode === 'selling') {
			return 'For sale';
		} else {
			return 'To let';
		}

	}

	private function area($address) {

		if($address->line3) {
			return $address->line3;
		} else if($address->line4) {
			return $address->line4;
		} else if($address->postcode) {
			$postcode = explode(' ', $address->postcode);
			$postcode = is_array($postcode) ? $postcode[0] : $postcode;
			return $postcode;
		}

	}

	private function price($mode, $data) {

		$price = '';

		switch ($mode) {
			case 'selling':
				if($data->price && $data->price !== '') {
					$price = $data->price;
				}
				break;

			case 'letting':
			default:
				if($data->rent && $data->rent !== '') { 
					$price = $data->rent;
				}
				break;
		}

		return $price;

	}

	private function price_qualifier($qualifier) {

		if(!$qualifier || $qualifier === '') return false;
		
		switch ($qualifier) {
			case 'guidePrice':
				return 'Guide price';
				break;
			case 'offersInExcess':
				return 'Offers in excess of';
				break;
			case 'offersInRegion':
				return 'Offers in region of';
				break;
			case 'offersOver':
				return 'Offers over';
				break;
			case 'askingPrice':
			case 'fixedPrice':
			case 'askingRent':
				return '';
				break;
			default:
				return '';
		}

	}

	private function price_frequency($frequency) {

		switch ($frequency) {
			case 'weekly':
				return 'pw';
				break;

			case 'monthly':
			default:
				return 'pcm';
				break;
		}

	}

	private function is_studio($beds) {
		return !$beds || $beds == 0 || $beds === '' ? true : false;
	}

	private function type($type) {

		if(empty($type)) return false;
		
		switch ($type) {
            case 'house':
                return 'house';
                break;
            case 'bungalow':
                return 'bungalow';
                break;
            case 'flatApartment':
                return 'flat';
                break;
            case 'maisonette':
                return 'maisonette';
                break;
            case 'land':
                return 'land';
                break;
            case 'farm':
                return 'farm';
                break;
            case 'cottage':
                return 'cottage';
                break;
            case 'studio':
                return 'studio';
                break;
            case 'townhouse':
                return 'town house';
                break;
            case 'developmentPlot':
                return 'development plot';
                break;
            default:
                return 'property';
        }

	}

	/**
	 * Status
	 * 
	 * @param object $selling
	 * @see https://github.com/reapit/foundations/issues/2906
	 * 
	 * Sales:
	 * forSale,underOffer,underOfferUnavailable,exchanged,forSaleUnavailable,soldExternally,reserved,completed,withdrawn
	 * 
	 * Lettings:
	 * offerPending,offerWithdrawn,offerRejected,arranging,current,finished,cancelled,toLet 
	 * 
	 * - If a tenant makes an offer to rent a property not at the advertised terms (eg. less rent), the 'offerPending' status would be initially set.
	 * - The landlord can reject the offer (offerRejected) or the tenant can decide to later withdraw that offer (offerWithdrawn).
	 * - If the offer is accepted, the tenancy moves to 'arranging' status. This it when the checks and processes required to let a property take place.
	 * - If something goes wrong at this stage, 'cancelled' is set.
	 * - Once complete and the tenancy has started, we use the 'current' status.
	 * - When the tenancy has ended, 'finished' is set
	 */
	private function status($status) {

		if(!$status) return false;
		
		switch($status) {

            case 'forSaleUnavailable':
                return 'Sold STC';
                break;

            case 'underOfferUnavailable':
			case 'exchanged':
			case 'soldExternally':
                return 'Sold';
                break;

			case 'underOffer':
				return 'Under Offer';
				break;

            case 'reserved':
                return 'Reserved';
                break;

            case 'completed':
                return 'Completed';
                break;

            case 'withdrawn':
                return 'Withdrawn';
                break;

            case 'toLet':
            default:
				return '';
				break;
        }

    }

	private function address_searchable($address) {

		if(empty($address)) return false;

		$components = [];

		if (!empty($address->buildingName)) {
			$components[] = $address->buildingName;
		}

		if (!empty($address->buildingNumber)) {
			$components[] = $address->buildingNumber;
		}

		if (!empty($address->line1)) {
			$components[] = $address->line1;
		}

		if (!empty($address->line2)) {
			$components[] = $address->line2;
		}

		if (!empty($address->line3)) {
			$components[] = $address->line3;
		}

		if (!empty($address->line4)) {
			$components[] = $address->line4;
		}

		if (!empty($address->postcode)) {
			$components[] = $address->postcode;
		}

		return implode(', ', $components);

	}

	private function latitude($address) {

		return $address->geolocation->latitude ? $address->geolocation->latitude : false;

	}

	private function longitude($address) {

		return $address->geolocation->longitude ? $address->geolocation->longitude : false;

	}

	private function features($summary) {

		if(!$summary) return false;

		$_summary = str_replace("•","", $summary);
		$_summary = str_replace("\r","\n", $summary);
        $_summary = explode(PHP_EOL, $_summary);
		return $_summary;

	}

	private function media($images) {

		if(empty($images)) return false;

		$media = array(
			'floorplans' => array(),
			'epcs' => array(),
			'images' => array()
		);
		
		foreach($images as $image_value) {
			if($image_value->type === 'floorPlan') { 
				$media['floorplans'][] = $image_value;
			}

			if($image_value->type === 'epc') { 
				$media['epcs'][] = $image_value;
			}
			
			if($image_value->type === 'photograph') { 
				$media['images'][] = $image_value;
			}
		}

		if(!empty($media['floorplans'])) { 
			usort($media['floorplans'], function ($item1, $item2) {
				return $item1->order <=> $item2->order;
			});
		}
		
		if(!empty($media['epcs'])) {
			usort($media['epcs'], function ($item1, $item2) {
				return $item1->order <=> $item2->order;
			});
		}

		if(!empty($media['images'])) { 
			usort($media['images'], function ($item1, $item2) {
				return $item1->order <=> $item2->order;
			});
		}

		return $media;

	}

    /**
     * Converts office IDs to an array
     * 
     * @return array
     */
    private function officeIds() {

        if(!$this->office_ids) {
            return false;
        }

        return explode(',', $this->office_ids);

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

}