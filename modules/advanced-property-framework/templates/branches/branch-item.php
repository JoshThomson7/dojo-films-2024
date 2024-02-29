<?php
/**
 * APF Branch item
 */
$branch = new APF_Branch($branch_id);
$branch_img = $branch->image();
$address = $branch->get_address('address');
$phone = $branch->get_phone();
$email = $branch->get_email();
?>
<div class="branch single__branch">
	<?php if(is_array($branch_img)): ?>
		<div class="branch-thumb">
			<img src="<?php echo $branch_img['url'] ?>" alt="<?php echo $branch->get_name(); ?>" />
		</div><!-- branch-thumb -->
	<?php endif; ?>

	<div class="branch-details">
		<div class="branch-address">
			<?php if($address): ?>
				<h3>Address</h3>

				<p class="address">
					<i class="fal fa-map-marker"></i>
					<?php echo str_replace(',', '<br />', $address); ?>
				</p>
			<?php endif; ?>

			<?php if($phone): ?>
				<h3>Phone</h3>
				<p>
					<i class="fal fa-phone"></i>
					<?php echo $phone; ?>
				</p>
			<?php endif; ?>

			<?php if($email): ?>
				<h3>Email</h3>
				<p>
					<i class="fal fa-envelope"></i>
					<?php echo $email; ?>
				</p>
			<?php endif; ?>
		</div><!-- branch-addres -->

		<?php
			if($branch->get_opening_times()):
			
			$todays_times = $branch->todays_times();
		?>
			<div class="branch-hours">
				<h3>Opening hours <small class="<?php echo $todays_times->status->class; ?>"><?php echo $todays_times->status->text; ?></small></h3>
				<ul class="hours-table">
					<?php
						foreach($branch->opening_times() as $opening_time):
							$is_today = $opening_time->weekday->is_today;
							$today_classes = array($is_today, $opening_time->status->class);
					?>
						<li class="<?php echo join(' ', $today_classes); ?>"><?php echo $opening_time->display; ?></li>
					<?php endforeach; ?>
				</ul><!-- hours-table -->
			</div><!-- branch-hours -->
		<?php endif; ?>
	</div><!-- branch-details -->
</div><!-- branch -->