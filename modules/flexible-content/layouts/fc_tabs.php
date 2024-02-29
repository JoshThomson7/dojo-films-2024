<?php
/*
---------------------------
  ______      __
 /_  __/___ _/ /_  _____
  / / / __ `/ __ \/ ___/
 / / / /_/ / /_/ (__  )
/_/  \__,_/_.___/____/

---------------------------
Tabs
*/

$tab_classes = array();

$layout = get_sub_field('tabs_layout');
if($layout) {
    $tab_classes[] = $layout;
}

$no_content = get_sub_field('tab_no_content');
if($no_content) {
    $tab_classes[] = 'no-content';
}

$tabs_alignment = get_sub_field('tabs_alignment');
if($tabs_alignment) {
    $tab_classes[] = $tabs_alignment;
}

$tabs_content_align = get_sub_field('tabs_content_align');
if($tabs_content_align) {
    $tab_classes[] = 'content-'.$tabs_content_align;
}
?>

<div class="tabbed-wrapper <?php echo join(' ', $tab_classes); ?>">
    <ul class="tabbed <?php echo $no_content ? 'no-content' : ''; ?>">
        <?php
            while(have_rows('tabs')) : the_row();
            $tabbed_id = strtolower(preg_replace("#[^A-Za-z0-9]#", "", get_sub_field('tab_heading')));
            $button_link = get_sub_field('tab_button_link');
            $tab_href = $no_content ? $button_link : '#';
            $active = '';
            if($no_content && $button_link) {
                $path = str_replace(home_url(), '', $button_link);
                $page = get_page_by_path($path);
                if($page && $page->ID == get_the_ID()) {
                    $active = ' class="active"';
                }
            }
        ?>
            <li<?php echo $active; ?>>
                <a href="<?php echo $tab_href ? $tab_href : '#'; ?>" data-id="<?php echo $tabbed_id; ?>_tabbed" title="<?php the_sub_field('tab_heading'); ?>">
                    <?php if(get_sub_field('tab_icon')): ?>
                        <figure><img src="<?php the_sub_field('tab_icon'); ?>" /></figure>
                    <?php endif; ?>

                    <?php if(!get_sub_field('tab_heading_hide')): ?>
                        <strong><?php the_sub_field('tab_heading'); ?></strong>
                    <?php endif; ?>
                </a>
            </li>
        <?php endwhile; ?>
    </ul>

    <?php
        if(!$no_content):
            while(have_rows('tabs')) : the_row();
            $tab_link_only = get_sub_field('tab_link_only');
            $tabbed_id = strtolower(preg_replace("#[^A-Za-z0-9]#", "", get_sub_field('tab_heading')));
            $tab_align = get_sub_field('tab_align');
    ?>
            <div class="tab__content <?php echo $tabbed_id; ?>_tabbed">
                <div class="tab__content--text">
                    <?php echo apply_filters('the_content', get_sub_field('tab_content')); ?>
                </div>

                <?php if(get_sub_field('tab_button_label') && get_sub_field('tab_button_link')): ?>
                    <a href="<?php the_sub_field('tab_button_link'); ?>" class="button primary icon-right"><span><?php the_sub_field('tab_button_label'); ?></span> <i class="fa fa-chevron-right"></i></a>
                <?php endif; ?>
            </div><!-- tab-content -->
        <?php endwhile; ?>
    <?php endif; ?>
</div>