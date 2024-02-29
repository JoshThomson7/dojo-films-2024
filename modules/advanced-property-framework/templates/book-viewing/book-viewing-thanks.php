<?php
/*
    APF Bookm a viewing (thanks)
*/

// // piss off if not set
// $property_id = $_GET['ref'];
// if(!isset($property_id) || $property_id === '') {
//     wp_redirect(esc_url(home_url().'/property-search/'));
// }

get_header();

avb_banner();

$property_id = $_GET['ref'];

$args = array(
    'post_type'     => 'property',
    'post_status'   => 'publish',
    'post__in'      =>  array($property_id)
);

$property_query = new WP_Query($args);
?>

    <?php while($property_query->have_posts()) : $property_query->the_post(); ?>

        <section class="apf apf__combined apf__book__viewing__thanks">
            <div class="max__width">

                <div class="apf__results">
                    <div class="apf__results__list">
                        <div class="apf__properties list">
                            <article>

                                <div class="apf__property__border">

                                    <a href="<?php the_permalink(); ?>" class="apf__property__img" title="<?php the_title(); ?>" style="background-image:url('<?php apf_the_property_image(null, null, false, true); ?>');">

                                        <?php apf_the_property_status(); ?>

                                        <div class="apf__property__img__overlay">
                                            <span class="ion-ios-plus-outline"></span>
                                        </div><!-- property__img__overlay -->
                                    </a>

                                    <div class="apf__property__details__wrap">

                                        <div class="apf__property__details">
                                            <h3><?php apf_the_property_price(); ?></h3>
                                            <h5><?php apf_the_property_seo_title(); ?></h5>
                                            <p><?php the_title(); ?></p>
                                        </div><!-- apf__property__details -->

                                        <div class="apf__property__meta">
                                            <div class="apf__property__meta__data">

                                                <?php if(get_field('property_receptions')): ?>
                                                    <span><i class="fi flaticon-sofa"></i><?php the_field('property_receptions'); ?></span>
                                                <?php endif; ?>

                                                <?php if(get_field('property_bedrooms')): ?>
                                                    <span><i class="fi flaticon-bed"></i><?php the_field('property_bedrooms'); ?></span>
                                                <?php endif; ?>

                                                <?php if(get_field('property_bathrooms')): ?>
                                                    <span><i class="fi flaticon-bath"></i><?php the_field('property_bathrooms'); ?></span>
                                                <?php endif; ?>

                                            </div><!-- apf__property__meta__data -->

                                            <a href="<?php the_permalink(); ?>" title="Full details" class="apf__property__meta__action">Full details</a>
                                        </div><!-- apf__property__meta -->

                                    </div><!-- apf__property__details__wrap -->

                                </div><!-- apf__property__border -->
                            </article>
                        </div><!-- apf__properties -->
                    </div><!-- apf__results__list -->
                </div><!-- apf__results -->

            </div><!-- max__width -->
        </section><!-- apf__book__viewing__thanks -->

    <?php endwhile; wp_reset_postdata(); ?>

    <?php include(get_stylesheet_directory().'/modules/flexible-content/flexible-content.php'); ?>

<?php get_footer(); ?>
