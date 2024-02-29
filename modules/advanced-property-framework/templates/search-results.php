<?php
/**
 * APF Main template
 *
 * @author  Various
 * @package Advanced Property Framework
 *
*/
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

get_header();

// Globals
global $post, $wp_query;
?>

    <section class="apf">
        <?php APF_Helpers::search_form(); ?>

        <div class="apf__results">
            <div class="apf__results__list">
                <div class="apf__properties"></div>
            </div>

            <?php require_once 'map.php'; ?>
        </div>
    </section>
<?php get_footer(); ?>
