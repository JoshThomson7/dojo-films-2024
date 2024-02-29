<?php
/*
    APF Update properties
*/

if(!current_user_can('administrator')) {
    wp_redirect(home_url());
}

global $post;

get_header();
?>

    <section class="banners inner">
        <div class="banner orange">
            <div class="banner__caption">
                <h1><?php echo get_the_title($post->ID); ?></h1>
            </div><!-- banner__caption -->

            <div class="banner__pattern"><img src="<?php echo esc_url(get_stylesheet_directory_uri()); ?>/img/patterns/leaf.svg" class="style-svg"></div>
            <div class="banner__shadow"></div>
        </div><!-- banner -->
    </section><!-- banner -->

    <section class="apf__update">
        <div class="apf__update__width">
            <h2>To start the update process click on the button below.</h2>
            <p>Do not refresh or close this window while the update is processing.</p>

            <a href="#" class="apf__update__trigger">Update now</a>

            <div id="apf_update_properties"></div>
        </div><!-- apf__update__width -->
    </section><!-- apf__update -->

<?php get_footer(); ?>
