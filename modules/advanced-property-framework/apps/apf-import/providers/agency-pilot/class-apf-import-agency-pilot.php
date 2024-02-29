<?php
/**
 * Agency Pilot Import Class
 * 
 * @package APF
 * @version 2.0
 */

class APFI_Agency_Pilot {

    private $user;
    private $client_id;
    private $client_secret;
    private $token;

    public function __construct() {
        
        $this->user = get_field('apf_provider_agency_pilot_user', 'option');
        $this->client_id = get_field('apf_provider_agency_pilot_client_id', 'option');
        $this->client_secret = get_field('apf_provider_agency_pilot_client_secret', 'option');

    }

    public function xml() {

        $properties = $this->fetchData();

        if($this->isDebug()) {
            echo '<pre>';
            print_r($properties);
            echo '</pre>';
            die();
        }

        header("Content-Type: application/xml; charset=utf-8");
        
        $xml_data = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><properties></properties>');

        APFI_Utils::array_to_xml($properties, $xml_data);

        $dom = dom_import_simplexml($xml_data)->ownerDocument;
        $dom = new DOMDocument('1.0');
        $dom->encoding = 'UTF-8';
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom->loadXML($xml_data->asXML());
        echo $dom->saveXML();
        
    }

    private function getToken() {

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://'.$this->user.'.agencypilot.com/api/version1_0_1/token',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => 'grant_type=client_credentials&client_id='.$this->client_id.'&client_secret='.$this->client_secret,
            CURLOPT_HTTPHEADER => array(
              "Content-Type: application/xwww-form-urlencoded",
              "Accept: application/json"
            ),
        ));

        $token = curl_exec($curl);
        $token = json_decode($token);

        if(!empty($token) && isset($token->access_token)) {
            return $token->access_token;
        }

        curl_close($curl);

        return null;

    }

    private function fetchData() {

        $properties = array();

        if($this->getToken()) {

            $curl = curl_init();

            $postFields = array(
                'DisplayOptions' => array(
                    'Additional' => true,
                    'Categories' => true,
                    'Photos' => true,
                    'DocumentMedia' => true,
                    'Floors' => true,
                    'Agents' => true,
                    'Auctions' => false,
                    'SystemDetails' => true
                ),
                'FilterOptions' => array(
                    'Statuses' => array(
                        14, // Available
                        5, // Let
                        4, // Sold
                        6, // Sold STC
                        7, // Under development
                        8, // Under offer
                    )
                )
            );

            // Get data
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://'.$this->user.'.agencypilot.com/api/version1_0_1/PropertyFeed/Property',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                // CURLOPT_POSTFIELDS =>"{\r\n\t\"DisplayOptions\":{\r\n\t\t\"Additional\": true,\r\n\t\t\"Categories\": true,\r\n\t\t\"Photos\": true,\r\n\t\t\"DocumentMedia\": true,\r\n\t\t\"Floors\": true,\r\n\t\t\"Agents\": true,\r\n\t\t\"Auctions\": false,\r\n\t\t\"SystemDetails\": true\r\n\t}\r\n}",
                CURLOPT_POSTFIELDS => json_encode($postFields),
                CURLOPT_HTTPHEADER => array(
                    'Authorization: bearer '.$this->getToken(),
                    'Content-Type: application/json',
                    'Accept: application/json',
                    ': '
                )
            ));

            $response = curl_exec($curl);
            curl_close($curl);

            if(!empty($response)) {

                $properties = json_decode($response);

                if(!$this->isDebug()) {
                    $properties = $this->restructureData($properties);
                }

                return $properties;

            }

        }

        return null;

    }

    /**
     * Gets existing data and restructures it
     * to suit our needs.
     * 
     * @param object $collection
     */
    private function restructureData($collection) {

        $properties = array();

        foreach($collection as $property) {

            $property_data = new stdClass;

            $property_data->PropertyID = $property->ID;
            $property_data->Market = $this->propertyMarket($property->Residential);
            $property_data->Department = $this->propertyDepartment($property->Tenure->ForSale);
            $property_data->BranchID = 'PH001'; // Hard-coded
            $property_data->Type = ucwords($property->Additional->BrochureHeader);
            $property_data->Status = $property->MarketStatus->Name;
            $property_data->Price = $this->propertyPrice($property);
            $property_data->Title = $property->Address->DisplayAddress;
            $property_data->Address = $this->propertyAddress($property->Address);
            $property_data->AddressFull = $property->Address;
            $property_data->Lat = $property->Address->Latitude;
            $property_data->Lng = $property->Address->Longitude;
            $property_data->MainImage = $this->propertyMainImage($property->Photos);
            $property_data->Images = $this->propertyImages($property->Photos);
            $property_data->Summary = $property->Description;
            $property_data->About = $this->propertyAbout($property->Additional->Info);
            $property_data->Features = $this->propertyFeatures($property->Additional->Bullets);
            $property_data->Brochure = $this->propertyBrochure($property->DocumentMedia);
            $property_data->EPC = $this->propertyEPC($property->DocumentMedia);
            $property_data->MinSize = $property->Size->MinSize;
            $property_data->MaxSize = $property->Size->MaxSize;
            
            array_push($properties, $property_data);

        }

        return $properties;

    }

    /**
     * Generate custom property market
     */
    private function propertyMarket($data) {

        if(!empty($data)) {
            return 'Residential';
        }

        return 'Commercial';

    }

    /**
     * Generate custom property department
     */
    private function propertyDepartment($data) {

        if($data) { 
            return 'For sale';
        }

        return 'To let';

    }

    /**
     * Generate custom property price
     */
    private function propertyPrice($property) {

        $price = '';

        if($property->Tenure->ForSale) {
            
            if($property->Tenure->ForSalePriceFrom) {
                $price = $property->Tenure->ForSalePriceFrom;
            } else {
                $price = $property->Tenure->ForSalePriceTo;
            }

        } else {

            if($property->Tenure->ForRentPriceFrom) {
                $price = $property->Tenure->ForRentPriceFrom;
            } else {
                $price = $property->Tenure->ForRentPriceTo;
            }

        }

        return $price;

    }

    /**
     * Generate custom property title
     */
    private function propertyTitle($data) {

        $propertyTitle = array();

        $Street = $data->Street ? array_push($propertyTitle, $data->Street) : '';
        $Town = $data->Town ? array_push($propertyTitle, $data->Town) : '';
        $Postcode = $data->Postcode ? explode(' ', $data->Postcode) : '';

        if(is_array($Postcode)) {
            array_push($propertyTitle, $Postcode[0]);
        }

        return join(', ', $propertyTitle);

    }

    /**
     * Generate custom property address
     */
    private function propertyAddress($propertyAddress) {

        $address = array(
            $propertyAddress->Street,
            $propertyAddress->Town,
            $propertyAddress->County,
            $propertyAddress->Postcode
        );

        return join(', ', $address);

    }

    /**
     * Generate custom property rooms
     */
    private function propertyRooms($data) {

        unset($data->DescriptionType);
        unset($data->Name);
        unset($data->Notes);

        return $data;

    }

    /**
     * Generate custom property amenities
     */
    private function propertyAmenities($data) {

        unset($data->DescriptionType);
        unset($data->AcreageMeasurementUnitType);
        unset($data->Name);
        unset($data->Notes);

        return $data;

    }

    /**
     * Generate custom property branch
     */
    private function propertyBranch($data) {

        $branch = new stdClass;

        $branch->ID = $data->Id;
        $branch->Name = $data->Name;
        $branch->Address = $data->ContactDetails->Addresses;

        if(is_array($data->ContactDetails->Addresses) && !empty($data->ContactDetails->Addresses)) {
            $data->ContactDetails->Addresses = reset($data->ContactDetails->Addresses);
            $branch->Address = $data->ContactDetails->Addresses->Address;
            unset($branch->Address->OrganizationName);
            unset($branch->Address->OrganizationName);
        }

        return $branch;

    }

    /**
     * Generates main property image
     */
    private function propertyMainImage($data) {

        if(is_array($data) && !empty($data)) {
            $image = reset($data);
            if($image->URLFullSize) {
                return $image->URLFullSize;
            }
        }

        return null;

    }

    /**
     * Generate custom array
     * of property images
     */
    private function propertyImages($data) {

        $images = array();

        if(is_array($data) && !empty($data)) {
            foreach($data as $image) {
                array_push($images, $image->URLFullSize);
            }
        }

        return $images;

    }

    /**
     * Generate Brochure
     */
    private function propertyBrochure($data) {

        if(!isset($data[0]) && isset($data[0]->URLs[0])) {
            return $data[0]->URLs[0];
        }

        return null;

    }

    /**
     * Generate EPC
     */
    private function propertyEPC($data) {

        if(!isset($data[1]) && isset($data[1]->URLs[0])) {
            return $data[1]->URLs[0];
        }

        return null;

    }

    /**
     * Generate floorplan
     */
    private function propertyFloorplans($data) {

        $floorplans = array();

        if(is_array($data) && !empty($data)) {
            foreach($data as $doc) {
                if($doc->DocumentSubType->DisplayName !== 'Floorplan') { continue; }

                if(!empty($doc->Url)) {
                    array_push($floorplans, trim($doc->Url));
                }
            }
        }

        return $floorplans;

    }

    /**
     * Generate About
     */
    private function propertyAbout($data) {

        $about = '';

        if(!empty($data) && is_array($data)) {
            foreach($data as $info) {
                if(!empty($info->Information)) {
                    $about .= htmlspecialchars_decode('<h4>'.$info->Description.'</h4>');
                    $about .= htmlspecialchars_decode($info->Information);
                }
            }
        }
        
        return $about;

    }

    /**
     * Generate Features
     */
    private function propertyFeatures($data) {

        $features = array();

        if(!empty($data) && is_array($data)) {
            foreach($data as $feature) {
                if($feature->BulletPoint) {
                    array_push($features, $feature->BulletPoint);
                }
            }
        }

        return $features;

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