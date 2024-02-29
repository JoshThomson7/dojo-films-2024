<?php
/**
 * BLM Import Class
 * 
 * @package APF
 * @version 2.0
 */

class APFI_BLM {

    private $dir;
    private $extensions;
    private $path;
    private $img_url;

    public function __construct($dir, $extensions) {

        $this->extensions = $extensions;
        $this->path = dirname(__FILE__).'/';
        $this->img_url = APF_IMPORT_URL.'providers/blm/images/';
        $this->dir = $this->path.$dir.'/';
        
        require_once($this->path.'inc/rightmove.class.php');
        require_once($this->path.'inc/zip.class.php');
        require_once($this->path.'inc/geocode.php');

    }

    public function xml() {

        $this->blm_to_xml();

        if($this->has_files($this->path.'xml/', array('xml'))) {
            header("Content-Type: application/xml; charset=utf-8");
            header("Expires: on, 01 Jan 1970 00:00:00 GMT");
            header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
            header("Cache-Control: no-store, no-cache, must-revalidate");
            header("Cache-Control: post-check=0, pre-check=0", false);
            header("Pragma: no-cache");     
            echo file_get_contents(APF_IMPORT_URL.'providers/blm/xml/blm.xml?'.time());
        } else {
            echo 'No XML file could be found.';
        }

    }

