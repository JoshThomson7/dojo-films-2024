<?php
/**
 * FL1 Security
 *
 * Helper static functions
 *
 * @author  fl1
 * @link    http://fl1.digital
 * @version 1.0
 */

 // Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class FL1_Security {

    /**
     * Initialize hooks on load.
     *
     * @since 1.0
     * @return void
     */
    public function __construct() {

        // Remove WP version meta tag and from RSS feed
        add_filter('the_generator', '__return_false');

        // Disable ping back scanner and complete XMLRPC class
        add_filter('wp_xmlrpc_server_class', '__return_false');
        add_filter('xmlrpc_enabled', '__return_false');

        add_filter('style_loader_src', array($this, 'remove_wp_ver_css_js'), 9999 );
        add_filter('script_loader_src', array($this, 'remove_wp_ver_css_js'), 9999 );
        add_filter('wp_headers', array($this, 'remove_x_pingback'));

        add_filter('login_errors', array($this, 'login_errors'));
        add_filter('comment_class' , array($this, 'remove_comment_author_class'));

        add_filter('request', array($this, 'author_url_request'));
        add_filter('author_link', array($this, 'author_link'), 10, 3);
		add_action('wp', array($this, 'disable_author_page'));

        add_action('do_feed', array($this, 'go_disable_feed'), 1);
        add_action('do_feed_rdf', array($this, 'go_disable_feed'), 1);
        add_action('do_feed_rss', array($this, 'go_disable_feed'), 1);
        add_action('do_feed_rss2', array($this, 'go_disable_feed'), 1);
        add_action('do_feed_atom', array($this, 'go_disable_feed'), 1);
        add_action('do_feed_rss2_comments', array($this, 'go_disable_feed'), 1);
        add_action('do_feed_atom_comments', array($this, 'go_disable_feed'), 1);
        add_action('signup_header', array($this, 'prevent_multisite_signup'));
        add_action('user_profile_update_errors', array($this, 'set_user_nicename_to_nickname'), 10, 3 );

        remove_action('wp_head', 'print_emoji_detection_script', 7);
        remove_action('admin_print_scripts', 'print_emoji_detection_script');
        remove_action('wp_print_styles', 'print_emoji_styles');
        remove_action('admin_print_styles', 'print_emoji_styles');
        remove_action('wp_head', 'feed_links_extra', 3);
        remove_action('wp_head', 'rsd_link');
        remove_action('wp_head', 'wlwmanifest_link');
        remove_action('wp_head', 'wp_generator');
        remove_action('wp_head', 'start_post_rel_link');
        remove_action('wp_head', 'index_rel_link');
        remove_action('wp_head', 'parent_post_rel_link');
        remove_action('wp_head', 'adjacent_posts_rel_link_wp_head'); // for WordPress >= 3.0

    }

    public function remove_wp_ver_css_js( $src ) {
        if ( strpos( $src, 'ver=' ) ) {
            $src = remove_query_arg( 'ver', $src );
        }

        return $src;
    }

    public function go_disable_feed() {
        wp_die( __('No feed available. Back to <a href="'. get_bloginfo('url') .'">website</a>.') );
    }

    public function prevent_multisite_signup() {
        wp_redirect(site_url());
        wp_die();
    }

    public function remove_x_pingback($headers) {
        unset($headers['X-Pingback']);
        return $headers;
    }

    public function login_errors() {
        return 'Incorrect email or password. Please try again.';
    }

    public function remove_comment_author_class( $classes ) {
        foreach( $classes as $key => $class ) {
            if(strstr($class, "comment-author-")) {
                unset( $classes[$key] );
            }
        }
    
        return $classes;
    }
    
    /**
     * Change the author URL slug so it doesn't show the actual username but the nickname
     * @see http://wordpress.stackexchange.com/questions/5742/change-the-author-slug-from-username-to-nickname
     */
    public function author_url_request( $query_vars ) {
        if ( array_key_exists( 'author_name', $query_vars ) ) {
            global $wpdb;
            $author_id = $wpdb->get_var( $wpdb->prepare( "SELECT user_id FROM {$wpdb->usermeta} WHERE meta_key='nickname' AND meta_value = %s", $query_vars['author_name'] ) );
            if ( $author_id ) {
                $query_vars['author'] = $author_id;
                unset( $query_vars['author_name'] );
            }
        }
    
        return $query_vars;
    }

    public function author_link( $link, $author_id, $author_nicename ) {
		$user = new FL1_User( $author_id );
		$full_name = strtolower(str_replace(' ', '-', $user->get_full_name()));
        $link = str_replace( $author_nicename, $full_name, $link );
        return $link;
    }

	/**
	 * Disable author page
	 * 
	 * @see https://wordpress.stackexchange.com/questions/288868/remove-author-link-wherever-authors-name-is-display
	 */
	public function disable_author_page() {
		global $wp_query;
		if ($wp_query->is_author ) {
			wp_safe_redirect( get_bloginfo( 'url' ), 301 );
			exit;
		}
	
	}

    public function set_user_nicename_to_nickname( &$errors, $update, &$user ) {
        if ( ! empty( $user->nickname ) ) {
            $user->user_nicename = sanitize_title( $user->nickname, $user->display_name );
        }
    }

}