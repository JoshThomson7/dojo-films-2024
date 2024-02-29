<?php
/**
 * FL1 Helpers
 *
 * Helper static functions
 *
 * @author  fl1
 * @link    http://fl1.digital
 * @version 1.0
 */

 // Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class FL1_Helpers {

    public static function byte_size($bytes) {
        $size = $bytes / 1024;
        if($size < 1024)
            {
            $size = number_format($size, 2);
            $size .= ' KB';
            }
        else
            {
            if($size / 1024 < 1024)
                {
                $size = number_format($size / 1024, 2);
                $size .= ' MB';
                }
            else if ($size / 1024 / 1024 < 1024)
                {
                $size = number_format($size / 1024 / 1024, 2);
                $size .= ' GB';
                }
            }
        return $size;
    }

    public static function get_top_parent_page_id() {
        global $post;
        // Check if page is a child page (any level)
        if ($post->ancestors) {
            //  Grab the ID of top-level page from the tree
            return end($post->ancestors);
        } else {
            // Page is the top level, so use  it's own id
            return $post->ID;
        }
    }

    public static function hide_email($email, $text = '', $classes = '') {
        $character_set = '+-.0123456789@ABCDEFGHIJKLMNOPQRSTUVWXYZ_abcdefghijklmnopqrstuvwxyz';
        $key = str_shuffle($character_set); $cipher_text = ''; $id = 'e'.rand(1,999999999);
        for ($i=0;$i<strlen($email);$i+=1) $cipher_text.= $key[strpos($character_set,$email[$i])];
        $script = 'var a="'.$key.'";var b=a.split("").sort().join("");var c="'.$cipher_text.'";var d="";';
        $script.= 'for(var e=0;e<c.length;e++)d+=b.charAt(a.indexOf(c.charAt(e)));';
        $script.= 'document.getElementById("'.$id.'").innerHTML="<a href=\\"mailto:"+d+"\\" class=\\"'.$classes.'\\">'.($text ? $text : '"+d+"').'</a>"';
        $script = "eval(\"".str_replace(array("\\",'"'),array("\\\\",'\"'), $script)."\")";
        $script = '<script type="text/javascript">/*<![CDATA[*/'.$script.'/*]]>*/</script>'; return '<span id="'.$id.'">[javascript protected email address]</span>'.$script;
    }

    public static function pagination($pages = '', $range = 4, $count = null) {
        $showitems = ($range * 2)+1;
    
        global $paged;
        if(empty($paged)) $paged = 1;
    
        if($pages == '') {
            global $wp_query;
            $pages = $wp_query->max_num_pages;
            if(!$pages) {
                $pages = 1;
            }
        }
    
        if(1 != $pages) {
    
            echo "<div class=\"pagination\">";
            echo ($count === true) ? "<div class=\"page__count\">Page ".$paged." of ".$pages."</div>" : '';
            echo "<div class=\"page__numbers\">";
            if($paged > 2 && $paged > $range+1 && $showitems < $pages) echo "<a href='".get_pagenum_link(1)."'>&laquo;</a>";
            if($paged > 1 && $showitems < $pages) echo "<a href='".get_pagenum_link($paged - 1)."'>&lsaquo;</a>";
    
            for ($i=1; $i <= $pages; $i++) {
                if (1 != $pages &&( !($i >= $paged+$range+1 || $i <= $paged-$range-1) || $pages <= $showitems ))
                {
                    echo ($paged == $i)? "<span class=\"current__page\">".$i."</span>":"<a href='".get_pagenum_link($i)."' class=\"inactive\">".$i."</a>";
                }
            }
    
            if ($paged < $pages && $showitems < $pages) echo "<a href=\"".get_pagenum_link($paged + 1)."\">&rsaquo;</a>";
            if ($paged < $pages-1 &&  $paged+$range-1 < $pages && $showitems < $pages) echo "<a href='".get_pagenum_link($pages)."'>&raquo;</a>";
            echo "</div></div>\n";
        }
    }

	public static function ajax_pagination($pages = '', $range = 4, $apf_page = 1) {
		$showitems = ($range * 2)+1;
	
		$paged = $apf_page;
	
		if(empty($paged)) $paged = 1;
	
		if(1 != $pages) {
	
			echo "<div class=\"ajax-pagination\"><div class=\"ajax-pagination--count\">Page ".$paged." of ".$pages."</div><div class=\"ajax-pagination--numbers\">";
			if($paged > 2 && $paged > $range+1 && $showitems < $pages) echo "<a href=\"#\" class=\"ajax-paginate\" data-paged=\"1\">&laquo;</a>";
			if($paged > 1 && $showitems < $pages) echo "<a href=\"#\" class=\"ajax-paginate\" data-paged=\"".($paged - 1)."\">&lsaquo;</a>";
	
			for ($i=1; $i <= $pages; $i++) {
	
				if (1 != $pages &&( !($i >= $paged+$range+1 || $i <= $paged-$range-1) || $pages <= $showitems )) {
					echo ($paged == $i)? "<span class=\"ajax-pagination--current-page\">".$i."</span>":"<a href=\"#\" class=\"inactive ajax-paginate\" data-paged=\"".$i."\">".$i."</a>";
				}
	
			}
	
			if ($paged < $pages && $showitems < $pages) echo "<a href=\"#\" class=\"ajax-paginate\" data-paged=\"".($paged + 1)."\">&rsaquo;</a>";
			if ($paged < $pages-1 &&  $paged+$range-1 < $pages && $showitems < $pages) echo "<a href=\"#\" class=\"ajax-paginate\" data-paged=\"".$pages."\">&raquo;</a>";
			echo "</div></div>\n";
		}
	}

    public static function pretty_print($var) {
       
       echo '<pre>';
       print_r($var);
       echo '</pre>';
       
    }

    public static function post_is_in_descendant_term( $terms, $_post = null ) {
		foreach ( (array) $terms as $term ) {
			// get_term_children() accepts integer ID only
			$descendants = get_term_children( (int) $terms, 'resource_type' );
			if ( $descendants && in_category( $descendants, $_post ) )
				return true;
		}
		return false;
	}

    public static function trunc($string, $word_limit) {

        $words = explode(' ', $string, ($word_limit + 1));
    
        if(count($words) > $word_limit) {
            array_pop($words);
            return implode(' ', $words)." [...]"; //add a ... at last article when more than limit word count
    
        } else {
            return implode(' ', $words);
    
        }
    }

    /**
    * Resize images dynamically using wp built in functions.
    * Update: Use WP_Image_Editor class instead of deprecated image_resize.
    * Updated by Joel Lisenby (original function by Victor Teixeira)
    *
    * php 5.2+
    *
    * Example use:
    * 
    * <?php
    * $thumb = get_post_thumbnail_id(); 
    * $image = vt_resize($thumb,'' , 140, 110, true);
    * ?>
    * <img src="<?php echo $image['url']; ?>" width="<?php echo $image['width']; ?>" height="<?php echo $image['height']; ?>" />
    *
    * @param int $attach_id
    * @param string $img_url
    * @param int $width
    * @param int $height
    * @param bool $crop
    * @return array
    */
    public static function vt_resize($attach_id = null, $img_url = null, $width, $height, $crop = false) {

        if($attach_id) {
            $img_path = get_attached_file($attach_id);
			$image_url = wp_get_attachment_image_src($attach_id, 'full');

			// if extension is svg
			if(is_array($image_url) && pathinfo($image_url[0], PATHINFO_EXTENSION) === 'svg') {
				return array (
					'url' => $image_url[0],
					'width' => '',
					'height' => ''
				);
			}
        } else {
            $img_path = wp_make_link_relative($img_url);
        }
    
        $editor = wp_get_image_editor($img_path);
    
        if(is_wp_error($editor)) { return $editor; }
    
        $editor->resize($width, $height, $crop);
        $new_img_path = $editor->generate_filename();
        $new_img_url = str_replace(wp_normalize_path(untrailingslashit(ABSPATH)), site_url(), wp_normalize_path($new_img_path));
        $new_img = $editor->save($new_img_path);
    
        if(is_wp_error($new_img)) { return $new_img; }
    
        return array (
            'url' => $new_img_url,
            'width' => $new_img['width'],
            'height' => $new_img['height']
        );

    }

    public static function wp_spinner($echo = true) {
        $html = '<div class="wp__loading"><img src="'.admin_url().'/images/spinner-2x.gif"></div>';
        if($echo === true) {
            echo $html;
        } else {
            return $html;
        }
    }

    /**
     * Returns all post types added by the plugin.
     *
     * @since 1.0
     * @param string $src
     */
    public static function registered_post_types($generator = '', $exclude = array()) {

        $post_types = array();
        
        // Filter by generator.
        $args = array(
            'generator' => $generator
        );

        // Get smart post types.
        $get_post_types = get_post_types($args, 'names');

        if(!empty($get_post_types)) {

            if($exclude) {
                $get_post_types = array_diff($exclude, $get_post_types);
            }
        
            // Iterate through post types.
            foreach($get_post_types as $post_type ) {
                $post_types[] = $post_type;
            }

        }

        return $post_types;

    }

    public static function array_to_xml( $data, &$xml_data ) {
        foreach( $data as $key => $value ) {
            if(is_array($value)) {
                if(is_numeric($key)) {
                    $key = 'child_'.$key; //dealing with <0/>..<n/> issues
                }
                $subnode = $xml_data->addChild($key);
                array_to_xml($value, $subnode);
            } else {
                $xml_data->addChild("$key", htmlspecialchars("$value"));
            }
         }
    }

    public static function param($param, $method = 'GET') {

        switch ($method) {
            case 'query_vars':
                global $wp_query;
                return isset($wp_query->query_vars[$param]) && !empty($wp_query->query_vars[$param]) ? $wp_query->query_vars[$param] : null;
                break;

            case 'POST':
                return isset($_POST[$param]) ? $_POST[$param] : null;
                break;
            
            default:
                return isset($_GET[$param]) ? $_GET[$param] : null;
                break;
        }

    }

    public static function search_array($array, $key, $value) {
        $results = array();

        if (is_array($array)) {
            if (isset($array[$key]) && $array[$key] == $value) {
                $results[] = $array;
            }

            foreach ($array as $subarray) {
                $results = array_merge($results, self::search_array($subarray, $key, $value));
            }
        }

        return $results;
    }

    public static function get_string_between($string, $start, $end){
        $string = ' ' . $string;
        $ini = strpos($string, $start);
        if ($ini == 0) return '';
        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;
        return substr($string, $ini, $len);
    }


    public static function base64url_encode($data) {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * Insert an attachment from a URL address.
     *
     * @param  string   $url            The URL address.
     * @param  int|null $parent_post_id The parent post ID (Optional).
     * @return int|false                The attachment ID on success. False on failure.
     */
    public static function wp_insert_attachment_from_url($url, $parent_post_id = null) {

        $img_name = sanitize_file_name(basename($url));
        $local_path = wp_get_upload_dir()['path'].'/'.$img_name;
        
        if(file_exists($local_path)) {
            $local_url = wp_get_upload_dir()['url'].'/'.$img_name;
            $attach_id = attachment_url_to_postid($local_url);
        } else {

            if ( ! class_exists( 'WP_Http' ) ) {
                require_once ABSPATH . WPINC . '/class-http.php';
            }

            $http     = new WP_Http();
            $response = $http->request( $url );
            if ( 200 !== $response['response']['code'] ) {
                return false;
            }

            $upload = wp_upload_bits( basename( $url ), null, $response['body'] );
            if ( ! empty( $upload['error'] ) ) {
                return false;
            }

            $file_path        = $upload['file'];
            $file_name        = basename( $file_path );
            $file_type        = wp_check_filetype( $file_name, null );
            $attachment_title = sanitize_file_name( pathinfo( $file_name, PATHINFO_FILENAME ) );

            $wp_upload_dir    = wp_upload_dir();

            $post_info = array(
                'guid'           => $wp_upload_dir['url'] . '/' . $file_name,
                'post_mime_type' => $file_type['type'],
                'post_title'     => $attachment_title,
                'post_content'   => '',
                'post_status'    => 'inherit',
            );

            // Create the attachment.
            $attach_id = wp_insert_attachment( $post_info, $file_path, $parent_post_id );

            // Include image.php.
            require_once ABSPATH . 'wp-admin/includes/image.php';

            // Generate the attachment metadata.
            $attach_data = wp_generate_attachment_metadata( $attach_id, $file_path );

            // Assign metadata to attachment.
            wp_update_attachment_metadata( $attach_id, $attach_data );

        }

        return $attach_id;

    }

	/**
     * Checks if a number has decimals
     * 
     * @param string $number
     */
	public static function has_decimals($number) {
		return floor($number) != $number;
	}

	/**
     * Works out correct genitive case
     * 
     * @param string $word
     */
    public static function genitive_case($word = null) {

        $ending = '';
        if ($word) {
            $ending = "'s";

            if (substr($word, -1) == 's') {
                $ending = "'";
            }
        }

        return $ending;
    }

	/**
	 * Return button markup
	 * 
	 * @param string $label
	 * @param string $url
	 * @param array $classes
	 */
    public static function button($label, $url, $classes = '') {

		if($label && $url) {

			$target = self::link_target($url);

			if($is_gf_form = strpos($url, 'gf=') !== false) {
				$gf_form_id = explode('=', $url)[1];
				$url = '#';
				$target = '';
			}

			$button = '<a href="'.$url.'" class="'.$classes.'"'.($is_gf_form ? ' data-gf-id="'.$gf_form_id.'" data-gf-ajax-trigger' : '').$target.'>'.$label.'</a>';
			return $button;

		}

    }


	/**
     * Works out correct target for links
     * 
     * @param string $url
     */
    public static function link_target($url) {

		$target = ' target="_blank"';

		if (strpos($url, $_SERVER['HTTP_HOST']) !== false || strpos($url, '#') === 0) {
			$target = '';
		}

		return $target;

    }

	/**
	 * Expander helper
	 * 
	 * @param string $prefix
	 * @param array $data
	 * @param string $message
	 */
	public static function xpanda($prefix, $data, $message) {
		$result = $message;

		foreach ($data as $key => $value) {
			// Build up the variable we're looking for in the template so we can expand it
			$variable = "[".$prefix."-".$key."]";

			// This is the exciting bit where we do the find and replace!
			$result = str_replace($variable, $value, $result);
		}

		// send back the results
		return ($result);
	}

	/**
     * Returns the service categories.
     * 
     * @param string $taxonomy
     * @param string $return | Accepts: all | all_with_object_id | ids | tt_ids | slugs | count | id=>parent | id=>name | id=>slug
     * @see https://developer.wordpress.org/reference/classes/wp_term_query/__construct/
     */
    public static function get_terms($post_id, $taxonomy = '', $return = null) {

        $args = $return ? array('fields' => $return) : array();        
        $post_terms = wp_get_object_terms($post_id, $taxonomy, $args);

        if(!empty($post_terms) && !is_wp_error($post_terms)) {
            return $post_terms;
        }

        return null;

    }

	/**
     * Returns the post/page URL by slug.
     * 
	 * @param string $slug
     */
    public static function get_post_url($slug) {

        $post = get_page_by_path($slug, OBJECT, 'page');

		if($post) {
			return get_permalink($post->ID);
		}

		return null;

    }

	/**
	 * Breadcrumbs
	 */
	public static function breadcrumbs() {

		global $post;
		$post_id = $post->ID;
		
		// Settings
		$separator          = '<li class="separator"><i class="fal fa-slash-forward"></i></li>';
		$breadcrums_id      = 'breadcrumbs';
		$breadcrums_class   = 'breadcrumbs';
		$home_title         = 'Home';
		
		// Do not display on the homepage
		if ( !is_front_page() ) {
		
			// Build the breadcrums
			echo '<div class="max__width"><ul id="'.$breadcrums_id.'" class="'.$breadcrums_class.'">';
			
			// Home page
			echo '<li><a href="'.get_home_url().'">'.$home_title.'</a></li>';

			if(is_singular('development')) {

				echo $separator;
				echo '<li><a href="'.self::get_post_url('developments').'">Developments</a></li>';
				echo $separator;
				echo '<li><strong>'.get_the_title($post_id).'</strong></li>';

			} elseif(is_singular('branch')) {

				echo $separator;
				echo '<li><a href="'.self::get_post_url('contact-us').'">Contact us</a></li>';
				echo $separator;
				echo '<li><strong>'.get_the_title($post_id).'</strong></li>';
				
			} else if ( is_category() ) {
				
				// Category page
				echo '<li class="item-current item-cat"><strong class="bread-current bread-cat">'.single_cat_title('', false).'</strong></li>';

			} else if (is_single()) {
				
				// Single post (Only display the first category)
				$cat = get_the_category(); $cat = $cat[0];
				echo $separator;
				echo '<li><a href="'.get_category_link($cat->term_id).'">'.$cat->name.'</a></li>';
				echo $separator;
				echo '<li><strong>'.get_the_title().'</strong></li>';
				
			} else if ( is_page() ) {

				$current_title = get_the_title();
				
				// Standard page
				if( $post->post_parent ) {
					
					// If child page, get parents 
					$ancestors = get_post_ancestors( $post->ID );
					$ancestors = array_reverse($ancestors);
					
					// Parent page loop
					$parents = $separator;
					if(!empty($ancestors)) {
						foreach ( $ancestors as $ancestor ) {
							if(get_the_title($ancestor) === 'Smart') { continue; }

							$page_title = get_the_title($ancestor);
							$parents .= '<li><a href="'.get_permalink($ancestor).'">'.$page_title.'</a></li>'.$separator;
							
						}
					}

					// Display parent pages
					echo $parents;
					echo '<li><strong>'.$current_title.'</strong></li>';
					
				} else {

					echo $separator;
					echo '<li><strong> '.$current_title.'</strong></li>';
					
				}
			
			} else if ( get_query_var('paged') ) {
				
				// Paginated archives
				echo '<li><strong>'.__('Page').' '.get_query_var('paged').'</strong></li>';
				
			} else if ( is_search() ) {
			
				// Search results page
				echo '<li><strong>Search results for: '.get_search_query().'</strong></li>';
			
			} elseif ( is_404() ) {
				
				// 404 page
				echo '<li>'.'Error 404'.'</li>';
			}
		
			echo '</ul></div>';
			
		}
		
	}
    
}