<?php
/**
 * Class in charge of creating crons
 *
 * @since      1.0
 * @package    Core
 * 
 * IMPORTANT: Remember to add this to class-smart.php
 * inside the if statement of the instance() method.
 * 
 * $fl1_cron = new FL1_Cron();
 * add_action('fl1_cron', array($fl1_cron, 'cron_callback'), 10, 2);
 * 
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH') ) exit;

class FL1_Cron {

    public $hook_function = 'fl1_cron';
    public $when;
    public $cron_args;
    public $recurrence;

    /**
     * Construct
     * 
     * @param string $hook_function
     * @param int $when (timestamp)
     * @param array $cron_args
     * @param string $recurrence
     */
    public function __construct($when = null, $cron_args = array(), $recurrence = null) {

        $this->when = $when;
        $this->cron_args = $cron_args;
        $this->recurrence = $recurrence;

        // Bail early if no date
        if(empty($this->when)) { 
            return new WP_Error('date_missing', __('A date (timestamp) is required to register a cron', 'tpiw'));
        }

        // Clear existing cron
        if($this->when === 'clear') {
            
            wp_clear_scheduled_hook($this->hook_function, $this->cron_args);

        } else {
     
            // One-off.
            if(empty($this->recurrence)) {
                wp_schedule_single_event($this->when, $this->hook_function, $this->cron_args);

            // Recurring.
            } else {
                wp_schedule_event($this->when, $this->recurrence, $this->hook_function, $this->cron_args);
            }

        }

    }

    /**
     * Cron callback
     * 
     * @param $string
     */
    public function cron_callback($callback, $args) {

        if(!empty($callback)) {
            $this->$callback($args);
        }
    
    }

    /** ============================================================ *
     * 
     * Callbacks
     * 
     * ============================================================ */

    public function expire_event($args) {

        $post_id = isset($args['post_id']) && !empty($args['post_id']) ? $args['post_id'] : null;

        if($post_id) { // If 0, then it's a guest

            $event = new WVL_Event($post_id);
            $event->expire();

        }

    }
    
}