<nav class="mega-menu" role="navigation">
    <ul>
        <?php
            while(have_rows('mega_menu', 'option')) : the_row();
            
            if(get_sub_field('menu_item_hide')) { continue; }

            $left = get_sub_field('menu_left_position') ?? '';
        ?>
            <li class="<?php if(get_sub_field('menu_item_columns')): ?>has-columns<?php endif; ?>">
                <a href="<?php the_sub_field('menu_item_link'); ?>">
                    <?php if(get_sub_field('menu_item_icon')): ?><span class="nav__icon <?php the_sub_field('menu_item_icon'); ?>"></span><?php endif; ?>
                    <?php the_sub_field('menu_item_label'); ?>
                </a>

                <?php if(have_rows('menu_item_columns')): ?>
                    <div class="panel" <?php echo $left ? 'style="left: '.$left.'px; border-top-left-radius: var(--fl1-radius)"' : ''; ?>>
                        <?php 
                            while(have_rows('menu_item_columns')) : the_row();
                            
                            $column_classes = array();

                            $column_name = get_sub_field('column_name');
                            $column_name_link = get_sub_field('column_link');
                            $column_name_link_target = get_sub_field('column_name_link_target');

                            $column_icon = get_sub_field('column_icon');
                            if($column_icon) {
                                $column_classes[] = 'has__icon';
                            }

                            $column_icon_img = get_sub_field('column_icon_img');
                            if($column_icon_img) {
                                $column_classes[] = 'has__icon';
                            }

                            $column_theme = get_sub_field('column_theme');
                            if($column_theme) {
                                $column_classes[] = 'tlc-theme-'.$column_theme;
                            }
                        ?>
                            <div class="panel__column <?php echo join(' ', $column_classes); ?>">
                                <?php if($column_name): ?>
                                    <h3>
                                        <?php
                                            if($column_name_link):

                                                $target = '';
                                                if($column_name_link_target) {
                                                    $target = ' target="_blank"';
                                                }
                                        ?>
                                            <a href="<?php echo $column_name_link; ?>"<?php echo $target; ?>>
                                        <?php endif; ?>

                                        <?php if($column_icon): ?><span class="<?php echo $column_icon; ?>"></span><?php endif; ?>
                                        <?php if($column_icon_img): ?>
                                            <span><img src="<?php echo $column_icon_img; ?>" /></span>
                                        <?php endif; ?>

                                        <?php echo $column_name; ?>

                                        <?php if($column_name_link): ?></a><?php endif; ?>
                                    </h3>
                                <?php endif; ?>

                                <?php
                                    // -------------- COLUMN --------------
                                    if(get_row_layout() == 'submenu_column'):
                                    $column_subs = get_sub_field('column_subs');

                                    if($column_subs):
                                ?>
                                        <ul class="submenu">
                                            <?php
                                                foreach($column_subs as $column_sub):

                                                    $post_img = '';
                                                    $post_title = '';
                                                    $post_url = '';

                                                    if($column_sub['type'] == 'post') {

                                                        $post_id = $column_sub['post'];
                                                    
                                                        $attachment_id = get_field('page_banner', $post_id);
                                                        if($attachment_id) {
                                                            $post_img = vt_resize($attachment_id, '', 150, 150, true);
                                                        }

                                                        $post_title = get_the_title($post_id);
                                                        $post_url = get_permalink($post_id);

                                                    } else {

                                                        $custom = $column_sub['custom'];
                                                        $custom_img = $custom['image'];

                                                        $post_title = $custom['label'];
                                                        $post_url = $custom['url'];

                                                        if($custom_img) {
                                                            $post_img = vt_resize($custom_img, '', 150, 150, true);
                                                        }

                                                    }
                                            
                                            ?>
                                                <li>
                                                    <a href="<?php echo $post_url; ?>">
                                                        <?php if($post_img): ?>
                                                            <figure><img src="<?php echo $post_img['url']; ?>" alt="<?php echo $post_title; ?>"></figure>
                                                        <?php endif; ?>
                                                        <?php echo $post_title; ?>
                                                    </a>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php endif; ?>

                                    <?php
                                    // -------------- Icon Links --------------
                                    elseif(get_row_layout() == 'icon_links'):
                                        $column_subs = get_sub_field('column_subs');
                                    ?>
                                        
                                        <ul class="submenu icon-links">
                                            <?php
                                                foreach($column_subs as $column_sub):
                                                    $label = $column_sub['label'];
                                                    $url = $column_sub['url'];
                                                    $icon = $column_sub['icon'];
                                            ?>
                                                <li>
                                                    <a href="<?php echo $url; ?>">
                                                        <?php if($icon): ?>
                                                            <figure><i class="<?php echo $icon; ?> fa-fw"></i></figure>
                                                        <?php endif; ?>
                                                        <?php echo $label; ?>
                                                    </a>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php endif; ?>
                            </div><!-- panel__column -->
                        <?php endwhile; ?>
                    </div><!-- panel -->
                <?php endif; ?>
            </li>
        <?php endwhile; ?>
    </ul>
</nav>
