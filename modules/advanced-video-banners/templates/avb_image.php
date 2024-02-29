<?php
/**
 * AVB Image
 *
 * @package advanced-video-banners/
 * @version 2.0
 */

$avb_banner_image = new AVB_Banner_Image($banner_data);
?>
<div class="avb-banner__medium <?php echo $avb_banner_image->classes(); ?>"
	<?php echo $avb_banner_image->image() ? 'style="background-image:url('.$avb_banner_image->image().');"' : ''; ?> role="img" alt="<?php echo $avb_banner_image->image('alt'); ?>">
</div>