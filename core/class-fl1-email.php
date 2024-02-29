<?php

/**
 * Class in charge of emails.
 *
 * @since      1.0
 * @package    WP
 */

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

class FL1_Email {

    private $recipients;
    private $subject;
    private $body;
    private $template;
    private $headers = array();
    private $attachments;

    /**
     * Init hooks
     */
    public function init() {

        // From name and email address
        add_filter('wp_mail_from', array($this, 'custom_from_address'));
        add_filter('wp_mail_from_name', array($this, 'custom_from_name'));

        // HTML emails
        add_filter('wp_mail_content_type', array($this, 'wp_mail_html_it_up'));

        // WP core emails
        add_filter('wp_new_user_notification_email', array($this, 'custom_new_user_notification_email'), 10, 3);
        add_filter('wp_new_user_notification_email_admin', array($this, 'custom_new_user_notification_email_admin'), 10, 3);
        add_filter('retrieve_password_message', array($this, 'custom_retrieve_password_message'), 20, 4);
        add_filter('retrieve_password_title', array($this, 'custom_retrieve_password_title'));
        add_filter('password_change_email', array($this, 'custom_password_change_email'), 20, 3);
        add_filter('email_change_email', array($this, 'custom_email_change_email'), 20, 3);

		add_filter('magic_login_email_subject', array($this, 'magic_login_email_subject'), 20, 2);
		add_filter('magic_login_email_content', array($this, 'magic_login_email_content'), 20, 2);
    }

    /**
     * Set From header
     * 
     * @param string $from
     * @param string $name
     */
    public function From($from = null, $name = null) {
        $this->headers[] = 'From: ' . $name . ' <' . $from . '>';
    }

    /**
     * Sets email subject
     * 
     * @param string $subject
     */
    public function Subject($subject = null) {

        if (!$subject) {
            $subject = 'Notification';
        }

        $this->subject = $subject;
    }

    /**
     * Sets headers
     * 
     * @param array $headers
     */
    public function Headers($headers = array()) {

        if(!empty($headers)) {
            foreach($headers as $header) {
                $this->headers[] = $header;
            }
        }
    }

    /**
     * Set recipients
     * 
     * @param array $recipient
     */
    public function To($recipients = array()) {

        $this->recipients = $recipients;
    }

    /**
     * Set custom body
     * 
     * @param string $body
     */
    public function Body($body) {

        $this->body = $body;
    }

    /**
     * Sets attachments
     * 
     * @param array $files
     */
    public function Attachments($files = array()) {

        $this->attachments = $files;
    }

    /**
     * Set custom body
     * 
     * @param string $body
     */
    public function Template($tag) {

        $this->template = $tag;
    }

    /**
     * Global email styles.
     *
     * @since 1.0
     */
    public function get_email_styles() {

        $styles = array(
            'background' => 'background: #f7f8fb; padding: 40px 0; width: 100%; height: 100%;',
            'wrapper' => 'max-width: 600px; margin: 0 auto; padding: 40px; background: #fff; border-radius: 8px; font-family: sans-serif;',
            'logo' => array(
                'a' => 'width: 64px; margin: 0 auto 40px; display: block;',
                'img' => 'width: 64px;'
            ),
            'h2' => 'font-size: 17px; color: #E61689; font-weight: 600;',
            'h3' => 'font-size: 15.5px; color: #E61689; font-weight: 600;',
            'p' => 'color: #525f7f; font-size: 14px; margin-bottom: 14px;',
            'small' => 'color: #525f7f; font-size: 11px;',
            'strong' => 'font-weight: 700;',
            //'a' => 'color: #E61689;',
            'ul' => 'padding-left: 20px; margin-bottom: 20px;',
            'li' => 'color: #525f7f; font-size: 14px; margin-bottom: 14px;',
			'button' => 'background: #E61689; border-radius: 8px; color: #fff; display: inline-block; padding: 6px 25px; text-decoration: none; font-weight: bold;'#
        );

        return $styles;
    }

    /**
     * Replace styles if found
     */
    public function apply_styles($body) {

        $styles = $this->get_email_styles();

        // Loop through styles and replace
        if (!empty($styles) && is_array($styles)) {

            foreach ($styles as $key => $value) {

                if ($key === 'logo' || $key === 'button') {
                    continue;
                }

                $body = str_replace('<' . $key, '<' . $key . ' style="' . $value . '" ', $body);
            }
        }

        return $body;
    }

