<?php
/**
 * Fire!
 * 
 * @package APF
 * @version 2.0
 */

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

if(class_exists('APF_Settings')) {

    if(!class_exists('APF_Import')) {
        include 'class-apf-import.php';
    }

    // APF Settings
    $apf_settings = new APF_Settings();
    $provider = $apf_settings->provider('value');

    // APF Import
    $apf_import = new APF_Import($provider);
    $provider = $apf_import->provider();
    $provider->xml();

} else {

    echo 'APF is not installed.';

}
?>