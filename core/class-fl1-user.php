<?php
/**
 * FL1 User
 *
 * Class in charge of patient users
 * 
 */

 // Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class FL1_User {
    
    private $id;
    private $user_data;
    private $user_meta;

    public function __construct( $id ) {

        $this->id = $id;
        $this->user_data = $this->get_data();
        $this->user_meta = $this->get_meta();

    
    }

    /**
     * Gets the user ID.
     *
     * @since 1.0
     * @access public
     */
    public function get_id() {
        return $this->id;
    
    }

    /**
     * Gets user data.
     *
     * @since 1.0
     * @access public
     */
    public function get_data() {
        return get_userdata($this->id);
    
    }

    /**
     * Gets user meat data.
     *
     * @since 1.0
     * @access public
     */
    public function get_meta($field = '') {

        $meta = get_user_meta($this->id);

        if($field) {
            return isset($meta[$field]) && is_array($meta[$field]) ? $meta[$field][0] : null;
        }

        return $meta;
    
    }

    /**
     * Gets WP user login.
     *
     * @since 1.0
     * @access public
     */
    public function get_username() {

        return $this->user_data->user_login;
    
    }

    /**
     * Gets user role.
     *
     * @since 1.0
     * @access public
     */
    public function get_roles() {
        return $this->user_data && is_array($this->user_data->roles) ? $this->user_data->roles : array();
    
    }

    /**
     * Gets user first name.
     *
     * @since 1.0
     * @param bool $genitive
     * @access public
     */
    public function get_first_name($genitive = false) {

        $first_name = $this->user_data->first_name;
        return $first_name . FL1_Helpers::genitive_case($genitive ? $first_name : null);
    
    }

    /**
     * Gets user last name.
     *
     * @since 1.0
     * @param bool $genitive
     * @access public
     */
    public function get_last_name($genitive = false) {

        $last_name = $this->user_data->last_name;

        return $this->user_data->last_name . FL1_Helpers::genitive_case($genitive ? $last_name : null);

    }

    /**
     * Gets user full name.
     *
     * @since 1.0
     * @param bool $genitive
     * @access public
     */
    public function get_full_name($genitive = false) {

        $full_name = $this->get_first_name() . ' ' . $this->get_last_name();
        $full_name = mb_convert_case($full_name, MB_CASE_TITLE, "UTF-8");

        return $full_name . FL1_Helpers::genitive_case($genitive ? $this->get_last_name() : null);
    
    }

    /**
     * Gets user email.
     *
     * @since 1.0
     * @access public
     */
    public function get_email() {

        return $this->user_data->user_email;
    
    }

    /**
     * Gets user email.
     *
     * @since 5.3.8
     * @access public
     */
    public function get_last_login() {

        $session_tokens = get_user_meta($this->id, 'session_tokens', true);

        if (empty($session_tokens)) {
            return '';
        
        }

        $session_token = array_pop($session_tokens);
        $login_stamp = $session_token['login'];
        $currentTime = new DateTime();
        $currentTime = DateTime::createFromFormat('U', $login_stamp);
        return $currentTime->format('j M Y H:i');
    
    }

    /**
     * Gets user initials.
     *
     * @since 1.0
     * @access public
     */
    public function get_initials() {

        return mb_substr($this->get_first_name(), 0, 1, 'utf-8') . mb_substr($this->get_last_name(), 0, 1, 'utf-8');
    
    }

    /**
     * Gets user profile (author) URL.
     *
     * @since 1.0
     * @access public
     */
    public function get_profile_url($endpoint = null) {

        $user_profile_url = get_author_posts_url($this->id);

        if ($endpoint) {
            $user_profile_url .= $endpoint . '/';
        
        }

        return esc_url($user_profile_url);
    
    }

    /**
     * Returns the user picture ID from ACF
     */
    public function get_profile_picture_id() {
        return get_field('user_profile_picture', 'user_' . $this->id);
    
    }

    /**
     * Returns the user picture ID from ACF
     */
    public function get_profile_picture($width = 600, $height = 600, $crop = true) {

        $user_picture_id = $this->get_profile_picture_id();

        if($user_picture_id) {
            $user_img = vt_resize($user_picture_id, '', $width, $height, $crop);
            return is_array($user_img) && isset($user_img['url']) ? $user_img['url'] : '';
        
        }

        return false;

    
    }

    /**
     * Rest API Data outputget_id
     * 
     * @return object $data
     */
    public function rest_api_data() {

        $data = new stdClass();

        $data->ID = $this->id;
        $data->username = $this->get_username();
        $data->email = $this->get_email();
        $data->fullName = $this->get_full_name();
        $data->firstName = $this->get_first_name();
        $data->lastName = $this->get_last_name();
        $data->initials = $this->get_initials();
        $data->profilePicture = $this->get_profile_picture();

        return $data;

    
    }

}