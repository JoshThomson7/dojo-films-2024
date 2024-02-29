<?php
/**
 * APF_Public
 *
 * Class in charge of APF Public facing side
 */

 // Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class APF_Public {

    public function __construct() {

		add_filter('body_class', array($this, 'body_classes'));
        add_filter('page_template', array($this, 'pages'));
		add_filter('single_template', array($this, 'singles'));
		add_action('wp_head', array($this, 'wp_head'));

    }

	public function body_classes( $classes ) {

		if(APF_Helpers::is_apf()) {
			$classes[] = 'apf__body';
		}
	
		if(APF_Helpers::is_property_search()) {
			$classes[] = 'apf__property__search';
		}
	
		if(is_singular('property')) {
			$classes[] = 'apf__single';
		}
	
		return $classes;
	
	}

	/**
	 * Define custom page templates
	 * 
	 * @param string $page_template
	 */
	public function pages($page_template) {
	
		if(APF_Helpers::is_property_search()) {
			$page_template = APF_PATH . 'templates/search-results.php';
	
		}
		
		if(is_page('property-search/xml')) {
			$page_template = APF_PATH . 'templates/map-xml.php';
			
		}
		
		if(is_page('property-search/import')) {
			$page_template = APF_PATH . 'apps/apf-import/index.php';
	
		}
		
		if(is_page('thank-you-for-arranging-a-viewing')) {
			$page_template = APF_PATH . 'templates/book-viewing-thanks.php';
	
		}
		
		if(is_page('update-properties')) {
			$page_template = APF_PATH . 'apps/apf-update/templates/update-properties.php';
	
		}

		if(is_page('find-a-branch')) {
			$page_template = apf_path() . 'templates/branches/branches.php';
		}
		
		if(is_page('find-a-branch/xml')) {
			$page_template = apf_path() . 'templates/branches/branches-xml.php';
		}
	
		return $page_template;
	
	}

	/**
	 * Define custom single templates
	 * 
	 * @param string $single_template
	 */
	public function singles($single_template) {

		global $post;
	
		if ($post->post_type === 'property') {
			$single_template = APF_PATH . 'templates/single-property.php';
		}

		if ($post->post_type === 'branch') {
			$single_template = APF_PATH . 'templates/branches/single-branch.php';
		}
	
		return $single_template;
	}

	public function wp_head() {

		global $post;
		$post_id = $post->ID;

		if(!is_singular('property')) {
			return;
		}

		$property = new APF_Property($post_id);
		$name = $property->get_name();
		$about = strip_tags($property->get_about());
		$bedrooms = $property->is_studio() ? 0 : ($property->get_bedrooms() ? $property->get_bedrooms() : 0);
		$receptions = $property->get_receptions() ? $property->get_receptions() : 0;
		$rooms = (int)$bedrooms + (int)$receptions;

		?>
			<script type="application/ld+json">
				{
					"@context": "https://schema.org",
					"@type": "SingleFamilyResidence",
					"name": "<?php echo $name; ?>",
					"description": "<?php echo $about; ?>",
					"numberOfRooms": <?php echo $rooms; ?>,
					"numberOfBathroomsTotal": <?php echo $property->get_bathrooms(); ?>,
					"numberOfBedrooms": <?php echo $bedrooms; ?>,
					"address": "<?php echo $property->get_address_searchable(); ?>",
				}
			</script>
		<?

	}

}

