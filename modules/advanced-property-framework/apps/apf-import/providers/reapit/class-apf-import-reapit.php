<?php
/**
 * Reapit Import Class
 * 
 * @package APF
 * @version 2.0
 */

class APFI_Reapit {

    private $client_id;
    private $password;

    public function __construct() {
        
        $this->client_id = get_field('apf_provider_reapit_client_id', 'option');
        $this->password = get_field('apf_provider_reapit_password', 'option');

    }

    public function xml() {

        $properties = $this->fetchData();

        foreach($properties as $idx => $property) {
            if(isset($property->Age) && is_array($property->Age) && in_array('New Build', $property->Age)) {
                $properties[$idx]->isNewHome = true;
            }
            if(isset($property->AccommodationSummary) && is_array($property->AccommodationSummary) && in_array('Bespoke Marketing', $property->AccommodationSummary)) {
                $properties[$idx]->isBespokeMarketing = true;
            }
        }

        if($this->isDebug()) {
            echo '<pre>';
            print_r($properties);
            echo '</pre>';
            die();
        }

        header("Content-Type: application/xml; charset=utf-8");

        $xml_data = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><properties></properties>');

        APFI_Utils::array_to_xml($properties, $xml_data, '', 'reapit');

        $dom = dom_import_simplexml($xml_data)->ownerDocument;
        $dom = new DOMDocument('1.0');
        $dom->encoding = 'UTF-8';
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom->loadXML($xml_data->asXML());
        echo $dom->saveXML();
        
    }

    private function fetchData() {

        $client = new SoapClient('https://webservice.reapit.net/oae/?wsdl', array("trace" => 1)); //LIVE
        //$client = new SoapClient('http://webservice.staging.reapit.net/sag/?wsdl', array("trace" => 1)); //TEST

        $authHeaders = array(
            new SoapHeader('https://soapinterop.org/echoheader/', 'ClientID', $this->client_id),
            new SoapHeader('https://soapinterop.org/echoheader/', 'Password', $this->password)
        );
        $client->__setSoapHeaders($authHeaders);

        // SALES
        $params_sales = array('Criteria' =>
            array(
                'SearchType' => 'sales',
                'PropertyField'=>array('ID', 'PriceString', 'SalePrice', 'SaleMaxPrice', 'SalePriceString', 'SaleStatus', 'ExchangePrice', 'PropertyArchived', 'PropertyArchiveDate', 'PriceQualifier', 'Currency', 'PriceReduced', 'Size', 'MaxSize', 'SizeString', 'NumPlots', 'Strapline', 'HouseName', 'HouseNumber', 'Address1', 'Address2', 'Address3', 'Address4', 'Postcode', 'Area', 'Country', 'Latitude', 'Longitude', 'Available', 'AvailableFrom', 'TimeAmended', 'ExchangeDate', 'Status', 'Tenure', 'Disposal', 'Furnish', 'AgencyType', 'Description', 'AccommodationSummary', 'Image', 'Floorplan', 'SitePlan', 'PrintableDetails', 'PDF', 'PDFAmendTime', 'Office', 'MarketingOffice', 'Negotiator', 'EPC', 'EPCURL', 'Room', 'OpenHouse', 'DateMarketed', 'DoubleBedrooms', 'SingleBedrooms', 'ReceptionRooms', 'Bathrooms', 'TotalBedrooms', 'Type', 'Style', 'Situation', 'Parking', 'Age', 'Locality', 'Special', 'LongDesc', 'Distance', 'Directions', 'Featured', 'IsDevelopment', 'Developer', 'IsSubPlot', 'PrimaryDevelopment', 'VTour')
            )
        );
        $property_data_sales = $client->__soapCall('GetGeneralProperties', $params_sales);

        //DEBUG:
        // echo '<h1>Sales</h1>';
        // echo '<pre>';
        // print_r($property_data_sales);
        // echo '</pre>';

        // LETTINGS
        $params_lettings = array('Criteria' =>
            array(
                'SearchType' => 'lettings',
                'PropertyField'=>array('ID', 'PriceString', 'WeeklyRent', 'RentString', 'RentalPeriod', 'LettingStatus', 'PropertyArchived', 'PropertyArchiveDate', 'PriceQualifier', 'Currency', 'PriceReduced', 'Size', 'MaxSize', 'SizeString', 'NumPlots', 'Strapline', 'HouseName', 'HouseNumber', 'Address1', 'Address2', 'Address3', 'Address4', 'Postcode', 'Area', 'Country', 'Latitude', 'Longitude', 'Available', 'AvailableFrom', 'TimeAmended', 'ExchangeDate', 'Status', 'Tenure', 'Disposal', 'Furnish', 'AgencyType', 'Description', 'AccommodationSummary', 'Image', 'Floorplan', 'SitePlan', 'PrintableDetails', 'PDF', 'PDFAmendTime', 'Office', 'MarketingOffice', 'Negotiator', 'EPC', 'EPCURL', 'Room', 'OpenHouse', 'DateMarketed', 'DoubleBedrooms', 'SingleBedrooms', 'ReceptionRooms', 'Bathrooms', 'TotalBedrooms', 'Type', 'Style', 'Situation', 'Parking', 'Age', 'Locality', 'Special', 'LongDesc', 'Distance', 'Directions', 'Featured', 'IsDevelopment', 'Developer', 'IsSubPlot', 'PrimaryDevelopment', 'VTour')
            )
        );
        $property_data_lettings = $client->__soapCall('GetGeneralProperties', $params_lettings);

        // DEBUG:
        // echo '<h1>Lettings</h1>';
        // echo '<pre>';
        // print_r($property_data_lettings);
        // echo '</pre>';
        // die();

        if(!is_soap_fault($property_data_sales) && !is_soap_fault($property_data_lettings)) {

            return array_merge($property_data_sales, $property_data_lettings);

        } else {

            if(is_soap_fault($property_data_sales)) {
                trigger_error("SOAP Fault Sales: (faultcode: {$property_data_sales->faultcode}, faultstring: {$property_data_sales->faultstring})", E_USER_ERROR);
            } else {
                return $property_data_sales;
            }

            if(is_soap_fault($property_data_lettings)) {
                trigger_error("SOAP Fault Lettings: (faultcode: {$property_data_lettings->faultcode}, faultstring: {$property_data_lettings->faultstring})", E_USER_ERROR);
            } else {
                return $property_data_lettings;
            }

            return false;

        }

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