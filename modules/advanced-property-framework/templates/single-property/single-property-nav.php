<?php
/**
 * APF Single Property - Nav
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>
<div class="apf-single-property--nav">
	<a href="<?php echo APF_Helpers::property_search_url(); ?>" title="Back to search results" class="button small icon-left animate-icon">
		<i class="far fa-chevron-left"></i> <strong>Back to search results</strong>
	</a>

	<ul class="apf-nav--anchors">
		<li><a href="#about" class="scroll"><i class="fal fa-house"></i>About</a></li>
		<?php if($property->has_map()): ?><li><a href="#map_single" class="scroll"><i class="fal fa-map"></i>Map</a></li><?php endif; ?>
		<?php if($floorplans): ?><li><a href="#floorplan" class="scroll"><i class="fi flaticon-blueprint"></i>Floorplan</a></li><?php endif; ?>
		<?php if($epc): ?><li><a href="#epc" class="scroll"><i class="fal fa-plug"></i>EPC</a></li><?php endif; ?>
	</ul>
</div>