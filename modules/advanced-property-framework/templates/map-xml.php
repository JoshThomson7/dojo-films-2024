<?php
global $post;

$posts = explode(',', $_GET['posts']);

echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<markers>';
?>
    <?php
        foreach($posts as $post_id):
            
			$property = new APF_Property($post_id);

            $name = htmlentities($property->get_name());
            $permalink = $property->get_permalink();
			$lat = $property->get_latitude();
			$lng = $property->get_longitude();

			$address = $property->get_address();
            if(isset($address['address'])) {
                $lat = $address['lat'];
                $lng = $address['lng'];
            }

			if(empty($lat) || empty($lat)) { continue; }

            $property_price = number_format((float)$property->get_price());
            $property_image = $property->get_main_image();
    ?>
        <marker lat="<?php echo $lat; ?>" lng="<?php echo $lng; ?>" permalink="<?php echo $permalink; ?>" name="<?php echo $name; ?>" price="<?php echo $property_price; ?>" type="<?php echo get_post_type(); ?>" status="<?php echo $property->get_status(); ?>" seo="<?php echo $property->get_seo_title(); ?>" image="<?php echo $property_image; ?>" />
        <?php endforeach; wp_reset_postdata(); ?>
</markers>
