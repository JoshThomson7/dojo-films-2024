<?php
/**
 * APF: Advanced Property Framework
 *
 * Class in charge of APF_Helpers
 */

 // Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class APF_Helpers {

	/**
	 * Check if the current page is an APF page
	 * 
	 * @return boolean
	 */
	public static function is_apf() {

		global $post;
	
		$property_search_obj = get_page_by_path('property-search', OBJECT, 'page');
		$property_search_id = $property_search_obj->ID;
	
		if($property_search_id == $post->ID || is_singular('property')) {
			return true;
		} else {
			return false;
		}

	}

	/**
	 * Check if the current page is the property search page
	 * 
	 * @return boolean
	 */
	public static function is_property_search() {

		return is_page('property-search');

	}

	/**
	 * Check if the current page is an APF endpoint
	 * 
	 * @return boolean
	 */
	public static function is_apf_endpoint() {

		if( get_query_var('gallery') || get_query_var('map') || get_query_var('video') || get_query_var('floorplan') ) {
			return true;
		}

		return false;
	
	}

	/**
	 * Returns property search page URL
	 */
	public static function property_search_url() {

		return get_permalink(get_page_by_path('property-search'));

	}
	

	/**
	 * Outputs the APF search form
	 * 
	 * @param boolean $is_banner
	 */
	public static function search_form($is_banner = null) {

		if($is_banner === true) { 
			echo '<div class="avb__apf__search">';
			echo '<div class="max__width">';
		}
	
		require_once APF_PATH . '/templates/search-form.php';
	
		if($is_banner === true) { 
			echo '</div>';
			echo '</div>';
		}
	
	}

	/**
	 * Exclude statuses
	 */
	public static function exclude_statuses($status) {

		if(isset($status) && $status === 'exclude') {
			$property_status = array();
	
		} else {
			$property_status = array('Let', 'Let Agreed', 'Sold', 'Sold STC', 'Under offer');
		}
	
		return $property_status;
	}

	/**
	 * Returns APF pagination
	 * 
	 * @param integer $pages
	 * @param integer $range
	 * @param integer $apf_page
	 */
	public static function pagination($pages = '', $range = 4, $apf_page = 1) {

		$showitems = ($range * 2)+1;
	
		$paged = $apf_page;
	
		if(empty($paged)) $paged = 1;
	
		if($pages == '') {
			global $wp_query;
			$pages = $wp_query->max_num_pages;
	
			if(!$pages) {
				$pages = 1;
			}
		}
	
		if(1 != $pages) {
	
			echo "<div class=\"apf__pagination\"><div class=\"apf__page__count\">Page ".$paged." of ".$pages."</div><div class=\"apf__page__numbers\">";
			if($paged > 2 && $paged > $range+1 && $showitems < $pages) echo "<a href=\"#\" class=\"apf-paginate apf__pagination\" data-apf-page=\"1\">&laquo;</a>";
			if($paged > 1 && $showitems < $pages) echo "<a href=\"#\" class=\"apf-paginate apf__paginate\" data-apf-page=\"".($paged - 1)."\">&lsaquo;</a>";
	
			for ($i=1; $i <= $pages; $i++) {
	
				if (1 != $pages &&( !($i >= $paged+$range+1 || $i <= $paged-$range-1) || $pages <= $showitems )) {
					echo ($paged == $i)? "<span class=\"apf__current__page\">".$i."</span>":"<a href=\"#\" class=\"inactive apf-paginate apf__paginate\" data-apf-page=\"".$i."\">".$i."</a>";
				}
	
			}
	
			if ($paged < $pages && $showitems < $pages) echo "<a href=\"#\" class=\"apf-paginate apf__paginate\" data-apf-page=\"".($paged + 1)."\">&rsaquo;</a>";
			if ($paged < $pages-1 &&  $paged+$range-1 < $pages && $showitems < $pages) echo "<a href=\"#\" class=\"apf-paginate apf__paginate\" data-apf-page=\"".$pages."\">&raquo;</a>";
			echo "</div></div>\n";
		}
	}

}