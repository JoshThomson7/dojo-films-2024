<?php
/**
 * FL1 Helper Functions
 *
 * @author  FL1
 * @package WordPress
 *
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

function vt_resize($attach_id = null, $img_url = null, $width, $height, $crop = false) {
    return FL1_Helpers::vt_resize($attach_id, $img_url, $width, $height, $crop);
}

function pretty_print($var) {
    echo '<pre>';
    print_r($var);
    echo '</pre>';
}
