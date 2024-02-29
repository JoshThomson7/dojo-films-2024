<?php
/*
    Branches template
*/

global $post;
get_header();
AVB::avb_banners();
?>

<div class="flexible__content">
	<section class="fc-layout fc_branch">
		<div class="fc-layout-container fc-background fc-bk-secondary fc-background-shrink" style="padding: 40px 0 40px 0">
			<div class="max__width">
				<?php
					$branch_id = $post->ID;
					include APF_PATH.'templates/branches/branch-item.php';
				?>
			</div>
        </div>
    </section>
</div>

<?php FC_Helpers::flexible_content(); ?>

<?php get_footer(); ?>
