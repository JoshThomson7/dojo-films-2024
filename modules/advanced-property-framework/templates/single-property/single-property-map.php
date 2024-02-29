<?php
/**
 * APF Single Property - Map
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$address = $property->get_address();
$latitude = $property->get_latitude();
$longitude = $property->get_longitude();

if($address || ($latitude && $longitude)):
	if(!empty($address)) {
		$latitude = $address['lat'];
		$longitude = $address['lng'];
	}

	if($latitude && $longitude):
?>
		<article id="map_section">
			<h2>Map</h2>

			<div id="map_single" data-src="<?php echo esc_url(APF_URL.'img/apf-blank.png'); ?>" data-lat="<?php echo $latitude; ?>" data-lng="<?php echo $longitude; ?>"></div>
		</article>
	<?php endif; ?>
<?php endif; ?>