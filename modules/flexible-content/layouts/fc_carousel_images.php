<?php
/*
------------------------------------------------
   ______                                 __
  / ____/___ __________  __  __________  / /
 / /   / __ `/ ___/ __ \/ / / / ___/ _ \/ /
/ /___/ /_/ / /  / /_/ / /_/ (__  )  __/ /
\____/\__,_/_/   \____/\__,_/____/\___/_/

------------------------------------------------
Carousel
*/

$logos = get_sub_field('carousel_images');
?>


<div class="carousel_images">
    <?php
        foreach($logos as $logo):

        // URL
        $link_start = '';
        $link_end = '';

        if($logo['carousel_item_url']) {
            $target = '';

            if($logo['carousel_item_target']) {
                $target = ' target="_blank"';
            }

            $link_start = '<a href="'.$logo['carousel_item_url'].'" title="'.$logo['carousel_item_name'].'"'.$target.'>';
            $link_end = '</a>';
        }

        $attachment_id = $logo['carousel_item_image'];
        $carousel_image = vt_resize($attachment_id,'' , 400, 100, false);
    ?>
        <div class="carousel_image">
            <?php echo $link_start; ?>
                <img src="<?php echo $carousel_image['url']; ?>" alt="<?php $logo['carousel_item_name']; ?>">
            <?php echo $link_end; ?>
        </div><!--  -->
    <?php endforeach; ?>
</div><!-- carousel_images -->
