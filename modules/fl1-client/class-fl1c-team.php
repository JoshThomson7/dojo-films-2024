<?php
/**
 * FL1C_Team_Member
 *
 * Class in charge of team member
 */

 // Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class FL1C_Team_Member {

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
    public function name($name = '' | 'first' | 'last') {

        $full_name = get_the_title($this->id);

		switch ($name) {
			case 'first':
				return explode(' ', $full_name)[0];
				break;

			case 'last':
				return explode(' ', $full_name)[1];
				break;
			
			default:
				return $full_name;
				break;
		}

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
    public function image($width = 900, $height = 900, $crop = true) {

        $attachment_id = get_post_thumbnail_id($this->id);

        if($attachment_id) {
            return vt_resize($attachment_id, '', $width, $height, $crop);
        }

        return false;

    }

    /**
     * Returns job_title.
     * 
     * @return string
     */
    public function job_title() {

        return get_field('team_job_title', $this->id) ?? null;

    }

    /**
     * Returns team_phone.
     * 
     * @return string
     */
    public function phone() {

        return get_field('team_phone', $this->id) ?? null;

    }

    /**
     * Returns email.
     * 
     * @return string
     */
    public function email() {

        return get_field('team_email', $this->id) ?? null;

    }

	/**
     * Returns team_office.
     * 
     * @return string
     */
    public function office_id() {

        return get_field('team_office', $this->id) ?? null;

    }

	/**
     * Returns office name.
     * 
     * @return string
     */
    public function office() {

        return $this->office_id() ? get_the_title($this->office_id()) : '';

    }

	/**
     * Returns bio/content.
     * 
     * @return string
     */
    public function bio($trunc = 0) {

        $content = apply_filters( 'the_content', get_the_content(null, false, $this->id) );
        return $trunc ? FL1_Helpers::trunc($content, $trunc) : $content;

    }

}

