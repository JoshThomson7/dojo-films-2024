<?php
/**
 * APF Single Property - Gallery
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if(!empty($gallery)):
?>
	<div class="apf-property--gallery">
		<?php if(!empty($main_image)): ?>
			<div class="apf-property--gallery-main">
				<figure class="apf-property--gallery-image" data-src="<?php echo $main_image; ?>" style="background-image: url(<?php echo $main_image ?>);">
					<img src="<?php echo $main_image ?>">
				</figure>
			</div>
		<?php endif; ?>
		
		<div class="apf-property--gallery-rest">
			<?php
				$gal_grid = 2;
				$gal_ids_count = !empty($gallery) ? count($gallery) : 0;
				$missing = $gal_grid - $gal_ids_count;

				$gallery_id_count = 1;
				foreach($gallery as $gallery_img):
			?>
				<figure class="apf-property--gallery-image <?php if($gallery_id_count >= 3) { echo 'hide'; }; ?>" data-src="<?php echo $gallery_img['image_url']; ?>" style="background-image: url(<?php echo $gallery_img['image_url']; ?>);">
					<?php if(count($gallery) >= 1 && $gallery_id_count == 2): ?>
						<div class="apf-property--gallery-more">
							<i class="fa-light fa-camera"></i> <?php echo count($gallery); ?> photos
						</div>
					<?php endif; ?>
					<img src="<?php echo $gallery_img['image_url']; ?>" <?php if($gallery_id_count >= 9) { echo 'class="no-lazy"'; }; ?>>
				</figure>
			<?php $gallery_id_count++; endforeach; ?>

			<?php if($missing > 0): ?>
				<?php for($i = 0; $i < $missing; $i++): ?>
					<figure class="apf-property--gallery-image no-img"></figure>
				<?php endfor; ?>
			<?php endif; ?>
		</div>
	</div><!-- property--gallery -->
<?php endif; ?>