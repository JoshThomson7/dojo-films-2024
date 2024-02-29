<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$branch_args = array(
    'post_type' => 'branch',
    'post_status' => 'publish',
    'posts_per_page' => -1
);

$branches_query = new WP_Query($branch_args);

echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<markers>';
?>
    <?php
        while($branches_query->have_posts()) : $branches_query->the_post();

            // Name
            $name = htmlentities(utf8_decode(get_the_title()));

            // Permalink
            $permalink = get_permalink();

            // Address
            $branch_address = '';
            $lat = '';
            $lng = '';
            if(get_field('branch_address')) {
                $branch_address = get_field('branch_address')['address'];
                $lat = get_field('branch_address')['lat'];
                $lng = get_field('branch_address')['lng'];
            }

            $branch_search_url = esc_url(get_permalink(get_page_by_path('property-search')));
            $branch_search_url .= '/?type=sales&area_search='.str_replace(' ', '-', strtolower(get_the_title()));

            // Shitty fix if branches have no lat/lng
            if(empty($lat) || empty($lat)) { continue; }

            // Image
            $branch_image = '';
            if(get_field('branch_image')) {
                $branch_img_id = get_field('branch_image');
                $branch_image = vt_resize($branch_img_id,'' , 600, 320, true);
                $branch_image = $branch_image['url'];
            }
    ?>
        <marker address="<?php echo $branch_address; ?>" lat="<?php echo $lat; ?>" lng="<?php echo $lng; ?>" permalink="<?php the_permalink(); ?>" name="<?php echo $name; ?>" image="<?php echo $branch_image; ?>" />
    <?php endwhile; wp_reset_query(); ?>
</markers>
