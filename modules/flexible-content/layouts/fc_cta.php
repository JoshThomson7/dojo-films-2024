<?php
/*
    Call to action
*/

// image
$cta_bk = get_sub_field('cta_bk');
$bk_img = '';
if($cta_bk) {
    $attachment_id = get_sub_field('cta_bk');
    $bk_img = vt_resize($attachment_id, '', 2000, 600, true);
    $bk_img = ' style="background-image:url('.$bk_img['url'].');"';
}

$scroll = '';
if (substr(get_sub_field('cta_button_link'), 0, 1) === '#') {
    $scroll = 'scroll ';
}

$parallax = '';
if(get_sub_field('cta_parallax')) {
    $parallax = ' cta__parallax';
}

// styles
$styles = get_sub_field('fc_styles');
$full_width = $styles['fc_full_width'] == true ? true : false;

$cta_button_link = get_sub_field('cta_button_link');
$cta_button_label = get_sub_field('cta_button_label');

$cta_button_link_2 = get_sub_field('cta_button_link_2');
$cta_button_label_2 = get_sub_field('cta_button_label_2');
?>

    <div class="cta__wrapper<?php echo $full_width ? ' fw' : ''; ?>">
        <?php if($full_width): ?><div class="max__width"><?php endif; ?>
        <article>
            <h2><?php the_sub_field('cta_heading'); echo $padding; ?></h2>
            <p><?php the_sub_field('cta_caption'); ?></p>
        </article>
		
		<div class="cta__buttons">
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

        <div class="cta__overlay" style="background: rgba(19, 118, 158, <?php the_sub_field('cta_overlay_opacity'); ?>);"></div>
        <div class="cta__image<?php echo $parallax; ?>"<?php echo $bk_img; ?>></div>
        <?php if($full_width): ?></div><?php endif; ?>
    </div><!-- cta__wrapper -->
