<?php
/**
 * FL1 Settings
 *
 * Helper static functions for settings
 *
 * @author  fl1
 * @link    http://fl1.digital
 * @version 1.0
 */

 // Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class FL1_Settings {

    public static function google_maps_api_key() {

		return get_field('google_maps_api_key', 'option');

    }

       

}