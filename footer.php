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

</div><!-- #page -->

<?php wp_footer(); ?>
</body>

</html>