<?php
/**
 * AVB Banner Image
 * Class in charge of the Image type banner
 * 
 * @package AVB
 */

 // Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class AVB_Banner_Image extends AVB_Banner {

	private $classes = array('image');

    public function image($return = 'url', $width = 2000, $height = 1400, $crop = true) {

        $image_id = $this->get_prop('image');
        $image = '';
        if($image_id) {
            $image = vt_resize($image_id, '', $width, $height, $crop);
			if(is_array($image)) {
				$image['alt'] = get_post_meta($image_id, '_wp_attachment_image_alt', true) ?? '';
				return isset($image[$return]) ? $image[$return] : null;
			}
			return null;
        }

        return null;

    }

	public function classes() {

		if(!$this->image()) {
			$this->classes[] = 'no-img';
		}

		if($this->get_prop('image_mobile')) {
			$this->classes[] = 'hide-on-mobile';
		}

		return implode(' ', $this->classes);
	}

}
