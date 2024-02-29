<?php
/**
 * APF Single Property - Virtual Tour
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>

<article class="apf__brochure">
    <h2>Virtual Tour</h2>

    <?php 
        echo apf_vtour(get_field('property_video'));
        echo apf_matterport(get_field('property_matterport'))
    ?>
</article>