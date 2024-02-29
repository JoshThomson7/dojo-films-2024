<?php
/**
 * FC Team - Loop
 */
?>
<div class="team__wrap">
    <?php
        foreach($team as $team_id):
			$_team = new FL1C_Team_Member($team_id);
        	$member_id = preg_replace("#[^A-Za-z0-9]#", "", $_team->name());
    ?>
        <article data-image-list="<?php echo $_team->image(400, 400)['url']; ?>">
			<div class="padder">
				<a <?php echo !$no_bios ? 'href="#'.$member_id.'"' : ''; ?>" title="<?php echo $_team->name(); ?>" class="<?php echo !$no_bios ? 'team__modal' : ''; ?>">
					<img src="<?php echo $_team->image(400, 400)['url']; ?>" alt="<?php echo $_team->name(); ?>" />
				</a>
				<h5><?php echo $_team->name(); ?></h5>
				<h6><?php echo $_team->job_title(); ?></h6>
			</div>
        </article>
    <?php endforeach; ?>
</div><!-- team__wrap -->

<div class="team__popup__holder">
	<?php
		$team_count = 1;

		foreach($team as $team_id):
			$_team = new FL1C_Team_Member($team_id);
			$member_id = preg_replace("#[^A-Za-z0-9]#", "", $_team->name());
	?>
		<div id="<?php echo $member_id; ?>" class="team__popup">

			<div class="team__popup__img">
				<img src="<?php echo $_team->image(600, 700)['url']; ?>" alt="<?php echo $_team->name(); ?>" />
			</div><!-- team__popup__img -->

			<div class="team__popup__content">
				<div class="team__popup__nav">
					<ul>
						<li<?php if($team_count == 1): ?> class="inactive"<?php endif; ?>><a href="#" class="team__switch team__prev"><i class="fa-regular fa-chevron-left"></i></a></li>
						<li<?php if($team_count == $team_total): ?> class="inactive"<?php endif; ?>><a href="#" class="team__switch team__next"><i class="fa-regular fa-chevron-right"></i></a></li>
						<li><a href="#" class="team__close"><i class="fa-regular fa-times"></i></a></li>
					</ul>
				</div><!-- team__popup__nav -->

				<h3><?php echo $_team->name(); ?> <span><?php echo $_team->job_title(); ?></span></h3>

				<?php if($_team->email()): ?>
					<div class="team__popup__icon">
						<i class="fa-regular fa-envelope"></i>
						<?php echo FL1_Helpers::hide_email($_team->email()); ?>
					</div>
				<?php endif; ?>

				<?php if($_team->phone()): ?>
					<div class="team__popup__icon">
						<i class="fa-regular fa-phone"></i>
						<a href="tel:<?php echo $_team->phone(); ?>" target="_blank"><?php echo $_team->phone(); ?></a>
					</div>
				<?php endif; ?>

				<?php echo $_team->bio(); ?>
			</div><!-- team__popup__content -->
		</div><!-- team__popup -->
	<?php $team_count++; endforeach; ?>
</div><!-- team__popup__holder -->
