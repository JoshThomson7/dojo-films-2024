<?php
/*
FC Buttons
*/

$align = 'align-'.get_sub_field('buttons_align');
?>
<div class="buttons__wrap <?php echo $align; ?>">
    <?php
        while(have_rows('buttons')) {
			the_row();

			$button_label = get_sub_field('button_label');
			$button_url = get_sub_field('button_url');

			if(!$button_label || !$button_url) continue;

			echo FL1_Helpers::button($button_label, $button_url, 'button primary large');
		}
	?>
</div><!-- buttons__wrap -->
