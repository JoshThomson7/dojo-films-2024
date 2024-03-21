<?php do_action('before_footer'); ?>

<footer role="contentinfo">
	<div class="subfooter">
		<div class="max__width">

			<div class="subfooter--left">
				<a href="<?php echo esc_url(home_url()); ?>" title="<?php bloginfo('name'); ?>">
					<img src="<?php echo esc_url(get_stylesheet_directory_uri().'/img/logo.png'); ?>" />
				</a>
			</div><!-- subfooter--left -->
		</div><!-- max__width -->
	</div><!-- subfooter -->
</footer>

</div><!-- #page -->

<?php wp_footer(); ?>
</body>

</html>