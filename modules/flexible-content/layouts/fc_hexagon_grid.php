<?php
/**
 * Hexagon Grid
 */

$hexagons = get_sub_field('hexagons');

if(empty($hexagons)) return; /*
?>
<div class="fc-hexagons">
	<div class="fc-hexagons--main">
		<div class="fc-hexagons--container">
			<figure class="hide"></figure>
			<?php
				$hex_count = 0;
				foreach($hexagons as $hexagon):
					$hide = array(2, 5);
					$figure_hide = in_array($hex_count, $hide);
					$hexagon_img = vt_resize('', $hexagon['image'], 300, 300, true);
			?>
				<?php if($figure_hide): ?><figure class="hide"></figure><?php endif; ?>
				<figure>
					<?php
						if($hexagon['image']):
							$hexagon_img = vt_resize($hexagon['image'], '', 440, 440, true);
					?>
						<img src="<?php echo $hexagon_img['url']; ?>" />
					<?php endif; ?>
				</figure>
			<?php $hex_count++; endforeach; ?>
			<figure class="hide"></figure>
		</div>
	</div>
</div> */ ?>

<div id="hexGrid">
	<div class="hexCrop">
		<div class="hexGrid">
			<div class="hex hide"></div>
			<?php
				$hex_count = 0;
				foreach($hexagons as $hexagon):
					$figure_hide = $hex_count == 0;
					$hexagon_img = vt_resize('', $hexagon['image'], 300, 300, true);
					$hexagon_link = $hexagon['link'];
			?>
				<div class="hex">
					<?php if($hexagon_link): ?><a href="<?php echo $hexagon_link; ?>"><?php endif; ?>
					<?php
						if($hexagon['image']):
							$hexagon_img = vt_resize($hexagon['image'], '', 440, 440, true);
					?>
						<img src="<?php echo $hexagon_img['url']; ?>" />
					<?php endif; ?>
					<?php if($hexagon_link): ?></a><?php endif; ?>
				</div>
				<?php if($figure_hide): ?><div class="hex hide"></div><?php endif; ?>
			<?php $hex_count++; endforeach; ?>
		</div>
	</div>
</div>