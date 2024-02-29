<?php
/**
 * AVB Home Banners
 *
 * @package advanced-video-banners/
 * @version 1.0
 * @dependencies
 *      ACF PRO: https://www.advancedcustomfields.com/pro/
 *      Lighslider: http://sachinchoolur.github.io/lightslider/examples.html
 *      @see lib/lightslider
 *      YouTube API: https://developers.google.com/youtube/iframe_api_reference
 */

global $post;

$banner_height = get_field('avb_height');
$banner_dots_position = get_field('avb_dots_position');
$banner_down_arrow = get_field('avb_down_arrow');
$banners = get_field('avb');

$avb_page = is_front_page() ? 'avb-home' : 'avb-inner';

if(!empty($banners)):
?>

    <section class="avb">

        <div class="avb-banners avb-dots-<?php echo $banner_dots_position; ?> <?php echo $avb_page; ?>">
            <?php
                $avb_count = 1;
                foreach($banners as $banner_data):
                
                $banner_data['index'] = $avb_count;
                $banner = new AVB_Banner($banner_data);
            ?>
                
                <div class="avb-banner" data-type="<?php echo $banner->layout(); ?>" data-duration="<?php echo $banner->duration(); ?>">

                    <div class="avb-banner__caption">
                        <div class="max__width">
                            <div class="avb-banner__caption-wrap">
                                <?php
                                    if($banner->get_prop('logo')):
                                    $small_logo = $banner->get_prop('logo_small') ? 'small' : '';
                                ?>
                                    <figure class="<?php echo $small_logo; ?>"><?php echo $banner->logo(); ?></figure>
                                <?php endif; ?>
                                <?php if($banner->headingTop()): ?><?php echo $banner->headingTop(); ?><?php endif; ?>
                                <?php if($banner->heading()): ?><?php echo $banner->heading(); ?><?php endif; ?>
                                <?php if($banner->caption()): ?><h3><?php echo $banner->caption(); ?></h3><?php endif; ?>
								<?php echo $banner->sub_caption() ? '<div class="sub-caption">'.$banner->sub_caption().'</div>' : ''; ?>

                                <?php if($banner->button_label() || $banner->button_2_label()): ?>
                                    <div class="avb-banner__caption-actions">
										<?php if($banner->button_label()): ?>
											<a href="<?php echo $banner->button_url(); ?>"<?php echo $banner->button_url_target(); ?> title="<?php echo $banner->button_label(); ?>" class="button primary">
												<span><?php echo $banner->button_label(); ?></span>
											</a>
										<?php endif; ?>

										<?php if($banner->button_2_label()): ?>
											<a href="<?php echo $banner->button_2_url(); ?>"<?php echo $banner->button_2_url_target(); ?> title="<?php echo $banner->button_2_label(); ?>" class="button primary">
												<span><?php echo $banner->button_2_label(); ?></span>
											</a>
										<?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                        
                    <figure class="avb-banner__overlay <?php echo $banner->overlay_opacity(); ?>"></figure>

                    <div class="avb-banner__media">
                        <?php include AVB_PATH.'templates/'.$banner->layout().'.php'; ?>

                        <?php if($banner->get_prop('image_mobile')): ?>
                            <div class="avb-banner__medium show-on-mobile image" style="background-image:url(<?php echo $banner->image_mobile(); ?>);">
                                <img data-lazy="<?php echo $banner->image_mobile(); ?>">
                            </div>
                        <?php endif; ?>
                    </div>

                </div>

            <?php $avb_count++; endforeach; ?>

        </div><!-- avb-banners -->

        <?php if($banner_down_arrow): ?>
            <div class="avb__down-arrow">
                <figure>
                    <?php echo file_get_contents(AVB_PATH.'img/avb-chevron-down.svg'); ?>
                </figure>
            </div>
        <?php endif; ?>

    </section><!-- avb -->

<?php else: ?>
    
    <section class="avb">
        <div class="avb-banners avb-empty">
            <div class="avb-banner">
                <div class="avb-banner__caption">
                    <div class="max__width">
                        <div class="avb-banner__caption-wrap">
                            <h1><?php echo get_the_title($post->ID); ?></h1>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

<?php endif; ?>
