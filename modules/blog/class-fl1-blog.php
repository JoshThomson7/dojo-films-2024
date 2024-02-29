<?php
/**
 * FL1 Blog
 *
 * Class in charge of single blog
 */

 // Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class FL1_Blog {

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
     * Returns the exceprt
     * 
     * @param int trunc
     */
    public function excerpt($trunc = 0) {

        return $trunc ? FL1_Helpers::trunc(get_the_excerpt($this->id), $trunc) : get_the_excerpt($this->id);

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
     * Returns blog image.
     * 
     * @param int $width
     * @param int $height
     * @param bool $crop
     * @see vt_resize() in modules/wp-image-resize.php
     */
    public function image($width = 900, $height = 500, $crop = true) {

        $attachment_id = get_post_thumbnail_id($this->id());

        if($attachment_id) {
            return vt_resize($attachment_id,'' , $width, $height, $crop);
        }

        return false;

    }

	/**
     * Returns blog image.
     * 
     * @param int $width
     * @param int $height
     * @param bool $crop
     * @see vt_resize() in modules/wp-image-resize.php
     */
    public function get_image($width = 900, $height = 500, $crop = true) {

        $attachment_id = get_post_thumbnail_id($this->id);

        if($attachment_id) {
            return vt_resize($attachment_id,'' , $width, $height, $crop);
        }

        return false;

    }

    /**
     * Checks if blog is members only
     * 
     * @param string $pluck
     */
    public function is_members_only() {

        if($this->membership('slug') === 'member') {
            return true;
        }

        return false;

    }

    /**
     * Returns the main category (first in array, index [0] via reset()).
     * 
     * @see https://www.php.net/manual/en/function.reset.php
     * @param string $return | See $this->categories() above
     */
    public function main_category($return = null) {

        $post_cats = $this->get_terms('category', $return);
        
        if(!empty($post_cats) && !is_wp_error($post_cats)) {
            return reset($post_cats);
        }

        return null;
        
    }

    /**
     * Returns membership type
     * 
     * @param string $pluck
     */
    public function membership($pluck = '') {

        $term = null;
        $terms = $this->get_terms('post_membership', 'all');

        if(!empty($terms)) {
            $term = reset($terms);

            if($pluck) {
                $term = $term->$pluck;
            } else {
                $term = $term;
            }
        }

        return $term;

    }


    /**
     * Returns the service categories.
     * 
     * @param string $taxonomy
     * @param string $return | Accepts: all | all_with_object_id | ids | tt_ids | slugs | count | id=>parent | id=>name | id=>slug
     * @see https://developer.wordpress.org/reference/classes/wp_term_query/__construct/
     */
    public function get_terms($taxonomy = '', $return = null) {

        $args = $return ? array('fields' => $return) : array();        
        $post_terms = wp_get_object_terms($this->id, $taxonomy, $args);

        if(!empty($post_terms) && !is_wp_error($post_terms)) {
            return $post_terms;
        }

        return null;

    }

}

