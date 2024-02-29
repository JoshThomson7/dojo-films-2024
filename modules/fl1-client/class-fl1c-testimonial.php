<?php
/**
 * FL1C_Testimonial
 *
 * Class in charge of single testimonial
 */

 // Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class FL1C_Testimonial {

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
     * Returns rating.
     * 
     * @return int
     */
    public function name() {

        $name = get_field('review_name', $this->id) ?? '';
		return $name ? $name : $this->title(); 

    }

    /**
     * Returns rating.
     * 
     * @return int
     */
    public function company() {

        return get_field('review_company', $this->id) ?? '';

    }

    /**
     * Returns rating.
     * 
     * @return int
     */
    public function rating() {

        return (int)get_field('review_rating', $this->id) ?? 0;

    }

    /**
     * Returns quote.
     * 
     * @return int
     */
    public function quote($trunc = 0) {

        $quote = get_field('review_quote', $this->id);
        return $trunc ? FL1_Helpers::trunc($quote, $trunc) : $quote;

    }

    /**
     * Display rating.
     * 
     * @return int
     */
    public function rating_display() {

        $rating = $this->rating();
        $left = 5 - $rating;

        if($rating): ?>
            <div class="stars">
                <?php for($x = 1; $x <= $rating; $x++): ?>
                    <span style="color: #f0c914; font-size: 18px;">&#x2605;</span>
                <?php endfor; ?>

                <?php if($left): ?>
                    <?php for($x = 1; $x <= $left; $x++): ?>
                        <span style="color: #d1def7; font-size: 18px;">&#x2605;</span>
                    <?php endfor; ?>
                <?php endif; ?>
            </div>
        <?php endif;

    }

}

