<?php
/**
 * Veco Import Class
 * 
 * @package APF
 * @version 2.0
 */

require 'vendor/autoload.php';
use PropertyAPI\Client;

class APFI_Veco {

    private function accessToken() {
        return get_field('apf_provider_veco_access_token', 'option');
    }

    public function xml() {

        if(!$this->accessToken()) {
            echo 'No token';
            die();
        }

        $client = new Client([
            'accessToken' => $this->accessToken()
        ]);
        
        $all_properties = $client->getProperties([
            'size' => 999,
        ]);
        $properties = $all_properties->getRows();
        
        $xmlstr = "<?xml version='1.0' encoding='UTF-8' ?><Properties>";
            foreach($properties as $property) {
                $xml_generater = new XMLSerializer;
                $xmlstr .= $xml_generater->generateValidXmlFromObj($property->_source, 'PropertyData');	
            }
        $xmlstr .= "</Properties>";
        
        header("Content-Type: application/xml; charset=utf-8");
        
        $property_xml = simplexml_load_string($xmlstr);
        $output = dom_import_simplexml($property_xml)->ownerDocument;
        $output->formatOutput = true;
        $output->preserveWhiteSpace = false;
        echo $output->saveXML();

    }
    
}

class XMLSerializer {

    public static function generateValidXmlFromObj(stdClass $obj, $node_block='nodes', $node_name='node') {
        $arr = get_object_vars($obj);
        return self::generateValidXmlFromArray($arr, $node_block, $node_name);
    }

    public static function generateValidXmlFromArray($array, $node_block='nodes', $node_name='node') {
        $xml = '<' . $node_block . '>';
        $xml .= self::generateXmlFromArray($array, $node_name);
        $xml .= '</' . $node_block . '>';

        return $xml;
    }

    private static function generateXmlFromArray($array, $node_name) {
		
		$url_elements = array('Image', 'Photo', 'Document', 'Plan', 'Video');
		
        $xml = '';

        if (is_array($array) || is_object($array)) {
            foreach ($array as $key=>$value) {
                if (is_numeric($key)) {
                    $key = preg_replace('/[0-9]+/', '', $node_name);
                }
				if(in_array(preg_replace('/[0-9]+/', '', $key), $url_elements) && $value != ''){
					$value = "https://passport.eurolink.co/api/properties/v1/media/".$value;
				}
                $xml .= '<' . $key . '>' . self::generateXmlFromArray($value, $node_name) . '</' . $key . '>';
            }
        } else {
            $xml = htmlspecialchars($array, ENT_QUOTES);
        }

        return $xml;
    }

}