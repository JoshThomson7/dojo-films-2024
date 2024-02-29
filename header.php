<!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <title><?php wp_title( ' - ', true, 'right' ); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <meta name="format-detection" content="telephone=no">
	<meta name="theme-color" content="#F96852"/>

	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700&family=Poppins:wght@500;700&display=swap" rel="stylesheet">
	<link rel='stylesheet' href='https://charlottes-bakery.wpdev3.com/wp-content/themes/charlottes-bakery-2023/modules/fl1-woo/assets/fl1-woo.min.css' type='text/css' media='all' />

    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

    <?php include get_stylesheet_directory().'/modules/mega-menu-mobile.php'; ?>

	<div id="page">
		<header class="header">
			<?php if(get_field('sitewide_notice_enable', 'option')): ?>
				<div class="header--site__wide__notice">
					<div class="max__width">
						<?php the_field('sitewide_notice', 'option'); ?>
					</div>
				</div>
			<?php endif; ?>

			<div class="header__main">
				<div class="max__width">
					<div class="header__main--left">
						<div class="logo">
							<a href="<?php echo esc_url(home_url()); ?>" title="<?php bloginfo('name'); ?>">
								<img src="<?php echo esc_url(get_stylesheet_directory_uri().'/img/logo.png'); ?>" />
							</a>
						</div>
					</div>

					<div class="header__main--right">
						<?php include get_stylesheet_directory().'/modules/mega-menu.php'; ?>

						<ul class="actions"> 
							<li class="burger-menu">
								<a href="#nav_mobile">
									<i class="fal fa-bars"></i>
								</a>
							</li>
						</ul>

						<ul class="buttons">
							<a href="#" class="button outline">Login <i class="fa fa-arrow-right"></i></a>
							<a href="#" class="button white">Register <i class="fa fa-arrow-right"></i></a>
						</ul>
					</div>
				</div><!-- max__width -->
			</div><!-- header__main -->
		</header><!-- header -->