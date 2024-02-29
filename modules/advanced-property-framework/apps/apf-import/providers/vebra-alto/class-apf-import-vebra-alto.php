<?php
/**
 * Vebra Alto Import Class
 * 
 * @package APF
 * @version 2.0
 */

//include('vebra-alto-api.php');

class APFI_Vebra_Alto {

    private $username;
    private $password;
    private $datafeedID;

    public function __construct() {

        $this->username = $this->credentials('username');
        $this->password = $this->credentials('password');
        $this->datafeedID = $this->credentials('feed_id');
        
    }

    /**
     * Get credentials from ACF options
     * 
     * @param $key
     */
    public function credentials($key = '') {
        
        $credentials = get_field('apf_provider_vebra_alto', 'option');

        if(!empty($credentials) && $key) {
            $credentials = $credentials[$key];
        }

        return $credentials;

    }

    /**
     * Release the kraken!
     */
    public function xml() {

        $request = "https://webservices.vebra.com/export/$this->datafeedID/v10/branch";
        $branches = $this->connect($request);

        //Start making some delicious XML
        $output =
        '<?xml version="1.0" encoding="utf-8"?>
            <properties>
                ';

        //Start the big loop of loops
        foreach($branches as $branch) {

            if($branch->branchid == 3) { continue; }

            //Hello API - me again...
            $branch_url = $branch->url."/property";
            $properties = $this->connect($branch_url);

            //Looping again
            if( !$properties ) continue; 

            foreach($properties as $property_info){
                
                $property = $this->connect($property_info->url);

                if( null == $property ){ continue; }

                //Add some more to the XML
                $output .=
                '<property>
                ';

                //Add the ID to our collection
                $id = (string)$property['id'];
                $pid = (string)$property['propertyid'];

                //Add these two values (and another one) to the XML
                $output .=
                    '<property_id>'.$pid.'</property_id>
                    <feed_source>Vebra</feed_source>
                ';

                //Get some deets
                $featured = (string)$property['featured'];
                $bid = (string)$property['branchid'];
                $price = $property->price;
                $uploaded = $property->uploaded;
                $currency = $property->price['currency'];
                $furnished = $property->furnished; 
                $bedrooms = $property->bedrooms;
                $bathrooms = $property->bathrooms;
                $livingrooms = $property->receptions;
                $lat = $property->latitude;
                $lng = $property->longitude;

                //Buy or Rent
                $rent = $property->price['rent'];
                if(isset($rent) && $rent != ''){
                    $type = 'To Let';
                }else{
                    $type = 'For Sale';
                }

                //Address magic
                $property_name = (string)preg_replace('/&(?!#?[a-z0-9]+;)/', '&amp;',  (string)$property->address->name );
                $street = $property->address->street; 
                
                $sa1 = $property_name." ".$property->address->street;
                $sa2 = $property->address->locality;
                $town = $property->address->town;
                $county = $property->address->county;
                $postcode = $property->address->postcode;
                $country = 'United Kingdom';

                if(isset($sa2) && $sa2 != ''){
                    $property_area = $sa2;
                }elseif( isset($town) && $town != ''){
                    $property_area = $town;
                }else{
                    $property_area = $postcode;
                }

                //Address wizardry
                $full_address = array();
                $formatted_address = array(); 

                if($sa1 != ''){
                    $formatted_address[] = $sa1;
                }
                if($sa2 != ''){
                    $formatted_address[] = $sa2;
                }
                if($town != ''){
                    $full_address[] = $town;
                    $formatted_address[] = $town; 
                }
                if($county != ''){
                    $full_address[] = $county;
                    $formatted_address[] = $county; 
                }
                if($postcode != ''){
                    $full_address[] = $postcode;
                    $formatted_address[] = $postcode; 
                }
                //May also need to check for 'Un-United Kindom' soon. Do topical jokes work in code comments?
                if($country != '') {
                    $full_address[] = $country;
                }

                //I'm imploding!
                $full_address = implode(', ', $full_address);
                $formatted_address = implode(', ', $formatted_address );

                $post_title = $property->address->display;

                //What is it?
                $property_type = $property->type[0];
                if($property_type == 'Not Specified') { 
                    $property_type = 'property';
                }

                //Sum it
                $summary = $property->description;

                //More output for the file monster
                $output .=
                    '<posttitle><![CDATA[ '.$post_title.' ]]></posttitle>
                    <branchid>'.$bid.'</branchid>
                    <type>'.$type.'</type>
                    <featured>'.$featured.'</featured>
                    <price>'.$price.'</price>
                    <uploaded>'.$uploaded.'</uploaded>
                    <currency>'.$currency.'</currency>
                    <bedrooms>'.$bedrooms.'</bedrooms>
                    <bathrooms>'.$bathrooms.'</bathrooms>
                    <livingrooms>'.$livingrooms.'</livingrooms>
                    <lat>'.$lat.'</lat>
                    <lng>'.$lng.'</lng>
                    <full_address>'.$full_address.'</full_address>
                    <formatted_address>'.$formatted_address.'</formatted_address>
                    <property_name>'.$property_name.'</property_name>
                    <street>'.$street.'</street>
                    <town>'.$town.'</town>
                    <county>'.$county.'</county>
                    <postcode>'.$postcode.'</postcode>
                    <area>'.$property_area.'</area>
                    <country>'.$country.'</country>
                    <property_type>'.$property_type.'</property_type>
                    <summary><![CDATA[ '.$summary.' ]]></summary>
                ';

                //Describe the property in 1 multi-dimensional array or less...
                $output .= '<description><![CDATA[';
                foreach($property->paragraphs->paragraph as $paragraph){
                    $output .= '<p>'.(string)$paragraph->text.'</p>';
                }
                $output .= ']]></description>';

                //I like your 'features'
                $output .= '<features>';
                foreach($property->bullets->bullet as $feature){
                    $output .= '<feature><![CDATA[ '.$feature.' ]]></feature>';
                }
                $output .= '</features>';

                //Oooo pretty pictures!
                $output .= '<pictures>';
                foreach( $property->files->file as $file){

                    if($file['type'] == 0){
                        $output .= '<picture><![CDATA[ '.str_replace('http://', 'https://', $file->url).' ]]></picture>';
                    }

                }
                $output .= '</pictures>';

                //I have found a floor in your plans!
                $output .= '<floorplans>';
                foreach( $property->files->file as $file){
                    if($file['type'] == 2){
                        $output .= '<floorplan><![CDATA[ '.str_replace('http://', 'https://', $file->url).' ]]></floorplan>';
                    }
                }
                $output .= '</floorplans>';

                //EPC...EIC...EIEIO...
                foreach($property->files->file as $file){
                    if($file['type'] == 9){
                        if(substr(basename($file->url), 0, 2) == 'EE'){
                            $output .= '<eer><![CDATA[ '.str_replace('http://', 'https://', $file->url).' ]]></eer>';
                        }
                        if(substr(basename($file->url), 0, 2) == 'EI'){
                            $output .= '<eic><![CDATA[ '.str_replace('http://', 'https://', $file->url).' ]]></eic>';
                        }
                    }
                }

                //Everything else
                foreach($property->files->file as $file){
                    if($file['type'] == 7){
                        $output .= '<brochure><![CDATA[ '.str_replace('http://', 'https://', $file->url).' ]]></brochure>';
                    }
                    if($file['type'] == 11){
                        $output .= '<virtualtour><![CDATA[ '.str_replace('http://', 'https://', $file->url).' ]]></virtualtour>';
                    }
                }

                //Are you impressed by my status?
                switch($property->web_status){
                    case 0:
                        $status = 'Available';
                        break;
                    case 1:
                        if($type == 'For Sale'){
                            $status = 'Under Offer';
                        }else{
                            $status = 'Let';
                        }
                        break;
                    case 2:
                        if($type == 'For Sale'){
                            $status = 'Sold';
                        }else{
                            $status = 'Under Offer';
                        }
                        break;
                    case 3:
                        if($type == 'For Sale'){
                            $status = 'Sold STC';
                        }else{
                            $status = 'Reserved';
                        }
                        break;
                    case 4:
                        if($type == 'For Sale'){
                            $status = 'For Sale By Auction';
                        }else{
                            $status = 'Let Agreed';
                        }
                        break;
                    case 5:
                        if($type == 'For Sale'){
                            $status = 'Reserved';
                        }else{
                            $status = '';
                        }
                        break;
                    case 6:
                        if($type == 'For Sale'){
                            $status = 'New Instruction';
                        }else{
                            $status = '';
                        }
                        break;
                    case 7:
                        if($type == 'For Sale'){
                            $status = 'Just on Market';
                        }else{
                            $status = '';
                        }
                        break;
                    case 8:
                        if($type == 'For Sale'){
                            $status = 'Price Reduction';
                        }else{
                            $status = '';
                        }
                        break;
                    case 9:
                        if($type == 'For Sale'){
                            $status = 'Keen to Sell';
                        }else{
                            $status = '';
                        }
                        break;
                    case 10:
                        if($type == 'For Sale'){
                            $status = 'No Chain';
                        }else{
                            $status = '';
                        }
                        break;
                    case 11:
                        if($type == 'For Sale'){
                            $status = 'Vendor will pay stamp duty';
                        }else{
                            $status = '';
                        }
                        break;
                    case 12:
                        if($type == 'For Sale'){
                            $status = 'Offers in the region of';
                        }else{
                            $status = '';
                        }
                        break;
                    case 13:
                        if($type == 'For Sale'){
                            $status = 'Guide Price';
                        }else{
                            $status = '';
                        }
                        break;

                    case 100:
                        $status = 'To Let';
                        break;
                    case 101:
                        $status = 'Let';
                        break;
                    case 102:
                        $status = 'Under Offer';
                        break;
                    case 103:
                        $status = 'Reserved';
                        break;
                    case 104:
                        $status = 'Let Agreed';
                        break;
                }

                // Add Vebra URL 
                $output .= "<url>http://www.vebra.com/details/property/{$property['firmid']}/{$id}</url>";

                // Add status
                $output .= '<status>'.$status.'</status>';

                // Add status ID
                $output .= '<statusID>'.$property->web_status.'</statusID>';

                //Close tha muvver daaan
                $output .=
                '</property>
                ';
            }

        }

        //Oooo exciting! We're almost there...
        $output .= '</properties>';

        //Do some crazy XML juggling to make it look pretty
        try {
            
            $xml = new SimpleXMLElement($output);
            $dom = new DOMDocument("1.0");
            $dom->preserveWhiteSpace = false;
            $dom->formatOutput = true;
            $dom->loadXML($xml->asXML());

            // $dom->save(APF_IMPORT_PATH.'/xml/vebra-alto.xml');
            header('Content-Type: application/xml; charset=utf-8');
            echo $dom->saveXML();
            //echo 'XML generated successfully';

        }
        catch( Exception $e ){

            echo "Generate: Exception triggered: {$e->getMessage()} \n";

            libxml_use_internal_errors(true);
            $sxe = simplexml_load_string($output);

            if ($sxe === false) {
                echo "Generate: Failed loading XML\n";
                foreach(libxml_get_errors() as $error) {
                    echo "\t". $error->message." \n";
                }
            }
        }

    }