    /**
     * Email template.
     *
     * @since 1.0
     */
    public function email_template($message = null) {

        $styles = $this->get_email_styles();

        if (!$message) {
            $message = $this->body;
        }

        $template = '
            <div style="' . $styles['background'] . '">
                <div style="' . $styles['wrapper'] . '">
                    
                    <a href="' . esc_url(home_url()) . '" title="' . get_bloginfo('name') . '" style="' . $styles['logo']['a'] . '">
                        <img src="' . esc_url(get_stylesheet_directory_uri()) . '/img/logo.png" alt="' . get_bloginfo('name') . '" style="' . $styles['logo']['img'] . '">
                    </a>

                    <h2 style="' . $styles['h2'] . '">' . $this->subject . '</h2>
                    ' . make_clickable($this->apply_styles($message)) . '
                    <p style="' . $styles['p'] . '"><strong>The ' . get_bloginfo('name') . ' Team</strong></p>
                    <p style="' . $styles['p'] . ' margin-top: 20px; padding-top: 20px; border-top: 1px #dfe6f5 solid;"><small style="' . $styles['small'] . '">The information contained in this document may be privileged and confidential and is intended for the exclusive use of the addressee designated above. If you are not the addressee, any disclosure, reproduction, distribution or other dissemination or use of this communication is strictly prohibited. If you have received this email in error please inform the sender.</small></p>
                </div>
            </div>';

        return $template;
    }

    /**
     * Fire email
     */
    public function send() {

        if (!empty($this->recipients) && is_array($this->recipients)) {

            wp_mail($this->recipients, $this->subject, $this->email_template(), $this->headers, $this->attachments);
        }
    }

    /**
     * HTML email up
     */
    public function wp_mail_html_it_up() {
        return 'text/html';
    }

    /**
     * Custom from address
     * 
     * @param string $email
     */
    public function custom_from_address($email) {
        $host = str_replace('http://', '', get_site_url());
        $host = str_replace('https://', '', $host);
        $host = str_replace('www.', '', $host);
        return 'no-reply@'.$host;
    }

    /**
	 * Custom from email
	 * 
	 * @param string $from_name
	 */
	public function custom_from_name($from_name) {
		$decoded_name = html_entity_decode(get_bloginfo('name'), ENT_QUOTES, 'UTF-8');
		return $decoded_name;
	}


    /**
     * Custom retrieve password subject email
     * 
     */
    public function custom_retrieve_password_title() {
        return 'Reset your password';
    }

