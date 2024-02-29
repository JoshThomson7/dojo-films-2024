<?php
/**
 * X1_Development
 *
 * Class in charge of developments
 */

 // Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class X1_Development {

    /**
	 * The post ID.
	 *
	 * @since 1.0
	 * @access   private
	 * @var      string
	 */
    protected $id;
    
    /**
	 * Initialize the class and set its properties.
	 *
	 * @since 1.0
	 * @access public
	 * @param int $id
	 */
    public function __construct($id = null) {

        $this->id = $id;

    }

    /**
     * Gets post ID.
     * If not set, use global $post
     */
    public function id() {

        if($this->id) {

            return $this->id;

        } else {

            global $post;
            
            if(isset($post->ID)) {
                return $post->ID;
            }

        }

        return null;

    }

    /**
     * Returns post title
     */
    public function title() {

        return get_the_title($this->id);

    }

    /**
     * Returns permalink
     */
    public function url() {

        return get_permalink($this->id);

    }

    /**
     * Returns date
     * 
     * @param string $format
     */
    public function date($format = 'M jS Y') {

        return get_the_time($format, $this->id);

    }

    /**
     * Returns featured image.
     * 
     * @param int $width
     * @param int $height
     * @param bool $crop
     * @see vt_resize() in modules/wp-image-resize.php
     */
    public function image($width = 1200, $height = 900, $crop = true) {

        $attachment_id = get_post_thumbnail_id($this->id);

        if($attachment_id) {
            return vt_resize($attachment_id, '', $width, $height, $crop);
        }

        return false;

    }

    /**
     * Returns development_reapit_id.
     * 
     * @return array
     */
    public function reapit_id() {

        return get_field('development_reapit_id', $this->id) ?? null;

    }

	/**
     * Returns development_gallery.
     * 
     * @return array
     */
    public function gallery_ids() {

        return get_field('development_gallery', $this->id) ?? null;

    }

    /**
     * Returns gallery URLs.
     * 
     * @return array
     */
    public function gallery($width = 1200, $height = 900, $crop = true) {

        $gallery_ids = $this->gallery_ids();

		if(!empty($gallery_ids)) {

			$gallery = array();

			foreach($gallery_ids as $gallery_id) {
				$image = vt_resize($gallery_id, '', $width, $height, $crop);
				if(!is_wp_error($image) && is_array($image) && isset($image['url'])) {
					$gallery[] = $image['url'];
				}
			}

			return $gallery;

		}

		return false;

    }

	/**
     * Returns development_team.
     * 
     * @return array
     */
    public function primary_contacy() {

        return get_field('development_team', $this->id) ?? null;

    }

	/**
     * Returns development_team.
     * 
     * @return array
     */
    public function team_member() {

        $team_id = $this->primary_contacy();
		if(!$team_id) return;

		return new FL1C_Team_Member($team_id);

    }

	/**
     * Returns development_team_bio.
     * 
     * @return array
     */
    public function team_member_bio() {

        return get_field('development_team_bio', $this->id) ?? null;

    }

	/**
     * Returns development_location.
     * 
     * @return array
     */
    public function location() {

        return get_field('development_location', $this->id) ?? null;

    }

}

