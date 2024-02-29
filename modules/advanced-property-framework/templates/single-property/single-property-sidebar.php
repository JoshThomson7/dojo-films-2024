<?php
/**
 * APF: Single property sidebar
 *
 * @author  FL1
 * @package Advanced Property Framework
 *
*/
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>

<div class="apf__single__property__sidebar">

    <?php
        // Get branch
        $property_branch_id = $property->get_branch_id();
        $branch_query = new WP_Query(array(
            'post_type'         => 'branch',
            'post_status'       => 'publish',
            'posts_per_page'    => 1,
            'meta_query' => array(
                array(
                    'key'       => 'branch_id',
                    'value'     => $property_branch_id,
                    'compare'   => 'LIKE'
                )
			),
			'fields' => 'ids'
        ));
		$branch = $branch_query->posts;
		$branch_id = count($branch) > 0 ? $branch[0] : null;
        if(get_post($branch_id)):
			$branch = new APF_Branch($branch_id);
            $branch_image = $branch->image(600, 400);
            $branch_address = $branch->get_address();
			$branch_phone = $branch->get_phone();
			$branch_email = $branch->get_email();
			$banch_opening_times = $branch->opening_times();
			$todays_times = $branch->todays_times();
    ?>

		<article class="viewing">
			<h3>Book a viewing</h3>
			<p>Call <strong><?php echo $branch_phone; ?></strong> or book online</p>
			<?php if($property->is_student()): ?>
				<a href="#" class="button primary large" title="Book now">Book now</a>
			<?php endif; ?>
			<a href="#" class="apf-do-book-viewing-form button <?php echo $property->is_student() ? 'white' : 'primary'; ?> large" title="Arrange a viewing">Arrange a viewing</a>
		</article>

		<article class="branch">
			<?php if(isset($branch_image['url'])): ?>
				<div class="branch__img">
					<a href="<?php echo $branch->get_permalink(); ?>" title="<?php echo $branch->get_name(); ?>">
						<img src="<?php echo $branch_image['url']; ?>" alt="<?php echo $branch->get_name(); ?>">
					</a>
				</div><!-- branch__img -->
			<?php endif; ?>

			<div class="branch__details">
				<div class="branch__dept">
					<h3><?php echo $branch->get_name(); ?> branch</h3>

					<?php if(isset($branch_address['url'])): ?>
						<p class="address"><i class="fal fa-map-marker"></i><?php echo $branch_address['address']; ?></p>
					<?php endif; ?>

					<?php if($branch_phone): ?>
						<p><i class="fal fa-phone"></i><?php echo $branch_phone; ?></p>
					<?php endif; ?>

					<?php if($branch_email): ?>
						<p><i class="fal fa-envelope"></i><?php echo $branch_email; ?></p>
					<?php endif; ?>
				</div><!-- branch-dept -->
				
				<?php if(!empty($banch_opening_times)): ?>
					<div class="branch__hours">
						<h4>Opening hours <small class="<?php echo $todays_times->status->class; ?>"><?php echo $todays_times->status->text; ?></small></h4>

						<ul class="hours-table">        
							<?php
								foreach($branch->opening_times() as $opening_time):
									$is_today = $opening_time->weekday->is_today;
									$today_classes = array($is_today, $opening_time->status->class);
							?>
								<li class="<?php echo join(' ', $today_classes); ?>"><?php echo $opening_time->display; ?></li>
							<?php endforeach; ?>
						</ul><!-- hours-table -->
					</div><!-- branch__hours -->
				<?php endif; ?>
			</div><!-- branch__details -->
		</article>
	<?php endif; ?>

</div><!-- apf__single__property__sidebar -->
