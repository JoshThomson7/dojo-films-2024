<?php
/**
 * APF: Loop item
 *
 * @author  FL1
 * @package Advanced Property Framework
 *
*/
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$property = new APF_Property($post_id);
$property_img = $property->get_main_image();
$receptions = $property->get_receptions();
$bedrooms = $property->get_bedrooms();
$bathrooms = $property->get_bathrooms();
?>
<article>
    
    <div class="apf__property__border">

		<?php if(isset($property_img)): ?>
			<a href="<?php echo $property->get_permalink(); ?>" class="apf__property__img" title="<?php echo $property->get_name(); ?>" style="background-image:url('<?php echo $property_img; ?>');">
				<?php echo $property->get_status_html(); ?>
			</a>
		<?php endif; ?>

        <div class="apf__property__details__wrap">

            <div class="apf__property__details">
                <h3><?php echo $property->get_price_html(); ?></h3>
                <h5><?php echo $property->get_seo_title(); ?></h5>
                <p><i class="fal fa-map-marker-alt"></i><?php echo $property->get_name(); ?></p>
            </div><!-- apf__property__details -->

            <div class="apf__property__meta">
                <div class="apf__property__meta__data">
                    <?php if($receptions): ?>
                        <span><i class="fa-light fa-couch"></i><?php echo $receptions; ?></span>
                    <?php endif; ?>

                    <?php if($bedrooms): ?>
                        <span><i class="fa-light fa-bed-empty"></i><?php echo $bedrooms; ?></span>
                    <?php endif; ?>

                    <?php if($bathrooms): ?>
                        <span><i class="fa-light fa-shower"></i><?php echo $bathrooms; ?></span>
                    <?php endif; ?>
                </div><!-- apf__property__meta__data -->

                <a href="<?php echo $property->get_permalink(); ?>" title="Full details" class="apf__property__meta__action button small icon-right">Details<i class="fa-light fa-chevron-right"></i></a>
            </div><!-- apf__property__meta -->

        </div><!-- apf__property__details__wrap -->

    </div><!-- apf__property__border -->
</article>