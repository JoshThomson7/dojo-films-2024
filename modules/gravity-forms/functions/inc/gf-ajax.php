<?php
/**
 * Gravity Forms AJAX
 */

 if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

function gf_ajax_form() {

	check_ajax_referer('$C.cGLu/1zxq%.KH}PjIKK|2_7WDN`x[vdhtF5GS4|+6%$wvG)2xZgJcWv3H2K_M', 'security');

    $gf_id = FL1_Helpers::param('gf_id');

	if(function_exists('gravity_form') && !empty($gf_id)) {
    	gravity_form( $gf_id, true, true, false, false, true );
	}

    wp_die();
	
}
add_action('wp_ajax_nopriv_gf_ajax_form', 'gf_ajax_form');
add_action('wp_ajax_gf_ajax_form' , 'gf_ajax_form');
