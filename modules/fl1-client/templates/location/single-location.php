<?php
/**
 * Single location template
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

get_header();
AVB::avb_banners();
FC_Helpers::flexible_content();
get_footer();
?>