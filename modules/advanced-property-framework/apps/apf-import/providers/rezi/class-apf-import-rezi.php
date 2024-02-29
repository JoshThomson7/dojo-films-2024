<?php
/**
 * Rezi Import Class
 * 
 * @package APF
 * @version 2.0
 */

class APFI_Rezi {

    public function apiKey() {
        return get_field('apf_provider_rezi_api_key', 'option');
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
        $dom = new DOMDocument("1.0");
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom->loadXML($xml_data->asXML());
        $dom->save(APF_XML_PATH . 'rezi.xml');

        echo 'XML successfully generated in '.APFI_Utils::executionTime().' seconds :)';
        
    }

    private function fetchData() {

        $properties = array();

        $searchURL = 'https://api.dezrez.com/api/simplepropertyrole/search?APIKey='.$this->apiKey();
        $search_parameters = json_encode(
            array(
                'BranchIdList' => '',
                'MinimumPrice' => 0,
                'MaximumPrice' => 9999999,
                'MarketingFlags' => 'ApprovedForMarketingWebsite',
                'PageSize' => 9999999,
                'IncludeStc' => true
            )
        );

        // CURL it up
        $curlResponse = '';
        $curlRetries = 0;

        while(empty($curlResponse) && $curlRetries < 3) {
            $curlHandle = curl_init($searchURL.$API_key);
            curl_setopt($curlHandle, CURLOPT_POST, true);
            curl_setopt($curlHandle, CURLOPT_POSTFIELDS, $search_parameters);
            curl_setopt($curlHandle, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Rezi-Api-Version: 1.0')
            );
            curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, 1);
            $curlResponse = curl_exec($curlHandle);
            echo curl_error($curlHandle);
            curl_close($curlHandle);
            $curlRetries++;
        }

        if(!empty($curlResponse)) {

            $collection = json_decode($curlResponse);
            $collection = $collection->Collection;

            if(!empty($collection)) {
                if(!$this->isDebug()) {
                    $properties = $this->restructureData($collection);
                } else {
                    return $collection;
                }
            }

        }

        return $properties;

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

            $property_data->PropertyID = $property->PropertyId;
            $property_data->Market = $this->propertyMarket($property->PropertyType->SystemName);
            $property_data->Department = $this->propertyDepartment($property->Price);
            $property_data->Type = $property->PropertyType->DisplayName;
            $property_data->Status = $this->propertyStatus($property->RoleStatus->SystemName, $property_data->Department);
            $property_data->Price = $this->propertyPrice($property->Price);
            $property_data->Title = $this->propertyTitle($property->Address);
            $property_data->Address = $this->propertyAddress($property->Address);
            $property_data->Rooms = $this->propertyRooms($property->RoomCountsDescription);
            $property_data->Amenities = $this->propertyAmenities($property->AmenityDescription);
            $property_data->Branch = $this->propertyBranch($property->BranchDetails);
            $property_data->Images = $this->propertyImages($property->Images);
            $property_data->Description = $property->SummaryTextDescription;
            $property_data->EPC = $this->propertyEPC($property->EPC);
            $property_data->Floorplans = $this->propertyFloorplans($property->Documents);
            $property_data->DateInstructed = $property->DateInstructed;
            $property_data->LastUpdated = $property->LastUpdated;
            
            array_push($properties, $property_data);

        }

        return $properties;

    }

    /**
     * Generate custom property market
     */
    private function propertyMarket($data) {

        if($data) {

            $types = array(
                'Office',
                'Shop',
                'HighStreetRetailProperty'
            );

            if(in_array($data, $types)) {
                return 'Commercial';
            }

        }

        return 'Residential';

    }

    /**
     * Generate custom property department
     */
    private function propertyDepartment($data) {

        if(!empty($data->PriceType)) { 
            return 'To let';
        }

        return 'For sale';

    }

    /**
     * Generates status from givne data
     */
    private function propertyStatus($data, $department) {

        $isSales = $department === 'For sale' ? true : false;

        switch ($data) {
            case 'OfferAccepted':
                $status = $isSales ? 'Sold STC' : 'Let agreed';
                break;
            
            default:
                $status = '';
                break;
        }

        return $status;

    }

    /**
     * Generate custom property price
     */
    private function propertyPrice($data) {

        $price = new stdClass;

        $price->Value = $data->PriceValue;
        $price->Currency = $data->CurrencyCode;
        $price->Qualifier = !empty($data->PriceQualifierType) ? $data->PriceQualifierType->DisplayName : '';

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
    private function propertyAddress($data) {

        $Postcode = $data->Postcode ? explode(' ', $data->Postcode) : '';

        if(is_array($Postcode)) {            
            $data->Postcode1 = $Postcode[0];
            $data->Postcode2 = $Postcode[1];
        }

        return $data;

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
     * Generate custom array
     * of property images
     */
    private function propertyImages($data) {

        $images = array();

        if(is_array($data) && !empty($data)) {
            foreach($data as $image) {
                array_push($images, $image->Url);
            }
        }

        return $images;

    }

    /**
     * Generate EPC
     */
    private function propertyEPC($data) {

        $epc = '';

        if(!empty($data->Image) && !empty($data->Image->Url)) {
            $epc = $data->Image->Url;
        }

        return $epc;

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
     * Chekc if in debug mode
     */
    private function isDebug() {
        if(isset($_GET['debug']) && !empty($_GET['debug']) && $_GET['debug'] == 'true') {
            return true;
        }

        return false;
    }

}