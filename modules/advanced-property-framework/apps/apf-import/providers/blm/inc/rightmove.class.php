<?php
    //rightmove.class.php
 
    class RightmoveParser{
 
        public $folder;  //folder to scan
        public $rmfile;  //source zip file
        public $temp_loc; //temporary file location
        public $image_loc; //property images file location
        public $archive_loc; //location to archive
        public $keep_source_file = true;
        public $validate_fields = false;
        public $validate_values = false;
        public $scan_folder = true;
 
        //property value with status
        public $STATUS_ID = array(  "Available",
                                    "SSTC (Sales only)",
                                    "SSTCM(Scottish Sales only)",
                                    "Under Offer (Sales only)",
                                    "Reserved (Lettings only)",
                                    "Let Agreed (Lettings only)"
                                );
        public $PRICE_QUALIFIER     = array(
                                            0 => "Default",
                                            1 => "POA",
                                            2 => "Guide Price",
                                            3 => "Fixed Price",
                                            4 => "Offers in Excess of",
                                            5 => "OIRO",
                                            6 => "Sale by Tender",
                                            7 => "From",
                                            9 => "Shared Ownership",
                                            10 => "Offers Over",
                                            11 => "Part Buy Part Rent",
                                            12 => "Shared Equity"
                                        );
        public $PUBLISHED_FLAG      = array(0 => "Hidden/invisible", 1 => "Visible");
        public $LET_TYPE_ID         = array(0=>"Not Specified", 1=>"Long Term", 2=>"Short Term", 3=>"Student", 4=>"Commercial");
        public $LET_FURN_ID         = array(0 => "Furnished", 1 => "Part Furnished", 2 => "Unfurnished", 3 => "Not Specified", 4=>"Furnished/Un Furnished");
        public $LET_RENT_FREQUENCY  = array(0 => "Weekly", 1 => "Monthly", 2 => "Quarterly", 3 => "Annual");
        public $TENURE_TYPE_ID      = array(1 => "Freehold", 2 => "Leasehold", 3 => "Feudal", 4 => "Commonhold", 5 => "Share of Freehold");
        public $TRANS_TYPE_ID       = array(1 => "Resale", 2=> "Lettings");
        public $NEW_HOME_FLAG       = array("Y" => "New Home", "N" => "Non New Home");
        public $PROP_SUB_ID         = array(
                                            0=>"Not Specified",
                                            1=>"Terraced",
                                            2=>"End of Terrace",
                                            3=>"Semi-Detached",
                                            4=>"Detached",
                                            5=>"Mews",
                                            6=>"Cluster House",
                                            7=>"Ground Flat",
                                            8=>"Flat",
                                            9=>"Studio",
                                            10=>"Ground Maisonette",
                                            11=>"Maisonette",
                                            12=>"Bungalow",
                                            13=>"Terraced Bungalow",
                                            14=>"Semi-Detached Bungalow",
                                            15=>"Detached Bungalow",
                                            16=>"Mobile Home",
                                            17=>"Hotel",
                                            18=>"Guest House",
                                            19=>"Commercial Property",
                                            20=>"Land",
                                            21=>"Link Detached House",
                                            22=>"Town House",
                                            23=>"Cottage",
                                            24=>"Chalet",
                                            27=>"Villa",
                                            28=>"Apartment",
                                            29=>"Penthouse",
                                            30=>"Finca",
                                            43=>"Barn Conversion",
                                            44=>"Serviced Apartments",
                                            45=>"Parking",
                                            46=>"Sheltered Housing",
                                            47=>"Retirement Property",
                                            48=>"House Share",
                                            49=>"Flat Share",
                                            50=>"Park Home",
                                            51=>"Garages",
                                            52=>"Farm House",
                                            53=>"Equestrian",
                                            56=>"Duplex",
                                            59=>"Triplex",
                                            62=>"Longere",
                                            65=>"Gite",
                                            68=>"Barn",
                                            71=>"Trulli",
                                            74=>"Mill",
                                            77=>"Ruins",
                                            80=>"Restaurant",
                                            83=>"Cafe",
                                            86=>"Mill",
                                            89=>"Trulli",
                                            92=>"Castle",
                                            95=>"Village House",
                                            101=>"Cave House",
                                            104=>"Cortijo",
                                            107=>"Farm Land",
                                            110=>"Plot",
                                            113=>"Country House",
                                            116=>"Stone House",
                                            117=>"Caravan",
                                            118=>"Lodge",
                                            119=>"Log Cabin",
                                            120=>"Manor House",
                                            121=>"Stately Home",
                                            125=>"Off-Plan",
                                            128=>"Semi-detached Villa",
                                            131=>"Detached Villa",
                                            134=>"Bar",
                                            137=>"Shop",
                                            140=>"Riad",
                                            141=>"House Boat",
                                            142=>"Hotel Room",
                                            );
 
        private $document_files = array();  //document files(.blm file) inside zip folder
        private $media_files = array();     //media files such as jpg, gif, pd
        private $doc_header = array('line_separator'=>'EOR', 'field_separator'=>'EOF', 'total'=>0);
        private $properties = array();      //property data
        private $pe_properties = array();
 
 
        /* Note:
 
            MEDIA_DOCUMENT_50, EDIA_DOCUMENT_TEXT_50  - MEDIA_DOCUMENT_59, MEDIA_DOCUMENT_TEXT_59 are HIP/EPC values
            MEDIA_IMAGE_60, MEDIA_IMAGE_TEXT_60 - EDIA_IMAGE_61 & MEDIA_IMAGE_TEXT_61 for EPC Graph
        */
 
        //parse and return data
        public function getPropertyData(){
 
                try{
 
                    if($this->scan_folder && !isset($this->rmfile)){
 
                        //scan the folder first
                        $newData = $this->getZipFile();
 
                        //If no new data, terminate
                        if(!$newData)
                        {
                            throw new Exception('<h3>No zip file inside folder</h3>');
                        }
 
                    }   
 
                    $this->unzipFiles(); //unzip files
                    $this->parseDocs();      //now parse the document files
                    $this->removeTempFiles();    //remove temporary files
                    $this->archiveZipFile(); //archive the processed file
 
                    if(count($this->pe_properties)<1)
                        throw new Exception('<h3>No Data Found</h3>');
 
                    return $this->pe_properties;
 
                }catch(Exception $e){
                    echo $e->getMessage();
                }
        }
 
        //scan a folder for agent's zip file
        private function getZipFile(){
            try{
                if ($handle = opendir($this->folder)) {
 
                    /* This is the correct way to loop over the directory. */
                    while (false !== ($file = readdir($handle))) {
                         
                        if ($file != "." && $file != ".." && substr($file,0,1)!='.' ) {
 
                            if(strstr($file,".zip")){
                                $this->rmfile = $file;
                                break;      //get only one zip file per folder
                            }
 
                        }
                    }
                    closedir($handle);
                    return true;
                }else return false;
            }catch(Exception $e){
                echo $e->getMessage();
            }
 
        }
 
        //unzip file and copy to related
        private function unzipFiles(){
             
            try{
             
                //unzip and move file to temporary locations
                $zip = new zipfile;
     
                $data = $zip->read_zip($this->folder.$this->rmfile);
                 
                if($data[0]['data']=='' OR !isset($data) )
                    
					throw new Exception('Failed to read zip file');
 					//print_r($data);
                 
                $docidx = 0;
                $mediaidx = 0;
     
                foreach($data as $idx=>$fileinfo){
                        //copy files to temporary folder
                        $handle = fopen($this->temp_loc.$fileinfo['name'], 'w');
                        fwrite($handle, $fileinfo['data']);
                        fclose($handle);
     
                        //check if it is a document file or other media file
    //                  if (fnmatch("*.blm", $fileinfo['name'])) {
                        if (strstr($fileinfo['name'], ".blm")) {
                            $this->document_files[$docidx]['branchid'] = basename($this->rmfile, ".zip");
                            $this->document_files[$docidx]['file_path']= $this->temp_loc.$fileinfo['name'];
                            $this->document_files[$docidx++]['basename']= $fileinfo['name'];
                        }else{
                            $this->media_files[$mediaidx]['file_path']= $this->temp_loc.$fileinfo['name'];
                            $this->media_files[$mediaidx++]['basename']= $fileinfo['name'];
                        }
     
                }
                 
                }catch(Exception $e){
                 echo $e->getMessage();
                 exit(1);
                }
 
        //  print_r($this->document_files);
        }
 
        //Now parse the blm file
        private function parseDocs(){
            try {
                foreach($this->document_files as $doc){
                 
                    //read the document file
                    $fp = fopen($doc['file_path'],'r');
                    $data = fread($fp, filesize($doc['file_path']));
                    fclose($fp);
                     
 
                    //split the content to header, definition, data
                    $header = substr($data,strpos($data,'#HEADER#')+8,strpos($data,'#DEFINITION#')-8);  //document header
 
                    //process header data
                    $header_data = explode("\n",$header);
                    $header_data = array_filter($header_data,array($this,"cleanArray"));
                     
                     
 
                    foreach($header_data as $hdata){
 
                        //field value separator
                        if(strstr($hdata,"EOF")){
                            $replace_chars = array("EOF"," ",":","'","\n","\r");
                            $this->doc_header['field_separator'] = str_replace($replace_chars,"",$hdata);
                        }
 
                        //line separator
                        if(strstr($hdata,"EOR")){
                            $replace_chars = array("EOR"," ",":","'","\n","\r");
                            $this->doc_header['line_separator'] = str_replace($replace_chars,"",$hdata);
                        }
 
                        //total properties
                        if(strstr($hdata,"Property Count")){
                            $replace_chars = array("Property Count"," ",":","\n","\r");
                            $this->doc_header['total'] = (int) str_replace($replace_chars,"",$hdata);
                        }
                    }
                    //end of processing header data
				/*  !bookmark */
                    //process definition
                    $definition_length = strpos($data, $this->doc_header['line_separator'], strpos($data,'#DEFINITION#') )-strpos($data,'#DEFINITION#')-12;
                    $definition = substr($data, strpos($data,'#DEFINITION#')+12, $definition_length);   //field's details
                    $definition = trim($definition);
                    $this->doc_definition = explode($this->doc_header['field_separator'],$definition);
                    $this->doc_definition = array_filter($this->doc_definition,array($this,"cleanArray"));
                    //end of processing definition
                    /* temp commented
                    $this->checkMandatoyFields();    //check if document has mandatory fields
                    */
 
                    $content_lenghth = strpos($data, '#END#' )-strpos($data,'#DATA#')-6;
                    $content =  substr($data,strpos($data,'#DATA#')+6, $content_lenghth);   //field's details
                    $content_data = explode($this->doc_header['line_separator'],$content);
                    $content_data = array_filter($content_data,array($this,"cleanArray"));
 
                    array_walk($content_data, array($this, 'trimArray'));   //trim the lines            
 
                    //if total properties and number of properties defined in data is not same, throw error
                    if((count($content_data)!=$this->doc_header['total']) && $this->doc_header['total']>0 ){
                        throw new Exception('<p>Total number of properties in header and total properties defined in data is not same.<br />Defined header total# '.$this->doc_header['total'].'<br />Number of properties in dataset# '.count($content_data).'</p>');
                    }else{
                        $this->doc_header['total'] = count($content_data);
                    }
					//print_r($this->doc_definition);
 
                    foreach($content_data as $key1=>$property_data){
                        $property_data = substr($property_data,0,-1); //exclude last field separator
                        $raw_data = explode($this->doc_header['field_separator'],$property_data);
 
                        //if total fields defined in definition and fields defined in data is not same, throw error
                        if(count($this->doc_definition)!==count($raw_data)){
                            throw new Exception('<p>Total fields defined in definition and fields defined in data is not same</p>');
                        }
 
                        foreach($raw_data as $key2=>$property){
 
                            $this->checkMandatoyValues($key1, $this->doc_definition[$key2],$property);    //check value for mandatory value fields, throw exception else
 
                            //escape output
                            if($property!='')
                                $property = $this->formatValue($this->doc_definition[$key2], $property);
 
                            //copy the media proper location
                            if(preg_match("/MEDIA_IMAGE_[0-9]{2}/",$this->doc_definition[$key2])){
                                if($property!=''){
                                    if(file_exists($this->temp_loc.$property)){
 
                                        $path_parts = pathinfo($this->temp_loc.$property);
                                        if(is_numeric(array_search(strtolower($path_parts['extension']),array('jpg','gif','pd'))))
                                            @copy($this->temp_loc.$property,$this->image_loc.$property);
                                        else
                                        throw new Exception("Unsupported file ".$property);
 
                                    }
                                    /*
                                    else
                                        throw new Exception("File '".$property."' doesn't exist in zip.");*/
 
                                }
                            }   
 
                            $this->properties[$key1][$this->doc_definition[$key2]] = $property;
                            $this->pe_properties[$key1][$this->doc_definition[$key2]] = $property;
 
                            //put few text values for status code from manual
                            if(strstr($this->doc_definition[$key2],"STATUS_ID") && $property!=''){
                                $this->pe_properties[$key1]['STATUS_ID_TEXT'] = $this->STATUS_ID[$property];
                            }
 
                            if(strstr($this->doc_definition[$key2],"PRICE_QUALIFIER") && $property!=''){
                                $this->pe_properties[$key1]['PRICE_QUALIFIER_TEXT'] = $this->PRICE_QUALIFIER[$property];
                            }                           
 
                            if(strstr($this->doc_definition[$key2],"PUBLISHED_FLAG") && $property!=''){
                                $this->pe_properties[$key1]['PUBLISHED_FLAG_TEXT'] = $this->PUBLISHED_FLAG[$property];
                            }
 
                            if(strstr($this->doc_definition[$key2],"LET_TYPE_ID") && $property!=''){
                                $this->pe_properties[$key1]['LET_TYPE_ID_TEXT'] = $this->LET_TYPE_ID[$property];
                            }                           
 
                            if(strstr($this->doc_definition[$key2],"LET_FURN_ID") && $property!=''){
                                $this->pe_properties[$key1]['LET_FURN_ID_TEXT'] = $this->LET_FURN_ID[$property];
                            }
 
                            if(strstr($this->doc_definition[$key2],"LET_RENT_FREQUENCY") && $property!=''){
                                $this->pe_properties[$key1]['LET_RENT_FREQUENCY_TEXT'] = $this->LET_RENT_FREQUENCY[$property];
                            }                           
 
                            if(strstr($this->doc_definition[$key2],"TENURE_TYPE_ID") && $property!=''){
                                $this->pe_properties[$key1]['TENURE_TYPE_ID_TEXT'] = $this->TENURE_TYPE_ID[$property];
                            }
 
                            if(strstr($this->doc_definition[$key2],"TRANS_TYPE_ID") && $property!=''){
                                $this->pe_properties[$key1]['TRANS_TYPE_ID_TEXT'] = $this->TRANS_TYPE_ID[$property];
                            }                           
 
                            if(strstr($this->doc_definition[$key2],"NEW_HOME_FLAG") && $property!=''){
                                $this->pe_properties[$key1]['NEW_HOME_FLAG_TEXT'] = $this->NEW_HOME_FLAG[$property];
                            }
                            //end of put few text values for status code from manual
 
                        }
 
                    }
//                  print_r($this->pe_properties);
 
                }
            }catch(Exception $e){
                echo $e->getMessage();
            }
        }
 
        //function to archive a file, remove the source file too
        private function archiveZipFile(){
            @unlink($this->archive_loc.$this->rmfile);        //remove if there is already a same named file
            @copy($this->folder.$this->rmfile, $this->archive_loc.$this->rmfile);
 
            //delete the source file if needed
            if(!$this->keep_source_file){
                @unlink($this->folder.$this->rmfile);
            }
 
        }
 
        //remove files from temporary folder
        private function removeTempFiles(){
            //remove the media files
            foreach($this->media_files as $media_file){
                @unlink($media_file['file_path']);
            }
            //remove the document files
            foreach($this->document_files as $document_file){
                @unlink($document_file['file_path']);
            }   
 
        }
 
        //check if document contains mandatory fields
        private function checkMandatoyFields(){
            //if we need validation
            if($this->validate_fields){
 
                $fields = array("AGENT_REF","ADDRESS_1","ADDRESS_2","TOWN","POSTCODE1","POSTCODE2","FEATURE1","FEATURE2","FEATURE3","FEATURE4","FEATURE5","SUMMARY","DESCRIPTION","BRANCH_ID","STATUS_ID","BEDROOMS","PRICE","PRICE_QUALIFIER","PROP_SUB_ID","CREATE_DATE","UPDATE_DATE","DISPLAY_ADDRESS","PUBLISHED_FLAG","LET_DATE_AVAILABLE","LET_BOND","LET_TYPE_ID","LET_FURN_ID","LET_RENT_FREQUENCY","TENURE_TYPE_ID","TRANS_TYPE_ID","NEW_HOME_FLAG", "MEDIA_IMAGE_00", "MEDIA_IMAGE_60","MEDIA_IMAGE_TEXT_60","MEDIA_DOCUMENT_50","MEDIA_DOCUMENT_TEXT_50");
                try{
                    $absent_fields = array_diff($fields,$this->doc_definition);
    //              print_r($absent_fields);
                    if(count($absent_fields)>0){
                        $msg = "<h3>You document missing these fields - ".implode(", ",$absent_fields)."</h3>";
                        throw new Exception($msg);
                    }
 
                }catch(Exception $e){
                    echo $e->getMessage();
                }
            }
        }
 
        //check mandatory fields
        private function checkMandatoyValues($line,$field,$value){
            //if we need validation
            if($this->validate_values){
 
                $field_for_values = array("AGENT_REF","ADDRESS_1","ADDRESS_2","TOWN","POSTCODE1","POSTCODE2","FEATURE1","FEATURE2","FEATURE3","SUMMARY","DESCRIPTION","BRANCH_ID","STATUS_ID","BEDROOMS","PRICE","PROP_SUB_ID","DISPLAY_ADDRESS","PUBLISHED_FLAG","TRANS_TYPE_ID", "MEDIA_IMAGE_00");
                try{
                    if(is_numeric(array_search($field,$field_for_values)) && $value==''){
                        $msg = "<h3> Line #".$line." - ".$field." can not be empty</h3>\n";
                        throw new Exception($msg);
                    }
                }catch(Exception $e){
                    echo $e->getMessage();
                }
            }       
 
        }
 
        //filter array, ommit empty value
        private function cleanArray($var){
            $replace_chars = array(" ","\n","\r","\t");
            $var = trim(str_replace($replace_chars,"",$var));
            return (isset($var) && $var!='');
        }
 
        //trim an array
        private function trimArray(&$var){
            $var = trim($var);
        }
 
        //escape output
        private function formatValue($field, $value){
            $string_fields = array("AGENT_REF","ADDRESS_1","ADDRESS_2","TOWN","POSTCODE1","POSTCODE2","FEATURE1","FEATURE2","FEATURE3","SUMMARY","DESCRIPTION","DISPLAY_ADDRESS","NEW_HOME_FLAG", "MEDIA_IMAGE_TEXT_00");
            $number_fields = array("BRANCH_ID","STATUS_ID","BEDROOMS","PRICE","PRICE_QUALIFIER","PROP_SUB_ID","PUBLISHED_FLAG","LET_BOND","LET_TYPE_ID","LET_FURN_ID","LET_RENT_FREQUENCY","TENURE_TYPE_ID","TRANS_TYPE_ID");   
 
            if(is_numeric(array_search($field,$string_fields)) && $value==''){
                $value = (string) $value;
                $value = strip_tags($value, '<p><u><strong><i><b>');
            }
 
            if(is_numeric(array_search($field,$number_fields)) && $value==''){
 
                if(is_numeric(array_search($field,array("LET_BOND","PRICE") )))
                    $value = (float) $value;
                else
                    $value = (int) $value;
            }
 
            return $value;
        }       
 
    }
 
?>