    /**
     * Reset password email override
     */
    public function custom_retrieve_password_message($message, $key, $user_login, $user_data) {

        $styles = $this->get_email_styles();

        $user_id = $user_data->data->ID;
        $user = new FL1_User($user_id);

        $password_reset_url = network_site_url('wp-login.php?action=rp&key=' . $key . '&login=' . rawurlencode($user_login), 'login');

        $body = $this->Body('
            <h2 style="' . $styles['h2'] . '">Hi ' . $user->get_first_name() . '</h2>

            <p style="' . $styles['p'] . '">You have requested to reset your password for the following account:</p>
            <p style="' . $styles['p'] . '"><strong>' . $user_login . '</strong></p>
            
            <p style="' . $styles['p'] . '">To reset it, please click on the button below.</p>
            <p style="' . $styles['p'] . '"><a href="' . $password_reset_url . '" style="' . $styles['button'] . '">Reset your password</a></p>
            
            <p style="' . $styles['p'] . '">If you didn\'t make this request, simply ignore this email and nothing will happen.</p>
        ');

        $message = $this->email_template($body);

        return $message;
    }

    /**
     * Password changed email override
     */
    public function custom_password_change_email($change_mail, $user, $userdata) {

        $styles = $this->get_email_styles();

        $user_id = $user['ID'];
        $user = new FL1_User($user_id);

        $body = $this->Body('
            <h2 style="' . $styles['h2'] . '">Hi ' . $user->get_first_name() . '</h2>

            <p style="' . $styles['p'] . '">This notice confirms that your password was changed on ' . get_bloginfo('name') . '.</p>
            <p style="' . $styles['p'] . '">If you did not change your password and are not aware of us having reset it for you, please contact us immediately.</p>
        ');

        $message = $this->email_template($body);

        $change_mail['subject'] = 'Password changed';
        $change_mail['message'] = $message;

        return $change_mail;
    }
	
    /**
     * Email changed email override
     */
    public function custom_email_change_email($email_change_email, $user, $userdata) {

		$email_change_email['subject'] = 'Email address changed';

        $styles = $this->get_email_styles();

        $user_id = $user['ID'];
        $user = new FL1_User($user_id);

        $body = $this->Body('
            <h2 style="' . $styles['h2'] . '">Hi ' . $user->get_first_name() . '</h2>

            <p style="' . $styles['p'] . '">This email confirms that your email was changed on '.get_home_url().'.</p>
            <p style="' . $styles['p'] . '">Your old email was ###EMAIL###. Your new one is ###NEW_EMAIL###.</p>
            <p style="' . $styles['p'] . '">If you did not change your email and are not aware of us having changed it for you, please contact us immediately.</p>
        ');

        $email_change_email['message'] = $this->email_template($body);

        return $email_change_email;
    }

    /**
     * Overrides core WP user notification email
     * 
     * @param array $email_data
     * @param object $user WP_User object
     * @param string $blogname
     */
    public function custom_new_user_notification_email($email_data, $user, $blogname) {

        // Parse message string into variables
        // so we can get the password reset key
        parse_str($email_data['message'], $message);

        // User data
        $user_login = $user->user_email;
        $key = $message['key'];
        $password_reset_url = network_site_url('wp-login.php?action=rp&key=' . $key . '&login=' . rawurlencode($user_login), 'login');

        // Create user object
        $user = new FL1_User($user->ID);

        /**
         * Prepare custom email
         */
        // Load styles
        $styles = $this->get_email_styles();

        // Custom body message
        $body = $this->Body('
            <h2 style="' . $styles['h2'] . '">Hi ' . $user->get_first_name() . '</h2>

            <p style="' . $styles['p'] . '">Your new account is almost ready.</p>
            
            <p style="' . $styles['p'] . '">Simply click on the button below and you will be taken to a page to set your new password.</p>
            <p style="' . $styles['p'] . '"><a href="' . $password_reset_url . '" style="' . $styles['button'] . '">Set your new password</a></p>
            
            <p style="' . $styles['p'] . '">If you think this email was not for you, simply ignore it and nothing will happen.</p>
        ');

        // Inject custom message body into template
        $message = $this->email_template($body);

        // Override email props
        $email_data['subject'] = 'Your new account';
        $email_data['message'] = $message;

        return $email_data;
    }

    /**
     * Overrides core WP user notification email
     * 
     * @param array $email_data
     * @param object $user WP_User object
     * @param string $blogname
     */
    public function custom_new_user_notification_email_admin($email_data, $user, $blogname) {

        // Parse message string into variables
        // so we can get the password reset key
        parse_str($email_data['message'], $message);

        // User data
        $user_login = $user->user_email;

        // Create user object
        $user = new FL1_User($user->ID);

        /**
         * Prepare custom email
         */
        // Load styles
        $styles = $this->get_email_styles();

        // Custom body message
        $body = $this->Body('
            <h2 style="' . $styles['h2'] . '">New user account</h2>

            <p style="' . $styles['p'] . '">A new user account with email address <strong>' . $user_login . '</strong> has been created.</p>
            <p style="' . $styles['p'] . '">If you think this is a mistake, please get in touch with the user in question: <strong>' . $user->get_full_name() . '.</strong></p>
        ');

        // Inject custom message body into template
        $message = $this->email_template($body);

        // Override email props
        $email_data['subject'] = 'New user account';
        $email_data['message'] = $message;

        return $email_data;
    }

	/**
	 * Overrides Magic Link email subject
	 * 
	 * @param string $login_email
	 * @param array $placeholder_values
	 */
	public function magic_login_email_subject($login_email, $placeholder_values) {

		return '🪄 Your magic login link';

	}

	/**
	 * Overrides Magic Link email content
	 * 
	 * @param string $login_email
	 * @param array $placeholder_values
	 */
	public function magic_login_email_content($login_email, $placeholder_values) {

		// $placeholder_values = [
		// 	'{{SITEURL}}'               => home_url(),
		// 	'{{USERNAME}}'              => $user->user_login,
		// 	'{{SITENAME}}'              => $site_name,
		// 	'{{EXPIRES}}'               => $settings['token_ttl'],
		// 	'{{EXPIRES_WITH_INTERVAL}}' => $token_ttl . ' ' . $selected_interval_str,
		// 	'{{MAGIC_LINK}}'            => $login_link,
		// 	'{{TOKEN_VALIDITY_COUNT}}'  => $settings['token_validity'],
		// ];

		/**
		 * Prepare custom email
		 */
		// Load styles
		$styles = $this->get_email_styles();

		// Custom body message
		$body = $this->Body('
			<h2>Here\'s your magic link 🪄</h2>
			<p>You have requested to log in to ' . get_bloginfo('name') . '.</p>
			
			<p>Simply click on the button below and you will be automatically logged in.</p>
			<p><a href="' . $placeholder_values['{{MAGIC_LINK}}'] . '" style="' . $styles['button'] . '">Log in</a></p>
			<p>If the button does not work, you can copy the link below and paste it in the address bar:</p>
			<p>'.$placeholder_values['{{MAGIC_LINK}}'].'</p>
			<p>If you did not request this, ignore this email and nothing will happen.</p>
		
		');

		// Inject custom message body into template
		$message = $this->email_template($body);

		return $message;

	}
}