    public function blm_to_xml() {

        //WPDB this mother
        global $wpdb;
        $wpdb->show_errors = true;

        // have we got files?
        if($this->has_files( $this->dir, $this->extensions )) {

                // Remove existing history files
                $files_history = glob($this->path.'history/*.{blm}', GLOB_BRACE);

                if(!empty($files_history)){
                    foreach($files_history as $file_history) {
                        unlink($file_history);
                    }
                }

                // Copy new files to history
                $files = glob($this->dir.'*.{blm}', GLOB_BRACE);
                if(!empty($files)) {
                    foreach($files as $file) {
                        copy($file, $this->path.'history/'.basename($file));
                    }
                }

                // Move images and media
                $media_files = glob($this->dir.'*.{jpeg,JPEG,jpg,JPG,png,PNG,gif,GIF,pdf,PDF}', GLOB_BRACE);
                $destination = $this->path.'images/';
                if(!empty($media_files)){
                    foreach($media_files as $media_file) {
                        $file_name = basename($media_file);
                        rename($media_file, $destination.$file_name);
                    }
                }

                /* ------------------------------------------------------------------------- */
                /*	Generate XML
                /* ------------------------------------------------------------------------- */
                //Start making some delicious XML
                $output =
                '<?xml version="1.0" encoding="utf-8"?>
                    <properties>
                        ';

                        //Get blm files
                        $blm_files = glob($this->dir.'*.{blm}', GLOB_BRACE);

                        //For each blm file
                        foreach($blm_files as $blm_file) {

                            //parse the blm file
                            $rmparser = new RightmoveParser();
                            $rmparser->folder = $this->dir;
                            $rmparser->temp_loc = $this->path.'tmp/';
                            $rmparser->image_loc = $this->path.'images/';
                            $rmparser->archive_loc = $this->path.'history/';
                            $rmparser->keep_source_file = true;
                            $zip = new ZipArchive();
                            $filename = $this->dir.'import.zip';
                            if($zip->open($filename, ZipArchive::CREATE)!== TRUE){
                                exit("cannot open <$filename>\n");
                            }
                            $zip->addFile($blm_file, "import.blm");
                            $zip->close();
                            $rmparser->rmfile = "import.zip";
                            $rmdata = $rmparser->getPropertyData();

                            //loop through properties in blm file
                            foreach($rmdata as $property) {

                                //Add some more to the XML
                                $output .=
                                '<property>
                                ';

                                //reset latlng and area variables
                                unset($lat);
                                unset($lng);
                                unset($latlng);
                                unset($area);

                                //This is the big one
                                $propDetails = array();

                                //Some other ones
                                $pictures = array();
                                $floorplans = array();
                                $documents = array();

                                //Get unique property ID
                                $pid = $property['AGENT_REF'];
                                $propDetails['PROPERTY_ID'] = $pid;

                                //Set the feed source
                                //$propDetails['FEED_SOURCE'] = 'VTUK';

                                /**
                                 * Handle address
                                */
                                $address_1   = isset($property['ADDRESS_1']) && !empty($property['ADDRESS_1']) ? $property['ADDRESS_1'].', ' : '';
                                $address_2   = isset($property['ADDRESS_2']) && !empty($property['ADDRESS_2']) ? $property['ADDRESS_2'].', ' : '';
                                $town        = isset($property['TOWN'])      && !empty($property['TOWN'])      ? $property['TOWN'].', '      : '';
                                $postcode_1  = isset($property['POSTCODE1']) && !empty($property['POSTCODE1']) ? $property['POSTCODE1']      : '';
                                $postcode_2  = isset($property['POSTCODE2']) && !empty($property['POSTCODE2']) ? ' '.$property['POSTCODE2']      : '';

                                // post title
                                $post_title = $address_1.$address_2.$town.$postcode_1;
                                $propDetails['POST_TITLE'] = iconv("cp1252", "UTF-8", $post_title);

                                // full address
                                $full_address = $address_1.$address_2.$town.$postcode_1.$postcode_2.' United Kingdom';
                                $full_address = str_replace(', ', ' ', $full_address);
                                $propDetails['FULL_ADDRESS'] = iconv("cp1252", "UTF-8", $full_address);

                                //Add address data to the array
                                $geocode = geocode($full_address); // see inc/geocode.php

                                if(empty($geocode)) {
                                    $alt_address = $postcode_1.$postcode_2.' United Kingdom';
                                    $geocode = geocode($alt_address);
                                }

                                $propDetails['LATITUDE'] = $geocode[0];
                                $propDetails['LONGITUDE'] = $geocode[1];

                                // $area
                                if( isset($property['TOWN']) && !empty($property['TOWN']) ) {
                                    $propDetails['AREA'] = $property['TOWN'];
                                } else {
                                    $propDetails['AREA'] = $property['POSTCODE1'];
                                }

                                //set property type
                                if($property['TRANS_TYPE_ID'] == 1){
                                    $type = 'For Sale';
                                }else{
                                    $type = 'To Let';
                                }
                                $propDetails['TYPE'] = $type;

                                //Don't want this stuff
                                $skip_fields = array(
                                    'STATUS_ID_TEXT',
                                    'PRICE_QUALIFIER',
                                    'PRICE_QUALIFIER_TEXT',
                                    'CREATE_DATE',
                                    'UPDATE_DATE',
                                    'PUBLISHED_FLAG',
                                    'PUBLISHED_FLAG_TEXT',
                                    'LET_DATE_AVAILABLE',
                                    'LET_BOND',
                                    'LET_TYPE_ID_TEXT',
                                    'LET_FURN_ID',
                                    'LET_RENT_FREQUENCY',
                                    'TRANS_TYPE_ID',
                                    'TRANS_TYPE_ID_TEXT',
                                    'MEDIA_IMAGE_TEXT_00',
                                    'MEDIA_IMAGE_TEXT_01',
                                    'MEDIA_IMAGE_TEXT_02',
                                    'MEDIA_IMAGE_TEXT_03',
                                    'MEDIA_IMAGE_TEXT_04',
                                    'MEDIA_IMAGE_TEXT_05',
                                    'MEDIA_IMAGE_TEXT_06',
                                    'MEDIA_IMAGE_TEXT_07',
                                    'MEDIA_IMAGE_TEXT_08',
                                    'MEDIA_IMAGE_TEXT_09',
                                    'MEDIA_IMAGE_TEXT_10',
                                    'MEDIA_IMAGE_TEXT_11',
                                    'MEDIA_IMAGE_TEXT_12',
                                    'MEDIA_IMAGE_TEXT_13',
                                    'MEDIA_IMAGE_TEXT_14',
                                    'MEDIA_IMAGE_TEXT_15',
                                    'MEDIA_IMAGE_TEXT_16',
                                    'MEDIA_IMAGE_TEXT_17',
                                    'MEDIA_IMAGE_TEXT_18',
                                    'MEDIA_IMAGE_TEXT_19',
                                    'MEDIA_IMAGE_TEXT_20',
                                    'MEDIA_IMAGE_TEXT_21',
                                    'MEDIA_IMAGE_TEXT_22',
                                    'MEDIA_IMAGE_TEXT_23',
                                    'MEDIA_IMAGE_TEXT_24',
                                    'MEDIA_IMAGE_TEXT_25',
                                    'MEDIA_IMAGE_TEXT_26',
                                    'MEDIA_IMAGE_TEXT_27',
                                    'MEDIA_IMAGE_TEXT_28',
                                    'MEDIA_IMAGE_TEXT_29',
                                    'MEDIA_IMAGE_TEXT_30',
                                    'MEDIA_IMAGE_TEXT_31',
                                    'MEDIA_IMAGE_TEXT_32',
                                    'MEDIA_IMAGE_TEXT_33',
                                    'MEDIA_IMAGE_TEXT_34',
                                    'MEDIA_IMAGE_TEXT_35',
                                    'MEDIA_IMAGE_TEXT_36',
                                    'MEDIA_IMAGE_TEXT_37',
                                    'MEDIA_IMAGE_TEXT_38',
                                    'MEDIA_IMAGE_TEXT_39',
                                    'MEDIA_IMAGE_TEXT_60',
                                    'MEDIA_FLOOR_PLAN_TEXT_00',
                                    'MEDIA_FLOOR_PLAN_TEXT_01',
                                    'MEDIA_FLOOR_PLAN_TEXT_02',
                                    'MEDIA_FLOOR_PLAN_TEXT_03',
                                    'MEDIA_FLOOR_PLAN_TEXT_04',
                                    'MEDIA_FLOOR_PLAN_TEXT_05',
                                    'MEDIA_FLOOR_PLAN_TEXT_06',
                                    'MEDIA_FLOOR_PLAN_TEXT_07',
                                    'MEDIA_FLOOR_PLAN_TEXT_08',
                                    'MEDIA_FLOOR_PLAN_TEXT_09',
                                    'MEDIA_DOCUMENT_TEXT_00',
                                    'MEDIA_DOCUMENT_TEXT_01',
                                    'MEDIA_DOCUMENT_TEXT_02',
                                    'MEDIA_DOCUMENT_TEXT_03',
                                    'MEDIA_DOCUMENT_TEXT_04',
                                    'MEDIA_DOCUMENT_TEXT_05',
                                    'MEDIA_DOCUMENT_TEXT_06',
                                    'MEDIA_DOCUMENT_TEXT_07',
                                    'MEDIA_DOCUMENT_TEXT_08',
                                    'MEDIA_DOCUMENT_TEXT_09',
                                    'MEDIA_DOCUMENT_TEXT_10',
                                    'MEDIA_DOCUMENT_TEXT_11',
                                    'MEDIA_DOCUMENT_TEXT_12',
                                    'MEDIA_DOCUMENT_TEXT_13',
                                    'MEDIA_DOCUMENT_TEXT_14',
                                    'MEDIA_DOCUMENT_TEXT_50',
                                    'MEDIA_DOCUMENT_TEXT_51'
                                );
                                //add all blm data to the array
                                foreach ($property as $key => $value){
                                    if($value != '' && !in_array($key, $skip_fields)){
                                        if(strpos($key, "FEATURE") !== false || $key == "SUMMARY" || $key == "DESCRIPTION") {
                                            $value = iconv("cp1252","UTF-8",$value);
                                            $propDetails[$key] = '<![CDATA[ '.$value.' ]]>';
                                        }elseif($key == "STATUS_ID"){
                                            switch($value){
                                                case 0:
                                                    $propDetails['STATUS'] = 'Available';
                                                    break;
                                                case 1:
                                                    $propDetails['STATUS'] = 'Sold STC';
                                                    break;
                                                case 2:
                                                    $propDetails['STATUS'] = 'Sold STCM';
                                                    break;
                                                case 3:
                                                    $propDetails['STATUS'] = 'Under Offer';
                                                    break;
                                                case 4:
                                                    $propDetails['STATUS'] = 'Reserved';
                                                    break;
                                                case 5:
                                                    $propDetails['STATUS'] = 'Let Agreed';
                                                    break;
                                                case 6:
                                                    $propDetails['STATUS'] = 'Sold';
                                                    break;
                                                case 7:
                                                    $propDetails['STATUS'] = 'Let';
                                                    break;
                                            }
                                        }elseif($key == "PROP_SUB_ID"){
                                            switch($value){
                                                case 0:
                                                    $propDetails['PROPERTY_TYPE'] = 'Not Specified';
                                                    break;
                                                case 1:
                                                    $propDetails['PROPERTY_TYPE'] = 'Terraced';
                                                    break;
                                                case 2:
                                                    $propDetails['PROPERTY_TYPE'] = 'End of Terrace';
                                                    break;
                                                case 3:
                                                    $propDetails['PROPERTY_TYPE'] = 'Semi-Detached';
                                                    break;
                                                case 4:
                                                    $propDetails['PROPERTY_TYPE'] = 'Detached';
                                                    break;
                                                case 5:
                                                    $propDetails['PROPERTY_TYPE'] = 'Mews';
                                                    break;
                                                case 6:
                                                    $propDetails['PROPERTY_TYPE'] = 'Cluster House';
                                                    break;
                                                case 7:
                                                    $propDetails['PROPERTY_TYPE'] = 'Ground Flat';
                                                    break;
                                                case 8:
                                                    $propDetails['PROPERTY_TYPE'] = 'Flat';
                                                    break;
                                                case 9:
                                                    $propDetails['PROPERTY_TYPE'] = 'Studio';
                                                    break;
                                                case 10:
                                                    $propDetails['PROPERTY_TYPE'] = 'Ground Maisonette';
                                                    break;
                                                case 11:
                                                    $propDetails['PROPERTY_TYPE'] = 'Maisonette';
                                                    break;
                                                case 12:
                                                    $propDetails['PROPERTY_TYPE'] = 'Bungalow';
                                                    break;
                                                case 13:
                                                    $propDetails['PROPERTY_TYPE'] = 'Terraced Bungalow';
                                                    break;
                                                case 14:
                                                    $propDetails['PROPERTY_TYPE'] = 'Semi-Detached Bungalow';
                                                    break;
                                                case 15:
                                                    $propDetails['PROPERTY_TYPE'] = 'Detached Bungalow';
                                                    break;
                                                case 16:
                                                    $propDetails['PROPERTY_TYPE'] = 'Mobile Home';
                                                    break;
                                                case 17:
                                                    $propDetails['PROPERTY_TYPE'] = 'Hotel';
                                                    break;
                                                case 18:
                                                    $propDetails['PROPERTY_TYPE'] = 'Guest House';
                                                    break;
                                                case 19:
                                                    $propDetails['PROPERTY_TYPE'] = 'Commercial Property';
                                                    break;
                                                case 20:
                                                    $propDetails['PROPERTY_TYPE'] = 'Land';
                                                    break;
                                                case 21:
                                                    $propDetails['PROPERTY_TYPE'] = 'Link Detached House';
                                                    break;
                                                case 22:
                                                    $propDetails['PROPERTY_TYPE'] = 'Town House';
                                                    break;
                                                case 23:
                                                    $propDetails['PROPERTY_TYPE'] = 'Cottage';
                                                    break;
                                                case 24:
                                                    $propDetails['PROPERTY_TYPE'] = 'Chalet';
                                                    break;
                                                case 27:
                                                    $propDetails['PROPERTY_TYPE'] = 'Villa';
                                                    break;
                                                case 28:
                                                    $propDetails['PROPERTY_TYPE'] = 'Apartment';
                                                    break;
                                                case 29:
                                                    $propDetails['PROPERTY_TYPE'] = 'Penthouse';
                                                    break;
                                                case 30:
                                                    $propDetails['PROPERTY_TYPE'] = 'Finca';
                                                    break;
                                                case 43:
                                                    $propDetails['PROPERTY_TYPE'] = 'Barn Conversion';
                                                    break;
                                                case 44:
                                                    $propDetails['PROPERTY_TYPE'] = 'Serviced Apartments';
                                                    break;
                                                case 45:
                                                    $propDetails['PROPERTY_TYPE'] = 'Parking';
                                                    break;
                                                case 46:
                                                    $propDetails['PROPERTY_TYPE'] = 'Sheltered Housing';
                                                    break;
                                                case 47:
                                                    $propDetails['PROPERTY_TYPE'] = 'Retirement Property';
                                                    break;
                                                case 48:
                                                    $propDetails['PROPERTY_TYPE'] = 'House Share';
                                                    break;
                                                case 49:
                                                    $propDetails['PROPERTY_TYPE'] = 'Flat Share';
                                                    break;
                                                case 50:
                                                    $propDetails['PROPERTY_TYPE'] = 'Park Home';
                                                    break;
                                                case 51:
                                                    $propDetails['PROPERTY_TYPE'] = 'Garages';
                                                    break;
                                                case 52:
                                                    $propDetails['PROPERTY_TYPE'] = 'Farm House';
                                                    break;
                                                case 53:
                                                    $propDetails['PROPERTY_TYPE'] = 'Equestrian';
                                                    break;
                                                case 56:
                                                    $propDetails['PROPERTY_TYPE'] = 'Duplex';
                                                    break;
                                                case 59:
                                                    $propDetails['PROPERTY_TYPE'] = 'Triplex';
                                                    break;
                                                case 62:
                                                    $propDetails['PROPERTY_TYPE'] = 'Longere';
                                                    break;
                                                case 65:
                                                    $propDetails['PROPERTY_TYPE'] = 'Gite';
                                                    break;
                                                case 68:
                                                    $propDetails['PROPERTY_TYPE'] = 'Barn';
                                                    break;
                                                case 71:
                                                    $propDetails['PROPERTY_TYPE'] = 'Trulli';
                                                    break;
                                                case 74:
                                                    $propDetails['PROPERTY_TYPE'] = 'Mill';
                                                    break;
                                                case 77:
                                                    $propDetails['PROPERTY_TYPE'] = 'Ruins';
                                                    break;
                                                case 80:
                                                    $propDetails['PROPERTY_TYPE'] = 'Restaurant';
                                                    break;
                                                case 83:
                                                    $propDetails['PROPERTY_TYPE'] = 'Cafe';
                                                    break;
                                                case 86:
                                                    $propDetails['PROPERTY_TYPE'] = 'Mill';
                                                    break;
                                                case 89:
                                                    $propDetails['PROPERTY_TYPE'] = 'Trulli';
                                                    break;
                                                case 92:
                                                    $propDetails['PROPERTY_TYPE'] = 'Castle';
                                                    break;
                                                case 95:
                                                    $propDetails['PROPERTY_TYPE'] = 'Village House';
                                                    break;
                                                case 101:
                                                    $propDetails['PROPERTY_TYPE'] = 'Cave House';
                                                    break;
                                                case 104:
                                                    $propDetails['PROPERTY_TYPE'] = 'Cortijo';
                                                    break;
                                                case 107:
                                                    $propDetails['PROPERTY_TYPE'] = 'Farm Land';
                                                    break;
                                                case 110:
                                                    $propDetails['PROPERTY_TYPE'] = 'Plot';
                                                    break;
                                                case 113:
                                                    $propDetails['PROPERTY_TYPE'] = 'Country House';
                                                    break;
                                                case 116:
                                                    $propDetails['PROPERTY_TYPE'] = 'Stone House';
                                                    break;
                                                case 117:
                                                    $propDetails['PROPERTY_TYPE'] = 'Caravan';
                                                    break;
                                                case 118:
                                                    $propDetails['PROPERTY_TYPE'] = 'Lodge';
                                                    break;
                                                case 119:
                                                    $propDetails['PROPERTY_TYPE'] = 'Log Cabin';
                                                    break;
                                                case 120:
                                                    $propDetails['PROPERTY_TYPE'] = 'Manor House';
                                                    break;
                                                case 121:
                                                    $propDetails['PROPERTY_TYPE'] = 'Stately Home';
                                                    break;
                                                case 125:
                                                    $propDetails['PROPERTY_TYPE'] = 'Off-Plan';
                                                    break;
                                                case 128:
                                                    $propDetails['PROPERTY_TYPE'] = 'Semi-detached Villa';
                                                    break;
                                                case 131:
                                                    $propDetails['PROPERTY_TYPE'] = 'Detached Villa';
                                                    break;
                                                case 134:
                                                    $propDetails['PROPERTY_TYPE'] = 'Bar';
                                                    break;
                                                case 137:
                                                    $propDetails['PROPERTY_TYPE'] = 'Shop';
                                                    break;
                                                case 140:
                                                    $propDetails['PROPERTY_TYPE'] = 'Riad';
                                                    break;
                                                case 141:
                                                    $propDetails['PROPERTY_TYPE'] = 'House Boat';
                                                    break;
                                                case 142:
                                                    $propDetails['PROPERTY_TYPE'] = 'Hotel Room';
                                                    break;
                                            }
                                        }elseif($key == "TENURE_TYPE_ID"){
                                            switch($value){
                                                case 1:
                                                    $propDetails['LEASE_TYPE'] = 'Freehold';
                                                    break;
                                                case 2:
                                                    $propDetails['LEASE_TYPE'] = 'Leasehold';
                                                    break;
                                                case 3:
                                                    $propDetails['LEASE_TYPE'] = 'Feudal';
                                                    break;
                                                case 4:
                                                    $propDetails['LEASE_TYPE'] = 'Commonhold';
                                                    break;
                                                case 5:
                                                    $propDetails['LEASE_TYPE'] = 'Share of Freehold';
                                                    break;
                                            }
                                        }elseif($key == "MEDIA_IMAGE_60"){
                                            $propDetails['EPC'] = $this->img_url.$value;
                                        }elseif(strpos($key, "MEDIA_IMAGE") !== false){
                                            $pictures[] = $this->img_url.$value;
                                        }elseif(strpos($key, "MEDIA_FLOOR_PLAN") !== false){
                                            $floorplans[] = $this->img_url.$value;
                                        }elseif(strpos($key, "MEDIA_DOCUMENT") !== false){
                                            $documents[] = $this->img_url.$value;
                                        }elseif($key == "MEDIA_VIRTUAL_TOUR_00"){
                                            $propDetails['VIRTUAL_TOUR'] = '<![CDATA[ '.$value.' ]]>';
                                        }else{
                                            $propDetails[$key] = iconv("cp1252","UTF-8",$value);
                                        }
                                    }
                                }

                                //Tasty XML data. Nom Nom Nom.
                                foreach($propDetails as $key => $val){
                                    $output .= '<'.$key.'>'.$val.'</'.$key.'>';
                                }

                                //Pictures
                                if(isset($pictures) && !empty($pictures)){
                                    $output .= '<pictures>';
                                    foreach($pictures as $picture){
                                        $output .= '<picture>'.$picture.'</picture>';
                                    }
                                    $output .= '</pictures>';
                                }

                                //Floorplans
                                if(isset($floorplans) && !empty($floorplans)){
                                    $output .= '<floorplans>';
                                    foreach($floorplans as $floorplan){
                                        $output .= '<floorplan>'.$floorplan.'</floorplan>';
                                    }
                                    $output .= '</floorplans>';
                                }

                                //Documents
                                if(isset($documents) && !empty($documents)){
                                    $output .= '<documents>';
                                    foreach($documents as $document){
                                        $output .= '<document>'.$document.'</document>';
                                    }
                                    $output .= '</documents>';
                                }

                                //Gotta close them tags
                                $output .= '</property>';

                            }

                            // Delete generated ZIP file
                            unlink($this->dir.'import.zip');

                        }

                        // Delete all other files
                        $delete_files = glob($this->dir.'*', GLOB_BRACE);
                        foreach($delete_files as $delete_file){
                            unlink($delete_file);
                        }

                //Oooo exciting! We're almost there...
                $output .=
                    '</properties>';

                //Do some crazy XML juggling to make it look pretty
                $xml = new SimpleXMLElement($output);
                $dom = new DOMDocument("1.0");
                $dom->preserveWhiteSpace = false;
                $dom->formatOutput = true;
                $dom->loadXML($xml->asXML());
                $dom->save($this->path.'xml/blm.xml');

        }
        
    }

    private function has_files( $dir, $extensions = array() ) {
        if ( empty( $extensions ) || ! is_array( $extensions ) || ! is_dir( $dir ) ) return false;
    
        $found = array();
        foreach ( $extensions as $ext ) {
            if ( count( glob( $dir . '/*.' . $ext ) ) > 0 ) {
                $found[$ext] = 1;
            }
        }
    
        if(!empty( $found )) {
            return true;
        } else {
            return false;
        }
    
    }

}