    /**
     * Gets the last token from the tokens.txt file
     * 
     * @return array || null
     */
    function get_last_token_saved() {

        if($this->credentials('token_active')) {

            return array(
                'token'			=> $this->credentials('token_active'),
                'token_start'	=> DateTime::createFromFormat("d/m/Y H:i:s", $this->credentials('token_start'), wp_timezone()),
                'token_expire'	=> DateTime::createFromFormat("d/m/Y H:i:s", $this->credentials('token_expiry'), wp_timezone())
            );
            
        }
    }

    /**
     * Returns the most available unexpired token or false
     * 
     * @return BASE64Encoded $token | false 
     */
    function check_for_available_token( ){

        $token_data = $this->get_last_token_saved(); 
        if( !empty($token_data) && $token_data['token_expire'] instanceof DateTime  ){
            
            // Check expiry is > than the current time 
            $now = new DateTime('now', wp_timezone()); 
            $expiry = $token_data['token_expire']; 

            if( $expiry > $now ){
                // echo "Token Available \n";
                return $token_data['token']; 
            }else{
                echo "Token Unavailable \n";
            }
        }
    }


    //	Function to authenticate self to API and return/store the Token
    function getToken($url) {
        
        //Overwiting the response headers from each attempt in this file (for information only)
        $file = APF_IMPORT_PATH.'/providers/vebra-alto/headers.txt';
        $fh = fopen($file, "w");
        
        //Start curl session
        $ch = curl_init($url);
        //Define Basic HTTP Authentication method
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        //Provide Username and Password Details
        curl_setopt($ch, CURLOPT_USERPWD, "$this->username:$this->password");
        //Show headers in returned data but not body as we are only using this curl session to aquire and store the token
        curl_setopt($ch, CURLOPT_HEADER, 1 ); 
        curl_setopt($ch, CURLOPT_NOBODY, 1 ); 
        //write the output (returned headers) to file
        curl_setopt($ch, CURLOPT_FILE, $fh);
        //execute curl session
        curl_exec($ch);
        // close curl session
        curl_close($ch); 
        //close headers.txt file
        fclose($fh); 

        //read each line of the returned headers back into an array
        $headers = file(APF_IMPORT_PATH.'/providers/vebra-alto/headers.txt', FILE_SKIP_EMPTY_LINES);
        
        //for each line of the array explode the line by ':' (Seperating the header name from its value)
        foreach ($headers as $headerLine) {

            $line = explode(':', $headerLine);
            $header = $line[0];
            $value = trim($line[1]);
            
            //If the request is successful and we are returned a token
            if($header == "Token") {

                //save token start and expire time (roughly)
                $tokenStart = time(); 
                $tokenExpire = $tokenStart + 60*60;

                $now = new DateTime('now', wp_timezone());
                
                //save the token in a session variable (base 64 encoded)
                $base_64_token = base64_encode($value);
                update_field('apf_provider_vebra_alto_token_active', $base_64_token, 'option');
                update_field('apf_provider_vebra_alto_token_start', $now->format('d/m/Y H:i:s'), 'option');

                $now->modify('+1 hour');
                update_field('apf_provider_vebra_alto_token_expiry', $now->format('d/m/Y H:i:s'), 'option');
                
                //For now write this new token, its start and expiry datetime into a .txt (appending not overwriting - this is for reference in case you loose your session data)
                $file = APF_IMPORT_PATH.'/providers/vebra-alto/tokens.txt';
                $fh = fopen($file, "a+");
                
                //write the line in
                $newLine = "'".$base_64_token."','".date('d/m/Y H:i:s', $tokenStart)."','".date('d/m/Y H:i:s', $tokenExpire)."'"."\n";
                fwrite($fh, $newLine);
                
                //Close file
                fclose($fh);

            }
                
        }
        
        //If we have been given a token request XML from the API authenticating using the token
        if (!empty( $base_64_token )) {
            $this->connect($url);
        } else {
            //If we have not been given a new token its because we already have a live token which has not expired yet (check the tokens.txt file)
            echo '<br />There is still an active Token, you must wait for this token to expire before a new one can be requested!<br />';
        }
    }


