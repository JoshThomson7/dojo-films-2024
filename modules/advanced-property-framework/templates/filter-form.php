<?php
/**
 * APF Filter Form
 *
 * @author  FL1 Digital
 * @package Advanced Property Framework
 *
*/
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$apf_settings = new APF_Settings();
?>

<div class="apf__results__filter">

    <a href="#" class="apf__filter__refine" title="Refine search"><span>Refine search</span></a>

    <div class="apf__results__filter__view">

        <input type="radio" id="apf_view_grid" name="apf_view" value="grid" checked />
        <label for="apf_view_grid" class="apf-view-on-change tooltip" title="Grid" data-view="grid"><i class="fa-light fa-grid-2"></i></label>

        <input type="radio" id="apf_view_list" name="apf_view" value="list"/>
        <label for="apf_view_list" class="apf-view-on-change tooltip" title="List" data-view="list"><i class="fa-light fa-diagram-cells"></i></label>

        <input type="checkbox" id="apf_view_map" name="apf_map" value="map" checked />
        <label for="apf_view_map" class="apf-view-on-change tooltip" title="Show/hide map" data-view="map"><i class="fa-light fa-location-dot"></i></label>

    </div><!-- apf__results__filter__view -->

    <div class="apf__results__filter__sort">

        <?php if($apf_settings->search_new_home()): ?>
            <article class="apf-search-hide-on-mobile">
                <input type="checkbox" id="apf_new_homes" class="apf-fetch" name="apf_new_homes" value="true"<?php if($apf_new_homes == 'true') { echo " checked";} ?>>
                <label for="apf_new_homes" class="apf__new__homes">Show New Homes only</label>
            </article>
        <?php endif; ?>

        <?php if($apf_settings->search_hide_gone()): ?>
            <article class="apf-search-hide-on-mobile">
                <input type="checkbox" id="apf_status" name="apf_status" class="apf__filter__status apf-fetch" value="exclude"<?php if($apf_status == 'exclude') { echo " checked";} ?>>
                <label for="apf_status" class="apf__status">Show Sold properties</label>
            </article>
        <?php endif; ?>

        <?php
            /*$branches_query = new WP_Query(array(
                'post_type' => 'branch',
                'post_status' => 'publish',
                'posts_per_page' => -1
            ));

            if($branches_query->have_posts()):
        ?>
            <select name="apf_branch">
                <option value="">Any branch</option>
                <?php while($branches_query->have_posts()) : $branches_query->the_post(); ?>
                    <option value="<?php the_field('branch_id'); ?>"><?php the_title(); ?></option>
                <?php endwhile; ?>
            </select>
        <?php endif;*/ ?>

        <?php if($apf_settings->search_sorting_filters()): ?>
            <select name="apf_order" class="apf-fetch-on-change">
                <?php foreach($apf_settings->search_sorting_filters() as $filter): ?>
                    <option value="<?php echo $filter['value']; ?>"><?php echo $filter['label'];  ?></option>
                <?php endforeach; ?>
            </select>
        <?php endif; ?>

    </div><!-- apf__results__filter__sort -->

</div><!-- apf__results__filter -->
