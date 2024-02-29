<?php do_action('before_footer'); ?>

<footer role="contentinfo">
	<div class="footer__menus">
		<div class="max__width">
			<?php
			while (have_rows('footer_menus', 'options')) : the_row();

				$footer_menu = get_sub_field('footer_menu');
			?>
				<article class="footer__menu">
					<?php if ($footer_menu) : ?>
						<h5><?php echo $footer_menu->name; ?> <i class="fas fa-chevron-down"></i></h5>
						<?php wp_nav_menu(array('menu' => $footer_menu->name, 'container' => false, 'depth' => 1)); ?>
					<?php endif; ?>
				</article>

			<?php endwhile; ?>
		</div>
	</div>
</footer>

<div class="subfooter">
		<div class="max__width">

		<div class="subfooter--left">
			<small>&copy; <?php bloginfo('name') ?> <?php echo date('Y'); ?>. Registered with number: 10246071</small>
		</div><!-- subfooter--left -->

		<div class="subfooter--right">
			<small><a href="http://www.fl1.digital" target="_blank">Powered by FL1 Digital</a></small>
		</div><!-- subfooter--left -->

	</div><!-- max__width -->
</div><!-- subfooter -->

<div class="spotlight-search">
	<div class="spotlight-search--content">
		<a href="#" class="spotlight-close"><i class="fal fa-times"></i></a>

		<h2>Search <?php bloginfo('name'); ?></h2>
		<form action="<?php echo esc_url(home_url()); ?>">
			<input type="text" name="s" placeholder="ie. Tenants" />
			<button type="submit" class="button primary"><i class="fal fa-search"></i></button>
		</form>
	</div>
</div>

</div><!-- #page -->

<?php wp_footer(); ?>
</body>

</html>