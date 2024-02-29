<?php
/**
 * APF_Branch
 *
 * Class in charge of branch
 */

 // Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class APF_Branch {

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
    public function get_name() {

        return get_the_title($this->id);

    }

    /**
     * Returns permalink
     */
    public function get_permalink() {

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
	 * Returns the branch ID
	 */
    public function get_branch_id() {
		
		return get_field('branch_id', $this->id);
		
	}

	/**
	 * Returns the branch image ID
	 */
    public function get_branch_img_id() {
		
		return get_field('branch_image', $this->id);
		
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

        $attachment_id = $this->get_branch_img_id();

        if($attachment_id) {
            return vt_resize($attachment_id, '', $width, $height, $crop);
        }

        return false;

    }

	/**
	 * Returns the branch phone
	 */
    public function get_phone() {
		
		return get_field('branch_phone', $this->id);
		
	}

	/**
	 * Returns the branch email
	 */
    public function get_email($protected = true) {
		
		$email = get_field('branch_email', $this->id);
		return $email && $protected ? FL1_Helpers::hide_email($email) : $email;
		
	}

	/**
	 * Returns the branch address
	 */
    public function get_address($pluck = '') {
		
		$address = get_field('branch_address', $this->id) ?? array();
		return isset($address[$pluck]) ? $address[$pluck] : $address;
		
	}

	/**
	 * Returns the branch opening times
	 */
    public function get_opening_times() {
		
		return get_field('opening_times', $this->id);
		
	}

	public function opening_times() {

		// Bail early if no times have been set.
		$times = $this->get_opening_times();
		if (empty($times)) {
			return;
		}
	
		$now = new DateTime('now', wp_timezone());
		$current_time = $now->getTimestamp();
		$today = $now->format('l');
	
		$opening_times = array();
	
		foreach ($times as $time) {
			$weekday = $time['day'];
			$opening_time = $time['opening_time'];
			$closing_time = $time['closing_time'];
	
			$opening_time_date = new DateTime("$weekday $opening_time", wp_timezone());
			$closing_time_date = new DateTime("$weekday $closing_time", wp_timezone());
	
			$opening_time_timestamp = $opening_time_date->getTimestamp();
			$closing_time_timestamp = $closing_time_date->getTimestamp();

			$one_hour_before_closing_date = $closing_time_date->modify('-1 hour');
			$one_hour_before_closing = $one_hour_before_closing_date->getTimestamp();
	
			$status = new stdClass();
	
			if ($today === $weekday) {
				if ($current_time >= $opening_time_timestamp && $current_time <= $closing_time_timestamp) {

					$status->text = 'Open';
					$status->class = 'open';

					if ($current_time >= $one_hour_before_closing && $current_time <= $closing_time_timestamp) {
						$status->text = 'Closing soon';
						$status->class = 'closing-soon';
					}

				} elseif ($current_time > $closing_time_timestamp || $current_time < $opening_time_timestamp) {
					// Business is closed for the day.
					$status->text = 'Closed';
					$status->class = 'closed';

				}
			} else {
				$status->text = 'Closed';
				$status->class = 'closed';

				// Check if it's past closing time and set "Opens soon" status for the next day.
				$tomorrow = $now->modify('+1 day');
				$tomorrow_opening_time_date = new DateTime("$weekday $opening_time", wp_timezone());
				$tomorrow_opening_time_date->setDate($tomorrow->format('Y'), $tomorrow->format('m'), $tomorrow->format('d'));
				$tomorrow_opening_time_date->setTime($opening_time_date->format('H'), $opening_time_date->format('i'));
				$tomorrow_opening_time_timestamp = $tomorrow_opening_time_date->getTimestamp();

				$two_hours_before_opening_date = $tomorrow_opening_time_date->modify('-24 hours');
				$two_hours_before_opening = $two_hours_before_opening_date->getTimestamp();

				if ($current_time >= $two_hours_before_opening && $current_time < $tomorrow_opening_time_timestamp) {
					$status->text = 'Opens soon';
					$status->class = 'opens-soon';
				}
			}
	
			$display_times = ($opening_time && $closing_time) ? "$opening_time - $closing_time" : 'Closed';
			
			$day_times = new stdClass();
			$day_times->weekday = new stdClass();
			$day_times->opens = new stdClass();
			$day_times->closes = new stdClass();

			$day_times->weekday->day = $weekday;
			$day_times->weekday->is_today = ($today === $weekday) ? 'today' : '';
	
			$day_times->opens->display_time = $opening_time;
			$day_times->opens->timestamp = $opening_time_timestamp;
	
			$day_times->closes->display_time = $closing_time;
			$day_times->closes->timestamp = $closing_time_timestamp;
	
			$day_times->status = $status;
			$day_times->display = "<span class='weekday'>$weekday</span><span class='times'>$display_times</span>";

			array_push($opening_times, $day_times);
		}
	
		return $opening_times;
	}

    /**
     * todays_times()
     * 
     * @param int $post_id
     * @param bool True for string / false for array
     * @return array
     */
    public function todays_times() {

        // Bail early if no times have been set.
        if(!$this->get_opening_times()) return;
        
        $todays_weekday = date('l');

        // Get opening times
        $opening_times = $this->opening_times();

        $todays_times = new stdClass();

        // Loop through opening times
        foreach($opening_times as $times) { 
            if($todays_weekday == $times->weekday->day) { 
				$todays_times = $times;
				break;
			}
        }

        return $todays_times;

    }
	
}

