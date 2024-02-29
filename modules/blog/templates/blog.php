<?php
/**
 * Blog
 */

get_header();

$featured = FL1_Blog_Helpers::get_blogs(array(
	'posts_per_page' => 1,
));
$featured_id = reset($featured['posts']);
$featured = new FL1_Blog($featured_id);
$featured_image = $featured->image('bg', 1200, 600);

$current_page = get_query_var('paged');
$current_page = max(1, $current_page);

$per_page = 15;

$args = array(
	'paged' => $current_page,
	'posts_per_page' => $per_page,
	'post__not_in' => array($featured_id),
);

$blogs = FL1_Blog_Helpers::get_blogs($args);

AVB::avb_banners();
?>
<section class="blog">
	<div class="max__width">

		<article class="blog--post-featured" <?php echo $featured_image; ?>>
			<a href="<?php echo $featured->url(); ?>" title="<?php echo $featured->title(); ?>" class="blog--post-featured--overlay">
				<div class="blog--post-featured--content">
					<?php if($blog_cat): ?>
						<h5><?php echo $blog_cat; ?></a></h5>
					<?php endif; ?>
					
					<h2><?php echo $featured->title(); ?></h2>

					<date>
						<?php echo $featured->date('M jS Y') ?>
					</date>
					
					<p><?php echo $featured->excerpt($featured ? 35 : 15); ?></p>

					<div class="blog--post-featured--action">
						<span>Read more</span>
					</div>
				</div>
			</a>
		</article>

		<div class="blog--cats">
			<select onchange="var selectedOption = this.options[this.selectedIndex]; var url = selectedOption.value; if (url !== 'all') { window.location.href = url; }">
				<option value="">Category</option>
				<?php
					$categories = get_categories();
					foreach ($categories as $category) {
						echo '<option value="' . get_term_link($category, 'category') . '">' . $category->name . '</option>';
					}
				?>
			</select>
		</div>

		<div class="blog--loop grid">
			<?php
				$featured = '';
				include FL1_BLOG_PATH . 'templates/blog-loop.php';
				FL1_Helpers::pagination($blogs['max_num_pages'], 4, true);
			?>
		</div>
	</div><!-- max__width -->
</section><!-- blog -->

<?php get_footer(); ?>
