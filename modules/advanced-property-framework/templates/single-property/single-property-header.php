<?php

/**
 * APF: Single property template
 *
 * @author  FL1
 * @package Advanced Property Framework
 *
 */
if (!defined('ABSPATH')) exit; // Exit if accessed directly

$receptions = $property->get_receptions();
$bathrooms = $property->get_bathrooms();
$bedrooms = $property->get_bedrooms();
$floorplans = $property->get_floorplans();
?>
<header class="apf__single__property__header">
	<div class="apf__single__property__title">
		<h1><?php echo $property->get_name(); ?><span><?php echo $property->get_seo_title(); ?></span></h1>
		<ul>
			<?php if ($receptions) : ?>
				<li><i class="fa-light fa-couch"></i> <?php echo $receptions; ?></li>
			<?php endif; ?>

			<?php if ($bedrooms) : ?>
				<li><i class="fa-light fa-bed-empty"></i> <?php echo $bedrooms; ?></li>
			<?php endif; ?>

			<?php if ($bathrooms) : ?>
				<li><i class="fa-light fa-shower"></i> <?php echo $bathrooms; ?></li>
			<?php endif; ?>
		</ul>
	</div>

	<div class="apf__single__property__price">
		<div class="digits"><?php echo $property->get_price_html(); ?></div>

		<?php if ($property->get_status()) : ?>
			<div class="status">
				<?php echo $property->get_status_html(); ?>
			</div><!-- status -->
		<?php endif; ?>
	</div>
</header>