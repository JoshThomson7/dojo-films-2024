<?php
/**
 * FC Helpers
 *
 * Helper static methods for the FC module.
 */

 // Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class FC_Helpers {
    
    public static function flexible_content($fc_id = null) {

        $current_user_id = get_current_user_id();

        do_action('fl1_before_flexible_content');

        if(have_rows('fc_content_types', $fc_id)) {

            $row_count = 1;
			$skip = array(
				'fc_wrapper_open',
				'fc_wrapper_close',
			);

            if(!$fc_id) {
                echo '<div class="flexible__content">';
            }

            while(have_rows('fc_content_types', $fc_id)) {
                the_row();

                // open section - see fc-functions.php
                $open = FC_Helpers::fc_field_section(get_row_layout(), 'open', $row_count);

                if(!$open['skip_open']) {
                    echo get_row_layout() === 'fc_global' ? '' : $open['html'];
                }

                if(file_exists(FC_PATH . 'layouts/'.get_row_layout().'.php')) {
                    require FC_PATH . 'layouts/'.get_row_layout().'.php';
                } else {
					if(!in_array(get_row_layout(), $skip)) {
						echo '<p>Layout not found: '.get_row_layout().'.php</p>';
					}
				}

                // close section - see fc-functions.php
                $close = FC_Helpers::fc_field_section(get_row_layout(), 'close', $row_count);

                if(!$close['skip_close']) {
                    echo get_row_layout() === 'fc_global' ? '' : $close['html'];
                }

                $row_count++; 
            }

            if(!$fc_id) {
                echo '</div><!-- flexible__content -->';
            }
        }

        do_action('fl1_after_flexible_content');
    }

    public static function fc_field_section($row_layout, $open_close, $row_count) {

        $fc_classes = array('fc-layout', $row_layout);
        $fc_layout_container_classes = array('fc-layout-container');
        $max_width_classes = array('max__width');

        // section heading
        $options = get_sub_field('fc_options');
        $option_heading_logo = $options['heading_logo'];
        $option_top_heading = $options['top_heading'];
        $option_heading = $options['heading'];
        $option_heading_center = $options['heading_center'];
        $option_dots_separator = $options['dots_separator'];
        $option_caption = $options['caption'];
        $option_tab = $options['tab_name'];

        // generate section ID
        $tab_id = '';
        if($option_tab) {
            $tab_id = ' id="'.strtolower(preg_replace("#[^A-Za-z0-9]#", "", $option_tab)).'_section"';
        }

        // Styles
        $style = array();
        $fc_styles = get_sub_field('fc_styles');

        // Padding
        $padding = $fc_styles['fc_padding'];
        $padding_style = 'padding:';
        $padding_style .= !empty($padding['fc_padding_top']) ? ' '.$padding['fc_padding_top'].'px' : ' 0';
        $padding_style .= !empty($padding['fc_padding_right']) ? ' '.(($padding['fc_padding_right']*100)/1200).'%' : ' 0';
        $padding_style .= !empty($padding['fc_padding_bottom']) ? ' '.$padding['fc_padding_bottom'].'px' : ' 0';
        $padding_style .= !empty($padding['fc_padding_left']) ? ' '.(($padding['fc_padding_left']*100)/1200).'%' : ' 0';

		// Background
		$background = $fc_styles['fc_background'];
		if($background) {
			$fc_layout_container_classes[] = 'fc-background fc-bk-'.$background;
		}

		$background_shrink = $fc_styles['fc_background_shrink'];
		if($background && $background_shrink) {
			$fc_layout_container_classes[] = 'fc-background-shrink';
		}

		$shapes = $fc_styles['fc_shapes'];
		$shape_side = $shapes['side'];

        $style[] = $padding_style;

        $fc_classes = join(' ', $fc_classes);
        $fc_layout_container_classes = join(' ', $fc_layout_container_classes);
		$max_width_classes = join(' ', $max_width_classes);

        // full width
        $full_width = $fc_styles['fc_full_width'] == true ? true : false;

        // open/close
        $html = '';
        if($open_close === 'open') {

            if($row_layout === 'fc_carousel_open') {

                $html .= '<div class="fc-layout-carousel '.$fc_classes.'">';

            } else {

                $html .= '<section'.$tab_id.' class="'.$fc_classes.'">';
                $html .='<div class="'.$fc_layout_container_classes.'" style="'.$padding_style.'">';

				if($shape_side) {
					$shape_container_classes = array('fc-shape');
					$shape_container_classes[] = $shape_side === 'left' ? 'fc-shape-left' : 'fc-shape-right';
					if($shapes['shape']) {
						$shape_container_classes[] = $shapes['shape'] === 'x' ? 'fc-shape-x' : 'fc-shape-one';
					}

					if($shapes['flip']) {
						$shape_container_classes[] = 'fc-shape-flip';
					}

					$shape_styles = array();

					if($shapes['top']) {
						$shape_styles[] = 'top:'.$shapes['top'].'px;';
					}

					if($shapes['left_right']) {
						$shape_lr_px = $shape_side === 'left' ? '2024px' : '1690px';
						$shape_styles[] = '--fc-shape-side: calc('.$shape_lr_px.' + '.$shapes['left_right'].'px)';
					}

					$html .='<div class="'.join(' ', $shape_container_classes).'" style="'.join(' ', $shape_styles).'"></div>';
				}

                // check if full width
                if(!$full_width && $row_layout !== 'fc_wrapper_open') {
                    $html .='<div class="'.$max_width_classes.'">';
                }

                if($option_top_heading || $option_heading || $option_caption) {
                    $centre_heading = '';
                    if($option_heading_center) {
                        $centre_heading = ' centred';
                    }

                    $section_top_heading = '';
                    if($option_top_heading) {
                        $section_top_heading = '<h5>'.$option_top_heading.'</h5>';
                    }

                    $section_heading = '';
                    if($option_heading) {
                        $section_heading = '<h2>'.$option_heading.'</h2>';
                    }

                    $section_caption = '';
                    if($option_caption) {
                        $section_caption = $option_caption;
                    }

                    $html .= '<div class="fc-layout-heading'.$centre_heading.'">';
                    $html .= '<div class="fc-layout-heading-left">'.$section_top_heading.$section_heading.$section_caption.'</div>';
                    $html .= '<div class="fc-layout-heading-right"></div>';
                    $html .= '</div>';
                }
            }


        } elseif($open_close === 'close') {

            if($row_layout === 'fc_carousel_close') {

                $html .= '</div>';

            } else {

                // check if full with
                if(!$full_width && $row_layout !== 'fc_wrapper_close') {
                    $html .= '</div><!-- max__width -->';
                    $html .='</div><!-- fc-layout-container -->';
                    $html .= '</section><!-- '.$row_layout.' -->';
                } else {
                    $html .= '</div><!-- fc-layout-container -->';
                    $html .= '</section><!-- '.$row_layout.' -->';
                }

            }

        }

        switch ($row_layout) {
            case 'fc_carousel_open':
            case 'fc_wrapper_open':
                $skip_close = true;
                $skip_open = false;
                break;

            case 'fc_carousel_close':
            case 'fc_wrapper_close':
                $skip_close = false;
                $skip_open = true;
                break;
            
            default:
                $skip_close = false;
                $skip_open = false;
                break;
        }

        return array(
            'html' => $html,
			'is_full_width' => $full_width,
            'skip_open' => $skip_open,
            'skip_close' => $skip_close,
        );
    }

	public static function video_popup($string) {

		$pattern = '/(https?:\/\/)?(www\.)?(youtube\.com|youtu\.?be)\/.+$/';
		$pattern2 = '/(https?:\/\/)?(www\.)?(vimeo\.com)\/.+$/';

		if(preg_match($pattern, $string) || preg_match($pattern2, $string)) {
			return true;
		}

		return false;

	}

}