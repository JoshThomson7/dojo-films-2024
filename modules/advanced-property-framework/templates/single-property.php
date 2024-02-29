<?php
/**
 * APF: Single property template
 *
 * @author  FL1
 * @package Advanced Property Framework
 *
*/
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

get_header();

global $post;
$property = new APF_Property($post->ID);

$main_image = $property->get_main_image();
$features = $property->get_features();
$summary = $property->get_summary();
$about = $property->get_about();
$brochure = $property->get_brochure();
$gallery = $property->get_gallery();
$floorplans = $property->get_floorplans();
$epc = $property->get_epc();
?>
<section class="apf apf-single-property">

	<div class="max__width">
		<?php require_once 'single-property/single-property-gallery.php'; ?>

		<div class="apf__single__property__content__wrap">

			<div class="apf__single__property__content">
				<?php require_once 'single-property/single-property-nav.php'; ?>
				<?php require_once 'single-property/single-property-header.php'; ?>

				<a href="#" class="apf__book__viewing__button apf-do-book-viewing-form apf__mobile" title="Book a viewing">Book a viewing</a>

				<?php if(!empty($features)): ?>
					<article id="about" class="apf__single__property__features">
						<h2>Main Features</h2>
						<ul>
							<?php foreach($features as $feature): ?>
								<li><?php echo $feature['property_feature']; ?></li>
							<?php endforeach; ?>
						</li>
					</article><!-- apf__single__property__features -->
				<?php endif; ?>

				<?php if($summary || $about): ?>
					<article id="about">
						<h2>About this property</h2>
						<?php echo $about ? $about : $summary; ?>
					</article>
				<?php endif; ?>

				<?php require_once 'single-property/single-property-map.php'; ?>

				<?php if($brochure): ?>
					<article id="apf_brochure" class="apf__brochure">
						<h2>Brochure</h2>

						<div class="apf__brochure__pdf">
							<div class="apf__brochure__pdf__img">
								<?php if(isset($main_image['url'])): ?>
									<img src="<?php echo $main_image['url']; ?>" alt="Property Brochure" />
								<?php endif; ?>
								<h4><?php echo $property->get_name(); ?></h4>
								<h5><?php echo $property->get_price_html(); ?></h5>
							</div><!-- apf__brochure__pdf__img -->

							<div class="apf__brochure__action">
								<p>Full PDF brochure containing all the details of the property in <span><?php echo $property->get_name(); ?></span></p>
								<a href="<?php echo $brochure;; ?>" target="_blank" class="apf__article__button">View brochure</a>
							</div><!-- apf__brochure__action -->
						</div><!-- apf__brochure__pdf -->
					</article>
				<?php endif; ?>

				<?php if($floorplans): ?>
					<article id="floorplan" class="floorplan">
						<h2>Floorplan</h2>

						<div class="floorplan-items apf-property--floorplans">
							<?php foreach($floorplans as $floorplan): ?>
								<figure data-src="<?php echo $floorplan['image_url']; ?>">
									<img src="<?php echo $floorplan['image_url']; ?>" />
								</figure>
							<?php endforeach; ?>
						</div>
					</article>
				<?php endif; ?>

				<?php if($epc): ?>
					<article id="epc" class="epc">
						<h2>EPC</h2>

						<div class="epc__graphics">
							<img src="<?php echo $epc; ?>" />
						</div><!-- epc__graphics -->
					</article>
				<?php endif; ?>
			</div>

			<?php require_once 'single-property/single-property-sidebar.php'; ?>
		</div>
	</div>
</section>

<?php require_once 'book-viewing/book-viewing-form.php'; ?>

<?php get_footer(); ?>
