<?php
/**
 * FL1 Admin
 *
 * Admin hooks and filters
 *
 * @author  fl1
 * @link    http://fl1.digital
 * @version 1.0
 */

 // Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class FL1_Admin {

    /**
     * Initialize hooks on load.
     *
     * @since 1.0
     * @return void
     */
    public function __construct() {

        add_theme_support('post-thumbnails');
        add_theme_support('menus');
        add_theme_support('nav-menus');

        add_filter('login_display_language_dropdown', '__return_false');
        add_filter('wp_fatal_error_handler_enabled', '__return_false');
        add_filter('upload_mimes', array($this, 'mime_types'), 11);
        add_filter('posts_search', array($this, 'post_meta_search'), 500, 2 );
        add_filter('acf/settings/show_admin', array($this, 'acf_hide_menu'));
        add_filter('admin_footer_text', array($this, 'fl1_footer_admin'));
        add_filter('wpseo_metabox_prio', array($this, 'yoast_to_bottom'));
        add_filter('gettext', array($this, 'admin_replace_text'), 10, 2);

        add_action('admin_menu', array($this, 'remove_core_menus'));
        add_action('admin_head', array($this, 'robots_blocked_alert'));
        add_action('wp_before_admin_bar_render', array($this, 'wp_logo_remove'), 0);

    }

    /**
     * Register custom mime types.
     *
     * @since 1.0
     * @return void
     */
    public function mime_types($mimes) {
        $mimes['svg'] = 'image/svg+xml';
        return $mimes;
    }

    /**
     * Custom search query that includes custom fields.
     *
     * @since 1.0
     * @return $where
     */
    /**
     * Custom search query that includes custom fields.
     *
     * @since 1.0
     * @return $where
     */
    public function post_meta_search( $where, $wp_query ) {

        global $wpdb;
    
        $prefix = $wpdb->prefix;
    
        if ( empty( $where ))
            return $where;
    
        // get search expression
        $terms = sanitize_text_field($wp_query->query_vars[ 's' ]);
    
        // explode search expression to get search terms
        $exploded = explode( ' ', $terms );
        if( $exploded === FALSE || count( $exploded ) == 0 )
            $exploded = array( 0 => $terms );
    
        // reset search in order to rebuilt it as we whish
        $where = '';

        $searchables = apply_filters('fl1_searchables', array(
            'taxonomies' => array(),
            'meta' => array()
        ), $wp_query->query_vars['post_type']);
    
        $search_tax = $searchables['taxonomies'];
        $search_meta = $searchables['meta'];
    
        foreach( $exploded as $tag ) :
            $where .= "
              	AND ((".$prefix."posts.post_title LIKE '%$tag%')
					OR (".$prefix."posts.post_content LIKE '%$tag%')
					OR (".$prefix."posts.post_excerpt LIKE '%$tag%')";

				if(!empty($search_tax)):	
					$where .= "
					OR EXISTS (
						SELECT * FROM ".$prefix."postmeta
						WHERE post_id = ".$prefix."posts.ID
							AND (";
							foreach ($search_meta as $search_meta_item) :
								if ($search_meta_item == $search_meta[0]):
									$where .= " (meta_key LIKE '%" . $search_meta_item . "%' AND meta_value LIKE '%$tag%') ";
								else :
									$where .= " OR (meta_key LIKE '%" . $search_meta_item . "%' AND meta_value LIKE '%$tag%') ";
								endif;
							endforeach;
					$where .= ")";
				endif;

                $where .= "
					OR EXISTS (
					SELECT * FROM ".$prefix."comments
					WHERE comment_post_ID = ".$prefix."posts.ID
						AND comment_content LIKE '%$tag%'
					)";

				if(!empty($search_tax)):
					$where .= "
					OR EXISTS (
						SELECT * FROM ".$prefix."terms
						INNER JOIN ".$prefix."term_taxonomy
							ON ".$prefix."term_taxonomy.term_id = ".$prefix."terms.term_id
						INNER JOIN ".$prefix."term_relationships
							ON ".$prefix."term_relationships.term_taxonomy_id = ".$prefix."term_taxonomy.term_taxonomy_id
							WHERE taxonomy IN ('".implode("','", $search_tax)."')
							AND object_id = ".$prefix."posts.ID
							AND ".$prefix."terms.name LIKE '%$tag%'
					)";
				endif;
		$where .= ")";
        endforeach;
		//pretty_print($where);
        return $where;
    }

    /**
     * Hides ACF menu if not in list.
     */
    public function acf_hide_menu() {

        $admins = array(
            'TWS',
        );

        $current_user = wp_get_current_user();
		$user_login = $current_user->user_login;
		
        return in_array($current_user->user_login, $admins) || strpos($user_login, '@fl1.digital') !== false;

    }

    public function robots_blocked_alert() {
        if(get_option('blog_public') == 0):
        ?>
            <script type="text/javascript">
                jQuery(document).ready(function($) {
                    jQuery('.wrap > h2').parent().prev().after('<div class="update-nag notice notice-warning" style="display: block;"><strong>Robots blocked</strong>. Remember to change this setting when going live under <strong>Settings > Reading</strong> by unchecking the <strong>Discourage search engines from indexing this site</strong> option.</div>');
                });
            </script>
        <?php
        endif;
    } 

    public function wp_logo_remove() {
        global $wp_admin_bar;
        $wp_admin_bar->remove_menu('wp-logo');
    }

    public function fl1_footer_admin()  {
        echo '<span style="display:flex; align-items:center;"><i class="dashicons dashicons-editor-code" style="margin:0 3px;font-size:16px;line-height:21px;" title="Developed"></i> with <i class="dashicons dashicons-heart" style="margin:0 3px;font-size:16px;line-height:21px;" title="love"></i> by <a href="https://fl1.digital" title="FL1 Digital" target="_blank"><img src="'.get_stylesheet_directory_uri().'/img/fl1-icon.svg" style="width:22px; margin:3px 3px 0 5px;"></a> using <i class="dashicons dashicons-wordpress-alt" style="margin:0 3px;font-size:16px;line-height:21px;" title="WordPress"></i></span>';
    }

    public function remove_core_menus() {
        remove_menu_page('edit-comments.php');
    }

    public function yoast_to_bottom() {
        return 'low';
    }

    public function admin_replace_text( $translation, $original ) {
        if ( 'Username' == $original ) {
            return 'E-mail';
        }
        if ( 'E-mail' == $original ) {
            return 'Confirm Email';
        }
        return $translation;
    }

}