<?php
/**
 * APF Single Property - Floorplan
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>
<article>
    <h2>Floorplan</h2>

    <?php if($floorplans): ?>
        <ul class="property__gallery__all">
            <?php foreach($floorplans as $floorplan); ?>
                <li data-src="<?php echo $floorplan['property_floorplan_image_url']; ?>">
                    <a href="#"><img src="<?php echo $floorplan['property_floorplan_image_url']; ?>" /></a>
                </li>
            <?php endwhile; ?>
        </ul>
    <?php endif; ?>
</article>