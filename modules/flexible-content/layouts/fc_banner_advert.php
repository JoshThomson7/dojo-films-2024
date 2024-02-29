<?php
/*
    Call to action
*/

// image
$cta_bk = get_sub_field('ad_bk');
$bk_img = '';
if($cta_bk) {
    $attachment_id = get_sub_field('ad_bk');
    $bk_img = vt_resize($attachment_id, '', 2000, 600, true);
    $bk_img = ' style="background-image:url('.$bk_img['url'].');"';
}

$scroll = '';
if (substr(get_sub_field('ad_button_link'), 0, 1) === '#') {
    $scroll = 'scroll ';
}

$parallax = '';
if(get_sub_field('ad_parallax')) {
    $parallax = ' cta__parallax';
}

$styles = get_sub_field('fc_styles');
$full_width = $styles['fc_full_width'] == true ? true : false;

$icon_heading = get_sub_field('ad_icon_heading');
$icon_heading_icon = $icon_heading['icon'];
$icon_heading_heading = $icon_heading['heading'];

$small_heading = get_sub_field('ad_small_heading');

$bullets = get_sub_field('ad_bullets');

$cta_button_link = get_sub_field('ad_button_link');
$cta_button_label = get_sub_field('ad_button_label');

$cta_button_link_2 = get_sub_field('ad_button_link_2');
$cta_button_label_2 = get_sub_field('ad_button_label_2');

$badge = get_sub_field('badge');
$badge_top = $badge['top'];
$badge_mid = $badge['mid'];
$badge_bottom = $badge['bottom'];

$info = get_sub_field('info');
?>

    <div class="ad__wrapper<?php echo $full_width ? ' fw' : ''; ?>">
        <?php if($full_width): ?><div class="max__width"><?php endif; ?>
        <article>
			<?php if($icon_heading_heading): ?>
				<h5 class="icon__heading">
					<?php if($icon_heading_icon): ?>
						<i class="<?php echo $icon_heading_icon; ?>"></i>
					<?php endif; ?>
					<?php echo $icon_heading_heading; ?>
				</h5>
			<?php endif; ?>
			
			<?php if($small_heading): ?>
				<h5><?php echo $small_heading; ?></h5>
			<?php endif; ?>

            <h2><?php the_sub_field('ad_heading'); echo $padding; ?></h2>

			<?php if(!empty($bullets)): ?>
				<ul>
					<?php foreach($bullets as $bullet): ?>
						<li><i class="fa-light fa-check"></i><?php echo $bullet['bullet']; ?></li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>

			<div class="ad__buttons">
				<?php
					if($cta_button_link && $cta_button_label):
						echo FL1_Helpers::button($cta_button_label, $cta_button_link, 'button primary large');
					endif;
				?>

				<?php
					if($cta_button_link_2 && $cta_button_label_2):
						echo FL1_Helpers::button($cta_button_label_2, $cta_button_link_2, 'button white large');
					endif;
				?>
			</div>
        </article>

		<aside>
			<?php if($badge_top || $badge_mid || $badge_bottom): ?>
				<figure class="badge">
					<?php if($badge_top): ?><span class="top"><?php echo $badge_top; ?></span><?php endif; ?>
					<?php if($badge_mid): ?><span class="mid"><?php echo $badge_mid; ?></span><?php endif; ?>
					<?php if($badge_bottom): ?><span class="bottom"><?php echo $badge_bottom; ?></span><?php endif; ?>
				</figure>
			<?php endif; ?>

			<?php if(!empty($info)): ?>
				<ul>
					<?php foreach($info as $info_item): ?>
						<li><?php echo $info_item['key'] ? $info_item['key'].': ' : ''; ?><?php echo $info_item['value'] ? $info_item['value'] : ''; ?></li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</aside>

        <div class="ad__overlay" style="background: rgba(19, 118, 158, <?php the_sub_field('ad_overlay_opacity'); ?>);"></div>
        <div class="ad__image<?php echo $parallax; ?>"<?php echo $bk_img; ?>></div>
        <?php if($full_width): ?></div><?php endif; ?>
    </div><!-- cta__wrapper -->
