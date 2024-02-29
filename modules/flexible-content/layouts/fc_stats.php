<?php
/**
 * FC Stats
 */
$heading = get_sub_field('heading');
$stats = get_sub_field('stats');

if(empty($stats)) return;
?>
<div class="fc-stats">
	<?php if($heading): ?><h3><?php echo $heading; ?></h3><?php endif; ?>
	
	<div class="stats">
		<?php
			foreach($stats as $stat):
				$icon = $stat['icon'];
				$caption = $stat['caption'];
				$stat = $stat['stat'];

				$has_stat = !empty($stat['figure']);
				$before = !empty($stat['before']) ? $stat['before'] : '';
				$figure = !empty($stat['figure']) ? $stat['figure'] : 0;
				$after = !empty($stat['after']) ? $stat['after'] : '';

		?>
			<article>
				<?php if($icon): ?><i class="<?php echo $icon; ?>"></i><?php endif; ?>
				<?php if($has_stat): ?>
					<h5>
						<?php if($before): ?><span class="before"><?php echo $before; ?></span><?php endif; ?>
						<span class="figure"><?php echo $figure; ?></span>
						<?php if($after): ?><span class="after"><?php echo $after; ?></span><?php endif; ?>
					</h5>
				<?php endif; ?>
				<?php if($caption): ?><p><?php echo $caption; ?></p><?php endif; ?>
			</article>
		<?php endforeach; ?>
	</div>
</div>
