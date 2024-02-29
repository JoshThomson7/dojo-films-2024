<?php
/**
 * FL1C_FAQ
 *
 * Class in charge of single FAQ
 */

 // Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class FL1C_FAQ {

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
    public function excerpt() {

        return get_the_excerpt($this->id);

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
     * Returns answer/content.
     * 
     * @return string
     */
    public function answer($trunc = 0) {

        $content = apply_filters( 'the_content', get_the_content(null, false, $this->id) );
        return $trunc ? FL1_Helpers::trunc($content, $trunc) : $content;

    }

}

