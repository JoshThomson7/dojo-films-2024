<?php
/*
    Grid Boxes
*/
$grid_boxes_classes = array();

$grid_boxes_num = get_sub_field('grid_boxes_num');
$grid_boxes_overlay = get_sub_field('grid_boxes_overlay');
if ($grid_boxes_overlay) {
	$grid_boxes_classes[] = 'overlay';
}

$grid_boxes_carousel = get_sub_field('grid_boxes_carousel');
if ($grid_boxes_carousel) {
	$grid_boxes_classes[] = 'grid-boxes-carousel';
}

$grid_boxes = get_sub_field('grid_boxes');

if (!empty($grid_boxes) && is_array($grid_boxes)) :
?>
	<div class="grid__boxes__wrapper <?php echo join(' ', $grid_boxes_classes); ?>" <?php echo $grid_boxes_spacing_fix; ?>>
		<?php
		foreach ($grid_boxes as $grid_box) :

			// Vars
			$image_id = $grid_box['image'];
			$image_no_crop = $grid_box['no_crop'];
			$top_label = $grid_box['top_label'];
			$heading = $grid_box['heading'];
			$content_type = $grid_box['content_type'];
			$caption = $grid_box['caption'];
			$links = $grid_box['links'];
			$button_label = $grid_box['button_label'];
			$button_url = $grid_box['button_url'];
			$text_align = $grid_box['text_align'];

			// Defaults
			$link_open = '';
			$link_close = '';
			$overlay_link_open = '';
			$overlay_link_close = '';
			$image = '';
			$content_placement = '';
			$image = array();

			// Link and overlay
			if ($button_url) {
				$target = ' target="_blank"';

				if (strpos($button_url, $_SERVER['HTTP_HOST']) !== false) {
					$target = '';
				}

				if ($grid_boxes_overlay) {
					$overlay_link_open = '<a href="' . $button_url . '" class="grid__overlay__a"' . $target . '>';
					$overlay_link_close = '</a>';
				} else {
					$link_open = '<a href="' . $button_url . '"' . $target . '>';
					$link_close = '</a>';
				}
			}
		?>
			<article class="<?php echo $grid_boxes_num; ?>" <?php echo $grid_boxes_spacing; ?>>
				<div class="padder">

					<?php
					if ($image_id) :
						$image = vt_resize($image_id, '', 700, 500, !$image_no_crop);
					?>
						<figure style="background-image: url(<?php echo $image['url']; ?>)"><?php echo $link_open . $link_close; ?></figure>
					<?php endif; ?>

					<div class="grid-box-content <?php echo $text_align; ?>">
						<?php echo $overlay_link_open; ?>
						<?php echo $link_open; ?>
						<?php if ($top_label) : ?><h5><?php echo $top_label; ?></h5><?php endif; ?>
						<?php if ($heading) : ?><h3><?php echo $heading; ?></h3><?php endif; ?>
						<?php echo $link_close; ?>

						<?php
							if($content_type === 'free-text' && $caption) { echo $caption; }
							if($content_type === 'links' && !empty($links)) {
								echo '<ul class="links">';
								foreach($links as $link) {
									echo '<li><a href="' . $link['link'] . '">' . $link['label'] . ' <i class="fa-light fa-chevron-right"></i></a></li>';
								}
								echo '</ul>';
							}
						?>
						<?php echo $overlay_link_close; ?>

						<?php if ($button_label && !$grid_boxes_overlay) : ?>
							<a href="<?php echo $button_url; ?>" class="link animate-icon"><?php echo $button_label; ?> <i class="fa fa-chevron-right"></i></a>
						<?php endif; ?>
					</div><!-- grid__box__content -->

				</div><!-- padder -->
			</article>
		<?php endforeach; ?>
	</div><!-- grid__boxes__wrapper -->
<?php endif; ?>