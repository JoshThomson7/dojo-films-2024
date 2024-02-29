<?php

/**
 * Blog
 */

global $paged;
get_header();

$term = get_queried_object();

$title = 'Blog';

if ($term->taxonomy === 'category') {
	$top_heading = 'Category';
}

if ($term->taxonomy === 'post_tag') {
	$top_heading = 'Tag';
}

$blog_args['paged'] = $paged;
$blog_args['posts_per_page'] = 15;
$blog_args['tax_query'] = array(
	array(
		'taxonomy' => $term->taxonomy,
		'field' => 'id',
		'terms' => $term->term_id
	)
);
$blogs = FL1_Blog_Helpers::get_blogs($blog_args);
?>
<section class="avb">
	<?php FL1_Helpers::breadcrumbs(); ?>
	<div class="avb-banners avb-dots-left avb-inner">
		<div class="avb-banner" data-type="avb_image">
			<div class="avb-banner__caption">
				<div class="max__width">
					<div class="avb-banner__caption-wrap">
						<h1><?php echo $term->name; ?></h1>
						<p>Blog <?php echo $top_heading; ?></p>
					</div>
				</div>
			</div>
		</div>
	</div><!-- avb-banners -->
</section>
<section class="blog">
	<div class="max__width">

		<div class="blog--archive">
			<div class="blog--loop">
				<?php include FL1_BLOG_PATH . 'templates/blog-loop.php'; ?>
				<?php FL1_Helpers::pagination($blogs['max_num_pages']); ?>
			</div>

			<?php include 'blog-filters.php'; ?>
		</div>
	</div>
</section>

<?php get_footer(); ?>