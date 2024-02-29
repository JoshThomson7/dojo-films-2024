<?php
/**
 * FL1_GF_Ajax
 *
 * Class in charge of initialising everything FL1_GF_Ajax
 * 
 */

 // Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class FL1_GF_Public {

    public function __construct() {

        add_action('wp_ajax_nopriv_gf_ajax_form', array($this, 'gf_ajax_form'));
		add_action('wp_ajax_gf_ajax_form' , array($this, 'gf_ajax_form'));
		add_filter('gform_ajax_spinner_url', array($this, 'spinner_url'), 10, 2);
		add_action('get_header', array($this, 'enqueue_gf_scripts'));
		add_action('wp_footer', array($this, 'gf_footer'));

    }

	public function gf_ajax_form() {

		check_ajax_referer('$C.cGLu/1zxq%.KH}PjIKK|2_7WDN`x[vdhtF5GS4|+6%$wvG)2xZgJcWv3H2K_M', 'security');
	
		$gf_id = FL1_Helpers::param('gf_id');
	
		if(function_exists('gravity_form') && !empty($gf_id)) {
			gravity_form($gf_id, true, true, false, false, true);
		}
	
		wp_die();
		
	}

	public function spinner_url( $image_src, $form ) {
		return GF_URL.'/img/gf-loader.svg';
	}

	public function gf_footer() {

		$fc_content_types = get_field('fc_content_types');

		$has_gf_form = false;
		$gf_form_html = '<div class="gf__modal__form__overlay">
							<div class="gf__modal__form">
								<a href="#" class="close"><i class="fa-light fa-xmark"></i></a>';

		if($fc_content_types) {
			foreach($fc_content_types as $fc_content_type) {
				
				switch ($fc_content_type['acf_fc_layout']) {
					case 'fc_buttons':
						$buttons = $fc_content_type['buttons'];
						if(!empty($buttons)) {
							foreach($buttons as $button) {
								$button_url = $button['button_url'];
								if(strpos($button_url, 'gf=') !== false) {
									$has_gf_form = true;
									$gf_form_id = explode('=', $button_url)[1];
									$gf_form_html .= '<div id="gf_ajax_form_'.$gf_form_id.'"></div>';
								}
							}
						}
						break;

					case 'fc_cta':
						$button_url = $fc_content_type['cta_button_link'];
						if(strpos($button_url, 'gf=') !== false) {
							$has_gf_form = true;
							$gf_form_id = explode('=', $button_url)[1];
							$gf_form_html .= '<div id="gf_ajax_form_'.$gf_form_id.'"></div>';
						}
						break;

					case 'fc_feature':
						$feature_link_url = $fc_content_type['feature_link_url'];
						if(strpos($feature_link_url, 'gf=') !== false) {
							$has_gf_form = true;
							$gf_form_id = explode('=', $feature_link_url)[1];
							$gf_form_html .= '<div id="gf_ajax_form_'.$gf_form_id.'"></div>';
						}
						break;
					
					default:
						break;
				}

			}
		}

		$gf_form_html .= '</div></div>';

		if($has_gf_form) {
			echo $gf_form_html;
		}

	}

	public function enqueue_gf_scripts() {

		// Bail early if Gravity Forms is not active
		if(!function_exists('gravity_form_enqueue_scripts')) return;

		$fc_content_types = get_field('fc_content_types');

		if($fc_content_types) {
			foreach($fc_content_types as $fc_content_type) {
				
				switch ($fc_content_type['acf_fc_layout']) {

					case 'fc_buttons':
						$buttons = $fc_content_type['button'];
						if(!empty($buttons)) {
							foreach($buttons as $button) {
								$button_url = $button['button_url'];
								if(strpos($button_url, 'gf=') !== false) {
									$gf_form_id = explode('=', $button_url)[1];
									gravity_form_enqueue_scripts($gf_form_id, true);
								}
							}
						}
						break;

					case 'fc_cta':
						$button_url = $fc_content_type['cta_button_link'];
						if(strpos($button_url, 'gf=') !== false) {
							$gf_form_id = explode('=', $button_url)[1];
							gravity_form_enqueue_scripts($gf_form_id, true);
						}
						break;

					case 'fc_feature':
						$feature_link_url = $fc_content_type['feature_link_url'];
						if(strpos($feature_link_url, 'gf=') !== false) {
							$gf_form_id = explode('=', $feature_link_url)[1];
							gravity_form_enqueue_scripts($gf_form_id, true);
						}
						break;
					
					default:
						break;
				}

			}
		}

	}

}
