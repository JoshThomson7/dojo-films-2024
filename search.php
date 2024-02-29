<?php
/**
 * Search results page
 */
get_header();
?>

	<section class="search">
        <div class="max__width">

			<h2>Here's what we found for your search matching <span>&quot;<?php echo $s; ?>&quot;</span>:</h2>

			<?php if(have_posts()): ?>
				<ul class="site-search-results">
					<?php
						while(have_posts()) : the_post();
						if(get_post_type() == 'page') {
							$top_parent_id = FL1_Helpers::get_top_parent_page_id($post->ID); // Get top parent page ID width get_top_parent_page_id() in functions.php
							$top_parent_page_objects = get_post($top_parent_id); // Store parent page objects
							$top_parent_page_title = $top_parent_page_objects->post_title; // Get parent page title
							$posted_under = $top_parent_page_title;
						} elseif(get_post_type() == 'post') {
							$posted_under = 'Blog';
						} elseif(get_post_type() == 'team') {
							$posted_under = 'Team';
						} elseif(get_post_type() == 'page') {
							$posted_under = 'Page';
						} elseif(get_post_type() == 'product') {
							$posted_under = 'Resource';
						}
					?>
						<li><a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?><span><?php echo $posted_under; ?></span></a></li>
					<?php endwhile; wp_reset_query(); ?>
				</ul><!-- site-search-results -->

				<?php FL1_Helpers::pagination(); ?>
			<?php else: ?>
				<p>Please check your spelling or try a different search.</p>
			<?php endif; ?>

        </div><!-- max__width -->
    </section>

<?php get_footer(); ?>
