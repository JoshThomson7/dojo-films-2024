<?php
/**
 * FL1_Blogs
 *
 * Class in charge of services
 */

 // Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class FL1_Blog_Helpers {

    /**
     * WP_Query
     * 
     * @param array $custom_args
     */
    public static function get_blogs($custom_args = array()) {

        $default_args = array(
            'post_type' => 'post',
            'post_status' => 'publish',
            'posts_per_page' => 15,
            'orderby' => 'date',
            'order' => 'DESC',
            'fields' => 'ids'
        );

        $args = wp_parse_args($custom_args, $default_args);

        $posts = new WP_Query($args);
        return array(
            'posts' => $posts->posts,
            'max_num_pages' => $posts->max_num_pages
        );

    }

    /**
     * Returns blog cats
     */
    public static function get_categories($custom_args = array()) {

        $default_args = array(
            'taxonomy' => 'category',
            'parent' => 0,
            'orderby' => 'name',
            'order' => 'ASC',
            'hide_empty' => 1,
			'childless' => true
        );

        $args = wp_parse_args($custom_args, $default_args);
        $terms = get_terms($args);

        return $terms;

    }
    
}
