<?php
/*
*	Blog Single
*
*	@package Blog
*	@version 1.0
*/

global $post;
get_header();

$blog = new FL1_Blog($post->ID);
$blog_cat = $blog->main_category('id=>name');
$blog_image = $blog->image('bg', 1200, 600);
$excerpt = $blog->excerpt(1000);
?>

<div class="max__width">
	<article class="blog--hero" <?php echo $blog_image; ?>>
		<div class="blog--hero-overlay">
			<div class="blog--hero-content">
				<?php if($blog_cat): ?>
					<h5><?php echo $blog_cat; ?></a></h5>
				<?php endif; ?>
				
				<h2><?php echo $blog->title(); ?></h2>

				<date>
					<?php echo $blog->date('M jS Y') ?>
				</date>
				
				<p><?php echo $blog->excerpt($blog ? 35 : 15); ?></p>

			</div>
		</div>
	</article>
</div>

<div class="blog--content">
	<?php FC_Helpers::flexible_content();?>
</div>

<?php get_footer(); ?>