    //Function to connect to the API authenticating ourself with the token we have been given
    function connect($url) {

        $available_token = $this->check_for_available_token(); 

        //If token is not set skip to else condition to request a new token 
        if(!empty( $available_token )) {
            
            //Initiate a new curl session
            $ch = curl_init($url);
            //Don't require header this time as curl_getinfo will tell us if we get HTTP 200 or 401
            curl_setopt($ch, CURLOPT_HEADER, 0); 
            //Provide Token in header
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Basic '.$available_token ));
            
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            
            //Execute the curl session
            $result = curl_exec($ch);

            //error_log(print_r($result, true)."\n", 3, WP_CONTENT_DIR.'/results.log');

            //Store the curl session info/returned headers into the $info array
            $info = curl_getinfo($ch);
            
            //Check if we have been authorised or not
            if($info['http_code'] == '401') {
                $this->getToken($url);
                echo 'Token Failed - $this->getToken() has been run!<br />';
            } elseif ($info['http_code'] == '200') {
                // echo 'Token Worked - Success'; // Debugging
            }
            
            //Close the curl session
            curl_close($ch);

            try{ 
                $xml = new SimpleXMLElement( $result );
                return $xml;
            }
            catch( Exception $e){
                //echo "Connect Exception Triggered: {$e->getMessage()}"; 
            }
            
        } else {
        
            //Run the $this->getToken function above if we are not authenticated
            $this->getToken($url);
            
        }
    }

}