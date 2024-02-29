<?php
function geocode($address){

    // url encode the address
    $address = str_replace (" ", "+", urlencode($address));

    /**
     * Google Map geocode api url
     *
     * This API key should different from the usual one. In the Google Dev Console,
     * create a new key and restric it by IP addresses (web servers, cron jobs, etc.)
     *
    */
    $url = "https://maps.google.com/maps/api/geocode/json?address={$address}&key=AIzaSyD38vTHQtK3U0ZrqHLTVtMB4rvKxpu1B24";

    // get the json response
    $resp_json = file_get_contents($url);

    // decode the json
    $resp = json_decode($resp_json, true);

    // response status will be 'OK', if able to geocode given address
    if($resp['status']=='OK'){

        // get the important data
        $lati = $resp['results'][0]['geometry']['location']['lat'];
        $longi = $resp['results'][0]['geometry']['location']['lng'];
        $formatted_address = $resp['results'][0]['formatted_address'];

        // verify if data is complete
        if($lati && $longi && $formatted_address){

            // put the data in the array
            $data_arr = array();

            array_push(
                $data_arr,
                $lati,
                $longi,
                $formatted_address
            );

            return $data_arr;

        }else{
            return false;
        }

    }else{
        return false;
    }
}

// DEBUG:
// $add = geocode('Overstone Road Harpenden AL5 5PL United Kingdom');
// print_r($add);
?>
