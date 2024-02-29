<?php
/**
 * Blog Loop
 *
 * @package Blog
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if(!empty($blogs)) {
    foreach($blogs['posts'] as $blog_id) {
        require('blog-item.php');
    }
}