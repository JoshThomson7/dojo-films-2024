<?php
/**
 * Blog Item
 *
 * @package Blog
 * @version 1.0
*/

$blog = new FL1_Blog($blog_id);
$blog_image = $blog->image('bg', 400, 240);
$blog_cat_id = $blog->main_category('ids');
$blog_cat = $blog->main_category('id=>name');
?>
<article class="blog--post">
	<div class="blog--post-padder">
		<a class="blog--post-img" href="<?php echo $blog->url(); ?>" <?php echo $blog_image; ?>></a>
		
		<div class="blog--post-content">
			<?php if($blog_cat): ?>
				<h5>
					<a href="<?php echo get_term_link($blog_cat_id, 'category'); ?>"><?php echo $blog_cat; ?></a>
				</h5>
			<?php endif; ?>
			<h2><a href="<?php echo $blog->url(); ?>" title="<?php echo $blog->title(); ?>"><?php echo $blog->title(); ?></a></h2>

			<date>
				<?php echo $blog->date('j M Y') ?>
			</date>
			
			<p><?php echo $blog->excerpt(8); ?></p>

			<div class="blog--post-action">
				<a href="<?php echo $blog->url(); ?>" class="link animate-icon">
					<span>Read more</span> <i class="fa fa-chevron-right"></i>
				</a>
			</div>
		</div>
	</div>
</article>