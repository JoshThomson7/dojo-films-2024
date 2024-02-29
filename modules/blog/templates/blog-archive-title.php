<?php
/**
 * Blog Archive Title
 */

$term = get_queried_object();

if($term->taxonomy === 'category') {
	$top_heading = 'Category';
}

if($term->taxonomy === 'post_tag') {
	$top_heading = 'Tag';
}
?>

<section class="avb">
	<div class="avb-banners avb-inner">
		<div class="avb-banner">
			<div class="max__width">
				<div class="avb-banner__caption">
					<div class="avb-banner__caption-wrap" style="gap: 0;">
						<h3>Blog <?php echo $top_heading; ?></h3>
						<h1><?php echo $term->name; ?></h1>
					</div>
				</div>
			</div>
		</div>
	</div><!-- avb-banners -->
</section>