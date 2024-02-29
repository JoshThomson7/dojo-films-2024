<?php
/**
 * FC - Feature Boxes
 */

$box_classes = array('');

if($per_row = get_sub_field('feature_boxes_per_row')) { 
    $box_classes[] = $per_row;
}

if(get_sub_field('feature_boxes_bg')) { 
    $box_classes[] = 'with-bg';
}

if($text_align = get_sub_field('text_align')) { 
    $box_classes[] = $text_align;
}
?>

<div class="fc_feature_boxes_wrapper">
    <?php while(have_rows('feature_boxes')) : the_row(); ?>
        <article class="<?php echo join(' ', $box_classes); ?>">
            <div class="fc__feature__box__inner">
                <h3>
                    <?php if(get_sub_field('feature_box_icon')): ?><figure><i class="<?php the_sub_field('feature_box_icon'); ?>"></i></figure><?php endif; ?>
                    <?php if(get_sub_field('feature_box_button_url')): ?><a href="<?php the_sub_field('feature_box_button_url'); ?>" title="<?php the_sub_field('feature_box_heading'); ?>"><?php endif; ?>
                        <?php the_sub_field('feature_box_heading'); ?>
                    <?php if(get_sub_field('feature_box_button_url')): ?></a><?php endif; ?>
                </h3>

                <?php the_sub_field('feature_box_content'); ?>

                <?php
                    if(get_sub_field('feature_box_button_url') || get_sub_field('feature_box_load_form')):
                ?>
                    <a href="<?php the_sub_field('feature_box_button_url'); ?>" class="link animate-icon">
                        <?php the_sub_field('feature_box_button_label'); ?> <i class="fa fa-chevron-right"></i>
                    </a>
                <?php endif; ?>
            </div><!-- fc__feature__box__inner -->
        </article>
    <?php endwhile; ?>
</div><!-- fc_feature_boxes_wrapper -->
