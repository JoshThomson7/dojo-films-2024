<?php
/**
 * Feature
 */
$attachment_id = get_sub_field('feature_image');
$feature_text_width = '';

if($attachment_id) {
    $crop = get_sub_field('feature_image_crop') ?? false;
    $feature_img = vt_resize($attachment_id,'' , 800, 800, $crop);

    $feature_img_align = ' left';
    if(get_sub_field('feature_image_align') == 'right') {
        $feature_img_align = ' right';
    }

    $feature_text_width = get_sub_field('feature_text_width').'%';
}

$dropdown = get_sub_field('feature_dropdown_links');
$top_heading = get_sub_field('feature_top_heading');
$heading = get_sub_field('feature_heading');
$text = get_sub_field('feature_text');
$link_text = get_sub_field('feature_link_text');
$link_url = get_sub_field('feature_link_url');
$link_text_2 = get_sub_field('feature_link_2_text');
$link_url_2 = get_sub_field('feature_link_2_url');
$video = get_sub_field('feature_video');
$video_id = get_sub_field('feature_video_id');
?>

<div class="fc_feature_wrapper<?php echo $feature_img_align.($open['is_full_width'] ? ' fc-feature-expand' : ''); ?>">
    <?php if($attachment_id): ?>
        <div class="feature__image">
			<?php
				if($video_id):

					switch ($video) {
						case 'youtube':
							$url_play = 'https://www.youtube.com/watch?v=' . $video_id;
							break;
						
						default:
							$url_play = 'https://vimeo.com/' . $video_id;
							break;
					}
				
			?>
				<div class="feature__image-video video-pop">
					<a href="<?php echo $url_play; ?>" class="fa-regular fa-play tooltip" title="Play video"></a>
				</div>
			<?php endif; ?>
            <?php if($attachment_id): ?>
				<?php if($open['is_full_width']): ?>
					<div class="feature__image-expand" style="background-image: url(<?php echo $feature_img['url']; ?>);"></div>
				<?php endif; ?>
				<img src="<?php echo $feature_img['url']; ?>" />
            <?php endif; ?>
        </div><!-- feature__image -->
    <?php endif; ?>

    <div class="feature__text" style="width: <?php echo $feature_text_width; ?>">
        <?php if($top_heading): ?><h5><?php echo $top_heading; ?></h5><?php endif; ?>
        <?php if($heading): ?><h3><?php echo $heading; ?></h3><?php endif; ?>
        <?php echo $text ? $text : ''; ?>

		<div class="feature__action">
			<?php if($link_text && $link_url): ?>
				<div class="feature__action-main<?php if(FC_Helpers::video_popup($link_url)) { echo ' video-pop'; } ?>">
					<?php echo FL1_Helpers::button('<span>'.$link_text.'</span>', $link_url, 'button primary'.($dropdown ? ' has-dropdown' : '')); ?>

					<?php if($dropdown): ?>
						<div class="feature__action-dropdown">
							<?php while(have_rows('feature_dropdown_links')): ?>
								<?php the_row(); ?>
								<a href="<?php the_sub_field('url'); ?>" class="dropdown"<?php echo FL1_Helpers::link_target(get_sub_field('url')) ?>>
									<span><?php the_sub_field('label'); ?></span>
								</a>
							<?php endwhile; ?>
						</div>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<?php if($link_text_2 && $link_url_2): ?>
				<span <?php if(FC_Helpers::video_popup($link_url_2)) { echo 'class="video-pop"'; } ?>>
					<a href="<?php echo $link_url_2; ?>" class="link"<?php echo FL1_Helpers::link_target($link_url_2) ?>>
						<span><?php echo $link_text_2; ?></span>
					</a>
				</span>
			<?php endif; ?>
		</div><!-- feature__action -->
    </div><!-- feature__text -->
</div><!-- fc_feature_wrapper -->
